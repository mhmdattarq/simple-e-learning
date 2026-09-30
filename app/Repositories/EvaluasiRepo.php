<?php

namespace App\Repositories;

use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EvaluasiRepo
{
    /**
     * Query builder for Yajra DataTables server-side rendering per Course.
     * Mengembalikan instance query Builder Course dengan relasi kuis untuk pagination server-side.
     */
    public static function getDt(): Builder
    {
        return Course::query()
            ->with(['category'])
            ->withCount([
                'quizzes',
                'chapterQuizzes',
                'quizzes as questions_count' => function ($q) {
                    $q->join('quiz_questions', 'quizzes.id', '=', 'quiz_questions.quiz_id');
                },
                'quizzes as attempts_count' => function ($q) {
                    $q->join('quiz_attempts', 'quizzes.id', '=', 'quiz_attempts.quiz_id');
                },
            ])
            ->withExists('finalQuiz');
    }

    /**
     * Mendapatkan statistik ringkas untuk widget dashboard evaluasi.
     *
     * @return array{
     *     total_quizzes: int,
     *     chapter_quizzes: int,
     *     final_quizzes: int,
     *     total_attempts: int
     * }
     */
    public static function getStats(): array
    {
        return [
            'total_quizzes' => Quiz::count(),
            'chapter_quizzes' => Quiz::where('type', 'chapter')->count(),
            'final_quizzes' => Quiz::where('type', 'final')->count(),
            'total_attempts' => QuizAttempt::count(),
        ];
    }

    /**
     * Mendapatkan statistik monitoring evaluasi khusus untuk satu kelas diklat.
     *
     * @return array{
     *     total_quizzes: int,
     *     chapter_quizzes: int,
     *     has_final_quiz: bool,
     *     final_quiz: ?Quiz,
     *     total_attempts: int,
     *     passed_attempts: int,
     *     pass_rate: int,
     *     avg_score: float
     * }
     */
    public static function getCourseStats(int $courseId): array
    {
        $totalQuizzes = Quiz::where('course_id', $courseId)->count();
        $chapterQuizzes = Quiz::where('course_id', $courseId)->where('type', 'chapter')->count();
        $finalQuiz = Quiz::where('course_id', $courseId)->where('type', 'final')->first();

        $attemptsQuery = QuizAttempt::whereHas('quiz', fn ($q) => $q->where('course_id', $courseId));
        $totalAttempts = (clone $attemptsQuery)->count();
        $passedAttempts = (clone $attemptsQuery)->where('is_passed', true)->count();
        $passRate = $totalAttempts > 0 ? (int) round(($passedAttempts / $totalAttempts) * 100) : 0;
        $avgScore = $totalAttempts > 0 ? round((float) (clone $attemptsQuery)->avg('percentage'), 1) : 0.0;

        return [
            'total_quizzes' => $totalQuizzes,
            'chapter_quizzes' => $chapterQuizzes,
            'has_final_quiz' => $finalQuiz !== null,
            'final_quiz' => $finalQuiz,
            'total_attempts' => $totalAttempts,
            'passed_attempts' => $passedAttempts,
            'pass_rate' => $passRate,
            'avg_score' => $avgScore,
        ];
    }

    /**
     * Query builder riwayat pengerjaan kuis peserta untuk server-side DataTables di halaman detail evaluasi kelas.
     */
    public static function getAttemptsDt(int $courseId): Builder
    {
        return QuizAttempt::query()
            ->select('quiz_attempts.*')
            ->whereHas('quiz', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            })
            ->with(['user', 'quiz.chapter'])
            ->latest('quiz_attempts.id');
    }

    /**
     * Query builder terfilter untuk daftar evaluasi & kuis.
     */
    public static function queryFiltered(?string $search = null, ?int $courseId = null, ?string $type = null): Builder
    {
        $query = Quiz::query()
            ->with(['course.category', 'chapter', 'creator'])
            ->withCount(['questions', 'attempts']);

        if (! empty($search)) {
            $term = trim($search);
            $query->where(function (Builder $q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhereHas('course', function (Builder $cq) use ($term) {
                        $cq->where('title', 'like', "%{$term}%");
                    })
                    ->orWhereHas('chapter', function (Builder $chq) use ($term) {
                        $chq->where('title', 'like', "%{$term}%");
                    });
            });
        }

        if (! empty($courseId)) {
            $query->where('course_id', $courseId);
        }

        if (! empty($type) && in_array($type, ['chapter', 'final'], true)) {
            $query->where('type', $type);
        }

        return $query->latest('id');
    }

    /**
     * Mendapatkan daftar kuis terpaginasi.
     */
    public static function getPaginated(?string $search = null, ?int $courseId = null, ?string $type = null, int $perPage = 10): LengthAwarePaginator
    {
        return self::queryFiltered($search, $courseId, $type)->paginate($perPage);
    }

    /**
     * Mengambil detail kuis lengkap beserta butir soal dan kunci jawabannya.
     */
    public static function getByIdWithDetails(int $id): ?Quiz
    {
        return Quiz::with([
            'course.category',
            'chapter',
            'creator',
            'questions.options',
            'attempts' => fn ($q) => $q->latest()->limit(10)->with('user'),
        ])->find($id);
    }

    /**
     * Menghapus data evaluasi kuis secara aman.
     */
    public static function delete(int $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $quiz = Quiz::find($id);
                if (! $quiz) {
                    return false;
                }

                // Log aktivitas audit
                AuditLog::log(
                    action: 'quiz.deleted',
                    auditable: $quiz,
                    oldValues: [
                        'title' => $quiz->title,
                        'type' => $quiz->type,
                        'course_id' => $quiz->course_id,
                    ],
                    notes: 'Admin menghapus evaluasi kuis: '.$quiz->title
                );

                // Hapus kuis (soft delete pada quiz)
                $quiz->delete();

                return true;
            });
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus evaluasi kuis', [
                'quiz_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

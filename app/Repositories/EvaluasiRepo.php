<?php

namespace App\Repositories;

use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Carbon\Carbon;
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

    /**
     * Menyelesaikan dan mengkalkulasi skor evaluasi kuis secara permanen (Single Attempt).
     *
     * @param  array<int, int|string>  $userAnswers
     */
    public static function finalizeAttempt(Quiz $quiz, User $user, array $userAnswers = [], ?string $startedAt = null): QuizAttempt
    {
        $existing = $quiz->getAttemptForUser($user->id);
        if ($existing) {
            return $existing;
        }

        $earnedScore = 0;
        $answersData = [];

        // Ambil data pertanyaan dan opsi resmi langsung dari database untuk validasi & penilaian yang aman
        $questions = QuizQuestion::with(['options' => fn ($q) => $q->orderBy('order', 'asc')])
            ->where('quiz_id', $quiz->id)
            ->orderBy('order', 'asc')
            ->get();

        foreach ($questions as $question) {
            $correctOption = $question->options->firstWhere('is_correct', true)
                ?? QuizOption::where('question_id', $question->id)->where('is_correct', true)->first();
            $chosenOptionId = $userAnswers[$question->id] ?? null;

            $isCorrect = ($chosenOptionId && $correctOption && (int) $chosenOptionId === $correctOption->id);
            $scoreGained = $isCorrect ? (int) $question->score : 0;
            $earnedScore += $scoreGained;

            $answersData[] = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'chosen_option_id' => $chosenOptionId,
                'correct_option_id' => $correctOption?->id,
                'is_correct' => $isCorrect,
                'score_earned' => $scoreGained,
                'question_score' => $question->score,
            ];
        }

        $totalPossible = $quiz->total_score > 0 ? $quiz->total_score : max(1, (int) $questions->sum('score'));
        $percentage = round(($earnedScore / $totalPossible) * 100, 2);
        $isPassed = ($percentage >= $quiz->passing_score);

        return QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'total_earned_score' => $earnedScore,
            'total_possible_score' => $totalPossible,
            'percentage' => $percentage,
            'is_passed' => $isPassed,
            'answers_data' => $answersData,
            'started_at' => $startedAt ? Carbon::parse($startedAt) : now(),
            'submitted_at' => now(),
        ]);
    }

    /**
     * Memeriksa dan memfinalisasi otomatis seluruh sesi kuis aktif pengguna dari session.
     * Berguna saat user logout paksa atau sesi terputus saat pengerjaan kuis berlangsung.
     */
    public static function finalizeActiveSessionsForUser(User $user): int
    {
        $finalizedCount = 0;
        $sessionAll = session()->all();

        foreach ($sessionAll as $key => $progress) {
            if (is_string($key) && preg_match('/^quiz_progress_(\d+)_'.preg_quote((string) $user->id, '/').'$/', $key, $matches)) {
                $quizId = (int) $matches[1];
                $quiz = Quiz::find($quizId);

                if ($quiz && ! $quiz->isAttemptedByUser($user->id)) {
                    $userAnswers = (array) ($progress['answers'] ?? []);
                    $startedAt = $progress['started_at'] ?? null;
                    static::finalizeAttempt($quiz, $user, $userAnswers, $startedAt);
                    $finalizedCount++;
                }

                session()->forget($key);
            }
        }

        return $finalizedCount;
    }
}

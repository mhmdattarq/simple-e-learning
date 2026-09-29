<?php

namespace App\Repositories;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EvaluasiRepo
{
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

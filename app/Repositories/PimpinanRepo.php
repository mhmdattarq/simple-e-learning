<?php

namespace App\Repositories;

use App\Enums\CourseStatus;
use App\Models\AuditLog;
use App\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PimpinanRepo
{
    /**
     * Query builder for Yajra DataTables in Pimpinan Persetujuan Rencana.
     */
    public static function getPersetujuanDt(): Builder
    {
        return Course::query()
            ->select('courses.*')
            ->with(['category', 'creator'])
            ->where('courses.status', CourseStatus::Submitted);
    }

    /**
     * Find course with relationships for leadership review.
     */
    public static function getById(int|string $id): Course
    {
        return Course::with(['category', 'creator', 'approver'])->findOrFail($id);
    }

    /**
     * Summary statistics for leadership approval screen.
     *
     * @return array<string, int>
     */
    public static function getPersetujuanSummary(): array
    {
        return [
            'pending_count' => Course::where('status', CourseStatus::Submitted)->count(),
            'approved_month_count' => Course::where('status', CourseStatus::Approved)
                ->whereMonth('approved_at', now()->month)
                ->count(),
            'active_count' => Course::whereIn('status', [CourseStatus::Published, CourseStatus::Ongoing])->count(),
        ];
    }

    /**
     * Approve a submitted course plan.
     *
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public static function approvePlan(int $courseId, ?string $notes = null, ?int $approverId = null): array
    {
        return DB::transaction(function () use ($courseId, $notes, $approverId) {
            $course = Course::lockForUpdate()->findOrFail($courseId);

            if ($course->status !== CourseStatus::Submitted) {
                throw ValidationException::withMessages([
                    'status' => 'Hanya pelatihan dengan status Diajukan yang dapat disetujui.',
                ]);
            }

            $userId = $approverId ?? auth()->id();

            $oldStatus = $course->status->value;

            $course->update([
                'status' => CourseStatus::Approved,
                'approved_by' => $userId,
                'approved_at' => now(),
                'approval_notes' => $notes ?: 'Disetujui oleh Pimpinan.',
            ]);

            AuditLog::log(
                action: 'course.approved',
                auditable: $course,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => CourseStatus::Approved->value, 'approved_by' => $userId],
                notes: $notes ?: 'Disetujui oleh Pimpinan.',
                userId: $userId
            );

            return [
                'success' => true,
                'message' => 'Rencana pelatihan '.$course->code.' berhasil disetujui.',
                'course' => $course,
            ];
        });
    }

    /**
     * Return a submitted course plan back to draft with mandatory revision notes.
     *
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public static function rejectPlan(int $courseId, string $notes): array
    {
        if (trim($notes) === '') {
            throw ValidationException::withMessages([
                'decisionNotes' => 'Catatan revisi wajib diisi agar Admin Diklat mengetahui poin perbaikan.',
            ]);
        }

        return DB::transaction(function () use ($courseId, $notes) {
            $course = Course::lockForUpdate()->findOrFail($courseId);

            if ($course->status !== CourseStatus::Submitted) {
                throw ValidationException::withMessages([
                    'status' => 'Hanya pelatihan dengan status Diajukan yang dapat dikembalikan untuk revisi.',
                ]);
            }

            $oldStatus = $course->status->value;

            $course->update([
                'status' => CourseStatus::Draft,
                'approval_notes' => $notes,
            ]);

            AuditLog::log(
                action: 'course.rejected',
                auditable: $course,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => CourseStatus::Draft->value],
                notes: $notes,
                userId: auth()->id()
            );

            return [
                'success' => true,
                'message' => 'Rencana pelatihan '.$course->code.' telah dikembalikan ke status Draft untuk direvisi.',
                'course' => $course,
            ];
        });
    }
}

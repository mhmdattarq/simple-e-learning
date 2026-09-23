<?php

namespace App\Repositories;

use App\Enums\RegistrationStatus;
use App\Models\CourseUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifikasiRepo
{
    /**
     * Query builder for Yajra DataTables server-side rendering.
     * Note: Mengembalikan instance query Builder untuk pagination server-side Yajra DataTables.
     */
    public static function getDt(?string $status = null, ?int $courseId = null): Builder
    {
        $query = CourseUser::query()
            ->with(['user', 'course', 'course.category', 'verifier'])
            ->select('course_user.*');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        return $query->latest('id');
    }

    /**
     * Find registration by ID with relationships.
     */
    public static function getById(int|string $id): CourseUser
    {
        return CourseUser::with(['user', 'course', 'course.category', 'verifier'])->findOrFail($id);
    }

    /**
     * Process verification decision (Diverifikasi, Perlu Perbaikan, Ditolak).
     * Transaksi atomik verifikasi status peserta disertai pencatatan audit trail verifikator.
     */
    public static function verify(int|string $id, string|RegistrationStatus $status, ?string $notes, int $verifierId): CourseUser
    {
        return DB::transaction(function () use ($id, $status, $notes, $verifierId) {
            $reg = CourseUser::lockForUpdate()->findOrFail($id);

            $statusEnum = is_string($status) ? RegistrationStatus::from($status) : $status;

            $reg->update([
                'status' => $statusEnum,
                'verification_notes' => $notes,
                'verified_by' => $verifierId,
                'verified_at' => now(),
            ]);

            Log::info('Course registration verified', [
                'course_user_id' => $reg->id,
                'registration_number' => $reg->registration_number,
                'status' => $statusEnum->value,
                'verified_by' => $verifierId,
            ]);

            return $reg->fresh(['user', 'course', 'verifier']);
        });
    }

    /**
     * Get aggregate statistics for verification dashboard counters.
     *
     * @return array{total: int, pending: int, verified: int, revision_required: int, rejected: int}
     */
    public static function getStats(): array
    {
        return [
            'total' => CourseUser::count(),
            'pending' => CourseUser::where('status', RegistrationStatus::Pending)->count(),
            'verified' => CourseUser::where('status', RegistrationStatus::Verified)->count(),
            'revision_required' => CourseUser::where('status', RegistrationStatus::RevisionRequired)->count(),
            'rejected' => CourseUser::where('status', RegistrationStatus::Rejected)->count(),
        ];
    }
}

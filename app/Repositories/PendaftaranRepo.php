<?php

namespace App\Repositories;

use App\Enums\CourseStatus;
use App\Enums\RegistrationStatus;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PendaftaranRepo
{
    /**
     * Query builder for Yajra DataTables server-side rendering.
     * Note: Mengembalikan instance query Builder untuk pagination server-side Yajra DataTables.
     */
    public static function getDt(?int $courseId = null): Builder
    {
        $query = CourseUser::query()
            ->with(['user', 'course', 'course.category'])
            ->select('course_user.*');

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
        return CourseUser::with(['user', 'course', 'course.category'])->findOrFail($id);
    }

    /**
     * Generate unique registration number (Format: REG-YYYYMM-XXXX).
     */
    public static function generateRegistrationNumber(): string
    {
        $prefix = 'REG-'.date('Ym').'-';

        $lastRecord = CourseUser::where('registration_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->first();

        if (! $lastRecord) {
            return $prefix.'0001';
        }

        $lastNumber = (int) substr($lastRecord->registration_number, -4);
        $nextNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);

        return $prefix.$nextNumber;
    }

    /**
     * Check if a user is already registered for a course.
     */
    public static function hasRegistered(int $userId, int $courseId): bool
    {
        return CourseUser::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();
    }

    /**
     * Register a participant to a course with atomic transaction.
     *
     * @param  array<string, mixed>  $userData
     */
    public static function register(int $userId, int $courseId, array $userData, ?UploadedFile $letterFile = null): ?CourseUser
    {
        try {
            return DB::transaction(function () use ($userId, $courseId, $userData, $letterFile) {
                // Rule 1: Course must exist and be in 'published' state
                $course = Course::lockForUpdate()->findOrFail($courseId);
                if ($course->status !== CourseStatus::Published) {
                    throw new \Exception('Periode pendaftaran untuk pelatihan ini belum dibuka atau telah ditutup.');
                }

                // Rule 2: Registration period dates
                $now = now();
                if ($course->registration_open_at && $now->lt($course->registration_open_at)) {
                    throw new \Exception('Periode pendaftaran untuk pelatihan ini belum dimulai.');
                }
                if ($course->registration_close_at && $now->gt($course->registration_close_at)) {
                    throw new \Exception('Periode pendaftaran untuk pelatihan ini telah berakhir.');
                }

                // Rule 3: Quota check
                $enrolledCount = CourseUser::where('course_id', $courseId)->count();
                if ($enrolledCount >= $course->quota) {
                    throw new \Exception('Kuota pendaftaran pelatihan ini sudah penuh.');
                }

                // Rule 4: Duplicate registration check
                if (self::hasRegistered($userId, $courseId)) {
                    throw new \Exception('Anda sudah terdaftar pada pelatihan ini.');
                }

                // 1. Update user's ASN profile details
                $user = User::findOrFail($userId);
                $profileFields = array_intersect_key($userData, array_flip([
                    'nip', 'name', 'opd_agency', 'position', 'rank_class', 'phone_number',
                ]));

                if (! empty($profileFields)) {
                    $user->update($profileFields);
                }

                // 2. Handle recommendation letter file upload
                $letterPath = null;
                if ($letterFile) {
                    $letterPath = $letterFile->store('recommendations', 'public');
                }

                // 3. Create course_user registration record
                $registrationNumber = self::generateRegistrationNumber();

                return CourseUser::create([
                    'user_id' => $userId,
                    'course_id' => $courseId,
                    'registration_number' => $registrationNumber,
                    'status' => RegistrationStatus::Pending,
                    'recommendation_letter_path' => $letterPath,
                    'notes' => $userData['notes'] ?? null,
                    'enrolled_at' => now(),
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Pendaftaran pelatihan gagal', [
                'user_id' => $userId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Update registration status (Verifikasi / Approval).
     */
    public static function updateStatus(int|string $id, string|RegistrationStatus $status, ?string $notes = null): bool
    {
        try {
            $registration = CourseUser::findOrFail($id);

            $statusValue = $status instanceof RegistrationStatus ? $status->value : $status;

            $registration->update([
                'status' => $statusValue,
                'notes' => $notes ?? $registration->notes,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Update status pendaftaran gagal', [
                'id' => $id,
                'status' => $status,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Delete registration and cleanup associated file.
     */
    public static function delete(int|string $id): bool
    {
        try {
            $registration = CourseUser::findOrFail($id);

            if ($registration->recommendation_letter_path) {
                Storage::disk('public')->delete($registration->recommendation_letter_path);
            }

            $registration->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('Delete pendaftaran pelatihan gagal', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}

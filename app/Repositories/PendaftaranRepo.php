<?php

namespace App\Repositories;

use App\Enums\CourseStatus;
use App\Enums\RegistrationStatus;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PendaftaranRepo
{
    /**
     * Query builder for Yajra DataTables server-side rendering.
     * Mengembalikan instance query Builder untuk pagination server-side Yajra DataTables dengan multi-filter.
     *
     * @param  array<string, mixed>  $filters
     */
    public static function getDt(?int $courseId = null, array $filters = []): Builder
    {
        $query = CourseUser::query()
            ->with(['user', 'course', 'course.category', 'verifier'])
            ->select('course_user.*');

        $effectiveCourseId = $courseId ?: ($filters['course_id'] ?? null);
        if ($effectiveCourseId) {
            $query->where('course_id', $effectiveCourseId);
        }

        if (! empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['opd'])) {
            $opd = $filters['opd'];
            $query->whereHas('user', function (Builder $q) use ($opd) {
                $q->where('opd_agency', 'like', '%'.$opd.'%');
            });
        }

        if (! empty($filters['start_date'])) {
            $query->whereDate('enrolled_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->whereDate('enrolled_at', '<=', $filters['end_date']);
        }

        return $query->latest('id');
    }

    /**
     * Get courses eligible for registration configuration (Only Approved status).
     *
     * @return Collection<int, Course>
     */
    public static function getCoursesForRegistrationSetting(): Collection
    {
        return Course::query()
            ->withCount('registrations')
            ->where('status', CourseStatus::Approved)
            ->latest('id')
            ->get();
    }

    /**
     * Get real-time course registration summary statistics.
     *
     * @return array<string, mixed>|null
     */
    public static function getCourseRegistrationStats(int $courseId): ?array
    {
        $course = Course::withCount('registrations')->find($courseId);
        if (! $course) {
            return null;
        }

        $enrolledCount = (int) ($course->registrations_count ?? 0);
        $remainingQuota = max(0, (int) $course->quota - $enrolledCount);

        return [
            'id' => $course->id,
            'code' => $course->code,
            'title' => $course->title,
            'quota' => (int) $course->quota,
            'enrolled_count' => $enrolledCount,
            'remaining_quota' => $remainingQuota,
            'status' => $course->status,
            'status_label' => $course->status->label(),
            'status_badge' => $course->status->badgeClass(),
            'registration_open_at' => $course->registration_open_at?->format('Y-m-d\TH:i') ?? '',
            'registration_close_at' => $course->registration_close_at?->format('Y-m-d\TH:i') ?? '',
            'start_date' => $course->start_date?->format('d M Y'),
            'end_date' => $course->end_date?->format('d M Y'),
            'is_permanent' => $course->isPermanent(),
        ];
    }

    /**
     * Open registration period for an approved course with strict business validation.
     *
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public static function openRegistrationPeriod(int $courseId, ?string $openAt, ?string $closeAt): array
    {
        $course = Course::findOrFail($courseId);

        if ($course->status !== CourseStatus::Approved) {
            throw ValidationException::withMessages([
                'course' => 'Hanya pelatihan dengan status Disetujui yang dapat dibuka pendaftarannya.',
            ]);
        }

        $errors = [];
        if (empty($openAt)) {
            $errors['registration_open_at'] = 'Tanggal buka pendaftaran wajib diisi.';
        }

        if (empty($closeAt)) {
            $errors['registration_close_at'] = 'Tanggal tutup pendaftaran wajib diisi.';
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        try {
            $openCarbon = Carbon::parse($openAt);
            $closeCarbon = Carbon::parse($closeAt);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'registration_open_at' => 'Format tanggal pendaftaran tidak valid.',
            ]);
        }

        // B. Validasi Periode:
        // 1. registration_open_at harus sebelum registration_close_at
        if ($openCarbon->gte($closeCarbon)) {
            throw ValidationException::withMessages([
                'registration_open_at' => 'Tanggal buka pendaftaran harus sebelum tanggal tutup pendaftaran.',
            ]);
        }

        // 2. Pelatihan tidak bisa dibuka jika tanggal mulai pelatihan sudah lewat dari tanggal hari ini
        if ($course->start_date && now()->startOfDay()->gt($course->start_date->startOfDay())) {
            throw ValidationException::withMessages([
                'registration_open_at' => 'Pelatihan tidak dapat dibuka karena tanggal mulai pelatihan telah lewat.',
            ]);
        }

        // 3. registration_close_at harus sebelum tanggal mulai pelatihan (start_date)
        if ($course->start_date && $closeCarbon->startOfDay()->gt($course->start_date->startOfDay())) {
            throw ValidationException::withMessages([
                'registration_close_at' => 'Tanggal tutup pendaftaran tidak boleh melewati tanggal mulai pelatihan.',
            ]);
        }

        $course->update([
            'registration_open_at' => $openCarbon,
            'registration_close_at' => $closeCarbon,
            'status' => CourseStatus::Published,
        ]);

        return [
            'success' => true,
            'message' => 'Periode pendaftaran berhasil dibuka. Status pelatihan kini Dibuka.',
            'course' => $course,
        ];
    }

    /**
     * Close registration period for a published course.
     *
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public static function closeRegistrationPeriod(int $courseId): array
    {
        $course = Course::findOrFail($courseId);

        if ($course->status !== CourseStatus::Published) {
            throw ValidationException::withMessages([
                'course' => 'Hanya pelatihan dengan status Dibuka yang dapat ditutup pendaftarannya.',
            ]);
        }

        // Jika tanggal mulai pelatihan telah lewat atau hari ini, masuk ke tahap berjalan
        $newStatus = ($course->start_date && now()->startOfDay()->gte($course->start_date->startOfDay()))
            ? CourseStatus::Ongoing
            : CourseStatus::Approved;

        $course->update([
            'registration_close_at' => now(),
            'status' => $newStatus,
        ]);

        return [
            'success' => true,
            'message' => 'Pendaftaran pelatihan berhasil ditutup.',
            'course' => $course,
        ];
    }

    /**
     * Find registration by ID with relationships.
     */
    public static function getById(int|string $id): CourseUser
    {
        return CourseUser::with(['user', 'course', 'course.category', 'verifier'])->findOrFail($id);
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

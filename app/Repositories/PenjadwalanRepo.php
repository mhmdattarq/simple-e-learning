<?php

namespace App\Repositories;

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenjadwalanRepo
{
    /**
     * Query builder for Yajra DataTables server-side rendering.
     * Note: Mengembalikan instance query Builder untuk pagination server-side Yajra DataTables.
     */
    public static function getDt(?int $courseId = null, ?int $mentorId = null, ?string $date = null): Builder
    {
        $query = CourseSchedule::query()
            ->with(['course', 'mentor', 'creator'])
            ->select('course_schedules.*');

        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        if ($mentorId) {
            $query->where('mentor_id', $mentorId);
        }

        if ($date) {
            $query->whereDate('session_date', $date);
        }

        return $query->orderBy('session_date', 'desc')->orderBy('start_time', 'asc');
    }

    /**
     * Find schedule by ID with relationships.
     */
    public static function getById(int|string $id): CourseSchedule
    {
        return CourseSchedule::with(['course', 'mentor', 'creator'])->findOrFail($id);
    }

    /**
     * Validasi apakah tanggal pelaksanaan sesi berada di dalam periode pelatihan (start_date - end_date).
     *
     * @return string|null Error message if outside period, null if valid.
     */
    public static function validateCoursePeriod(int|string $courseId, ?string $sessionDate): ?string
    {
        if (! $courseId || ! $sessionDate) {
            return null;
        }

        $course = Course::find($courseId);
        if (! $course) {
            return 'Program pelatihan yang dipilih tidak ditemukan.';
        }

        // Jika pelatihan mandiri (permanent), tidak memiliki batas periode tanggal mulai/selesai tertentu
        if ($course->isPermanent()) {
            return null;
        }

        if ($course->start_date && $course->end_date) {
            $sessionCarbon = Carbon::parse($sessionDate)->startOfDay();
            $startCarbon = $course->start_date->copy()->startOfDay();
            $endCarbon = $course->end_date->copy()->endOfDay();

            if ($sessionCarbon->lt($startCarbon) || $sessionCarbon->gt($endCarbon)) {
                $startFormatted = $course->start_date->format('d/m/Y');
                $endFormatted = $course->end_date->format('d/m/Y');

                return "Waktu sesi tidak valid: Tanggal sesi ({$sessionCarbon->format('d/m/Y')}) harus berada di dalam periode pelaksanaan pelatihan {$course->code} ({$startFormatted} s.d {$endFormatted}).";
            }
        }

        return null;
    }

    /**
     * Validasi anti-bentrok jadwal mentor dan ruangan/lokasi sesi pelatihan.
     * Validates mentor availability and physical room collision.
     *
     * @return string|null Error message if conflict found, null if clean.
     */
    public static function checkConflict(array $data, ?int $excludeId = null): ?string
    {
        $sessionDate = $data['session_date'] ?? null;
        $startTime = $data['start_time'] ?? null;
        $endTime = $data['end_time'] ?? null;
        $mentorId = $data['mentor_id'] ?? null;
        $roomOrLink = trim($data['room_or_link'] ?? '');

        if (! $sessionDate || ! $startTime || ! $endTime) {
            return null;
        }

        // Format start and end time if formatted with seconds or without
        $startTimeFormatted = strlen($startTime) === 5 ? $startTime.':00' : $startTime;
        $endTimeFormatted = strlen($endTime) === 5 ? $endTime.':00' : $endTime;

        // 1. Check Mentor Clash (Mentor cannot teach two sessions simultaneously, ignore cancelled)
        if ($mentorId) {
            $mentorConflict = CourseSchedule::query()
                ->where('session_date', $sessionDate)
                ->where('mentor_id', $mentorId)
                ->where('status', '!=', 'cancelled')
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->where(function ($q) use ($startTimeFormatted, $endTimeFormatted) {
                    $q->where('start_time', '<', $endTimeFormatted)
                        ->where('end_time', '>', $startTimeFormatted);
                })
                ->with(['course', 'mentor'])
                ->first();

            if ($mentorConflict) {
                $mentorName = $mentorConflict->mentor?->name ?? 'Mentor';

                return "Bentrok Jadwal: {$mentorName} sudah memiliki jadwal sesi '{$mentorConflict->session_title}' pada tanggal {$sessionDate} pukul {$mentorConflict->start_time} - {$mentorConflict->end_time}.";
            }
        }

        // 2. Check Physical Room Collision (only if room_or_link is not an online URL link, ignore cancelled)
        $isOnlineLink = filter_var($roomOrLink, FILTER_VALIDATE_URL) !== false
            || str_starts_with($roomOrLink, 'http://')
            || str_starts_with($roomOrLink, 'https://');

        if (! $isOnlineLink && ! empty($roomOrLink)) {
            $roomConflict = CourseSchedule::query()
                ->where('session_date', $sessionDate)
                ->whereRaw('LOWER(TRIM(room_or_link)) = ?', [strtolower($roomOrLink)])
                ->where('status', '!=', 'cancelled')
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->where(function ($q) use ($startTimeFormatted, $endTimeFormatted) {
                    $q->where('start_time', '<', $endTimeFormatted)
                        ->where('end_time', '>', $startTimeFormatted);
                })
                ->first();

            if ($roomConflict) {
                return "Bentrok Ruangan: Ruangan '{$roomOrLink}' telah digunakan untuk sesi '{$roomConflict->session_title}' pada tanggal {$sessionDate} pukul {$roomConflict->start_time} - {$roomConflict->end_time}.";
            }
        }

        return null;
    }

    /**
     * Create a new course schedule session.
     */
    public static function create(array $data, ?int $creatorId = null): CourseSchedule
    {
        return DB::transaction(function () use ($data, $creatorId) {
            $data['created_by'] = $creatorId;
            $data['status'] = $data['status'] ?? 'scheduled';

            $schedule = CourseSchedule::create($data);

            Log::info('Course schedule created', [
                'schedule_id' => $schedule->id,
                'course_id' => $schedule->course_id,
                'mentor_id' => $schedule->mentor_id,
                'session_date' => $schedule->session_date,
            ]);

            return $schedule;
        });
    }

    /**
     * Update an existing course schedule session.
     */
    public static function update(int|string $id, array $data): CourseSchedule
    {
        return DB::transaction(function () use ($id, $data) {
            $schedule = CourseSchedule::lockForUpdate()->findOrFail($id);
            $schedule->update($data);

            Log::info('Course schedule updated', [
                'schedule_id' => $schedule->id,
                'course_id' => $schedule->course_id,
            ]);

            return $schedule;
        });
    }

    /**
     * Cek apakah sesi jadwal sudah memiliki data presensi/absensi atau keterkaitan materi.
     */
    public static function hasAttendanceOrMaterials(int|string $id): bool
    {
        $schedule = CourseSchedule::withCount('attendances')->with('course')->findOrFail($id);

        if ($schedule->attendances_count > 0 || ! empty($schedule->attendance_token)) {
            return true;
        }

        // Cek apakah pelatihan memiliki materi silabus yang sudah terbit/dibuat
        if ($schedule->course && $schedule->course->chapters()->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Batalkan sesi jadwal dan simpan alasan pembatalan.
     */
    public static function cancel(int|string $id, string $reason): CourseSchedule
    {
        return DB::transaction(function () use ($id, $reason) {
            $schedule = CourseSchedule::lockForUpdate()->findOrFail($id);
            $schedule->update([
                'status' => 'cancelled',
                'cancellation_reason' => trim($reason),
                'is_attendance_open' => false,
            ]);

            Log::info('Course schedule cancelled', [
                'schedule_id' => $schedule->id,
                'reason' => $reason,
            ]);

            return $schedule;
        });
    }

    /**
     * Delete a course schedule session.
     * Aturan penting: Sesi yang sudah memiliki absensi atau materi tidak boleh dihapus permanen.
     */
    public static function delete(int|string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $schedule = CourseSchedule::withCount('attendances')->with('course')->findOrFail($id);

            if ($schedule->attendances_count > 0 || ! empty($schedule->attendance_token)) {
                throw new \DomainException('Sesi jadwal tidak dapat dihapus permanen karena sudah memiliki data absensi/token presensi. Silakan batalkan sesi jadwal ini.');
            }

            if ($schedule->course && $schedule->course->chapters()->count() > 0) {
                throw new \DomainException('Sesi jadwal tidak dapat dihapus permanen karena pelatihan telah memiliki materi kurikulum. Silakan batalkan sesi jadwal ini.');
            }

            $deleted = $schedule->delete();

            Log::info('Course schedule deleted', ['schedule_id' => $id]);

            return (bool) $deleted;
        });
    }

    /**
     * Get approved/published courses for schedule assignment.
     * Aturan penting: Jadwal hanya dibuat untuk pelatihan yang sudah disetujui / dibuka.
     */
    public static function getCoursesList(): Collection
    {
        return Course::query()
            ->whereIn('status', [CourseStatus::Approved, CourseStatus::Published, CourseStatus::Ongoing])
            ->orderBy('title')
            ->get(['id', 'title', 'code', 'type', 'method', 'status', 'start_date', 'end_date']);
    }

    /**
     * Get list of mentors (users with role mentor or admin) for schedule assignment.
     */
    public static function getMentorsList(): Collection
    {
        return User::query()
            ->whereIn('role', ['mentor', 'admin'])
            ->orderBy('name')
            ->get(['id', 'name', 'nip', 'role']);
    }
}

<?php

namespace App\Repositories;

use App\Models\Attendance;
use App\Models\CourseSchedule;
use App\Models\CourseUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AbsensiRepo
{
    /**
     * Query builder for Yajra DataTables server-side rendering.
     * Note: Mengembalikan instance query Builder untuk pagination server-side Yajra DataTables.
     */
    public static function getDt(?int $courseId = null, ?string $date = null, ?string $tokenStatus = null, ?int $mentorId = null): Builder
    {
        $query = CourseSchedule::query()
            ->with(['course', 'mentor', 'attendances'])
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

        if ($tokenStatus === 'active') {
            $query->where('is_attendance_open', true)
                ->whereNotNull('attendance_token')
                ->where('token_expires_at', '>', now());
        } elseif ($tokenStatus === 'unopened') {
            $query->whereNull('attendance_token');
        } elseif ($tokenStatus === 'expired' || $tokenStatus === 'closed') {
            $query->where(function ($q) {
                $q->where(function ($sq) {
                    $sq->whereNotNull('attendance_token')
                        ->where('token_expires_at', '<=', now());
                })->orWhere(function ($sq) {
                    $sq->where('is_attendance_open', false)
                        ->whereNotNull('attendance_token');
                });
            });
        }

        return $query->orderBy('session_date', 'desc')->orderBy('start_time', 'asc');
    }

    /**
     * Find schedule by ID with full relations.
     */
    public static function getById(int|string $scheduleId): CourseSchedule
    {
        return CourseSchedule::with(['course.participants', 'mentor', 'attendances.user'])->findOrFail($scheduleId);
    }

    /**
     * Generate / Buka Sesi Token Absensi Elektronik.
     * Default masa berlaku token: 15 menit per sesi diklat.
     */
    public static function openAttendanceSession(CourseSchedule $schedule, int $validityMinutes = 15): string
    {
        // Token 6-digit angka acak aman (100000 - 999999)
        $token = (string) random_int(100000, 999999);

        $schedule->update([
            'attendance_token' => $token,
            'token_validity_minutes' => $validityMinutes,
            'token_expires_at' => now()->addMinutes($validityMinutes),
            'is_attendance_open' => true,
        ]);

        Log::info('Absensi: Token sesi dibuka', [
            'schedule_id' => $schedule->id,
            'token' => $token,
            'validity_minutes' => $validityMinutes,
            'expires_at' => $schedule->token_expires_at,
        ]);

        return $token;
    }

    /**
     * Tutup Sesi Token Absensi Lebih Awal.
     */
    public static function closeAttendanceSession(CourseSchedule $schedule): bool
    {
        $schedule->update([
            'is_attendance_open' => false,
            'token_expires_at' => now(),
        ]);

        Log::info('Absensi: Token sesi ditutup', [
            'schedule_id' => $schedule->id,
        ]);

        return true;
    }

    /**
     * Input Peserta: Presensi Mandiri via Token 6 Digit.
     *
     * @return array{success: bool, message: string, attendance?: Attendance}
     */
    public static function checkInPeserta(User $user, string $token): array
    {
        $cleanToken = trim($token);

        if (empty($cleanToken) || strlen($cleanToken) !== 6) {
            return [
                'success' => false,
                'message' => 'Format kode token tidak valid (harus 6 digit angka).',
            ];
        }

        // 1. Cari sesi aktif dengan token tersebut
        $schedule = CourseSchedule::query()
            ->where('attendance_token', $cleanToken)
            ->where('is_attendance_open', true)
            ->where('token_expires_at', '>', now())
            ->first();

        if (! $schedule) {
            return [
                'success' => false,
                'message' => 'Kode token salah atau masa berlaku sesi absensi telah kedaluwarsa.',
            ];
        }

        // 2. Validasi apakah user terdaftar resmi & terverifikasi pada kursus terkait
        $enrollment = CourseUser::query()
            ->where('user_id', $user->id)
            ->where('course_id', $schedule->course_id)
            ->whereIn('status', ['verified', 'active', 'completed'])
            ->first();

        if (! $enrollment) {
            return [
                'success' => false,
                'message' => 'Anda tidak terdaftar sebagai peserta terverifikasi pada pelatihan ini.',
            ];
        }

        // 3. Cek apakah user sudah pernah absen pada sesi ini
        $existing = Attendance::query()
            ->where('schedule_id', $schedule->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return [
                'success' => false,
                'message' => 'Anda sudah tercatat melakukan presensi untuk sesi ini (Status: '.ucfirst($existing->status).').',
            ];
        }

        // 4. Rekam presensi kehadiran
        return DB::transaction(function () use ($schedule, $user) {
            $attendance = Attendance::create([
                'schedule_id' => $schedule->id,
                'user_id' => $user->id,
                'status' => 'hadir',
                'check_in_at' => now(),
                'is_manual_correction' => false,
            ]);

            Log::info('Absensi: Peserta berhasil check-in', [
                'schedule_id' => $schedule->id,
                'user_id' => $user->id,
                'time' => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Presensi berhasil direkam! Selamat mengikuti sesi pembelajaran.',
                'attendance' => $attendance,
            ];
        });
    }

    /**
     * Dapatkan Rekapitulasi Presensi Seluruh Peserta Pelatihan untuk Sesi Ini.
     *
     * @return array{
     *     schedule: CourseSchedule,
     *     total_enrolled: int,
     *     hadir_count: int,
     *     terlambat_count: int,
     *     izin_count: int,
     *     sakit_count: int,
     *     alpa_count: int,
     *     belum_absen_count: int,
     *     attendance_percentage: float,
     *     participants: array
     * }
     */
    public static function getScheduleAttendanceSheet(CourseSchedule $schedule): array
    {
        // 1. Ambil seluruh peserta terverifikasi / aktif pada kursus
        $enrollments = CourseUser::with('user')
            ->where('course_id', $schedule->course_id)
            ->whereIn('status', ['verified', 'active', 'completed'])
            ->get();

        // 2. Ambil seluruh data presensi sesi ini
        $attendances = Attendance::with('corrector')
            ->where('schedule_id', $schedule->id)
            ->get()
            ->keyBy('user_id');

        $hadirCount = 0;
        $terlambatCount = 0;
        $izinCount = 0;
        $sakitCount = 0;
        $alpaCount = 0;
        $belumAbsenCount = 0;

        $participantSheet = [];

        foreach ($enrollments as $enrollment) {
            $user = $enrollment->user;
            if (! $user) {
                continue;
            }

            $att = $attendances->get($user->id);
            $status = $att ? $att->status : 'belum_absen';

            match ($status) {
                'hadir' => $hadirCount++,
                'terlambat' => $terlambatCount++,
                'izin' => $izinCount++,
                'sakit' => $sakitCount++,
                'alpa' => $alpaCount++,
                default => $belumAbsenCount++,
            };

            $participantSheet[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'nip' => $user->nip ?? '-',
                'email' => $user->email,
                'agency' => $user->agency ?? 'Pemerintah Kabupaten Aceh Timur',
                'attendance_id' => $att?->id,
                'status' => $status,
                'check_in_at' => $att?->check_in_at?->format('H:i:s d/m/Y') ?? '-',
                'is_manual_correction' => (bool) ($att?->is_manual_correction ?? false),
                'correction_reason' => $att?->correction_reason,
                'corrected_by_name' => $att?->corrector?->name,
            ];
        }

        $totalEnrolled = count($participantSheet);
        $totalPresent = $hadirCount + $terlambatCount;
        $percentage = $totalEnrolled > 0 ? round(($totalPresent / $totalEnrolled) * 100, 1) : 0;

        return [
            'schedule' => $schedule,
            'total_enrolled' => $totalEnrolled,
            'hadir_count' => $hadirCount,
            'terlambat_count' => $terlambatCount,
            'izin_count' => $izinCount,
            'sakit_count' => $sakitCount,
            'alpa_count' => $alpaCount,
            'belum_absen_count' => $belumAbsenCount,
            'attendance_percentage' => $percentage,
            'participants' => $participantSheet,
        ];
    }

    /**
     * Koreksi Manual Status Kehadiran (Admin/Mentor).
     * Wajib menyertakan correction_reason untuk rekam jejak audit perubahan presensi.
     */
    public static function applyManualCorrection(
        int $scheduleId,
        int $userId,
        string $status,
        string $reason,
        int $correctorId
    ): Attendance {
        return DB::transaction(function () use ($scheduleId, $userId, $status, $reason, $correctorId) {
            $attendance = Attendance::updateOrCreate(
                [
                    'schedule_id' => $scheduleId,
                    'user_id' => $userId,
                ],
                [
                    'status' => $status,
                    'check_in_at' => now(),
                    'is_manual_correction' => true,
                    'correction_reason' => trim($reason),
                    'corrected_by' => $correctorId,
                ]
            );

            Log::info('Absensi: Koreksi manual status kehadiran', [
                'schedule_id' => $scheduleId,
                'user_id' => $userId,
                'new_status' => $status,
                'reason' => $reason,
                'corrected_by' => $correctorId,
            ]);

            return $attendance;
        });
    }
}

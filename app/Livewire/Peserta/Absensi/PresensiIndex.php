<?php

namespace App\Livewire\Peserta\Absensi;

use App\Models\Attendance;
use App\Models\CourseSchedule;
use App\Models\CourseUser;
use App\Repositories\AbsensiRepo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
#[Title('Presensi Elektronik Pelatihan - SIMPEL BKPSDM')]
class PresensiIndex extends Component
{
    #[Url]
    public string $token = '';

    public string $method = 'token';

    public ?int $previewScheduleId = null;

    public ?string $previewCourseTitle = null;

    public ?string $previewSessionTitle = null;

    public ?string $previewMentorName = null;

    public ?string $previewRoomOrLink = null;

    public ?string $previewDate = null;

    public ?string $previewTime = null;

    public ?string $previewExpiresAt = null;

    public bool $isVerifiedForCourse = false;

    public ?Attendance $existingAttendance = null;

    public ?array $lastCheckInResult = null;

    public string $errorMessage = '';

    public function mount(): void
    {
        if (! empty($this->token)) {
            $this->method = 'qr';
            $this->checkTokenPreview();
        }
    }

    public function updatedToken(): void
    {
        $this->errorMessage = '';
        $this->lastCheckInResult = null;
        $this->token = preg_replace('/[^0-9]/', '', $this->token);
        if (strlen($this->token) > 6) {
            $this->token = substr($this->token, 0, 6);
        }

        if (strlen($this->token) === 6) {
            $this->checkTokenPreview();
        } else {
            $this->resetPreview();
        }
    }

    public function checkTokenPreview(): void
    {
        $this->errorMessage = '';
        $cleanToken = trim($this->token);

        if (strlen($cleanToken) !== 6) {
            $this->resetPreview();

            return;
        }

        $schedule = CourseSchedule::with(['course', 'mentor'])
            ->where('attendance_token', $cleanToken)
            ->where('is_attendance_open', true)
            ->where('status', '!=', 'cancelled')
            ->where('token_expires_at', '>', now())
            ->first();

        if (! $schedule) {
            $this->resetPreview();
            $this->errorMessage = 'Kode token ['.$cleanToken.'] tidak ditemukan, tidak aktif, atau masa berlakunya telah kedaluwarsa.';

            return;
        }

        $user = Auth::user();
        $this->previewScheduleId = $schedule->id;
        $this->previewCourseTitle = $schedule->course?->title ?? '-';
        $this->previewSessionTitle = $schedule->session_title;
        $this->previewMentorName = $schedule->mentor?->name ?? '-';
        $this->previewRoomOrLink = $schedule->room_or_link ?? '-';
        $this->previewDate = $schedule->session_date?->translatedFormat('l, d F Y') ?? '-';
        $this->previewTime = ($schedule->start_time ? substr($schedule->start_time, 0, 5) : '-').' - '.($schedule->end_time ? substr($schedule->end_time, 0, 5) : '-').' WIB';
        $this->previewExpiresAt = $schedule->token_expires_at?->format('H:i') ?? '-';

        // Check user enrollment & verification
        $enrollment = CourseUser::where('user_id', $user->id)
            ->where('course_id', $schedule->course_id)
            ->whereIn('status', ['verified', 'active', 'completed'])
            ->first();

        $this->isVerifiedForCourse = (bool) $enrollment;

        // Check if user already attended
        $this->existingAttendance = Attendance::where('schedule_id', $schedule->id)
            ->where('user_id', $user->id)
            ->first();
    }

    public function resetPreview(): void
    {
        $this->previewScheduleId = null;
        $this->previewCourseTitle = null;
        $this->previewSessionTitle = null;
        $this->previewMentorName = null;
        $this->previewRoomOrLink = null;
        $this->previewDate = null;
        $this->previewTime = null;
        $this->previewExpiresAt = null;
        $this->isVerifiedForCourse = false;
        $this->existingAttendance = null;
    }

    public function fillToken(string $token): void
    {
        $this->token = $token;
        $this->method = 'token';
        $this->checkTokenPreview();
    }

    public function submitPresensi(): void
    {
        $this->errorMessage = '';
        $this->lastCheckInResult = null;

        $this->validate([
            'token' => 'required|digits:6',
        ], [
            'token.required' => 'Silakan masukkan 6 digit kode token absensi.',
            'token.digits' => 'Kode token absensi harus terdiri dari 6 digit angka.',
        ]);

        $user = Auth::user();
        $result = AbsensiRepo::checkInPeserta($user, $this->token, $this->method);

        if (! $result['success']) {
            $this->errorMessage = $result['message'];

            return;
        }

        /** @var Attendance $attendance */
        $attendance = $result['attendance'];

        $this->lastCheckInResult = [
            'status' => $attendance->status,
            'status_label' => match ($attendance->status) {
                'hadir' => 'Hadir Tepat Waktu',
                'terlambat' => 'Hadir Terlambat',
                default => ucfirst($attendance->status),
            },
            'check_in_at' => $attendance->check_in_at?->format('H:i:s d/m/Y') ?? now()->format('H:i:s d/m/Y'),
            'method' => $attendance->method === 'qr' ? 'Scan QR Code' : 'Input Token Mandiri',
            'message' => $result['message'],
        ];

        // Refresh existing attendance state
        $this->existingAttendance = $attendance;
    }

    public function render()
    {
        $user = Auth::user();

        // 1. Ambil daftar kursus terverifikasi peserta
        $enrolledCourseIds = CourseUser::where('user_id', $user->id)
            ->whereIn('status', ['verified', 'active', 'completed'])
            ->pluck('course_id');

        // 2. Ambil sesi yang dijadwalkan dari kursus yang diikuti (termasuk yang aktif/buka hari ini)
        $todaySchedules = CourseSchedule::with(['course', 'mentor'])
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('status', '!=', 'cancelled')
            ->orderBy('session_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(5)
            ->get();

        // 3. Ambil riwayat presensi peserta
        $recentAttendances = Attendance::with(['schedule.course', 'schedule.mentor'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return view('mods.peserta.absensi.presensi-index', compact('todaySchedules', 'recentAttendances'));
    }
}

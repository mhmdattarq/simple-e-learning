<?php

namespace App\Livewire\Admin\Absensi;

use App\Models\Course;
use App\Models\CourseSchedule;
use App\Repositories\AbsensiRepo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('templates.layouts.app')]
class AbsensiData extends Component
{
    // Filter pencarian
    public ?int $filterCourse = null;

    public ?string $filterDate = null;

    public ?string $filterTokenStatus = null;

    // State Modal Token Sesi
    public ?int $selectedScheduleId = null;

    public ?string $selectedScheduleTitle = null;

    public ?string $selectedCourseTitle = null;

    public int $tokenValidityMinutes = 15;

    public int $lateThresholdMinutes = 15;

    public bool $autoAlpaOnClose = true;

    public ?string $activeToken = null;

    public ?string $activeTokenExpires = null;

    public bool $isTokenActive = false;

    // State Modal Rekap Presensi & Koreksi Manual
    public ?int $sheetScheduleId = null;

    public ?array $attendanceSheet = null;

    public ?int $correctionUserId = null;

    public string $correctionUserName = '';

    public string $correctionStatus = 'hadir';

    public string $correctionReason = '';

    public function mount(): void
    {
        // Default mount
    }

    /**
     * Buka Modal Pengaturan / Generate Token Sesi.
     * Otorisasi: Admin atau Mentor pengampu sesi diklat.
     */
    public function hookModalToken(int $scheduleId): void
    {
        $schedule = CourseSchedule::with('course')->find($scheduleId);
        if (! $schedule) {
            $this->dispatch('alert-show', data: ['type' => 'danger', 'message' => 'Sesi jadwal tidak ditemukan.']);

            return;
        }

        $user = Auth::user();
        if (! $user || ! AbsensiRepo::canManage($user, $schedule)) {
            $this->dispatch('alert-show', data: ['type' => 'danger', 'message' => 'Akses ditolak: Hanya Admin atau mentor pengampu sesi ini yang berhak membuka atau mengatur token absensi.']);

            return;
        }

        $this->selectedScheduleId = $schedule->id;
        $this->selectedScheduleTitle = $schedule->session_title;
        $this->selectedCourseTitle = $schedule->course?->title ?? '-';
        $this->tokenValidityMinutes = $schedule->token_validity_minutes ?: 15;
        $this->lateThresholdMinutes = $schedule->late_threshold_minutes ?: 15;
        $this->activeToken = $schedule->attendance_token;
        $this->activeTokenExpires = $schedule->token_expires_at?->format('H:i') ?? null;
        $this->isTokenActive = $schedule->isAttendanceActive();

        $this->dispatch('open-modal-token');
    }

    /**
     * Submit Buka / Regenerate Token Sesi.
     * Otorisasi: Admin atau Mentor pengampu sesi diklat.
     */
    public function submitOpenToken(): void
    {
        $this->validate([
            'tokenValidityMinutes' => 'required|integer|min:5|max:180',
            'lateThresholdMinutes' => 'required|integer|min:1|max:180',
        ], [
            'tokenValidityMinutes.required' => 'Masa berlaku token wajib diisi.',
            'tokenValidityMinutes.min' => 'Masa berlaku minimal 5 menit.',
            'tokenValidityMinutes.max' => 'Masa berlaku maksimal 180 menit (3 jam).',
            'lateThresholdMinutes.required' => 'Batas waktu keterlambatan wajib diisi.',
            'lateThresholdMinutes.min' => 'Batas waktu keterlambatan minimal 1 menit.',
            'lateThresholdMinutes.max' => 'Batas waktu keterlambatan maksimal 180 menit.',
        ]);

        if (! $this->selectedScheduleId) {
            return;
        }

        $schedule = CourseSchedule::findOrFail($this->selectedScheduleId);

        $user = Auth::user();
        if (! $user || ! AbsensiRepo::canManage($user, $schedule)) {
            $this->dispatch('alert-show', data: ['type' => 'danger', 'message' => 'Akses ditolak: Anda tidak memiliki wewenang untuk membuka token sesi ini.']);

            return;
        }

        $token = AbsensiRepo::openAttendanceSession($schedule, $this->tokenValidityMinutes, $this->lateThresholdMinutes);

        $fresh = $schedule->fresh();
        $this->activeToken = $token;
        $this->activeTokenExpires = $fresh->token_expires_at?->format('H:i');
        $this->isTokenActive = true;

        if ($this->sheetScheduleId === $schedule->id) {
            $this->attendanceSheet = AbsensiRepo::getScheduleAttendanceSheet($fresh);
        }

        $this->dispatch('alert-show', data: [
            'type' => 'success',
            'message' => "Token absensi [{$token}] berhasil dibuka dan aktif selama {$this->tokenValidityMinutes} menit.",
        ]);
        $this->dispatch('reloadDT');
        $this->dispatch('render-qr', token: $token);
    }

    /**
     * Tutup Sesi Token Absensi.
     * Otorisasi: Admin atau Mentor pengampu sesi diklat.
     */
    public function closeToken(int $scheduleId): void
    {
        $schedule = CourseSchedule::find($scheduleId);
        if (! $schedule) {
            return;
        }

        $user = Auth::user();
        if (! $user || ! AbsensiRepo::canManage($user, $schedule)) {
            $this->dispatch('alert-show', data: ['type' => 'danger', 'message' => 'Akses ditolak: Anda tidak memiliki wewenang untuk menutup token sesi ini.']);

            return;
        }

        AbsensiRepo::closeAttendanceSession($schedule, $this->autoAlpaOnClose);

        $fresh = $schedule->fresh();
        if ($this->selectedScheduleId === $scheduleId) {
            $this->isTokenActive = false;
        }

        if ($this->sheetScheduleId === $scheduleId) {
            $this->attendanceSheet = AbsensiRepo::getScheduleAttendanceSheet($fresh);
        }

        $this->dispatch('alert-show', data: ['type' => 'info', 'message' => 'Sesi token absensi telah ditutup.']);
        $this->dispatch('reloadDT');
    }

    /**
     * Aksi Tombol Kelola Absensi:
     * Menyiapkan data sesi, kontrol token, dan lembar rekapitulasi presensi.
     */
    #[On('open-manage-attendance')]
    public function openManageAttendance(int $scheduleId): void
    {
        $schedule = CourseSchedule::with(['course', 'mentor'])->find($scheduleId);
        if (! $schedule) {
            $this->dispatch('alert-show', data: ['type' => 'danger', 'message' => 'Sesi jadwal tidak ditemukan.']);

            return;
        }

        // Set context token
        $this->selectedScheduleId = $schedule->id;
        $this->selectedScheduleTitle = $schedule->session_title;
        $this->selectedCourseTitle = $schedule->course?->title ?? '-';
        $this->tokenValidityMinutes = $schedule->token_validity_minutes ?: 15;
        $this->lateThresholdMinutes = $schedule->late_threshold_minutes ?: 15;
        $this->activeToken = $schedule->attendance_token;
        $this->activeTokenExpires = $schedule->token_expires_at?->format('H:i') ?? null;
        $this->isTokenActive = $schedule->isAttendanceActive();

        // Set context lembar rekapitulasi kehadiran
        $this->sheetScheduleId = $schedule->id;
        $this->attendanceSheet = AbsensiRepo::getScheduleAttendanceSheet($schedule);

        $this->dispatch('open-modal-sheet');

        if ($this->isTokenActive && $this->activeToken) {
            $this->dispatch('render-qr', token: $this->activeToken);
        }
    }

    /**
     * Segarkan Lembar Presensi Sesi Saat Ini.
     */
    public function refreshAttendanceSheet(): void
    {
        if (! $this->sheetScheduleId) {
            return;
        }

        $schedule = CourseSchedule::with(['course', 'mentor'])->find($this->sheetScheduleId);
        if ($schedule) {
            $this->attendanceSheet = AbsensiRepo::getScheduleAttendanceSheet($schedule);
            $this->isTokenActive = $schedule->isAttendanceActive();
            $this->activeToken = $schedule->attendance_token;
            $this->activeTokenExpires = $schedule->token_expires_at?->format('H:i');

            if ($this->isTokenActive && $this->activeToken) {
                $this->dispatch('render-qr', token: $this->activeToken);
            }
        }
    }

    /**
     * Buka Drawer / Modal Rekapitulasi Presensi Peserta.
     */
    public function showAttendanceDetail(int $scheduleId): void
    {
        $schedule = CourseSchedule::with(['course', 'mentor'])->find($scheduleId);
        if (! $schedule) {
            $this->dispatch('alert-show', data: ['type' => 'danger', 'message' => 'Sesi jadwal tidak ditemukan.']);

            return;
        }

        $this->sheetScheduleId = $schedule->id;
        $this->attendanceSheet = AbsensiRepo::getScheduleAttendanceSheet($schedule);

        $this->dispatch('open-modal-sheet');
    }

    /**
     * Siapkan Form Modal Koreksi Manual Kehadiran.
     */
    public function hookModalCorrection(int $userId, string $userName, string $currentStatus): void
    {
        $this->correctionUserId = $userId;
        $this->correctionUserName = $userName;
        $this->correctionStatus = in_array($currentStatus, ['hadir', 'terlambat', 'izin', 'sakit', 'alpa']) ? $currentStatus : 'hadir';
        $this->correctionReason = '';

        $this->dispatch('open-modal-correction');
    }

    /**
     * Simpan Koreksi Manual Kehadiran.
     */
    public function submitCorrection(): void
    {
        $this->validate([
            'correctionStatus' => 'required|in:hadir,terlambat,izin,sakit,alpa',
            'correctionReason' => 'required|string|min:3|max:500',
        ], [
            'correctionStatus.required' => 'Status kehadiran wajib dipilih.',
            'correctionStatus.in' => 'Status kehadiran tidak valid.',
            'correctionReason.required' => 'Alasan koreksi kehadiran wajib diisi.',
            'correctionReason.min' => 'Alasan koreksi minimal 3 karakter.',
        ]);

        if (! $this->sheetScheduleId || ! $this->correctionUserId) {
            return;
        }

        $correctorId = Auth::id();
        AbsensiRepo::applyManualCorrection(
            $this->sheetScheduleId,
            $this->correctionUserId,
            $this->correctionStatus,
            $this->correctionReason,
            $correctorId
        );

        // Refresh attendance sheet
        $schedule = CourseSchedule::findOrFail($this->sheetScheduleId);
        $this->attendanceSheet = AbsensiRepo::getScheduleAttendanceSheet($schedule);

        $this->dispatch('close-modal-correction');
        $this->dispatch('alert-show', data: [
            'type' => 'success',
            'message' => 'Status kehadiran peserta berhasil dikoreksi.',
        ]);
        $this->dispatch('reloadDT');
    }

    public function render()
    {
        $user = Auth::user();
        $isMentor = $user?->isMentor() ?? false;

        $courses = Course::query()->orderBy('title', 'asc')->get();

        // Ringkasan Statistik Kartu Atas
        $today = now()->toDateString();
        $queryToday = CourseSchedule::whereDate('session_date', $today);
        $queryActive = CourseSchedule::where('is_attendance_open', true)
            ->where('token_expires_at', '>', now());

        if ($isMentor && $user) {
            $queryToday->where('mentor_id', $user->id);
            $queryActive->where('mentor_id', $user->id);
        }

        $totalSessionsToday = $queryToday->count();
        $activeSessionsNow = $queryActive->count();

        return view('mods.admin.absensi.absensi-data', [
            'courses' => $courses,
            'totalSessionsToday' => $totalSessionsToday,
            'activeSessionsNow' => $activeSessionsNow,
            'isMentor' => $isMentor,
        ]);
    }
}

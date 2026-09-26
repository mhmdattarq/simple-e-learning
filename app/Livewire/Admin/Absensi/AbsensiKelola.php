<?php

namespace App\Livewire\Admin\Absensi;

use App\Models\CourseSchedule;
use App\Repositories\AbsensiRepo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.app')]
#[Title('Kelola Absensi Sesi Pelatihan - SIMPEL BKPSDM')]
class AbsensiKelola extends Component
{
    public CourseSchedule $schedule;

    // Token Settings
    public int $tokenValidityMinutes = 15;

    public int $lateThresholdMinutes = 15;

    public bool $autoAlpaOnClose = true;

    // State Active Token
    public ?string $activeToken = null;

    public ?string $activeTokenExpires = null;

    public bool $isTokenActive = false;

    // Sheet Attendance
    public ?array $attendanceSheet = null;

    // Manual Correction Modal State
    public ?int $correctionUserId = null;

    public string $correctionUserName = '';

    public string $correctionStatus = 'hadir';

    public string $correctionReason = '';

    public function mount(int|string $id): void
    {
        $this->schedule = CourseSchedule::with(['course', 'mentor'])->findOrFail($id);

        $user = Auth::user();
        abort_if(! $user || ! AbsensiRepo::canManage($user, $this->schedule), 403, 'Akses ditolak: Anda tidak memiliki wewenang mengelola sesi ini.');

        $this->tokenValidityMinutes = $this->schedule->token_validity_minutes ?: 15;
        $this->lateThresholdMinutes = $this->schedule->late_threshold_minutes ?: 15;
        $this->activeToken = $this->schedule->attendance_token;
        $this->activeTokenExpires = $this->schedule->token_expires_at?->format('H:i');
        $this->isTokenActive = $this->schedule->isAttendanceActive();

        $this->loadAttendanceSheet();
    }

    public function loadAttendanceSheet(): void
    {
        $this->schedule->refresh();
        $this->attendanceSheet = AbsensiRepo::getScheduleAttendanceSheet($this->schedule);
        $this->isTokenActive = $this->schedule->isAttendanceActive();
        $this->activeToken = $this->schedule->attendance_token;
        $this->activeTokenExpires = $this->schedule->token_expires_at?->format('H:i');
    }

    public function submitOpenToken(): void
    {
        $user = Auth::user();
        abort_if(! $user || ! AbsensiRepo::canManage($user, $this->schedule), 403);

        $this->validate([
            'tokenValidityMinutes' => 'required|integer|min:5|max:180',
            'lateThresholdMinutes' => 'required|integer|min:1|max:180',
        ], [
            'tokenValidityMinutes.required' => 'Masa berlaku token wajib diisi.',
            'tokenValidityMinutes.min' => 'Masa berlaku minimal 5 menit.',
            'tokenValidityMinutes.max' => 'Masa berlaku maksimal 180 menit.',
            'lateThresholdMinutes.required' => 'Batas waktu keterlambatan wajib diisi.',
            'lateThresholdMinutes.min' => 'Batas waktu keterlambatan minimal 1 menit.',
            'lateThresholdMinutes.max' => 'Batas waktu keterlambatan maksimal 180 menit.',
        ]);

        $token = AbsensiRepo::openAttendanceSession($this->schedule, $this->tokenValidityMinutes, $this->lateThresholdMinutes);

        $this->loadAttendanceSheet();

        $this->dispatch('alert-show', data: [
            'type' => 'success',
            'message' => "Token absensi [{$token}] berhasil dibuka dan aktif selama {$this->tokenValidityMinutes} menit.",
        ]);
        $this->dispatch('render-qr', token: $token);
    }

    public function closeToken(): void
    {
        $user = Auth::user();
        abort_if(! $user || ! AbsensiRepo::canManage($user, $this->schedule), 403);

        AbsensiRepo::closeAttendanceSession($this->schedule, $this->autoAlpaOnClose);

        $this->loadAttendanceSheet();

        $this->dispatch('alert-show', data: [
            'type' => 'info',
            'message' => 'Sesi token absensi telah ditutup.',
        ]);
    }

    public function hookModalCorrection(int $userId, string $userName, string $currentStatus): void
    {
        $this->correctionUserId = $userId;
        $this->correctionUserName = $userName;
        $this->correctionStatus = in_array($currentStatus, ['hadir', 'terlambat', 'izin', 'sakit', 'alpa']) ? $currentStatus : 'hadir';
        $this->correctionReason = '';

        $this->dispatch('open-modal-correction');
    }

    public function submitCorrection(): void
    {
        $user = Auth::user();
        abort_if(! $user || ! AbsensiRepo::canManage($user, $this->schedule), 403);

        $this->validate([
            'correctionStatus' => 'required|in:hadir,terlambat,izin,sakit,alpa',
            'correctionReason' => 'required|string|min:3|max:500',
        ], [
            'correctionStatus.required' => 'Status kehadiran wajib dipilih.',
            'correctionStatus.in' => 'Status kehadiran tidak valid.',
            'correctionReason.required' => 'Alasan koreksi kehadiran wajib diisi.',
            'correctionReason.min' => 'Alasan koreksi minimal 3 karakter.',
        ]);

        if (! $this->correctionUserId) {
            return;
        }

        AbsensiRepo::applyManualCorrection(
            $this->schedule->id,
            $this->correctionUserId,
            $this->correctionStatus,
            $this->correctionReason,
            Auth::id()
        );

        $this->loadAttendanceSheet();

        $this->dispatch('close-modal-correction');
        $this->dispatch('alert-show', data: [
            'type' => 'success',
            'message' => 'Status kehadiran peserta berhasil dikoreksi.',
        ]);
    }

    public function render()
    {
        return view('mods.admin.absensi.absensi-kelola');
    }
}

<?php

namespace App\Livewire\Admin\Verifikasi;

use App\Models\Course;
use App\Repositories\VerifikasiRepo;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('templates.layouts.app')]
class VerifikasiData extends Component
{
    public string $statusFilter = 'all';

    public ?int $courseFilter = null;

    public array $stats = [];

    public ?array $selectedVerification = null;

    public ?array $selectedDetail = null;

    /**
     * Form state for verification decision.
     */
    public array $verifyForm = [
        'id' => null,
        'status' => 'verified',
        'verification_notes' => '',
    ];

    public function mount(): void
    {
        $this->refreshStats();
    }

    public function refreshStats(): void
    {
        $this->stats = VerifikasiRepo::getStats();
    }

    /**
     * Open verification action modal.
     */
    public function openVerifyModal($id): void
    {
        $this->resetValidation();

        try {
            $reg = VerifikasiRepo::getById($id);

            $this->selectedVerification = [
                'id' => $reg->id,
                'registration_number' => $reg->registration_number,
                'status' => $reg->status->value,
                'status_label' => $reg->status->label(),
                'status_badge' => $reg->status->badgeClass(),
                'enrolled_at' => $reg->enrolled_at?->format('d M Y, H:i').' WIB',
                'recommendation_letter_url' => $reg->recommendation_letter_path
                    ? asset('storage/'.$reg->recommendation_letter_path)
                    : null,
                'user_name' => $reg->user?->name ?? '-',
                'user_nip' => $reg->user?->nip ?? '-',
                'user_email' => $reg->user?->email ?? '-',
                'user_phone' => $reg->user?->phone_number ?? '-',
                'user_opd' => $reg->user?->opd_agency ?? '-',
                'user_position' => $reg->user?->position ?? '-',
                'user_rank' => $reg->user?->rank_class ?? '-',
                'course_title' => $reg->course?->title ?? '-',
                'course_code' => $reg->course?->code ?? '-',
            ];

            $initialStatus = in_array($reg->status->value, ['verified', 'revision_required', 'rejected'], true)
                ? $reg->status->value
                : 'verified';

            $this->verifyForm = [
                'id' => $reg->id,
                'status' => $initialStatus,
                'verification_notes' => $reg->verification_notes ?? '',
            ];

            $this->dispatch('openModal', id: 'modalVerifikasiAction');
        } catch (\Exception $e) {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Tidak dapat memuat berkas verifikasi: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Submit verification decision (Diverifikasi, Perlu Perbaikan, Ditolak).
     */
    public function submitVerification(): void
    {
        $this->validate([
            'verifyForm.id' => 'required|exists:course_user,id',
            'verifyForm.status' => 'required|in:verified,revision_required,rejected',
            'verifyForm.verification_notes' => 'required_if:verifyForm.status,revision_required,rejected',
        ], [
            'verifyForm.status.required' => 'Keputusan verifikasi wajib dipilih.',
            'verifyForm.status.in' => 'Pilihan status keputusan tidak valid.',
            'verifyForm.verification_notes.required_if' => 'Catatan perbaikan / alasan wajib diisi saat memilih Perlu Perbaikan atau Ditolak.',
        ]);

        try {
            $verifierId = auth()->id();

            VerifikasiRepo::verify(
                $this->verifyForm['id'],
                $this->verifyForm['status'],
                $this->verifyForm['verification_notes'],
                $verifierId
            );

            $this->refreshStats();
            $this->dispatch('closeModal', id: 'modalVerifikasiAction');

            $statusLabel = match ($this->verifyForm['status']) {
                'verified' => 'Diverifikasi / Diterima',
                'revision_required' => 'Perlu Perbaikan',
                'rejected' => 'Ditolak',
                default => $this->verifyForm['status'],
            };

            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => "Pendaftaran berhasil diperbarui menjadi: {$statusLabel}.",
            ]);

            $this->dispatch('reloadDT', data: 'dtTable');
        } catch (\Exception $e) {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Gagal menyimpan hasil verifikasi: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Show read-only detail & audit trail modal.
     */
    public function showDetail($id): void
    {
        try {
            $reg = VerifikasiRepo::getById($id);

            $this->selectedDetail = [
                'id' => $reg->id,
                'registration_number' => $reg->registration_number,
                'status_label' => $reg->status->label(),
                'status_badge' => $reg->status->badgeClass(),
                'enrolled_at' => $reg->enrolled_at?->format('d M Y, H:i').' WIB',
                'verified_at' => $reg->verified_at?->format('d M Y, H:i').' WIB',
                'verifier_name' => $reg->verifier?->name ?? 'Belum Diverifikasi',
                'verification_notes' => $reg->verification_notes,
                'recommendation_letter_url' => $reg->recommendation_letter_path
                    ? asset('storage/'.$reg->recommendation_letter_path)
                    : null,
                'user_name' => $reg->user?->name ?? '-',
                'user_nip' => $reg->user?->nip ?? '-',
                'user_email' => $reg->user?->email ?? '-',
                'user_phone' => $reg->user?->phone_number ?? '-',
                'user_opd' => $reg->user?->opd_agency ?? '-',
                'user_position' => $reg->user?->position ?? '-',
                'user_rank' => $reg->user?->rank_class ?? '-',
                'course_title' => $reg->course?->title ?? '-',
                'course_code' => $reg->course?->code ?? '-',
                'course_category' => $reg->course?->category?->name ?? '-',
            ];

            $this->dispatch('openModal', id: 'modalDetailVerifikasi');
        } catch (\Exception $e) {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Tidak dapat memuat detail berkas pendaftaran.',
            ]);
        }
    }

    public function render()
    {
        $courses = Course::query()->orderBy('title')->get(['id', 'title', 'code']);

        return view('mods.admin.verifikasi.verifikasi-data', [
            'courses' => $courses,
        ]);
    }
}

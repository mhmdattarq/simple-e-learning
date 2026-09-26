<?php

namespace App\Livewire\Admin\Verifikasi;

use App\Enums\Role;
use App\Models\CourseUser;
use App\Repositories\VerifikasiRepo;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.app')]
#[Title('Pemeriksaan Berkas Calon Peserta - SIMPEL BKPSDM')]
class VerifikasiDetail extends Component
{
    public CourseUser $registration;

    /**
     * Form state for verification decision.
     *
     * @var array{status: string, verification_notes: string}
     */
    public array $verifyForm = [
        'status' => 'verified',
        'verification_notes' => '',
    ];

    public function mount(int|string $id): void
    {
        abort_if(! in_array(auth()->user()?->role, [Role::Admin, Role::Verifikator], true), 403);

        $this->registration = VerifikasiRepo::getById($id);

        $initialStatus = in_array($this->registration->status->value, ['verified', 'revision_required', 'rejected'], true)
            ? $this->registration->status->value
            : 'verified';

        $this->verifyForm = [
            'status' => $initialStatus,
            'verification_notes' => $this->registration->verification_notes ?? '',
        ];
    }

    public function submitVerification(): void
    {
        abort_if(! in_array(auth()->user()?->role, [Role::Admin, Role::Verifikator], true), 403);

        $this->validate([
            'verifyForm.status' => 'required|in:verified,revision_required,rejected',
            'verifyForm.verification_notes' => 'required_if:verifyForm.status,revision_required,rejected',
        ], [
            'verifyForm.status.required' => 'Keputusan verifikasi wajib dipilih.',
            'verifyForm.status.in' => 'Pilihan status keputusan tidak valid.',
            'verifyForm.verification_notes.required_if' => 'Catatan perbaikan / alasan penolakan wajib diisi saat memilih Perlu Perbaikan atau Ditolak.',
        ]);

        $verifierId = auth()->id();

        VerifikasiRepo::verify(
            $this->registration->id,
            $this->verifyForm['status'],
            $this->verifyForm['verification_notes'],
            $verifierId
        );

        $statusLabel = match ($this->verifyForm['status']) {
            'verified' => 'Diverifikasi / Diterima',
            'revision_required' => 'Perlu Perbaikan Berkas',
            'rejected' => 'Ditolak',
            default => $this->verifyForm['status'],
        };

        session()->flash('success_message', "Keputusan verifikasi pendaftaran {$this->registration->registration_number} berhasil disimpan: {$statusLabel}.");

        $this->redirect(route('verifikasi.data'), navigate: true);
    }

    public function render()
    {
        return view('mods.admin.verifikasi.verifikasi-detail');
    }
}

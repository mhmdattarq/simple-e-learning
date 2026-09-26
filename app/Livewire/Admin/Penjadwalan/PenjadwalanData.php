<?php

namespace App\Livewire\Admin\Penjadwalan;

use App\Repositories\PenjadwalanRepo;
use Livewire\Attributes\On;
use Livewire\Component;

class PenjadwalanData extends Component
{
    public ?int $cancelScheduleId = null;

    public string $cancelScheduleTitle = '';

    public string $cancellationReason = '';

    /**
     * Hook into universal confirmation delete modal.
     */
    public function hookModalDelete(int|string $id, ?string $title = null): void
    {
        if (! $title) {
            $schedule = PenjadwalanRepo::getById($id);
            $title = $schedule->session_title;
        }

        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus Jadwal Sesi',
            'msg' => "Apakah Anda yakin ingin menghapus jadwal sesi '{$title}'? Data yang dihapus tidak dapat dikembalikan.",
            'dispatch' => 'PenjadwalanData-delete',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    /**
     * Buka modal pembatalan sesi jadwal.
     */
    public function openCancelModal(int|string $id, ?string $title = null): void
    {
        if (! $title) {
            $schedule = PenjadwalanRepo::getById($id);
            $title = $schedule->session_title;
        }

        $this->cancelScheduleId = (int) $id;
        $this->cancelScheduleTitle = $title;
        $this->cancellationReason = '';
        $this->resetValidation();
        $this->dispatch('showModal', id: 'modalCancelSchedule');
    }

    /**
     * Simpan pembatalan sesi jadwal beserta alasannya.
     */
    public function submitCancel(): void
    {
        $this->validate([
            'cancellationReason' => 'required|string|min:5|max:500',
        ], [
            'cancellationReason.required' => 'Alasan pembatalan sesi jadwal wajib diisi.',
            'cancellationReason.min' => 'Alasan pembatalan minimal 5 karakter.',
            'cancellationReason.max' => 'Alasan pembatalan maksimal 500 karakter.',
        ]);

        try {
            PenjadwalanRepo::cancel($this->cancelScheduleId, $this->cancellationReason);

            $this->dispatch('closeModal', id: 'modalCancelSchedule');
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => "Jadwal sesi '{$this->cancelScheduleTitle}' berhasil dibatalkan.",
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');

            $this->cancelScheduleId = null;
            $this->cancelScheduleTitle = '';
            $this->cancellationReason = '';
        } catch (\Exception $e) {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Gagal membatalkan sesi: '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Delete schedule session.
     */
    #[On('PenjadwalanData-delete')]
    public function delete($data): void
    {
        $id = is_array($data) ? ($data['id'] ?? null) : $data;

        try {
            $process = PenjadwalanRepo::delete($id);

            if ($process) {
                $this->dispatch('closeModal', id: 'modalDelete');
                $this->dispatch('alert-show', data: [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'Jadwal sesi pelatihan berhasil dihapus.',
                ]);
                $this->dispatch('reloadDT', data: 'dtTable');
            } else {
                $this->dispatch('alert-show', data: [
                    'type' => 'danger',
                    'title' => 'Gagal',
                    'message' => 'Terjadi kesalahan sistem saat menghapus jadwal sesi.',
                ]);
            }
        } catch (\DomainException $de) {
            $this->dispatch('closeModal', id: 'modalDelete');
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Tidak Dapat Dihapus',
                'message' => $de->getMessage(),
            ]);
        } catch (\Exception $e) {
            $this->dispatch('closeModal', id: 'modalDelete');
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan sistem: '.$e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('mods.admin.penjadwalan.penjadwalan-data');
    }
}

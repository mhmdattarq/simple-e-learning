<?php

namespace App\Livewire\Admin\Kontak;

use App\Repositories\ContactMessageRepo;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('templates.layouts.app')]
class KontakPesanData extends Component
{
    public string $statusFilter = '';

    /**
     * Bridge to open message detail in global modal component.
     */
    public function openDetail(int $id): void
    {
        $this->dispatch('modal-detail-pesan-set', ['id' => $id]);
    }

    /**
     * Hook into global modal-delete.
     */
    public function hookModalDelete(int $id, string $identity): void
    {
        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus Pesan',
            'msg' => 'Apakah Anda yakin ingin menghapus pesan dari "'.$identity.'"? Data yang dihapus tidak dapat dipulihkan.',
            'dispatch' => 'KontakPesanData-delete',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    /**
     * Listener for modal delete execution.
     */
    #[On('KontakPesanData-delete')]
    public function delete($id = null, $data = null): void
    {
        $targetId = $id ?? (is_array($data) ? ($data['id'] ?? null) : $data);
        if (! $targetId) {
            return;
        }

        $result = ContactMessageRepo::delete((int) $targetId);

        $this->dispatch('closeModal', id: 'modalDelete');

        if ($result['status']) {
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => $result['message'],
            ]);
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => $result['message'],
            ]);
        }

        $this->dispatch('reloadDT');
    }

    public function render(): View
    {
        return view('mods.admin.kontak.pesan-data', [
            'stats' => ContactMessageRepo::getStats(),
        ]);
    }
}

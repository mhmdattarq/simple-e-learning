<?php

namespace App\Livewire\Admin\Penjadwalan;

use App\Repositories\PenjadwalanRepo;
use Livewire\Attributes\On;
use Livewire\Component;

class PenjadwalanData extends Component
{
    /**
     * Hook into universal confirmation delete modal.
     */
    public function hookModalDelete(int|string $id, string $title): void
    {
        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus Jadwal Sesi',
            'msg' => "Apakah Anda yakin ingin menghapus jadwal sesi '{$title}'? Data yang dihapus tidak dapat dikembalikan.",
            'dispatch' => 'PenjadwalanData-delete',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    /**
     * Delete schedule session.
     */
    #[On('PenjadwalanData-delete')]
    public function delete($data): void
    {
        $id = is_array($data) ? ($data['id'] ?? null) : $data;
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
    }

    public function render()
    {
        return view('mods.admin.penjadwalan.penjadwalan-data');
    }
}

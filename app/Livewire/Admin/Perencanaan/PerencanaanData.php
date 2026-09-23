<?php

namespace App\Livewire\Admin\Perencanaan;

use App\Repositories\PerencanaanRepo;
use Livewire\Attributes\On;
use Livewire\Component;

class PerencanaanData extends Component
{
    public function hookModalDelete($id, $identity)
    {
        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus Pelatihan',
            'msg' => 'Apakah Anda yakin ingin menghapus data pelatihan '.$identity.'? Data terkait akan dihapus secara permanen.',
            'dispatch' => 'PerencanaanData-delete',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    #[On('PerencanaanData-delete')]
    public function delete($data)
    {
        $id = is_array($data) ? ($data['id'] ?? null) : $data;
        $process = PerencanaanRepo::delete($id);

        if ($process) {
            $this->dispatch('closeModal', id: 'modalDelete');
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data Pelatihan Berhasil dihapus.',
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan sistem saat menghapus data pelatihan.',
            ]);
        }
    }

    public function submitToLeader($id)
    {
        $process = PerencanaanRepo::submitToLeader($id);

        if ($process) {
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Program pelatihan berhasil diajukan ke Pimpinan untuk persetujuan.',
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan sistem saat mengajukan program pelatihan.',
            ]);
        }
    }

    public function render()
    {
        return view('mods.admin.perencanaan.perencanaan-data');
    }
}

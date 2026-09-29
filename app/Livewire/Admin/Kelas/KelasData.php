<?php

namespace App\Livewire\Admin\Kelas;

use App\Repositories\KelasRepo;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Data Kelas - SIMPEL BKPSDM')]
class KelasData extends Component
{
    public function hookModalDelete($id, $identity)
    {
        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus Kelas',
            'msg' => 'Apakah Anda yakin ingin menghapus kelas '.$identity.'? Data terkait akan dihapus secara permanen.',
            'dispatch' => 'KelasData-delete',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    #[On('KelasData-delete')]
    public function delete($data)
    {
        $id = is_array($data) ? ($data['id'] ?? null) : $data;
        $process = KelasRepo::delete($id);

        if ($process) {
            $this->dispatch('closeModal', id: 'modalDelete');
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data kelas berhasil dihapus.',
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan sistem saat menghapus data kelas.',
            ]);
        }
    }

    public function archiveCourse($id)
    {
        $process = KelasRepo::archiveCourse($id);

        if ($process) {
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Kelas berhasil diarsipkan.',
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Gagal mengarsipkan kelas.',
            ]);
        }
    }

    public function render()
    {
        return view('mods.admin.kelas.kelas-data');
    }
}

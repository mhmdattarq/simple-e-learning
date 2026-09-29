<?php

namespace App\Livewire\Admin\Kategori;

use App\Repositories\KategoriRepo;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class KategoriData extends Component
{
    public function hookModalDelete(int $id, string $identity): void
    {
        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus Kategori',
            'msg' => 'Apakah Anda yakin ingin menghapus kategori "'.$identity.'"?',
            'dispatch' => 'KategoriData-delete',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    #[On('KategoriData-delete')]
    public function delete($data): void
    {
        $id = is_array($data) ? ($data['id'] ?? null) : $data;
        $result = KategoriRepo::delete((int) $id);

        $this->dispatch('closeModal', id: 'modalDelete');

        if ($result['status']) {
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => $result['message'],
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => $result['message'],
            ]);
        }
    }

    public function render()
    {
        return view('mods.admin.kategori.kategori-data');
    }
}

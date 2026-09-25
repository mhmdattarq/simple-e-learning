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
                'message' => 'Hanya pelatihan berstatus Draft yang dapat diajukan ke Pimpinan.',
            ]);
        }
    }

    public function openRegistration($id)
    {
        $process = PerencanaanRepo::openRegistration($id);

        if ($process) {
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Pendaftaran pelatihan berhasil dibuka (tayang di katalog).',
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Gagal membuka pendaftaran. Pastikan pelatihan sudah disetujui.',
            ]);
        }
    }

    public function startCourse($id)
    {
        $process = PerencanaanRepo::startCourse($id);

        if ($process) {
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Status pelatihan diperbarui menjadi Berjalan (Ongoing).',
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Hanya pelatihan berstatus Dibuka yang dapat dimulai.',
            ]);
        }
    }

    public function completeCourse($id)
    {
        $process = PerencanaanRepo::completeCourse($id);

        if ($process) {
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Pelatihan telah selesai diselenggarakan.',
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Hanya pelatihan yang sedang Berjalan yang dapat diselesaikan.',
            ]);
        }
    }

    public function archiveCourse($id)
    {
        $process = PerencanaanRepo::archiveCourse($id);

        if ($process) {
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Pelatihan berhasil diarsipkan.',
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Gagal mengarsipkan pelatihan.',
            ]);
        }
    }

    public function render()
    {
        return view('mods.admin.perencanaan.perencanaan-data');
    }
}

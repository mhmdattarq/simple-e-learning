<?php

namespace App\Livewire\Admin\Pendaftaran;

use App\Repositories\PendaftaranRepo;
use Livewire\Attributes\On;
use Livewire\Component;

class PendaftaranData extends Component
{
    public ?array $selectedDetail = null;

    public function hookModalDelete($id, $identity): void
    {
        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus Pendaftaran',
            'msg' => 'Apakah Anda yakin ingin menghapus data pendaftaran '.$identity.'? Berkas surat rekomendasi terkait akan dihapus secara permanen.',
            'dispatch' => 'PendaftaranData-delete',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    #[On('PendaftaranData-delete')]
    public function delete($data): void
    {
        $id = is_array($data) ? ($data['id'] ?? null) : $data;
        $process = PendaftaranRepo::delete($id);

        if ($process) {
            $this->dispatch('closeModal', id: 'modalDelete');
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data Pendaftaran Berhasil dihapus.',
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan sistem saat menghapus data pendaftaran.',
            ]);
        }
    }

    public function showDetail($id): void
    {
        try {
            $reg = PendaftaranRepo::getById($id);

            $this->selectedDetail = [
                'id' => $reg->id,
                'registration_number' => $reg->registration_number,
                'status_label' => $reg->status->label(),
                'status_badge' => $reg->status->badgeClass(),
                'enrolled_at' => $reg->enrolled_at?->format('d M Y, H:i').' WIB',
                'notes' => $reg->notes,
                'recommendation_letter_url' => $reg->recommendation_letter_path
                    ? asset('storage/'.$reg->recommendation_letter_path)
                    : null,
                // User Details
                'user_name' => $reg->user?->name ?? '-',
                'user_nip' => $reg->user?->nip ?? '-',
                'user_email' => $reg->user?->email ?? '-',
                'user_phone' => $reg->user?->phone_number ?? '-',
                'user_opd' => $reg->user?->opd_agency ?? '-',
                'user_position' => $reg->user?->position ?? '-',
                'user_rank' => $reg->user?->rank_class ?? '-',
                // Course Details
                'course_title' => $reg->course?->title ?? '-',
                'course_code' => $reg->course?->code ?? '-',
                'course_category' => $reg->course?->category?->name ?? '-',
                'course_method' => ucfirst((string) $reg->course?->method),
                'course_type' => $reg->course?->isPermanent() ? 'Mandiri (Buka Terus)' : 'Batch Terjadwal',
            ];

            $this->dispatch('openModal', id: 'modalDetailPendaftaran');
        } catch (\Exception $e) {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Tidak dapat memuat detail pendaftaran.',
            ]);
        }
    }

    public function render()
    {
        return view('mods.admin.pendaftaran.pendaftaran-data');
    }
}

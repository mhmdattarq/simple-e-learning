<?php

namespace App\Livewire\Admin\Kelas;

use App\Models\AuditLog;
use App\Models\Course;
use App\Models\CourseExternal;
use App\Repositories\KelasRepo;
use Livewire\Attributes\On;
use Livewire\Component;

class KelasData extends Component
{
    public string $selectedType = 'all';

    public function filterType(string $type): void
    {
        $this->selectedType = $type;
        $this->dispatch('filterTypeChanged', type: $type);
    }

    public function hookModalDelete($id, $identity)
    {
        $course = Course::withCount(['lessons', 'registrations'])->find($id);

        if (! $course) {
            return;
        }

        if ($course->registrations_count > 0) {
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Tidak Dapat Dihapus',
                'message' => 'Kelas "'.$identity.'" sudah memiliki '.$course->registrations_count.' peserta terdaftar dan tidak dapat dihapus. Anda dapat mengarsipkan kelas ini.',
            ]);

            return;
        }

        if ($course->lessons_count > 0) {
            $msg = "PERHATIAN KURIKULUM & MATERI:\nKelas \"{$identity}\" memiliki {$course->lessons_count} materi pembelajaran.\n\nSeluruh bab kurikulum dan materi pembelajaran di dalamnya akan ikut dinonaktifkan. Apakah Anda yakin ingin melanjutkan?";
            $msgBoxClass = 'bg-danger-subtle border-danger text-danger';
        } else {
            $msg = "Apakah Anda yakin ingin menghapus kelas \"{$identity}\"?\nData kelas akan dihapus dari daftar kelas.";
            $msgBoxClass = '';
        }

        $dtHook = [
            'id' => $id,
            'title' => $course->lessons_count > 0 ? 'Peringatan Hapus Kelas & Materi' : 'Konfirmasi Hapus Kelas',
            'msg' => $msg,
            'msgBoxClass' => $msgBoxClass,
            'dispatch' => 'KelasData-delete',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    #[On('KelasData-delete')]
    public function delete($data)
    {
        $id = is_array($data) ? ($data['id'] ?? null) : $data;
        $course = Course::find($id);

        if ($course && ! KelasRepo::canBeDeleted($course)) {
            $this->dispatch('closeModal', id: 'modalDelete');
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Tidak Dapat Dihapus',
                'message' => 'Kelas tidak dapat dihapus karena sudah memiliki peserta terdaftar. Silakan arsipkan kelas.',
            ]);

            return;
        }

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

    public function hookModalDeleteExternal($id, $identity)
    {
        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus Pelatihan Eksternal',
            'msg' => "Apakah Anda yakin ingin menghapus rujukan pelatihan eksternal \"{$identity}\"?\nData akan dihapus dari direktori Bangkom Eksternal.",
            'msgBoxClass' => '',
            'dispatch' => 'KelasData-deleteExternal',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    #[On('KelasData-deleteExternal')]
    public function deleteExternal($data)
    {
        $id = is_array($data) ? ($data['id'] ?? null) : $data;
        $item = CourseExternal::find($id);

        if ($item) {
            AuditLog::log(
                action: 'course_external.deleted',
                auditable: $item,
                oldValues: ['name' => $item->name, 'url' => $item->url],
                notes: 'Admin menghapus rujukan bangkom eksternal: '.$item->name
            );

            $item->delete();

            $this->dispatch('closeModal', id: 'modalDelete');
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data Pelatihan Eksternal berhasil dihapus.',
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
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
        $internalCount = Course::count();
        $externalCount = CourseExternal::count();
        $totalCount = $internalCount + $externalCount;

        $typeLabels = [
            'all' => 'Semua Tipe',
            'bulanan' => 'Bulanan',
            'bangkom' => 'Bangkom',
            'bangkom_external' => 'Bangkom Eksternal',
        ];

        return view('mods.admin.kelas.kelas-data', compact(
            'internalCount',
            'externalCount',
            'totalCount',
            'typeLabels'
        ));
    }
}

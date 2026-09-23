<?php

namespace App\Livewire\Admin\Penjadwalan;

use App\Repositories\PenjadwalanRepo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PenjadwalanData extends Component
{
    public ?int $editId = null;

    public ?string $conflictError = null;

    /**
     * Form state per PRD-LW.md.
     */
    public array $form = [
        'course_id' => '',
        'mentor_id' => '',
        'session_title' => '',
        'session_date' => '',
        'start_time' => '',
        'end_time' => '',
        'room_or_link' => '',
        'status' => 'scheduled',
    ];

    /**
     * Open create modal and reset form state.
     */
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->conflictError = null;
        $this->dispatch('openModal', id: 'modalScheduleForm');
    }

    /**
     * Open edit modal with schedule data.
     */
    public function openEditModal(int|string $id): void
    {
        $schedule = PenjadwalanRepo::getById($id);

        $this->editId = (int) $schedule->id;
        $this->conflictError = null;
        $this->form = [
            'course_id' => $schedule->course_id,
            'mentor_id' => $schedule->mentor_id,
            'session_title' => $schedule->session_title,
            'session_date' => $schedule->session_date ? $schedule->session_date->format('Y-m-d') : '',
            'start_time' => substr($schedule->start_time, 0, 5),
            'end_time' => substr($schedule->end_time, 0, 5),
            'room_or_link' => $schedule->room_or_link,
            'status' => $schedule->status ?? 'scheduled',
        ];

        $this->dispatch('openModal', id: 'modalScheduleForm');
    }

    /**
     * Save (create or update) schedule session with validation and anti-bentrok check.
     */
    public function save(): void
    {
        $this->conflictError = null;

        $validated = $this->validate([
            'form.course_id' => 'required|exists:courses,id',
            'form.mentor_id' => 'required|exists:users,id',
            'form.session_title' => 'required|string|max:255',
            'form.session_date' => 'required|date',
            'form.start_time' => 'required|date_format:H:i',
            'form.end_time' => 'required|date_format:H:i|after:form.start_time',
            'form.room_or_link' => 'required|string|max:255',
            'form.status' => 'nullable|string|in:scheduled,ongoing,completed,cancelled',
        ], [
            'form.course_id.required' => 'Pelatihan wajib dipilih.',
            'form.mentor_id.required' => 'Narasumber / Mentor wajib dipilih.',
            'form.session_title.required' => 'Judul materi / agenda sesi wajib diisi.',
            'form.session_date.required' => 'Tanggal pelaksanaan sesi wajib ditentukan.',
            'form.start_time.required' => 'Jam mulai wajib diisi.',
            'form.end_time.required' => 'Jam selesai wajib diisi.',
            'form.end_time.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'form.room_or_link.required' => 'Ruangan fisik atau tautan daring wajib diisi.',
        ]);

        // Anti-bentrok conflict validation per PRD.md
        $conflict = PenjadwalanRepo::checkConflict($this->form, $this->editId);
        if ($conflict) {
            $this->conflictError = $conflict;
            $this->addError('form.start_time', $conflict);

            return;
        }

        if ($this->editId) {
            PenjadwalanRepo::update($this->editId, $this->form);
            $message = 'Jadwal sesi pelatihan berhasil diperbarui.';
        } else {
            PenjadwalanRepo::create($this->form, Auth::id());
            $message = 'Jadwal sesi pelatihan berhasil ditambahkan.';
        }

        $this->dispatch('closeModal', id: 'modalScheduleForm');
        $this->dispatch('alert-show', data: [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => $message,
        ]);
        $this->dispatch('reloadDT', data: 'dtTable');

        $this->resetForm();
    }

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

    /**
     * Reset form state.
     */
    private function resetForm(): void
    {
        $this->editId = null;
        $this->conflictError = null;
        $this->form = [
            'course_id' => '',
            'mentor_id' => '',
            'session_title' => '',
            'session_date' => date('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'room_or_link' => '',
            'status' => 'scheduled',
        ];
        $this->resetValidation();
    }

    public function render()
    {
        return view('mods.admin.penjadwalan.penjadwalan-data', [
            'courses' => PenjadwalanRepo::getCoursesList(),
            'mentors' => PenjadwalanRepo::getMentorsList(),
        ]);
    }
}

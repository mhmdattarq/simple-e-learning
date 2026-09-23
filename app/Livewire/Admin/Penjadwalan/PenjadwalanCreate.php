<?php

namespace App\Livewire\Admin\Penjadwalan;

use App\Models\Course;
use App\Models\User;
use App\Repositories\PenjadwalanRepo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PenjadwalanCreate extends Component
{
    public array $form = [];

    public ?string $conflictError = null;

    public Collection $courses;

    public Collection $mentors;

    public function mount(): void
    {
        $this->courses = Course::orderBy('title')->get();
        $this->mentors = User::whereIn('role', ['mentor', 'admin'])->orderBy('name')->get();
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->form = [
            'course_id' => $this->courses->first()?->id ?? '',
            'mentor_id' => $this->mentors->first()?->id ?? '',
            'session_title' => '',
            'session_date' => date('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '11:30',
            'room_or_link' => '',
            'status' => 'scheduled',
        ];

        $this->conflictError = null;
    }

    public function rules(): array
    {
        return [
            'form.course_id' => 'required|exists:courses,id',
            'form.mentor_id' => 'required|exists:users,id',
            'form.session_title' => 'required|string|max:255',
            'form.session_date' => 'required|date',
            'form.start_time' => 'required|date_format:H:i',
            'form.end_time' => 'required|date_format:H:i|after:form.start_time',
            'form.room_or_link' => 'required|string|max:255',
            'form.status' => 'required|string|in:scheduled,ongoing,completed,cancelled',
        ];
    }

    public function messages(): array
    {
        return [
            'form.course_id.required' => 'Program pelatihan wajib dipilih.',
            'form.course_id.exists' => 'Program pelatihan yang dipilih tidak valid.',
            'form.mentor_id.required' => 'Narasumber / Mentor pengampu wajib dipilih.',
            'form.mentor_id.exists' => 'Narasumber / Mentor yang dipilih tidak valid.',
            'form.session_title.required' => 'Judul materi / agenda sesi wajib diisi.',
            'form.session_title.max' => 'Judul materi maksimal 255 karakter.',
            'form.session_date.required' => 'Tanggal pelaksanaan sesi wajib ditentukan.',
            'form.session_date.date' => 'Format tanggal pelaksanaan tidak valid.',
            'form.start_time.required' => 'Jam mulai sesi wajib ditentukan.',
            'form.start_time.date_format' => 'Format jam mulai harus HH:mm (contoh: 09:00).',
            'form.end_time.required' => 'Jam selesai sesi wajib ditentukan.',
            'form.end_time.date_format' => 'Format jam selesai harus HH:mm (contoh: 11:30).',
            'form.end_time.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'form.room_or_link.required' => 'Ruangan fisik atau tautan kelas daring wajib diisi.',
            'form.status.required' => 'Status agenda sesi wajib ditentukan.',
        ];
    }

    public array $validationAttributes = [
        'form.course_id' => 'Program Pelatihan',
        'form.mentor_id' => 'Narasumber / Mentor',
        'form.session_title' => 'Judul Materi Sesi',
        'form.session_date' => 'Tanggal Sesi',
        'form.start_time' => 'Jam Mulai',
        'form.end_time' => 'Jam Selesai',
        'form.room_or_link' => 'Ruangan / Link Pertemuan',
        'form.status' => 'Status Agenda',
    ];

    public function formSubmit()
    {
        $this->conflictError = null;
        $this->validate();

        // Anti-bentrok conflict validation per PRD.md
        $conflict = PenjadwalanRepo::checkConflict($this->form);
        if ($conflict) {
            $this->conflictError = $conflict;
            $this->addError('form.start_time', $conflict);

            return;
        }

        $payload = [
            'course_id' => $this->form['course_id'],
            'mentor_id' => $this->form['mentor_id'],
            'session_title' => trim($this->form['session_title']),
            'session_date' => $this->form['session_date'],
            'start_time' => $this->form['start_time'],
            'end_time' => $this->form['end_time'],
            'room_or_link' => trim($this->form['room_or_link']),
            'status' => $this->form['status'],
        ];

        $process = PenjadwalanRepo::create($payload, Auth::id());

        if ($process) {
            session()->flash('alert-show', [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Jadwal sesi pelatihan baru berhasil disimpan.',
            ]);

            return $this->redirectRoute('penjadwalan.data', navigate: true);
        }

        $this->dispatch('alert-show', data: [
            'type' => 'danger',
            'title' => 'Gagal',
            'message' => 'Terjadi kesalahan sistem saat menyimpan jadwal sesi.',
        ]);
    }

    public function render()
    {
        return view('mods.admin.penjadwalan.penjadwalan-create');
    }
}

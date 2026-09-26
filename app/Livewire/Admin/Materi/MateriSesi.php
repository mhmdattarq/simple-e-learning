<?php

namespace App\Livewire\Admin\Materi;

use App\Models\Course;
use App\Models\CourseSchedule;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Daftar Sesi Pelatihan - Ruang Materi SIMPEL')]
class MateriSesi extends Component
{
    public int $courseId;

    public ?Course $course = null;

    public bool $isAdmin = true;

    public bool $isMentor = false;

    public string $search = '';

    public function mount(int $id): void
    {
        $this->courseId = $id;
        $this->course = Course::with(['category'])->findOrFail($id);

        $user = Auth::user();
        $this->isAdmin = $user ? $user->isAdmin() : false;
        $this->isMentor = $user ? $user->isMentor() : false;

        // Otorisasi: Admin berhak atas semua, mentor berhak jika mengajar pada sesi pelatihan ini
        if (! $this->isAdmin) {
            $teachesCourse = CourseSchedule::where('course_id', $this->courseId)
                ->where('mentor_id', $user?->id)
                ->exists();

            abort_if(! $teachesCourse, 403, 'Akses ditolak: Anda tidak ditugaskan sebagai mentor pada pelatihan ini.');
        }
    }

    public function render()
    {
        $user = Auth::user();

        $query = CourseSchedule::with(['mentor', 'chapters.lessons'])
            ->where('course_id', $this->courseId)
            ->where('status', '!=', 'cancelled');

        if ($this->isMentor && ! $this->isAdmin) {
            $query->where('mentor_id', $user->id);
        }

        if (! empty(trim($this->search))) {
            $query->where('session_title', 'like', '%'.trim($this->search).'%');
        }

        $schedules = $query->orderBy('session_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return view('mods.admin.materi.materi-sesi', compact('schedules'));
    }
}

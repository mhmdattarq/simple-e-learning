<?php

namespace App\Livewire\Admin\Materi;

use App\Models\Chapter;
use App\Models\Course;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Struktur Materi Kelas - Ruang Materi SIMPEL')]
class MateriSesi extends Component
{
    public int $courseId;

    public ?Course $course = null;

    public string $search = '';

    public function mount(int $id): void
    {
        $this->courseId = $id;
        $this->course = Course::with(['category'])->findOrFail($id);
    }

    public function render()
    {
        $query = Chapter::with(['lessons'])
            ->where('course_id', $this->courseId);

        if (! empty(trim($this->search))) {
            $query->where('title', 'like', '%'.trim($this->search).'%');
        }

        $chapters = $query->orderBy('order', 'asc')->get();

        return view('mods.admin.materi.materi-sesi', compact('chapters'));
    }
}

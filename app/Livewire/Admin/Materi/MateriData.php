<?php

namespace App\Livewire\Admin\Materi;

use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;

class MateriData extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterType = '';

    public string $filterStatus = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Course::query()->with(['category', 'chapters.lessons']);

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('slug', 'like', '%'.$this->search.'%');
            });
        }

        if (! empty($this->filterType)) {
            $query->where('type', $this->filterType);
        }

        $courses = $query->latest()->paginate(9);

        return view('mods.admin.materi.materi-data', [
            'courses' => $courses,
        ]);
    }
}

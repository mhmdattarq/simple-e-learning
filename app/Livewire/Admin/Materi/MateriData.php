<?php

namespace App\Livewire\Admin\Materi;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user();

        $query = Course::query()->with(['category', 'schedules.mentor']);

        // Role restriction: Mentor only sees assigned courses, Admin has full backup access
        if ($user && $user->isMentor() && ! $user->hasAdminAccess()) {
            $query->whereHas('schedules', function ($q) use ($user) {
                $q->where('mentor_id', $user->id);
            });
        }

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%');
            });
        }

        if (! empty($this->filterType)) {
            $query->where('type', $this->filterType);
        }

        $courses = $query->latest()->paginate(9);

        // Client/Collection level filter for frozen status if selected
        if (! empty($this->filterStatus)) {
            // Let the view or collection highlight accordingly
        }

        return view('mods.admin.materi.materi-data', [
            'courses' => $courses,
            'isMentor' => $user ? $user->isMentor() : false,
            'isAdmin' => $user ? $user->hasAdminAccess() : true,
        ]);
    }
}

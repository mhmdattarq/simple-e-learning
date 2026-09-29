<?php

namespace App\Livewire\Landing;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
#[Title('Katalog Kelas Berbayar – SIMPEL BKPSDM Aceh Timur')]
class KelasBerbayar extends Component
{
    public string $search = '';

    public string $selectedCategory = 'all';

    public function filterCategory(string $categorySlug): void
    {
        $this->selectedCategory = $categorySlug;
    }

    public function resetFilter(): void
    {
        $this->search = '';
        $this->selectedCategory = 'all';
    }

    public function render()
    {
        $user = Auth::user();
        $tablesExist = Schema::hasTable('courses');

        $categories = Schema::hasTable('categories')
            ? Category::whereHas('courses', function ($q) {
                $q->where('status', 'published')->where('type', 'paid');
            })->get()
            : collect();

        $courses = collect();
        $userRegistrations = collect();

        if ($tablesExist) {
            $query = Course::with(['category'])
                ->withCount(['chapters', 'lessons', 'registrations'])
                ->where('status', 'published')
                ->where('type', 'paid');

            $trimmedSearch = trim($this->search);
            if ($trimmedSearch !== '') {
                $query->where('title', 'like', '%'.$trimmedSearch.'%');
            }

            if ($this->selectedCategory !== 'all' && $this->selectedCategory !== '') {
                $query->whereHas('category', function ($q) {
                    $q->where('slug', $this->selectedCategory);
                });
            }

            $courses = $query->latest('id')->get();

            if ($user && $courses->isNotEmpty()) {
                $userRegistrations = CourseUser::where('user_id', $user->id)
                    ->whereIn('course_id', $courses->pluck('id'))
                    ->get()
                    ->keyBy('course_id');
            }
        }

        return view('mods.landing.kelas-berbayar', compact('courses', 'categories', 'userRegistrations'));
    }
}

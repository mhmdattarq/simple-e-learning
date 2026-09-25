<?php

namespace App\Livewire\Landing;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseUser;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
#[Title('SIMPEL E-Learning - Portal Pelatihan Digital ASN & Aparatur')]
class LandingIndex extends Component
{
    public string $searchQuery = '';

    public string $selectedCategory = 'all';

    public function filterCategory(string $category): void
    {
        $this->selectedCategory = $category;
    }

    public function render()
    {
        $tablesExist = Schema::hasTable('courses');

        $courses = $tablesExist
            ? Course::with('category')
                ->where('status', 'published')
                ->latest('id')
                ->take(6)
                ->get()
            : collect();

        $totalPublishedCourses = $tablesExist
            ? Course::where('status', 'published')->count()
            : 0;

        $totalApprovedParticipants = $tablesExist && Schema::hasTable('course_user')
            ? CourseUser::where('status', 'approved')->count()
            : 0;

        $categories = $tablesExist && Schema::hasTable('categories')
            ? Category::withCount(['courses' => fn ($q) => $q->where('status', 'published')])->get()
            : collect();

        return view('mods.landing.landing-index', compact(
            'courses',
            'totalPublishedCourses',
            'totalApprovedParticipants',
            'categories',
        ));
    }
}

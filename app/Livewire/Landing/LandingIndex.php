<?php

namespace App\Livewire\Landing;

use App\Models\Course;
use App\Models\CourseUser;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
class LandingIndex extends Component
{
    public string $searchQuery = '';

    public string $selectedCategory = 'all';

    public string $selectedType = 'all';

    public function filterCategory(string $category): void
    {
        $this->selectedCategory = $category;
    }

    public function filterType(string $type): void
    {
        $this->selectedType = $type;
    }

    public function render()
    {
        $tablesExist = Schema::hasTable('courses');

        $coursesQuery = Course::with('category')
            ->where('status', 'published');

        if ($this->selectedType !== 'all') {
            $coursesQuery->where('type', $this->selectedType);
        }

        $courses = $tablesExist
            ? $coursesQuery->latest('id')
            ->take(6)
            ->get()
            : collect();

        $totalPublishedCourses = $tablesExist
            ? Course::where('status', 'published')->count()
            : 0;

        $batchCoursesCount = $tablesExist
            ? Course::where('status', 'published')->where('type', 'batch')->count()
            : 0;

        $permanentCoursesCount = $tablesExist
            ? Course::where('status', 'published')->where('type', 'permanent')->count()
            : 0;

        $paidCoursesCount = $tablesExist
            ? Course::where('status', 'published')->where('type', 'paid')->count()
            : 0;

        $totalApprovedParticipants = $tablesExist && Schema::hasTable('course_user')
            ? CourseUser::where('status', 'approved')->count()
            : 0;

        $upcomingJadwals = $tablesExist
            ? Course::with('category')
            ->where('status', 'published')
            ->where('type', 'batch')
            ->whereNotNull('start_date')
            ->orderBy('start_date', 'asc')
            ->take(3)
            ->get()
            : collect();

        return view('mods.landing.landing-index', compact(
            'courses',
            'totalPublishedCourses',
            'batchCoursesCount',
            'permanentCoursesCount',
            'paidCoursesCount',
            'totalApprovedParticipants',
            'upcomingJadwals',
        ));
    }
}

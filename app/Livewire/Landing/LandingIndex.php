<?php

namespace App\Livewire\Landing;

use App\Models\Course;
use App\Models\CourseExternal;
use App\Models\CourseUser;
use App\Repositories\CertificateRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
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
        $externalsExist = Schema::hasTable('courses_externals');

        $courses = collect();
        $externals = collect();

        if (in_array($this->selectedType, ['bangkom_external', 'paid', 'berbayar'], true)) {
            $externals = $externalsExist
                ? CourseExternal::with('category')
                    ->where('status', 'published')
                    ->latest('id')
                    ->take(3)
                    ->get()
                : collect();
        } elseif ($tablesExist) {
            $coursesQuery = Course::with('category')
                ->where('status', 'published');

            $limit = 6;
            if ($this->selectedType !== 'all') {
                if (in_array($this->selectedType, ['bulanan', 'batch'], true)) {
                    $coursesQuery->whereIn('type', ['bulanan', 'batch']);
                    $limit = 3;
                } elseif (in_array($this->selectedType, ['bangkom', 'permanent'], true)) {
                    $coursesQuery->whereIn('type', ['bangkom', 'permanent']);
                    $limit = 3;
                } else {
                    $coursesQuery->where('type', $this->selectedType);
                }
            }

            $courses = $coursesQuery->latest('id')
                ->take($limit)
                ->get();
        }

        $totalPublishedCourses = $tablesExist
            ? Course::where('status', 'published')->count()
            : 0;

        $batchCoursesCount = $tablesExist
            ? Course::where('status', 'published')->whereIn('type', ['bulanan', 'batch'])->count()
            : 0;

        $permanentCoursesCount = $tablesExist
            ? Course::where('status', 'published')->whereIn('type', ['bangkom', 'permanent'])->count()
            : 0;

        $paidCoursesCount = ($externalsExist ? CourseExternal::where('status', 'published')->count() : 0)
            + ($tablesExist ? Course::where('status', 'published')->whereIn('type', ['bangkom_external', 'paid', 'berbayar'])->count() : 0);

        $bulananCoursesCount = $batchCoursesCount;
        $bangkomCoursesCount = $permanentCoursesCount;
        $bangkomExternalCoursesCount = $paidCoursesCount;

        $totalApprovedParticipants = $tablesExist && Schema::hasTable('course_user')
            ? CourseUser::where('status', 'approved')->count()
            : 0;

        $upcomingJadwals = $tablesExist
            ? Course::with('category')
                ->where('status', 'published')
                ->whereIn('type', ['bulanan', 'batch'])
                ->whereNotNull('start_date')
                ->orderBy('start_date', 'asc')
                ->take(3)
                ->get()
            : collect();

        $user = Auth::user();
        $myCertificates = ($user && $tablesExist)
            ? CertificateRepo::getUserCertificates($user)
            : collect();

        return view('mods.landing.landing-index', compact(
            'courses',
            'externals',
            'totalPublishedCourses',
            'batchCoursesCount',
            'permanentCoursesCount',
            'paidCoursesCount',
            'bulananCoursesCount',
            'bangkomCoursesCount',
            'bangkomExternalCoursesCount',
            'totalApprovedParticipants',
            'upcomingJadwals',
            'myCertificates',
        ));
    }
}

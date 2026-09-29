<?php

namespace App\Livewire\Admin\Dashboard;

use App\Enums\CourseStatus;
use App\Enums\RegistrationStatus;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Lesson;
use App\Models\QuizAttempt;
use Livewire\Component;

class DashboardIndex extends Component
{
    public function render()
    {
        $publishedCoursesCount = Course::where('status', CourseStatus::Published)->count();
        $totalCoursesCount = Course::count();

        $totalParticipantsCount = CourseUser::count();
        $activeParticipantsCount = CourseUser::where('status', RegistrationStatus::Active)->count();

        $totalLessonsCount = Lesson::count();
        $totalChaptersCount = Chapter::count();

        $completedEvaluationsCount = QuizAttempt::whereNotNull('submitted_at')->count();
        $passedEvaluationsCount = QuizAttempt::whereNotNull('submitted_at')->where('is_passed', true)->count();
        $passRate = $completedEvaluationsCount > 0
            ? round(($passedEvaluationsCount / $completedEvaluationsCount) * 100, 1)
            : 0;

        // Data Tren 6 Bulan Terakhir
        $chartMonths = collect();
        for ($i = 5; $i >= 0; $i--) {
            $targetDate = now()->subMonths($i);
            $count = CourseUser::whereYear('created_at', $targetDate->year)
                ->whereMonth('created_at', $targetDate->month)
                ->count();

            $chartMonths->push([
                'label' => $targetDate->translatedFormat('M'),
                'full' => $targetDate->translatedFormat('F Y'),
                'count' => $count,
                'is_current' => $i === 0,
            ]);
        }

        $maxMonthlyCount = max($chartMonths->max('count') ?: 1, 1);
        $chartData = $chartMonths->map(function ($item) use ($maxMonthlyCount) {
            $percentage = $item['count'] > 0
                ? max(round(($item['count'] / $maxMonthlyCount) * 100), 12)
                : 6;

            return array_merge($item, ['height_percent' => $percentage]);
        });

        // Kelas Terbaru / Jadwal Agenda
        $recentCourses = Course::with('category')->latest()->take(3)->get();

        // 3 Indikator Antrian & Kesiapan
        $pendingRegistrationsCount = CourseUser::where('status', RegistrationStatus::Pending)->count();
        $registrationVerifyRate = $totalParticipantsCount > 0
            ? round((($totalParticipantsCount - $pendingRegistrationsCount) / $totalParticipantsCount) * 100)
            : 100;

        $coursesWithLessonsCount = Course::whereHas('lessons')->count();
        $syllabusCompletenessRate = $totalCoursesCount > 0
            ? round(($coursesWithLessonsCount / $totalCoursesCount) * 100)
            : 0;

        $avgQuizScore = round(QuizAttempt::whereNotNull('submitted_at')->avg('percentage') ?? 0, 1);

        // Pendaftaran Terbaru
        $recentRegistrations = CourseUser::with(['user', 'course'])->latest()->take(5)->get();

        // Kategori Kelas untuk Dropdown Modal Rencana Kelas
        $categories = Category::orderBy('name', 'asc')->get();

        return view('mods.admin.dashboard.dashboard-index', [
            'publishedCoursesCount' => $publishedCoursesCount,
            'totalCoursesCount' => $totalCoursesCount,
            'totalParticipantsCount' => $totalParticipantsCount,
            'activeParticipantsCount' => $activeParticipantsCount,
            'totalLessonsCount' => $totalLessonsCount,
            'totalChaptersCount' => $totalChaptersCount,
            'completedEvaluationsCount' => $completedEvaluationsCount,
            'passRate' => $passRate,
            'chartData' => $chartData,
            'recentCourses' => $recentCourses,
            'pendingRegistrationsCount' => $pendingRegistrationsCount,
            'registrationVerifyRate' => $registrationVerifyRate,
            'coursesWithLessonsCount' => $coursesWithLessonsCount,
            'syllabusCompletenessRate' => $syllabusCompletenessRate,
            'avgQuizScore' => $avgQuizScore,
            'recentRegistrations' => $recentRegistrations,
            'categories' => $categories,
        ]);
    }
}

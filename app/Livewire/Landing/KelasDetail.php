<?php

namespace App\Livewire\Landing;

use App\Models\Course;
use App\Models\CourseUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
class KelasDetail extends Component
{
    public int $courseId;

    public function mount(int|string $id): void
    {
        $this->courseId = (int) $id;
    }

    public function render()
    {
        $user = Auth::user();

        $course = Course::with([
            'category',
            'chapters' => function ($q) {
                $q->orderBy('order', 'asc')->with(['lessons' => function ($l) {
                    $l->orderBy('order', 'asc');
                }]);
            },
        ])->findOrFail($this->courseId);

        $isEnrolled = false;
        if ($user) {
            $isEnrolled = CourseUser::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->exists();
        }

        $totalChapters = $course->chapters->count();
        $totalLessons = $course->chapters->sum(fn($ch) => $ch->lessons->count());

        $backUrl = match (true) {
            $course->isPaid() => route('landing.kelas.berbayar'),
            $course->isBatch() => route('landing.kelas.batch'),
            default => route('landing.kelas.permanen'),
        };

        return view('mods.landing.kelas-detail', compact(
            'course',
            'isEnrolled',
            'totalChapters',
            'totalLessons',
            'backUrl'
        ));
    }
}

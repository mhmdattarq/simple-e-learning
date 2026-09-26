<?php

namespace App\Livewire\Landing;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
#[Title('Katalog Pelatihan – SIMPEL E-Learning BKPSDM Aceh Timur')]
class PelatihanIndex extends Component
{
    public function render()
    {
        $user = Auth::user();

        $courses = Schema::hasTable('courses')
            ? Course::with(['category', 'schedules' => function ($q) {
                $q->where('status', '!=', 'cancelled')
                    ->orderBy('session_date', 'asc')
                    ->orderBy('start_time', 'asc');
            }])
                ->where('status', 'published')
                ->latest('id')
                ->get()
            : collect();

        $userRegistrations = collect();
        $userAttendances = collect();

        if ($user && $courses->isNotEmpty()) {
            $userRegistrations = CourseUser::where('user_id', $user->id)
                ->whereIn('course_id', $courses->pluck('id'))
                ->get()
                ->keyBy('course_id');

            $scheduleIds = $courses->flatMap->schedules->pluck('id')->filter()->unique();

            if ($scheduleIds->isNotEmpty()) {
                $userAttendances = Attendance::where('user_id', $user->id)
                    ->whereIn('schedule_id', $scheduleIds)
                    ->get()
                    ->keyBy('schedule_id');
            }
        }

        return view('mods.landing.pelatihan-index', compact('courses', 'userRegistrations', 'userAttendances'));
    }
}

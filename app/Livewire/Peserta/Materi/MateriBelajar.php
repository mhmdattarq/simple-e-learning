<?php

namespace App\Livewire\Peserta\Materi;

use App\Models\Attendance;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\CourseUser;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
#[Title('Ruang Belajar Mandiri - SIMPEL BKPSDM')]
class MateriBelajar extends Component
{
    public int $courseId;

    public ?Course $course = null;

    #[Url(as: 'lesson')]
    public ?int $selectedLessonId = null;

    public ?int $selectedScheduleId = null;

    public function mount(int $id): void
    {
        $this->courseId = $id;
        $this->course = Course::with(['category'])->findOrFail($id);

        $user = Auth::user();

        // Otorisasi: Verifikasi peserta atau staff internal
        $isEnrolledVerified = CourseUser::where('user_id', $user->id)
            ->where('course_id', $this->courseId)
            ->whereIn('status', ['verified', 'active', 'completed'])
            ->exists();

        $hasAdminAccess = $user->hasAdminAccess() || $user->isMentor();

        if (! $isEnrolledVerified && ! $hasAdminAccess) {
            abort(403, 'Akses materi hanya untuk peserta terverifikasi pada pelatihan ini.');
        }

        // Auto-select initial unlocked lesson if none selected
        if (! $this->selectedLessonId) {
            $this->selectFirstAvailableLesson();
        }
    }

    public function selectFirstAvailableLesson(): void
    {
        $user = Auth::user();
        $schedules = CourseSchedule::where('course_id', $this->courseId)
            ->where('status', '!=', 'cancelled')
            ->orderBy('session_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $attendedScheduleIds = Attendance::where('user_id', $user->id)
            ->whereIn('schedule_id', $schedules->pluck('id'))
            ->pluck('schedule_id')
            ->toArray();

        // Cari lesson dari sesi yang sudah dihadiri
        foreach ($schedules as $sch) {
            if (in_array($sch->id, $attendedScheduleIds) || $user->hasAdminAccess()) {
                $lesson = Lesson::whereHas('chapter', function ($q) use ($sch) {
                    $q->where('schedule_id', $sch->id);
                })->orderBy('order', 'asc')->first();

                if ($lesson) {
                    $this->selectedLessonId = $lesson->id;
                    $this->selectedScheduleId = $sch->id;

                    return;
                }
            }
        }

        // Jika tidak ada di sesi, cek materi umum kursus
        $generalLesson = Lesson::whereHas('chapter', function ($q) {
            $q->where('course_id', $this->courseId)->whereNull('schedule_id');
        })->orderBy('order', 'asc')->first();

        if ($generalLesson) {
            $this->selectedLessonId = $generalLesson->id;
            $this->selectedScheduleId = null;
        }
    }

    public function selectLesson(int $lessonId): void
    {
        $lesson = Lesson::with('chapter')->find($lessonId);
        if (! $lesson) {
            return;
        }

        $user = Auth::user();
        $scheduleId = $lesson->chapter?->schedule_id;

        // Validasi apakah sesi materi ini sudah diabsen
        if ($scheduleId && ! $user->hasAdminAccess()) {
            $hasAttended = Attendance::where('user_id', $user->id)
                ->where('schedule_id', $scheduleId)
                ->exists();

            if (! $hasAttended) {
                $this->dispatch('show-toast', [
                    'type' => 'warning',
                    'message' => 'Sesi materi ini masih terkunci. Anda harus melakukan presensi terlebih dahulu.',
                ]);

                return;
            }
        }

        $this->selectedLessonId = $lessonId;
        $this->selectedScheduleId = $scheduleId;
    }

    public function toggleCompleteLesson(int $lessonId): void
    {
        $user = Auth::user();
        $record = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('lesson_id', $lessonId)
            ->first();

        if ($record && $record->is_completed) {
            DB::table('lesson_user')
                ->where('user_id', $user->id)
                ->where('lesson_id', $lessonId)
                ->update([
                    'is_completed' => false,
                    'completed_at' => null,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('lesson_user')->updateOrInsert(
                ['user_id' => $user->id, 'lesson_id' => $lessonId],
                [
                    'is_completed' => true,
                    'completed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function render()
    {
        $user = Auth::user();

        $schedules = CourseSchedule::with(['mentor', 'chapters.lessons'])
            ->where('course_id', $this->courseId)
            ->where('status', '!=', 'cancelled')
            ->orderBy('session_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $generalChapters = Chapter::with('lessons')
            ->where('course_id', $this->courseId)
            ->whereNull('schedule_id')
            ->orderBy('order', 'asc')
            ->get();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereIn('schedule_id', $schedules->pluck('id'))
            ->get()
            ->keyBy('schedule_id');

        $completedLessonIds = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        $currentLesson = null;
        $currentChapter = null;
        $currentSchedule = null;

        if ($this->selectedLessonId) {
            $currentLesson = Lesson::with('chapter.schedule')->find($this->selectedLessonId);
            if ($currentLesson) {
                $currentChapter = $currentLesson->chapter;
                $currentSchedule = $currentChapter?->schedule;
            }
        }

        return view('mods.peserta.materi.materi-belajar', compact(
            'schedules',
            'generalChapters',
            'attendances',
            'completedLessonIds',
            'currentLesson',
            'currentChapter',
            'currentSchedule'
        ));
    }
}

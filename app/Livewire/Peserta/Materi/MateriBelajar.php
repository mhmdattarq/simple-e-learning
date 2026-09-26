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

    public bool $showCompleteModal = false;

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

        $completedLessonIds = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        // Cari materi pertama yang belum selesai, atau materi pertama yang bisa diakses
        $firstAccessibleLessonId = null;
        $firstAccessibleScheduleId = null;

        foreach ($schedules as $sch) {
            if (in_array($sch->id, $attendedScheduleIds) || $user->hasAdminAccess()) {
                $lessons = Lesson::whereHas('chapter', function ($q) use ($sch) {
                    $q->where('schedule_id', $sch->id);
                })
                    ->join('chapters', 'chapters.id', '=', 'lessons.chapter_id')
                    ->orderBy('chapters.order', 'asc')
                    ->orderBy('lessons.order', 'asc')
                    ->select('lessons.*')
                    ->get();

                foreach ($lessons as $lesson) {
                    if (! $firstAccessibleLessonId) {
                        $firstAccessibleLessonId = $lesson->id;
                        $firstAccessibleScheduleId = $sch->id;
                    }
                    // Jika belum selesai, inilah materi aktif peserta saat ini!
                    if (! in_array($lesson->id, $completedLessonIds)) {
                        $this->selectedLessonId = $lesson->id;
                        $this->selectedScheduleId = $sch->id;

                        return;
                    }
                }
            }
        }

        // Jika tidak ada di sesi, cek materi umum kursus
        $generalLessons = Lesson::whereHas('chapter', function ($q) {
            $q->where('course_id', $this->courseId)->whereNull('schedule_id');
        })
            ->join('chapters', 'chapters.id', '=', 'lessons.chapter_id')
            ->orderBy('chapters.order', 'asc')
            ->orderBy('lessons.order', 'asc')
            ->select('lessons.*')
            ->get();

        foreach ($generalLessons as $lesson) {
            if (! $firstAccessibleLessonId) {
                $firstAccessibleLessonId = $lesson->id;
                $firstAccessibleScheduleId = null;
            }
            if (! in_array($lesson->id, $completedLessonIds)) {
                $this->selectedLessonId = $lesson->id;
                $this->selectedScheduleId = null;

                return;
            }
        }

        if ($firstAccessibleLessonId) {
            $this->selectedLessonId = $firstAccessibleLessonId;
            $this->selectedScheduleId = $firstAccessibleScheduleId;
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

        // Validasi sekuensial: Peserta tidak boleh lompat ke materi yang masih terkunci
        if (! $user->hasAdminAccess() && ! $this->isLessonAccessible($lessonId)) {
            $this->dispatch('show-toast', [
                'type' => 'warning',
                'message' => 'Materi ini masih terkunci. Selesaikan materi pembelajaran sebelumnya terlebih dahulu.',
            ]);

            return;
        }

        $this->selectedLessonId = $lessonId;
        $this->selectedScheduleId = $scheduleId;
        $this->showCompleteModal = false;
    }

    /**
     * Memeriksa apakah materi tertentu dapat diakses oleh user.
     */
    public function isLessonAccessible(int $lessonId): bool
    {
        $orderedLessons = $this->getAllLinearLessons();
        $user = Auth::user();

        $completedLessonIds = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        foreach ($orderedLessons as $item) {
            if ($item->id === $lessonId) {
                return true;
            }
            // Jika ada materi sebelumnya yang belum selesai, maka materi setelahnya terkunci
            if (! in_array($item->id, $completedLessonIds)) {
                return false;
            }
        }

        return false;
    }

    /**
     * Mengambil seluruh materi dalam urutan linier sesuai hierarki sesi & bab.
     */
    protected function getAllLinearLessons(): array
    {
        $schedules = CourseSchedule::where('course_id', $this->courseId)
            ->where('status', '!=', 'cancelled')
            ->orderBy('session_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        $allLessons = [];

        foreach ($schedules as $sch) {
            $chapters = Chapter::with(['lessons' => function ($q) {
                $q->orderBy('order', 'asc')->orderBy('id', 'asc');
            }])
                ->where('schedule_id', $sch->id)
                ->orderBy('order', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            foreach ($chapters as $chapter) {
                foreach ($chapter->lessons as $lesson) {
                    $allLessons[] = $lesson;
                }
            }
        }

        $generalChapters = Chapter::with(['lessons' => function ($q) {
            $q->orderBy('order', 'asc')->orderBy('id', 'asc');
        }])
            ->where('course_id', $this->courseId)
            ->whereNull('schedule_id')
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($generalChapters as $chapter) {
            foreach ($chapter->lessons as $lesson) {
                $allLessons[] = $lesson;
            }
        }

        return $allLessons;
    }

    /**
     * Berpindah ke materi sebelumnya.
     */
    public function previousLesson(): void
    {
        $allLessons = $this->getAllLinearLessons();
        $currentIndex = null;

        foreach ($allLessons as $index => $item) {
            if ($item->id === $this->selectedLessonId) {
                $currentIndex = $index;
                break;
            }
        }

        if ($currentIndex !== null && $currentIndex > 0) {
            $prevLesson = $allLessons[$currentIndex - 1];
            $this->selectLesson($prevLesson->id);
        }
    }

    /**
     * Berpindah ke materi selanjutnya dalam bab yang sama.
     */
    public function nextLesson(): void
    {
        if (! $this->selectedLessonId) {
            return;
        }

        $currentLesson = Lesson::with('chapter.lessons')->find($this->selectedLessonId);
        if (! $currentLesson || ! $currentLesson->chapter) {
            return;
        }

        $chapterLessons = $currentLesson->chapter->lessons()->orderBy('order', 'asc')->orderBy('id', 'asc')->get();
        $currentIndex = $chapterLessons->search(fn ($l) => $l->id === $currentLesson->id);

        if ($currentIndex !== false && $currentIndex < $chapterLessons->count() - 1) {
            // Tandai materi saat ini selesai terlebih dahulu agar sekuensial
            $this->markLessonComplete($currentLesson->id);

            $nextLesson = $chapterLessons[$currentIndex + 1];
            $this->selectLesson($nextLesson->id);
        }
    }

    /**
     * Membuka modal konfirmasi penyelesaian bab.
     */
    public function promptCompleteChapter(): void
    {
        $this->showCompleteModal = true;
    }

    /**
     * Menutup modal konfirmasi.
     */
    public function cancelCompleteChapter(): void
    {
        $this->showCompleteModal = false;
    }

    /**
     * Menyelesaikan seluruh materi dalam bab saat ini dan otomatis lanjut ke bab berikutnya.
     */
    public function confirmCompleteChapter(): void
    {
        $user = Auth::user();

        if (! $this->selectedLessonId) {
            $this->showCompleteModal = false;

            return;
        }

        $currentLesson = Lesson::with('chapter')->find($this->selectedLessonId);
        if (! $currentLesson || ! $currentLesson->chapter) {
            $this->showCompleteModal = false;

            return;
        }

        $chapter = $currentLesson->chapter;

        // Tandai semua materi dalam bab ini sebagai completed
        $lessonIds = $chapter->lessons()->pluck('id')->toArray();
        foreach ($lessonIds as $lId) {
            $this->markLessonComplete($lId);
        }

        $this->showCompleteModal = false;

        // Cari bab berikutnya dalam urutan linier
        $allLessons = $this->getAllLinearLessons();
        $nextLesson = null;
        $foundCurrentChapter = false;

        foreach ($allLessons as $lesson) {
            if ($lesson->chapter_id === $chapter->id) {
                $foundCurrentChapter = true;

                continue;
            }
            if ($foundCurrentChapter && $lesson->chapter_id !== $chapter->id) {
                $nextLesson = $lesson;
                break;
            }
        }

        if ($nextLesson) {
            $this->selectLesson($nextLesson->id);
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Selamat! Bab telah selesai, Anda melanjutkan ke materi berikutnya.',
            ]);
        } else {
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Selamat! Anda telah menyelesaikan seluruh bab materi ini.',
            ]);
        }
    }

    protected function markLessonComplete(int $lessonId): void
    {
        $user = Auth::user();
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
            $this->markLessonComplete($lessonId);
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
        $isFirstLesson = true;
        $isLastInChapter = false;
        $hasNextChapter = false;

        if ($this->selectedLessonId) {
            $currentLesson = Lesson::with('chapter.schedule')->find($this->selectedLessonId);
            if ($currentLesson) {
                $currentChapter = $currentLesson->chapter;
                $currentSchedule = $currentChapter?->schedule;

                $allLinear = $this->getAllLinearLessons();
                $linearIndex = null;
                foreach ($allLinear as $idx => $l) {
                    if ($l->id === $currentLesson->id) {
                        $linearIndex = $idx;
                        break;
                    }
                }

                $isFirstLesson = ($linearIndex === null || $linearIndex === 0);

                if ($currentChapter) {
                    $chapterLessons = $currentChapter->lessons()->orderBy('order', 'asc')->orderBy('id', 'asc')->get();
                    $lastLessonInChap = $chapterLessons->last();
                    $isLastInChapter = ($lastLessonInChap && $lastLessonInChap->id === $currentLesson->id);

                    // Periksa apakah ada bab selanjutnya
                    if ($linearIndex !== null) {
                        for ($i = $linearIndex + 1; $i < count($allLinear); $i++) {
                            if ($allLinear[$i]->chapter_id !== $currentChapter->id) {
                                $hasNextChapter = true;
                                break;
                            }
                        }
                    }
                }
            }
        }

        return view('mods.peserta.materi.materi-belajar', compact(
            'schedules',
            'generalChapters',
            'attendances',
            'completedLessonIds',
            'currentLesson',
            'currentChapter',
            'currentSchedule',
            'isFirstLesson',
            'isLastInChapter',
            'hasNextChapter'
        ));
    }
}

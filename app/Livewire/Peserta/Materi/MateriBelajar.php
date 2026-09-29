<?php

namespace App\Livewire\Peserta\Materi;

use App\Enums\CourseStatus;
use App\Enums\RegistrationStatus;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
class MateriBelajar extends Component
{
    public int $courseId;

    public ?Course $course = null;

    #[Url(as: 'lesson')]
    public ?int $selectedLessonId = null;

    public function mount(int $id): void
    {
        $this->courseId = $id;
        $this->course = Course::with(['category'])->findOrFail($id);

        $user = Auth::user();

        if ($this->course->status !== CourseStatus::Published && ! $user->hasAdminAccess()) {
            abort(404, 'Kelas tidak ditemukan atau belum dipublikasikan.');
        }

        // Auto-enroll peserta yang login agar riwayat & progres belajar tercatat
        $enrollment = CourseUser::firstOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $this->courseId,
            ],
            [
                'status' => 'active',
                'enrolled_at' => now(),
            ]
        );

        if ($enrollment->status === RegistrationStatus::Pending) {
            $enrollment->update([
                'status' => 'active',
                'enrolled_at' => $enrollment->enrolled_at ?? now(),
            ]);
        }

        // Auto-select initial lesson if none selected
        if (! $this->selectedLessonId) {
            $this->selectFirstAvailableLesson();
        }
    }

    public bool $showCompleteModal = false;

    public function selectFirstAvailableLesson(): void
    {
        $user = Auth::user();

        $completedLessonIds = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        $chapters = Chapter::with(['lessons' => fn($q) => $q->orderBy('order', 'asc')])
            ->where('course_id', $this->courseId)
            ->orderBy('order', 'asc')
            ->get();

        foreach ($chapters as $chapter) {
            foreach ($chapter->lessons as $lesson) {
                if (! in_array($lesson->id, $completedLessonIds)) {
                    $this->selectedLessonId = $lesson->id;

                    return;
                }
            }
        }

        // All completed: go to first lesson
        $first = $chapters->first()?->lessons->first();
        if ($first) {
            $this->selectedLessonId = $first->id;
        }
    }

    public function selectLesson(int $lessonId): void
    {
        $lesson = Lesson::with('chapter')->find($lessonId);
        if (! $lesson) {
            return;
        }

        $user = Auth::user();

        // Sequential lock: peserta cannot skip ahead
        if (! $user->hasAdminAccess() && ! $this->isLessonAccessible($lessonId)) {
            $this->dispatch('show-toast', [
                'type' => 'warning',
                'message' => 'Materi ini masih terkunci. Selesaikan materi pembelajaran sebelumnya terlebih dahulu.',
            ]);

            return;
        }

        $this->selectedLessonId = $lessonId;
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
            if (! in_array($item->id, $completedLessonIds)) {
                return false;
            }
        }

        return false;
    }

    /**
     * Mengambil seluruh materi dalam urutan linier sesuai hierarki bab.
     */
    protected function getAllLinearLessons(): array
    {
        $chapters = Chapter::with(['lessons' => fn($q) => $q->orderBy('order', 'asc')->orderBy('id', 'asc')])
            ->where('course_id', $this->courseId)
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $allLessons = [];
        foreach ($chapters as $chapter) {
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
        $currentIndex = $chapterLessons->search(fn($l) => $l->id === $currentLesson->id);

        if ($currentIndex !== false && $currentIndex < $chapterLessons->count() - 1) {
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

        // Cari bab berikutnya
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
                'message' => 'Selamat! Anda telah menyelesaikan seluruh materi kelas ini.',
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

        $chapters = Chapter::with(['lessons' => fn($q) => $q->orderBy('order', 'asc')])
            ->where('course_id', $this->courseId)
            ->orderBy('order', 'asc')
            ->get();

        $completedLessonIds = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        $currentLesson = null;
        $currentChapter = null;
        $isFirstLesson = true;
        $isLastInChapter = false;
        $hasNextChapter = false;

        if ($this->selectedLessonId) {
            $currentLesson = Lesson::with('chapter')->find($this->selectedLessonId);
            if ($currentLesson) {
                $currentChapter = $currentLesson->chapter;

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
            'chapters',
            'completedLessonIds',
            'currentLesson',
            'currentChapter',
            'isFirstLesson',
            'isLastInChapter',
            'hasNextChapter'
        ));
    }
}

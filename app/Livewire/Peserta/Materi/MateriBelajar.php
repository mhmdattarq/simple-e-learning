<?php

namespace App\Livewire\Peserta\Materi;

use App\Enums\CourseStatus;
use App\Enums\RegistrationStatus;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
class MateriBelajar extends Component
{
    public int $courseId;

    public ?Course $course = null;

    #[Url(as: 'lesson')]
    public ?int $selectedLessonId = null;

    public function mount(int|string|Course|null $course = null, int|string|null $id = null): void
    {
        $resolved = $course ?? $id;

        if ($resolved instanceof Course) {
            $this->course = $resolved;
            $this->courseId = $resolved->id;
        } else {
            $this->course = Course::with(['category'])
                ->where('slug', $resolved)
                ->orWhere(fn ($q) => is_numeric($resolved) ? $q->where('id', (int) $resolved) : null)
                ->firstOrFail();
            $this->courseId = $this->course->id;
        }

        /** @var User $user */
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

        $chapters = Chapter::with(['lessons' => fn ($q) => $q->orderBy('order', 'asc')])
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

        /** @var User $user */
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
        $chapters = Chapter::with(['lessons' => fn ($q) => $q->orderBy('order', 'asc')->orderBy('id', 'asc')])
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
        $currentIndex = $chapterLessons->search(fn ($l) => $l->id === $currentLesson->id);

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
     * Menyelesaikan seluruh materi dalam bab saat ini dan otomatis lanjut ke bab berikutnya atau kuis.
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

        // Cek apakah bab ini memiliki kuis evaluasi dan belum dikerjakan
        if ($chapter->quiz && ! $chapter->quiz->isAttemptedByUser($user->id)) {
            $this->redirect(
                route('peserta.evaluasi.kerjakan', ['course' => $this->course ?? $this->courseId, 'quiz' => $chapter->quiz]),
                navigate: true
            );

            return;
        }

        // Cari materi pertama pada bab berikutnya
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
                'message' => 'Selamat! Bab telah selesai, Anda melanjutkan ke materi bab berikutnya.',
            ]);
        } else {
            // Seluruh bab telah selesai (berada di akhir kelas pembelajaran).
            // Cek apakah ada Evaluasi Akhir (Final Quiz) dan belum dikerjakan.
            $finalQuiz = Quiz::where('course_id', $this->courseId)->where('type', 'final')->first();

            if ($finalQuiz && ! $finalQuiz->isAttemptedByUser($user->id)) {
                $this->redirect(
                    route('peserta.evaluasi.kerjakan', ['course' => $this->course ?? $this->courseId, 'quiz' => $finalQuiz]),
                    navigate: true
                );

                return;
            }

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Selamat! Anda telah menyelesaikan seluruh rangkaian materi kelas ini.',
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

        $chapters = Chapter::with([
            'lessons' => fn ($q) => $q->orderBy('order', 'asc')->orderBy('id', 'asc'),
            'quiz.attempts' => fn ($q) => $q->where('user_id', $user->id),
        ])
            ->where('course_id', $this->courseId)
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $completedLessonIds = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        $allLinear = [];
        foreach ($chapters as $ch) {
            foreach ($ch->lessons as $l) {
                $allLinear[] = $l;
            }
        }

        $currentLesson = null;
        $currentChapter = null;
        $isFirstLesson = true;
        $isLastInChapter = false;
        $hasNextChapter = false;

        if ($this->selectedLessonId) {
            $linearIndex = null;
            foreach ($allLinear as $idx => $l) {
                if ($l->id === $this->selectedLessonId) {
                    $currentLesson = $l;
                    $linearIndex = $idx;
                    break;
                }
            }

            if ($currentLesson) {
                $currentChapter = $chapters->firstWhere('id', $currentLesson->chapter_id);
                $isFirstLesson = ($linearIndex === 0);

                if ($currentChapter) {
                    $chapterLessons = $currentChapter->lessons;
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

        $hasChapterQuiz = false;
        if ($currentChapter && $currentChapter->quiz) {
            $hasChapterQuiz = ! $currentChapter->quiz->isAttemptedByUser($user->id);
        }

        $finalQuiz = Quiz::with(['attempts' => fn ($q) => $q->where('user_id', $user->id)])
            ->where('course_id', $this->courseId)
            ->where('type', 'final')
            ->first();
        $hasFinalQuiz = $finalQuiz && ! $finalQuiz->isAttemptedByUser($user->id);

        return view('mods.peserta.materi.materi-belajar', compact(
            'chapters',
            'completedLessonIds',
            'currentLesson',
            'currentChapter',
            'isFirstLesson',
            'isLastInChapter',
            'hasNextChapter',
            'hasChapterQuiz',
            'hasFinalQuiz'
        ));
    }
}

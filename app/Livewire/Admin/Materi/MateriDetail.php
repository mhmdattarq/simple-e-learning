<?php

namespace App\Livewire\Admin\Materi;

use App\Models\Course;
use App\Repositories\MateriRepo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MateriDetail extends Component
{
    public int $courseId;

    public ?Course $course = null;

    public bool $isFrozen = false;

    public bool $isAdmin = true;

    public bool $isMentor = false;

    // Interactive curriculum state for UI
    public array $chapters = [];

    // Chapter Modal State
    public bool $showChapterModal = false;

    public array $chapterForm = [
        'id' => null,
        'title' => '',
        'order' => 1,
    ];

    // Preview Modal State
    public bool $showPreviewModal = false;

    public ?array $previewLesson = null;

    public function mount(int $id): void
    {
        $this->courseId = $id;
        $this->course = Course::with(['category', 'schedules.mentor'])->findOrFail($id);

        $user = Auth::user();
        $this->isAdmin = $user ? $user->hasAdminAccess() : true;
        $this->isMentor = $user ? $user->isMentor() : false;

        // Check Batch Freeze rule ala Dicoding
        $this->isFrozen = $this->course->isCurriculumFrozen();

        // Load kurikulum nyata dari database
        $this->loadCurriculum();
    }

    /**
     * Memuat struktur bab dan materi dari database melalui MateriRepo.
     */
    public function loadCurriculum(): void
    {
        $curriculum = MateriRepo::getCurriculumByCourse($this->courseId);

        $this->chapters = $curriculum->map(function ($chapter) {
            return [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'order' => $chapter->order,
                'lessons' => $chapter->lessons->map(function ($lesson) {
                    return [
                        'id' => $lesson->id,
                        'chapter_id' => $lesson->chapter_id,
                        'title' => $lesson->title,
                        'order' => $lesson->order,
                        'content_type' => $lesson->content_type,
                        'video_url' => $lesson->video_url,
                        'body_text' => $lesson->body_text,
                        'attachment_path' => $lesson->attachment_path,
                        'version' => $lesson->version,
                        'version_notes' => $lesson->version_notes,
                        'updated_at' => $lesson->updated_at ? $lesson->updated_at->format('d M Y, H:i') : '-',
                    ];
                })->toArray(),
            ];
        })->toArray();
    }

    // --- CHAPTER CRUD ACTIONS ---
    public function openCreateChapter(): void
    {
        if ($this->isFrozen) {
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Kurikulum Terkunci',
                'message' => 'Struktur bab tidak dapat ditambahkan karena pelatihan tipe Batch sedang aktif berjalan.',
            ]);

            return;
        }

        $this->chapterForm = [
            'id' => null,
            'title' => '',
            'order' => count($this->chapters) + 1,
        ];
        $this->showChapterModal = true;
    }

    public function openEditChapter(int $chapterId): void
    {
        if ($this->isFrozen) {
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Kurikulum Terkunci',
                'message' => 'Struktur bab tidak dapat diedit karena pelatihan tipe Batch sedang aktif berjalan.',
            ]);

            return;
        }

        foreach ($this->chapters as $chap) {
            if ($chap['id'] === $chapterId) {
                $this->chapterForm = [
                    'id' => $chap['id'],
                    'title' => $chap['title'],
                    'order' => $chap['order'],
                ];
                $this->showChapterModal = true;
                break;
            }
        }
    }

    public function saveChapter(): void
    {
        if ($this->isFrozen) {
            return;
        }

        $this->validate([
            'chapterForm.title' => 'required|string|max:255',
            'chapterForm.order' => 'required|integer|min:1',
        ], [
            'chapterForm.title.required' => 'Judul bab silabus kurikulum wajib diisi.',
            'chapterForm.order.required' => 'Nomor urut bab wajib ditentukan.',
        ]);

        if ($this->chapterForm['id']) {
            // Update via Repository
            $success = MateriRepo::updateChapter($this->chapterForm['id'], [
                'title' => $this->chapterForm['title'],
                'order' => $this->chapterForm['order'],
            ]);
            $msg = 'Bab kurikulum berhasil diperbarui.';
        } else {
            // Create via Repository
            $created = MateriRepo::createChapter([
                'course_id' => $this->courseId,
                'title' => $this->chapterForm['title'],
                'order' => $this->chapterForm['order'],
            ]);
            $success = (bool) $created;
            $msg = 'Bab baru berhasil ditambahkan ke kurikulum.';
        }

        if ($success) {
            $this->loadCurriculum();
            $this->showChapterModal = false;
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => $msg,
            ]);
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan sistem saat menyimpan bab kurikulum.',
            ]);
        }
    }

    public function deleteChapter(int $chapterId): void
    {
        if ($this->isFrozen) {
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Kurikulum Terkunci',
                'message' => 'Struktur bab tidak dapat dihapus karena pelatihan tipe Batch sedang aktif berjalan.',
            ]);

            return;
        }

        $success = MateriRepo::deleteChapter($chapterId);

        if ($success) {
            $this->loadCurriculum();
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Bab kurikulum dan seluruh materinya berhasil dihapus.',
            ]);
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan sistem saat menghapus bab kurikulum.',
            ]);
        }
    }

    // --- LESSON CRUD ACTIONS ---
    public function deleteLesson(int $chapterId, int $lessonId): void
    {
        if ($this->isFrozen) {
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Kurikulum Terkunci',
                'message' => 'Materi tidak dapat dihapus karena pelatihan tipe Batch sedang aktif berjalan.',
            ]);

            return;
        }

        $success = MateriRepo::deleteLesson($lessonId);

        if ($success) {
            $this->loadCurriculum();
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Materi pembelajaran berhasil dihapus dari kurikulum.',
            ]);
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan sistem saat menghapus materi pembelajaran.',
            ]);
        }
    }

    public function previewLesson(int $lessonId): void
    {
        $lesson = MateriRepo::getLessonById($lessonId);

        if ($lesson) {
            $this->previewLesson = [
                'id' => $lesson->id,
                'chapter_id' => $lesson->chapter_id,
                'title' => $lesson->title,
                'order' => $lesson->order,
                'content_type' => $lesson->content_type,
                'video_url' => $lesson->video_url,
                'body_text' => $lesson->body_text,
                'attachment_path' => $lesson->attachment_path,
                'version' => $lesson->version,
                'version_notes' => $lesson->version_notes,
                'updated_at' => $lesson->updated_at ? $lesson->updated_at->format('d M Y, H:i') : '-',
            ];
            $this->showPreviewModal = true;
        }
    }

    public function closePreview(): void
    {
        $this->showPreviewModal = false;
        $this->previewLesson = null;
    }

    public function render()
    {
        return view('mods.admin.materi.materi-detail');
    }
}

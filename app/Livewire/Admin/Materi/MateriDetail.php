<?php

namespace App\Livewire\Admin\Materi;

use App\Enums\CourseStatus;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Repositories\MateriRepo;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class MateriDetail extends Component
{
    public int $courseId;

    public ?Course $course = null;

    public bool $isFrozen = false;

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

    // Inline Editor State (Swap view within component ala DataTables serverside)
    #[Url(as: 'view')]
    public string $viewMode = 'silabus';

    #[Url(as: 'chapter_id')]
    public ?int $editorChapterId = null;

    #[Url(as: 'lesson_id')]
    public ?int $editorLessonId = null;

    public ?Chapter $activeEditorChapter = null;

    public array $lessonForm = [
        'id' => null,
        'chapter_id' => null,
        'title' => '',
        'order' => 1,
        'content_type' => 'article',
        'body_text' => '',
        'version' => 'Versi 1.0',
    ];

    public function mount(int $id): void
    {
        $this->courseId = $id;

        $this->course = Course::with(['category'])->findOrFail($id);

        // Check Batch Freeze rule
        $this->isFrozen = $this->course->isCurriculumFrozen();

        // Load kurikulum nyata dari database
        $this->loadCurriculum();

        // Handle direct view=editor parameter on mount
        if ($this->viewMode === 'editor') {
            if ($this->editorLessonId) {
                $this->openEditLesson($this->editorLessonId);
            } elseif ($this->editorChapterId) {
                $this->openCreateLesson($this->editorChapterId);
            } else {
                $firstChapter = Chapter::where('course_id', $this->courseId)
                    ->orderBy('order', 'asc')
                    ->first();
                if ($firstChapter) {
                    $this->openCreateLesson($firstChapter->id);
                } else {
                    $this->viewMode = 'silabus';
                }
            }
        }
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

        $nextOrder = count($this->chapters) + 1;
        $this->chapterForm = [
            'id' => null,
            'title' => '',
            'order' => $nextOrder,
        ];
        $this->showChapterModal = true;

        $this->dispatch('modal-chapter-set', [
            'id' => null,
            'course_id' => $this->courseId,
            'title' => '',
            'order' => $nextOrder,
        ]);
        $this->dispatch('showModal', id: 'modalChapter');
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

                $this->dispatch('modal-chapter-set', [
                    'id' => $chap['id'],
                    'course_id' => $this->courseId,
                    'title' => $chap['title'],
                    'order' => $chap['order'],
                ]);
                $this->dispatch('showModal', id: 'modalChapter');
                break;
            }
        }
    }

    #[On('MateriDetail-saveChapter')]
    public function saveChapter(?array $form = null): void
    {
        if ($this->isFrozen) {
            return;
        }

        if ($form) {
            $this->chapterForm = [
                'id' => $form['id'] ?? null,
                'title' => $form['title'] ?? '',
                'order' => $form['order'] ?? 1,
            ];
        }

        $this->validate([
            'chapterForm.title' => 'required|string|max:255',
            'chapterForm.order' => 'required|integer|min:1',
        ], [
            'chapterForm.title.required' => 'Judul bab silabus kurikulum wajib diisi.',
            'chapterForm.order.required' => 'Nomor urut bab wajib ditentukan.',
        ]);

        if (! empty($this->chapterForm['id'])) {
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
            $this->dispatch('closeModal', id: 'modalChapter');
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

    public function hookModalDeleteChapter(int $id, string $title): void
    {
        if ($this->isFrozen) {
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Kurikulum Terkunci',
                'message' => 'Struktur bab tidak dapat dihapus karena pelatihan tipe Batch sedang aktif berjalan.',
            ]);

            return;
        }

        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus Bab',
            'msg' => 'Apakah Anda yakin ingin menghapus bab "'.$title.'" beserta seluruh materinya? Tindakan ini tidak dapat dibatalkan.',
            'dispatch' => 'MateriDetail-deleteChapter',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
        $this->dispatch('showModal', id: 'modalDelete');
    }

    #[On('MateriDetail-deleteChapter')]
    public function deleteChapter($data): void
    {
        if ($this->isFrozen) {
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Kurikulum Terkunci',
                'message' => 'Struktur bab tidak dapat dihapus karena pelatihan tipe Batch sedang aktif berjalan.',
            ]);

            return;
        }

        $chapterId = is_array($data) ? ($data['id'] ?? null) : $data;
        if (! $chapterId) {
            return;
        }

        $success = MateriRepo::deleteChapter((int) $chapterId);

        if ($success) {
            $this->loadCurriculum();
            $this->dispatch('closeModal', id: 'modalDelete');
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
    public function hookModalDeleteLesson(int $id, string $title): void
    {
        if ($this->isFrozen) {
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Kurikulum Terkunci',
                'message' => 'Materi tidak dapat dihapus karena pelatihan tipe Batch sedang aktif berjalan.',
            ]);

            return;
        }

        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus Materi',
            'msg' => 'Apakah Anda yakin ingin menghapus materi "'.$title.'"? Tindakan ini tidak dapat dibatalkan.',
            'dispatch' => 'MateriDetail-deleteLesson',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
        $this->dispatch('showModal', id: 'modalDelete');
    }

    #[On('MateriDetail-deleteLesson')]
    public function deleteLesson($chapterIdOrData, ?int $lessonId = null): void
    {
        if ($this->isFrozen) {
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Kurikulum Terkunci',
                'message' => 'Materi tidak dapat dihapus karena pelatihan tipe Batch sedang aktif berjalan.',
            ]);

            return;
        }

        if (is_array($chapterIdOrData)) {
            $id = $chapterIdOrData['id'] ?? null;
        } else {
            $id = $lessonId ?? $chapterIdOrData;
        }

        if (! $id) {
            return;
        }

        $success = MateriRepo::deleteLesson((int) $id);

        if ($success) {
            $this->loadCurriculum();
            $this->dispatch('closeModal', id: 'modalDelete');
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

    // --- INLINE LESSON EDITOR ACTIONS (DATA-TABLES SERVER-SIDE STYLE SWAP) ---
    public function updated($propertyName): void
    {
        if (str_starts_with($propertyName, 'lessonForm.')) {
            $this->resetErrorBag($propertyName);
            $this->validateOnly($propertyName, [
                'lessonForm.title' => 'required|string|min:3|max:255',
            ], [
                'lessonForm.title.required' => 'Judul materi pembelajaran wajib diisi.',
                'lessonForm.title.min' => 'Judul materi pembelajaran minimal 3 karakter.',
                'lessonForm.title.max' => 'Judul materi pembelajaran maksimal 255 karakter.',
            ]);
        }
    }

    public function openCreateLesson(int $chapterId): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        if ($this->isFrozen) {
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Kurikulum Terkunci',
                'message' => 'Struktur bab tidak dapat ditambahkan materi karena pelatihan tipe Batch sedang aktif berjalan.',
            ]);

            return;
        }

        $chapter = Chapter::where('course_id', $this->courseId)->find($chapterId);
        if (! $chapter) {
            $chapter = Chapter::where('course_id', $this->courseId)
                ->orderBy('order', 'asc')
                ->first();
        }

        if (! $chapter) {
            $chapter = MateriRepo::createChapter([
                'course_id' => $this->courseId,
                'title' => 'Bab 1: Pendahuluan & Materi Umum',
                'order' => 1,
            ]);
        }

        $this->activeEditorChapter = $chapter;
        $this->editorChapterId = $chapter->id;
        $this->editorLessonId = null;

        $maxOrder = Lesson::where('chapter_id', $chapter->id)->max('order');
        $nextOrder = $maxOrder ? $maxOrder + 1 : 1;

        $this->lessonForm = [
            'id' => null,
            'chapter_id' => $chapter->id,
            'title' => '',
            'order' => $nextOrder,
            'content_type' => 'article',
            'body_text' => '',
            'version' => 'Versi 1.0',
        ];

        $this->viewMode = 'editor';
        $this->dispatch('init-editor');
    }

    public function openEditLesson(int $lessonId): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        if ($this->isFrozen) {
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Kurikulum Terkunci',
                'message' => 'Materi tidak dapat diedit karena pelatihan tipe Batch sedang aktif berjalan.',
            ]);

            return;
        }

        $lessonModel = MateriRepo::getLessonById($lessonId);
        if (! $lessonModel) {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Tidak Ditemukan',
                'message' => 'Data materi pembelajaran tidak ditemukan.',
            ]);

            return;
        }

        $this->activeEditorChapter = $lessonModel->chapter;
        $this->editorChapterId = $lessonModel->chapter_id;
        $this->editorLessonId = $lessonModel->id;

        $this->lessonForm = [
            'id' => $lessonModel->id,
            'chapter_id' => $lessonModel->chapter_id,
            'title' => $lessonModel->title,
            'order' => $lessonModel->order,
            'content_type' => 'article',
            'body_text' => $lessonModel->body_text ?? '',
            'version' => $lessonModel->version ?? 'Versi 1.0',
        ];

        $this->viewMode = 'editor';
        $this->dispatch('init-editor');
    }

    public function closeEditor(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->viewMode = 'silabus';
        $this->editorLessonId = null;
        $this->editorChapterId = null;
        $this->activeEditorChapter = null;
        $this->lessonForm = [
            'id' => null,
            'chapter_id' => null,
            'title' => '',
            'order' => 1,
            'content_type' => 'article',
            'body_text' => '',
            'version' => 'Versi 1.0',
        ];
    }

    public function saveLesson(?string $bodyText = null): void
    {
        if ($this->isFrozen) {
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Kurikulum Terkunci',
                'message' => 'Pelatihan tipe Batch sedang aktif berjalan, materi tidak dapat disimpan atau diubah.',
            ]);

            return;
        }

        if ($bodyText !== null) {
            $this->lessonForm['body_text'] = $bodyText;
        }

        try {
            $this->validate([
                'lessonForm.title' => 'required|string|min:3|max:255',
                'lessonForm.chapter_id' => 'required|exists:chapters,id',
                'lessonForm.body_text' => 'required|string',
            ], [
                'lessonForm.title.required' => 'Judul materi pembelajaran wajib diisi.',
                'lessonForm.title.min' => 'Judul materi pembelajaran minimal 3 karakter.',
                'lessonForm.title.max' => 'Judul materi pembelajaran maksimal 255 karakter.',
                'lessonForm.body_text.required' => 'Naskah konten materi pembelajaran belum diisi pada editor.',
            ]);
        } catch (ValidationException $e) {
            $firstError = $e->validator->errors()->first('lessonForm.title') ?: $e->validator->errors()->first();
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'message' => $firstError,
            ]);

            throw $e;
        }

        $payload = [
            'chapter_id' => (int) $this->lessonForm['chapter_id'],
            'title' => trim($this->lessonForm['title']),
            'order' => (int) ($this->lessonForm['order'] ?? 1),
            'content_type' => 'article',
            'body_text' => $this->lessonForm['body_text'],
            'version' => $this->lessonForm['version'] ?? 'Versi 1.0',
        ];

        if ($this->editorLessonId) {
            $success = MateriRepo::updateLesson($this->editorLessonId, $payload);
            $msg = 'Materi pembelajaran berhasil diperbarui.';
        } else {
            $created = MateriRepo::createLesson($payload);
            $success = (bool) $created;
            $msg = 'Materi pembelajaran baru berhasil disimpan ke kurikulum.';
        }

        if ($success) {
            $this->loadCurriculum();
            $this->closeEditor();
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => $msg,
            ]);
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan sistem saat menyimpan materi pembelajaran.',
            ]);
        }
    }

    /**
     * Terbitkan kelas agar dapat diakses oleh peserta.
     */
    public function publishCourse(): void
    {
        if ($this->course && $this->course->isDraft()) {
            $this->course->update([
                'status' => CourseStatus::Published,
            ]);
            $this->course->refresh();

            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Kelas Diterbitkan',
                'message' => 'Kelas "'.$this->course->title.'" kini telah resmi dibuka untuk peserta.',
            ]);
        }
    }

    public function render()
    {
        return view('mods.admin.materi.materi-detail');
    }
}

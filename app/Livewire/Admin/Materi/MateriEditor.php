<?php

namespace App\Livewire\Admin\Materi;

use App\Models\Course;
use App\Models\Lesson;
use App\Repositories\MateriRepo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class MateriEditor extends Component
{
    use WithFileUploads;

    public int $courseId;

    public ?int $chapterId = null;

    public ?int $lessonId = null;

    public ?Course $course = null;

    public bool $isFrozen = false;

    public bool $isAdmin = true;

    // Structured form data
    public array $lesson = [
        'id' => null,
        'chapter_id' => null,
        'title' => '',
        'order' => 1,
        'content_type' => 'article',
        'video_url' => '',
        'body_text' => '',
        'version' => 'Versi 1.0',
        'version_notes' => '',
    ];

    public $attachmentFile = null;

    public array $chapters = [];

    public function mount(int $course_id, ?int $lesson_id = null): void
    {
        $this->courseId = $course_id;
        $this->lessonId = $lesson_id;
        $this->chapterId = request()->query('chapter_id') ? (int) request()->query('chapter_id') : null;

        $this->course = Course::with(['category', 'schedules.mentor'])->findOrFail($course_id);

        $user = Auth::user();
        $this->isAdmin = $user ? $user->hasAdminAccess() : true;

        // Batch freeze rule ala Dicoding
        $this->isFrozen = $this->course->isCurriculumFrozen();

        // Ambil bab silabus nyata dari database
        $chaptersCollection = MateriRepo::getChaptersList($course_id);

        // Jika belum ada bab sama sekali, otomatis inisialisasi Bab 1 agar form editor siap pakai
        if ($chaptersCollection->isEmpty()) {
            $initialChapter = MateriRepo::createChapter([
                'course_id' => $course_id,
                'title' => 'Bab 1: Pendahuluan & Materi Umum',
                'order' => 1,
            ]);
            $chaptersCollection = MateriRepo::getChaptersList($course_id);
        }

        $this->chapters = $chaptersCollection->toArray();

        $defaultChapterId = $this->chapterId ?? ($this->chapters[0]['id'] ?? null);

        if ($this->lessonId) {
            // Edit mode: Ambil data nyata dari database
            $lessonModel = MateriRepo::getLessonById($this->lessonId);
            if (! $lessonModel) {
                session()->flash('alert-show', [
                    'type' => 'danger',
                    'title' => 'Tidak Ditemukan',
                    'message' => 'Data materi pembelajaran tidak ditemukan.',
                ]);
                $this->redirect(route('materi.detail', $this->courseId), navigate: true);

                return;
            }

            $this->lesson = [
                'id' => $lessonModel->id,
                'chapter_id' => $lessonModel->chapter_id,
                'title' => $lessonModel->title,
                'order' => $lessonModel->order,
                'content_type' => $lessonModel->content_type,
                'video_url' => $lessonModel->video_url ?? '',
                'body_text' => $lessonModel->body_text ?? '',
                'version' => $lessonModel->version ?? 'Versi 1.0',
                'version_notes' => $lessonModel->version_notes ?? '',
            ];
        } else {
            // Create mode: Form bersih dengan urutan otomatis
            $nextOrder = 1;
            if ($defaultChapterId) {
                $maxOrder = Lesson::where('chapter_id', $defaultChapterId)->max('order');
                $nextOrder = $maxOrder ? $maxOrder + 1 : 1;
            }

            $this->lesson = [
                'id' => null,
                'chapter_id' => $defaultChapterId,
                'title' => '',
                'order' => $nextOrder,
                'content_type' => 'article',
                'video_url' => '',
                'body_text' => '',
                'version' => 'Versi 1.0',
                'version_notes' => '',
            ];
        }
    }

    public function updatedLessonChapterId($value): void
    {
        // Otomatis sesuaikan nomor urut jika bab diubah pada create mode
        if (! $this->lessonId && $value) {
            $maxOrder = Lesson::where('chapter_id', $value)->max('order');
            $this->lesson['order'] = $maxOrder ? $maxOrder + 1 : 1;
        }
    }

    public function save(?string $bodyText = null): void
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
            $this->lesson['body_text'] = $bodyText;
        }

        $this->validate([
            'lesson.title' => 'required|string|max:255',
            'lesson.chapter_id' => 'required|exists:chapters,id',
            'lesson.order' => 'required|integer|min:1',
            'lesson.version' => 'required|string|max:50',
            'lesson.body_text' => 'required|string',
            'attachmentFile' => 'nullable|file|max:10240', // 10MB max
        ], [
            'lesson.title.required' => 'Judul materi pembelajaran wajib diisi.',
            'lesson.chapter_id.required' => 'Bab kurikulum wajib dipilih.',
            'lesson.chapter_id.exists' => 'Bab kurikulum yang dipilih tidak valid.',
            'lesson.order.required' => 'Nomor urut materi wajib ditentukan.',
            'lesson.version.required' => 'Versi modul materi wajib ditentukan.',
            'lesson.body_text.required' => 'Naskah konten materi pembelajaran belum diisi pada editor.',
        ]);

        $payload = [
            'chapter_id' => (int) $this->lesson['chapter_id'],
            'title' => trim($this->lesson['title']),
            'order' => (int) $this->lesson['order'],
            'content_type' => $this->lesson['content_type'] ?? 'article',
            'video_url' => ! empty($this->lesson['video_url']) ? trim($this->lesson['video_url']) : null,
            'body_text' => $this->lesson['body_text'],
            'version' => trim($this->lesson['version']),
            'version_notes' => ! empty($this->lesson['version_notes']) ? trim($this->lesson['version_notes']) : null,
        ];

        // Simpan attachment jika ada file diunggah
        if ($this->attachmentFile) {
            $payload['attachment_path'] = $this->attachmentFile->store('courses/materials', 'public');
        }

        if ($this->lessonId) {
            // Update
            $success = MateriRepo::updateLesson($this->lessonId, $payload);
            $msg = 'Materi pembelajaran berhasil diperbarui.';
        } else {
            // Create
            $created = MateriRepo::createLesson($payload);
            $success = (bool) $created;
            $msg = 'Materi pembelajaran baru berhasil disimpan ke kurikulum.';
        }

        if ($success) {
            session()->flash('alert-show', [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => $msg,
            ]);

            $this->redirect(route('materi.detail', $this->courseId), navigate: true);

            return;
        }

        $this->dispatch('alert-show', data: [
            'type' => 'danger',
            'title' => 'Gagal',
            'message' => 'Terjadi kesalahan sistem saat menyimpan materi pembelajaran.',
        ]);
    }

    public function render()
    {
        return view('mods.admin.materi.materi-editor');
    }
}

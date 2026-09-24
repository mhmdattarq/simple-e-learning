<?php

namespace App\Livewire\Admin\Materi;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Repositories\MateriRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Url;
use Livewire\Component;

class MateriEditor extends Component
{
    public int $courseId;

    #[Url(as: 'chapter_id')]
    public ?int $chapterId = null;

    public ?int $lessonId = null;

    public ?Course $course = null;

    public ?Chapter $chapter = null;

    public bool $isFrozen = false;

    public bool $isAdmin = true;

    // Structured form data
    public array $lesson = [
        'id' => null,
        'chapter_id' => null,
        'title' => '',
        'order' => 1,
        'content_type' => 'article',
        'body_text' => '',
        'version' => 'Versi 1.0',
    ];

    public function mount(int $course_id, ?int $lesson_id = null, ?int $chapter_id = null): void
    {
        $this->courseId = $course_id;
        $this->lessonId = $lesson_id ?: null;
        $this->chapterId = $chapter_id ?: (request()->query('chapter_id') ? (int) request()->query('chapter_id') : null);

        $this->course = Course::with(['category', 'schedules.mentor'])->findOrFail($course_id);

        $user = Auth::user();
        $this->isAdmin = $user ? $user->hasAdminAccess() : true;

        // Batch freeze rule ala Dicoding
        $this->isFrozen = $this->course->isCurriculumFrozen();

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

            $this->chapter = $lessonModel->chapter;
            $this->chapterId = $lessonModel->chapter_id;

            $this->lesson = [
                'id' => $lessonModel->id,
                'chapter_id' => $lessonModel->chapter_id,
                'title' => $lessonModel->title,
                'order' => $lessonModel->order,
                'content_type' => 'article',
                'body_text' => $lessonModel->body_text ?? '',
                'version' => $lessonModel->version ?? 'Versi 1.0',
            ];
        } else {
            // Create mode: Menggunakan bab yang diklik dari detail materi
            if ($this->chapterId) {
                $this->chapter = Chapter::where('course_id', $course_id)->find($this->chapterId);
            }

            // Fallback jika belum ada parameter atau bab belum dibuat
            if (! $this->chapter) {
                $this->chapter = Chapter::where('course_id', $course_id)->orderBy('order', 'asc')->first();
                if (! $this->chapter) {
                    $this->chapter = MateriRepo::createChapter([
                        'course_id' => $course_id,
                        'title' => 'Bab 1: Pendahuluan & Materi Umum',
                        'order' => 1,
                    ]);
                }
            }

            $this->chapterId = $this->chapter->id;

            // Hitung nomor urut materi berikutnya secara otomatis
            $maxOrder = Lesson::where('chapter_id', $this->chapterId)->max('order');
            $nextOrder = $maxOrder ? $maxOrder + 1 : 1;

            $this->lesson = [
                'id' => null,
                'chapter_id' => $this->chapterId,
                'title' => '',
                'order' => $nextOrder,
                'content_type' => 'article',
                'body_text' => '',
                'version' => 'Versi 1.0',
            ];
        }
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName, [
            'lesson.title' => 'required|string|min:3|max:255',
        ], [
            'lesson.title.required' => 'Judul materi pembelajaran wajib diisi.',
            'lesson.title.min' => 'Judul materi pembelajaran minimal 3 karakter.',
            'lesson.title.max' => 'Judul materi pembelajaran maksimal 255 karakter.',
        ]);
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

        try {
            $this->validate([
                'lesson.title' => 'required|string|min:3|max:255',
                'lesson.chapter_id' => 'required|exists:chapters,id',
                'lesson.body_text' => 'required|string',
            ], [
                'lesson.title.required' => 'Judul materi pembelajaran wajib diisi.',
                'lesson.title.min' => 'Judul materi pembelajaran minimal 3 karakter.',
                'lesson.title.max' => 'Judul materi pembelajaran maksimal 255 karakter.',
                'lesson.body_text.required' => 'Naskah konten materi pembelajaran belum diisi pada editor.',
            ]);
        } catch (ValidationException $e) {
            $firstError = $e->validator->errors()->first('lesson.title') ?: $e->validator->errors()->first();
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'message' => $firstError,
            ]);

            throw $e;
        }

        $payload = [
            'chapter_id' => (int) $this->lesson['chapter_id'],
            'title' => trim($this->lesson['title']),
            'order' => (int) ($this->lesson['order'] ?? 1),
            'content_type' => 'article',
            'body_text' => $this->lesson['body_text'],
            'version' => $this->lesson['version'] ?? 'Versi 1.0',
        ];

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

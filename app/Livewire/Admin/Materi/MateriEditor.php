<?php

namespace App\Livewire\Admin\Materi;

use App\Models\Course;
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
        'version_notes' => 'Rilis awal materi',
    ];

    public $attachmentFile = null;

    public array $chapters = [];

    public bool $showPreview = false;

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

        // Sample chapters for navigation and selection
        $this->chapters = [
            ['id' => 1, 'title' => 'Bab 1: Dasar Regulasi & Pengantar Kurikulum ASN', 'order' => 1],
            ['id' => 2, 'title' => 'Bab 2: Pendalaman Kompetensi Teknis & Studi Kasus', 'order' => 2],
        ];

        if ($this->lessonId) {
            // Edit existing lesson (sample data)
            $this->lesson = [
                'id' => $this->lessonId,
                'chapter_id' => 1,
                'title' => 'Naskah Bacaan: Kerangka Nilai Dasar ASN BerAKHLAK',
                'order' => 2,
                'content_type' => 'article',
                'video_url' => '',
                'body_text' => '{"time":1774396800000,"blocks":[{"type":"paragraph","data":{"text":"BerAKHLAK merupakan akronim dari Berorientasi Pelayanan, Akuntabel, Kompeten, Harmonis, Loyal, Adaptif, dan Kolaboratif."}},{"type":"paragraph","data":{"text":"Nilai-nilai ini menjadi pondasi dasar setiap ASN di lingkungan Pemerintah Kabupaten Aceh Timur dalam melayani masyarakat."}}]}',
                'version' => 'Versi 1.1',
                'version_notes' => 'Pembaruan redaksi regulasi SE MenPAN-RB 2026',
            ];
        } else {
            // Create new lesson
            $this->lesson = [
                'id' => null,
                'chapter_id' => $this->chapterId ?? ($this->chapters[0]['id'] ?? 1),
                'title' => '',
                'order' => 1,
                'content_type' => 'article',
                'video_url' => '',
                'body_text' => '',
                'version' => 'Versi 1.0',
                'version_notes' => 'Rilis materi awal',
            ];
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
            'lesson.chapter_id' => 'required',
            'lesson.order' => 'required|integer|min:1',
            'lesson.version' => 'required|string|max:50',
            'lesson.body_text' => 'required|string',
        ], [
            'lesson.title.required' => 'Judul materi pembelajaran wajib diisi.',
            'lesson.body_text.required' => 'Naskah konten materi pembelajaran belum diisi pada editor.',
        ]);

        $this->dispatch('alert-show', data: [
            'type' => 'success',
            'title' => 'Konten Tersimpan',
            'message' => 'Naskah materi pembelajaran berhasil disimpan ke dalam kurikulum.',
        ]);

        $this->redirect(route('materi.detail', $this->courseId), navigate: true);
    }

    public function render()
    {
        return view('mods.admin.materi.materi-editor');
    }
}

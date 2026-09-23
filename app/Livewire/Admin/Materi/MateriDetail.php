<?php

namespace App\Livewire\Admin\Materi;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class MateriDetail extends Component
{
    use WithFileUploads;

    public int $courseId;

    public ?Course $course = null;

    public bool $isFrozen = false;

    public bool $isAdmin = true;

    public bool $isMentor = false;

    // Interactive curriculum state for UI preview & workflow
    public array $chapters = [];

    // Chapter Modal State
    public bool $showChapterModal = false;

    public array $chapterForm = [
        'id' => null,
        'title' => '',
        'order' => 1,
    ];

    // Lesson Modal State
    public bool $showLessonModal = false;

    public array $lessonForm = [
        'id' => null,
        'chapter_id' => null,
        'title' => '',
        'order' => 1,
        'content_type' => 'article', // 'article' | 'video' | 'document'
        'video_url' => '',
        'body_text' => '',
        'version' => 'Versi 1.0',
        'version_notes' => '',
    ];

    public $attachmentFile = null;

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

        // Initialize structured sample syllabus ala Dicoding so user immediately sees UI
        $this->initSampleCurriculum();
    }

    private function initSampleCurriculum(): void
    {
        $this->chapters = [
            [
                'id' => 1,
                'title' => 'Bab 1: Dasar Regulasi & Pengantar Kurikulum ASN',
                'order' => 1,
                'lessons' => [
                    [
                        'id' => 101,
                        'chapter_id' => 1,
                        'title' => 'Video Orientasi & Visi Misi Pelatihan BerAKHLAK',
                        'order' => 1,
                        'content_type' => 'video',
                        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                        'body_text' => '{"time":1774396800000,"blocks":[{"type":"paragraph","data":{"text":"Selamat datang di orientasi materi pelatihan ASN BerAKHLAK. Simak paparan video berikut dengan seksama."}}]}',
                        'attachment_path' => null,
                        'version' => 'Versi 1.0',
                        'version_notes' => 'Rilis materi orientasi awal',
                        'updated_at' => now()->format('d M Y, H:i'),
                    ],
                    [
                        'id' => 102,
                        'chapter_id' => 1,
                        'title' => 'Naskah Bacaan: Kerangka Nilai Dasar ASN BerAKHLAK',
                        'order' => 2,
                        'content_type' => 'article',
                        'video_url' => null,
                        'body_text' => '{"time":1774396800000,"blocks":[{"type":"header","data":{"text":"Core Values ASN: BerAKHLAK","level":3}},{"type":"paragraph","data":{"text":"BerAKHLAK merupakan akronim dari Berorientasi Pelayanan, Akuntabel, Kompeten, Harmonis, Loyal, Adaptif, dan Kolaboratif. Nilai-nilai ini menjadi pondasi dasar setiap ASN di lingkungan Pemerintah Kabupaten Aceh Timur."}},{"type":"paragraph","data":{"text":"Setiap peserta wajib menginternalisasi nilai-nilai ini dalam pelaksanaan tugas sehari-hari."}}]}',
                        'attachment_path' => null,
                        'version' => 'Versi 1.1',
                        'version_notes' => 'Pembaruan redaksi regulasi SE MenPAN-RB 2026',
                        'updated_at' => now()->subDays(1)->format('d M Y, H:i'),
                    ],
                    [
                        'id' => 103,
                        'chapter_id' => 1,
                        'title' => 'Slide Modul Tayang Paparan Widyaiswara (PDF)',
                        'order' => 3,
                        'content_type' => 'document',
                        'video_url' => null,
                        'body_text' => '{"time":1774396800000,"blocks":[{"type":"paragraph","data":{"text":"Unduh dan pelajari slide materi paparan berikut sebagai bahan diskusi sesi tatap muka."}}]}',
                        'attachment_path' => 'slide_modul_bab_1.pdf',
                        'version' => 'Versi 1.0',
                        'version_notes' => 'Slide tayang modul resmi',
                        'updated_at' => now()->subDays(2)->format('d M Y, H:i'),
                    ],
                ],
            ],
            [
                'id' => 2,
                'title' => 'Bab 2: Pendalaman Kompetensi Teknis & Studi Kasus',
                'order' => 2,
                'lessons' => [
                    [
                        'id' => 201,
                        'chapter_id' => 2,
                        'title' => 'Bedah Kasus: Efektivitas Layanan Publik Terpadu',
                        'order' => 1,
                        'content_type' => 'article',
                        'video_url' => null,
                        'body_text' => '{"time":1774396800000,"blocks":[{"type":"paragraph","data":{"text":"Pelajari studi kasus implementasi pelayanan publik terpadu pada dinas teknis di Aceh Timur."}}]}',
                        'attachment_path' => null,
                        'version' => 'Versi 1.0',
                        'version_notes' => 'Naskah studi kasus',
                        'updated_at' => now()->subDays(3)->format('d M Y, H:i'),
                    ],
                ],
            ],
        ];
    }

    // --- CHAPTER ACTIONS ---
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
        ]);

        if ($this->chapterForm['id']) {
            // Update existing
            foreach ($this->chapters as &$chap) {
                if ($chap['id'] === $this->chapterForm['id']) {
                    $chap['title'] = $this->chapterForm['title'];
                    $chap['order'] = (int) $this->chapterForm['order'];
                    break;
                }
            }
            $msg = 'Bab kurikulum berhasil diperbarui.';
        } else {
            // Create new
            $newId = time();
            $this->chapters[] = [
                'id' => $newId,
                'title' => $this->chapterForm['title'],
                'order' => (int) $this->chapterForm['order'],
                'lessons' => [],
            ];
            $msg = 'Bab baru berhasil ditambahkan ke kurikulum.';
        }

        $this->showChapterModal = false;
        $this->dispatch('alert-show', data: [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => $msg,
        ]);
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

        $this->chapters = array_values(array_filter($this->chapters, fn ($c) => $c['id'] !== $chapterId));

        $this->dispatch('alert-show', data: [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'Bab kurikulum berhasil dihapus.',
        ]);
    }

    // --- LESSON ACTIONS ---
    public function openCreateLesson(int $chapterId): void
    {
        if ($this->isFrozen) {
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Kurikulum Terkunci',
                'message' => 'Materi tidak dapat ditambah karena pelatihan tipe Batch sedang aktif berjalan.',
            ]);

            return;
        }

        // Count lessons in this chapter to auto-calculate order
        $order = 1;
        foreach ($this->chapters as $chap) {
            if ($chap['id'] === $chapterId) {
                $order = count($chap['lessons']) + 1;
                break;
            }
        }

        $this->lessonForm = [
            'id' => null,
            'chapter_id' => $chapterId,
            'title' => '',
            'order' => $order,
            'content_type' => 'article',
            'video_url' => '',
            'body_text' => '',
            'version' => 'Versi 1.0',
            'version_notes' => 'Rilis materi awal',
        ];

        $this->attachmentFile = null;
        $this->showLessonModal = true;

        // Dispatch browser event to initialize Editor.js
        $this->dispatch('init-editorjs', content: '');
    }

    public function openEditLesson(int $lessonId): void
    {
        if ($this->isFrozen) {
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Kurikulum Terkunci',
                'message' => 'Materi tidak dapat diedit karena pelatihan tipe Batch sedang aktif berjalan.',
            ]);

            return;
        }

        foreach ($this->chapters as $chap) {
            foreach ($chap['lessons'] as $les) {
                if ($les['id'] === $lessonId) {
                    $this->lessonForm = [
                        'id' => $les['id'],
                        'chapter_id' => $les['chapter_id'],
                        'title' => $les['title'],
                        'order' => $les['order'],
                        'content_type' => $les['content_type'],
                        'video_url' => $les['video_url'] ?? '',
                        'body_text' => $les['body_text'] ?? '',
                        'version' => $les['version'],
                        'version_notes' => $les['version_notes'] ?? '',
                    ];
                    $this->attachmentFile = null;
                    $this->showLessonModal = true;

                    // Dispatch browser event to initialize Editor.js with saved content
                    $this->dispatch('init-editorjs', content: $les['body_text'] ?? '');

                    return;
                }
            }
        }
    }

    public function saveLesson(): void
    {
        if ($this->isFrozen) {
            return;
        }

        $this->validate([
            'lessonForm.title' => 'required|string|max:255',
            'lessonForm.order' => 'required|integer|min:1',
            'lessonForm.content_type' => 'required|in:article,video,document',
            'lessonForm.version' => 'required|string|max:50',
        ]);

        $chapId = $this->lessonForm['chapter_id'];

        if ($this->lessonForm['id']) {
            // Update
            foreach ($this->chapters as &$chap) {
                if ($chap['id'] === $chapId) {
                    foreach ($chap['lessons'] as &$les) {
                        if ($les['id'] === $this->lessonForm['id']) {
                            $les['title'] = $this->lessonForm['title'];
                            $les['order'] = (int) $this->lessonForm['order'];
                            $les['content_type'] = $this->lessonForm['content_type'];
                            $les['video_url'] = $this->lessonForm['video_url'];
                            $les['body_text'] = $this->lessonForm['body_text'];
                            $les['version'] = $this->lessonForm['version'];
                            $les['version_notes'] = $this->lessonForm['version_notes'];
                            $les['updated_at'] = now()->format('d M Y, H:i');
                            break;
                        }
                    }
                }
            }
            $msg = 'Materi pembelajaran berhasil diperbarui.';
        } else {
            // Create
            $newLessonId = time();
            foreach ($this->chapters as &$chap) {
                if ($chap['id'] === $chapId) {
                    $chap['lessons'][] = [
                        'id' => $newLessonId,
                        'chapter_id' => $chapId,
                        'title' => $this->lessonForm['title'],
                        'order' => (int) $this->lessonForm['order'],
                        'content_type' => $this->lessonForm['content_type'],
                        'video_url' => $this->lessonForm['video_url'],
                        'body_text' => $this->lessonForm['body_text'],
                        'attachment_path' => $this->attachmentFile ? $this->attachmentFile->getClientOriginalName() : null,
                        'version' => $this->lessonForm['version'],
                        'version_notes' => $this->lessonForm['version_notes'],
                        'updated_at' => now()->format('d M Y, H:i'),
                    ];
                    break;
                }
            }
            $msg = 'Materi baru berhasil ditambahkan ke kurikulum.';
        }

        $this->showLessonModal = false;
        $this->dispatch('alert-show', data: [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => $msg,
        ]);
    }

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

        foreach ($this->chapters as &$chap) {
            if ($chap['id'] === $chapterId) {
                $chap['lessons'] = array_values(array_filter($chap['lessons'], fn ($l) => $l['id'] !== $lessonId));
                break;
            }
        }

        $this->dispatch('alert-show', data: [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'Materi berhasil dihapus dari kurikulum.',
        ]);
    }

    public function previewLesson(int $lessonId): void
    {
        foreach ($this->chapters as $chap) {
            foreach ($chap['lessons'] as $les) {
                if ($les['id'] === $lessonId) {
                    $this->previewLesson = $les;
                    $this->showPreviewModal = true;

                    return;
                }
            }
        }
    }

    public function render()
    {
        return view('mods.admin.materi.materi-detail');
    }
}

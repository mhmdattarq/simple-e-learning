<div>
    {{-- Page Header Section --}}
    <section class="page-header py-4" style="background: linear-gradient(135deg, #071a33 0%, #102a43 100%);">
        <div class="container">
            <div class="page-header__inner d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <span class="badge px-3 py-2 text-white border border-light-subtle rounded-pill mb-2 fs-8"
                        style="background: rgba(255, 255, 255, 0.1);">
                        <i class="ri-book-open-line text-warning me-1"></i> Ruang Belajar Mandiri
                    </span>
                    <h2 class="text-white fw-bold mb-1 fs-4 fs-md-3">{{ $course->title }}</h2>
                    <p class="text-white-50 mb-0 fs-7">
                        Akses seluruh modul dan silabus materi pembelajaran interaktif Anda.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 w-100 w-sm-auto">
                    <a href="{{ route('landing.kelas.detail', $course) }}" class="btn btn-simpel-outline-light w-100 w-sm-auto text-center">
                        <i class="ri-arrow-left-line me-1"></i> Kembali ke Detail Kelas
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content Section --}}
    <section class="py-4" style="background: #f8fafc; min-height: 80vh;">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success border-0 shadow-sm radius-12 p-3 mb-4 d-flex align-items-center gap-2">
                    <i class="ri-checkbox-circle-fill text-success fs-5"></i>
                    <span class="fs-8 fw-semibold text-dark">{{ session('success') }}</span>
                </div>
            @endif

            <div class="row g-4">
                {{-- Sidebar: Daftar Sesi & Materi --}}
                <div class="col-lg-4 col-12 order-2 order-lg-1">
                    <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden mb-4 sticky-top"
                        style="top: 20px; z-index: 10;">
                        <div
                            class="card-header bg-white py-14 px-20 border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="fw-bold text-dark mb-0 fs-7">
                                <i class="ri-list-check-2 text-primary me-1"></i> Alur Pembelajaran Sesi
                            </h6>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-8">
                                {{ count($completedLessonIds) }} Materi Selesai
                            </span>
                        </div>

                        <div class="card-body p-0" style="max-height: 75vh; overflow-y: auto;">
                            @forelse($chapters as $chapter)
                                <div class="border-bottom p-3">
                                    <div
                                        class="px-1 py-1 text-secondary fw-bold fs-8 d-flex align-items-center gap-1 mb-2">
                                        <i class="ri-folder-2-line text-primary"></i>
                                        <span>{{ $chapter->title }}</span>
                                    </div>
                                    <div class="list-group list-group-flush rounded-3 overflow-hidden">
                                        @foreach ($chapter->lessons as $lesson)
                                            @php
                                                $isDone = in_array($lesson->id, $completedLessonIds);
                                                $isSelected = $selectedLessonId === $lesson->id;
                                            @endphp
                                            <div wire:click="selectLesson({{ $lesson->id }})" role="button"
                                                class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2 fs-8 border-0 {{ $isSelected ? 'bg-primary text-white fw-bold shadow-sm' : ($isDone ? 'bg-light bg-opacity-50 text-dark' : 'text-dark') }}"
                                                style="user-select: none;">
                                                <div class="d-flex align-items-center gap-2 overflow-hidden text-start">
                                                    @if ($isDone)
                                                        <i
                                                            class="ri-checkbox-circle-fill text-success flex-shrink-0 {{ $isSelected ? 'text-white' : '' }}"></i>
                                                    @else
                                                        <i
                                                            class="ri-play-circle-line flex-shrink-0 {{ $isSelected ? 'text-white' : 'text-primary' }}"></i>
                                                    @endif
                                                    <span class="text-truncate">{{ $lesson->title }}</span>
                                                </div>
                                                <div class="flex-shrink-0 ms-2">
                                                    @if ($lesson->content_type === 'video')
                                                        <i
                                                            class="ri-video-line {{ $isSelected ? 'text-white' : 'text-muted' }}"></i>
                                                    @elseif($lesson->content_type === 'document')
                                                        <i
                                                            class="ri-file-pdf-line {{ $isSelected ? 'text-white' : 'text-muted' }}"></i>
                                                    @else
                                                        <i
                                                            class="ri-article-line {{ $isSelected ? 'text-white' : 'text-muted' }}"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Kuis Evaluasi Bab jika ada --}}
                                    @if ($chapter->quiz)
                                        @php
                                            $chapLessonsAllDone =
                                                $chapter->lessons->isNotEmpty() &&
                                                $chapter->lessons->every(
                                                    fn($l) => in_array($l->id, $completedLessonIds),
                                                );
                                            $quizAttempt = $chapter->quiz->attempts->first();
                                        @endphp
                                        <div class="mt-2 pt-2 border-top">
                                            @if ($quizAttempt)
                                                <a href="{{ route('peserta.evaluasi.kerjakan', ['course' => $course, 'quiz' => $chapter->quiz]) }}"
                                                    class="list-group-item d-flex align-items-center justify-content-between p-2 fs-8 rounded-2 border {{ $quizAttempt->is_passed ? 'bg-success-subtle text-success border-success-subtle' : 'bg-warning-subtle text-warning border-warning-subtle' }} text-decoration-none"
                                                    wire:navigate>
                                                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                        <i class="ri-checkbox-circle-fill flex-shrink-0"></i>
                                                        <span
                                                            class="text-truncate fw-semibold">{{ $chapter->quiz->title }}</span>
                                                    </div>
                                                    <span
                                                        class="badge {{ $quizAttempt->is_passed ? 'bg-success text-white' : 'bg-warning text-dark' }} fs-8">
                                                        {{ number_format($quizAttempt->percentage, 0) }}%
                                                        {{ $quizAttempt->is_passed ? 'Lulus' : 'Belum Lulus' }}
                                                    </span>
                                                </a>
                                            @elseif ($chapLessonsAllDone)
                                                <a href="{{ route('peserta.evaluasi.kerjakan', ['course' => $course, 'quiz' => $chapter->quiz]) }}"
                                                    class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2 fs-8 rounded-2 border border-primary bg-primary-subtle text-primary fw-bold text-decoration-none shadow-sm"
                                                    wire:navigate>
                                                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                        <i class="ri-file-list-3-line flex-shrink-0 fs-6"></i>
                                                        <span class="text-truncate">{{ $chapter->quiz->title }}</span>
                                                    </div>
                                                    <span class="badge bg-primary text-white fs-8">Kerjakan Kuis</span>
                                                </a>
                                            @else
                                                <div class="list-group-item d-flex align-items-center justify-content-between p-2 fs-8 rounded-2 border bg-light text-muted opacity-75"
                                                    title="Selesaikan seluruh materi di bab ini untuk membuka kuis">
                                                    <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                        <i class="ri-lock-line flex-shrink-0 text-secondary"></i>
                                                        <span class="text-truncate">{{ $chapter->quiz->title }}</span>
                                                    </div>
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary fs-8">Terkunci</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted fs-8">
                                    <i class="ri-book-open-line fs-2 text-secondary mb-2 d-block"></i>
                                    Belum ada materi yang diunggah untuk kelas ini.
                                </div>
                            @endforelse

                            {{-- Ujian Akhir Kelas (Final Quiz) jika ada --}}
                            @if ($course->finalQuiz)
                                @php
                                    $totalCourseLessons = $chapters->sum(fn($c) => $c->lessons->count());
                                    $allLessonsDone =
                                        $totalCourseLessons > 0 && count($completedLessonIds) >= $totalCourseLessons;
                                    $finalAttempt = $course->finalQuiz->getAttemptForUser(auth()->id());
                                @endphp
                                <div class="p-3 bg-light border-top">
                                    <div
                                        class="px-1 py-1 text-secondary fw-bold fs-8 d-flex align-items-center gap-1 mb-2">
                                        <i class="ri-award-line text-warning"></i>
                                        <span>UJIAN KELULUSAN KELAS</span>
                                    </div>
                                    @if ($finalAttempt)
                                        <a href="{{ route('peserta.evaluasi.kerjakan', ['course' => $course, 'quiz' => $course->finalQuiz]) }}"
                                            class="list-group-item d-flex align-items-center justify-content-between p-2 fs-8 rounded-2 border {{ $finalAttempt->is_passed ? 'bg-success-subtle text-success border-success-subtle' : 'bg-warning-subtle text-warning border-warning-subtle' }} text-decoration-none"
                                            wire:navigate>
                                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                <i class="ri-award-fill flex-shrink-0"></i>
                                                <span
                                                    class="text-truncate fw-semibold">{{ $course->finalQuiz->title }}</span>
                                            </div>
                                            <span
                                                class="badge {{ $finalAttempt->is_passed ? 'bg-success text-white' : 'bg-warning text-dark' }} fs-8">
                                                {{ number_format($finalAttempt->percentage, 0) }}%
                                                {{ $finalAttempt->is_passed ? 'Lulus' : 'Belum Lulus' }}
                                            </span>
                                        </a>
                                    @elseif ($allLessonsDone)
                                        <a href="{{ route('peserta.evaluasi.kerjakan', ['course' => $course, 'quiz' => $course->finalQuiz]) }}"
                                            class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2 fs-8 rounded-2 border border-warning bg-warning-subtle text-warning fw-bold text-decoration-none shadow-sm"
                                            wire:navigate>
                                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                <i class="ri-award-fill flex-shrink-0 text-warning fs-6"></i>
                                                <span
                                                    class="text-truncate text-dark">{{ $course->finalQuiz->title }}</span>
                                            </div>
                                            <span class="badge bg-warning text-dark fs-8">Ujian Akhir</span>
                                        </a>
                                    @else
                                        <div class="list-group-item d-flex align-items-center justify-content-between p-2 fs-8 rounded-2 border bg-light text-muted opacity-75"
                                            title="Selesaikan seluruh materi di semua bab untuk membuka ujian akhir">
                                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                <i class="ri-lock-line flex-shrink-0 text-secondary"></i>
                                                <span class="text-truncate">{{ $course->finalQuiz->title }}</span>
                                            </div>
                                            <span class="badge bg-secondary-subtle text-secondary fs-8">Terkunci</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Main Column: Pembaca Materi / Lesson Player --}}
                <div class="col-lg-8 col-12 order-1 order-lg-2">
                    @if ($currentLesson)
                        @php
                            $isCompleted = in_array($currentLesson->id, $completedLessonIds);
                        @endphp

                        {{-- Viewer Materi Terbuka --}}
                        <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden mb-4 position-relative">
                            {{-- Header Materi --}}
                            <div class="p-3 p-sm-4 p-md-5 border-bottom bg-white">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                    <span
                                        class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1_5 fs-8 rounded-pill">
                                        <i class="ri-folder-2-line me-1"></i>
                                        {{ $currentChapter?->title ?? 'Bab Pembelajaran' }}
                                    </span>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        @if ($isCompleted)
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1_5 fs-8 rounded-pill">
                                                <i class="ri-checkbox-circle-fill me-1"></i> Selesai Dipelajari
                                            </span>
                                        @endif
                                        <span
                                            class="badge bg-secondary-subtle text-secondary px-3 py-1_5 fs-8 rounded-pill text-uppercase">
                                            <i class="ri-file-info-line me-1"></i> {{ $currentLesson->content_type }}
                                        </span>
                                    </div>
                                </div>
                                <h3 class="fw-bold text-dark mb-0 fs-4 fs-md-3">{{ $currentLesson->title }}</h3>
                            </div>

                            {{-- Body Materi --}}
                            <div class="card-body p-3 p-sm-4 p-md-5">
                                {{-- Video Player --}}
                                @if ($currentLesson->content_type === 'video' && $currentLesson->video_url)
                                    <div class="ratio ratio-16x9 rounded-12 overflow-hidden shadow-sm mb-4 bg-dark">
                                        @if (str_contains($currentLesson->video_url, 'youtube.com') || str_contains($currentLesson->video_url, 'youtu.be'))
                                            @php
                                                $videoUrl = $currentLesson->video_url;
                                                preg_match(
                                                    '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
                                                    $videoUrl,
                                                    $match,
                                                );
                                                $youtubeId = $match[1] ?? '';
                                            @endphp
                                            <iframe src="https://www.youtube.com/embed/{{ $youtubeId }}"
                                                title="{{ $currentLesson->title }}" allowfullscreen></iframe>
                                        @else
                                            <iframe src="{{ $currentLesson->video_url }}"
                                                title="{{ $currentLesson->title }}" allowfullscreen></iframe>
                                        @endif
                                    </div>
                                @endif

                                {{-- Konten Teks / Artikel Bersih & Render Gambar/Format --}}
                                @if ($currentLesson->body_text)
                                    <div class="article-content fs-6 text-dark lh-lg mb-4 p-4 p-md-5 bg-white rounded-16 border shadow-none"
                                        style="font-size: 16px; color: #1e293b; line-height: 1.85;">
                                        <style>
                                            .article-content img {
                                                max-width: 100%;
                                                max-height: 480px;
                                                object-fit: contain;
                                                height: auto;
                                                display: block;
                                                margin: 1.5rem auto;
                                                border-radius: 12px;
                                                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
                                                border: 1px solid #e2e8f0;
                                            }

                                            .article-content iframe,
                                            .article-content video,
                                            .article-content embed {
                                                display: block !important;
                                                margin: 1.75rem auto !important;
                                                max-width: 100% !important;
                                                width: 100% !important;
                                                aspect-ratio: 16 / 9 !important;
                                                height: auto !important;
                                                border-radius: 12px;
                                                border: 1px solid #e2e8f0;
                                                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
                                            }

                                            .article-content p:has(> iframe),
                                            .article-content div:has(> iframe) {
                                                display: flex;
                                                justify-content: center;
                                                width: 100%;
                                                margin: 1.5rem 0;
                                            }

                                            .article-content h1,
                                            .article-content h2,
                                            .article-content h3,
                                            .article-content h4 {
                                                color: #0f172a;
                                                font-weight: 700;
                                                margin-top: 1.75rem;
                                                margin-bottom: 0.85rem;
                                                letter-spacing: -0.2px;
                                            }

                                            .article-content p {
                                                margin-bottom: 1.25rem;
                                                word-wrap: break-word;
                                            }

                                            .article-content ul,
                                            .article-content ol {
                                                padding-left: 1.75rem;
                                                margin-bottom: 1.25rem;
                                            }

                                            .article-content li {
                                                margin-bottom: 0.4rem;
                                            }

                                            .article-content blockquote {
                                                border-left: 4px solid #3b82f6;
                                                padding: 10px 20px;
                                                margin: 1.5rem 0;
                                                background: #f8fafc;
                                                border-radius: 0 8px 8px 0;
                                                color: #475569;
                                            }

                                            .article-content table {
                                                width: 100%;
                                                border-collapse: collapse;
                                                margin: 1.5rem 0;
                                            }

                                            .article-content table th,
                                            .article-content table td {
                                                border: 1px solid #e2e8f0;
                                                padding: 10px 14px;
                                                font-size: 14px;
                                            }

                                            .article-content table th {
                                                background-color: #f1f5f9;
                                                font-weight: 600;
                                            }
                                        </style>
                                        {!! $currentLesson->body_text !!}
                                    </div>
                                @endif

                                {{-- Lampiran Dokumen --}}
                                @if ($currentLesson->attachment_path)
                                    <div
                                        class="p-3 rounded-12 border bg-light d-flex align-items-center justify-content-between mb-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ri-file-download-line fs-3 text-primary"></i>
                                            <div>
                                                <strong class="d-block text-dark fs-8">Berkas Modul
                                                    Pembelajaran</strong>
                                                <small
                                                    class="text-muted">{{ basename($currentLesson->attachment_path) }}</small>
                                            </div>
                                        </div>
                                        <a href="{{ asset('storage/' . $currentLesson->attachment_path) }}"
                                            target="_blank" class="btn btn-outline-primary btn-sm radius-8 fs-8">
                                            <i class="ri-download-cloud-2-line me-1"></i> Unduh Berkas
                                        </a>
                                    </div>
                                @endif
                            </div>

                            {{-- Footer Navigasi : Sticky Bottom Bar agar tombol selalu mudah diakses jika artikel panjang --}}
                            <div class="card-footer bg-white p-3 p-md-4 border-top d-flex align-items-center justify-content-between gap-2 sticky-bottom shadow-sm"
                                style="bottom: 0; z-index: 5; background-color: #ffffff !important; border-top: 1px solid #e2e8f0;">
                                {{-- Tombol Sebelumnya --}}
                                <div class="flex-fill flex-sm-grow-0">
                                    <button type="button" wire:click="previousLesson"
                                        class="btn btn-outline-danger radius-10 px-3 px-sm-4 py-2 fs-7 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 shadow-none w-100 w-sm-auto"
                                        @if ($isFirstLesson) disabled @endif>
                                        <i class="ri-arrow-left-line"></i>
                                        <span>Sebelumnya</span>
                                    </button>
                                </div>

                                {{-- Tombol Kanan: Selanjutnya atau Tandai Selesai Belajar --}}
                                <div class="flex-fill flex-sm-grow-0 text-end">
                                    @if (!$isLastInChapter)
                                        {{-- Masih ada materi berikutnya dalam bab yang sama --}}
                                        <button type="button" wire:click="nextLesson"
                                            class="btn btn-simple-gold radius-10 px-3 px-sm-4 py-2 fs-7 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm w-100 w-sm-auto">
                                            <span>Selanjutnya</span>
                                            <i class="ri-arrow-right-line"></i>
                                        </button>
                                    @elseif (!$hasChapterQuiz && $hasNextChapter)
                                        {{-- Di akhir bab, TIDAK ADA evaluasi bab, dan MASIH ADA bab berikutnya: Tombol Selanjutnya --}}
                                        <button type="button" wire:click="promptCompleteChapter"
                                            class="btn btn-simple-gold radius-10 px-3 px-sm-4 py-2 fs-7 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm w-100 w-sm-auto">
                                            <span>Selanjutnya</span>
                                            <i class="ri-arrow-right-line"></i>
                                        </button>
                                    @else
                                        {{-- Ada evaluasi bab ATAU sudah di akhir seluruh rangkaian bab: Tombol Selesai --}}
                                        <button type="button" wire:click="promptCompleteChapter"
                                            class="btn btn-success radius-10 px-3 px-sm-4 py-2 fs-7 fw-bold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm w-100 w-sm-auto">
                                            <i class="ri-checkbox-circle-line"></i>
                                            <span>Selesai</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Keadaan Belum Ada Materi Dipilih --}}
                        <div class="card border-0 shadow-sm radius-16 bg-white p-5 text-center">
                            <div class="mb-3">
                                <i class="ri-article-line text-secondary" style="font-size: 54px; opacity: 0.5;"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Pilih Materi Pembelajaran</h5>
                            <p class="text-muted fs-8 mb-0">
                                Silakan pilih salah satu judul materi dari daftar sesi di sebelah kiri untuk mulai
                                belajar.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Konfirmasi Selesai Bab --}}
    @if ($showCompleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(15, 23, 42, 0.6); z-index: 1060;"
            role="dialog" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
                <div class="modal-content border-0 radius-20 shadow-lg overflow-hidden bg-white">
                    <div class="modal-body p-4 text-center">
                        {{-- Icon Badge --}}
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 {{ $hasChapterQuiz || !$hasNextChapter ? 'text-success' : 'text-primary' }}"
                            style="width: 64px; height: 64px; background-color: {{ $hasChapterQuiz || !$hasNextChapter ? '#ecfdf5' : '#eff6ff' }}; box-shadow: 0 8px 24px rgba(16, 185, 129, 0.15);">
                            <i class="{{ $hasChapterQuiz || !$hasNextChapter ? 'ri-checkbox-circle-fill' : 'ri-arrow-right-circle-fill' }}"
                                style="font-size: 32px;"></i>
                        </div>

                        <h5 class="fw-bold text-dark mb-2">
                            @if (!$hasChapterQuiz && $hasNextChapter)
                                Lanjut ke Bab Berikutnya
                            @elseif ($hasChapterQuiz)
                                Selesaikan Bab &amp; Mulai Evaluasi
                            @else
                                Selesaikan Pembelajaran Kelas
                            @endif
                        </h5>

                        <div class="bg-light p-3 rounded-12 border mb-4 text-start">
                            <p class="text-muted fs-8 mb-0 line-height-base">
                                @if (!$hasChapterQuiz && $hasNextChapter)
                                    Materi pada bab <strong>{{ $currentChapter?->title ?? 'ini' }}</strong> telah
                                    selesai dipelajari. Apakah Anda ingin melanjutkan ke materi bab berikutnya?
                                @elseif ($hasChapterQuiz)
                                    Bab <strong>{{ $currentChapter?->title ?? 'ini' }}</strong> memiliki kuis evaluasi
                                    pemahaman. Anda akan diarahkan untuk mengerjakan kuis evaluasi bab terlebih dahulu.
                                @elseif ($hasFinalQuiz)
                                    Selamat! Anda telah menyelesaikan materi di semua bab. Anda akan diarahkan menuju ke
                                    <strong>Ujian Akhir Kelas</strong>.
                                @else
                                    Selamat! Anda telah menuntaskan seluruh rangkaian modul pembelajaran pada kelas ini.
                                @endif
                            </p>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="button" wire:click="cancelCompleteChapter"
                                class="btn btn-outline-danger w-50 py-2 radius-10 fw-semibold fs-8">
                                Batal
                            </button>
                            <button type="button" wire:click="confirmCompleteChapter"
                                class="btn {{ $hasChapterQuiz || !$hasNextChapter ? 'btn-success' : 'btn-simple-gold' }} w-50 py-2 radius-10 fw-semibold fs-8 d-inline-flex align-items-center justify-content-center gap-1 shadow-sm">
                                <i class="ri-check-line"></i>
                                {{ !$hasChapterQuiz && $hasNextChapter ? 'Lanjutkan' : 'Ya, Selesai' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

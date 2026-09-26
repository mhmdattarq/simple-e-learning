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
                    <h2 class="text-white fw-bold mb-1 fs-3">{{ $course->title }}</h2>
                    <p class="text-white-50 mb-0 fs-7">
                        Akses silabus materi terstruktur berdasarkan kehadiran sesi pelatihan Anda.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('pelatihan.index') }}" class="btn btn-outline-light btn-sm radius-8 px-3 py-2 fs-8">
                        <i class="ri-arrow-left-line me-1"></i> Katalog Pelatihan
                    </a>
                    @php
                        $allAttended = $schedules->isNotEmpty() && $schedules->every(fn($s) => isset($attendances[$s->id]));
                        $hasActiveUnattended = $schedules->contains(fn($s) => $s->isAttendanceActive() && !isset($attendances[$s->id]));
                        $hasAttended = $attendances->isNotEmpty();
                        $showPortalPresensi = (!$hasAttended && $schedules->isNotEmpty()) || $hasActiveUnattended;
                    @endphp
                    @if($showPortalPresensi && !$allAttended)
                        <a href="{{ route('presensi.index') }}" class="btn btn-warning btn-sm text-dark fw-bold radius-8 px-3 py-2 fs-8">
                            <i class="ri-qr-code-line me-1"></i> Portal Presensi
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content Section --}}
    <section class="py-4" style="background: #f8fafc; min-height: 80vh;">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm radius-12 p-3 mb-4 d-flex align-items-center gap-2">
                    <i class="ri-checkbox-circle-fill text-success fs-5"></i>
                    <span class="fs-8 fw-semibold text-dark">{{ session('success') }}</span>
                </div>
            @endif

            <div class="row g-4">
                {{-- Sidebar: Daftar Sesi & Materi --}}
                <div class="col-lg-4 col-12">
                    <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden mb-4 sticky-top" style="top: 20px; z-index: 10;">
                        <div class="card-header bg-white py-14 px-20 border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="fw-bold text-dark mb-0 fs-7">
                                <i class="ri-list-check-2 text-primary me-1"></i> Alur Pembelajaran Sesi
                            </h6>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-8">
                                {{ count($completedLessonIds) }} Materi Selesai
                            </span>
                        </div>

                        <div class="card-body p-0" style="max-height: 75vh; overflow-y: auto;">
                            {{-- Sesi-sesi Pelatihan --}}
                            @forelse($schedules as $index => $sch)
                                @php
                                    $isAttended = isset($attendances[$sch->id]) || auth()->user()->hasAdminAccess();
                                    $att = $attendances[$sch->id] ?? null;
                                @endphp
                                <div class="border-bottom {{ $isAttended ? 'bg-white' : 'bg-light bg-opacity-75' }}">
                                    {{-- Header Sesi --}}
                                    <div class="p-3 d-flex align-items-start justify-content-between gap-2 border-bottom border-light">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge {{ $isAttended ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }} px-2 py-1 fs-8">
                                                    @if($isAttended)
                                                        <i class="ri-checkbox-circle-line me-1"></i> Hadir (Terbuka)
                                                    @else
                                                        <i class="ri-lock-line me-1"></i> Belum Absen (Terkunci)
                                                    @endif
                                                </span>
                                                <span class="text-muted fs-8">{{ $sch->session_date?->format('d/m/Y') }}</span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1 fs-8">Sesi {{ $index + 1 }}: {{ $sch->session_title }}</h6>
                                            <small class="text-muted d-block fs-8">
                                                <i class="ri-user-voice-line me-1"></i> {{ $sch->mentor?->name ?? 'Tim Pengajar' }}
                                            </small>
                                        </div>

                                        @if(!$isAttended)
                                            <a href="{{ route('presensi.index', $sch->attendance_token ? ['token' => $sch->attendance_token] : []) }}"
                                                class="btn btn-sm btn-warning text-dark fw-bold radius-8 fs-8 flex-shrink-0">
                                                <i class="ri-qr-code-line me-1"></i> Absen
                                            </a>
                                        @endif
                                    </div>

                                    {{-- List Bab dan Konten Sesi --}}
                                    <div class="p-2">
                                        @if($sch->chapters->isEmpty())
                                            <div class="text-muted fs-8 px-2 py-2 fst-italic">
                                                Belum ada materi untuk sesi ini.
                                            </div>
                                        @else
                                            @foreach($sch->chapters as $chapter)
                                                <div class="mb-2">
                                                    <div class="px-2 py-1 text-secondary fw-bold fs-8 d-flex align-items-center gap-1">
                                                        <i class="ri-folder-2-line text-primary"></i>
                                                        <span>{{ $chapter->title }}</span>
                                                    </div>

                                                    <div class="list-group list-group-flush rounded-3 overflow-hidden">
                                                        @foreach($chapter->lessons as $lesson)
                                                            @php
                                                                $isDone = in_array($lesson->id, $completedLessonIds);
                                                                $isSelected = $selectedLessonId === $lesson->id;
                                                                $isAccessible = $isAttended && ($this->isLessonAccessible($lesson->id) || auth()->user()->hasAdminAccess());
                                                            @endphp
                                                            <div class="list-group-item d-flex align-items-center justify-content-between p-2 fs-8 border-0 {{ $isSelected ? 'bg-primary text-white fw-bold shadow-sm' : ($isAccessible ? ($isDone ? 'bg-light bg-opacity-50 text-dark' : 'text-dark') : 'opacity-50 bg-light text-muted') }}"
                                                                style="user-select: none;">
                                                                <div class="d-flex align-items-center gap-2 overflow-hidden text-start">
                                                                    @if(!$isAttended || !$isAccessible)
                                                                        <i class="ri-lock-2-line text-secondary flex-shrink-0"></i>
                                                                    @elseif($isDone)
                                                                        <i class="ri-checkbox-circle-fill text-success flex-shrink-0 {{ $isSelected ? 'text-white' : '' }}"></i>
                                                                    @else
                                                                        <i class="ri-play-circle-line flex-shrink-0 {{ $isSelected ? 'text-white' : 'text-primary' }}"></i>
                                                                    @endif
                                                                    <span class="text-truncate">{{ $lesson->title }}</span>
                                                                </div>
                                                                <div class="flex-shrink-0 ms-2">
                                                                    @if($lesson->content_type === 'video')
                                                                        <i class="ri-video-line {{ $isSelected ? 'text-white' : 'text-muted' }}"></i>
                                                                    @elseif($lesson->content_type === 'document')
                                                                        <i class="ri-file-pdf-line {{ $isSelected ? 'text-white' : 'text-muted' }}"></i>
                                                                    @else
                                                                        <i class="ri-article-line {{ $isSelected ? 'text-white' : 'text-muted' }}"></i>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @empty
                                {{-- Jika belum ada jadwal sesi, tampilkan materi umum --}}
                                @if($generalChapters->isNotEmpty())
                                    <div class="p-3">
                                        <div class="alert alert-info border-0 fs-8 mb-3">
                                            Materi silabus umum pelatihan:
                                        </div>
                                        @foreach($generalChapters as $chapter)
                                            <div class="mb-3">
                                                <div class="px-2 py-1 text-secondary fw-bold fs-8 d-flex align-items-center gap-1">
                                                    <i class="ri-folder-2-line text-primary"></i>
                                                    <span>{{ $chapter->title }}</span>
                                                </div>
                                                <div class="list-group list-group-flush rounded-3">
                                                    @foreach($chapter->lessons as $lesson)
                                                        @php
                                                            $isDone = in_array($lesson->id, $completedLessonIds);
                                                            $isSelected = $selectedLessonId === $lesson->id;
                                                            $isAccessible = $this->isLessonAccessible($lesson->id) || auth()->user()->hasAdminAccess();
                                                        @endphp
                                                        <div class="list-group-item d-flex align-items-center justify-content-between p-2 fs-8 border-0 {{ $isSelected ? 'bg-primary text-white fw-bold shadow-sm' : ($isAccessible ? ($isDone ? 'bg-light bg-opacity-50 text-dark' : 'text-dark') : 'opacity-50 bg-light text-muted') }}"
                                                            style="user-select: none;">
                                                            <div class="d-flex align-items-center gap-2 overflow-hidden text-start">
                                                                @if(!$isAccessible)
                                                                    <i class="ri-lock-2-line text-secondary flex-shrink-0"></i>
                                                                @elseif($isDone)
                                                                    <i class="ri-checkbox-circle-fill text-success flex-shrink-0 {{ $isSelected ? 'text-white' : '' }}"></i>
                                                                @else
                                                                    <i class="ri-play-circle-line flex-shrink-0 {{ $isSelected ? 'text-white' : 'text-primary' }}"></i>
                                                                @endif
                                                                <span class="text-truncate">{{ $lesson->title }}</span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="p-4 text-center text-muted fs-8">
                                        <i class="ri-book-open-line fs-2 text-secondary mb-2 d-block"></i>
                                        Belum ada jadwal sesi atau materi yang diunggah untuk pelatihan ini.
                                    </div>
                                @endif
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Main Column: Pembaca Materi / Lesson Player --}}
                <div class="col-lg-8 col-12">
                    @if($currentLesson)
                        @php
                            $isCurrentSessionAttended = true;
                            if ($currentSchedule && !auth()->user()->hasAdminAccess()) {
                                $isCurrentSessionAttended = isset($attendances[$currentSchedule->id]);
                            }
                            $isCompleted = in_array($currentLesson->id, $completedLessonIds);
                        @endphp

                        @if(!$isCurrentSessionAttended)
                            {{-- Gating Alert: Materi Terkunci karena Belum Absen --}}
                            <div class="card border-0 shadow-sm radius-16 bg-white p-5 text-center">
                                <div class="mb-3">
                                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex align-items-center justify-content-center"
                                        style="width: 72px; height: 72px;">
                                        <i class="ri-lock-2-fill fs-1"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold text-dark mb-2">Materi Sesi Ini Masih Terkunci</h4>
                                <p class="text-muted fs-7 mb-4 mx-auto" style="max-width: 520px;">
                                    Untuk mengakses modul <strong>{{ $currentLesson->title }}</strong> pada <strong>{{ $currentSchedule?->session_title ?? 'Sesi Pelatihan' }}</strong>,
                                    Anda diwajibkan untuk melakukan presensi kehadiran terlebih dahulu.
                                </p>
                                <div>
                                    <a href="{{ route('presensi.index', $currentSchedule?->attendance_token ? ['token' => $currentSchedule->attendance_token] : []) }}"
                                        class="btn btn-warning fw-bold text-dark px-4 py-2 radius-10 shadow-sm">
                                        <i class="ri-qr-code-line me-1"></i> Lakukan Absensi Sekarang
                                    </a>
                                </div>
                            </div>
                        @else
                            {{-- Viewer Materi Terbuka ala Dicoding --}}
                            <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden mb-4 position-relative">
                                {{-- Header Materi --}}
                                <div class="p-24 border-bottom bg-white">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 fs-8 rounded-pill">
                                            <i class="ri-folder-2-line me-1"></i> {{ $currentChapter?->title ?? 'Bab Pembelajaran' }}
                                        </span>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($isCompleted)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 fs-8 rounded-pill">
                                                    <i class="ri-checkbox-circle-fill me-1"></i> Selesai Dipelajari
                                                </span>
                                            @endif
                                            <span class="badge bg-secondary-subtle text-secondary px-3 py-1 fs-8 rounded-pill text-uppercase">
                                                <i class="ri-file-info-line me-1"></i> {{ $currentLesson->content_type }}
                                            </span>
                                        </div>
                                    </div>
                                    <h3 class="fw-bold text-dark mb-1 fs-4">{{ $currentLesson->title }}</h3>
                                    @if($currentSchedule)
                                        <small class="text-muted d-block fs-8">
                                            <i class="ri-calendar-check-line text-success me-1"></i> Terkait dengan: <strong>{{ $currentSchedule->session_title }}</strong>
                                        </small>
                                    @endif
                                </div>

                                {{-- Body Materi ala Dicoding --}}
                                <div class="card-body p-24 p-md-32">
                                    {{-- Video Player --}}
                                    @if($currentLesson->content_type === 'video' && $currentLesson->video_url)
                                        <div class="ratio ratio-16x9 rounded-12 overflow-hidden shadow-sm mb-4 bg-dark">
                                            @if(str_contains($currentLesson->video_url, 'youtube.com') || str_contains($currentLesson->video_url, 'youtu.be'))
                                                @php
                                                    $videoUrl = $currentLesson->video_url;
                                                    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videoUrl, $match);
                                                    $youtubeId = $match[1] ?? '';
                                                @endphp
                                                <iframe src="https://www.youtube.com/embed/{{ $youtubeId }}" title="{{ $currentLesson->title }}" allowfullscreen></iframe>
                                            @else
                                                <iframe src="{{ $currentLesson->video_url }}" title="{{ $currentLesson->title }}" allowfullscreen></iframe>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Konten Teks / Artikel Bersih & Render Gambar/Format --}}
                                    @if($currentLesson->body_text)
                                        <div class="article-content fs-6 text-dark lh-lg mb-4 p-4 bg-white rounded-12 border shadow-none" style="font-size: 15.5px; color: #1e293b; line-height: 1.85;">
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
                                                .article-content h1, .article-content h2, .article-content h3, .article-content h4 {
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
                                                .article-content ul, .article-content ol {
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
                                                .article-content table th, .article-content table td {
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
                                    @if($currentLesson->attachment_path)
                                        <div class="p-3 rounded-12 border bg-light d-flex align-items-center justify-content-between mb-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ri-file-download-line fs-3 text-primary"></i>
                                                <div>
                                                    <strong class="d-block text-dark fs-8">Berkas Modul Pembelajaran</strong>
                                                    <small class="text-muted">{{ basename($currentLesson->attachment_path) }}</small>
                                                </div>
                                            </div>
                                            <a href="{{ asset('storage/' . $currentLesson->attachment_path) }}" target="_blank"
                                                class="btn btn-outline-primary btn-sm radius-8 fs-8">
                                                <i class="ri-download-cloud-2-line me-1"></i> Unduh Berkas
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                {{-- Footer Navigasi Dicoding: Sticky Bottom Bar agar tombol selalu mudah diakses jika artikel panjang --}}
                                <div class="card-footer bg-white p-20 border-top d-flex align-items-center justify-content-between flex-wrap gap-3 sticky-bottom shadow-sm"
                                    style="bottom: 0; z-index: 5; background-color: #ffffff !important; border-top: 1px solid #e2e8f0;">
                                    {{-- Tombol Sebelumnya --}}
                                    <div>
                                        <button type="button"
                                            wire:click="previousLesson"
                                            class="btn btn-outline-secondary radius-10 px-4 py-2 fs-7 fw-semibold d-inline-flex align-items-center gap-2 shadow-none"
                                            @if($isFirstLesson) disabled @endif>
                                            <i class="ri-arrow-left-line"></i>
                                            <span>Sebelumnya</span>
                                        </button>
                                    </div>

                                    {{-- Tombol Kanan: Selanjutnya atau Tandai Selesai Belajar --}}
                                    <div>
                                        @if(!$isLastInChapter)
                                            {{-- Masih ada materi berikutnya dalam bab yang sama --}}
                                            <button type="button"
                                                wire:click="nextLesson"
                                                class="btn btn-primary radius-10 px-4 py-2 fs-7 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                                                <span>Selanjutnya</span>
                                                <i class="ri-arrow-right-line"></i>
                                            </button>
                                        @else
                                            {{-- Di materi terakhir bab: Munculkan Tandai Selesai Belajar --}}
                                            <button type="button"
                                                wire:click="promptCompleteChapter"
                                                class="btn btn-success radius-10 px-4 py-2 fs-7 fw-bold d-inline-flex align-items-center gap-2 shadow-sm">
                                                <i class="ri-checkbox-circle-line"></i>
                                                <span>Tandai Selesai Belajar</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        {{-- Keadaan Belum Ada Materi Dipilih --}}
                        <div class="card border-0 shadow-sm radius-16 bg-white p-5 text-center">
                            <div class="mb-3">
                                <i class="ri-article-line text-secondary" style="font-size: 54px; opacity: 0.5;"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Pilih Materi Pembelajaran</h5>
                            <p class="text-muted fs-8 mb-0">
                                Silakan pilih salah satu judul materi dari daftar sesi di sebelah kiri untuk mulai belajar.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Konfirmasi Selesai Bab ala Dicoding --}}
    @if($showCompleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(15, 23, 42, 0.6); z-index: 1060;" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
                <div class="modal-content border-0 radius-20 shadow-lg overflow-hidden bg-white">
                    <div class="modal-body p-4 text-center">
                        {{-- Icon Badge --}}
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 text-success"
                            style="width: 64px; height: 64px; background-color: #ecfdf5; box-shadow: 0 8px 24px rgba(16, 185, 129, 0.15);">
                            <i class="ri-checkbox-circle-fill" style="font-size: 32px;"></i>
                        </div>

                        <h5 class="fw-bold text-dark mb-2">Konfirmasi Selesai Bab</h5>

                        <div class="bg-light p-3 rounded-12 border mb-4 text-start">
                            <p class="text-muted fs-8 mb-0 line-height-base">
                                Apakah Anda yakin menandai bab <strong>{{ $currentChapter?->title ?? 'ini' }}</strong> selesai?
                                @if($hasNextChapter)
                                    Setelah ini, sistem akan otomatis mengarahkan Anda ke materi bab berikutnya.
                                @else
                                    Seluruh modul pembelajaran pada materi ini telah Anda tuntaskan.
                                @endif
                            </p>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="button"
                                wire:click="cancelCompleteChapter"
                                class="btn btn-outline-secondary w-50 py-2 radius-10 fw-semibold fs-8">
                                Batal
                            </button>
                            <button type="button"
                                wire:click="confirmCompleteChapter"
                                class="btn btn-success w-50 py-2 radius-10 fw-semibold fs-8 d-inline-flex align-items-center justify-content-center gap-1 shadow-sm">
                                <i class="ri-check-line"></i> Ya, Selesai
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

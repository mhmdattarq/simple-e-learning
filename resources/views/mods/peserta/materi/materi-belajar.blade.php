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
                    <a href="{{ route('presensi.index') }}" class="btn btn-warning btn-sm text-dark fw-bold radius-8 px-3 py-2 fs-8">
                        <i class="ri-qr-code-line me-1"></i> Portal Presensi
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content Section --}}
    <section class="py-4" style="background: #f8fafc; min-height: 80vh;">
        <div class="container">
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
                                                            @endphp
                                                            <button type="button"
                                                                wire:click="selectLesson({{ $lesson->id }})"
                                                                class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2 fs-8 border-0 {{ $isSelected ? 'bg-primary text-white' : ($isAttended ? 'hover-bg-light' : 'opacity-60 text-muted') }}"
                                                                @if(!$isAttended) title="Lakukan absensi pada Sesi ini untuk membuka materi" @endif>
                                                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                                    @if(!$isAttended)
                                                                        <i class="ri-lock-2-line text-warning flex-shrink-0"></i>
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
                                                            </button>
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
                                                        @endphp
                                                        <button type="button"
                                                            wire:click="selectLesson({{ $lesson->id }})"
                                                            class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2 fs-8 border-0 {{ $isSelected ? 'bg-primary text-white' : 'hover-bg-light' }}">
                                                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                                @if($isDone)
                                                                    <i class="ri-checkbox-circle-fill text-success flex-shrink-0 {{ $isSelected ? 'text-white' : '' }}"></i>
                                                                @else
                                                                    <i class="ri-play-circle-line flex-shrink-0 {{ $isSelected ? 'text-white' : 'text-primary' }}"></i>
                                                                @endif
                                                                <span class="text-truncate">{{ $lesson->title }}</span>
                                                            </div>
                                                        </button>
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
                            {{-- Viewer Materi Terbuka --}}
                            <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden mb-4">
                                {{-- Header Materi --}}
                                <div class="p-24 border-bottom">
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 fs-8 rounded-pill">
                                            <i class="ri-folder-2-line me-1"></i> {{ $currentChapter?->title ?? 'Bab Pembelajaran' }}
                                        </span>
                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-1 fs-8 rounded-pill text-uppercase">
                                            <i class="ri-file-info-line me-1"></i> Tipe: {{ $currentLesson->content_type }}
                                        </span>
                                    </div>
                                    <h3 class="fw-bold text-dark mb-1 fs-4">{{ $currentLesson->title }}</h3>
                                    @if($currentSchedule)
                                        <small class="text-muted d-block fs-8">
                                            <i class="ri-calendar-check-line text-success me-1"></i> Terkait dengan: <strong>{{ $currentSchedule->session_title }}</strong>
                                        </small>
                                    @endif
                                </div>

                                {{-- Body Materi --}}
                                <div class="card-body p-24">
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

                                    {{-- Konten Teks / Artikel --}}
                                    @if($currentLesson->body_text)
                                        <div class="article-content fs-7 text-dark lh-lg mb-4 p-3 bg-light rounded-12 border">
                                            {!! nl2br(e($currentLesson->body_text)) !!}
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

                                {{-- Footer Aksi Selesai --}}
                                <div class="card-footer bg-white p-20 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div class="fs-8 text-muted">
                                        @if($isCompleted)
                                            <span class="text-success fw-bold d-flex align-items-center gap-1">
                                                <i class="ri-checkbox-circle-fill"></i> Anda telah menyelesaikan materi ini.
                                            </span>
                                        @else
                                            <span>Tandai selesai jika Anda telah mempelajari materi ini.</span>
                                        @endif
                                    </div>
                                    <div>
                                        <button type="button"
                                            wire:click="toggleCompleteLesson({{ $currentLesson->id }})"
                                            class="btn {{ $isCompleted ? 'btn-outline-secondary' : 'btn-success' }} fw-bold radius-10 px-4 py-2 fs-7 d-inline-flex align-items-center gap-2">
                                            @if($isCompleted)
                                                <i class="ri-restart-line"></i> Batalkan Status Selesai
                                            @else
                                                <i class="ri-checkbox-circle-line"></i> Tandai Selesai Belajar
                                            @endif
                                        </button>
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
</div>

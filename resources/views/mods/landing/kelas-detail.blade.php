<div>
    {{-- Header Banner --}}
    <section class="py-5 text-white position-relative"
        style="background: linear-gradient(135deg, #051427 0%, #071a33 50%, #0c3158 100%);">
        <div class="container py-lg-3 py-2">
            {{-- Breadcrumb & Back --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 fs-8">
                        <li class="breadcrumb-item"><a href="{{ route('landing') }}"
                                class="text-white-50 text-decoration-none hover-gold">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ $backUrl }}"
                                class="text-white-50 text-decoration-none hover-gold">Katalog Kelas</a></li>
                        <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">
                            {{ Str::limit($course->title, 40) }}</li>
                    </ol>
                </nav>
                <a href="{{ $backUrl }}" class="btn-simpel-outline-light">
                    <i class="ri-arrow-left-line me-1"></i>
                    <span>Kembali ke Katalog</span>
                </a>
            </div>

            <div class="row align-items-center g-4">
                <div class="col-lg-8 wow fadeInLeft" data-wow-delay="100ms">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                        @if ($course->isPermanent())
                            <span class="badge bg-gold text-navy fw-bold fs-8 px-3 py-1_5">
                                <i class="ri-infinity-line me-1"></i>Belajar Mandiri (Self-Paced)
                            </span>
                        @elseif ($course->isBatch())
                            <span class="badge bg-gold text-navy fw-bold fs-8 px-3 py-1_5">
                                <i class="ri-calendar-event-line me-1"></i>Batch Terjadwal
                            </span>
                        @else
                            <span class="badge bg-gold text-navy fw-bold fs-8 px-3 py-1_5">
                                <i class="ri-money-dollar-circle-line me-1"></i>Kelas Berbayar
                            </span>
                        @endif

                        @if ($course->category)
                            <span class="badge" style="background: rgba(255, 255, 255, 0.15); color: #fff;">
                                {{ $course->category->name }}
                            </span>
                        @endif

                        <span class="badge" style="background: rgba(255, 255, 255, 0.15); color: #fff;">
                            <i class="ri-award-line me-1"></i>Bersertifikat
                        </span>
                    </div>

                    <h1 class="display-6 fw-extrabold text-white mb-3">
                        {{ $course->title }}
                    </h1>

                    <p class="text-white-80 fs-6 mb-4 pe-lg-4 line-clamp-3">
                        {{ $course->description ?: ($course->category?->description ?: 'Tingkatkan kompetensi Anda melalui program kelas terstruktur dari BKPSDM Aceh Timur. Pelajari modul, ikuti kuis, dan raih sertifikat resmi.') }}
                    </p>

                    <div class="d-flex flex-wrap align-items-center gap-4 text-white-80 fs-8 pt-2">
                        <span class="d-flex align-items-center gap-2">
                            <i class="ri-folder-2-line text-gold fs-6"></i>
                            <strong>{{ $totalChapters }} Bab Modul</strong>
                        </span>
                        <span class="d-flex align-items-center gap-2">
                            <i class="ri-book-open-line text-gold fs-6"></i>
                            <strong>{{ $totalLessons }} Materi Pelajaran</strong>
                        </span>
                        <span class="d-flex align-items-center gap-2">
                            <i class="ri-time-line text-gold fs-6"></i>
                            <strong>
                                @if ($course->isPermanent())
                                    Akses Kapan Saja (24/7)
                                @elseif ($course->start_date && $course->end_date)
                                    {{ $course->start_date->format('H:i') !== '00:00' ? $course->start_date->format('d M Y, H:i') : $course->start_date->format('d M') }}
                                    -
                                    {{ $course->end_date->format('H:i') !== '00:00' ? $course->end_date->format('d M Y, H:i \W\I\B') : $course->end_date->format('d M Y') }}
                                @else
                                    Sesuai Jadwal Diklat
                                @endif
                            </strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content Section --}}
    <section class="py-5 bg-light" style="min-height: 70vh;">
        <div class="container py-lg-3">
            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show radius-12 border-0 shadow-sm mb-4 d-flex align-items-center gap-3 bg-warning-subtle text-dark"
                    role="alert">
                    <i class="ri-alert-line fs-4 text-warning"></i>
                    <div class="flex-grow-1 fs-7">
                        {{ session('warning') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show radius-12 border-0 shadow-sm mb-4 d-flex align-items-center gap-3 bg-info-subtle text-dark"
                    role="alert">
                    <i class="ri-information-line fs-4 text-info"></i>
                    <div class="flex-grow-1 fs-7">
                        {{ session('info') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                {{-- Left Column: Detail, Syllabus, Requirements (Order 2 on mobile, Order 1 on desktop) --}}
                <div class="col-lg-8 order-2 order-lg-1">
                    {{-- About Course --}}
                    <div class="card border border-simpel rounded-4 bg-white shadow-xs p-4 mb-4">
                        <h4 class="fw-bold text-navy mb-3 d-flex align-items-center gap-2">
                            <i class="ri-information-line text-gold"></i>
                            <span>Tentang Kelas Ini</span>
                        </h4>
                        <div class="text-secondary fs-7 lh-lg">
                            @if ($course->description)
                                <div class="mb-3" style="white-space: pre-line;">{{ $course->description }}</div>
                            @elseif ($course->category?->description)
                                <div class="mb-3">{{ $course->category->description }}</div>
                            @else
                                <p class="mb-3">
                                    Program kelas ini dirancang khusus untuk meningkatkan keahlian dan kompetensi
                                    aparatur dalam menghadapi tantangan profesional era digital. Materi disusun secara
                                    komprehensif mulai dari konsep dasar hingga implementasi praktis.
                                </p>
                            @endif
                            <p class="mb-0 text-muted fs-8">
                                Setiap peserta dapat mempelajari modul secara mandiri atau terjadwal, menyelesaikan
                                latihan pada tiap bab, dan memantau kemajuan belajar secara langsung melalui platform
                                pembelajaran elektronik BKPSDM Aceh Timur.
                            </p>
                        </div>
                    </div>

                    {{-- Syllabus / Curriculum --}}
                    <div class="card border border-simpel rounded-4 bg-white shadow-xs p-4 mb-4">
                        <div
                            class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom border-simpel">
                            <div>
                                <h4 class="fw-bold text-navy mb-1 d-flex align-items-center gap-2">
                                    <i class="ri-list-check-2 text-gold"></i>
                                    <span>Silabus & Kurikulum Materi</span>
                                </h4>
                                <span class="text-muted fs-8">Struktur materi pembelajaran yang akan Anda pelajari dalam
                                    kelas ini</span>
                            </div>
                            <span class="badge bg-navy text-white fw-bold fs-8 px-3 py-1_5">
                                {{ $totalChapters }} Bab • {{ $totalLessons }} Materi
                            </span>
                        </div>

                        @if ($course->chapters->isNotEmpty())
                            <div class="accordion accordion-flush" id="accordionSyllabus">
                                @foreach ($course->chapters as $index => $chapter)
                                    <div class="accordion-item border border-simpel rounded-3 mb-3 overflow-hidden">
                                        <h2 class="accordion-header" id="heading-{{ $chapter->id }}">
                                            <button
                                                class="accordion-button {{ $index === 0 ? '' : 'collapsed' }} bg-light fw-bold text-navy py-3 px-3 px-sm-4"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#collapse-{{ $chapter->id }}"
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                                aria-controls="collapse-{{ $chapter->id }}">
                                                <div
                                                    class="d-flex align-items-center justify-content-between w-100 me-2 me-sm-3 flex-wrap gap-1">
                                                    <span class="d-flex align-items-center gap-2 fs-7">
                                                        <span
                                                            class="badge bg-gold text-navy rounded-circle px-2 py-1 fs-9">{{ $loop->iteration }}</span>
                                                        <span>{{ $chapter->title }}</span>
                                                    </span>
                                                    <span class="text-muted fs-8 fw-normal">
                                                        {{ $chapter->lessons->count() }} Pelajaran
                                                    </span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse-{{ $chapter->id }}"
                                            class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                            aria-labelledby="heading-{{ $chapter->id }}"
                                            data-bs-parent="#accordionSyllabus">
                                            <div class="accordion-body p-0">
                                                <ul class="list-group list-group-flush">
                                                    @forelse ($chapter->lessons as $lesson)
                                                        <li
                                                            class="list-group-item d-flex align-items-center justify-content-between py-2_5 py-sm-3 px-3 px-sm-4 border-simpel fs-8 gap-2">
                                                            <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                                <i class="ri-play-circle-line text-gold fs-6 flex-shrink-0"></i>
                                                                <span
                                                                    class="text-dark fw-medium text-truncate">{{ $lesson->title }}</span>
                                                            </div>
                                                            <span
                                                                class="badge bg-light text-muted border border-simpel fs-9 flex-shrink-0">
                                                                Tersedia
                                                            </span>
                                                        </li>
                                                    @empty
                                                        <li
                                                            class="list-group-item py-3 px-3 px-sm-4 text-muted fs-8 fst-italic">
                                                            Materi sedang dalam proses persiapan.
                                                        </li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="ri-book-open-line fs-1 text-gold opacity-50 mb-2"></i>
                                <p class="fs-8 mb-0">Silabus materi sedang disusun oleh pengampu materi.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right Column: Action Card (Order 1 on mobile, Order 2 on desktop) --}}
                <div class="col-lg-4 order-1 order-lg-2">
                    <div class="card border border-simpel rounded-4 bg-white shadow-sm overflow-hidden sticky-lg-top"
                        style="top: 105px; z-index: 15;">
                        {{-- Thumbnail Preview --}}
                        <div class="position-relative" style="height: 200px; background: #071a33;">
                            @if ($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                    class="w-100 h-100 object-fit-cover">
                            @else
                                <div
                                    class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white">
                                    <i class="ri-book-open-line text-gold" style="font-size: 54px;"></i>
                                    <span class="fs-8 text-white-50 mt-1">SIMPEL E-Learning</span>
                                </div>
                            @endif
                            <div class="position-absolute top-0 start-0 m-3">
                                @if ($course->isPaid())
                                    <span class="badge bg-gold text-navy fw-bold fs-8 px-2_5 py-1">
                                        Rp {{ number_format($course->price, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="badge bg-success text-white fw-bold fs-8 px-2_5 py-1">
                                        100% Gratis
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Card Action Body --}}
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <small class="text-muted d-block fs-8">Biaya Program</small>
                                <h3 class="fw-extrabold text-navy mb-0">
                                    {{ $course->isPaid() ? 'Rp ' . number_format($course->price, 0, ',', '.') : 'Gratis' }}
                                </h3>
                            </div>

                            {{-- CTA Button to Materi --}}
                            @auth
                                @if ($course->isBatchNotStarted() && ! auth()->user()->hasAdminAccess())
                                    <button type="button"
                                        class="btn btn-secondary w-100 py-3 fw-bold text-white shadow-none d-flex align-items-center justify-content-center gap-2 radius-10 mb-2 opacity-75"
                                        disabled>
                                        <i class="ri-time-line fs-5"></i>
                                        <span>Batch Belum Dibuka</span>
                                    </button>
                                    <div class="p-2_5 bg-warning-subtle text-dark border border-warning-subtle radius-10 fs-8 mb-3 text-center">
                                        <i class="ri-calendar-event-line text-warning me-1"></i>
                                        Materi dapat diakses mulai <strong>{{ $course->start_date?->translatedFormat('d M Y, H:i') }} WIB</strong>
                                    </div>
                                @elseif ($course->isBatchEnded() && ! auth()->user()->hasAdminAccess())
                                    <button type="button"
                                        class="btn btn-secondary w-100 py-3 fw-bold text-white shadow-none d-flex align-items-center justify-content-center gap-2 radius-10 mb-2 opacity-75"
                                        disabled>
                                        <i class="ri-forbid-line fs-5"></i>
                                        <span>Batch Telah Berakhir</span>
                                    </button>
                                    <div class="p-2_5 bg-danger-subtle text-danger border border-danger-subtle radius-10 fs-8 mb-3 text-center">
                                        <i class="ri-error-warning-line me-1"></i>
                                        Masa pembelajaran kelas ini telah selesai pada <strong>{{ $course->end_date?->translatedFormat('d M Y, H:i') }} WIB</strong>
                                    </div>
                                @else
                                    <a href="{{ route('peserta.materi', $course) }}"
                                        class="btn btn-warning w-100 py-3 fw-bold text-navy shadow-sm d-flex align-items-center justify-content-center gap-2 radius-10 mb-3"
                                        style="background: #e5a93b; border-color: #e5a93b;">
                                        <i class="ri-play-circle-line fs-5"></i>
                                        <span>{{ $isEnrolled ? 'Lanjut Belajar' : 'Mulai Belajar Sekarang' }}</span>
                                        <i class="ri-arrow-right-line"></i>
                                    </a>
                                @endif
                            @else
                                @if ($course->isBatchEnded())
                                    <button type="button"
                                        class="btn btn-secondary w-100 py-3 fw-bold text-white shadow-none d-flex align-items-center justify-content-center gap-2 radius-10 mb-2 opacity-75"
                                        disabled>
                                        <i class="ri-forbid-line fs-5"></i>
                                        <span>Batch Telah Berakhir</span>
                                    </button>
                                @else
                                    <a href="{{ route('login', ['redirect' => route('peserta.materi', $course)]) }}"
                                        class="btn btn-warning w-100 py-3 fw-bold text-navy shadow-sm d-flex align-items-center justify-content-center gap-2 radius-10 mb-3"
                                        style="background: #e5a93b; border-color: #e5a93b;">
                                        <i class="ri-login-box-line fs-5"></i>
                                        <span>Masuk untuk Belajar</span>
                                        <i class="ri-arrow-right-line"></i>
                                    </a>
                                @endif
                                <p class="text-center text-muted fs-8 mb-3">
                                    Belum memiliki akun?
                                    <a href="{{ route('register', ['redirect' => route('peserta.materi', $course)]) }}"
                                        class="text-navy fw-bold text-decoration-none hover-gold">Daftar Akun Baru</a>
                                </p>
                            @endauth

                            {{-- Summary Meta Specs --}}
                            <div class="pt-3 border-top border-simpel">
                                <h6 class="fw-bold text-navy fs-8 mb-3">Informasi Kelas</h6>
                                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 fs-8 text-secondary">
                                    <li class="d-flex align-items-center justify-content-between">
                                        <span class="d-flex align-items-center gap-2">
                                            <i class="ri-time-line text-gold"></i>
                                            <span>Jadwal Akses</span>
                                        </span>
                                        <strong class="text-navy">
                                            @if ($course->isPermanent())
                                                Mandiri 24/7
                                            @elseif ($course->start_date && $course->end_date)
                                                {{ $course->start_date->format('H:i') !== '00:00' ? $course->start_date->format('d M Y, H:i') : $course->start_date->format('d M') }}
                                                -
                                                {{ $course->end_date->format('H:i') !== '00:00' ? $course->end_date->format('d M Y, H:i \W\I\B') : $course->end_date->format('d M Y') }}
                                            @else
                                                Terjadwal
                                            @endif
                                        </strong>
                                    </li>
                                    <li class="d-flex align-items-center justify-content-between">
                                        <span class="d-flex align-items-center gap-2">
                                            <i class="ri-folder-2-line text-gold"></i>
                                            <span>Jumlah Bab</span>
                                        </span>
                                        <strong class="text-navy">{{ $totalChapters }} Bab</strong>
                                    </li>
                                    <li class="d-flex align-items-center justify-content-between">
                                        <span class="d-flex align-items-center gap-2">
                                            <i class="ri-book-open-line text-gold"></i>
                                            <span>Total Pelajaran</span>
                                        </span>
                                        <strong class="text-navy">{{ $totalLessons }} Materi</strong>
                                    </li>
                                    <li class="d-flex align-items-center justify-content-between">
                                        <span class="d-flex align-items-center gap-2">
                                            <i class="ri-award-line text-gold"></i>
                                            <span>Sertifikat</span>
                                        </span>
                                        <strong class="text-success">Tersedia</strong>
                                    </li>
                                    <li class="d-flex align-items-center justify-content-between">
                                        <span class="d-flex align-items-center gap-2">
                                            <i class="ri-building-line text-gold"></i>
                                            <span>Penyelenggara</span>
                                        </span>
                                        <strong class="text-navy">BKPSDM Aceh Timur</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

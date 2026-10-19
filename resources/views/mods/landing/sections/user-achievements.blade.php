{{-- Section: Pencapaian & Sertifikat Pengguna (Khusus Pengguna Login yang Memiliki Sertifikat) --}}
@auth
    @if (isset($myCertificates) && $myCertificates->isNotEmpty())
        <section class="py-5 bg-white border-bottom border-simpel position-relative overflow-hidden"
            id="pencapaian-sertifikat">
            {{-- Background subtle decorative pattern --}}
            <div class="position-absolute top-0 end-0 p-4 opacity-10 pointer-events-none d-none d-lg-block">
                <i class="ri-award-fill text-gold" style="font-size: 140px; line-height: 1;"></i>
            </div>

            <div class="container py-lg-3 py-1 position-relative" style="z-index: 1;">
                {{-- Carousel Alpine Container --}}
                <div x-data="{
                    scrollEl: null,
                    canPrev: false,
                    canNext: false,
                    hasOverflow: false,
                    init() {
                        this.scrollEl = this.$refs.track;
                        this.$nextTick(() => this.updateControls());
                    },
                    scrollStep(direction) {
                        if (!this.scrollEl) return;
                        const slide = this.scrollEl.querySelector('.simpel-cert-slide');
                        const step = slide ? (slide.offsetWidth + 24) : (this.scrollEl.clientWidth / 3);
                        this.scrollEl.scrollBy({ left: direction * step, behavior: 'smooth' });
                        setTimeout(() => this.updateControls(), 350);
                    },
                    prev() {
                        this.scrollStep(-1);
                    },
                    next() {
                        this.scrollStep(1);
                    },
                    updateControls() {
                        if (!this.scrollEl) return;
                        const sl = this.scrollEl.scrollLeft;
                        const maxScroll = this.scrollEl.scrollWidth - this.scrollEl.clientWidth;
                        this.hasOverflow = maxScroll > 15;
                        this.canPrev = sl > 10;
                        this.canNext = sl < maxScroll - 10;
                    }
                }" @resize.window.debounce.150ms="updateControls()">

                    {{-- Section Header & Navigation Controls --}}
                    <div class="row align-items-end justify-content-between mb-4 g-3">
                        <div class="col-md-8">
                            <div class="d-inline-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-gold text-navy fw-bold fs-8 px-3 py-1_5 rounded-pill shadow-xs">
                                    <i class="ri-award-fill me-1"></i>Pencapaian Belajar Anda
                                </span>
                                <span
                                    class="badge bg-success-subtle text-success border border-success-subtle fw-semibold fs-8 px-2 py-1 rounded-pill">
                                    {{ $myCertificates->count() }} Sertifikat Diraih
                                </span>
                            </div>
                            <h2 class="fw-extrabold text-navy display-6 mb-2 fs-3 fs-md-2">
                                Sertifikat & <span class="text-gold">Kelas Selesai</span>
                            </h2>
                            <p class="text-muted fs-6 mb-0">
                                Selamat, <strong>{{ Auth::user()->name }}</strong>! Anda telah berhasil menuntaskan program
                                kompetensi aparatur di bawah ini. Unduh atau simpan sertifikat resmi Anda.
                            </p>
                        </div>

                        {{-- Navigasi Carousel (Muncul otomatis jika sertifikat melebihi kapasitas layar, misal > 3 card di desktop) --}}
                        <div class="col-md-4 text-md-end d-flex align-items-center justify-content-md-end gap-2"
                            x-show="hasOverflow" x-cloak>
                            <button type="button"
                                class="btn btn-outline-secondary rounded-circle d-inline-flex align-items-center justify-content-center p-0 border border-simpel"
                                style="width: 42px; height: 42px; transition: all 0.2s ease;"
                                :class="{ 'opacity-40 pe-none': !canPrev, 'hover-navy shadow-sm': canPrev }" @click="prev"
                                aria-label="Sertifikat Sebelumnya">
                                <i class="ri-arrow-left-s-line fs-4"></i>
                            </button>
                            <button type="button"
                                class="btn btn-navy text-white rounded-circle d-inline-flex align-items-center justify-content-center p-0 shadow-sm"
                                style="width: 42px; height: 42px; transition: all 0.2s ease;"
                                :class="{ 'opacity-40 pe-none': !canNext }" @click="next"
                                aria-label="Sertifikat Selanjutnya">
                                <i class="ri-arrow-right-s-line fs-4"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Carousel Track (Responsive Horizontal Scroll Snap dengan lebar persis 3 card di desktop) --}}
                    <div x-ref="track" @scroll.passive="updateControls()"
                        class="d-flex gap-4 pb-3 pt-1 overflow-x-auto simpel-carousel-track"
                        style="scroll-snap-type: x mandatory; scrollbar-width: none; -ms-overflow-style: none; -webkit-overflow-scrolling: touch;">

                        @foreach ($myCertificates as $certCourse)
                            <div class="simpel-cert-slide">
                                <div
                                    class="card h-100 border border-simpel rounded-4 bg-white overflow-hidden shadow-xs simpel-course-card d-flex flex-column">
                                    {{-- Card Thumbnail & Badges (Matching Katalog Kelas) --}}
                                    <div class="position-relative overflow-hidden w-100" style="height: 190px; background-color: var(--simpel-navy, #071a33);">
                                        @if ($certCourse->thumbnail)
                                            <img src="{{ asset('storage/' . $certCourse->thumbnail) }}"
                                                alt="{{ $certCourse->title }}" class="w-100 h-100 object-fit-cover"
                                                style="object-fit: cover; object-position: center; display: block;">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white"
                                                style="background: linear-gradient(135deg, #071a33 0%, #0c3158 100%);">
                                                @if ($certCourse->isBangkom())
                                                    <i class="ri-infinity-line display-4 text-gold"></i>
                                                @elseif ($certCourse->isBangkomExternal())
                                                    <i class="ri-global-line display-4 text-gold"></i>
                                                @else
                                                    <i class="ri-calendar-line display-4 text-gold"></i>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Badges Top-Left --}}
                                        <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                                            @if ($certCourse->isBulanan())
                                                <span class="badge bg-primary text-white fw-bold fs-8 px-2_5 py-1">
                                                    <i class="ri-calendar-line me-1"></i>Bulanan
                                                </span>
                                            @elseif ($certCourse->isBangkom())
                                                <span class="badge bg-success text-white fw-bold fs-8 px-2_5 py-1">
                                                    <i class="ri-infinity-line me-1"></i>Bangkom
                                                </span>
                                            @else
                                                <span
                                                    class="badge bg-warning-subtle text-dark border border-warning fw-bold fs-8 px-2_5 py-1">
                                                    <i class="ri-global-line me-1"></i>Bangkom External
                                                </span>
                                            @endif

                                            @if ($certCourse->category)
                                                <span class="badge bg-navy text-white fw-bold fs-8 px-2_5 py-1">
                                                    {{ $certCourse->category->name }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Badge Bottom-Right: JP Pelatihan --}}
                                        <div class="position-absolute bottom-0 end-0 m-3">
                                            <span class="badge bg-gold text-dark fs-8 fw-bold shadow-sm">
                                                <i class="ri-time-line me-1"></i>{{ $certCourse->jp }} Jam Pelajaran
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Card Body --}}
                                    <div class="p-4 d-flex flex-column flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between text-muted fs-8 mb-2">
                                            <span class="badge bg-light text-navy border border-simpel">
                                                {{ $certCourse->category?->name ?? 'Kelas ASN' }}
                                            </span>
                                            <span class="text-success fw-bold">
                                                <i class="ri-checkbox-circle-line me-1"></i>Lulus &amp; Bersertifikat
                                            </span>
                                        </div>

                                        <h5 class="fw-bold text-navy mb-2 line-clamp-2"
                                            style="font-size: 16px; line-height: 1.4;">
                                            <a href="{{ route('landing.kelas.detail', $certCourse) }}"
                                                class="text-navy text-decoration-none hover-gold">
                                                {{ $certCourse->title }}
                                            </a>
                                        </h5>

                                        <p class="text-muted fs-7 mb-3 flex-grow-1 line-clamp-2">
                                            {{ $certCourse->description ?: ($certCourse->category?->description ?: 'Program kelas kompetensi aparatur yang diselenggarakan oleh BKPSDM Kabupaten Aceh Timur.') }}
                                        </p>

                                        {{-- Footer Action --}}
                                        <div class="pt-3 border-top border-simpel mt-auto">
                                            <div class="d-flex align-items-center justify-content-between mb-2_5">
                                                <span class="text-muted fs-8">
                                                    <i class="ri-calendar-check-line text-success me-1"></i>Tgl. Kelulusan
                                                </span>
                                                <span class="fw-extrabold text-navy fs-7">
                                                    {{ $certCourse->completed_at ? $certCourse->completed_at->locale('id')->translatedFormat('d F Y') : '-' }}
                                                </span>
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                {{-- Tombol Pratinjau --}}
                                                <a href="{{ route('peserta.sertifikat.preview', $certCourse) }}"
                                                    target="_blank"
                                                    class="btn-simpel-cta-gold py-1_5 px-3 fs-8 flex-fill text-center d-inline-flex align-items-center justify-content-center rounded-pill"
                                                    title="Pratinjau Sertifikat di Tab Baru">
                                                    <i class="ri-eye-line me-1"></i>Lihat
                                                </a>

                                                {{-- Dropdown Unduh Sertifikat --}}
                                                <div class="dropdown flex-fill">
                                                    <button
                                                        class="btn-simpel-cta-gold py-1_5 px-3 fs-8 dropdown-toggle w-100 text-center d-inline-flex align-items-center justify-content-center rounded-pill"
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                        title="Unduh Sertifikat Resmi">
                                                        <i class="ri-award-fill me-1"></i>Unduh
                                                    </button>
                                                    <ul
                                                        class="dropdown-menu dropdown-menu-end shadow border-0 fs-8 radius-8 mt-1">
                                                        <li>
                                                            <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                                href="{{ route('peserta.sertifikat.unduh-pdf', $certCourse) }}">
                                                                <i class="ri-file-pdf-fill text-danger fs-6"></i>
                                                                <div>
                                                                    <div class="fw-semibold">Format Dokumen PDF</div>
                                                                    <small class="text-muted fs-9">Standar A4 Landscape Resmi</small>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider my-1">
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item py-2 d-flex align-items-center gap-2"
                                                                href="{{ route('peserta.sertifikat.unduh-gambar', $certCourse) }}">
                                                                <i class="ri-image-fill text-primary fs-6"></i>
                                                                <div>
                                                                    <div class="fw-semibold">Format Gambar PNG</div>
                                                                    <small class="text-muted fs-9">Resolusi Tinggi (High-Res)</small>
                                                                </div>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>

            <style>
                .simpel-carousel-track::-webkit-scrollbar {
                    display: none;
                }

                .simpel-cert-slide {
                    flex: 0 0 100%;
                    max-width: 100%;
                    scroll-snap-align: start;
                }

                @media (min-width: 576px) {
                    .simpel-cert-slide {
                        flex: 0 0 calc((100% - 24px) / 2) !important;
                        max-width: calc((100% - 24px) / 2) !important;
                    }
                }

                @media (min-width: 992px) {
                    .simpel-cert-slide {
                        flex: 0 0 calc((100% - 48px) / 3) !important;
                        max-width: calc((100% - 48px) / 3) !important;
                    }
                }

                .hover-gold:hover {
                    color: var(--simpel-gold, #f3bc42) !important;
                }

                .fs-9 {
                    font-size: 0.72rem !important;
                }

                .px-2_5 {
                    padding-left: 0.65rem !important;
                    padding-right: 0.65rem !important;
                }

                .gap-1_5 {
                    gap: 0.45rem !important;
                }

                .mb-2_5 {
                    margin-bottom: 0.65rem !important;
                }
            </style>
        </section>
    @endif
@endauth

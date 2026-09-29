{{-- Section 1: Hero Banner (SIMPEL Modern Architecture Style) --}}
<section class="simpel-landing-hero position-relative py-5 overflow-hidden text-white" id="beranda"
    style="background: linear-gradient(135deg, #051427 0%, #071a33 50%, #0c3158 100%);">
    {{-- Ambient radial background shape --}}
    <div class="simpel-hero-backdrop"></div>
    <div class="position-absolute bottom-0 end-0 opacity-10 pointer-events-none d-none d-lg-block">
        <img src="{{ asset('landing/assets/images/shapes/banner-one-shape-1.png') }}" alt="" class="img-fluid"
            style="max-width: 420px;">
    </div>

    <div class="container position-relative py-lg-4 py-2" style="z-index: 2;">
        <div class="row align-items-center g-4">
            {{-- Left Column: Main Headline & Architecture Highlight --}}
            <div class="col-lg-7 wow fadeInLeft" data-wow-delay="100ms">
                {{-- Headline --}}
                <h1 class="display-5 fw-extrabold text-white mb-3 lh-sm">
                    Akselerasi Kompetensi <span class="text-gold">Pembelajaran Digital</span> Berkelanjutan
                </h1>

                {{-- Subtitle --}}
                <p class="text-white-80 fs-6 mb-4 pe-lg-4 lh-base">
                    Platform pembelajaran digital terintegrasi <strong>SIMPEL E-Learning</strong> BKPSDM Kabupaten Aceh Timur.
                    Tingkatkan keahlian melalui <strong>Kelas Batch</strong> berjadwal, <strong>Kelas Permanen</strong> mandiri,
                    serta <strong>Kelas Berbayar</strong> intensif dengan kurikulum terstruktur dan evaluasi kompetensi terarah.
                </p>

                {{-- Action CTA Buttons using SIMPEL Button System --}}
                <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                    <a href="#katalog-kelas" class="btn-simpel-cta-gold fs-6" style="padding: 13px 26px; border-radius: 12px;">
                        <span>Jelajahi Kelas</span>
                        <i class="ri-arrow-right-line"></i>
                    </a>
                    <a href="#jenis-kelas" class="btn-simpel-outline-light fs-6" style="padding: 13px 24px; border-radius: 12px;">
                        <i class="ri-layout-grid-line fs-5"></i>
                        <span>Daftar Jenis Kelas</span>
                    </a>
                </div>

                {{-- Key Metric Indicators --}}
                <div class="row g-3 pt-3 border-top border-white-10 text-white">
                    <div class="col-4">
                        <h4 class="mb-0 fw-extrabold text-gold">{{ $totalPublishedCourses > 0 ? $totalPublishedCourses.'+' : '—' }}</h4>
                        <small class="text-white-70 fs-8">Katalog Kelas Terbuka</small>
                    </div>
                    <div class="col-4 border-start border-white-15 ps-3">
                        <h4 class="mb-0 fw-extrabold text-gold">3 Model</h4>
                        <small class="text-white-70 fs-8">Batch, Permanen, Berbayar</small>
                    </div>
                    <div class="col-4 border-start border-white-15 ps-3">
                        <h4 class="mb-0 fw-extrabold text-gold">Online</h4>
                        <small class="text-white-70 fs-8">Akses Belajar Fleksibel</small>
                    </div>
                </div>
            </div>

            {{-- Right Column: 2 Stacked Feature Cards Highlighting Class Architectures --}}
            <div class="col-lg-5 wow fadeInRight" data-wow-delay="200ms">
                <div class="d-flex flex-column gap-3">
                    {{-- Highlight Card 1: Kelas Batch --}}
                    <div class="card border border-white-15 rounded-4 p-3_5 text-white shadow-lg transition-all"
                        style="background: rgba(7, 26, 51, 0.75); backdrop-filter: blur(12px);">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 52px; height: 52px; background: rgba(243, 188, 66, 0.15); border: 1px solid rgba(243, 188, 66, 0.3);">
                                <i class="ri-calendar-event-line text-gold fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge bg-gold text-navy fw-bold fs-8">Kelas Batch</span>
                                    <small class="text-white-60 fs-8"><i class="ri-time-line me-1"></i>Pelatihan Berjadwal</small>
                                </div>
                                <h6 class="fw-bold text-white mb-1">Pelatihan Terjadwal &amp; Kuota Terarah</h6>
                                <p class="text-white-70 fs-8 mb-2 lh-sm">
                                    Program pelatihan berkala dengan periode pendaftaran, kuota peserta terarah, serta evaluasi komprehensif.
                                </p>
                                <a href="{{ route('landing.kelas.batch') }}"
                                    class="text-gold text-decoration-none fs-8 fw-semibold d-inline-flex align-items-center gap-1 hover-gold">
                                    <span>Lihat Jadwal Kelas Batch</span>
                                    <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Highlight Card 2: Kelas Permanen & Berbayar --}}
                    <div class="card border border-white-15 rounded-4 p-3_5 text-white shadow-lg transition-all"
                        style="background: rgba(12, 49, 88, 0.75); backdrop-filter: blur(12px);">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 52px; height: 52px; background: rgba(243, 188, 66, 0.15); border: 1px solid rgba(243, 188, 66, 0.3);">
                                <i class="ri-play-circle-line text-gold fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge bg-white-15 text-white fw-medium fs-8">Permanen &amp; Berbayar</span>
                                    <small class="text-white-60 fs-8"><i class="ri-flashlight-line me-1"></i>Akses Fleksibel</small>
                                </div>
                                <h6 class="fw-bold text-white mb-1">Belajar Mandiri &amp; Program Intensif</h6>
                                <p class="text-white-70 fs-8 mb-2 lh-sm">
                                    Akses materi pembelajaran mandiri kapan saja atau ikuti kelas berbayar untuk pendalaman materi spesialisasi.
                                </p>
                                <a href="{{ route('landing.kelas.permanen') }}"
                                    class="text-gold text-decoration-none fs-8 fw-semibold d-inline-flex align-items-center gap-1 hover-gold">
                                    <span>Jelajahi Kelas Mandiri</span>
                                    <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Mini Status Bar --}}
                    <div
                        class="d-flex align-items-center justify-content-between p-2_5 px-3 rounded-3 bg-white-10 border border-white-10 text-white-80 fs-8">
                        <span class="d-inline-flex align-items-center gap-1_5">
                            <i class="ri-checkbox-circle-fill text-gold fs-6"></i>
                            <span>Akses Modul Materi Interaktif &amp; Evaluasi Pembelajaran</span>
                        </span>
                        <a href="#alur-pendaftaran" class="text-gold text-decoration-none fw-semibold">Pelajari Alur
                            &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

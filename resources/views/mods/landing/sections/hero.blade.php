{{-- Section 1: Hero Banner (Digitalent Grid Style with Master Template Buttons & Anim) --}}
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
            {{-- Left Column: Main DTS Highlight --}}
            <div class="col-lg-7 wow fadeInLeft" data-wow-delay="100ms">
                {{-- Headline --}}
                <h1 class="display-5 fw-extrabold text-white mb-3 lh-sm">
                    Akselerasi Kompetensi <span class="text-gold">Digital ASN</span> Menuju Birokrasi Berkelas Dunia
                </h1>

                {{-- Subtitle --}}
                <p class="text-white-80 fs-6 mb-4 pe-lg-4 lh-base">
                    Platform beasiswa pelatihan digital terintegrasi <strong>SIMPEL E-Learning</strong> untuk aparatur
                    sipil negara. Dilengkapi kurikulum berstandar nasional, mentoring pakar, dan e-sertifikat kompetensi
                    resmi terhubung ke SIASN BKN.
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
                        <small class="text-white-70 fs-8">Tema Pelatihan SPBE</small>
                    </div>
                    <div class="col-4 border-start border-white-15 ps-3">
                        <h4 class="mb-0 fw-extrabold text-gold">{{ $totalApprovedParticipants > 0 ? $totalApprovedParticipants.'+' : '100%' }}</h4>
                        <small class="text-white-70 fs-8">Beasiswa Pemerintah</small>
                    </div>
                    <div class="col-4 border-start border-white-15 ps-3">
                        <h4 class="mb-0 fw-extrabold text-gold">SIASN</h4>
                        <small class="text-white-70 fs-8">Tervalidasi BKN</small>
                    </div>
                </div>
            </div>

            {{-- Right Column: 2 Stacked Feature Cards (Digitalent Style) --}}
            <div class="col-lg-5 wow fadeInRight" data-wow-delay="200ms">
                <div class="d-flex flex-column gap-3">
                    {{-- Highlight Card 1: GTA --}}
                    <div class="card border border-white-15 rounded-4 p-3_5 text-white shadow-lg transition-all"
                        style="background: rgba(7, 26, 51, 0.75); backdrop-filter: blur(12px);">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 52px; height: 52px; background: rgba(243, 188, 66, 0.15); border: 1px solid rgba(243, 188, 66, 0.3);">
                                <i class="ri-government-line text-gold fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge bg-gold text-navy fw-bold fs-8">Unggulan GTA</span>
                                    <small class="text-white-60 fs-8"><i class="ri-time-line me-1"></i>40 JP</small>
                                </div>
                                <h6 class="fw-bold text-white mb-1">Government Transformation Academy</h6>
                                <p class="text-white-70 fs-8 mb-2 lh-sm">Pelatihan arsitektur SPBE, tata kelola data
                                    pemerintahan, dan rekayasa layanan terpadu.</p>
                                <a href="#katalog-kelas"
                                    class="text-gold text-decoration-none fs-8 fw-semibold d-inline-flex align-items-center gap-1 hover-gold">
                                    <span>Lihat Kuota & Silabus</span>
                                    <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Highlight Card 2: CDA --}}
                    <div class="card border border-white-15 rounded-4 p-3_5 text-white shadow-lg transition-all"
                        style="background: rgba(12, 49, 88, 0.75); backdrop-filter: blur(12px);">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width: 52px; height: 52px; background: rgba(243, 188, 66, 0.15); border: 1px solid rgba(243, 188, 66, 0.3);">
                                <i class="ri-shield-keyhole-line text-gold fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge bg-white-15 text-white fw-medium fs-8">Cyber Security</span>
                                    <small class="text-white-60 fs-8"><i class="ri-group-line me-1"></i>Sisa 35
                                        Kursi</small>
                                </div>
                                <h6 class="fw-bold text-white mb-1">Cybersecurity & CSIRT Instansi</h6>
                                <p class="text-white-70 fs-8 mb-2 lh-sm">Pengamanan infrastruktur kritis instansi
                                    pemerintah, ISO 27001, dan mitigasi insiden siber.</p>
                                <a href="#katalog-kelas"
                                    class="text-gold text-decoration-none fs-8 fw-semibold d-inline-flex align-items-center gap-1 hover-gold">
                                    <span>Lihat Kuota & Silabus</span>
                                    <i class="ri-arrow-right-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Mini Status Bar --}}
                    <div
                        class="d-flex align-items-center justify-content-between p-2_5 px-3 rounded-3 bg-white-10 border border-white-10 text-white-80 fs-8">
                        <span class="d-inline-flex align-items-center gap-1_5">
                            <i class="ri-verified-badge-fill text-gold fs-6"></i>
                            <span>Sertifikat Resmi Terakreditasi Nasional</span>
                        </span>
                        <a href="#alur-pendaftaran" class="text-gold text-decoration-none fw-semibold">Pelajari Alur
                            &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

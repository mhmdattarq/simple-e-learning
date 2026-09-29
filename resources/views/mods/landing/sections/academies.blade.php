{{-- Section 2: Daftar Jenis Kelas (3 Card Interaktif dengan Style Penjadwalan) --}}
<section class="py-5 bg-white border-bottom border-simpel" id="jenis-kelas">
    <div class="container py-lg-4 py-2">

        {{-- Section Header --}}
        <div class="row align-items-end justify-content-between mb-4 g-3 wow fadeInUp" data-wow-delay="100ms">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-gold text-navy fw-bold fs-8 px-3 py-1_5">
                        <i class="ri-layout-grid-line me-1"></i>Kategori Program
                    </span>
                </div>
                <h2 class="fw-extrabold text-navy display-6 mb-2">
                    Daftar Jenis <span class="text-gold">Kelas</span>
                </h2>
                <p class="text-muted fs-6 mb-0">
                    Pilih skema kelas yang sesuai dengan kebutuhan pengembangan kompetensi Anda: Kelas Berjadwal (Batch), Pembelajaran Mandiri (Permanen), atau Program Sertifikasi Lanjutan (Berbayar).
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="#katalog-kelas" class="btn-simpel-outline-navy">
                    <i class="ri-book-open-line"></i>
                    <span>Lihat Semua Katalog</span>
                    <i class="ri-arrow-down-line"></i>
                </a>
            </div>
        </div>

        {{-- 3 Jenis Kelas Cards --}}
        <div class="row g-4">
            {{-- Card 1: Kelas Batch --}}
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="150ms">
                <div class="card h-100 border border-simpel rounded-4 bg-white shadow-xs overflow-hidden simpel-academy-card position-relative d-flex flex-column">
                    {{-- Top Color Bar --}}
                    <div class="rounded-top-4"
                        style="height: 4px; background: linear-gradient(90deg, #1e40af 0%, #3b82f6 100%);">
                    </div>

                    <div class="p-4 d-flex flex-column h-100">
                        {{-- Icon & Status Badge --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 44px; height: 44px; background: rgba(30, 64, 175, 0.08); color: #1e40af;">
                                    <i class="ri-calendar-event-line fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-extrabold text-navy" style="font-size: 22px; line-height: 1.1;">
                                        {{ $batchCoursesCount ?? 0 }}
                                        <span class="fs-8 fw-semibold text-muted">Kelas Aktif</span>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">Jadwal Berkala</small>
                                </div>
                            </div>
                            <span class="badge bg-primary text-white rounded-pill fw-bold fs-8">
                                Batch Terjadwal
                            </span>
                        </div>

                        {{-- Tag --}}
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-medium fs-8 mb-2 align-self-start">
                            <i class="ri-calendar-check-line me-1"></i>Periode & Kuota Berkala
                        </span>

                        {{-- Title & Description --}}
                        <h5 class="fw-bold text-navy mb-2 lh-sm" style="font-size: 17px;">
                            Kelas Batch
                        </h5>
                        <p class="text-muted fs-7 mb-3 flex-grow-1">
                            Program kelas kedinasan berjadwal dengan kuota dan periode registrasi berkala untuk aparatur sipil negara.
                        </p>

                        {{-- Highlight Feature --}}
                        <div class="d-flex align-items-center gap-2 text-muted fs-8 mb-4">
                            <i class="ri-checkbox-circle-fill text-success fs-6"></i>
                            <span>Jadwal terstruktur & interaksi bimbingan</span>
                        </div>

                        {{-- Quick Action CTA --}}
                        <div class="pt-3 border-top border-simpel mt-auto">
                            <a href="{{ route('landing.kelas.batch') }}" class="btn-simpel-cta-gold w-100">
                                <span>Jelajahi Kelas Batch</span>
                                <i class="ri-arrow-right-line ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Kelas Permanen --}}
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="250ms">
                <div class="card h-100 border border-simpel rounded-4 bg-white shadow-xs overflow-hidden simpel-academy-card position-relative d-flex flex-column">
                    {{-- Top Color Bar --}}
                    <div class="rounded-top-4"
                        style="height: 4px; background: linear-gradient(90deg, #059669 0%, #10b981 100%);">
                    </div>

                    <div class="p-4 d-flex flex-column h-100">
                        {{-- Icon & Status Badge --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 44px; height: 44px; background: rgba(5, 150, 105, 0.08); color: #059669;">
                                    <i class="ri-infinity-line fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-extrabold text-navy" style="font-size: 22px; line-height: 1.1;">
                                        {{ $permanentCoursesCount ?? 0 }}
                                        <span class="fs-8 fw-semibold text-muted">Kelas Aktif</span>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">Mandiri 24/7</small>
                                </div>
                            </div>
                            <span class="badge bg-success text-white rounded-pill fw-bold fs-8">
                                Self-Paced
                            </span>
                        </div>

                        {{-- Tag --}}
                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-medium fs-8 mb-2 align-self-start">
                            <i class="ri-time-line me-1"></i>Akses Fleksibel 24/7
                        </span>

                        {{-- Title & Description --}}
                        <h5 class="fw-bold text-navy mb-2 lh-sm" style="font-size: 17px;">
                            Kelas Permanen
                        </h5>
                        <p class="text-muted fs-7 mb-3 flex-grow-1">
                            Kelas digital mandiri yang dapat diakses kapan saja dan di mana saja tanpa batasan jadwal atau tanggal berakhir.
                        </p>

                        {{-- Highlight Feature --}}
                        <div class="d-flex align-items-center gap-2 text-muted fs-8 mb-4">
                            <i class="ri-checkbox-circle-fill text-success fs-6"></i>
                            <span>Belajar mandiri sesuai kecepatan sendiri</span>
                        </div>

                        {{-- Quick Action CTA --}}
                        <div class="pt-3 border-top border-simpel mt-auto">
                            <a href="{{ route('landing.kelas.permanen') }}" class="btn-simpel-cta-gold w-100">
                                <span>Jelajahi Kelas Permanen</span>
                                <i class="ri-arrow-right-line ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Kelas Berbayar --}}
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="350ms">
                <div class="card h-100 border border-simpel rounded-4 bg-white shadow-xs overflow-hidden simpel-academy-card position-relative d-flex flex-column">
                    {{-- Top Color Bar --}}
                    <div class="rounded-top-4"
                        style="height: 4px; background: linear-gradient(90deg, #d97706 0%, #f59e0b 100%);">
                    </div>

                    <div class="p-4 d-flex flex-column h-100">
                        {{-- Icon & Status Badge --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                    style="width: 44px; height: 44px; background: rgba(217, 119, 6, 0.08); color: #d97706;">
                                    <i class="ri-money-dollar-circle-line fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-extrabold text-navy" style="font-size: 22px; line-height: 1.1;">
                                        {{ $paidCoursesCount ?? 0 }}
                                        <span class="fs-8 fw-semibold text-muted">Kelas Aktif</span>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">Sertifikasi Resmi</small>
                                </div>
                            </div>
                            <span class="badge bg-warning-subtle text-dark border border-warning rounded-pill fw-bold fs-8">
                                Spesialisasi
                            </span>
                        </div>

                        {{-- Tag --}}
                        <span class="badge bg-warning-subtle text-dark border border-warning fw-medium fs-8 mb-2 align-self-start">
                            <i class="ri-award-line me-1"></i>Sertifikasi Profesi
                        </span>

                        {{-- Title & Description --}}
                        <h5 class="fw-bold text-navy mb-2 lh-sm" style="font-size: 17px;">
                            Kelas Berbayar
                        </h5>
                        <p class="text-muted fs-7 mb-3 flex-grow-1">
                            Program sertifikasi keahlian khusus dan kelas profesi lanjutan yang diselenggarakan bersama mitra terakreditasi resmi.
                        </p>

                        {{-- Highlight Feature --}}
                        <div class="d-flex align-items-center gap-2 text-muted fs-8 mb-4">
                            <i class="ri-checkbox-circle-fill text-success fs-6"></i>
                            <span>Kurikulum standar nasional & sertifikat resmi</span>
                        </div>

                        {{-- Quick Action CTA --}}
                        <div class="pt-3 border-top border-simpel mt-auto">
                            <a href="{{ route('landing.kelas.berbayar') }}" class="btn-simpel-cta-gold w-100">
                                <span>Jelajahi Kelas Berbayar</span>
                                <i class="ri-arrow-right-line ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

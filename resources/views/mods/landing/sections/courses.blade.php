{{-- Section 3: Katalog Pelatihan Terbuka (Digitalent Course Catalog with Template Cards & Buttons) --}}
<section class="py-5 bg-light border-bottom border-simpel" id="pelatihan">
    <div class="container py-lg-4 py-2">
        {{-- Section Header & Filter Pills --}}
        <div class="row align-items-end justify-content-between mb-4 g-3 wow fadeInUp" data-wow-delay="100ms">
            <div class="col-lg-6">
                <h2 class="fw-extrabold text-navy display-6 mb-2">
                    Katalog Pelatihan Digital Terbuka
                </h2>
                <p class="text-muted fs-6 mb-0">
                    Pilih program pelatihan beasiswa kompetensi aparatur. Tersedia program mandiri tanpa batas waktu, pelatihan berkala (batch), dan penugasan khusus.
                </p>
            </div>

            {{-- Category Filter Navigation (Aligned with PM 3 Course Types) --}}
            <div class="col-lg-6 text-lg-end">
                <div class="d-inline-flex flex-wrap gap-1 p-1 bg-white border border-simpel rounded-pill shadow-xs">
                    <button type="button"
                        class="btn btn-sm rounded-pill px-3 fw-bold bg-navy text-white">Semua Program</button>
                    <button type="button"
                        class="btn btn-sm rounded-pill px-3 fw-medium text-secondary hover-navy">Pelatihan Mandiri</button>
                    <button type="button"
                        class="btn btn-sm rounded-pill px-3 fw-medium text-secondary hover-navy">Batch Berkala</button>
                    <button type="button"
                        class="btn btn-sm rounded-pill px-3 fw-medium text-secondary hover-navy">Penugasan Khusus</button>
                </div>
            </div>
        </div>

        {{-- Courses Grid --}}
        <div class="row g-4">
            @if (isset($courses) && $courses->isNotEmpty())
                @foreach ($courses as $course)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="150ms">
                        <div class="card h-100 border border-simpel rounded-4 bg-white overflow-hidden shadow-xs simpel-course-card">
                            <div class="position-relative overflow-hidden" style="height: 190px;">
                                @if ($course->thumbnail)
                                    <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}"
                                        class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white"
                                        style="background: linear-gradient(135deg, #071a33 0%, #0c3158 100%);">
                                        <i class="ri-book-open-line display-4 text-gold"></i>
                                    </div>
                                @endif
                                <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                                    <span class="badge bg-navy text-white fw-bold fs-8 px-2_5 py-1">{{ $course->category?->name ?? 'Diklat ASN' }}</span>
                                    <span class="badge bg-gold text-navy fw-bold fs-8 px-2_5 py-1">
                                        {{ $course->isPermanent() ? 'Buka Selamanya' : 'Batch Terjadwal' }}
                                    </span>
                                </div>
                                <div class="position-absolute bottom-0 end-0 m-3">
                                    <span class="badge bg-dark bg-opacity-75 text-white fs-8">
                                        <i class="ri-user-line me-1 text-gold"></i>Kuota: {{ $course->quota }} ASN
                                    </span>
                                </div>
                            </div>

                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between text-muted fs-8 mb-2">
                                    <span class="badge bg-light text-navy border border-simpel font-monospace">{{ $course->code }}</span>
                                    <span class="text-success fw-bold"><i class="ri-checkbox-circle-line me-1"></i>Pendaftaran Terbuka</span>
                                </div>
                                <h5 class="fw-bold text-navy mb-2 line-clamp-2">
                                    {{ $course->title }}
                                </h5>
                                <p class="text-muted fs-7 mb-4 flex-grow-1 line-clamp-2">
                                    {{ $course->description ?: 'Program pelatihan kompetensi aparatur yang diselenggarakan oleh BKPSDM Kabupaten Aceh Timur.' }}
                                </p>

                                <div class="pt-3 border-top border-simpel d-flex align-items-center justify-content-between mt-auto">
                                    <div>
                                        <small class="text-muted d-block fs-8">Metode Program</small>
                                        <span class="fw-extrabold text-navy fs-7 text-uppercase">{{ $course->method }}</span>
                                    </div>
                                    <a href="{{ route('pelatihan.daftar', $course->id) }}" class="thm-btn py-2 px-3 fs-7"
                                        style="background-color: var(--simpel-gold); color: var(--simpel-navy); border-radius: 10px; font-weight: 700;">
                                        Daftar Pelatihan <i class="ri-arrow-right-line ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{-- Course 1: Pelatihan Mandiri (Buka Terus) --}}
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="150ms">
                    <div class="card h-100 border border-simpel rounded-4 bg-white overflow-hidden shadow-xs simpel-course-card">
                        {{-- Thumbnail --}}
                        <div class="position-relative overflow-hidden" style="height: 190px;">
                        <img src="{{ asset('landing/assets/images/courses/courses-1-1.jpg') }}" alt=""
                            class="w-100 h-100 object-fit-cover">
                        <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                            <span class="badge bg-navy text-white fw-bold fs-8 px-2_5 py-1">Mandiri</span>
                            <span class="badge bg-gold text-navy fw-bold fs-8 px-2_5 py-1">Buka Selamanya</span>
                        </div>
                        <div class="position-absolute bottom-0 end-0 m-3">
                            <span class="badge bg-dark bg-opacity-75 text-white fs-8"><i
                                    class="ri-time-line me-1 text-gold"></i>32 JP</span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between text-muted fs-8 mb-2">
                            <span><i class="ri-refresh-line me-1 text-gold"></i>Akses Fleksibel 24/7</span>
                            <span class="text-success fw-bold"><i class="ri-checkbox-circle-line me-1"></i>Pendaftaran Terbuka</span>
                        </div>
                        <h5 class="fw-bold text-navy mb-2 line-clamp-2">
                            Manajemen Kinerja & SKP ASN BerAKHLAK
                        </h5>
                        <p class="text-muted fs-7 mb-4 flex-grow-1 line-clamp-2">
                            Panduan implementasi penyusunan rencana aksi, dialog kinerja, dan evaluasi periodik kinerja aparatur sesuai regulasi terbaru.
                        </p>

                        <div
                            class="pt-3 border-top border-simpel d-flex align-items-center justify-content-between mt-auto">
                            <div>
                                <small class="text-muted d-block fs-8">Biaya Program</small>
                                <span class="fw-extrabold text-navy fs-6">100% BEASISWA</span>
                            </div>
                            <a href="#alur-pendaftaran" class="thm-btn py-2 px-3 fs-7"
                                style="background-color: var(--simpel-gold); color: var(--simpel-navy); border-radius: 10px; font-weight: 700;">
                                Mulai Belajar <i class="ri-arrow-right-line ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Course 2: Pelatihan Berkala (Batch Terjadwal) --}}
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                <div
                    class="card h-100 border border-simpel rounded-4 bg-white overflow-hidden shadow-xs simpel-course-card">
                    {{-- Thumbnail --}}
                    <div class="position-relative overflow-hidden" style="height: 190px;">
                        <img src="{{ asset('landing/assets/images/courses/courses-1-2.jpg') }}" alt=""
                            class="w-100 h-100 object-fit-cover">
                        <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                            <span class="badge bg-navy text-white fw-bold fs-8 px-2_5 py-1">Batch 2</span>
                            <span class="badge bg-gold text-navy fw-bold fs-8 px-2_5 py-1">Daring Terjadwal</span>
                        </div>
                        <div class="position-absolute bottom-0 end-0 m-3">
                            <span class="badge bg-dark bg-opacity-75 text-white fs-8"><i
                                    class="ri-time-line me-1 text-gold"></i>40 JP</span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between text-muted fs-8 mb-2">
                            <span><i class="ri-calendar-line me-1 text-gold"></i>10 - 24 Okt 2026</span>
                            <span class="text-success fw-bold"><i class="ri-user-line me-1"></i>Sisa 24 Kuota</span>
                        </div>
                        <h5 class="fw-bold text-navy mb-2 line-clamp-2">
                            Arsitektur & Peta Rencana SPBE Instansi Pemerintah
                        </h5>
                        <p class="text-muted fs-7 mb-4 flex-grow-1 line-clamp-2">
                            Penyusunan arsitektur proses bisnis, data, layanan, dan infrastruktur sistem informasi terpadu instansi pemerintah daerah.
                        </p>

                        <div
                            class="pt-3 border-top border-simpel d-flex align-items-center justify-content-between mt-auto">
                            <div>
                                <small class="text-muted d-block fs-8">Biaya Program</small>
                                <span class="fw-extrabold text-navy fs-6">100% BEASISWA</span>
                            </div>
                            <a href="#alur-pendaftaran" class="thm-btn py-2 px-3 fs-7"
                                style="background-color: var(--simpel-gold); color: var(--simpel-navy); border-radius: 10px; font-weight: 700;">
                                Daftar Batch <i class="ri-arrow-right-line ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Course 3: Pelatihan Penugasan Khusus (Bersyarat) --}}
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="250ms">
                <div
                    class="card h-100 border border-simpel rounded-4 bg-white overflow-hidden shadow-xs simpel-course-card">
                    {{-- Thumbnail --}}
                    <div class="position-relative overflow-hidden" style="height: 190px;">
                        <img src="{{ asset('landing/assets/images/courses/courses-1-3.jpg') }}" alt=""
                            class="w-100 h-100 object-fit-cover">
                        <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                            <span class="badge bg-navy text-white fw-bold fs-8 px-2_5 py-1">Penugasan</span>
                            <span class="badge bg-gold text-navy fw-bold fs-8 px-2_5 py-1">Khusus Pejabat</span>
                        </div>
                        <div class="position-absolute bottom-0 end-0 m-3">
                            <span class="badge bg-dark bg-opacity-75 text-white fs-8"><i
                                    class="ri-time-line me-1 text-gold"></i>48 JP</span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between text-muted fs-8 mb-2">
                            <span><i class="ri-shield-user-line me-1 text-gold"></i>Rekomendasi OPD</span>
                            <span class="text-primary fw-bold"><i class="ri-award-line me-1"></i>Seleksi Khusus</span>
                        </div>
                        <h5 class="fw-bold text-navy mb-2 line-clamp-2">
                            Transformasi Kepemimpinan Digital ASN
                        </h5>
                        <p class="text-muted fs-7 mb-4 flex-grow-1 line-clamp-2">
                            Akselerasi kepemimpinan adaptif, manajemen inovasi, dan mitigasi resistensi perubahan bagi pejabat administrator & pengawas daerah.
                        </p>

                        <div
                            class="pt-3 border-top border-simpel d-flex align-items-center justify-content-between mt-auto">
                            <div>
                                <small class="text-muted d-block fs-8">Biaya Program</small>
                                <span class="fw-extrabold text-navy fs-6">100% BEASISWA</span>
                            </div>
                            <a href="#alur-pendaftaran" class="thm-btn py-2 px-3 fs-7"
                                style="background-color: var(--simpel-gold); color: var(--simpel-navy); border-radius: 10px; font-weight: 700;">
                                Lihat Syarat <i class="ri-arrow-right-line ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Course 4: Pelatihan Mandiri (Buka Terus) --}}
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="300ms">
                <div
                    class="card h-100 border border-simpel rounded-4 bg-white overflow-hidden shadow-xs simpel-course-card">
                    {{-- Thumbnail --}}
                    <div class="position-relative overflow-hidden" style="height: 190px;">
                        <img src="{{ asset('landing/assets/images/courses/courses-1-4.jpg') }}" alt=""
                            class="w-100 h-100 object-fit-cover">
                        <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                            <span class="badge bg-navy text-white fw-bold fs-8 px-2_5 py-1">Mandiri</span>
                            <span class="badge bg-gold text-navy fw-bold fs-8 px-2_5 py-1">Buka Selamanya</span>
                        </div>
                        <div class="position-absolute bottom-0 end-0 m-3">
                            <span class="badge bg-dark bg-opacity-75 text-white fs-8"><i
                                    class="ri-time-line me-1 text-gold"></i>24 JP</span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between text-muted fs-8 mb-2">
                            <span><i class="ri-refresh-line me-1 text-gold"></i>Akses Fleksibel 24/7</span>
                            <span class="text-success fw-bold"><i class="ri-checkbox-circle-line me-1"></i>Pendaftaran Terbuka</span>
                        </div>
                        <h5 class="fw-bold text-navy mb-2 line-clamp-2">
                            Pelayanan Publik Digital & Standar Pelayanan Terpadu
                        </h5>
                        <p class="text-muted fs-7 mb-4 flex-grow-1 line-clamp-2">
                            Optimalisasi kepuasan masyarakat melalui kanal digital instansi yang transparan, mudah diakses, dan bebas hambatan birokrasi.
                        </p>

                        <div
                            class="pt-3 border-top border-simpel d-flex align-items-center justify-content-between mt-auto">
                            <div>
                                <small class="text-muted d-block fs-8">Biaya Program</small>
                                <span class="fw-extrabold text-navy fs-6">100% BEASISWA</span>
                            </div>
                            <a href="#alur-pendaftaran" class="thm-btn py-2 px-3 fs-7"
                                style="background-color: var(--simpel-gold); color: var(--simpel-navy); border-radius: 10px; font-weight: 700;">
                                Mulai Belajar <i class="ri-arrow-right-line ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Course 5: Pelatihan Berkala (Batch Terjadwal) --}}
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="350ms">
                <div
                    class="card h-100 border border-simpel rounded-4 bg-white overflow-hidden shadow-xs simpel-course-card">
                    {{-- Thumbnail --}}
                    <div class="position-relative overflow-hidden" style="height: 190px;">
                        <img src="{{ asset('landing/assets/images/courses/courses-1-5.jpg') }}" alt=""
                            class="w-100 h-100 object-fit-cover">
                        <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                            <span class="badge bg-navy text-white fw-bold fs-8 px-2_5 py-1">Batch 1</span>
                            <span class="badge bg-gold text-navy fw-bold fs-8 px-2_5 py-1">Daring Terjadwal</span>
                        </div>
                        <div class="position-absolute bottom-0 end-0 m-3">
                            <span class="badge bg-dark bg-opacity-75 text-white fs-8"><i
                                    class="ri-time-line me-1 text-gold"></i>50 JP</span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between text-muted fs-8 mb-2">
                            <span><i class="ri-calendar-line me-1 text-gold"></i>15 - 30 Nov 2026</span>
                            <span class="text-success fw-bold"><i class="ri-user-line me-1"></i>Sisa 18 Kuota</span>
                        </div>
                        <h5 class="fw-bold text-navy mb-2 line-clamp-2">
                            Keamanan Informasi & Tanggap Insiden CSIRT
                        </h5>
                        <p class="text-muted fs-7 mb-4 flex-grow-1 line-clamp-2">
                            Praktik mitigasi risiko siber, kepatuhan ISO 27001, perlindungan data pribadi publik, dan protokol insiden darurat siber.
                        </p>

                        <div
                            class="pt-3 border-top border-simpel d-flex align-items-center justify-content-between mt-auto">
                            <div>
                                <small class="text-muted d-block fs-8">Biaya Program</small>
                                <span class="fw-extrabold text-navy fs-6">100% BEASISWA</span>
                            </div>
                            <a href="#alur-pendaftaran" class="thm-btn py-2 px-3 fs-7"
                                style="background-color: var(--simpel-gold); color: var(--simpel-navy); border-radius: 10px; font-weight: 700;">
                                Daftar Batch <i class="ri-arrow-right-line ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Course 6: Pelatihan Berkala (Batch Terjadwal) --}}
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="400ms">
                <div
                    class="card h-100 border border-simpel rounded-4 bg-white overflow-hidden shadow-xs simpel-course-card">
                    {{-- Thumbnail --}}
                    <div class="position-relative overflow-hidden" style="height: 190px;">
                        <img src="{{ asset('landing/assets/images/courses/courses-1-6.jpg') }}" alt=""
                            class="w-100 h-100 object-fit-cover">
                        <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                            <span class="badge bg-navy text-white fw-bold fs-8 px-2_5 py-1">Batch 3</span>
                            <span class="badge bg-gold text-navy fw-bold fs-8 px-2_5 py-1">Daring Terjadwal</span>
                        </div>
                        <div class="position-absolute bottom-0 end-0 m-3">
                            <span class="badge bg-dark bg-opacity-75 text-white fs-8"><i
                                    class="ri-time-line me-1 text-gold"></i>40 JP</span>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-4 d-flex flex-column flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between text-muted fs-8 mb-2">
                            <span><i class="ri-calendar-line me-1 text-gold"></i>01 - 15 Des 2026</span>
                            <span class="text-success fw-bold"><i class="ri-user-line me-1"></i>Sisa 30 Kuota</span>
                        </div>
                        <h5 class="fw-bold text-navy mb-2 line-clamp-2">
                            Analisis Data & AI Sektor Publik Daerah
                        </h5>
                        <p class="text-muted fs-7 mb-4 flex-grow-1 line-clamp-2">
                            Eksplorasi teknik analisis data, dashboard intelijen bisnis birokrasi, dan integrasi portal Satu Data Indonesia.
                        </p>

                        <div
                            class="pt-3 border-top border-simpel d-flex align-items-center justify-content-between mt-auto">
                            <div>
                                <small class="text-muted d-block fs-8">Biaya Program</small>
                                <span class="fw-extrabold text-navy fs-6">100% BEASISWA</span>
                            </div>
                            <a href="#alur-pendaftaran" class="thm-btn py-2 px-3 fs-7"
                                style="background-color: var(--simpel-gold); color: var(--simpel-navy); border-radius: 10px; font-weight: 700;">
                                Daftar Batch <i class="ri-arrow-right-line ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

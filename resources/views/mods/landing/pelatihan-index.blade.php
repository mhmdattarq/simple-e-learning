<div>
    {{-- Page Header --}}
    <section class="py-5 text-white"
        style="background: linear-gradient(135deg, #051427 0%, #071a33 50%, #0c3158 100%);">
        <div class="container py-lg-3 py-2">
            <div class="row align-items-center">
                <div class="col-lg-8 wow fadeInLeft" data-wow-delay="100ms">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-gold text-navy fw-bold fs-8 px-3 py-1_5">
                            <i class="ri-book-open-line me-1"></i>Program Diklat ASN
                        </span>
                    </div>
                    <h1 class="display-6 fw-extrabold text-white mb-3">
                        Katalog Pelatihan <span class="text-gold">ASN</span>
                    </h1>
                    <p class="text-white-80 fs-6 mb-0 pe-lg-4">
                        Daftar lengkap program pengembangan kompetensi dan pelatihan digital aparatur sipil negara BKPSDM Kabupaten Aceh Timur. Pilih pelatihan yang Anda minati dan tingkatkan kompetensi jabatan Anda.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="{{ route('landing') }}" class="btn-simpel-outline-light">
                        <i class="ri-arrow-left-line"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Courses Catalog List --}}
    <section class="py-5 bg-light">
        <div class="container py-lg-4 py-2">
            @if ($courses->isNotEmpty())
                <div class="row g-4">
                    @foreach ($courses as $course)
                        <div class="col-12 wow fadeInUp" data-wow-delay="{{ ($loop->index * 50) + 100 }}ms">
                            <div class="card border border-simpel rounded-4 bg-white shadow-xs overflow-hidden">
                                <div class="card-body p-4">
                                    <div class="row align-items-center g-3">

                                        {{-- Left: Visual / Icon Box --}}
                                        <div class="col-auto">
                                            @if ($course->thumbnail)
                                                <div class="rounded-3 overflow-hidden flex-shrink-0"
                                                    style="width: 80px; height: 80px;">
                                                    <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}"
                                                        class="w-100 h-100 object-fit-cover">
                                                </div>
                                            @else
                                                <div class="text-center rounded-3 p-3 flex-shrink-0 d-flex align-items-center justify-content-center"
                                                    style="background: rgba(7, 26, 51, 0.06); width: 80px; height: 80px;">
                                                    <i class="ri-book-open-line text-navy" style="font-size: 34px;"></i>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Middle: Info --}}
                                        <div class="col">
                                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                                <span class="badge bg-navy text-white fw-bold fs-8 px-2_5 py-1">
                                                    {{ $course->category?->name ?? 'Diklat ASN' }}
                                                </span>
                                                <span class="badge bg-gold text-navy fw-bold fs-8 px-2_5 py-1">
                                                    {{ $course->isPermanent() ? 'Mandiri 24/7' : 'Batch Terjadwal' }}
                                                </span>
                                            </div>
                                            <h5 class="fw-bold text-navy mb-1">{{ $course->title }}</h5>
                                            <p class="text-muted fs-8 mb-2 line-clamp-2">
                                                {{ $course->category?->description ?: 'Program pelatihan kompetensi aparatur yang diselenggarakan oleh BKPSDM Kabupaten Aceh Timur.' }}
                                            </p>
                                            <div class="d-flex flex-wrap align-items-center gap-3 text-muted fs-8">
                                                <span class="d-flex align-items-center gap-1">
                                                    <i class="ri-price-tag-3-line text-gold"></i>
                                                    Biaya: <strong class="text-navy">{{ $course->isPaid() ? 'Rp ' . number_format($course->price, 0, ',', '.') : 'Gratis' }}</strong>
                                                </span>
                                                <span class="d-flex align-items-center gap-1">
                                                    <i class="ri-award-line text-gold"></i>
                                                    <strong class="text-success">{{ $course->isPaid() ? 'Bersertifikat Resmi' : '100% Beasiswa Pemerintah' }}</strong>
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Right: CTA Button --}}
                                        <div class="col-auto">
                                            <a href="{{ route('peserta.materi', $course->id) }}"
                                                class="btn btn-success fw-bold text-white radius-10 px-3 py-2 fs-7 d-inline-flex align-items-center gap-2 shadow-sm">
                                                <i class="ri-book-open-line"></i>
                                                <span>Akses Materi</span>
                                                <i class="ri-arrow-right-line"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Info note --}}
                <div
                    class="mt-4 p-3 rounded-3 bg-white border border-simpel d-flex align-items-center gap-2 text-muted fs-8 wow fadeInUp">
                    <i class="ri-information-line text-gold fs-5 flex-shrink-0"></i>
                    <span>Setiap aparatur sipil negara berhak mengikuti program pelatihan pengembangan kompetensi. Pastikan Anda telah
                        <a href="{{ route('register') }}"
                            class="text-navy fw-semibold text-decoration-none hover-gold">membuat akun</a>
                        untuk melakukan pendaftaran.</span>
                </div>

            @else
                {{-- Empty State --}}
                <div class="text-center py-5 wow fadeInUp">
                    <div class="mb-4">
                        <i class="ri-book-open-line text-gold" style="font-size: 72px; opacity: 0.4;"></i>
                    </div>
                    <h4 class="fw-bold text-navy mb-2">Belum Ada Program Pelatihan</h4>
                    <p class="text-muted fs-6 mb-4 max-w-500 mx-auto">
                        Program pelatihan sedang disiapkan oleh tim BKPSDM. Pantau terus halaman ini untuk pembaruan katalog pelatihan aparatur.
                    </p>
                    <a href="{{ route('landing') }}"
                        class="btn-simpel-cta-gold">
                        <i class="ri-arrow-left-line"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            @endif
        </div>
    </section>
</div>

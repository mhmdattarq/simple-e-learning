<div>
    {{-- Page Header --}}
    <section class="py-5 text-white"
        style="background: linear-gradient(135deg, #051427 0%, #071a33 50%, #0c3158 100%);">
        <div class="container py-lg-3 py-2">
            <div class="row align-items-center">
                <div class="col-lg-8 wow fadeInLeft" data-wow-delay="100ms">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-gold text-navy fw-bold fs-8 px-3 py-1_5">
                            <i class="ri-calendar-event-line me-1"></i>Agenda Resmi BKPSDM
                        </span>
                    </div>
                    <h1 class="display-6 fw-extrabold text-white mb-3">
                        Jadwal Pelatihan <span class="text-gold">ASN</span>
                    </h1>
                    <p class="text-white-80 fs-6 mb-0 pe-lg-4">
                        Daftar program pelatihan batch yang sedang dan akan diselenggarakan oleh BKPSDM Kabupaten Aceh
                        Timur. Pilih pelatihan yang sesuai dan daftarkan diri Anda sebelum kuota habis.
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

    {{-- Schedule Cards --}}
    <section class="py-5 bg-light">
        <div class="container py-lg-4 py-2">
            @if ($jadwals->isNotEmpty())
                <div class="row g-4">
                    @foreach ($jadwals as $jadwal)
                        <div class="col-12 wow fadeInUp" data-wow-delay="{{ ($loop->index * 50) + 100 }}ms">
                            <div class="card border border-simpel rounded-4 bg-white shadow-xs overflow-hidden">
                                <div class="card-body p-4">
                                    <div class="row align-items-center g-3">

                                        {{-- Left: Date Badge --}}
                                        <div class="col-auto">
                                            <div class="text-center rounded-3 p-3 flex-shrink-0"
                                                style="background: rgba(7, 26, 51, 0.06); min-width: 80px;">
                                                <div class="fw-extrabold text-navy"
                                                    style="font-size: 28px; line-height: 1;">
                                                    {{ $jadwal->start_date->format('d') }}
                                                </div>
                                                <div class="fw-bold text-gold fs-8 text-uppercase">
                                                    {{ $jadwal->start_date->translatedFormat('M Y') }}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Middle: Info --}}
                                        <div class="col">
                                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                                <span class="badge bg-navy text-white fw-bold fs-8 px-2_5 py-1">
                                                    {{ $jadwal->category?->name ?? 'Diklat ASN' }}
                                                </span>
                                                <span class="badge bg-gold text-navy fw-bold fs-8 px-2_5 py-1">
                                                    Batch Terjadwal
                                                </span>
                                            </div>
                                            <h5 class="fw-bold text-navy mb-1">{{ $jadwal->title }}</h5>
                                            <div
                                                class="d-flex flex-wrap align-items-center gap-3 text-muted fs-8 mt-2">
                                                <span class="d-flex align-items-center gap-1">
                                                    <i class="ri-calendar-line text-gold"></i>
                                                    {{ $jadwal->start_date->translatedFormat('d F Y') }}
                                                    @if ($jadwal->end_date)
                                                        &nbsp;&ndash;&nbsp;
                                                        {{ $jadwal->end_date->translatedFormat('d F Y') }}
                                                    @endif
                                                </span>
                                                <span class="d-flex align-items-center gap-1">
                                                    <i class="ri-price-tag-3-line text-gold"></i>
                                                    {{ $jadwal->isPaid() ? 'Rp ' . number_format($jadwal->price, 0, ',', '.') : 'Gratis' }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Right: CTA Button --}}
                                        <div class="col-auto">
                                            <a href="{{ route('pelatihan.index') }}"
                                                class="btn-simpel-cta-gold">
                                                <span>Lihat Kelas</span>
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
                    <span>Pendaftaran memerlukan akun peserta. Pastikan Anda sudah
                        <a href="{{ route('register') }}"
                            class="text-navy fw-semibold text-decoration-none hover-gold">membuat akun</a>
                        sebelum mendaftar.</span>
                </div>

            @else
                {{-- Empty State --}}
                <div class="text-center py-5 wow fadeInUp">
                    <div class="mb-4">
                        <i class="ri-calendar-2-line text-gold" style="font-size: 72px; opacity: 0.4;"></i>
                    </div>
                    <h4 class="fw-bold text-navy mb-2">Belum Ada Jadwal Pelatihan</h4>
                    <p class="text-muted fs-6 mb-4 max-w-500 mx-auto">
                        Jadwal batch pelatihan akan segera diumumkan. Pantau terus halaman ini atau
                        kunjungi katalog kelas yang bisa diakses kapan saja.
                    </p>
                    <a href="{{ route('landing') }}#katalog-kelas"
                        class="btn-simpel-cta-gold">
                        <i class="ri-book-open-line"></i>
                        <span>Lihat Katalog Kelas</span>
                    </a>
                </div>
            @endif
        </div>
    </section>
</div>

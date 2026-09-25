{{-- Section 2: Quick Info Jadwal Pelatihan Terbaru (maks 3 card) --}}
<section class="py-5 bg-white border-bottom border-simpel" id="akademi">
    <div class="container py-lg-4 py-2">

        {{-- Section Header --}}
        <div class="row align-items-end justify-content-between mb-4 g-3 wow fadeInUp" data-wow-delay="100ms">
            <div class="col-lg-7">
                <h2 class="fw-extrabold text-navy display-6 mb-2">
                    Jadwal Pelatihan <span class="text-gold">Terdekat</span>
                </h2>
                <p class="text-muted fs-6 mb-0">
                    Program pelatihan batch yang segera diselenggarakan. Daftar sekarang sebelum kuota habis.
                </p>
            </div>
            <div class="col-lg-5 text-lg-end">
                <a href="{{ route('jadwal') }}" class="btn-simpel-outline-navy">
                    <i class="ri-calendar-2-line"></i>
                    <span>Lihat Semua Jadwal</span>
                    <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>

        @if ($upcomingJadwals->isNotEmpty())
            {{-- Jadwal Cards --}}
            <div class="row g-4">
                @foreach ($upcomingJadwals as $jadwal)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="{{ $loop->index * 100 + 150 }}ms">
                        <div
                            class="card h-100 border border-simpel rounded-4 bg-white shadow-xs overflow-hidden simpel-academy-card position-relative">

                            {{-- Top color bar --}}
                            <div class="rounded-top-4"
                                style="height: 4px; background: linear-gradient(90deg, var(--simpel-navy) 0%, var(--simpel-gold) 100%);">
                            </div>

                            <div class="p-4 d-flex flex-column h-100">
                                {{-- Date Badge --}}
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0"
                                            style="width: 40px; height: 40px; background: rgba(7,26,51,0.07);">
                                            <i class="ri-calendar-event-line text-navy fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-extrabold text-navy"
                                                style="font-size: 20px; line-height: 1.1;">
                                                {{ $jadwal->start_date->format('d') }}
                                            </div>
                                            <div class="fw-bold text-gold fs-8 text-uppercase">
                                                {{ $jadwal->start_date->translatedFormat('M Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    <span class="badge bg-gold text-navy rounded-pill fw-bold fs-8">
                                        Batch Terjadwal
                                    </span>
                                </div>

                                {{-- Category --}}
                                <span class="badge bg-navy-soft text-navy fw-medium fs-8 mb-2 align-self-start"
                                    style="background: rgba(7,26,51,0.07); color: var(--simpel-navy);">
                                    {{ $jadwal->category?->name ?? 'Diklat ASN' }}
                                </span>

                                {{-- Title --}}
                                <h5 class="fw-bold text-navy mb-3 flex-grow-1 lh-sm" style="font-size: 15px;">
                                    {{ $jadwal->title }}
                                </h5>

                                {{-- Period --}}
                                <div class="d-flex align-items-center gap-1 text-muted fs-8 mb-4">
                                    <i class="ri-time-line text-gold"></i>
                                    <span>
                                        {{ $jadwal->start_date->translatedFormat('d M Y') }}
                                        @if ($jadwal->end_date)
                                            &ndash; {{ $jadwal->end_date->translatedFormat('d M Y') }}
                                        @endif
                                    </span>
                                </div>

                                {{-- CTA --}}
                                <div class="pt-3 border-top border-simpel mt-auto">
                                    <a href="{{ route('pelatihan.daftar', $jadwal->id) }}"
                                        class="btn-simpel-cta-gold w-100">
                                        <span>Daftar Pelatihan Ini</span>
                                        <i class="ri-arrow-right-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-5 wow fadeInUp">
                <i class="ri-calendar-2-line text-gold mb-3 d-block" style="font-size: 52px; opacity: 0.35;"></i>
                <h5 class="fw-bold text-navy mb-2">Belum Ada Jadwal Pelatihan</h5>
                <p class="text-muted fs-7 mb-3">
                    Jadwal batch akan segera diumumkan. Cek katalog pelatihan mandiri yang buka 24/7.
                </p>
                <a href="#pelatihan" class="text-navy fw-semibold text-decoration-none fs-7 hover-gold">
                    Lihat Katalog Pelatihan &rarr;
                </a>
            </div>
        @endif

    </div>
</section>

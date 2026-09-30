{{-- Section 3: Katalog Kelas (Maksimal 6 Kelas Aktif Terbuka dengan Filter Jenis Kelas) --}}
<section class="py-5 bg-light border-bottom border-simpel" id="katalog-kelas">
    <div class="container py-lg-4 py-2">

        {{-- Section Header & Filter Pills --}}
        <div class="row align-items-end justify-content-between mb-4 g-3 wow fadeInUp" data-wow-delay="100ms">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-gold text-navy fw-bold fs-8 px-3 py-1_5">
                        <i class="ri-book-open-line me-1"></i>Program Unggulan
                    </span>
                </div>
                <h2 class="fw-extrabold text-navy display-6 mb-2">
                    Daftar Katalog <span class="text-gold">Kelas</span>
                </h2>
                <p class="text-muted fs-6 mb-0">
                    Pilih dan ikuti kelas aktif dari berbagai bidang kompetensi aparatur yang diselenggarakan BKPSDM
                    Kabupaten Aceh Timur.
                </p>
            </div>

            {{-- Filter Pills Jenis Kelas --}}
            <div class="col-lg-6 text-lg-end">
                <div class="d-inline-flex flex-wrap gap-1 p-1 bg-white border border-simpel rounded-pill shadow-xs">
                    <button type="button"
                        class="btn btn-sm rounded-pill px-3 fw-medium {{ ($selectedType ?? 'all') === 'all' ? 'bg-navy text-white fw-bold' : 'text-secondary hover-navy' }}"
                        wire:click="filterType('all')">
                        Semua Kelas
                    </button>
                    <button type="button"
                        class="btn btn-sm rounded-pill px-3 fw-medium {{ ($selectedType ?? 'all') === 'batch' ? 'bg-navy text-white fw-bold' : 'text-secondary hover-navy' }}"
                        wire:click="filterType('batch')">
                        Kelas Batch
                    </button>
                    <button type="button"
                        class="btn btn-sm rounded-pill px-3 fw-medium {{ ($selectedType ?? 'all') === 'permanent' ? 'bg-navy text-white fw-bold' : 'text-secondary hover-navy' }}"
                        wire:click="filterType('permanent')">
                        Kelas Permanen
                    </button>
                    <button type="button"
                        class="btn btn-sm rounded-pill px-3 fw-medium {{ ($selectedType ?? 'all') === 'paid' ? 'bg-navy text-white fw-bold' : 'text-secondary hover-navy' }}"
                        wire:click="filterType('paid')">
                        Kelas Berbayar
                    </button>
                </div>
            </div>
        </div>

        {{-- Courses Grid (Max 6 Kelas Aktif) --}}
        <div class="row g-4">
            @if (isset($courses) && $courses->isNotEmpty())
                @foreach ($courses as $course)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="{{ $loop->index * 50 + 150 }}ms">
                        <div
                            class="card h-100 border border-simpel rounded-4 bg-white overflow-hidden shadow-xs simpel-course-card d-flex flex-column">
                            {{-- Card Thumbnail & Badges --}}
                            <div class="position-relative overflow-hidden" style="height: 190px;">
                                @if ($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                        class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white"
                                        style="background: linear-gradient(135deg, #071a33 0%, #0c3158 100%);">
                                        @if ($course->isPermanent())
                                            <i class="ri-infinity-line display-4 text-gold"></i>
                                        @elseif ($course->isPaid())
                                            <i class="ri-money-dollar-circle-line display-4 text-gold"></i>
                                        @else
                                            <i class="ri-calendar-line display-4 text-gold"></i>
                                        @endif
                                    </div>
                                @endif

                                <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                                    @if ($course->isBatch())
                                        <span class="badge bg-primary text-white fw-bold fs-8 px-2_5 py-1">
                                            <i class="ri-calendar-line me-1"></i>Batch
                                        </span>
                                    @elseif ($course->isPermanent())
                                        <span class="badge bg-success text-white fw-bold fs-8 px-2_5 py-1">
                                            <i class="ri-infinity-line me-1"></i>Permanen
                                        </span>
                                    @else
                                        <span
                                            class="badge bg-warning-subtle text-dark border border-warning fw-bold fs-8 px-2_5 py-1">
                                            <i class="ri-money-dollar-circle-line me-1"></i>Berbayar
                                        </span>
                                    @endif

                                    @if ($course->category)
                                        <span class="badge bg-navy text-white fw-bold fs-8 px-2_5 py-1">
                                            {{ $course->category->name }}
                                        </span>
                                    @endif
                                </div>

                                <div class="position-absolute bottom-0 end-0 m-3">
                                    @if ($course->isPaid())
                                        <span class="badge bg-gold text-dark fs-8 fw-bold shadow-sm">
                                            Rp {{ number_format($course->price, 0, ',', '.') }}
                                        </span>
                                    @elseif ($course->isPermanent())
                                        <span class="badge bg-dark bg-opacity-75 text-white fs-8">
                                            Mandiri 24/7
                                        </span>
                                    @else
                                        <span class="badge bg-dark bg-opacity-75 text-white fs-8">
                                            {{ $course->start_date ? $course->start_date->format('d M Y') : 'Batch' }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Card Body --}}
                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between text-muted fs-8 mb-2">
                                    <span class="badge bg-light text-navy border border-simpel">
                                        {{ $course->category?->name ?? 'Kelas ASN' }}
                                    </span>
                                    <span class="text-success fw-bold">
                                        <i class="ri-checkbox-circle-line me-1"></i>Kelas Terbuka
                                    </span>
                                </div>

                                <h5 class="fw-bold text-navy mb-2 line-clamp-2"
                                    style="font-size: 16px; line-height: 1.4;">
                                    <a href="{{ route('landing.kelas.detail', $course) }}"
                                        class="text-navy text-decoration-none hover-gold">
                                        {{ $course->title }}
                                    </a>
                                </h5>

                                <p class="text-muted fs-7 mb-4 flex-grow-1 line-clamp-2">
                                    {{ $course->description ?: ($course->category?->description ?: 'Program kelas kompetensi aparatur yang diselenggarakan oleh BKPSDM Kabupaten Aceh Timur.') }}
                                </p>

                                {{-- Footer Action --}}
                                <div
                                    class="pt-3 border-top border-simpel d-flex align-items-center justify-content-between mt-auto">
                                    <div>
                                        <small class="text-muted d-block fs-8">Tipe Kelas</small>
                                        <span class="fw-extrabold text-navy fs-7">
                                            @if ($course->isPaid())
                                                Berbayar
                                            @elseif ($course->isPermanent())
                                                Permanen
                                            @else
                                                Batch
                                            @endif
                                        </span>
                                    </div>

                                    <a href="{{ route('landing.kelas.detail', $course) }}"
                                        class="btn-simpel-cta-gold py-2 px-3 fs-7">
                                        <span>Lihat Detail</span>
                                        <i class="ri-arrow-right-line ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                {{-- Empty State --}}
                <div class="col-12">
                    <div class="card border border-simpel rounded-4 bg-white p-5 text-center shadow-xs">
                        <div class="mb-3">
                            <div class="d-inline-flex p-3 rounded-circle bg-light text-navy">
                                <i class="ri-book-open-line display-5"></i>
                            </div>
                        </div>
                        <h5 class="text-navy fw-bold mb-1">Belum Ada Kelas Aktif untuk Kategori Ini</h5>
                        <p class="text-muted fs-6 mb-3">
                            Saat ini belum ada kelas terbuka untuk jenis yang Anda pilih.
                        </p>
                        <div>
                            <button type="button" class="btn btn-sm btn-simpel-gold rounded-pill px-4"
                                wire:click="filterType('all')">
                                <i class="ri-refresh-line me-1"></i>Tampilkan Semua Kelas
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>
</section>

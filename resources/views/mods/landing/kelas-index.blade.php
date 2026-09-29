<div>
    {{-- Header Banner --}}
    <section class="py-5 text-white"
        style="background: linear-gradient(135deg, #051427 0%, #071a33 50%, #0c3158 100%);">
        <div class="container py-lg-3 py-2">
            <div class="row align-items-center">
                <div class="col-lg-8 wow fadeInLeft" data-wow-delay="100ms">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-gold text-navy fw-bold fs-8 px-3 py-1_5">
                            <i class="{{ $config['icon'] }} me-1"></i>{{ $config['badge'] }}
                        </span>
                        <span class="badge" style="background: rgba(255, 255, 255, 0.15); color: #fff;">
                            {{ $courses->count() }} Kelas Tersedia
                        </span>
                    </div>
                    <h1 class="display-6 fw-extrabold text-white mb-3">
                        {{ $config['title_prefix'] }} <span class="text-gold">{{ $config['title_highlight'] }}</span>
                    </h1>
                    <p class="text-white-80 fs-6 mb-0 pe-lg-4">
                        {{ $config['subtitle'] }}
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                    <a href="{{ route('landing') }}" class="btn-simpel-outline-light">
                        <i class="ri-arrow-left-line"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Filter & Search Section --}}
    <section class="py-4 bg-white border-bottom border-simpel">
        <div class="container">
            <div class="row align-items-center justify-content-between g-3">
                {{-- Search Box --}}
                <div class="col-md-6 col-lg-5">
                    <div class="input-group border border-simpel rounded-pill overflow-hidden shadow-xs bg-light">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3">
                            <i class="ri-search-line"></i>
                        </span>
                        <input type="text"
                            class="form-control bg-transparent border-0 shadow-none fs-7 py-2"
                            placeholder="Cari nama kelas..."
                            wire:model.live.debounce.300ms="search">
                        @if ($search !== '')
                            <button class="btn bg-transparent border-0 text-muted pe-3" type="button" wire:click="$set('search', '')">
                                <i class="ri-close-line"></i>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Category Filter Dropdown --}}
                <div class="col-md-5 col-lg-4 text-md-end">
                    <div class="dropdown d-inline-block w-100 w-md-auto">
                        <button class="btn btn-outline-secondary dropdown-toggle w-100 border-simpel rounded-pill px-3 py-2 fs-7 text-navy fw-medium d-flex align-items-center justify-content-between gap-2 shadow-xs"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="d-flex align-items-center gap-2">
                                <i class="ri-filter-3-line text-gold"></i>
                                @if ($selectedCategory === 'all')
                                    <span>Semua Kategori</span>
                                @else
                                    <span>{{ $categories->firstWhere('slug', $selectedCategory)?->name ?? 'Semua Kategori' }}</span>
                                @endif
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-simpel radius-10 p-2 mt-1 w-100" style="min-width: 220px; max-height: 280px; overflow-y: auto;">
                            <li>
                                <button type="button" class="dropdown-item radius-8 fs-8 py-2 d-flex align-items-center justify-content-between {{ $selectedCategory === 'all' ? 'active bg-gold text-navy fw-bold' : '' }}"
                                    wire:click="filterCategory('all')">
                                    <span>Semua Kategori</span>
                                    @if ($selectedCategory === 'all')
                                        <i class="ri-check-line"></i>
                                    @endif
                                </button>
                            </li>
                            @foreach ($categories as $cat)
                                <li>
                                    <button type="button" class="dropdown-item radius-8 fs-8 py-2 d-flex align-items-center justify-content-between {{ $selectedCategory === $cat->slug ? 'active bg-gold text-navy fw-bold' : '' }}"
                                        wire:click="filterCategory('{{ $cat->slug }}')">
                                        <span>{{ $cat->name }}</span>
                                        @if ($selectedCategory === $cat->slug)
                                            <i class="ri-check-line"></i>
                                        @endif
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Active Filter Pills --}}
            @if ($search !== '' || $selectedCategory !== 'all')
                <div class="d-flex align-items-center gap-2 mt-3 pt-3 border-top border-simpel flex-wrap fs-8">
                    <span class="text-muted">Filter aktif:</span>
                    @if ($search !== '')
                        <span class="badge bg-light text-navy border border-simpel py-1_5 px-2_5 rounded-pill d-inline-flex align-items-center gap-1">
                            Pencarian: "{{ $search }}"
                            <i class="ri-close-line cursor-pointer" wire:click="$set('search', '')"></i>
                        </span>
                    @endif
                    @if ($selectedCategory !== 'all')
                        <span class="badge bg-light text-navy border border-simpel py-1_5 px-2_5 rounded-pill d-inline-flex align-items-center gap-1">
                            Kategori: {{ $categories->firstWhere('slug', $selectedCategory)?->name }}
                            <i class="ri-close-line cursor-pointer" wire:click="filterCategory('all')"></i>
                        </span>
                    @endif
                    <button class="btn btn-link text-danger p-0 fs-8 text-decoration-none ms-auto" wire:click="resetFilter">
                        <i class="ri-refresh-line me-1"></i>Reset Semua
                    </button>
                </div>
            @endif
        </div>
    </section>

    {{-- Courses Grid Section --}}
    <section class="py-5 bg-light" style="min-height: 60vh;">
        <div class="container py-lg-3">
            @if ($courses->isNotEmpty())
                <div class="row g-4">
                    @foreach ($courses as $course)
                        @php
                            $isRegistered = isset($userRegistrations[$course->id]);
                        @endphp
                        <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="{{ ($loop->index * 50) + 100 }}ms">
                            <div class="card h-100 border border-simpel rounded-4 bg-white overflow-hidden shadow-xs simpel-course-card d-flex flex-column">
                                {{-- Thumbnail & Badges --}}
                                <div class="position-relative overflow-hidden" style="height: 190px;">
                                    @if ($course->thumbnail)
                                        <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}"
                                            class="w-100 h-100 object-fit-cover">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white"
                                            style="background: linear-gradient(135deg, #071a33 0%, #0c3158 100%);">
                                            <i class="{{ $config['icon'] }} display-4 text-gold"></i>
                                        </div>
                                    @endif

                                    <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                                        @if ($course->isPermanent())
                                            <span class="badge bg-success text-white fw-bold fs-8 px-2_5 py-1">
                                                <i class="ri-infinity-line me-1"></i>Mandiri 24/7
                                            </span>
                                        @elseif ($course->isBatch())
                                            <span class="badge bg-primary text-white fw-bold fs-8 px-2_5 py-1">
                                                <i class="ri-calendar-event-line me-1"></i>Batch Terjadwal
                                            </span>
                                        @else
                                            <span class="badge bg-gold text-navy fw-bold fs-8 px-2_5 py-1">
                                                <i class="ri-money-dollar-circle-line me-1"></i>Berbayar
                                            </span>
                                        @endif

                                        @if ($course->category)
                                            <span class="badge bg-navy text-white fw-bold fs-8 px-2_5 py-1">
                                                {{ $course->category->name }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="position-absolute bottom-0 start-0 m-3">
                                        <span class="badge bg-dark bg-opacity-75 text-white fs-8">
                                            @if ($course->isPermanent())
                                                <i class="ri-time-line me-1"></i>Akses Fleksibel 24/7
                                            @elseif ($course->isPaid())
                                                <i class="ri-price-tag-3-line me-1"></i>Rp {{ number_format($course->price, 0, ',', '.') }}
                                            @else
                                                <i class="ri-calendar-line me-1"></i>{{ $course->start_date ? $course->start_date->format('d M Y') : 'Sesuai Jadwal' }}
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                {{-- Body Info --}}
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between text-muted fs-8 mb-2">
                                        <span class="text-secondary">
                                            <i class="ri-folders-line me-1"></i>{{ $course->chapters_count ?? 0 }} Bab &bull; {{ $course->lessons_count ?? 0 }} Materi
                                        </span>
                                        @if ($isRegistered)
                                            <span class="badge bg-success-subtle text-success fw-bold">
                                                <i class="ri-check-line me-1"></i>Terdaftar
                                            </span>
                                        @else
                                            <span class="badge bg-light text-navy border border-simpel">
                                                {{ $course->isPaid() ? 'Berbayar' : 'Gratis' }}
                                            </span>
                                        @endif
                                    </div>

                                    <h5 class="fw-bold text-navy mb-2 line-clamp-2" style="font-size: 16px; line-height: 1.4;">
                                        <a href="{{ route('landing.kelas.detail', $course->id) }}" class="text-navy text-decoration-none hover-gold">
                                            {{ $course->title }}
                                        </a>
                                    </h5>
                                    <p class="text-muted fs-7 mb-4 flex-grow-1 line-clamp-2">
                                        {{ $course->description ?: ($course->category?->description ?: 'Kelas digital bagi ASN untuk peningkatan kompetensi berkelanjutan.') }}
                                    </p>

                                    {{-- Footer Action --}}
                                    <div class="pt-3 border-top border-simpel d-flex align-items-center justify-content-between mt-auto">
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 11px;">
                                                {{ $course->isPaid() ? 'Biaya Kelas' : ($course->isPermanent() ? 'Jadwal Akses' : 'Jadwal Diklat') }}
                                            </small>
                                            <span class="fw-bold text-navy fs-7">
                                                @if ($course->isPaid())
                                                    Rp {{ number_format($course->price, 0, ',', '.') }}
                                                @elseif ($course->isPermanent())
                                                    <span class="text-success"><i class="ri-checkbox-circle-line me-1"></i>Kapan Saja</span>
                                                @else
                                                    {{ $course->start_date ? $course->start_date->format('d M Y') : 'Sesuai Jadwal' }}
                                                @endif
                                            </span>
                                        </div>
                                        <a href="{{ route('landing.kelas.detail', $course->id) }}" class="btn-simpel-cta-gold py-2 px-3 fs-7">
                                            <span>Lihat Detail</span>
                                            <i class="ri-arrow-right-line ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Empty State --}}
                <div class="card border border-simpel rounded-4 bg-white p-5 text-center shadow-xs">
                    <div class="mb-3">
                        <div class="d-inline-flex p-3 rounded-circle bg-light text-navy">
                            <i class="{{ $config['icon'] }} display-5"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold text-navy mb-2">{{ $config['empty_title'] }}</h5>
                    <p class="text-muted fs-7 max-w-500 mx-auto mb-4">
                        @if ($search !== '' || $selectedCategory !== 'all')
                            Tidak ditemukan kelas yang cocok dengan kriteria pencarian atau kategori yang dipilih.
                        @else
                            {{ $config['empty_desc'] }}
                        @endif
                    </p>
                    <div>
                        @if ($search !== '' || $selectedCategory !== 'all')
                            <button class="btn btn-outline-secondary radius-8 px-4 py-2 fs-7" wire:click="resetFilter">
                                <i class="ri-refresh-line me-1"></i>Reset Pencarian
                            </button>
                        @else
                            <a href="{{ route('landing') }}" class="btn-simpel-cta-gold py-2 px-4 fs-7">
                                <i class="ri-home-4-line me-1"></i>Kembali ke Beranda
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>

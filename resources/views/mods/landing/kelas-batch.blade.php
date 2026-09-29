<div>
    {{-- Header Banner --}}
    <section class="py-5 text-white"
        style="background: linear-gradient(135deg, #051427 0%, #071a33 50%, #0c3158 100%);">
        <div class="container py-lg-3 py-2">
            <div class="row align-items-center">
                <div class="col-lg-8 wow fadeInLeft" data-wow-delay="100ms">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-gold text-navy fw-bold fs-8 px-3 py-1_5">
                            <i class="ri-calendar-event-line me-1"></i>Pelatihan Terjadwal (Batch)
                        </span>
                        <span class="badge bg-white bg-opacity-10 text-white fs-8 px-2_5 py-1">
                            {{ $courses->count() }} Kelas Tersedia
                        </span>
                    </div>
                    <h1 class="display-6 fw-extrabold text-white mb-3">
                        Katalog <span class="text-gold">Kelas Batch</span>
                    </h1>
                    <p class="text-white-80 fs-6 mb-0 pe-lg-4">
                        Program diklat dan pelatihan kedinasan berjadwal dengan kuota dan periode registrasi berkala untuk ASN BKPSDM Kabupaten Aceh Timur.
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
                        <input type="text" class="form-control bg-transparent border-0 fs-7 shadow-none"
                            placeholder="Cari nama kelas batch..."
                            wire:model.live.debounce.300ms="search">
                        @if ($search)
                            <button class="btn btn-link text-muted pe-2 text-decoration-none" wire:click="$set('search', '')" title="Hapus pencarian">
                                <i class="ri-close-circle-line"></i>
                            </button>
                        @endif
                        <span class="input-group-text bg-transparent border-0 pe-3 text-gold" wire:loading wire:target="search, selectedCategory">
                            <div class="spinner-border spinner-border-sm" role="status" style="width: 14px; height: 14px;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </span>
                    </div>
                </div>

                {{-- Dropdown Kategori (Opsi A: Styled Select Rounded Pill) --}}
                <div class="col-md-6 col-lg-4 text-md-end">
                    <div class="input-group border border-simpel rounded-pill overflow-hidden shadow-xs bg-light">
                        <span class="input-group-text bg-transparent border-0 text-navy ps-3 pe-1">
                            <i class="ri-filter-3-line"></i>
                        </span>
                        <select class="form-select bg-transparent border-0 fs-7 shadow-none text-navy fw-medium pe-4 ignore"
                            wire:model.live="selectedCategory">
                            <option value="all">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Course Grid --}}
    <section class="py-5 bg-light">
        <div class="container py-lg-3 py-2">
            @if ($courses->isNotEmpty())
                <div class="row g-4">
                    @foreach ($courses as $course)
                        @php
                            $isRegistered = isset($userRegistrations[$course->id]);
                            $reg = $isRegistered ? $userRegistrations[$course->id] : null;
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
                                            <i class="ri-calendar-line display-4 text-gold"></i>
                                        </div>
                                    @endif
                                    <div class="position-absolute top-0 start-0 m-3 d-flex gap-2">
                                        <span class="badge bg-navy text-white fw-bold fs-8 px-2_5 py-1">
                                            <i class="ri-calendar-line me-1"></i>Batch
                                        </span>
                                        @if ($course->category)
                                            <span class="badge bg-gold text-navy fw-bold fs-8 px-2_5 py-1">
                                                {{ $course->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                    @if ($course->start_date && $course->end_date)
                                        <div class="position-absolute bottom-0 start-0 m-3">
                                            <span class="badge bg-dark bg-opacity-75 text-white fs-8">
                                                <i class="ri-time-line me-1"></i>{{ $course->start_date->format('d M') }} - {{ $course->end_date->format('d M Y') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Body Info --}}
                                <div class="p-4 d-flex flex-column flex-grow-1">
                                    <div class="d-flex align-items-center justify-content-between text-muted fs-8 mb-2">
                                        <span class="text-secondary">
                                            <i class="ri-book-open-line me-1"></i>{{ $course->lessons_count ?? 0 }} Materi
                                        </span>
                                        @if ($isRegistered)
                                            <span class="badge bg-success-subtle text-success fw-bold">
                                                <i class="ri-check-line me-1"></i>Terdaftar
                                            </span>
                                        @else
                                            <span class="text-success fw-bold">
                                                <i class="ri-checkbox-circle-line me-1"></i>Pendaftaran Buka
                                            </span>
                                        @endif
                                    </div>

                                    <h5 class="fw-bold text-navy mb-2 line-clamp-2" style="font-size: 16px; line-height: 1.4;">
                                        {{ $course->title }}
                                    </h5>
                                    <p class="text-muted fs-7 mb-4 flex-grow-1 line-clamp-2">
                                        {{ $course->description ?? 'Pelatihan kedinasan terstruktur untuk meningkatkan kapabilitas aparatur sipil negara.' }}
                                    </p>

                                    {{-- Footer Action --}}
                                    <div class="pt-3 border-top border-simpel d-flex align-items-center justify-content-between mt-auto">
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 11px;">Jadwal Diklat</small>
                                            <span class="fw-bold text-navy fs-7">
                                                {{ $course->start_date ? $course->start_date->format('d M Y') : 'Sesuai Jadwal' }}
                                            </span>
                                        </div>
                                        @auth
                                            @if ($isRegistered)
                                                <a href="{{ route('peserta.materi', $course->id) }}" class="btn-simpel-cta-gold py-2 px-3 fs-7">
                                                    <span>Lanjut Belajar</span>
                                                    <i class="ri-arrow-right-line ms-1"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('peserta.materi', $course->id) }}" class="btn btn-sm btn-simpel-navy py-2 px-3 fs-7 rounded-pill">
                                                    <span>Mulai Belajar</span>
                                                    <i class="ri-arrow-right-line ms-1"></i>
                                                </a>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-sm btn-simpel-navy py-2 px-3 fs-7 rounded-pill">
                                                <span>Mulai Belajar</span>
                                                <i class="ri-login-box-line ms-1"></i>
                                            </a>
                                        @endauth
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
                            <i class="ri-calendar-close-line display-5"></i>
                        </div>
                    </div>
                    <h5 class="text-navy fw-bold mb-1">Tidak Ada Kelas Batch Ditemukan</h5>
                    <p class="text-muted fs-6 mb-3">
                        @if ($search || $selectedCategory !== 'all')
                            Tidak ada kelas batch yang cocok dengan kata kunci atau filter yang Anda pilih.
                        @else
                            Saat ini belum ada jadwal kelas batch aktif yang dibuka.
                        @endif
                    </p>
                    @if ($search || $selectedCategory !== 'all')
                        <div>
                            <button type="button" class="btn btn-sm btn-simpel-gold rounded-pill px-4" wire:click="resetFilter">
                                <i class="ri-refresh-line me-1"></i>Reset Filter
                            </button>
                        </div>
                    @else
                        <div>
                            <a href="{{ route('landing') }}" class="btn btn-sm btn-simpel-navy rounded-pill px-4">
                                <i class="ri-home-4-line me-1"></i>Kembali ke Beranda
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>
</div>

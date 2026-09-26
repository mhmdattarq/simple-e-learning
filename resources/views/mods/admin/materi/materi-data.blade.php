<div>
    {{-- Header Tahap 6: Ruang Materi (Kurikulum & Silabus) --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Tahap 6: Ruang Materi & Silabus Kurikulum</h5>
            <p class="text-muted mb-0">Manajemen bab dan unit materi pembelajaran sekuensial ala Dicoding, naskah rich text (Editor.js), serta proteksi curriculum freeze.</p>
        </div>
        @if ($isAdmin)
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 radius-8 fs-8">
                <i class="ri-shield-user-line me-1"></i> Mode Admin Diklat (Akses Penuh / Backup Mentor)
            </span>
        @else
            <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 radius-8 fs-8">
                <i class="ri-user-star-line me-1"></i> Mode Widyaiswara (Kelas Ampuan Anda)
            </span>
        @endif
    </div>

    {{-- Filter Toolbar --}}
    <div class="card simpel-card border-0 shadow-sm radius-16 mb-24">
        <div class="card-body p-20">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="position-relative">
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm ps-40 radius-8" placeholder="Cari judul atau kode pelatihan...">
                        <i class="ri-search-line position-absolute top-50 start-0 translate-middle-y ms-3 text-muted fs-6"></i>
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="filterType" class="form-select form-select-sm radius-8">
                        <option value="">-- Semua Model Diklat --</option>
                        <option value="permanent">Permanen (Buka Terus / Self-Paced)</option>
                        <option value="batch">Batch (Periode Angkatan Tertentu)</option>
                    </select>
                </div>
                <div class="col-md-4 text-md-end text-muted fs-8">
                    Menampilkan <strong>{{ $courses->total() }}</strong> program pelatihan diklat
                </div>
            </div>
        </div>
    </div>

    {{-- Courses Grid --}}
    <div class="row g-4">
        @forelse ($courses as $c)
            @php
                $isFrozen = $c->isCurriculumFrozen();
            @endphp
            <div class="col-md-6 col-lg-4" wire:key="course-card-{{ $c->id }}">
                <div class="card h-100 border-0 shadow-sm radius-16 overflow-hidden transition-all hover-shadow">
                    {{-- Card Header Thumbnail / Gradient --}}
                    <div class="position-relative p-20 bg-light border-bottom">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1 radius-4 font-monospace fs-8">
                                {{ $c->code }}
                            </span>
                            @if ($c->isBatch())
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 radius-4 fs-8">
                                    <i class="ri-calendar-line me-1"></i>Batch (Angkatan)
                                </span>
                            @else
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 radius-4 fs-8">
                                    <i class="ri-infinity-line me-1"></i>Permanen
                                </span>
                            @endif
                        </div>

                        <h6 class="fw-bold text-dark mb-1 text-truncate-2" style="min-height: 44px;">
                            {{ $c->title }}
                        </h6>

                        <div class="d-flex align-items-center gap-2 mt-2">
                            <span class="badge bg-light text-muted border px-2 py-1 fs-8">
                                <i class="ri-folder-line me-1"></i>{{ $c->category?->name ?? 'Umum' }}
                            </span>
                            @if ($c->isBatch() && $c->start_date && $c->end_date)
                                <small class="text-muted fs-8">
                                    {{ $c->start_date->format('d M') }} - {{ $c->end_date->format('d M Y') }}
                                </small>
                            @else
                                <small class="text-muted fs-8">Akses Mandiri Fleksibel</small>
                            @endif
                        </div>
                    </div>

                    {{-- Card Body Silabus Info --}}
                    <div class="card-body p-20 d-flex flex-column justify-content-between">
                        <div>
                            {{-- Batch Freeze Status Indicator ala Dicoding --}}
                            <div class="mb-3">
                                @if ($isFrozen)
                                    <div class="p-2 radius-8 bg-danger-subtle text-danger border border-danger-subtle d-flex align-items-center gap-2 fs-8">
                                        <i class="ri-lock-2-line fs-6 flex-shrink-0"></i>
                                        <div>
                                            <strong>Curriculum Freeze Aktif</strong><br>
                                            <span class="text-muted fs-9">Batch telah berjalan, struktur materi dikunci demi integritas progres peserta.</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-2 radius-8 bg-success-subtle text-success border border-success-subtle d-flex align-items-center gap-2 fs-8">
                                        <i class="ri-lock-unlock-line fs-6 flex-shrink-0"></i>
                                        <div>
                                            <strong>Kurikulum Terbuka</strong><br>
                                            <span class="text-muted fs-9">Dapat menambah/memperbarui Bab & Materi pembelajaran.</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Mentor Assignment Info --}}
                            <div class="d-flex align-items-center gap-2 mb-3 fs-8 text-muted">
                                <i class="ri-user-voice-line text-simple"></i>
                                <span>
                                    Mentor: 
                                    @php
                                        $mentors = $c->schedules->pluck('mentor.name')->filter()->unique();
                                    @endphp
                                    <strong>{{ $mentors->isNotEmpty() ? $mentors->implode(', ') : 'Belum Ditugaskan' }}</strong>
                                </span>
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <div class="pt-10 border-top mt-2">
                            <a href="{{ route('materi.sesi', $c->id) }}" class="btn btn-sm btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center gap-2 radius-8 py-2 font-weight-500 shadow-none" wire:navigate>
                                <i class="ri-calendar-event-line fs-6"></i>
                                <span>Kelola Sesi & Materi</span>
                                <i class="ri-arrow-right-line ms-auto"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm radius-16 text-center p-40">
                    <i class="ri-book-read-line text-muted display-4 mb-2"></i>
                    <h6 class="fw-bold text-dark mb-1">Tidak Ada Program Pelatihan Ditemukan</h6>
                    <p class="text-muted fs-8 mb-0">Coba ubah kata kunci pencarian atau filter tipe pelatihan.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-24">
        {{ $courses->links() }}
    </div>
</div>

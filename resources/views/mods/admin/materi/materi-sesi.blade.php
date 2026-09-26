<div>
    {{-- Header Navigasi & Breadcrumb --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-8 fw-semibold">
                    <i class="ri-book-open-line me-1"></i> Modul 6: Ruang Materi
                </span>
                <h5 class="fw-bold text-dark mb-0">Daftar Sesi Pelatihan</h5>
            </div>
            <p class="text-muted mb-0">
                Pilih sesi pelatihan untuk menyusun silabus, bab materi, dan konten pembelajaran interaktif.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('materi.data') }}" class="btn btn-outline-danger d-inline-flex align-items-center gap-1 radius-8 px-3 py-2" wire:navigate>
                <i class="ri-arrow-left-line"></i>
                <span>Kembali ke Katalog Pelatihan</span>
            </a>
        </div>
    </div>

    {{-- Kartu Ringkasan Informasi Pelatihan --}}
    <div class="card border-0 shadow-sm radius-16 bg-white mb-24 overflow-hidden">
        <div class="card-body p-20">
            <div class="row align-items-center g-3">
                <div class="col-lg-8 col-12">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        @if($course->code)
                            <span class="badge bg-light text-secondary border font-monospace">{{ $course->code }}</span>
                        @endif
                        @if($course->category)
                            <span class="badge bg-info-subtle text-info border border-info-subtle">{{ $course->category->name }}</span>
                        @endif
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                            {{ $course->isBatch() ? 'Batch (Angkatan)' : 'Permanen (Self-Paced)' }}
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mb-2 fs-5">{{ $course->title }}</h5>
                    <div class="d-flex flex-wrap align-items-center gap-3 fs-8 text-muted">
                        <span><i class="ri-map-pin-line text-danger me-1"></i>Metode: <strong>{{ ucfirst($course->method) }}</strong></span>
                        @if($course->location)
                            <span><i class="ri-building-line text-primary me-1"></i>Lokasi: <strong>{{ $course->location }}</strong></span>
                        @endif
                        @if($course->start_date && $course->end_date)
                            <span><i class="ri-calendar-line text-success me-1"></i>Periode: <strong>{{ $course->start_date->format('d/m/Y') }} - {{ $course->end_date->format('d/m/Y') }}</strong></span>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4 col-12 text-lg-end">
                    <div class="d-inline-flex flex-column align-items-lg-end gap-1 bg-light p-3 rounded-3">
                        <span class="text-muted fs-8">Total Sesi Pelatihan Terdaftar:</span>
                        <h4 class="fw-bold text-primary mb-0">{{ $schedules->count() }} Sesi</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar Pencarian Sesi --}}
    <div class="card border-0 shadow-sm radius-16 mb-24 bg-white">
        <div class="card-body p-16">
            <div class="row align-items-center g-3">
                <div class="col-md-6 col-12">
                    <div class="position-relative">
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm ps-40 radius-8"
                            placeholder="Cari judul sesi pelatihan...">
                        <i class="ri-search-line position-absolute top-50 start-0 translate-middle-y ms-3 text-muted fs-6"></i>
                    </div>
                </div>
                <div class="col-md-6 col-12 text-md-end text-muted fs-8">
                    Menampilkan <strong>{{ $schedules->count() }}</strong> sesi aktif
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Sesi Pelatihan --}}
    <div class="row g-4">
        @forelse($schedules as $index => $sch)
            @php
                $chapterCount = $sch->chapters->count();
                $lessonCount = $sch->chapters->sum(fn($ch) => $ch->lessons->count());
            @endphp
            <div class="col-lg-6 col-12" wire:key="session-card-{{ $sch->id }}">
                <div class="card h-100 border-0 shadow-sm radius-16 overflow-hidden bg-white">
                    <div class="card-header bg-white py-16 px-20 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 28px; height: 28px; font-size: 13px;">
                                {{ $index + 1 }}
                            </span>
                            <span class="fw-bold text-dark fs-7">Sesi Ke-{{ $index + 1 }}</span>
                        </div>
                        <div>
                            {!! $sch->getAttendanceStatusBadge() !!}
                        </div>
                    </div>
                    <div class="card-body p-20">
                        <h6 class="fw-bold text-dark mb-2 fs-6">{{ $sch->session_title }}</h6>

                        <div class="row g-2 fs-8 text-secondary mb-3">
                            <div class="col-sm-6">
                                <i class="ri-user-star-line text-primary me-1"></i><strong>Mentor:</strong>
                                <span>{{ $sch->mentor?->name ?? 'Belum Ditugaskan' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <i class="ri-map-pin-line text-danger me-1"></i><strong>Ruangan/Link:</strong>
                                <span class="text-truncate d-inline-block align-bottom" style="max-width: 150px;">{{ $sch->room_or_link ?? '-' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <i class="ri-calendar-line text-info me-1"></i><strong>Tanggal:</strong>
                                <span>{{ $sch->session_date?->translatedFormat('d F Y') ?? '-' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <i class="ri-time-line text-success me-1"></i><strong>Waktu:</strong>
                                <span>{{ substr($sch->start_time ?? '', 0, 5) }} - {{ substr($sch->end_time ?? '', 0, 5) }} WIB</span>
                            </div>
                        </div>

                        {{-- Ringkasan Materi di Sesi Ini --}}
                        <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded-3 mb-3 fs-8 text-muted">
                            <div class="d-flex align-items-center gap-3">
                                <span><i class="ri-folder-open-line text-warning me-1"></i><strong>{{ $chapterCount }}</strong> Bab Materi</span>
                                <span><i class="ri-file-text-line text-primary me-1"></i><strong>{{ $lessonCount }}</strong> Konten</span>
                            </div>
                            @if($chapterCount > 0)
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Sudah Ada Materi</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Belum Ada Materi</span>
                            @endif
                        </div>

                        {{-- Tombol Aksi Masuk ke Kelola Silabus & Materi Sesi --}}
                        <a href="{{ route('materi.detail', ['id' => $course->id, 'schedule_id' => $sch->id]) }}"
                            class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2 radius-8 py-2 font-weight-500"
                            wire:navigate>
                            <i class="ri-book-open-line fs-6"></i>
                            <span>Kelola Silabus & Materi Sesi</span>
                            <i class="ri-arrow-right-line ms-auto"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm radius-16 text-center p-40 bg-white">
                    <i class="ri-calendar-event-line text-muted display-4 mb-2"></i>
                    <h6 class="fw-bold text-dark mb-1">Belum Ada Sesi Pelatihan Terjadwal</h6>
                    <p class="text-muted fs-8 mb-3">Jadwal sesi pelatihan dibuat terlebih dahulu pada Modul Penjadwalan agar materi dapat dihubungkan ke masing-masing sesi.</p>
                    <div>
                        <a href="{{ route('penjadwalan.data') }}" class="btn btn-sm btn-outline-primary radius-8 px-3 py-2 fs-8" wire:navigate>
                            <i class="ri-calendar-check-line me-1"></i> Buka Modul Penjadwalan
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

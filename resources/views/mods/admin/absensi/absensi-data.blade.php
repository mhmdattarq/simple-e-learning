<div>
    {{-- Breadcrumb & Judul Modul --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Tahap 5: Absensi Elektronik</h5>
            <p class="text-muted mb-0">Manajemen presensi digital sesi diklat, pembuatan token absensi 6 digit,
                pemantauan kehadiran realtime, dan koreksi status kehadiran ASN.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if ($isMentor)
                <span
                    class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-7 fw-semibold">
                    <i class="ri-user-star-line me-1"></i> Mode Mentor Pengampu Sesi
                </span>
            @else
                <span
                    class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-7 fw-semibold">
                    <i class="ri-shield-user-line me-1"></i> Mode Admin: Monitoring & Audit Diklat
                </span>
            @endif
        </div>
    </div>

    {{-- Kartu Ringkasan Statistik & Indikator Sesi --}}
    <div class="row g-3 mb-24">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm radius-12 p-3 bg-white d-flex flex-row align-items-center gap-3">
                <div class="p-3 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 50px; height: 50px;">
                    <i class="ri-calendar-check-line fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted fs-8 mb-1">Total Sesi Pelatihan Hari Ini</h6>
                    <h4 class="fw-bold text-dark mb-0">{{ $totalSessionsToday }} <span
                            class="fs-8 fw-normal text-muted">Sesi</span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm radius-12 p-3 bg-white d-flex flex-row align-items-center gap-3">
                <div class="p-3 bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 50px; height: 50px;">
                    <i class="ri-radar-line fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted fs-8 mb-1">Sesi Token Aktif Berjalan</h6>
                    <h4 class="fw-bold text-success mb-0">{{ $activeSessionsNow }} <span
                            class="fs-8 fw-normal text-muted">Sesi Terbuka</span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm radius-12 p-3 bg-white d-flex flex-row align-items-center gap-3">
                <div class="p-3 bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 50px; height: 50px;">
                    <i class="ri-user-star-line fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted fs-8 mb-1">Masa Berlaku Standar Token</h6>
                    <h4 class="fw-bold text-dark mb-0">15 <span class="fs-8 fw-normal text-muted">Menit per Sesi</span>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card with Table --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div
            class="card-header bg-white pt-20 pb-0 px-20 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-qr-code-line text-simple fs-5"></i>
                <h6 class="fw-bold text-dark mb-0 fs-6">Daftar Sesi & Presensi Pelatihan</h6>
            </div>
        </div>

        {{-- Kontainer tabel dengan wire:ignore agar render DOM DataTables tidak terganggu siklus Livewire --}}
        <div class="card-body p-20" wire:ignore>
            {{-- Filter & Search Header Bar --}}
            <div class="row g-2 mb-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fs-8 text-muted mb-1">Filter Pelatihan:</label>
                    <select id="filter_course_id" class="form-select form-select-sm">
                        <option value="">-- Semua Pelatihan --</option>
                        @foreach ($courses as $c)
                            <option value="{{ $c->id }}">[{{ $c->code }}] {{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-8 text-muted mb-1">Filter Tanggal:</label>
                    <input type="date" id="filter_date" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-8 text-muted mb-1">Status Absensi:</label>
                    <select id="filter_token_status" class="form-select form-select-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="active">Sedang Dibuka</option>
                        <option value="unopened">Belum Dibuka</option>
                        <option value="closed">Ditutup</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-8 text-muted mb-1">Cari Data Sesi:</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted border-end-0"><i
                                class="ri-search-line"></i></span>
                        <input type="text" id="custom_search_dt" class="form-control form-control-sm border-start-0"
                            placeholder="Ketik judul sesi, mentor, token...">
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" id="btn-reset-filters" class="btn btn-sm btn-light border w-100"
                        title="Reset Semua Filter">
                        <i class="ri-refresh-line"></i> Reset
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table id="tableAbsensi" class="table table-hover align-middle mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Pelatihan</th>
                            <th>Judul Sesi</th>
                            <th style="width: 170px;">Tanggal & Jam Sesi</th>
                            <th>Mentor</th>
                            <th class="text-center" style="width: 140px;">Status Absensi</th>
                            <th class="text-center" style="width: 160px;">Kehadiran</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- AJAX DataTables Content --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('mods.admin.absensi.atc.absensi-data-atc')
</div>

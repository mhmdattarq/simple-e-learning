<div id="kontak-pesan-root">
    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Kotak Masuk Pesan</h5>
            <p class="text-muted mb-0">Kelola dan respon pertanyaan, aduan, serta permohonan informasi dari ASN atau
                masyarakat.</p>
        </div>
    </div>

    {{-- 4 Stat Cards --}}
    <div class="row g-3 mb-24">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm radius-16 p-20 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-8 text-uppercase fw-semibold">Total Pesan</span>
                        <h4 class="fw-bold text-dark mb-0 mt-1">{{ number_format($stats['total'] ?? 0) }}</h4>
                    </div>
                    <div
                        class="w-48-px h-48-px rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-4">
                        <i class="ri-mail-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm radius-16 p-20 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-8 text-uppercase fw-semibold">Belum Dibaca</span>
                        <h4 class="fw-bold text-danger mb-0 mt-1">{{ number_format($stats['unread'] ?? 0) }}</h4>
                    </div>
                    <div
                        class="w-48-px h-48-px rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center fs-4">
                        <i class="ri-mail-unread-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm radius-16 p-20 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-8 text-uppercase fw-semibold">Sudah Dibaca</span>
                        <h4 class="fw-bold text-info mb-0 mt-1">{{ number_format($stats['read'] ?? 0) }}</h4>
                    </div>
                    <div
                        class="w-48-px h-48-px rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center fs-4">
                        <i class="ri-mail-open-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm radius-16 p-20 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-8 text-uppercase fw-semibold">Telah Dibalas</span>
                        <h4 class="fw-bold text-success mb-0 mt-1">{{ number_format($stats['replied'] ?? 0) }}</h4>
                    </div>
                    <div
                        class="w-48-px h-48-px rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fs-4">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div
            class="card-header bg-white pt-20 pb-0 px-20 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-inbox-archive-line text-simple fs-5"></i>
                <h6 class="fw-bold text-dark mb-0 fs-6">Daftar Pertanyaan &amp; Pesan Masuk</h6>
            </div>
        </div>

        <div class="card-body p-20" wire:ignore>
            <div class="table-responsive">
                <table id="tablePesan" class="table table-hover align-middle mb-0" style="width: 100%;">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th class="text-center" style="width: 90px;">Aksi</th>
                            <th style="min-width: 180px;">Pengirim</th>
                            <th style="min-width: 180px;">Topik Pertanyaan</th>
                            <th style="min-width: 250px;">Pratinjau Pesan</th>
                            <th class="text-center" style="width: 120px;">Status</th>
                            <th class="text-center" style="width: 140px;">Waktu Kirim</th>
                        </tr>
                        {{-- Thead Kedua: Filter pencarian kolom --}}
                        <tr id="header-filter-pesan" class="bg-light">
                            <th></th>
                            <th></th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari pengirim/email...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari topik...">
                            </th>
                            <th></th>
                            <th class="text-center">
                                <select class="form-select form-select-sm search-col-dt">
                                    <option value="">Semua</option>
                                    <option value="unread">Belum Dibaca</option>
                                    <option value="read">Dibaca</option>
                                    <option value="replied">Dibalas</option>
                                </select>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- AJAX DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Include JS ATC --}}
    @include('mods.admin.kontak.atc.pesan-data-atc')
</div>

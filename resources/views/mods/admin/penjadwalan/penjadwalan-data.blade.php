<div>
    {{-- Header & Aksi Tambah --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Tahap 4: Penjadwalan Sesi Pelatihan</h5>
            <p class="text-muted mb-0">Manajemen jadwal kelas, penetapan ruangan fisik/tautan daring Zoom, tanggal
                pelaksanaan, dan mentor pengampu.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('penjadwalan.create') }}" class="btn btn-simple-gold" wire:navigate>
                <i class="ri-add-line fs-5"></i>
                <span>Tambah Jadwal Sesi</span>
            </a>
        </div>
    </div>

    {{-- Main Card with Table (Identical Structure to Perencanaan & Pendaftaran) --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div
            class="card-header bg-white pt-20 pb-0 px-20 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-calendar-event-fill text-simple fs-5"></i>
                <h6 class="fw-bold text-dark mb-0 fs-6">Daftar Jadwal Sesi Pelatihan (Agenda Diklat)</h6>
            </div>
        </div>

        {{-- Container Table Wajib wire:ignore (PRD-LW) --}}
        <div class="card-body p-20" wire:ignore>
            <div class="table-responsive">
                <table id="tablePenjadwalan" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 40px;">
                                <input class="form-check-input check-data-all" type="checkbox">
                            </th>
                            <th class="text-center" style="width: 70px;">Aksi</th>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Judul Sesi / Agenda</th>
                            <th>Pelatihan</th>
                            <th style="width: 170px;">Tanggal & Waktu</th>
                            <th>Mentor Pengampu</th>
                            <th style="width: 160px;">Ruangan / Link</th>
                            <th class="text-center" style="width: 110px;">Status</th>
                        </tr>
                        {{-- Thead Kedua: Search Filter Per Kolom (PRD-LW) --}}
                        <tr id="header-filter" class="bg-light">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari judul sesi...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari pelatihan...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari tanggal...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari mentor...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari lokasi...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari status...">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Diisi secara dinamis melalui AJAX Yajra DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('mods.admin.penjadwalan.atc.penjadwalan-data-atc')

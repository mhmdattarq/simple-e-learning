<div>
    {{-- Header & Aksi Tambah --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Tahap 1: Perencanaan Diklat</h5>
            <p class="text-muted text-xs mb-0">Manajemen perumusan program pelatihan, penetapan kuota, model
                permanen/batch, dan KAK.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('perencanaan.create') }}" class="btn btn-simple-gold" wire:navigate>
                <i class="ri-add-line fs-5"></i>
                <span>Tambah Pelatihan Baru</span>
            </a>
        </div>
    </div>

    {{-- Main Card with Table --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div
            class="card-header bg-white py-16 px-20 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-file-list-3-fill text-simple fs-5"></i>
                <h6 class="fw-bold text-dark mb-0 fs-6">Daftar Pelatihan (Katalog Perencanaan)</h6>
            </div>
        </div>

        {{-- Container Table Wajib wire:ignore (PRD-LW) --}}
        <div class="card-body p-20" wire:ignore>
            <div class="table-responsive">
                <table id="tablePerencanaan" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 40px;">
                                <input class="form-check-input check-data-all" type="checkbox">
                            </th>
                            <th class="text-center" style="width: 70px;">Aksi</th>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th style="width: 130px;">Kode</th>
                            <th>Nama Pelatihan</th>
                            <th>Kategori</th>
                            <th class="text-center" style="width: 140px;">Metode</th>
                            <th class="text-center" style="width: 110px;">Kuota</th>
                            <th class="text-center" style="width: 110px;">Status</th>
                        </tr>
                        {{-- Thead Kedua: Search Filter Per Kolom (PRD-LW) --}}
                        <tr id="header-filter" class="bg-light">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari kode...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari nama pelatihan...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari kategori...">
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Diisi secara dinamis melalui AJAX Yajra DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Include Script ATC --}}
    @include('mods.admin.perencanaan.atc.perencanaan-data-atc')
</div>

<div>
    {{-- Header & Aksi Tambah --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Kategori Kelas</h5>
            <p class="text-muted mb-0">Manajemen rumpun dan bidang kompetensi kelas pelatihan ASN.</p>
        </div>
    </div>

    {{-- Main Card with Table --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div
            class="card-header bg-white pt-20 pb-0 px-20 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-folder-3-fill text-simple fs-5"></i>
                <h6 class="fw-bold text-dark mb-0 fs-6">Daftar Kategori Kelas</h6>
            </div>
        </div>

        {{-- Kontainer tabel dengan wire:ignore agar render DOM DataTables tidak terganggu siklus Livewire --}}
        <div class="card-body p-20" wire:ignore>
            <div class="table-responsive">
                <table id="tableKategori" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 40px;">
                                <input class="form-check-input check-data-all" type="checkbox">
                            </th>
                            <th class="text-center" style="width: 70px;">Aksi</th>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th style="min-width: 200px;">Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th class="text-center" style="width: 120px;">Total Kelas</th>
                        </tr>
                        {{-- Thead Kedua: Filter pencarian spesifik per kolom tabel --}}
                        <tr id="header-filter-kategori" class="bg-light">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari nama kategori...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari deskripsi...">
                            </th>
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
    @include('mods.admin.kategori.atc.kategori-data-atc')
</div>

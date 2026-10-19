<div>
    {{-- Header & Aksi Tambah --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Data Kelas & Pelatihan</h5>
            <p class="text-muted mb-0">Manajemen seluruh katalog kelas internal (Bulanan & Bangkom) dan direktori Bangkom
                Eksternal.</p>
        </div>
    </div>

    {{-- Main Card with Unified Table (Mencakup Seluruh Kelas Internal & Eksternal) --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div
            class="card-header bg-white pt-20 pb-16 px-20 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-file-list-3-fill text-simple fs-5"></i>
                <h6 class="fw-bold text-dark mb-0 fs-6">Daftar Kelas & Pelatihan</h6>
                <span class="badge bg-light text-muted border rounded-pill">{{ $totalCount }} Total</span>
            </div>

            {{-- Filter Pills Jenis Kelas (Sama seperti di Landing Page) --}}
            <div class="d-inline-flex flex-wrap gap-1 p-1 bg-white border border-simpel rounded-pill shadow-xs simpel-filter-pills">
                <button type="button"
                    class="btn btn-sm rounded-pill px-3 py-1_5 fw-medium fs-8 {{ $selectedType === 'all' ? 'bg-navy text-white fw-bold shadow-xs' : 'text-secondary hover-navy' }}"
                    wire:click="filterType('all')" onclick="setKelasTypeFilter('all')">
                    Semua Kelas
                </button>
                <button type="button"
                    class="btn btn-sm rounded-pill px-3 py-1_5 fw-medium fs-8 {{ $selectedType === 'bulanan' ? 'bg-navy text-white fw-bold shadow-xs' : 'text-secondary hover-navy' }}"
                    wire:click="filterType('bulanan')" onclick="setKelasTypeFilter('bulanan')">
                    Bulanan
                </button>
                <button type="button"
                    class="btn btn-sm rounded-pill px-3 py-1_5 fw-medium fs-8 {{ $selectedType === 'bangkom' ? 'bg-navy text-white fw-bold shadow-xs' : 'text-secondary hover-navy' }}"
                    wire:click="filterType('bangkom')" onclick="setKelasTypeFilter('bangkom')">
                    Bangkom
                </button>
                <button type="button"
                    class="btn btn-sm rounded-pill px-3 py-1_5 fw-medium fs-8 {{ $selectedType === 'bangkom_external' ? 'bg-navy text-white fw-bold shadow-xs' : 'text-secondary hover-navy' }}"
                    wire:click="filterType('bangkom_external')" onclick="setKelasTypeFilter('bangkom_external')">
                    Bangkom Eksternal
                </button>
            </div>
        </div>

        {{-- Kontainer tabel dengan wire:ignore agar render DOM DataTables tidak terganggu siklus Livewire --}}
        <div class="card-body p-20" wire:ignore>
            <div class="table-responsive">
                <table id="tableKelas" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 40px;">
                                <input class="form-check-input check-data-all" type="checkbox">
                            </th>
                            <th class="text-center" style="width: 70px;">Aksi</th>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama Kelas</th>
                            <th>Kategori</th>
                            <th class="text-center" style="width: 170px;">Materi</th>
                            <th class="text-center" style="width: 110px;">Status</th>
                        </tr>
                        {{-- Thead Kedua: Filter pencarian spesifik per kolom tabel --}}
                        <tr id="header-filter" class="bg-light">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari nama kelas...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari kategori...">
                            </th>
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
    @include('mods.admin.kelas.atc.kelas-data-atc')
</div>

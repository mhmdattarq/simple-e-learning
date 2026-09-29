<div>
    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Data Evaluasi &amp; Kuis</h5>
            <p class="text-muted mb-0">Manajemen evaluasi pembelajaran, kuis bab, dan ujian kelulusan per kelas diklat.
            </p>
        </div>
    </div>

    {{-- Main Card with Table (Master Course Level) --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div
            class="card-header bg-white pt-20 pb-0 px-20 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-file-list-3-fill text-simple fs-5"></i>
                <h6 class="fw-bold text-dark mb-0 fs-6">Daftar Evaluasi Kelas</h6>
            </div>
        </div>

        {{-- Kontainer tabel dengan wire:ignore agar render DOM DataTables tidak terganggu siklus Livewire --}}
        <div class="card-body p-20" wire:ignore>
            <div class="table-responsive">
                <table id="tableEvaluasi" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th style="width: 24%;">Nama Kelas</th>
                            <th class="text-center" style="width: 18%;">Total Soal</th>
                            <th class="text-center" style="width: 22%;">Partisipasi</th>
                            <th class="text-center" style="width: 28%;">Evaluasi</th>
                        </tr>
                        {{-- Thead Kedua: Filter pencarian spesifik per kolom tabel --}}
                        <tr id="header-filter" class="bg-light">
                            <th></th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari nama kelas...">
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
    @include('mods.admin.evaluasi.atc.evaluasi-data-atc')
</div>

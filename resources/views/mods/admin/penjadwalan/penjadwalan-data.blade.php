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

        {{-- Kontainer tabel dengan wire:ignore agar render DOM DataTables tidak terganggu siklus Livewire --}}
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
                        {{-- Thead Kedua: Filter pencarian spesifik per kolom tabel --}}
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

    {{-- Modal Pembatalan Sesi Jadwal (Pencatatan Alasan Pembatalan) --}}
    <div class="modal fade" id="modalCancelSchedule" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 520px;">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header border-0 pb-0 pt-20 px-24 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger"
                            style="width: 38px; height: 38px;">
                            <i class="ri-close-circle-line fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Batalkan Sesi Pelatihan</h6>
                            <small class="text-muted" style="font-size: 12px;">Sesi yang dibatalkan tidak dihapus permanen untuk audit riwayat.</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form wire:submit="submitCancel">
                    <div class="modal-body p-24">
                        <div class="alert alert-warning border-0 d-flex align-items-start gap-2 fs-8 py-2 px-3 radius-8 mb-3">
                            <i class="ri-alert-line fs-6 mt-1 flex-shrink-0 text-warning"></i>
                            <div>
                                Anda akan membatalkan sesi: <strong>{{ $cancelScheduleTitle }}</strong>.
                                Sesi ini tidak dapat dipresensi lagi setelah dibatalkan.
                            </div>
                        </div>

                        <div>
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Alasan Pembatalan Sesi <span class="text-danger">*</span>
                            </label>
                            <textarea wire:model="cancellationReason" rows="3"
                                class="form-control text-xs @error('cancellationReason') is-invalid @enderror"
                                placeholder="Contoh: Narasumber berhalangan hadir mendadak / dialihkan ke sesi pekan depan..."></textarea>
                            @error('cancellationReason')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 px-24 pb-20 d-flex align-items-center justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">
                            Tutup
                        </button>
                        <button type="submit" class="btn btn-sm btn-danger px-3 d-inline-flex align-items-center gap-1"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submitCancel">
                                <i class="ri-close-circle-line"></i> Konfirmasi Pembatalan
                            </span>
                            <span wire:loading wire:target="submitCancel">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('mods.admin.penjadwalan.atc.penjadwalan-data-atc')

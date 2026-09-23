<div>
    {{-- Header & Aksi Tambah --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Tahap 4: Penjadwalan Sesi Pelatihan</h5>
            <p class="text-muted mb-0">Manajemen jadwal kelas, penetapan ruangan fisik/tautan daring Zoom, tanggal pelaksanaan, dan mentor pengampu.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-simple-gold d-inline-flex align-items-center gap-2" wire:click="openCreateModal">
                <i class="ri-add-line fs-5"></i>
                <span>Tambah Jadwal Sesi</span>
            </button>
        </div>
    </div>

    {{-- Main Card with Table (Identical Structure to Perencanaan & Pendaftaran) --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div class="card-header bg-white pt-20 pb-0 px-20 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
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
                                <input type="text" class="form-control form-control-sm search-col-dt" placeholder="Cari judul sesi...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt" placeholder="Cari pelatihan...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt" placeholder="Cari tanggal...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt" placeholder="Cari mentor...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt" placeholder="Cari lokasi...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt" placeholder="Cari status...">
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

    {{-- MODAL FORM JADWAL SESI (CREATE & EDIT) --}}
    <div class="modal fade" id="modalScheduleForm" tabindex="-1" role="dialog" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 620px;">
            <div class="modal-content border-0 shadow-lg radius-20 overflow-hidden bg-white">
                <div class="modal-header border-0 pb-0 pt-24 px-28 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; background-color: #f1f5f9; color: #071a33;">
                            <i class="ri-calendar-todo-line fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">{{ $editId ? 'Edit Jadwal Sesi Pelatihan' : 'Tambah Jadwal Sesi Baru' }}</h6>
                            <small class="text-muted" style="font-size: 12px;">Tahap 4: Penjadwalan Agenda Pembelajaran ASN</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="font-size: 11px;"></button>
                </div>

                <div class="modal-body p-28 pt-20">
                    {{-- Alert Banner jika ada konflik anti-bentrok --}}
                    @if ($conflictError)
                        <div class="alert alert-danger d-flex align-items-start gap-2 mb-3 py-2 px-3 radius-10" style="font-size: 12.5px;">
                            <i class="ri-error-warning-fill fs-5 mt-1"></i>
                            <div>
                                <strong>Peringatan Jadwal Bentrok!</strong>
                                <div>{{ $conflictError }}</div>
                            </div>
                        </div>
                    @endif

                    <form wire:submit="save">
                        {{-- 1. Pilihan Pelatihan --}}
                        <div class="mb-3">
                            <label for="course_id" class="form-label fw-semibold text-dark fs-7">
                                Program Pelatihan <span class="text-danger">*</span>
                            </label>
                            <select id="course_id" wire:model="form.course_id" class="form-select form-select-sm radius-8 @error('form.course_id') is-invalid @enderror" style="font-size: 13px;">
                                <option value="">-- Pilih Program Pelatihan --</option>
                                @foreach ($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->title }} ({{ ucfirst($c->method) }})</option>
                                @endforeach
                            </select>
                            @error('form.course_id')
                                <div class="invalid-feedback text-xs">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 2. Judul Sesi / Agenda Materi --}}
                        <div class="mb-3">
                            <label for="session_title" class="form-label fw-semibold text-dark fs-7">
                                Judul Sesi / Materi Pembelajaran <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="session_title" wire:model="form.session_title"
                                class="form-control form-control-sm radius-8 @error('form.session_title') is-invalid @enderror"
                                placeholder="Contoh: Orientasi Kebijakan ASN BerAKHLAK" style="font-size: 13px;">
                            @error('form.session_title')
                                <div class="invalid-feedback text-xs">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 3. Tanggal & Jam Pelaksanaan --}}
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label for="session_date" class="form-label fw-semibold text-dark fs-7">
                                    Tanggal Sesi <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="session_date" wire:model="form.session_date"
                                    class="form-control form-control-sm radius-8 @error('form.session_date') is-invalid @enderror" style="font-size: 13px;">
                                @error('form.session_date')
                                    <div class="invalid-feedback text-xs">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="start_time" class="form-label fw-semibold text-dark fs-7">
                                    Jam Mulai <span class="text-danger">*</span>
                                </label>
                                <input type="time" id="start_time" wire:model="form.start_time"
                                    class="form-control form-control-sm radius-8 @error('form.start_time') is-invalid @enderror" style="font-size: 13px;">
                                @error('form.start_time')
                                    <div class="invalid-feedback text-xs">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="end_time" class="form-label fw-semibold text-dark fs-7">
                                    Jam Selesai <span class="text-danger">*</span>
                                </label>
                                <input type="time" id="end_time" wire:model="form.end_time"
                                    class="form-control form-control-sm radius-8 @error('form.end_time') is-invalid @enderror" style="font-size: 13px;">
                                @error('form.end_time')
                                    <div class="invalid-feedback text-xs">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- 4. Pilihan Mentor Pengampu --}}
                        <div class="mb-3">
                            <label for="mentor_id" class="form-label fw-semibold text-dark fs-7">
                                Narasumber / Mentor Pengampu <span class="text-danger">*</span>
                            </label>
                            <select id="mentor_id" wire:model="form.mentor_id" class="form-select form-select-sm radius-8 @error('form.mentor_id') is-invalid @enderror" style="font-size: 13px;">
                                <option value="">-- Pilih Mentor Pengampu --</option>
                                @foreach ($mentors as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }} (NIP: {{ $m->nip ?: '-' }}) — {{ ucfirst($m->role->value ?? $m->role) }}</option>
                                @endforeach
                            </select>
                            @error('form.mentor_id')
                                <div class="invalid-feedback text-xs">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 5. Lokasi Fisik / URL Tautan Zoom --}}
                        <div class="mb-3">
                            <label for="room_or_link" class="form-label fw-semibold text-dark fs-7">
                                Ruangan Kelas Fisik atau Tautan Daring <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="room_or_link" wire:model="form.room_or_link"
                                class="form-control form-control-sm radius-8 @error('form.room_or_link') is-invalid @enderror"
                                placeholder="Contoh: Aula Gedung Diklat BKPSDM atau https://zoom.us/j/12345678" style="font-size: 13px;">
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                Masukkan nama ruangan fisik (misal: Aula BKPSDM) untuk validasi anti-bentrok ruangan, atau masukkan tautan video conference (Zoom / Google Meet).
                            </small>
                            @error('form.room_or_link')
                                <div class="invalid-feedback text-xs">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- 6. Status Sesi --}}
                        <div class="mb-4">
                            <label for="schedule_status" class="form-label fw-semibold text-dark fs-7">
                                Status Pelaksanaan
                            </label>
                            <select id="schedule_status" wire:model="form.status" class="form-select form-select-sm radius-8 @error('form.status') is-invalid @enderror" style="font-size: 13px;">
                                <option value="scheduled">Terjadwal (Akan Datang)</option>
                                <option value="ongoing">Sedang Berlangsung</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                            @error('form.status')
                                <div class="invalid-feedback text-xs">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Modal Action Buttons --}}
                        <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                            <button type="button" class="btn btn-light px-20 py-10 radius-10" data-bs-dismiss="modal" style="font-size: 13.5px;">
                                Batal
                            </button>
                            <button type="submit" class="btn btn-simple px-24 py-10 radius-10 d-inline-flex align-items-center gap-2" style="font-size: 13.5px;" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ri-check-line"></i> {{ $editId ? 'Perbarui Jadwal' : 'Simpan Jadwal' }}
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span> Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('mods.admin.penjadwalan.atc.penjadwalan-data-atc')

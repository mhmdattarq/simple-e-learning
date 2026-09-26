<div>
    {{-- Header Tahap 2: Pendaftaran --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Tahap 2: Pendaftaran Diklat</h5>
            <p class="text-muted mb-0">Manajemen pengaturan periode pendaftaran diklat, rekapitulasi usulan peserta ASN,
                dan status berkas rekomendasi.</p>
        </div>
    </div>

    {{-- A. Pengaturan Periode Pendaftaran Pelatihan --}}
    <div class="card simpel-card border-0 shadow-sm radius-16 mb-24">
        <div
            class="card-header bg-white pt-20 pb-16 px-20 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-calendar-event-line text-simple fs-5"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Pengaturan Periode Pendaftaran Diklat</h6>
                    <small class="text-muted">Pilih pelatihan berstatus Disetujui oleh pimpinan untuk menentukan batas
                        waktu pendaftaran peserta dan membuka pendaftaran.</small>
                </div>
            </div>
            @if ($courseStats)
                <div>
                    <span class="badge {{ $courseStats['status_badge'] }} px-3 py-1_5 fs-7">
                        <i class="ri-checkbox-circle-line me-1"></i> Status: {{ $courseStats['status_label'] }}
                    </span>
                </div>
            @endif
        </div>

        <div class="card-body p-20">
            <div class="row g-3 align-items-center">
                <div class="col-12">
                    <label class="form-label fw-semibold text-dark fs-7">
                        Pilih Program Pelatihan (Status Disetujui) <span class="text-danger">*</span>
                    </label>
                    <select wire:model.live="selectedCourseId" id="selectCoursePeriod"
                        class="form-select @error('selectedCourseId') is-invalid @enderror">
                        <option value="">-- Pilih Pelatihan (Disetujui) --</option>
                        @foreach ($settingCourses as $courseItem)
                            <option value="{{ $courseItem->id }}">
                                [{{ $courseItem->code }}] {{ $courseItem->title }} — (Disetujui)
                            </option>
                        @endforeach
                    </select>
                    @error('selectedCourseId')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @if ($settingCourses->isEmpty())
                        <div class="text-muted fs-8 mt-2">
                            <i class="ri-information-line me-1 text-primary"></i> Belum ada pelatihan berstatus
                            Disetujui yang dapat dibuka pendaftarannya saat ini.
                        </div>
                    @endif
                </div>
            </div>

            @if ($courseStats)
                {{-- Summary Cards & Quota --}}
                <div class="row g-3 mt-1">
                    <div class="{{ $courseStats['is_permanent'] ? 'col-md-3 col-6' : 'col-md-3 col-6' }}">
                        <div class="p-3 rounded-3 bg-light border border-simpel">
                            <span class="text-muted fs-8 d-block mb-1">Kode & Tipe Diklat</span>
                            <span
                                class="fw-bold text-dark font-monospace fs-7 d-block">{{ $courseStats['code'] }}</span>
                            <small
                                class="text-muted fs-8">{{ $courseStats['is_permanent'] ? 'Mandiri (Buka Terus)' : 'Batch Terjadwal' }}</small>
                        </div>
                    </div>
                    @if (!$courseStats['is_permanent'])
                        <div class="col-md-3 col-6">
                            <div class="p-3 rounded-3 bg-light border border-simpel">
                                <span class="text-muted fs-8 d-block mb-1">Jadwal Pelatihan</span>
                                <span class="fw-bold text-dark fs-7 d-block">
                                    {{ $courseStats['start_date'] ?? 'Fleksibel' }}
                                </span>
                                <small class="text-muted fs-8">s.d
                                    {{ $courseStats['end_date'] ?? 'Fleksibel' }}</small>
                            </div>
                        </div>
                    @endif
                    <div class="{{ $courseStats['is_permanent'] ? 'col-md-3 col-4' : 'col-md-2 col-4' }}">
                        <div class="p-3 rounded-3 bg-light border border-simpel text-center">
                            <span class="text-muted fs-8 d-block mb-1">Total Kuota</span>
                            <span class="fw-bold text-dark fs-5">{{ $courseStats['quota'] }}</span>
                            <small class="text-muted fs-8 d-block">Peserta</small>
                        </div>
                    </div>
                    <div class="{{ $courseStats['is_permanent'] ? 'col-md-3 col-4' : 'col-md-2 col-4' }}">
                        <div
                            class="p-3 rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 text-center">
                            <span class="text-primary fs-8 d-block mb-1">Terdaftar</span>
                            <span class="fw-bold text-primary fs-5">{{ $courseStats['enrolled_count'] }}</span>
                            <small class="text-primary fs-8 d-block">Peserta</small>
                        </div>
                    </div>
                    <div class="{{ $courseStats['is_permanent'] ? 'col-md-3 col-4' : 'col-md-2 col-4' }}">
                        <div
                            class="p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 text-center">
                            <span class="text-success fs-8 d-block mb-1">Sisa Kuota</span>
                            <span class="fw-bold text-success fs-5">{{ $courseStats['remaining_quota'] }}</span>
                            <small class="text-success fs-8 d-block">Slot Kosong</small>
                        </div>
                    </div>
                </div>

                @if ($canManageRegistration)
                    {{-- Form Periode Pendaftaran & Action Buttons --}}
                    <div class="p-3 rounded-3 bg-white border border-simpel mt-3">
                        <div class="row g-3 align-items-end">
                            @if (!$courseStats['is_permanent'])
                                {{-- Kolom Periode Pelatihan (Dari Perencanaan) - Read Only --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold text-dark fs-7 mb-1">
                                        <i class="ri-calendar-event-line text-warning me-1"></i> Periode Pelatihan
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted border-end-0 py-2"><i
                                                class="ri-lock-line"></i></span>
                                        <input type="text"
                                            class="form-control bg-light text-dark fw-semibold border-start-0 py-2"
                                            value="{{ $courseStats['start_date'] }} s.d {{ $courseStats['end_date'] }}"
                                            readonly disabled
                                            title="Periode jadwal pelaksanaan pelatihan yang sudah ditetapkan pada perencanaan">
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold text-dark fs-7 mb-1">
                                        <i class="ri-calendar-line text-primary me-1"></i> Tanggal Buka Pendaftaran
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local"
                                        class="form-control py-2 @error('registration_open_at') is-invalid @enderror"
                                        wire:model="registration_open_at">
                                    @error('registration_open_at')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold text-dark fs-7 mb-1">
                                        <i class="ri-calendar-close-line text-danger me-1"></i> Tanggal Tutup
                                        Pendaftaran <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local"
                                        class="form-control py-2 @error('registration_close_at') is-invalid @enderror"
                                        wire:model="registration_close_at">
                                    @error('registration_close_at')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-lg-3 col-md-6">
                                    <button type="button"
                                        class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm py-2"
                                        wire:click="openPeriod" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="openPeriod">
                                            <i class="ri-door-open-line fs-6"></i> Buka Pendaftaran
                                        </span>
                                        <span wire:loading wire:target="openPeriod">
                                            <span class="spinner-border spinner-border-sm me-1"></span> Membuka...
                                        </span>
                                    </button>
                                </div>

                                <div class="col-12 mt-2 pt-2 border-top">
                                    <div class="d-flex align-items-center gap-2 text-muted fs-8">
                                        <i class="ri-information-line text-primary fs-7"></i>
                                        <span>
                                            Pelatihan batch ini dijadwalkan mulai
                                            <strong>{{ $courseStats['start_date'] }}</strong>. Pastikan periode
                                            pendaftaran selesai sebelum tanggal mulai pelatihan.
                                        </span>
                                    </div>
                                </div>
                            @else
                                {{-- Jika Pelatihan Tipe Mandiri / Buka Terus (Permanent), tidak ada rentang tanggal batch --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark fs-7 mb-1">
                                        <i class="ri-calendar-line text-primary me-1"></i> Tanggal Buka Pendaftaran
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local"
                                        class="form-control py-2 @error('registration_open_at') is-invalid @enderror"
                                        wire:model="registration_open_at">
                                    @error('registration_open_at')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-dark fs-7 mb-1">
                                        <i class="ri-calendar-close-line text-danger me-1"></i> Tanggal Tutup
                                        Pendaftaran <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local"
                                        class="form-control py-2 @error('registration_close_at') is-invalid @enderror"
                                        wire:model="registration_close_at">
                                    @error('registration_close_at')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <button type="button"
                                        class="btn btn-primary w-100 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm py-2"
                                        wire:click="openPeriod" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="openPeriod">
                                            <i class="ri-door-open-line fs-6"></i> Buka Pendaftaran
                                        </span>
                                        <span wire:loading wire:target="openPeriod">
                                            <span class="spinner-border spinner-border-sm me-1"></span> Membuka...
                                        </span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="alert alert-info d-flex align-items-center gap-2 mt-3 mb-0 fs-8 py-2">
                        <i class="ri-information-line fs-6"></i>
                        <span>Anda masuk sebagai {{ auth()->user()?->role?->label() ?? 'Petugas' }}. Pengaturan
                            pembukaan dan penutupan periode pendaftaran hanya dapat diubah oleh Admin Diklat.</span>
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Main Card with Table & Multi-Filter --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div
            class="card-header bg-white pt-20 pb-16 px-20 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-user-add-fill text-simple fs-5"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Daftar Pendaftaran Peserta (Rekapitulasi ASN)</h6>
                    <small class="text-muted">Daftar usulan pendaftaran peserta seluruh pelatihan, verifikasi berkas,
                        dan status registrasi.</small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('pendaftaran.export') }}" target="_blank"
                    class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1 shadow-none"
                    title="Unduh Rekap Berkas Pendaftaran ASN">
                    <i class="ri-file-excel-2-line fs-6"></i>
                    <span>Unduh Rekap Berkas (CSV)</span>
                </a>
            </div>
        </div>

        {{-- Kontainer tabel dengan wire:ignore agar render DOM DataTables tidak terganggu siklus Livewire --}}
        <div class="card-body p-20" wire:ignore>
            <div class="table-responsive">
                <table id="tablePendaftaran" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th style="width: 150px;">No. Registrasi</th>
                            <th>Nama Peserta</th>
                            <th>NIP & Instansi (OPD)</th>
                            <th>Nama Pelatihan</th>
                            <th class="text-center" style="width: 130px;">Tgl Daftar</th>
                            <th class="text-center" style="width: 110px;">Status</th>
                            <th class="text-center" style="width: 90px;">Aksi</th>
                        </tr>
                        {{-- Thead Kedua: Filter pencarian spesifik per kolom tabel --}}
                        <tr id="header-filter" class="bg-light">
                            <th></th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari reg...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari nama...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari OPD / NIP...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari pelatihan...">
                            </th>
                            <th></th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari status...">
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Detail Pendaftaran --}}
    <div wire:ignore.self class="modal fade" id="modalDetailPendaftaran" tabindex="-1"
        aria-labelledby="modalDetailPendaftaranLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center bg-light rounded-circle"
                            style="width: 46px; height: 46px;">
                            <i class="ri-user-search-line fs-4 text-simple"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-navy mb-0" id="modalDetailPendaftaranLabel">
                                Detail Usulan Pendaftaran
                            </h5>
                            <small class="text-muted">Nomor Registrasi: <span
                                    class="fw-bold text-dark">{{ $selectedDetail['registration_number'] ?? '-' }}</span></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    @if ($selectedDetail)
                        {{-- Status Banner --}}
                        <div
                            class="p-3 rounded-3 mb-4 bg-light border border-simpel d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <span class="text-muted fs-8 d-block">Status Registrasi</span>
                                <span class="badge {{ $selectedDetail['status_badge'] }} fs-7 px-3 py-1 mt-1">
                                    {{ $selectedDetail['status_label'] }}
                                </span>
                            </div>
                            <div class="text-md-end">
                                <span class="text-muted fs-8 d-block">Waktu Mendaftar</span>
                                <span class="fw-semibold text-dark fs-7">{{ $selectedDetail['enrolled_at'] }}</span>
                            </div>
                        </div>

                        {{-- Section: Profil Peserta ASN --}}
                        <h6 class="fw-bold text-navy mb-3 d-flex align-items-center gap-2">
                            <i class="ri-id-card-line text-gold"></i>
                            Biodata & Kepegawaian ASN
                        </h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle fs-7 mb-0">
                                <tbody>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium" style="width: 30%;">Nama Lengkap
                                        </td>
                                        <td class="fw-bold text-dark">{{ $selectedDetail['user_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">NIP</td>
                                        <td class="fw-bold font-monospace">{{ $selectedDetail['user_nip'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">Instansi / Asal OPD</td>
                                        <td>{{ $selectedDetail['user_opd'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">Jabatan & Golongan</td>
                                        <td>{{ $selectedDetail['user_position'] }} —
                                            ({{ $selectedDetail['user_rank'] }})</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">Kontak (WhatsApp & Email)</td>
                                        <td>
                                            <i class="ri-whatsapp-line text-success me-1"></i>
                                            {{ $selectedDetail['user_phone'] }}
                                            <span class="mx-2 text-muted">|</span>
                                            <i class="ri-mail-line text-primary me-1"></i>
                                            {{ $selectedDetail['user_email'] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Section: Informasi Program Pelatihan --}}
                        <h6 class="fw-bold text-navy mb-3 d-flex align-items-center gap-2">
                            <i class="ri-book-open-line text-gold"></i>
                            Program Pelatihan yang Dipilih
                        </h6>
                        <div class="p-3 rounded-3 bg-light border border-simpel mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span
                                    class="badge bg-navy text-white fs-8">{{ $selectedDetail['course_code'] }}</span>
                                <span class="text-muted fs-8">{{ $selectedDetail['course_type'] }} &bull;
                                    {{ $selectedDetail['course_method'] }}</span>
                            </div>
                            <h6 class="fw-bold text-navy mb-1">{{ $selectedDetail['course_title'] }}</h6>
                            <span class="text-muted fs-8">{{ $selectedDetail['course_category'] }}</span>
                        </div>

                        {{-- Section: Berkas Dokumen Usulan --}}
                        <h6 class="fw-bold text-navy mb-3 d-flex align-items-center gap-2">
                            <i class="ri-file-text-line text-gold"></i>
                            Dokumen Surat Rekomendasi / Usulan Atasan
                        </h6>
                        <div
                            class="p-3 rounded-3 bg-white border border-simpel d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3 fs-3">
                                    <i class="ri-file-pdf-line"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-7">Surat Rekomendasi / Penugasan Diklat (PDF)
                                    </div>
                                    <small class="text-muted">Berkas resmi bertanda tangan pejabat pembina
                                        kepegawaian</small>
                                </div>
                            </div>
                            <div>
                                @if ($selectedDetail['recommendation_letter_url'])
                                    <a href="{{ $selectedDetail['recommendation_letter_url'] }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-semibold">
                                        <i class="ri-download-2-line me-1"></i> Buka Berkas PDF
                                    </a>
                                @else
                                    <span class="badge bg-secondary text-white">Tidak ada berkas diunggah</span>
                                @endif
                            </div>
                        </div>

                        {{-- Section: Hasil Verifikasi & Verifikator --}}
                        <h6 class="fw-bold text-navy mb-3 d-flex align-items-center gap-2">
                            <i class="ri-shield-check-line text-gold"></i>
                            Hasil Pemeriksaan & Catatan Verifikasi
                        </h6>
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered align-middle fs-7 mb-0">
                                <tbody>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium" style="width: 30%;">Petugas
                                            Verifikator</td>
                                        <td class="fw-bold text-dark">
                                            <i class="ri-user-star-line text-primary me-1"></i>
                                            {{ $selectedDetail['verifier_name'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">Waktu Verifikasi</td>
                                        <td>{{ $selectedDetail['verified_at'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">Catatan Verifikasi</td>
                                        <td>
                                            @if ($selectedDetail['verification_notes'] && $selectedDetail['verification_notes'] !== '-')
                                                <span
                                                    class="text-dark">{{ $selectedDetail['verification_notes'] }}</span>
                                            @else
                                                <span class="text-muted fst-italic">Belum ada catatan verifikasi</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if ($selectedDetail['notes'])
                            <div class="mt-3 p-3 rounded-3 bg-warning bg-opacity-10 border border-warning">
                                <div class="fw-bold text-dark fs-7 mb-1"><i
                                        class="ri-information-line text-warning me-1"></i> Catatan Pemohon:</div>
                                <p class="text-muted fs-8 mb-0">{{ $selectedDetail['notes'] }}</p>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="modal-footer border-0 pt-0 px-4 pb-4 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-light px-4 rounded-pill"
                        data-bs-dismiss="modal">Tutup</button>
                    @if ($selectedDetail && (auth()->user()?->isVerifikator() || auth()->user()?->isAdmin()))
                        <a href="{{ route('verifikasi.periksa', $selectedDetail['id']) }}" wire:navigate
                            class="btn btn-primary px-4 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm"
                            data-bs-dismiss="modal">
                            <i class="ri-shield-check-line"></i> Periksa / Verifikasi Berkas
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Script ATC (Action & Table Controller) --}}
    @include('mods.admin.pendaftaran.atc.pendaftaran-data-atc')
</div>

<div>
    {{-- Header Tahap 2: Pendaftaran --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Tahap 2: Pendaftaran Diklat</h5>
            <p class="text-muted mb-0">Manajemen usulan dan pendaftaran peserta diklat ASN, pemeriksaan berkas rekomendasi, dan status registrasi.</p>
        </div>
    </div>

    {{-- Main Card with Table --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div class="card-header bg-white pt-20 pb-0 px-20 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-user-add-fill text-simple fs-5"></i>
                <h6 class="fw-bold text-dark mb-0 fs-6">Daftar Pendaftaran Peserta (Rekapitulasi ASN)</h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('pendaftaran.export') }}" target="_blank" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1 shadow-none" title="Unduh Rekap Berkas Pendaftaran ASN">
                    <i class="ri-file-excel-2-line fs-6"></i>
                    <span>Unduh Rekap Berkas</span>
                </a>
            </div>
        </div>

        {{-- Kontainer tabel dengan wire:ignore agar render DOM DataTables tidak terganggu siklus Livewire --}}
        <div class="card-body p-20" wire:ignore>
            <div class="table-responsive">
                <table id="tablePendaftaran" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 40px;">
                                <input class="form-check-input check-data-all" type="checkbox">
                            </th>
                            <th class="text-center" style="width: 70px;">Aksi</th>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th style="width: 150px;">No. Registrasi</th>
                            <th>Nama Peserta</th>
                            <th>NIP & Instansi (OPD)</th>
                            <th>Nama Pelatihan</th>
                            <th class="text-center" style="width: 110px;">Surat Tugas</th>
                            <th class="text-center" style="width: 130px;">Tgl Daftar</th>
                            <th class="text-center" style="width: 110px;">Status</th>
                        </tr>
                        {{-- Thead Kedua: Filter pencarian spesifik per kolom tabel --}}
                        <tr id="header-filter" class="bg-light">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt" placeholder="Cari reg...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt" placeholder="Cari nama...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt" placeholder="Cari OPD / NIP...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt" placeholder="Cari pelatihan...">
                            </th>
                            <th></th>
                            <th></th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt" placeholder="Cari status...">
                            </th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Detail Pendaftaran --}}
    <div wire:ignore.self class="modal fade" id="modalDetailPendaftaran" tabindex="-1" aria-labelledby="modalDetailPendaftaranLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 46px; height: 46px;">
                            <i class="ri-user-search-line fs-4 text-simple"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-navy mb-0" id="modalDetailPendaftaranLabel">
                                Detail Usulan Pendaftaran
                            </h5>
                            <small class="text-muted">Nomor Registrasi: <span class="fw-bold text-dark">{{ $selectedDetail['registration_number'] ?? '-' }}</span></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    @if ($selectedDetail)
                        {{-- Status Banner --}}
                        <div class="p-3 rounded-3 mb-4 bg-light border border-simpel d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <span class="text-muted fs-8 d-block">Status Verifikasi Pendaftaran</span>
                                <span class="badge {{ $selectedDetail['status_badge'] }} fs-7 px-3 py-1 mt-1">
                                    {{ $selectedDetail['status_label'] }}
                                </span>
                            </div>
                            <div class="text-md-end">
                                <span class="text-muted fs-8 d-block">Waktu Pendaftaran</span>
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
                                        <td class="bg-light text-muted fw-medium" style="width: 30%;">Nama Lengkap</td>
                                        <td class="fw-bold text-dark">{{ $selectedDetail['user_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">NIP</td>
                                        <td class="fw-bold">{{ $selectedDetail['user_nip'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">Instansi / Asal OPD</td>
                                        <td>{{ $selectedDetail['user_opd'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">Jabatan & Golongan</td>
                                        <td>{{ $selectedDetail['user_position'] }} — ({{ $selectedDetail['user_rank'] }})</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">Kontak (WhatsApp & Email)</td>
                                        <td>
                                            <i class="ri-whatsapp-line text-success me-1"></i> {{ $selectedDetail['user_phone'] }}
                                            <span class="mx-2 text-muted">|</span>
                                            <i class="ri-mail-line text-primary me-1"></i> {{ $selectedDetail['user_email'] }}
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
                                <span class="badge bg-navy text-white fs-8">{{ $selectedDetail['course_code'] }}</span>
                                <span class="text-muted fs-8">{{ $selectedDetail['course_type'] }} &bull; {{ $selectedDetail['course_method'] }}</span>
                            </div>
                            <h6 class="fw-bold text-navy mb-1">{{ $selectedDetail['course_title'] }}</h6>
                            <span class="text-muted fs-8">{{ $selectedDetail['course_category'] }}</span>
                        </div>

                        {{-- Section: Berkas Dokumen Usulan --}}
                        <h6 class="fw-bold text-navy mb-3 d-flex align-items-center gap-2">
                            <i class="ri-file-text-line text-gold"></i>
                            Dokumen Surat Rekomendasi / Usulan Atasan
                        </h6>
                        <div class="p-3 rounded-3 bg-white border border-simpel d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3 fs-3">
                                    <i class="ri-file-pdf-line"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-7">Surat Rekomendasi / Penugasan Diklat (PDF)</div>
                                    <small class="text-muted">Berkas resmi bertanda tangan pejabat pembina kepegawaian</small>
                                </div>
                            </div>
                            <div>
                                @if ($selectedDetail['recommendation_letter_url'])
                                    <a href="{{ $selectedDetail['recommendation_letter_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-semibold">
                                        <i class="ri-download-2-line me-1"></i> Unduh Berkas
                                    </a>
                                @else
                                    <span class="badge bg-secondary text-white">Tidak ada berkas diunggah</span>
                                @endif
                            </div>
                        </div>

                        @if ($selectedDetail['notes'])
                            <div class="mt-4 p-3 rounded-3 bg-warning bg-opacity-10 border border-warning">
                                <div class="fw-bold text-dark fs-7 mb-1"><i class="ri-information-line text-warning me-1"></i> Catatan Pendaftaran:</div>
                                <p class="text-muted fs-8 mb-0">{{ $selectedDetail['notes'] }}</p>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Script ATC (Action & Table Controller) --}}
    @include('mods.admin.pendaftaran.atc.pendaftaran-data-atc')
</div>

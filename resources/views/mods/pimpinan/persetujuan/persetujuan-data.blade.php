<div>
    {{-- Header Modul Persetujuan --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Persetujuan Rencana Pelatihan</h5>
            <p class="text-muted mb-0">Pemeriksaan usulan program diklat ASN, kelayakan sasaran dan anggaran, serta penetapan keputusan persetujuan eksekutif.</p>
        </div>
    </div>

    {{-- Kartu Ringkasan Metrik Persetujuan --}}
    <div class="row g-3 mb-24">
        <div class="col-md-4 col-sm-6">
            <div class="card simpel-card border-0 shadow-sm radius-16 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="ri-time-line fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-8 d-block mb-1">Menunggu Persetujuan</span>
                        <h4 class="fw-bold text-dark mb-0">{{ $summary['pending_count'] ?? 0 }}</h4>
                        <small class="text-muted fs-8">Usulan Program Baru</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="card simpel-card border-0 shadow-sm radius-16 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="ri-checkbox-circle-line fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-8 d-block mb-1">Disetujui Bulan Ini</span>
                        <h4 class="fw-bold text-dark mb-0">{{ $summary['approved_month_count'] ?? 0 }}</h4>
                        <small class="text-muted fs-8">Siap Buka Pendaftaran</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12">
            <div class="card simpel-card border-0 shadow-sm radius-16 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="ri-broadcast-line fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted fs-8 d-block mb-1">Total Diklat Aktif / Dibuka</span>
                        <h4 class="fw-bold text-dark mb-0">{{ $summary['active_count'] ?? 0 }}</h4>
                        <small class="text-muted fs-8">Sedang Berjalan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card with Table --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div class="card-header bg-white pt-20 pb-16 px-20 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-file-shield-2-line text-simple fs-5"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Antrean Usulan Rencana Pelatihan</h6>
                    <small class="text-muted">Daftar program pelatihan berstatus Diajukan yang membutuhkan verifikasi dan persetujuan Pimpinan.</small>
                </div>
            </div>
        </div>

        <div class="card-body p-20" wire:ignore>
            <div class="table-responsive">
                <table id="tablePersetujuan" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th class="text-center" style="width: 90px;">Aksi</th>
                            <th style="width: 130px;">Kode Diklat</th>
                            <th style="min-width: 260px;">Program Pelatihan</th>
                            <th class="text-center" style="width: 120px;">Tipe & Metode</th>
                            <th style="width: 180px;">Jadwal & Kuota</th>
                            <th style="width: 170px;">Pengusul & Waktu</th>
                            <th class="text-center" style="width: 100px;">Status</th>
                        </tr>
                        <tr id="header-filter" class="bg-light">
                            <th></th>
                            <th></th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt" placeholder="Cari kode...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt" placeholder="Cari nama diklat / topik...">
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Tinjau Usulan & Keputusan Persetujuan --}}
    <div wire:ignore.self class="modal fade" id="modalTinjauRencana" tabindex="-1" aria-labelledby="modalTinjauRencanaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 48px; height: 48px;">
                            <i class="ri-file-shield-line fs-3 text-simple"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-navy mb-0" id="modalTinjauRencanaLabel">
                                Tinjau Usulan Perencanaan Pelatihan
                            </h5>
                            <small class="text-muted">Kode: <span class="fw-bold text-dark font-monospace">{{ $selectedCourse['code'] ?? '-' }}</span></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    @if ($selectedCourse)
                        {{-- Status Banner --}}
                        <div class="p-3 rounded-3 mb-4 bg-light border border-simpel d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <span class="text-muted fs-8 d-block">Status Usulan</span>
                                <span class="badge bg-info text-white fs-7 px-3 py-1 mt-1">
                                    <i class="ri-send-plane-line me-1"></i> Diajukan oleh {{ $selectedCourse['creator_name'] }}
                                </span>
                            </div>
                            <div class="text-md-end">
                                <span class="text-muted fs-8 d-block">Diajukan Pada</span>
                                <span class="fw-semibold text-dark fs-7">{{ $selectedCourse['submitted_at'] }}</span>
                            </div>
                        </div>

                        {{-- Section 1: Informasi Pokok Program --}}
                        <h6 class="fw-bold text-navy mb-3 d-flex align-items-center gap-2">
                            <i class="ri-book-open-line text-gold"></i>
                            Spesifikasi Program Pelatihan
                        </h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle fs-7 mb-0">
                                <tbody>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium" style="width: 30%;">Nama Pelatihan</td>
                                        <td class="fw-bold text-dark">{{ $selectedCourse['title'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">Kategori & Tipe</td>
                                        <td>{{ $selectedCourse['category_name'] }} &bull; {{ $selectedCourse['type_label'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">Metode & Lokasi</td>
                                        <td>{{ $selectedCourse['method_label'] }} — <span class="text-muted">{{ $selectedCourse['location'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">Jadwal Pelaksanaan</td>
                                        <td class="fw-semibold text-dark">{{ $selectedCourse['start_date'] }} s.d {{ $selectedCourse['end_date'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">Kapasitas Kuota</td>
                                        <td class="fw-bold text-primary">{{ $selectedCourse['quota'] }} Orang Peserta ASN</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Section 2: Sasaran & Anggaran --}}
                        <h6 class="fw-bold text-navy mb-3 d-flex align-items-center gap-2">
                            <i class="ri-money-dollar-circle-line text-gold"></i>
                            Sasaran & Sumber Pembiayaan
                        </h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle fs-7 mb-0">
                                <tbody>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium" style="width: 30%;">Sasaran Peserta</td>
                                        <td>{{ $selectedCourse['target_audience'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted fw-medium">Sumber Anggaran</td>
                                        <td class="fw-semibold">{{ $selectedCourse['budget_source'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Section 3: Deskripsi & Kompetensi --}}
                        <div class="mb-4">
                            <h6 class="fw-bold text-navy mb-2 fs-7">Deskripsi & Tujuan:</h6>
                            <div class="p-3 bg-light rounded-3 border border-simpel fs-8 text-muted mb-3">
                                {{ $selectedCourse['description'] }}
                            </div>

                            <h6 class="fw-bold text-navy mb-2 fs-7">Standar Kompetensi yang Dibangun:</h6>
                            <div class="p-3 bg-light rounded-3 border border-simpel fs-8 text-muted">
                                {{ $selectedCourse['competencies'] }}
                            </div>
                        </div>

                        {{-- Section 4: Dokumen TOR / KAK --}}
                        <div class="p-3 rounded-3 bg-white border border-simpel d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3 fs-3">
                                    <i class="ri-file-pdf-line"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-7">Dokumen Kerangka Acuan Kerja (TOR / KAK)</div>
                                    <small class="text-muted">Dokumen acuan perencanaan yang diunggah oleh pengusul</small>
                                </div>
                            </div>
                            <div>
                                @if ($selectedCourse['tor_url'])
                                    <a href="{{ $selectedCourse['tor_url'] }}" target="_blank" class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-semibold">
                                        <i class="ri-download-2-line me-1"></i> Buka Berkas PDF
                                    </a>
                                @else
                                    <span class="badge bg-secondary text-white">Tidak ada berkas TOR</span>
                                @endif
                            </div>
                        </div>

                        {{-- Section 5: Keputusan Pimpinan --}}
                        <div class="p-3 rounded-3 bg-warning bg-opacity-10 border border-warning">
                            <label class="form-label fw-bold text-dark fs-7 mb-1">
                                <i class="ri-edit-2-line text-warning me-1"></i> Catatan Keputusan / Instruksi Revisi
                            </label>
                            <textarea wire:model="decisionNotes" class="form-control bg-white @error('decisionNotes') is-invalid @enderror" rows="3"
                                placeholder="Tuliskan catatan arahan, pertimbangan persetujuan, atau catatan revisi (wajib diisi jika dikembalikan)..."></textarea>
                            @error('decisionNotes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted fs-8 d-block mt-1">
                                * Catatan opsional jika menyetujui, namun <strong>wajib diisi</strong> jika mengembalikan usulan untuk direvisi oleh Admin Diklat.
                            </small>
                        </div>
                    @endif
                </div>

                <div class="modal-footer border-0 pt-0 px-4 pb-4 d-flex align-items-center justify-content-between">
                    <button type="button" class="btn btn-light px-4 rounded-pill" data-bs-dismiss="modal">Tutup</button>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-danger px-3 rounded-pill d-inline-flex align-items-center gap-1"
                            wire:click="reject" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="reject"><i class="ri-arrow-go-back-line"></i> Kembalikan untuk Revisi</span>
                            <span wire:loading wire:target="reject"><span class="spinner-border spinner-border-sm me-1"></span> Mengembalikan...</span>
                        </button>

                        <button type="button" class="btn btn-success px-4 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm"
                            wire:click="approve" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="approve"><i class="ri-checkbox-circle-line"></i> Setujui Rencana</span>
                            <span wire:loading wire:target="approve"><span class="spinner-border spinner-border-sm me-1"></span> Menyetujui...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script ATC (Action & Table Controller) --}}
    @include('mods.pimpinan.persetujuan.atc.persetujuan-data-atc')
</div>

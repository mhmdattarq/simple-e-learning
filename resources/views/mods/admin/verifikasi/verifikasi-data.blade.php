<div>
    {{-- Header Tahap 3: Verifikasi --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Tahap 3: Verifikasi Berkas Pelatihan ASN</h5>
            <p class="text-muted mb-0">Pemeriksaan keabsahan dokumen usulan, surat rekomendasi atasan, dan penetapan
                keputusan kelayakan calon peserta pelatihan.</p>
        </div>
    </div>

    {{-- Main Card with Table (Identical to Perencanaan, Pendaftaran & Penjadwalan) --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div
            class="card-header bg-white pt-20 pb-0 px-20 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-checkbox-circle-fill text-simple fs-5"></i>
                <h6 class="fw-bold text-dark mb-0 fs-6">Daftar Verifikasi Berkas Peserta (Pemeriksaan ASN)</h6>
            </div>
        </div>

        {{-- Kontainer tabel dengan wire:ignore agar render DOM DataTables tidak terganggu siklus Livewire --}}
        <div class="card-body p-20" wire:ignore>
            <div class="table-responsive">
                <table id="tableVerifikasi" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th style="width: 150px;">No. Registrasi</th>
                            <th>Nama Peserta</th>
                            <th>NIP & Instansi (OPD)</th>
                            <th>Nama Pelatihan</th>
                            <th class="text-center" style="width: 130px;">Tgl Daftar</th>
                            <th class="text-center" style="width: 110px;">Status</th>
                        </tr>
                        {{-- Thead Kedua: Filter pencarian spesifik per kolom tabel --}}
                        <tr id="header-filter" class="bg-light">
                            <th></th>
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
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Aksi Verifikasi Berkas (Persetujuan/Penolakan pendaftaran dan catatan verifikator) --}}
    <div class="modal fade" id="modalVerifikasiAction" tabindex="-1" role="dialog" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 620px;">
            <div class="modal-content border-0 shadow-lg position-relative"
                style="border-radius: 20px; overflow: hidden; background-color: #ffffff;">

                {{-- Modal Header --}}
                <div class="modal-header border-0 pb-0 pt-24 px-28 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; background-color: #e9eff7; color: var(--simpel-navy);">
                            <i class="ri-shield-check-line fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Pemeriksaan & Keputusan Verifikasi</h6>
                            <small class="text-muted" style="font-size: 12px;">No. Registrasi: <span
                                    class="fw-bold text-simple-gold">{{ $selectedVerification['registration_number'] ?? '-' }}</span></small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="font-size: 11px;"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body p-28 pt-16">
                    @if ($selectedVerification)
                        {{-- Ringkasan Peserta --}}
                        <div class="p-16 rounded-12 mb-20"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <small class="text-muted text-xs d-block">Nama Peserta:</small>
                                    <span class="fw-bold text-dark"
                                        style="font-size: 13.5px;">{{ $selectedVerification['user_name'] }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted text-xs d-block">NIP ASN:</small>
                                    <span class="fw-semibold text-dark"
                                        style="font-size: 13px;">{{ $selectedVerification['user_nip'] }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted text-xs d-block">Instansi (OPD):</small>
                                    <span class="text-dark"
                                        style="font-size: 13px;">{{ $selectedVerification['user_opd'] }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted text-xs d-block">Pelatihan Dituju:</small>
                                    <span class="text-dark fw-semibold"
                                        style="font-size: 13px;">{{ $selectedVerification['course_title'] }}</span>
                                </div>
                            </div>

                            {{-- Tombol Buka Berkas Usulan --}}
                            <div
                                class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-file-pdf-2-fill text-danger fs-5"></i>
                                    <span class="text-dark" style="font-size: 13px;">Surat Usulan / Rekomendasi
                                        Atasan:</span>
                                </div>
                                @if ($selectedVerification['recommendation_letter_url'])
                                    <a href="{{ $selectedVerification['recommendation_letter_url'] }}" target="_blank"
                                        class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 py-1 px-3 radius-8"
                                        style="font-size: 12.5px;">
                                        <i class="ri-external-link-line"></i> Buka Dokumen PDF
                                    </a>
                                @else
                                    <span class="badge bg-light text-muted border">Tidak Ada Berkas</span>
                                @endif
                            </div>
                        </div>

                        {{-- Form Keputusan Verifikasi --}}
                        <form wire:submit.prevent="submitVerification">
                            <div class="mb-20">
                                <label class="form-label fw-bold text-dark mb-2" style="font-size: 13.5px;">
                                    Keputusan Verifikasi <span class="text-danger">*</span>
                                </label>
                                <div class="row g-2">
                                    {{-- Diverifikasi / Lolos --}}
                                    <div class="col-12 col-md-4">
                                        <label
                                            class="w-100 p-12 rounded-12 border text-center cursor-pointer transition-all d-block"
                                            style="{{ $verifyForm['status'] === 'verified' ? 'background-color: #f0fdf4; border-color: #22c55e !important; box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);' : 'background-color: #ffffff; border-color: #e2e8f0;' }}">
                                            <input type="radio" wire:model.live="verifyForm.status"
                                                value="verified" class="d-none">
                                            <i
                                                class="ri-checkbox-circle-fill d-block fs-4 {{ $verifyForm['status'] === 'verified' ? 'text-success' : 'text-muted' }} mb-1"></i>
                                            <span class="fw-bold text-xs d-block text-dark">Diverifikasi</span>
                                            <small class="text-muted text-2xs">Berkas Sah & Lengkap</small>
                                        </label>
                                    </div>

                                    {{-- Perlu Perbaikan --}}
                                    <div class="col-12 col-md-4">
                                        <label
                                            class="w-100 p-12 rounded-12 border text-center cursor-pointer transition-all d-block"
                                            style="{{ $verifyForm['status'] === 'revision_required' ? 'background-color: #fff7ed; border-color: #f97316 !important; box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.2);' : 'background-color: #ffffff; border-color: #e2e8f0;' }}">
                                            <input type="radio" wire:model.live="verifyForm.status"
                                                value="revision_required" class="d-none">
                                            <i class="ri-edit-circle-fill d-block fs-4 {{ $verifyForm['status'] === 'revision_required' ? 'text-warning' : 'text-muted' }} mb-1"
                                                style="{{ $verifyForm['status'] === 'revision_required' ? 'color: #ea580c !important;' : '' }}"></i>
                                            <span class="fw-bold text-xs d-block text-dark">Perlu Perbaikan</span>
                                            <small class="text-muted text-2xs">Minta Revisi Peserta</small>
                                        </label>
                                    </div>

                                    {{-- Ditolak --}}
                                    <div class="col-12 col-md-4">
                                        <label
                                            class="w-100 p-12 rounded-12 border text-center cursor-pointer transition-all d-block"
                                            style="{{ $verifyForm['status'] === 'rejected' ? 'background-color: #fef2f2; border-color: #ef4444 !important; box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);' : 'background-color: #ffffff; border-color: #e2e8f0;' }}">
                                            <input type="radio" wire:model.live="verifyForm.status"
                                                value="rejected" class="d-none">
                                            <i
                                                class="ri-close-circle-fill d-block fs-4 {{ $verifyForm['status'] === 'rejected' ? 'text-danger' : 'text-muted' }} mb-1"></i>
                                            <span class="fw-bold text-xs d-block text-dark">Ditolak</span>
                                            <small class="text-muted text-2xs">Tidak Memenuhi Syarat</small>
                                        </label>
                                    </div>
                                </div>
                                @error('verifyForm.status')
                                    <div class="text-danger text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Catatan Verifikasi / Alasan Penolakan (Wajib diisi jika pendaftaran ditolak atau revisi) --}}
                            <div class="mb-24">
                                <label for="verification_notes" class="form-label fw-bold text-dark mb-1"
                                    style="font-size: 13.5px;">
                                    Catatan Verifikasi
                                    @if ($verifyForm['status'] !== 'verified')
                                        <span class="text-danger">* (Wajib diisi)</span>
                                    @else
                                        <span class="text-muted fw-normal">(Opsional)</span>
                                    @endif
                                </label>
                                <textarea id="verification_notes" wire:model="verifyForm.verification_notes" rows="3"
                                    class="form-control @error('verifyForm.verification_notes') is-invalid @enderror radius-10"
                                    placeholder="{{ $verifyForm['status'] === 'revision_required' ? 'Contoh: Format surat rekomendasi belum ditandatangani Kepala OPD / stempel basah...' : ($verifyForm['status'] === 'rejected' ? 'Contoh: Kuota pelatihan sudah penuh atau kualifikasi jabatan belum sesuai...' : 'Catatan tambahan untuk peserta (opsional)...') }}"
                                    style="font-size: 13px;"></textarea>
                                @error('verifyForm.verification_notes')
                                    <div class="invalid-feedback text-xs">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Modal Actions --}}
                            <div class="d-flex align-items-center justify-content-end gap-2 pt-2 border-top">
                                <button type="button" class="btn btn-light px-20 py-10 radius-10"
                                    data-bs-dismiss="modal" style="font-size: 13.5px;">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="btn btn-simple px-24 py-10 radius-10 d-inline-flex align-items-center gap-2"
                                    style="font-size: 13.5px;" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="submitVerification">
                                        <i class="ri-check-line"></i> Simpan Keputusan
                                    </span>
                                    <span wire:loading wire:target="submitVerification">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL & AUDIT VERIFIKASI (READ-ONLY) --}}
    <div class="modal fade" id="modalDetailVerifikasi" tabindex="-1" role="dialog" aria-hidden="true"
        wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 600px;">
            <div class="modal-content border-0 shadow-lg radius-20 overflow-hidden bg-white">
                <div class="modal-header border-0 pb-0 pt-24 px-28 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; background-color: #f1f5f9; color: #334155;">
                            <i class="ri-file-text-line fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Detail Berkas & Riwayat Verifikasi</h6>
                            <small class="text-muted" style="font-size: 12px;">Audit Trail Pendaftaran ASN</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="font-size: 11px;"></button>
                </div>

                <div class="modal-body p-28 pt-16">
                    @if ($selectedDetail)
                        <div class="mb-3 p-16 rounded-12 d-flex align-items-center justify-content-between"
                            style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                            <div>
                                <small class="text-muted text-xs d-block">Status Saat Ini:</small>
                                <span class="badge {{ $selectedDetail['status_badge'] }} px-2 py-1 mt-1"
                                    style="font-size: 12.5px;">
                                    {{ $selectedDetail['status_label'] }}
                                </span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted text-xs d-block">No. Registrasi:</small>
                                <span class="fw-bold text-simple-gold"
                                    style="font-size: 13.5px;">{{ $selectedDetail['registration_number'] }}</span>
                            </div>
                        </div>

                        <div class="card border rounded-12 p-16 mb-16">
                            <h6 class="fw-bold text-dark text-xs text-uppercase tracking-wider mb-2">Profil Calon
                                Peserta</h6>
                            <div class="row g-2" style="font-size: 13px;">
                                <div class="col-4 text-muted">Nama Lengkap</div>
                                <div class="col-8 fw-semibold text-dark">{{ $selectedDetail['user_name'] }}</div>

                                <div class="col-4 text-muted">NIP ASN</div>
                                <div class="col-8 text-dark">{{ $selectedDetail['user_nip'] }}</div>

                                <div class="col-4 text-muted">Instansi / OPD</div>
                                <div class="col-8 text-dark">{{ $selectedDetail['user_opd'] }}</div>

                                <div class="col-4 text-muted">Jabatan / Gol.</div>
                                <div class="col-8 text-dark">{{ $selectedDetail['user_position'] }}
                                    ({{ $selectedDetail['user_rank'] }})</div>

                                <div class="col-4 text-muted">Kontak WhatsApp</div>
                                <div class="col-8 text-dark">{{ $selectedDetail['user_phone'] }}</div>
                            </div>
                        </div>

                        <div class="card border rounded-12 p-16 mb-16">
                            <h6 class="fw-bold text-dark text-xs text-uppercase tracking-wider mb-2">Audit Pemeriksaan
                                Berkas</h6>
                            <div class="row g-2" style="font-size: 13px;">
                                <div class="col-4 text-muted">Petugas Verifikator</div>
                                <div class="col-8 fw-semibold text-dark">{{ $selectedDetail['verifier_name'] }}</div>

                                <div class="col-4 text-muted">Waktu Verifikasi</div>
                                <div class="col-8 text-dark">
                                    {{ $selectedDetail['verified_at'] ?? 'Belum dilakukan verifikasi' }}</div>

                                <div class="col-4 text-muted">Catatan Verifikasi</div>
                                <div class="col-8 text-dark">{{ $selectedDetail['verification_notes'] ?: '-' }}</div>

                                <div class="col-4 text-muted">Surat Rekomendasi</div>
                                <div class="col-8">
                                    @if ($selectedDetail['recommendation_letter_url'])
                                        <a href="{{ $selectedDetail['recommendation_letter_url'] }}" target="_blank"
                                            class="btn btn-xs btn-outline-danger d-inline-flex align-items-center gap-1 py-1 px-2 radius-6"
                                            style="font-size: 12px;">
                                            <i class="ri-file-pdf-line"></i> Lihat PDF
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('mods.admin.verifikasi.atc.verifikasi-data-atc')

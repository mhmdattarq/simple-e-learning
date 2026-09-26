@push('css')
    <style>
        .simpel-card .form-control:focus,
        .simpel-card .form-select:focus {
            border-color: #f3bc42 !important;
            box-shadow: 0 0 0 3px rgba(243, 188, 66, 0.22) !important;
        }

        .decision-card {
            transition: all 0.2s ease;
        }

        .decision-card:hover {
            border-color: #071a33 !important;
        }
    </style>
@endpush

<div>
    {{-- Header & Navigasi Kembali --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h5 class="fw-bold text-dark mb-0">Pemeriksaan Berkas Calon Peserta</h5>
            </div>
            <p class="text-muted mb-0">Verifikasi kelayakan berkas persyaratan pendaftaran ASN dan surat usulan atasan.
            </p>
        </div>
        <div>
            <a href="{{ route('verifikasi.data') }}"
                class="btn btn-danger d-inline-flex align-items-center gap-1 radius-8 px-3 py-2" wire:navigate>
                <i class="ri-arrow-left-line"></i>
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- KOLOM KIRI: DOKUMEN SURAT USULAN (PDF VIEWER) --}}
        <div class="col-lg-7 col-12">
            <div class="card simpel-card border-0 shadow-sm radius-16 h-100">
                <div
                    class="card-header bg-white py-16 px-20 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger"
                            style="width: 36px; height: 36px;">
                            <i class="ri-file-pdf-2-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Surat Usulan / Rekomendasi Atasan</h6>
                            <small class="text-muted">Dokumen resmi pendaftaran yang diunggah oleh calon peserta</small>
                        </div>
                    </div>
                    @if ($registration->recommendation_letter_path)
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ asset('storage/' . $registration->recommendation_letter_path) }}"
                                target="_blank"
                                class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 radius-8 py-1 px-3"
                                title="Buka berkas di jendela / tab baru">
                                <i class="ri-external-link-line"></i> Buka Tab Baru
                            </a>
                            <a href="{{ asset('storage/' . $registration->recommendation_letter_path) }}" download
                                class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1 radius-8 py-1 px-3"
                                title="Unduh berkas PDF ke perangkat">
                                <i class="ri-download-2-line"></i> Unduh
                            </a>
                        </div>
                    @endif
                </div>

                <div class="card-body p-20 d-flex flex-column">
                    @if ($registration->recommendation_letter_path)
                        <div id="pdfViewerWrapper" wire:ignore
                            class="flex-grow-1 w-100 position-relative rounded-12 overflow-hidden border"
                            style="background-color: #525659; min-height: 720px;">
                            <iframe src="{{ asset('storage/' . $registration->recommendation_letter_path) }}#toolbar=1"
                                class="w-100 h-100 border-0 position-absolute top-0 start-0" style="min-height: 720px;"
                                title="Preview Dokumen Surat Rekomendasi Peserta">
                            </iframe>
                        </div>
                    @else
                        <div class="p-40 text-center rounded-12 bg-light border border-dashed my-auto">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning mb-3"
                                style="width: 64px; height: 64px;">
                                <i class="ri-file-warning-line display-6"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Tidak Ada Berkas Dokumen</h6>
                            <p class="text-muted fs-7 mb-0">Calon peserta ini tidak melampirkan berkas surat rekomendasi
                                atasan saat mendaftar.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: DATA PESERTA, DIKLAT & FORM KEPUTUSAN VERIFIKASI --}}
        <div class="col-lg-5 col-12">
            <div class="vstack gap-4">
                {{-- Card 1: Data Calon Peserta & Pelatihan --}}
                <div class="card simpel-card border-0 shadow-sm radius-16">
                    <div
                        class="card-header bg-white py-16 px-20 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ri-user-star-line text-simple fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Data Calon Peserta & Diklat</h6>
                        </div>
                        <span class="badge {{ $registration->status->badgeClass() }} px-3 py-1 fs-8">
                            {{ $registration->status->label() }}
                        </span>
                    </div>

                    <div class="card-body p-20">
                        {{-- Header Registrasi --}}
                        <div
                            class="p-3 rounded-12 bg-light border border-simpel mb-3 d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted text-xs d-block">Nomor Registrasi:</small>
                                <span
                                    class="fw-bold text-simple-gold font-monospace fs-6">{{ $registration->registration_number }}</span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted text-xs d-block">Waktu Mendaftar:</small>
                                <span
                                    class="fw-semibold text-dark fs-8">{{ $registration->enrolled_at?->format('d M Y, H:i') ?? '-' }}
                                    WIB</span>
                            </div>
                        </div>

                        {{-- Profil Peserta --}}
                        <div class="mb-3">
                            <h6 class="fw-bold text-dark text-xs text-uppercase tracking-wider mb-2">Identitas &
                                Kepegawaian</h6>
                            <table class="table table-sm table-bordered fs-7 mb-0 align-middle">
                                <tbody>
                                    <tr>
                                        <td class="bg-light text-muted" style="width: 38%;">Nama Lengkap</td>
                                        <td class="fw-bold text-dark">{{ $registration->user?->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted">NIP ASN</td>
                                        <td class="font-monospace text-dark">{{ $registration->user?->nip ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted">Instansi / OPD</td>
                                        <td class="text-dark">{{ $registration->user?->opd_agency ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted">Jabatan Saat Ini</td>
                                        <td class="text-dark">{{ $registration->user?->position ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted">Pangkat / Gol.</td>
                                        <td class="text-dark">{{ $registration->user?->rank_class ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light text-muted">Kontak</td>
                                        <td class="text-dark">
                                            <div><i
                                                    class="ri-whatsapp-line text-success me-1"></i>{{ $registration->user?->phone_number ?? '-' }}
                                            </div>
                                            <div class="mt-1"><i
                                                    class="ri-mail-line text-primary me-1"></i>{{ $registration->user?->email ?? '-' }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Pelatihan Dituju --}}
                        <div>
                            <h6 class="fw-bold text-dark text-xs text-uppercase tracking-wider mb-2">Program Pelatihan
                                Dituju</h6>
                            <div class="p-3 rounded-12 bg-white border border-simpel">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span
                                        class="badge bg-secondary-subtle text-secondary fs-8 font-monospace">{{ $registration->course?->code ?? '-' }}</span>
                                    <span class="text-muted fs-8">{{ ucfirst($registration->course?->method ?? '') }}
                                        &bull; Kuota: {{ $registration->course?->quota ?? '-' }} ASN</span>
                                </div>
                                <h6 class="fw-bold text-navy mb-1 fs-7">{{ $registration->course?->title ?? '-' }}</h6>
                                <span
                                    class="text-muted fs-8">{{ $registration->course?->category?->name ?? '-' }}</span>
                            </div>
                        </div>

                        @if ($registration->notes)
                            <div class="mt-3 p-3 rounded-12 bg-warning bg-opacity-10 border border-warning">
                                <small class="fw-bold text-dark d-block mb-1"><i
                                        class="ri-information-line text-warning me-1"></i>Catatan Pemohon:</small>
                                <p class="text-muted fs-8 mb-0">{{ $registration->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card 2: Form Keputusan Verifikasi --}}
                <div class="card simpel-card border-0 shadow-sm radius-16">
                    <div
                        class="card-header bg-white py-16 px-20 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ri-shield-check-line text-simple fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Keputusan Verifikasi Berkas</h6>
                        </div>
                    </div>

                    <div class="card-body p-20">
                        @if ($registration->verifier)
                            <div class="mb-3 p-3 rounded-12 bg-light border border-simpel fs-8">
                                <div class="text-muted mb-1"><i class="ri-history-line me-1"></i>Riwayat Pemeriksaan
                                    Terakhir:</div>
                                <div class="text-dark">
                                    Diverifikasi oleh: <strong>{{ $registration->verifier?->name }}</strong>
                                    pada {{ $registration->verified_at?->format('d/m/Y H:i') }} WIB
                                </div>
                            </div>
                        @endif

                        <form wire:submit="submitVerification">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark fs-7 mb-2">
                                    Pilih Keputusan Kelayakan <span class="text-danger">*</span>
                                </label>
                                <div class="vstack gap-2">
                                    {{-- Diverifikasi / Lolos --}}
                                    <label class="p-3 rounded-12 border cursor-pointer decision-card d-block"
                                        style="{{ $verifyForm['status'] === 'verified' ? 'background-color: #f0fdf4; border-color: #22c55e !important; box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);' : 'background-color: #ffffff; border-color: #e2e8f0;' }}">
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="radio" wire:model.live="verifyForm.status"
                                                value="verified" class="form-check-input mt-0">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ri-checkbox-circle-fill text-success fs-5"></i>
                                                <div>
                                                    <span class="fw-bold text-dark fs-7 d-block">Diverifikasi / Lolos
                                                        Syarat</span>
                                                    <small class="text-muted fs-8">Berkas lengkap, sah, dan memenuhi
                                                        kuota diklat.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </label>

                                    {{-- Perlu Perbaikan --}}
                                    <label class="p-3 rounded-12 border cursor-pointer decision-card d-block"
                                        style="{{ $verifyForm['status'] === 'revision_required' ? 'background-color: #fffbeb; border-color: #f59e0b !important; box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);' : 'background-color: #ffffff; border-color: #e2e8f0;' }}">
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="radio" wire:model.live="verifyForm.status"
                                                value="revision_required" class="form-check-input mt-0">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ri-error-warning-fill text-warning fs-5"></i>
                                                <div>
                                                    <span class="fw-bold text-dark fs-7 d-block">Perlu Perbaikan
                                                        Berkas</span>
                                                    <small class="text-muted fs-8">Berkas kurang jelas / belum bertanda
                                                        tangan resmi atasan.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </label>

                                    {{-- Ditolak --}}
                                    <label class="p-3 rounded-12 border cursor-pointer decision-card d-block"
                                        style="{{ $verifyForm['status'] === 'rejected' ? 'background-color: #fef2f2; border-color: #ef4444 !important; box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);' : 'background-color: #ffffff; border-color: #e2e8f0;' }}">
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="radio" wire:model.live="verifyForm.status"
                                                value="rejected" class="form-check-input mt-0">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ri-close-circle-fill text-danger fs-5"></i>
                                                <div>
                                                    <span class="fw-bold text-dark fs-7 d-block">Tolak
                                                        Pendaftaran</span>
                                                    <small class="text-muted fs-8">Peserta tidak memenuhi kriteria
                                                        persyaratan pelatihan.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @error('verifyForm.status')
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Catatan Verifikator --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold text-dark fs-7 mb-1">
                                    Catatan Tim Verifikator
                                    @if (in_array($verifyForm['status'], ['revision_required', 'rejected'], true))
                                        <span class="text-danger">*</span>
                                    @else
                                        <span class="text-muted fw-normal fs-8">(Opsional)</span>
                                    @endif
                                </label>
                                <textarea wire:model="verifyForm.verification_notes" rows="3"
                                    class="form-control fs-7 @error('verifyForm.verification_notes') is-invalid @enderror"
                                    placeholder="{{ $verifyForm['status'] === 'revision_required' ? 'Contoh: Format surat rekomendasi belum ditandatangani Kepala OPD / belum stempel basah...' : ($verifyForm['status'] === 'rejected' ? 'Contoh: Kualifikasi jabatan tidak sesuai sasaran diklat...' : 'Catatan tambahan untuk calon peserta (opsional)...') }}"></textarea>
                                @error('verifyForm.verification_notes')
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tombol Aksi Simpan --}}
                            <div class="d-flex align-items-center gap-2 pt-2 border-top mt-3">
                                <button type="submit"
                                    class="btn btn-simple-gold px-4 py-2 radius-8 fs-7 fw-semibold ms-auto d-inline-flex align-items-center gap-2 w-100"
                                    wire:loading.attr="disabled">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('mods.admin.verifikasi.atc.verifikasi-detail-atc')

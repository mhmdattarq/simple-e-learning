<div>
    {{-- Header & Breadcrumb Navigation --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('pimpinan.persetujuan.data') }}" class="text-decoration-none text-muted fs-8" wire:navigate>
                    <i class="ri-arrow-left-line me-1"></i> Antrean Persetujuan
                </a>
                <span class="text-muted fs-8">/</span>
                <span class="text-navy fw-semibold fs-8">Tinjau Usulan</span>
            </div>
            <h5 class="fw-bold text-dark mb-0">Tinjau Usulan Program Pelatihan</h5>
        </div>
        <div>
            <a href="{{ route('pimpinan.persetujuan.data') }}" class="btn btn-outline-secondary px-3 py-1_5 rounded-pill d-inline-flex align-items-center gap-1 shadow-none fs-7" wire:navigate>
                <i class="ri-arrow-left-line"></i>
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </div>

    {{-- Banner Status Usulan --}}
    @if ($course->status === \App\Enums\CourseStatus::Submitted)
        <div class="alert alert-warning border-0 shadow-sm rounded-4 p-3 d-flex align-items-center justify-content-between flex-wrap gap-2 mb-24">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning text-dark p-2_5 rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="ri-time-line fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Menunggu Verifikasi & Persetujuan Pimpinan</h6>
                    <small class="text-muted">
                        Diusulkan oleh <strong class="text-dark">{{ $course->creator?->name ?? 'Admin Diklat' }}</strong> pada {{ $course->updated_at?->format('d M Y, H:i') }} WIB.
                    </small>
                </div>
            </div>
            <div>
                <span class="badge bg-warning text-dark px-3 py-1_5 fs-8 fw-semibold">
                    <i class="ri-hourglass-fill me-1"></i> Status: Diajukan
                </span>
            </div>
        </div>
    @else
        <div class="alert alert-info border-0 shadow-sm rounded-4 p-3 d-flex align-items-center justify-content-between flex-wrap gap-2 mb-24">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-info text-white p-2_5 rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="ri-information-line fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Pelatihan Ini Telah Diproses</h6>
                    <small class="text-muted">
                        Status saat ini: <strong class="text-dark">{{ $course->status->label() }}</strong>
                        @if ($course->approved_at)
                            &bull; Diputuskan pada {{ $course->approved_at->format('d M Y, H:i') }} WIB
                        @endif
                    </small>
                </div>
            </div>
            <div>
                <span class="badge {{ $course->status->badgeClass() }} px-3 py-1_5 fs-8">
                    {{ $course->status->label() }}
                </span>
            </div>
        </div>
    @endif

    <div class="row g-4">
        {{-- Kolom Kiri: Rincian Lengkap Rencana Pelatihan (8 Kolom) --}}
        <div class="col-lg-8">
            {{-- Bagian 1: Spesifikasi Pokok Program --}}
            <div class="card simpel-card border-0 shadow-sm radius-16 mb-4">
                <div class="card-header bg-white pt-20 pb-16 px-24 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-book-open-line text-simple fs-5"></i>
                        <h6 class="fw-bold text-dark mb-0 fs-6">Spesifikasi Program Pelatihan</h6>
                    </div>
                    <span class="badge bg-light text-navy border border-simpel font-monospace px-2_5 py-1 fs-8">
                        {{ $course->code }}
                    </span>
                </div>

                <div class="card-body p-24">
                    <div class="mb-4">
                        <span class="text-muted fs-8 d-block mb-1">Nama Program Pelatihan</span>
                        <h5 class="fw-bold text-dark mb-2">{{ $course->title }}</h5>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2_5 py-1 fs-8">
                                <i class="ri-folder-3-line me-1"></i> {{ $course->category?->name ?? 'Umum' }}
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2_5 py-1 fs-8">
                                <i class="ri-stack-line me-1"></i> {{ $course->isPermanent() ? 'Mandiri (Buka Terus)' : 'Batch Terjadwal' }}
                            </span>
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2_5 py-1 fs-8">
                                <i class="ri-broadcast-line me-1"></i> {{ ucfirst((string) $course->method) }}
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle fs-7 mb-0">
                            <tbody>
                                <tr>
                                    <td class="bg-light text-muted fw-medium" style="width: 32%;">
                                        <i class="ri-map-pin-line text-secondary me-1"></i> Lokasi Pelaksanaan
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        {{ $course->location ?: 'Daring / Online Melalui LMS SIMPEL' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-light text-muted fw-medium">
                                        <i class="ri-calendar-line text-secondary me-1"></i> Periode / Jadwal
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        @if ($course->isPermanent())
                                            <span class="text-primary font-monospace">Sepanjang Tahun (Fleksibel / Self-Paced)</span>
                                        @else
                                            {{ $course->start_date?->format('d M Y') ?? '-' }}
                                            <span class="text-muted mx-1">s/d</span>
                                            {{ $course->end_date?->format('d M Y') ?? '-' }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg-light text-muted fw-medium">
                                        <i class="ri-group-line text-secondary me-1"></i> Kapasitas Kuota Peserta
                                    </td>
                                    <td class="fw-bold text-navy">
                                        {{ $course->quota }} Orang Peserta ASN
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Bagian 2: Sasaran & Sumber Anggaran --}}
            <div class="card simpel-card border-0 shadow-sm radius-16 mb-4">
                <div class="card-header bg-white pt-20 pb-16 px-24 border-bottom d-flex align-items-center gap-2">
                    <i class="ri-money-dollar-circle-line text-simple fs-5"></i>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Sasaran Peserta & Pembiayaan</h6>
                </div>
                <div class="card-body p-24">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border border-simpel h-100">
                                <span class="text-muted fs-8 d-block mb-1">
                                    <i class="ri-user-star-line me-1 text-primary"></i> Sasaran Peserta Diklat
                                </span>
                                <div class="fw-semibold text-dark fs-7">
                                    {{ $course->target_audience ?: 'Aparatur Sipil Negara (ASN) di Lingkungan Pemkab Aceh Timur' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border border-simpel h-100">
                                <span class="text-muted fs-8 d-block mb-1">
                                    <i class="ri-bank-card-line me-1 text-success"></i> Sumber Anggaran / DPA
                                </span>
                                <div class="fw-semibold text-dark fs-7">
                                    {{ $course->budget_source ?: 'APBD Kabupaten Aceh Timur' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian 3: Deskripsi & Kompetensi --}}
            <div class="card simpel-card border-0 shadow-sm radius-16 mb-4">
                <div class="card-header bg-white pt-20 pb-16 px-24 border-bottom d-flex align-items-center gap-2">
                    <i class="ri-file-text-line text-simple fs-5"></i>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Deskripsi & Standar Kompetensi</h6>
                </div>
                <div class="card-body p-24">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-navy fs-7 mb-2">Deskripsi & Tujuan Program Pelatihan:</label>
                        <div class="p-3 bg-light rounded-3 border border-simpel fs-7 text-dark lh-base">
                            {{ $course->description ?: 'Tidak ada uraian deskripsi tambahan.' }}
                        </div>
                    </div>

                    <div>
                        <label class="form-label fw-bold text-navy fs-7 mb-2">Standar Kompetensi yang Dibangun:</label>
                        <div class="p-3 bg-light rounded-3 border border-simpel fs-7 text-dark lh-base">
                            {{ $course->competencies ?: 'Tidak ada rincian kompetensi khusus yang dicantumkan.' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian 4: Dokumen Kerangka Acuan Kerja (TOR / KAK) --}}
            <div class="card simpel-card border-0 shadow-sm radius-16 mb-4">
                <div class="card-header bg-white pt-20 pb-16 px-24 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-attachment-line text-simple fs-5"></i>
                        <h6 class="fw-bold text-dark mb-0 fs-6">Dokumen Kerangka Acuan Kerja (TOR / KAK)</h6>
                    </div>
                </div>
                <div class="card-body p-24">
                    <div class="p-3 rounded-3 bg-white border border-simpel d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-3 fs-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                                <i class="ri-file-pdf-line"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark fs-7 mb-0">Berkas Kerangka Acuan Kerja (KAK)</h6>
                                <small class="text-muted">Dokumen resmi rincian kurikulum, pengajar, dan rincian biaya yang diunggah Admin.</small>
                            </div>
                        </div>
                        <div>
                            @if ($course->tor_file)
                                <a href="{{ asset('storage/' . $course->tor_file) }}" target="_blank" class="btn btn-outline-danger px-3 py-1_5 rounded-pill d-inline-flex align-items-center gap-1 fs-7 fw-semibold shadow-none">
                                    <i class="ri-download-2-line"></i> Unduh / Buka Dokumen PDF
                                </a>
                            @else
                                <span class="badge bg-light text-muted border px-3 py-2 fs-8">
                                    <i class="ri-file-warning-line me-1"></i> Tidak Ada Berkas TOR yang Diunggah
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Panel Keputusan Pimpinan (4 Kolom) --}}
        <div class="col-lg-4">
            <div style="position: sticky; top: 90px; z-index: 10;">
                {{-- Card Keputusan Eksekutif --}}
                <div class="card simpel-card border-0 shadow-sm radius-16 mb-4">
                    <div class="card-header bg-navy text-white pt-20 pb-16 px-20 radius-16-top d-flex align-items-center gap-2" style="background: linear-gradient(135deg, #071a33 0%, #0d284d 100%);">
                        <i class="ri-shield-check-line text-warning fs-5"></i>
                        <h6 class="fw-bold text-white mb-0 fs-6">Lembar Keputusan Pimpinan</h6>
                    </div>
                    <div class="card-body p-20">
                        @if ($course->status === \App\Enums\CourseStatus::Submitted)
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark fs-7 mb-1">
                                    Catatan Keputusan / Instruksi Revisi
                                </label>
                                <textarea wire:model="decisionNotes" class="form-control bg-light @error('decisionNotes') is-invalid @enderror" rows="4"
                                    placeholder="Tuliskan arahan persetujuan atau catatan rincian perbaikan (wajib jika dikembalikan untuk revisi)..."></textarea>
                                @error('decisionNotes')
                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted fs-8 d-block mt-1_5">
                                    * Catatan bersifat opsional saat menyetujui, namun <strong>wajib diisi</strong> jika usulan dikembalikan untuk direvisi oleh Admin.
                                </small>
                            </div>

                            <hr class="my-3 text-muted opacity-25">

                            <div class="d-grid gap-2">
                                {{-- Tombol Setujui Rencana --}}
                                <button type="button" class="btn btn-success py-2_5 rounded-pill d-flex align-items-center justify-content-center gap-2 shadow-sm fw-semibold"
                                    wire:click="approve" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="approve">
                                        <i class="ri-checkbox-circle-line fs-5"></i> Setujui Rencana Pelatihan
                                    </span>
                                    <span wire:loading wire:target="approve">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Memproses Persetujuan...
                                    </span>
                                </button>

                                {{-- Tombol Kembalikan untuk Revisi --}}
                                <button type="button" class="btn btn-outline-danger py-2_5 rounded-pill d-flex align-items-center justify-content-center gap-2 shadow-none fw-semibold"
                                    wire:click="reject" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="reject">
                                        <i class="ri-arrow-go-back-line fs-5"></i> Kembalikan untuk Revisi
                                    </span>
                                    <span wire:loading wire:target="reject">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Mengembalikan Usulan...
                                    </span>
                                </button>
                            </div>
                        @else
                            {{-- Pelatihan Sudah Diproses --}}
                            <div class="p-3 rounded-3 bg-light border border-simpel mb-3">
                                <span class="text-muted fs-8 d-block mb-1">Status Keputusan</span>
                                <span class="badge {{ $course->status->badgeClass() }} px-3 py-1_5 fs-7 mb-2">
                                    {{ $course->status->label() }}
                                </span>
                                @if ($course->approval_notes)
                                    <div class="mt-2 pt-2 border-top">
                                        <span class="text-muted fs-8 d-block mb-1">Catatan Keputusan:</span>
                                        <p class="text-dark fs-8 mb-0 fst-italic">"{{ $course->approval_notes }}"</p>
                                    </div>
                                @endif
                            </div>

                            <div class="alert alert-secondary fs-8 mb-0 py-2">
                                <i class="ri-information-line me-1"></i> Pelatihan ini telah berstatus <strong>{{ $course->status->label() }}</strong> sehingga keputusan persetujuan tidak dapat diubah kembali.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card Info Pengusul (Admin Diklat) --}}
                <div class="card simpel-card border-0 shadow-sm radius-16">
                    <div class="card-header bg-white pt-16 pb-12 px-20 border-bottom d-flex align-items-center gap-2">
                        <i class="ri-user-line text-simple fs-5"></i>
                        <h6 class="fw-bold text-dark mb-0 fs-7">Informasi Pengusul Usulan</h6>
                    </div>
                    <div class="card-body p-20">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 44px; height: 44px;">
                                {{ strtoupper(substr($course->creator?->name ?? 'A', 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0 fs-7">{{ $course->creator?->name ?? 'Admin Diklat' }}</h6>
                                <small class="text-muted fs-8 d-block">{{ $course->creator?->position ?? 'Petugas Administrator' }}</small>
                            </div>
                        </div>

                        <div class="fs-8 text-muted">
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span>NIP:</span>
                                <span class="fw-semibold text-dark font-monospace">{{ $course->creator?->nip ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom">
                                <span>Instansi / OPD:</span>
                                <span class="fw-semibold text-dark text-end">{{ $course->creator?->opd_agency ?? 'BKPSDM Aceh Timur' }}</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span>Diajukan Pada:</span>
                                <span class="fw-semibold text-dark">{{ $course->updated_at?->format('d M Y, H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

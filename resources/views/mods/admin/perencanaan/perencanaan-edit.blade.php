@push('css')
    <style>
        .simpel-card .form-control:focus,
        .simpel-card .form-select:focus {
            border-color: #f3bc42 !important;
            box-shadow: 0 0 0 3px rgba(243, 188, 66, 0.22) !important;
        }
    </style>
@endpush

<div>
    {{-- Header & Breadcrumb Navigation --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Edit Pelatihan</h5>
            <p class="text-muted mb-0">Formulir pembaruan program pelatihan pada Tahap 1 (Perencanaan Diklat
                ASN).</p>
        </div>
        <div>
            <a href="{{ route('perencanaan.data') }}" class="btn btn-danger d-flex align-items-center" wire:navigate>
                <i class="ri-arrow-left-line"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    {{-- Main Form Card --}}
    <form wire:submit="formSubmit">
        <div class="card simpel-card border-0 shadow-sm radius-16">
            <div
                class="card-header bg-white py-16 px-24 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0 fs-6">Formulir Pembaruan Pelatihan</h6>
                        <small class="text-muted">Perubahan akan langsung terbarui pada
                            katalog pelatihan yang terdaftar.</small>
                    </div>
                </div>
                <span class="simpel-badge simpel-badge-navy">
                    Tahap 1: Perencanaan
                </span>
            </div>

            <div class="card-body p-28 p-md-32">
                {{-- Bagian 1: Informasi Dasar Pelatihan --}}
                <div class="mb-40">
                    <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom"
                        style="border-color: #e6eaf0 !important;">
                        <span
                            class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                            style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">1</span>
                        <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">Informasi Dasar Pelatihan</h6>
                    </div>
                    <div class="row g-3">
                        {{-- Kode Diklat --}}
                        <div class="col-md-3">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Kode Diklat <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('form.code') is-invalid @enderror"
                                wire:model="form.code" placeholder="PLT-2026-001">
                            @error('form.code')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Kategori --}}
                        <div class="col-md-5">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Kategori Pelatihan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('form.category_id') is-invalid @enderror"
                                wire:model="form.category_id">
                                <option value="">-- Pilih Kategori Diklat --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('form.category_id')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status Pelatihan (Read-Only) --}}
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Status Pelatihan
                            </label>
                            <div class="pt-1">
                                <span class="badge bg-simple-gold text-dark px-3 py-2 fs-7 fw-semibold border">
                                    <i class="ri-draft-line me-1"></i>{{ $course->status->label() }}
                                </span>
                            </div>
                            <div class="form-text text-xs text-muted mt-1">Status berubah otomatis melalui alur persetujuan.</div>
                        </div>

                        {{-- Nama Lengkap Pelatihan --}}
                        <div class="col-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Nama Program Pelatihan <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('form.title') is-invalid @enderror"
                                wire:model="form.title"
                                placeholder="Contoh: Pelatihan Manajemen Administrator Angkatan I">
                            @error('form.title')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Ringkasan / Deskripsi --}}
                        <div class="col-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Deskripsi & Sasaran Kompetensi
                            </label>
                            <textarea class="form-control @error('form.description') is-invalid @enderror" wire:model="form.description"
                                rows="3" placeholder="Jelaskan tujuan umum diklat, output kompetensi aparatur, dan ringkasan silabus..."></textarea>
                            @error('form.description')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Waktu, Kuota & Pelaksanaan --}}
                <div class="mb-40">
                    <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom"
                        style="border-color: #e6eaf0 !important;">
                        <span
                            class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                            style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">2</span>
                        <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">Pelaksanaan, Waktu & Kuota</h6>
                    </div>
                    <div class="row g-3">
                        {{-- Layout Dinamis Berdasarkan Tipe Pelatihan --}}
                        @if ($form['type'] === 'batch')
                            {{-- Row 1 Batch: Tipe + Tanggal Mulai + Tanggal Selesai --}}
                            <div class="col-md-4">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Tipe Akses Belajar <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('form.type') is-invalid @enderror"
                                    wire:model.live="form.type">
                                    <option value="permanent">Permanen (Akses Mandiri / Tanpa Batas)</option>
                                    <option value="batch">Batch (Berdasarkan Gelombang / Periode)</option>
                                </select>
                                <div class="form-text text-xs text-muted mt-1"><i
                                        class="ri-calendar-check-line me-1"></i>Berdasarkan jadwal gelombang.</div>
                                @error('form.type')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Tanggal Mulai <span class="text-danger">*</span>
                                </label>
                                <input type="date"
                                    class="form-control @error('form.start_date') is-invalid @enderror"
                                    wire:model="form.start_date">
                                @error('form.start_date')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Tanggal Selesai <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('form.end_date') is-invalid @enderror"
                                    wire:model="form.end_date">
                                @error('form.end_date')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Row 2 Batch: Metode + Kuota + Lokasi --}}
                            <div class="col-md-4">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Metode Pelaksanaan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('form.method') is-invalid @enderror"
                                    wire:model="form.method">
                                    <option value="daring">Daring (Online / LMS Penuh)</option>
                                    <option value="luring">Luring (Tatap Muka Fisik)</option>
                                    <option value="hybrid">Hybrid (Kombinasi Daring & Luring)</option>
                                </select>
                                @error('form.method')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Kuota Maksimal Peserta <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" min="1"
                                        class="form-control @error('form.quota') is-invalid @enderror"
                                        wire:model="form.quota" placeholder="40">
                                    <span class="input-group-text bg-light text-muted">Orang</span>
                                </div>
                                @error('form.quota')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Ruangan Fisik / Tautan Kelas Online
                                </label>
                                <input type="text"
                                    class="form-control @error('form.location') is-invalid @enderror"
                                    wire:model="form.location" placeholder="Contoh: Aula BKPSDM / Link Zoom">
                                @error('form.location')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Row 3 Batch: Sasaran + Anggaran --}}
                            <div class="col-md-6">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Sasaran Peserta
                                </label>
                                <input type="text"
                                    class="form-control @error('form.target_audience') is-invalid @enderror"
                                    wire:model="form.target_audience"
                                    placeholder="Contoh: Pejabat Administrator Gol. III & IV">
                                @error('form.target_audience')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Sumber Dana / Anggaran
                                </label>
                                <input type="text"
                                    class="form-control @error('form.budget_source') is-invalid @enderror"
                                    wire:model="form.budget_source"
                                    placeholder="Contoh: DPA-BKPSDM Aceh Timur TA 2026">
                                @error('form.budget_source')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            {{-- Row 1 Permanent: Tipe + Metode + Kuota (Symmetric 3 columns) --}}
                            <div class="col-md-4">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Tipe Akses Belajar <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('form.type') is-invalid @enderror"
                                    wire:model.live="form.type">
                                    <option value="permanent">Permanen (Akses Mandiri / Tanpa Batas)</option>
                                    <option value="batch">Batch (Berdasarkan Gelombang / Periode)</option>
                                </select>
                                <div class="form-text text-xs text-muted mt-1"><i class="ri-time-line me-1"></i>Akses
                                    mandiri (self-paced) sepanjang tahun.</div>
                                @error('form.type')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Metode Pelaksanaan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('form.method') is-invalid @enderror"
                                    wire:model="form.method">
                                    <option value="daring">Daring (Online / LMS Penuh)</option>
                                    <option value="luring">Luring (Tatap Muka Fisik)</option>
                                    <option value="hybrid">Hybrid (Kombinasi Daring & Luring)</option>
                                </select>
                                <div class="form-text text-xs text-muted mt-1"><i
                                        class="ri-computer-line me-1"></i>Platform atau media kegiatan.</div>
                                @error('form.method')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Kuota Maksimal Peserta <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" min="1"
                                        class="form-control @error('form.quota') is-invalid @enderror"
                                        wire:model="form.quota" placeholder="40">
                                    <span class="input-group-text bg-light text-muted">Orang</span>
                                </div>
                                <div class="form-text text-xs text-muted mt-1"><i
                                        class="ri-user-line me-1"></i>Kapasitas maksimal aparatur.</div>
                                @error('form.quota')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Row 2 Permanent: Ruangan + Sasaran + Anggaran (Symmetric 3 columns) --}}
                            <div class="col-md-4">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Ruangan Fisik / Tautan Kelas Online
                                </label>
                                <input type="text"
                                    class="form-control @error('form.location') is-invalid @enderror"
                                    wire:model="form.location" placeholder="Contoh: Aula BKPSDM / Link Zoom">
                                @error('form.location')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Sasaran Peserta
                                </label>
                                <input type="text"
                                    class="form-control @error('form.target_audience') is-invalid @enderror"
                                    wire:model="form.target_audience"
                                    placeholder="Contoh: Seluruh ASN Pemkab Aceh Timur">
                                @error('form.target_audience')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Sumber Dana / Anggaran
                                </label>
                                <input type="text"
                                    class="form-control @error('form.budget_source') is-invalid @enderror"
                                    wire:model="form.budget_source" placeholder="Contoh: DPA-BKPSDM TA 2026">
                                @error('form.budget_source')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        {{-- Sasaran Kompetensi Khusus --}}
                        <div class="col-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Target Kompetensi ASN (Indikator Capaian)
                            </label>
                            <textarea class="form-control @error('form.competencies') is-invalid @enderror" wire:model="form.competencies"
                                rows="2" placeholder="Contoh: Mampu mengoperasikan aplikasi SPBE, menyusun regulasi teknis internal..."></textarea>
                            @error('form.competencies')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Bagian 3: Dokumen & Lampiran --}}
                <div class="mb-40">
                    <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom"
                        style="border-color: #e6eaf0 !important;">
                        <span
                            class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                            style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">3</span>
                        <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">Dokumen & Lampiran Berkas</h6>
                    </div>
                    <div class="row g-3">
                        {{-- Poster Thumbnail --}}
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Poster Pelatihan (JPG/PNG) <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i
                                        class="ri-image-line"></i></span>
                                <input type="file"
                                    class="form-control @error('thumbnailFile') is-invalid @enderror"
                                    wire:model="thumbnailFile" accept="image/jpeg,image/png">
                            </div>
                            <div class="form-text text-xs text-muted mt-1">Format gambar JPG atau PNG (maksimal 2 MB).
                                Kosongkan jika tidak diubah.</div>
                            @error('thumbnailFile')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror

                            {{-- Preview Poster --}}
                            @if ($thumbnailFile)
                                <div class="mt-2 p-2 border rounded-8 bg-light d-flex align-items-center gap-3">
                                    <img src="{{ $thumbnailFile->temporaryUrl() }}" class="rounded-8 border"
                                        style="height: 56px; width: 56px; object-fit: cover;"
                                        alt="Preview Poster Baru">
                                    <div class="text-xs">
                                        <span class="fw-semibold text-dark d-block">Poster Baru Terpilih</span>
                                        <span class="text-muted">Akan menggantikan poster lama saat disimpan.</span>
                                    </div>
                                </div>
                            @elseif ($course->thumbnail)
                                <div class="mt-2 p-2 border rounded-8 bg-light d-flex align-items-center gap-3">
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" class="rounded-8 border"
                                        style="height: 56px; width: 56px; object-fit: cover;" alt="Poster Saat Ini">
                                    <div class="text-xs">
                                        <span class="badge bg-secondary mb-1">Poster Saat Ini</span>
                                        <span class="text-muted d-block">Poster aktif yang sedang terpasang di
                                            katalog.</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Dokumen TOR / KAK --}}
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Dokumen Kerangka Acuan Kerja / KAK (PDF) <span
                                    class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i
                                        class="ri-file-pdf-line"></i></span>
                                <input type="file" class="form-control @error('torFile') is-invalid @enderror"
                                    wire:model="torFile" accept="application/pdf">
                            </div>
                            <div class="form-text text-xs text-muted mt-1">Format PDF resmi dokumen KAK/TOR (maksimal
                                10 MB). Kosongkan jika tidak diubah.</div>
                            @error('torFile')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror

                            @if ($course->tor_file)
                                <div class="mt-2 p-2 border rounded-8 bg-light d-flex align-items-center gap-2">
                                    <i class="ri-file-pdf-fill text-danger fs-5"></i>
                                    <div class="text-xs">
                                        <span class="fw-semibold text-dark d-block">Dokumen KAK Terlampir</span>
                                        <a href="{{ asset('storage/' . $course->tor_file) }}" target="_blank"
                                            class="text-decoration-none fw-semibold" style="color: #071a33;">
                                            <i class="ri-download-line me-1 text-warning"></i> Unduh / Buka KAK
                                            Eksisting
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action Buttons (Menyatu tanpa garis pemisah) --}}
                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-simpel-gold align-items-center flex-grow-1"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="ri-save-line"></i> Perbarui Pelatihan
                        </span>
                        <span wire:loading style="display: none;">
                            <span class="spinner-border spinner-border-sm"></span> Memperbarui...
                        </span>
                    </button>
                    @if (($form['status'] ?? '') === 'draft')
                        <button type="button" class="btn btn-outline-primary d-inline-flex align-items-center gap-1"
                            wire:click="submitToLeader" wire:loading.attr="disabled"
                            title="Simpan dan ajukan program pelatihan ke Pimpinan">
                            <i class="ri-send-plane-line"></i> Ajukan ke Pimpinan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

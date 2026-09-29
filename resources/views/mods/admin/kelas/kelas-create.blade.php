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
            <h5 class="fw-bold text-dark mb-1">Tambah Kelas Baru</h5>
            <p class="text-muted mb-0">Formulir penambahan data kelas (Batch, Permanen, dan Berbayar).</p>
        </div>
    </div>

    {{-- Main Form Card --}}
    <form wire:submit="formSubmit">
        <div class="card simpel-card border-0 shadow-sm radius-16 overflow-hidden">
            <div
                class="card-header bg-white py-16 px-24 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0 fs-6">Formulir Kelas</h6>
                        <small class="text-muted">Lengkapi data kelas melalui 3 langkah terpandu.</small>
                    </div>
                </div>
                <span class="simpel-badge simpel-badge-navy">
                    Langkah {{ $currentStep }} dari {{ $totalSteps }}
                </span>
            </div>

            {{-- Wizard Step Indicator --}}
            <div class="px-24 pt-20 pb-16 border-bottom bg-light bg-opacity-50">
                <div class="row g-2 align-items-center justify-content-between">
                    {{-- Step 1 --}}
                    <div class="col-4">
                        <button type="button" wire:click="goToStep(1)"
                            class="btn p-0 text-start w-100 border-0 bg-transparent shadow-none"
                            @if ($currentStep === 1) style="cursor: default;" @endif>
                            <div class="d-flex align-items-center gap-2">
                                <span
                                    class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold fs-7 shadow-xs"
                                    style="width: 32px; height: 32px; min-width: 32px;
                                    {{ $currentStep === 1 ? 'background-color: #071a33; color: #f3bc42;' : ($currentStep > 1 ? 'background-color: #198754; color: #fff;' : 'background-color: #e2e8f0; color: #64748b;') }}">
                                    @if ($currentStep > 1)
                                        <i class="ri-check-line"></i>
                                    @else
                                        1
                                    @endif
                                </span>
                                <div class="d-none d-md-block">
                                    <span class="d-block text-xs text-muted text-uppercase fw-semibold"
                                        style="letter-spacing: 0.5px;">Langkah 1</span>
                                    <span
                                        class="d-block fs-8 fw-bold {{ $currentStep === 1 ? 'text-dark' : 'text-muted' }}">Informasi
                                        Dasar</span>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar {{ $currentStep >= 1 ? ($currentStep > 1 ? 'bg-success' : 'bg-warning') : 'bg-light' }}"
                                    style="width: 100%;"></div>
                            </div>
                        </button>
                    </div>

                    {{-- Step 2 --}}
                    <div class="col-4">
                        <button type="button" wire:click="goToStep(2)"
                            class="btn p-0 text-start w-100 border-0 bg-transparent shadow-none"
                            @if ($currentStep === 2) style="cursor: default;" @endif>
                            <div class="d-flex align-items-center gap-2">
                                <span
                                    class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold fs-7 shadow-xs"
                                    style="width: 32px; height: 32px; min-width: 32px;
                                    {{ $currentStep === 2 ? 'background-color: #071a33; color: #f3bc42;' : ($currentStep > 2 ? 'background-color: #198754; color: #fff;' : 'background-color: #e2e8f0; color: #64748b;') }}">
                                    @if ($currentStep > 2)
                                        <i class="ri-check-line"></i>
                                    @else
                                        2
                                    @endif
                                </span>
                                <div class="d-none d-md-block">
                                    <span class="d-block text-xs text-muted text-uppercase fw-semibold"
                                        style="letter-spacing: 0.5px;">Langkah 2</span>
                                    <span
                                        class="d-block fs-8 fw-bold {{ $currentStep === 2 ? 'text-dark' : 'text-muted' }}">Tipe
                                        & Biaya/Jadwal</span>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar {{ $currentStep >= 2 ? ($currentStep > 2 ? 'bg-success' : 'bg-warning') : 'bg-light' }}"
                                    style="width: 100%;"></div>
                            </div>
                        </button>
                    </div>

                    {{-- Step 3 --}}
                    <div class="col-4">
                        <button type="button" wire:click="goToStep(3)"
                            class="btn p-0 text-start w-100 border-0 bg-transparent shadow-none"
                            @if ($currentStep === 3) style="cursor: default;" @endif>
                            <div class="d-flex align-items-center gap-2">
                                <span
                                    class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold fs-7 shadow-xs"
                                    style="width: 32px; height: 32px; min-width: 32px;
                                    {{ $currentStep === 3 ? 'background-color: #071a33; color: #f3bc42;' : 'background-color: #e2e8f0; color: #64748b;' }}">
                                    3
                                </span>
                                <div class="d-none d-md-block">
                                    <span class="d-block text-xs text-muted text-uppercase fw-semibold"
                                        style="letter-spacing: 0.5px;">Langkah 3</span>
                                    <span
                                        class="d-block fs-8 fw-bold {{ $currentStep === 3 ? 'text-dark' : 'text-muted' }}">Berkas
                                        & Status</span>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 4px;">
                                <div class="progress-bar {{ $currentStep === 3 ? 'bg-warning' : 'bg-light' }}"
                                    style="width: 100%;"></div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-28 p-md-32">
                {{-- Langkah 1: Informasi Dasar Kelas --}}
                @if ($currentStep === 1)
                    <div class="mb-32">
                        <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom"
                            style="border-color: #e6eaf0 !important;">
                            <span
                                class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                                style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">1</span>
                            <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">Informasi Dasar Kelas</h6>
                        </div>
                        <div class="row g-3">
                            {{-- Nama Lengkap Kelas --}}
                            <div class="col-12">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Nama Program Kelas <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('form.title') is-invalid @enderror"
                                    wire:model="form.title"
                                    placeholder="Contoh: Manajemen Administrator Angkatan I">
                                @error('form.title')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Kategori Kelas (Dropdown Terintegrasi Kategori) --}}
                            <div class="col-12">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Kategori Kelas <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('form.category_id') is-invalid @enderror"
                                    wire:model="form.category_id">
                                    <option value="">-- Pilih Kategori Kelas --</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('form.category_id')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Deskripsi Program Kelas --}}
                            <div class="col-12">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Deskripsi Kelas
                                </label>
                                <textarea class="form-control @error('form.description') is-invalid @enderror"
                                    wire:model="form.description"
                                    rows="4"
                                    placeholder="Tuliskan deskripsi lengkap, tujuan pembelajaran, atau ringkasan kelas ini..."></textarea>
                                @error('form.description')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">Deskripsi ini akan ditampilkan pada halaman detail kelas dan ringkasannya pada kartu katalog.</small>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Langkah 2: Jenis Kelas & Jadwal Waktu --}}
                @if ($currentStep === 2)
                    <div class="mb-32">
                        <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom"
                            style="border-color: #e6eaf0 !important;">
                            <span
                                class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                                style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">2</span>
                            <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">Jenis Kelas & Jadwal Waktu</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12" wire:key="field-type">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Jenis Kelas <span class="text-danger">*</span>
                                </label>
                                <select id="input_type" class="form-select @error('form.type') is-invalid @enderror"
                                    wire:model.live="form.type">
                                    <option value="batch">Batch (Berdasarkan Gelombang / Periode)</option>
                                    <option value="permanent">Permanen (Akses Mandiri / Self-Paced)</option>
                                    <option value="paid">Berbayar (Kelas Berbayar)</option>
                                </select>
                                <div class="form-text text-xs text-muted mt-1">
                                    @if (($form['type'] ?? '') === 'batch')
                                        <i class="ri-calendar-check-line me-1"></i>Berdasarkan jadwal gelombang.
                                    @elseif(in_array($form['type'] ?? '', ['paid', 'berbayar']))
                                        <i class="ri-money-dollar-circle-line me-1"></i>Program berbayar dengan
                                        penetapan tarif biaya.
                                    @else
                                        <i class="ri-time-line me-1"></i>Akses mandiri (self-paced) sepanjang waktu.
                                    @endif
                                </div>
                                @error('form.type')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Row (Khusus Tipe Batch): Tanggal Mulai & Selesai --}}
                            @if (($form['type'] ?? '') === 'batch')
                                <div class="col-md-6" wire:key="field-start-date">
                                    <label class="form-label text-xs fw-semibold text-dark mb-1">
                                        Tanggal Mulai <span class="text-danger">*</span>
                                    </label>
                                    <input id="input_start_date" type="date"
                                        class="form-control @error('form.start_date') is-invalid @enderror"
                                        wire:model="form.start_date">
                                    @error('form.start_date')
                                        <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6" wire:key="field-end-date">
                                    <label class="form-label text-xs fw-semibold text-dark mb-1">
                                        Tanggal Selesai <span class="text-danger">*</span>
                                    </label>
                                    <input id="input_end_date" type="date"
                                        class="form-control @error('form.end_date') is-invalid @enderror"
                                        wire:model="form.end_date">
                                    @error('form.end_date')
                                        <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            {{-- Row Khusus Kelas Berbayar: Biaya Kelas --}}
                            @if (in_array($form['type'] ?? '', ['paid', 'berbayar']))
                                <div class="col-md-12" wire:key="field-price">
                                    <label class="form-label text-xs fw-semibold text-dark mb-1">
                                        Biaya Kelas (Tarif Masuk) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-dark fw-bold">Rp</span>
                                        <input id="input_price" type="number" min="0" step="1000"
                                            class="form-control @error('form.price') is-invalid @enderror"
                                            wire:model="form.price" placeholder="Contoh: 150000">
                                    </div>
                                    <div class="form-text text-xs text-muted mt-1">Masukkan nominal biaya pendaftaran
                                         kelas berbayar.</div>
                                    @error('form.price')
                                        <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Langkah 3: Dokumen, Lampiran & Status Publikasi --}}
                @if ($currentStep === 3)
                    <div class="mb-32">
                        <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom"
                            style="border-color: #e6eaf0 !important;">
                            <span
                                class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                                style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">3</span>
                            <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">Berkas Lampiran & Status Publikasi
                            </h6>
                        </div>
                        <div class="row g-3">
                            {{-- Status Publikasi --}}
                            <div class="col-12">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Status Kelas <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('form.status') is-invalid @enderror"
                                    wire:model="form.status">
                                    <option value="published">Dibuka (Published - Siap diakses peserta)</option>
                                    <option value="draft">Draft (Konsep - Belum dibuka untuk peserta)</option>
                                    <option value="archived">Diarsipkan</option>
                                </select>
                                <div class="form-text text-xs text-muted mt-1">
                                    Pilih <strong>Draft</strong> jika kelas masih dalam tahap persiapan, atau
                                    <strong>Dibuka</strong> jika sudah siap diakses.
                                </div>
                                @error('form.status')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Poster Thumbnail --}}
                            <div class="col-md-6">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Poster Kelas (JPG/PNG) <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i
                                            class="ri-image-line"></i></span>
                                    <input type="file"
                                        class="form-control @error('thumbnailFile') is-invalid @enderror"
                                        wire:model="thumbnailFile" accept="image/jpeg,image/png">
                                </div>
                                <div class="form-text text-xs text-muted mt-1">Format gambar JPG atau PNG (maksimal 2
                                    MB).</div>
                                @error('thumbnailFile')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror

                                {{-- Preview Poster --}}
                                @if ($thumbnailFile)
                                    <div class="mt-2 p-2 border rounded-8 bg-light d-flex align-items-center gap-3">
                                        <img src="{{ $thumbnailFile->temporaryUrl() }}" class="rounded-8 border"
                                            style="height: 56px; width: 56px; object-fit: cover;"
                                            alt="Preview Poster">
                                        <div class="text-xs">
                                            <span class="fw-semibold text-dark d-block">Poster Baru Terpilih</span>
                                            <span class="text-muted">Berkas akan tersimpan otomatis saat formulir
                                                disubmit.</span>
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
                                <div class="form-text text-xs text-muted mt-1">Format dokumen PDF resmi KAK/TOR
                                    kegiatan (maksimal 10 MB).</div>
                                @error('torFile')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Navigation & Submit Action Buttons --}}
                <div class="d-flex align-items-center justify-content-between gap-3 pt-3 border-top mt-2">
                    <div>
                        @if ($currentStep > 1)
                            <button type="button" wire:click="previousStep"
                                class="btn btn-outline-secondary d-flex align-items-center gap-2 px-4">
                                <i class="ri-arrow-left-line"></i>
                                <span>Sebelumnya</span>
                            </button>
                        @endif
                    </div>

                    <div>
                        @if ($currentStep < $totalSteps)
                            <button type="button" wire:click="nextStep"
                                class="btn btn-simple-gold d-flex align-items-center gap-2 px-4 shadow-sm"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <span>Lanjut ke Langkah {{ $currentStep + 1 }}</span>
                                    <i class="ri-arrow-right-line ms-1"></i>
                                </span>
                                <span wire:loading style="display: none;">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Memvalidasi...
                                </span>
                            </button>
                        @else
                            <button type="submit"
                                class="btn btn-simpel-gold d-flex align-items-center gap-2 px-4 shadow-sm"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove class="d-inline-flex align-items-center gap-2">
                                    <span>Simpan & Lanjut Kelola Materi</span>
                                    <i class="ri-arrow-right-line"></i>
                                </span>
                                <span wire:loading style="display: none;">
                                    <span class="spinner-border spinner-border-sm"></span>
                                    <span>Menyimpan & Membuka Materi...</span>
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

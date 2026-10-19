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
            <p class="text-muted mb-0">Formulir penambahan data kelas (Bulanan, Bangkom, dan Bangkom External).</p>
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
                                        class="d-block fs-8 fw-bold {{ $currentStep === 3 ? 'text-dark' : 'text-muted' }}">Sampul
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
                            {{-- Nama Lengkap Kelas / Platform --}}
                            <div class="col-12">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    {{ ($form['type'] ?? '') === 'bangkom_external' ? 'Nama Platform / Pelatihan Eksternal' : 'Nama Program Kelas' }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('form.title') is-invalid @enderror"
                                    wire:model.blur="form.title"
                                    placeholder="{{ ($form['type'] ?? '') === 'bangkom_external' ? 'Contoh: SiBangkom LAN RI / Kemenkeu Learning Center' : 'Contoh: Manajemen Administrator Angkatan I' }}">
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
                                    wire:model.live="form.category_id">
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
                                    {{ ($form['type'] ?? '') === 'bangkom_external' ? 'Deskripsi Singkat Pelatihan Eksternal' : 'Deskripsi Kelas' }} <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('form.description') is-invalid @enderror" wire:model.blur="form.description"
                                    rows="4" placeholder="{{ ($form['type'] ?? '') === 'bangkom_external' ? 'Ringkasan singkat mengenai platform pelatihan eksternal ini (maks. 255 karakter)...' : 'Tuliskan deskripsi lengkap, tujuan pembelajaran, atau ringkasan kelas ini...' }}"></textarea>
                                @error('form.description')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
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
                            <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">
                                {{ ($form['type'] ?? '') === 'bangkom_external' ? 'Jenis & Tautan Pelatihan Eksternal' : 'Jenis Kelas & Jadwal Waktu' }}
                            </h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12" wire:key="field-type">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Jenis Kelas <span class="text-danger">*</span>
                                </label>
                                <select id="input_type" class="form-select @error('form.type') is-invalid @enderror"
                                    wire:model.live="form.type">
                                    <option value="bulanan">Bulanan</option>
                                    <option value="bangkom">Bangkom</option>
                                    <option value="bangkom_external">Bangkom External</option>
                                </select>
                                <div class="form-text text-xs text-muted mt-1">
                                    @if (in_array($form['type'] ?? '', ['bulanan', 'batch'], true))
                                        <i class="ri-calendar-check-line me-1"></i>Berdasarkan jadwal gelombang periode
                                        bulanan.
                                    @elseif(($form['type'] ?? '') === 'bangkom_external')
                                        <i class="ri-external-link-line me-1"></i>Direktori rujukan pelatihan eksternal dari portal atau instansi luar.
                                    @else
                                        <i class="ri-time-line me-1"></i>Akses mandiri (self-paced) sepanjang waktu.
                                    @endif
                                </div>
                                @error('form.type')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            @if (($form['type'] ?? '') === 'bangkom_external')
                                {{-- Link URL Bangkom Eksternal --}}
                                <div class="col-md-12" wire:key="field-url">
                                    <label class="form-label text-xs fw-semibold text-dark mb-1">
                                        Link URL Pelatihan Eksternal <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="ri-link"></i></span>
                                        <input id="input_url" type="url"
                                            class="form-control @error('form.url') is-invalid @enderror"
                                            wire:model="form.url" placeholder="https://contoh-pelatihan.go.id">
                                    </div>
                                    <div class="form-text text-xs text-muted mt-1">Alamat website tujuan yang dapat diakses publik (contoh: https://sibangkom.lan.go.id).</div>
                                    @error('form.url')
                                        <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Tipe Biaya: Gratis atau Berbayar --}}
                                <div class="col-md-6" wire:key="field-external-type">
                                    <label class="form-label text-xs fw-semibold text-dark mb-1">
                                        Tipe Pelatihan Eksternal <span class="text-danger">*</span>
                                    </label>
                                    <select id="input_external_type" class="form-select @error('form.external_type') is-invalid @enderror"
                                        wire:model="form.external_type">
                                        <option value="gratis">Gratis</option>
                                        <option value="berbayar">Berbayar</option>
                                    </select>
                                    <div class="form-text text-xs text-muted mt-1">Keterangan apakah pelatihan eksternal ini gratis atau berbayar.</div>
                                    @error('form.external_type')
                                        <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Benefit Pelatihan (contoh: Sertifikat, Piagam dkk) --}}
                                <div class="col-md-6" wire:key="field-benefit">
                                    <label class="form-label text-xs fw-semibold text-dark mb-1">
                                        Benefit Pelatihan <span class="text-muted fw-normal">(Opsional)</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="ri-award-line"></i></span>
                                        <input id="input_benefit" type="text"
                                            class="form-control @error('form.benefit') is-invalid @enderror"
                                            wire:model="form.benefit" placeholder="Contoh: Sertifikat, Piagam dkk">
                                    </div>
                                    <div class="form-text text-xs text-muted mt-1">Benefit atau penghargaan kelulusan (contoh: Sertifikat, Piagam dkk).</div>
                                    @error('form.benefit')
                                        <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                {{-- Estimasi Jam Pelajaran (JP) --}}
                                <div class="col-md-12" wire:key="field-jam-pelajaran">
                                    <label class="form-label text-xs fw-semibold text-dark mb-1">
                                        Jam Pelajaran (JP) / Bobot Pelatihan <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input id="input_jam_pelajaran" type="text"
                                            class="form-control @error('form.jam_pelajaran') is-invalid @enderror"
                                            wire:model="form.jam_pelajaran" placeholder="Contoh: 24 atau 3,5">
                                        <span class="input-group-text bg-light text-dark fw-bold">JP</span>
                                    </div>
                                    <div class="form-text text-xs text-muted mt-1">
                                        Estimasi bobot jam pelajaran (1 JP = 45 menit) yang akan tercantum pada sertifikat kelulusan peserta.
                                    </div>
                                    @error('form.jam_pelajaran')
                                        <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Row (Khusus Tipe Bulanan): Tanggal & Jam Mulai & Selesai --}}
                                @if (in_array($form['type'] ?? '', ['bulanan', 'batch'], true))
                                    <div class="col-md-6" wire:key="field-start-date">
                                        <label class="form-label text-xs fw-semibold text-dark mb-1">
                                            Tanggal & Jam Mulai <span class="text-danger">*</span>
                                        </label>
                                        <input id="input_start_date" type="datetime-local"
                                            class="form-control @error('form.start_date') is-invalid @enderror"
                                            wire:model="form.start_date">
                                        <div class="form-text text-xs text-muted mt-1">
                                            Pilih tanggal dan jam mulai program bulanan (tidak boleh sebelum hari ini).
                                        </div>
                                        @error('form.start_date')
                                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6" wire:key="field-end-date">
                                        <label class="form-label text-xs fw-semibold text-dark mb-1">
                                            Tanggal & Jam Selesai <span class="text-danger">*</span>
                                        </label>
                                        <input id="input_end_date" type="datetime-local"
                                            class="form-control @error('form.end_date') is-invalid @enderror"
                                            wire:model="form.end_date">
                                        <div class="form-text text-xs text-muted mt-1">
                                            Pilih tanggal dan jam selesai program bulanan (harus setelah waktu mulai).
                                        </div>
                                        @error('form.end_date')
                                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Langkah 3: Sampul Kelas & Status Publikasi --}}
                @if ($currentStep === 3)
                    <div class="mb-32">
                        <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom"
                            style="border-color: #e6eaf0 !important;">
                            <span
                                class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                                style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">3</span>
                            <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">Sampul Kelas & Status Publikasi
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

                            {{-- Poster / Logo Thumbnail (Single Upload) --}}
                            <div class="col-12">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    {{ ($form['type'] ?? '') === 'bangkom_external' ? 'Logo Platform / Instansi Pelatihan Eksternal (JPG/PNG)' : 'Poster / Sampul Thumbnail Kelas (JPG/PNG)' }} <span
                                        class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i
                                            class="ri-image-line"></i></span>
                                    <input type="file"
                                        class="form-control @error('thumbnailFile') is-invalid @enderror"
                                        wire:model="thumbnailFile" accept="image/jpeg,image/png,image/jpg">
                                </div>
                                <div class="form-text text-xs text-muted mt-1">
                                    {{ ($form['type'] ?? '') === 'bangkom_external' ? 'Format gambar JPG, JPEG, atau PNG (maksimal 2 MB). Logo ini akan ditampilkan pada kartu direktori Bangkom Eksternal.' : 'Format gambar JPG, JPEG, atau PNG (maksimal 2 MB). Gambar ini digunakan sebagai sampul visual utama kelas.' }}
                                </div>
                                @error('thumbnailFile')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror

                                {{-- Preview Poster / Logo --}}
                                @if ($thumbnailFile)
                                    <div class="mt-3 p-3 border rounded-8 bg-light d-flex align-items-center gap-3">
                                        <img src="{{ $thumbnailFile->temporaryUrl() }}" class="rounded-8 border"
                                            style="height: 64px; width: 64px; object-fit: contain; background-color: #fff;"
                                            alt="Preview Poster/Logo">
                                        <div class="text-xs">
                                            <span class="fw-semibold text-dark d-block">
                                                {{ ($form['type'] ?? '') === 'bangkom_external' ? 'Logo Baru Terpilih' : 'Poster Baru Terpilih' }}
                                            </span>
                                            <span class="text-muted">Berkas akan tersimpan otomatis saat formulir
                                                disubmit.</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Navigation & Submit Action Buttons --}}
                <div class="d-flex align-items-center justify-content-between gap-3 pt-3 border-top mt-2">
                    <div>
                        @if ($currentStep > 1)
                            <button type="button" wire:click="previousStep"
                                class="btn btn-outline-danger d-flex align-items-center gap-2">
                                <i class="ri-arrow-left-line"></i>
                                <span>Sebelumnya</span>
                            </button>
                        @endif
                    </div>

                    <div>
                        @if ($currentStep < $totalSteps)
                            <button type="button" wire:click="nextStep"
                                class="btn btn-simple-gold d-flex align-items-center gap-2 shadow-sm"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <span>Lanjut</span>
                                    <i class="ri-arrow-right-line ms-1"></i>
                                </span>
                                <span wire:loading style="display: none;">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Memvalidasi...
                                </span>
                            </button>
                        @else
                            @if (($form['type'] ?? '') === 'bangkom_external')
                                <button type="submit"
                                    class="btn btn-simpel-gold d-flex align-items-center gap-2 shadow-sm"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove class="d-inline-flex align-items-center gap-2">
                                        <i class="ri-checkbox-circle-line fs-5"></i>
                                        <span>Simpan Pelatihan Eksternal</span>
                                    </span>
                                    <span wire:loading style="display: none;">
                                        <span class="spinner-border spinner-border-sm"></span>
                                        <span>Menyimpan Pelatihan Eksternal...</span>
                                    </span>
                                </button>
                            @else
                                <button type="submit"
                                    class="btn btn-simpel-gold d-flex align-items-center gap-2 shadow-sm"
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

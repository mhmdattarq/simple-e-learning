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
            <h5 class="fw-bold text-dark mb-1">Edit Kelas</h5>
            <p class="text-muted mb-0">Formulir pembaruan data kelas (Batch, Permanen, dan Berbayar).</p>
        </div>
        <div>
            <a href="{{ route('kelas.data') }}" class="btn btn-danger d-flex align-items-center" wire:navigate>
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
                        <h6 class="fw-bold text-dark mb-0 fs-6">Formulir Pembaruan Kelas</h6>
                        <small class="text-muted">Perubahan data kelas akan langsung diperbarui di katalog.</small>
                    </div>
                </div>
                <span class="simpel-badge simpel-badge-navy">
                    Kelas
                </span>
            </div>

            <div class="card-body p-28 p-md-32">
                {{-- Bagian 1: Informasi Dasar Kelas --}}
                <div class="mb-40">
                    <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom"
                        style="border-color: #e6eaf0 !important;">
                        <span
                            class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                            style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">1</span>
                        <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">Informasi Dasar Kelas</h6>
                    </div>
                    <div class="row g-3">
                        {{-- Kategori Kelas (Dropdown Terintegrasi Kategori) --}}
                        <div class="col-md-6">
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

                        {{-- Status Kelas --}}
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Status Kelas <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('form.status') is-invalid @enderror"
                                wire:model="form.status">
                                <option value="published">Dibuka (Published)</option>
                                <option value="draft">Draft (Konsep)</option>
                                <option value="archived">Diarsipkan</option>
                            </select>
                            @error('form.status')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama Lengkap Kelas --}}
                        <div class="col-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Nama Program Kelas <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('form.title') is-invalid @enderror"
                                wire:model.blur="form.title" placeholder="Contoh: Manajemen Administrator Angkatan I">
                            @error('form.title')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deskripsi Program Kelas --}}
                        <div class="col-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Deskripsi Kelas <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('form.description') is-invalid @enderror" wire:model.blur="form.description"
                                rows="4" placeholder="Tuliskan deskripsi lengkap, tujuan pembelajaran, atau ringkasan kelas ini..."></textarea>
                            @error('form.description')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Jenis Kelas & Jadwal Waktu --}}
                <div class="mb-40">
                    <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom"
                        style="border-color: #e6eaf0 !important;">
                        <span
                            class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                            style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">2</span>
                        <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">Jenis Kelas & Jadwal Waktu</h6>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-12" wire:key="edit-field-type">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Jenis Kelas <span class="text-danger">*</span>
                            </label>
                            <select id="edit_input_type" class="form-select @error('form.type') is-invalid @enderror"
                                wire:model.live="form.type">
                                <option value="batch">Batch (Berdasarkan Gelombang / Periode)</option>
                                <option value="permanent">Permanen (Akses Mandiri / Self-Paced)</option>
                                <option value="paid">Berbayar (Kelas Berbayar)</option>
                            </select>
                            <div class="form-text text-xs text-muted mt-1">
                                @if (($form['type'] ?? '') === 'batch')
                                    <i class="ri-calendar-check-line me-1"></i>Berdasarkan jadwal gelombang.
                                @elseif(in_array($form['type'] ?? '', ['paid', 'berbayar']))
                                    <i class="ri-money-dollar-circle-line me-1"></i>Program berbayar dengan penetapan
                                    tarif biaya.
                                @else
                                    <i class="ri-time-line me-1"></i>Akses mandiri (self-paced) sepanjang waktu.
                                @endif
                            </div>
                            @error('form.type')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Row (Khusus Tipe Batch): Tanggal & Jam Mulai & Selesai --}}
                        @if (($form['type'] ?? '') === 'batch')
                            <div class="col-md-6" wire:key="edit-field-start-date">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Tanggal & Jam Mulai <span class="text-danger">*</span>
                                </label>
                                <input id="edit_input_start_date" type="datetime-local"
                                    class="form-control @error('form.start_date') is-invalid @enderror"
                                    wire:model="form.start_date">
                                <div class="form-text text-xs text-muted mt-1">
                                    Pilih tanggal dan jam mulai kelas batch.
                                </div>
                                @error('form.start_date')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6" wire:key="edit-field-end-date">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Tanggal & Jam Selesai <span class="text-danger">*</span>
                                </label>
                                <input id="edit_input_end_date" type="datetime-local"
                                    class="form-control @error('form.end_date') is-invalid @enderror"
                                    wire:model="form.end_date">
                                <div class="form-text text-xs text-muted mt-1">
                                    Pilih tanggal dan jam selesai kelas batch (harus setelah waktu mulai).
                                </div>
                                @error('form.end_date')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        {{-- Row Khusus Kelas Berbayar: Biaya Kelas --}}
                        @if (in_array($form['type'] ?? '', ['paid', 'berbayar']))
                            <div class="col-md-12" wire:key="edit-field-price">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Biaya Kelas (Tarif Masuk) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-dark fw-bold">Rp</span>
                                    <input id="edit_input_price" type="number" min="0" step="1000"
                                        class="form-control @error('form.price') is-invalid @enderror"
                                        wire:model="form.price" placeholder="Contoh: 150000">
                                </div>
                                <div class="form-text text-xs text-muted mt-1">Masukkan nominal biaya pendaftaran kelas
                                    berbayar.</div>
                                @error('form.price')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Bagian 3: Sampul Kelas --}}
                <div class="mb-40">
                    <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom"
                        style="border-color: #e6eaf0 !important;">
                        <span
                            class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                            style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">3</span>
                        <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">Sampul Thumbnail Kelas</h6>
                    </div>
                    <div class="row g-3">
                        {{-- Poster Thumbnail (Single Upload) --}}
                        <div class="col-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Poster / Thumbnail Sampul Kelas (JPG/PNG) <span
                                    class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i
                                        class="ri-image-line"></i></span>
                                <input type="file"
                                    class="form-control @error('thumbnailFile') is-invalid @enderror"
                                    wire:model="thumbnailFile" accept="image/jpeg,image/png,image/jpg">
                            </div>
                            <div class="form-text text-xs text-muted mt-1">Format gambar JPG, JPEG, atau PNG (maksimal
                                2 MB). Kosongkan jika tidak ingin mengubah sampul.</div>
                            @error('thumbnailFile')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror

                            {{-- Preview Poster --}}
                            @if ($thumbnailFile)
                                <div class="mt-3 p-3 border rounded-8 bg-light d-flex align-items-center gap-3">
                                    <img src="{{ $thumbnailFile->temporaryUrl() }}" class="rounded-8 border"
                                        style="height: 64px; width: 64px; object-fit: cover;"
                                        alt="Preview Poster Baru">
                                    <div class="text-xs">
                                        <span class="fw-semibold text-dark d-block">Poster Baru Terpilih</span>
                                        <span class="text-muted">Akan menggantikan poster lama saat disimpan.</span>
                                    </div>
                                </div>
                            @elseif ($course->thumbnail)
                                <div class="mt-3 p-3 border rounded-8 bg-light d-flex align-items-center gap-3">
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" class="rounded-8 border"
                                        style="height: 64px; width: 64px; object-fit: cover;" alt="Poster Saat Ini">
                                    <div class="text-xs">
                                        <span class="badge bg-secondary mb-1">Poster Saat Ini</span>
                                        <span class="text-muted d-block">Poster aktif yang sedang terpasang di
                                            katalog.</span>
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
                            <i class="ri-save-line"></i> Perbarui Kelas
                        </span>
                        <span wire:loading style="display: none;">
                            <span class="spinner-border spinner-border-sm"></span> Memperbarui...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

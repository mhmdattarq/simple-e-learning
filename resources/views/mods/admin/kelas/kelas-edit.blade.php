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
            <h5 class="fw-bold text-dark mb-1">{{ $isExternal ? 'Edit Pelatihan Eksternal' : 'Edit Kelas' }}</h5>
            <p class="text-muted mb-0">{{ $isExternal ? 'Formulir pembaruan rujukan pelatihan eksternal.' : 'Formulir pembaruan data kelas (Bulanan, Bangkom, dan Bangkom External).' }}</p>
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
                        <h6 class="fw-bold text-dark mb-0 fs-6">{{ $isExternal ? 'Formulir Pembaruan Pelatihan Eksternal' : 'Formulir Pembaruan Kelas' }}</h6>
                        <small class="text-muted">Perubahan data akan langsung diperbarui di katalog.</small>
                    </div>
                </div>
                <span class="simpel-badge {{ $isExternal ? 'simpel-badge-gold' : 'simpel-badge-navy' }}">
                    {{ $isExternal ? 'Bangkom Eksternal' : 'Kelas' }}
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
                        <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">Informasi Dasar</h6>
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
                                Status Publikasi <span class="text-danger">*</span>
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

                        {{-- Nama Lengkap Kelas / Platform --}}
                        <div class="col-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                {{ $isExternal ? 'Nama Platform / Pelatihan Eksternal' : 'Nama Program Kelas' }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('form.title') is-invalid @enderror"
                                wire:model.blur="form.title" placeholder="{{ $isExternal ? 'Contoh: SiBangkom LAN RI / Kemenkeu Learning Center' : 'Contoh: Manajemen Administrator Angkatan I' }}">
                            @error('form.title')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deskripsi Program Kelas --}}
                        <div class="col-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                {{ $isExternal ? 'Deskripsi Singkat Pelatihan Eksternal' : 'Deskripsi Kelas' }} <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('form.description') is-invalid @enderror" wire:model.blur="form.description"
                                rows="4" placeholder="{{ $isExternal ? 'Ringkasan singkat mengenai platform pelatihan eksternal ini (maks. 255 karakter)...' : 'Tuliskan deskripsi lengkap, tujuan pembelajaran, atau ringkasan kelas ini...' }}"></textarea>
                            @error('form.description')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Jenis Kelas & Jadwal Waktu / Tautan Eksternal --}}
                <div class="mb-40">
                    <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom"
                        style="border-color: #e6eaf0 !important;">
                        <span
                            class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                            style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">2</span>
                        <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">
                            {{ $isExternal ? 'Jenis & Tautan Pelatihan Eksternal' : 'Jenis Kelas & Jadwal Waktu' }}
                        </h6>
                    </div>
                    <div class="row g-3">
                        @if ($isExternal)
                            {{-- Jenis Kelas (Readonly / Locked to Bangkom External) --}}
                            <div class="col-md-12" wire:key="edit-field-type-external">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Jenis Pelatihan
                                </label>
                                <input type="text" class="form-control bg-light" value="Bangkom External (Rujukan Pelatihan Luar)" readonly>
                                <div class="form-text text-xs text-muted mt-1">
                                    <i class="ri-external-link-line me-1"></i>Direktori rujukan pelatihan eksternal dari portal atau instansi luar.
                                </div>
                            </div>

                            {{-- Link URL Bangkom Eksternal --}}
                            <div class="col-md-12" wire:key="edit-field-url">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Link URL Pelatihan Eksternal <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="ri-link"></i></span>
                                    <input id="edit_input_url" type="url"
                                        class="form-control @error('form.url') is-invalid @enderror"
                                        wire:model="form.url" placeholder="https://contoh-pelatihan.go.id">
                                </div>
                                <div class="form-text text-xs text-muted mt-1">Alamat website tujuan yang dapat diakses publik (contoh: https://sibangkom.lan.go.id).</div>
                                @error('form.url')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Tipe Biaya: Gratis atau Berbayar --}}
                            <div class="col-md-6" wire:key="edit-field-external-type">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Tipe Pelatihan Eksternal <span class="text-danger">*</span>
                                </label>
                                <select id="edit_input_external_type" class="form-select @error('form.external_type') is-invalid @enderror"
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
                            <div class="col-md-6" wire:key="edit-field-benefit">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Benefit Pelatihan <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="ri-award-line"></i></span>
                                    <input id="edit_input_benefit" type="text"
                                        class="form-control @error('form.benefit') is-invalid @enderror"
                                        wire:model="form.benefit" placeholder="Contoh: Sertifikat, Piagam dkk">
                                </div>
                                <div class="form-text text-xs text-muted mt-1">Benefit atau penghargaan kelulusan (contoh: Sertifikat, Piagam dkk).</div>
                                @error('form.benefit')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="col-md-12" wire:key="edit-field-type">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Jenis Kelas <span class="text-danger">*</span>
                                </label>
                                <select id="edit_input_type" class="form-select @error('form.type') is-invalid @enderror"
                                    wire:model.live="form.type">
                                    <option value="bulanan">Bulanan</option>
                                    <option value="bangkom">Bangkom</option>
                                </select>
                                <div class="form-text text-xs text-muted mt-1">
                                    @if (in_array($form['type'] ?? '', ['bulanan', 'batch'], true))
                                        <i class="ri-calendar-check-line me-1"></i>Berdasarkan jadwal gelombang periode bulanan.
                                    @else
                                        <i class="ri-time-line me-1"></i>Akses mandiri (self-paced) sepanjang waktu.
                                    @endif
                                </div>
                                @error('form.type')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Estimasi Jam Pelajaran (JP) --}}
                            <div class="col-md-12" wire:key="edit-field-jam-pelajaran">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Jam Pelajaran (JP) / Bobot Pelatihan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input id="edit_input_jam_pelajaran" type="text"
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
                                <div class="col-md-6" wire:key="edit-field-start-date">
                                    <label class="form-label text-xs fw-semibold text-dark mb-1">
                                        Tanggal & Jam Mulai <span class="text-danger">*</span>
                                    </label>
                                    <input id="edit_input_start_date" type="datetime-local"
                                        class="form-control @error('form.start_date') is-invalid @enderror"
                                        wire:model="form.start_date">
                                    <div class="form-text text-xs text-muted mt-1">
                                        Pilih tanggal dan jam mulai program bulanan.
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

                {{-- Bagian 3: Sampul / Logo Kelas --}}
                <div class="mb-40">
                    <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom"
                        style="border-color: #e6eaf0 !important;">
                        <span
                            class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                            style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">3</span>
                        <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">
                            {{ $isExternal ? 'Logo Platform / Instansi Pelatihan Eksternal' : 'Sampul Thumbnail Kelas' }}
                        </h6>
                    </div>
                    <div class="row g-3">
                        {{-- Poster / Logo (Single Upload) --}}
                        <div class="col-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                {{ $isExternal ? 'Logo Platform / Instansi Pelatihan Eksternal (JPG/PNG)' : 'Poster / Thumbnail Sampul Kelas (JPG/PNG)' }} <span
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
                                {{ $isExternal ? 'Format gambar JPG, JPEG, atau PNG (maksimal 2 MB). Kosongkan jika tidak ingin mengubah logo.' : 'Format gambar JPG, JPEG, atau PNG (maksimal 2 MB). Kosongkan jika tidak ingin mengubah sampul.' }}
                            </div>
                            @error('thumbnailFile')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror

                            {{-- Preview Poster / Logo --}}
                            @if ($thumbnailFile)
                                <div class="mt-3 p-3 border rounded-8 bg-light d-flex align-items-center gap-3">
                                    <img src="{{ $thumbnailFile->temporaryUrl() }}" class="rounded-8 border"
                                        style="height: 64px; width: 64px; object-fit: contain; background-color: #fff;"
                                        alt="Preview Poster/Logo Baru">
                                    <div class="text-xs">
                                        <span class="fw-semibold text-dark d-block">
                                            {{ $isExternal ? 'Logo Baru Terpilih' : 'Poster Baru Terpilih' }}
                                        </span>
                                        <span class="text-muted">Akan menggantikan visual lama saat disimpan.</span>
                                    </div>
                                </div>
                            @elseif ($isExternal && $courseExternal?->logo)
                                <div class="mt-3 p-3 border rounded-8 bg-light d-flex align-items-center gap-3">
                                    <img src="{{ asset('storage/' . $courseExternal->logo) }}" class="rounded-8 border p-1"
                                        style="height: 64px; width: 64px; object-fit: contain; background-color: #fff;" alt="Logo Saat Ini">
                                    <div class="text-xs">
                                        <span class="badge bg-secondary mb-1">Logo Saat Ini</span>
                                        <span class="text-muted d-block">Logo aktif yang sedang terpasang di direktori Bangkom Eksternal.</span>
                                    </div>
                                </div>
                            @elseif (! $isExternal && $course?->thumbnail)
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

                {{-- Action Buttons --}}
                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-simpel-gold align-items-center flex-grow-1"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove class="d-inline-flex align-items-center justify-content-center gap-2">
                            <i class="ri-save-line fs-5"></i>
                            <span>{{ $isExternal ? 'Perbarui Pelatihan Eksternal' : 'Perbarui Kelas' }}</span>
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

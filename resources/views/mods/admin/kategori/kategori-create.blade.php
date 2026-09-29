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
            <h5 class="fw-bold text-dark mb-1">Tambah Kategori Baru</h5>
            <p class="text-muted mb-0">Formulir penambahan rumpun atau klasifikasi bidang kelas pelatihan.</p>
        </div>
    </div>

    {{-- Main Form Card --}}
    <form wire:submit="formSubmit">
        <div class="card simpel-card border-0 shadow-sm radius-16">
            <div
                class="card-header bg-white py-16 px-24 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0 fs-6">Formulir Kategori Kelas</h6>
                        <small class="text-muted">Lengkapi nama dan deskripsi kategori kelas pembelajaran.</small>
                    </div>
                </div>
                <span class="simpel-badge simpel-badge-navy">
                    Kategori Kelas
                </span>
            </div>

            <div class="card-body p-28 p-md-32">
                <div class="mb-32">
                    <div class="row g-3">
                        {{-- Nama Kategori --}}
                        <div class="col-md-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Nama Kategori <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('form.name') is-invalid @enderror"
                                wire:model.blur="form.name"
                                placeholder="Contoh: Pelatihan Kepemimpinan, Pelatihan Teknis, dsb.">
                            @error('form.name')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Deskripsi Kategori --}}
                        <div class="col-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Deskripsi Kategori <span class="text-danger">*</span>
                            </label>
                            <textarea rows="4" class="form-control @error('form.description') is-invalid @enderror"
                                wire:model.blur="form.description"
                                placeholder="Jelaskan ruang lingkup materi atau target kompetensi yang dicakup oleh kategori ini..."></textarea>
                            @error('form.description')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Action Submit --}}
                <div class="d-flex align-items-center justify-content-end gap-2 pt-20">
                    <button type="submit" class="btn btn-simple-gold d-inline-flex align-items-center w-100"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="formSubmit">
                            <i class="ri-check-line"></i> Simpan Kategori
                        </span>
                        <span wire:loading wire:target="formSubmit">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

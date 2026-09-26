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
    {{-- Header & Navigasi Kembali --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Tambah Jadwal Sesi Baru</h5>
            <p class="text-muted mb-0">Formulir penentuan waktu, ruangan/tautan daring, dan penugasan mentor pada Tahap 4 (Penjadwalan Diklat ASN).</p>
        </div>
        <div>
            <a href="{{ route('penjadwalan.data') }}" class="btn btn-danger d-flex align-items-center" wire:navigate>
                <i class="ri-arrow-left-line me-1"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    {{-- Alert Banner jika ada konflik anti-bentrok --}}
    @if ($conflictError)
        <div class="alert alert-danger d-flex align-items-start gap-2 mb-24 py-3 px-3 radius-12 border-0 shadow-sm">
            <i class="ri-error-warning-fill fs-5 mt-1 text-danger"></i>
            <div>
                <strong class="d-block mb-1">Peringatan Jadwal Bentrok!</strong>
                <div>{{ $conflictError }}</div>
            </div>
        </div>
    @endif

    {{-- Main Form Card --}}
    <form wire:submit="formSubmit">
        <div class="card simpel-card border-0 shadow-sm radius-16">
            <div class="card-header bg-white py-16 px-24 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0 fs-6">Formulir Jadwal Sesi Pelatihan</h6>
                        <small class="text-muted">Lengkapi rincian sesi materi, waktu pelaksanaan, dan penugasan mentor aparatur.</small>
                    </div>
                </div>
                <span class="simpel-badge simpel-badge-navy">
                    Tahap 4: Penjadwalan
                </span>
            </div>

            <div class="card-body p-28 p-md-32">
                {{-- Bagian 1: Pelatihan & Agenda Sesi --}}
                <div class="mb-40">
                    <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom" style="border-color: #e6eaf0 !important;">
                        <span class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                            style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">1</span>
                        <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">Pelatihan & Agenda Sesi</h6>
                    </div>
                    <div class="row g-3">
                        {{-- Program Pelatihan --}}
                        <div class="col-md-7">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Program Pelatihan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('form.course_id') is-invalid @enderror" wire:model="form.course_id">
                                <option value="">-- Pilih Program Pelatihan (Disetujui / Dibuka) --</option>
                                @foreach ($courses as $c)
                                    @php
                                        $periodText = $c->isPermanent() ? 'Mandiri' : ($c->start_date && $c->end_date ? $c->start_date->format('d/m/y').'-'.$c->end_date->format('d/m/y') : 'Batch');
                                    @endphp
                                    <option value="{{ $c->id }}">{{ $c->code }} — {{ $c->title }} [{{ $c->status->label() }} &bull; {{ $periodText }}]</option>
                                @endforeach
                            </select>
                            @error('form.course_id')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status Agenda Sesi --}}
                        <div class="col-md-5">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Status Agenda Sesi <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('form.status') is-invalid @enderror" wire:model="form.status">
                                <option value="scheduled">Scheduled (Terjadwal)</option>
                                <option value="ongoing">Ongoing (Sedang Berlangsung)</option>
                                <option value="completed">Completed (Tuntas Dilaksanakan)</option>
                                <option value="cancelled">Cancelled (Dibatalkan)</option>
                            </select>
                            @error('form.status')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Judul Materi / Agenda Sesi --}}
                        <div class="col-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Judul Materi / Agenda Sesi <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('form.session_title') is-invalid @enderror"
                                wire:model="form.session_title" placeholder="Contoh: Pengantar Transformasi Digital SPBE & Keamanan Siber ASN">
                            @error('form.session_title')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Waktu Pelaksanaan & Narasumber --}}
                <div class="mb-40">
                    <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom" style="border-color: #e6eaf0 !important;">
                        <span class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                            style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">2</span>
                        <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">Waktu Pelaksanaan & Narasumber</h6>
                    </div>
                    <div class="row g-3">
                        {{-- Tanggal Pelaksanaan --}}
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Tanggal Pelaksanaan <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('form.session_date') is-invalid @enderror"
                                wire:model="form.session_date">
                            @error('form.session_date')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jam Mulai --}}
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Jam Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="time" class="form-control @error('form.start_time') is-invalid @enderror"
                                wire:model="form.start_time">
                            @error('form.start_time')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jam Selesai --}}
                        <div class="col-md-4">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Jam Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="time" class="form-control @error('form.end_time') is-invalid @enderror"
                                wire:model="form.end_time">
                            @error('form.end_time')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Narasumber / Pengampu Sesi --}}
                        <div class="col-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Narasumber / Mentor Pengampu <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('form.mentor_id') is-invalid @enderror" wire:model="form.mentor_id">
                                <option value="">-- Pilih Narasumber / Mentor --</option>
                                @foreach ($mentors as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }} (NIP: {{ $m->nip ?? '-' }}) — {{ $m->role instanceof \App\Enums\Role ? $m->role->label() : ucfirst($m->role) }}</option>
                                @endforeach
                            </select>
                            <div class="form-text text-xs text-muted mt-1">Sistem otomatis mendeteksi ketersediaan jadwal narasumber agar tidak bentrok dengan sesi lain.</div>
                            @error('form.mentor_id')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Bagian 3: Lokasi / Tautan Pertemuan --}}
                <div class="mb-40">
                    <div class="d-flex align-items-center gap-2 mb-20 pb-12 border-bottom" style="border-color: #e6eaf0 !important;">
                        <span class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                            style="width: 24px; height: 24px; font-size: 11.5px; background-color: #071a33; color: #f3bc42;">3</span>
                        <h6 class="fw-bold mb-0 fs-6" style="color: #071a33;">Lokasi Fisik / Tautan Kelas Daring</h6>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Ruangan Fisik atau Tautan URL Pertemuan <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="ri-map-pin-2-line"></i></span>
                                <input type="text" class="form-control @error('form.room_or_link') is-invalid @enderror"
                                    wire:model="form.room_or_link" placeholder="Contoh: Aula Gedung Diklat BKPSDM Aceh Timur atau https://zoom.us/j/8923482348">
                            </div>
                            <div class="form-text text-xs text-muted mt-1">Masukkan nama ruangan gedung untuk pelatihan tatap muka (luring) atau tautan Zoom/Google Meet untuk daring.</div>
                            @error('form.room_or_link')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex align-items-center">
                    <button type="submit" class="btn btn-simple-gold d-flex align-items-center justify-content-center gap-2 w-100"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="ri-save-line fs-5"></i> Simpan Jadwal Sesi
                        </span>
                        <span wire:loading style="display: none;">
                            <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

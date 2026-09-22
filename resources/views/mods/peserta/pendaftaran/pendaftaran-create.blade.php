<div class="py-5 bg-light" style="min-height: 85vh;">
    <div class="container py-lg-4 py-2">
        {{-- Breadcrumb & Back --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('landing') }}#pelatihan" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                <i class="ri-arrow-left-line me-1"></i> Kembali ke Katalog
            </a>
            <span class="badge bg-gold text-navy fw-bold px-3 py-1_5 rounded-pill fs-8">Tahap 2: Pendaftaran Peserta</span>
        </div>

        <div class="row g-4 justify-content-center">
            {{-- Kolom Kiri: Ringkasan Program Pelatihan --}}
            <div class="col-lg-4 col-md-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 90px;">
                    {{-- Header / Thumbnail --}}
                    @if ($course->thumbnail)
                        <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="w-100 object-fit-cover" style="height: 180px;">
                    @else
                        <div class="p-4 text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #071a33 0%, #0c3158 100%); height: 160px;">
                            <div class="text-center">
                                <i class="ri-book-open-line display-4 text-gold mb-2 d-block"></i>
                                <span class="badge bg-gold text-navy fw-bold fs-8">{{ $course->category?->name ?? 'Diklat ASN' }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-light text-navy border border-simpel font-monospace fs-8">{{ $course->code }}</span>
                            <span class="badge bg-success-subtle text-success fs-8">
                                @if ($course->isPermanent())
                                    <i class="ri-infinite-line me-1"></i>Mandiri
                                @else
                                    <i class="ri-calendar-line me-1"></i>Batch
                                @endif
                            </span>
                        </div>

                        <h5 class="fw-bold text-navy mb-2">{{ $course->title }}</h5>
                        <p class="text-muted fs-7 mb-3 line-clamp-3">{{ $course->description ?: 'Pelatihan kompetensi aparatur pemerintah yang diselenggarakan oleh BKPSDM Kabupaten Aceh Timur.' }}</p>

                        <hr class="my-3 border-simpel">

                        {{-- Metadata List --}}
                        <div class="vstack gap-2 fs-7">
                            <div class="d-flex justify-content-between text-muted">
                                <span><i class="ri-radar-line me-1 text-gold"></i>Metode:</span>
                                <strong class="text-dark">{{ ucfirst($course->method) }}</strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted">
                                <span><i class="ri-user-line me-1 text-gold"></i>Kuota Peserta:</span>
                                <strong class="text-dark">{{ $course->quota }} ASN</strong>
                            </div>
                            @if ($course->isBatch())
                                <div class="d-flex justify-content-between text-muted">
                                    <span><i class="ri-calendar-event-line me-1 text-gold"></i>Periode:</span>
                                    <strong class="text-dark">{{ $course->start_date?->format('d M') }} - {{ $course->end_date?->format('d M Y') }}</strong>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between text-muted">
                                <span><i class="ri-map-pin-line me-1 text-gold"></i>Lokasi:</span>
                                <strong class="text-dark">{{ $course->location ?: 'Online / LMS SIMPEL' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Form Pendaftaran / Status Terdaftar --}}
            <div class="col-lg-8 col-md-7">
                @if ($alreadyRegistered)
                    {{-- Tampilan Status Jika Sudah Terdaftar --}}
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-lg-5 text-center">
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" style="width: 72px; height: 72px; background: rgba(243, 188, 66, 0.15);">
                            <i class="ri-checkbox-circle-fill display-5 text-success"></i>
                        </div>

                        <h4 class="fw-bold text-navy mb-1">
                            @if ($registrationSuccess)
                                Pendaftaran Berhasil Dikirim!
                            @else
                                Anda Telah Terdaftar pada Pelatihan Ini
                            @endif
                        </h4>
                        <p class="text-muted fs-6 mb-4">
                            Nomor Registrasi Anda: <strong class="text-dark font-monospace fs-5">{{ $existingRegistration->registration_number ?? $newRegistrationNumber }}</strong>
                        </p>

                        <div class="p-3 bg-light rounded-3 border border-simpel text-start mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fs-8">Status Verifikasi Berkas:</span>
                                <span class="badge {{ $existingRegistration?->status?->badgeClass() ?? 'bg-warning text-dark' }} fs-7 px-3 py-1">
                                    {{ $existingRegistration?->status?->label() ?? 'Menunggu Verifikasi' }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted fs-8">Waktu Pendaftaran:</span>
                                <span class="fw-semibold text-dark fs-8">{{ $existingRegistration?->enrolled_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }} WIB</span>
                            </div>
                            @if ($existingRegistration?->notes)
                                <div class="mt-2 pt-2 border-top border-simpel">
                                    <span class="text-muted fs-8 d-block mb-1">Catatan Tim Verifikator:</span>
                                    <p class="text-dark fs-8 mb-0">{{ $existingRegistration->notes }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="alert alert-info border-0 rounded-3 text-start fs-7 mb-4">
                            <i class="ri-information-line me-1 fs-6 align-middle"></i>
                            Tim BKPSDM akan memeriksa berkas surat rekomendasi Anda. Pembaruan status diklat dapat dipantau berkala pada akun Anda.
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('landing') }}#pelatihan" class="btn btn-simple-navy px-4 py-2 rounded-pill fw-semibold">
                                <i class="ri-home-line me-1"></i> Kembali ke Katalog
                            </a>
                        </div>
                    </div>
                @else
                    {{-- Formulir Pendaftaran Baru --}}
                    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-lg-5">
                        <div class="mb-4">
                            <h4 class="fw-bold text-navy mb-1">Formulir Pendaftaran Pelatihan ASN</h4>
                            <p class="text-muted fs-7 mb-0">Lengkapi data kepegawaian dan lampirkan surat usulan resmi atasan untuk mengikuti pelatihan ini.</p>
                        </div>

                        @if ($errors->has('general'))
                            <div class="alert alert-danger rounded-3 fs-7 mb-4">
                                {{ $errors->first('general') }}
                            </div>
                        @endif

                        <form wire:submit="submit">
                            {{-- Section 1: Profil Kepegawaian --}}
                            <div class="mb-4">
                                <h6 class="fw-bold text-navy mb-3 d-flex align-items-center gap-2">
                                    <span class="badge rounded-circle bg-navy text-white fs-8" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                    Identitas & Kepegawaian ASN
                                </h6>

                                <div class="row g-3">
                                    {{-- NIP --}}
                                    <div class="col-md-6">
                                        <label class="form-label fs-7 fw-semibold text-dark">Nomor Induk Pegawai (NIP 18 Digit) <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="form.nip" class="form-control @error('form.nip') is-invalid @enderror" placeholder="Contoh: 198501012010011001" maxlength="18">
                                        @error('form.nip')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Nama Lengkap --}}
                                    <div class="col-md-6">
                                        <label class="form-label fs-7 fw-semibold text-dark">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="form.name" class="form-control @error('form.name') is-invalid @enderror" placeholder="Nama lengkap beserta gelar">
                                        @error('form.name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Asal OPD / Instansi --}}
                                    <div class="col-md-6">
                                        <label class="form-label fs-7 fw-semibold text-dark">Instansi / Asal OPD <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="form.opd_agency" class="form-control @error('form.opd_agency') is-invalid @enderror" placeholder="Contoh: BKPSDM Kab. Aceh Timur">
                                        @error('form.opd_agency')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Jabatan --}}
                                    <div class="col-md-6">
                                        <label class="form-label fs-7 fw-semibold text-dark">Jabatan Saat Ini <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="form.position" class="form-control @error('form.position') is-invalid @enderror" placeholder="Contoh: Pranata Komputer Ahli Muda">
                                        @error('form.position')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Pangkat / Golongan --}}
                                    <div class="col-md-4">
                                        <label class="form-label fs-7 fw-semibold text-dark">Pangkat / Golongan <span class="text-danger">*</span></label>
                                        <select wire:model="form.rank_class" class="form-select @error('form.rank_class') is-invalid @enderror">
                                            <option value="">Pilih Golongan...</option>
                                            <option value="Juru Muda - I/a">Juru Muda - I/a</option>
                                            <option value="Juru Muda Tk. I - I/b">Juru Muda Tk. I - I/b</option>
                                            <option value="Juru - I/c">Juru - I/c</option>
                                            <option value="Juru Tk. I - I/d">Juru Tk. I - I/d</option>
                                            <option value="Pengatur Muda - II/a">Pengatur Muda - II/a</option>
                                            <option value="Pengatur Muda Tk. I - II/b">Pengatur Muda Tk. I - II/b</option>
                                            <option value="Pengatur - II/c">Pengatur - II/c</option>
                                            <option value="Pengatur Tk. I - II/d">Pengatur Tk. I - II/d</option>
                                            <option value="Penata Muda - III/a">Penata Muda - III/a</option>
                                            <option value="Penata Muda Tk. I - III/b">Penata Muda Tk. I - III/b</option>
                                            <option value="Penata - III/c">Penata - III/c</option>
                                            <option value="Penata Tk. I - III/d">Penata Tk. I - III/d</option>
                                            <option value="Pembina - IV/a">Pembina - IV/a</option>
                                            <option value="Pembina Tk. I - IV/b">Pembina Tk. I - IV/b</option>
                                            <option value="Pembina Utama Muda - IV/c">Pembina Utama Muda - IV/c</option>
                                            <option value="Pembina Utama Madya - IV/d">Pembina Utama Madya - IV/d</option>
                                            <option value="Pembina Utama - IV/e">Pembina Utama - IV/e</option>
                                            <option value="PPPK">PPPK</option>
                                        </select>
                                        @error('form.rank_class')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- WhatsApp --}}
                                    <div class="col-md-4">
                                        <label class="form-label fs-7 fw-semibold text-dark">No. WhatsApp Aktif <span class="text-danger">*</span></label>
                                        <input type="tel" wire:model="form.phone_number" class="form-control @error('form.phone_number') is-invalid @enderror" placeholder="Contoh: 081234567890">
                                        @error('form.phone_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Email --}}
                                    <div class="col-md-4">
                                        <label class="form-label fs-7 fw-semibold text-dark">Alamat Email <span class="text-danger">*</span></label>
                                        <input type="email" wire:model="form.email" class="form-control @error('form.email') is-invalid @enderror" placeholder="email@instansi.go.id">
                                        @error('form.email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4 border-simpel">

                            {{-- Section 2: Unggah Surat Rekomendasi --}}
                            <div class="mb-4">
                                <h6 class="fw-bold text-navy mb-2 d-flex align-items-center gap-2">
                                    <span class="badge rounded-circle bg-navy text-white fs-8" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                    Dokumen Usulan / Surat Rekomendasi Atasan
                                </h6>
                                <p class="text-muted fs-8 mb-3">Wajib mengunggah surat tugas / usulan yang ditandatangani oleh atasan langsung / kepala OPD dalam format <strong>PDF (Maks. 10 MB)</strong>.</p>

                                <div class="p-3 bg-light rounded-3 border border-simpel">
                                    <input type="file" wire:model="recommendationLetter" class="form-control @error('recommendationLetter') is-invalid @enderror" accept="application/pdf">
                                    
                                    {{-- Upload Loading State --}}
                                    <div wire:loading wire:target="recommendationLetter" class="mt-2 text-primary fs-8">
                                        <div class="spinner-border spinner-border-sm me-1"></div> Mengunggah berkas dokumen...
                                    </div>

                                    @error('recommendationLetter')
                                        <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                    @enderror

                                    @if ($recommendationLetter)
                                        <div class="mt-2 d-flex align-items-center gap-2 text-success fs-8">
                                            <i class="ri-checkbox-circle-fill"></i> Berkas siap dikirim: <strong>{{ $recommendationLetter->getClientOriginalName() }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <hr class="my-4 border-simpel">

                            {{-- Section 3: Pakta Integritas / Ketentuan --}}
                            <div class="mb-4">
                                <div class="form-check p-3 bg-light rounded-3 border border-simpel">
                                    <input class="form-check-input ms-0 me-2 @error('form.agreement') is-invalid @enderror" type="checkbox" wire:model="form.agreement" id="checkAgreement">
                                    <label class="form-check-label fs-7 text-dark fw-medium" for="checkAgreement">
                                        Saya menyatakan bersedia mengikuti seluruh tata tertib, jadwal sesi, materi pembelajaran berurutan, absensi, serta evaluasi kuis yang diselenggarakan dalam program diklat ini.
                                    </label>
                                    @error('form.agreement')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="d-flex align-items-center justify-content-between pt-2">
                                <a href="{{ route('landing') }}#pelatihan" class="btn btn-light px-4 py-2 rounded-pill fw-semibold fs-7">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-simple-gold px-5 py-2 rounded-pill fw-bold fs-7" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="submit">
                                        Kirim Pendaftaran <i class="ri-arrow-right-line ms-1"></i>
                                    </span>
                                    <span wire:loading wire:target="submit">
                                        <span class="spinner-border spinner-border-sm me-1"></span> Memproses...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

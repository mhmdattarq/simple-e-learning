<div>
    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Profil Administrator</h5>
            <p class="text-muted mb-0">Kelola identitas, alamat email, kontak komunikasi, dan keamanan akun Anda.</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-danger d-flex align-items-center" wire:navigate>
                <i class="ri-arrow-left-line"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 radius-12 p-16 mb-24 border-0 shadow-sm"
            role="alert">
            <i class="ri-checkbox-circle-fill fs-5 text-success"></i>
            <div class="flex-grow-1 text-success fw-medium">
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Kolom Kiri: Kartu Identitas Akun --}}
        <div class="col-lg-4 col-12">
            <div class="card simpel-card border-0 shadow-sm radius-16 overflow-hidden">
                <div class="p-24 text-center text-white"
                    style="background: linear-gradient(135deg, #071a33 0%, #0c3158 100%);">
                    <div class="seal mx-auto mb-3"
                        style="width: 72px; height: 72px; font-size: 26px; border-radius: 20px;">
                        {{ strtoupper(substr($user->name ?: 'AD', 0, 2)) }}
                    </div>
                    <h5 class="fw-bold text-white mb-1">{{ $user->name }}</h5>
                    <p class="text-white-50 fs-8 mb-2">{{ $user->email }}</p>
                    <span class="badge"
                        style="background-color: #f3bc42; color: #071a33; font-weight: 700; font-size: 11px;">
                        {{ $user->role?->label() ?? 'Admin Diklat' }}
                    </span>
                </div>

                <div class="p-20 bg-white">
                    <div class="vstack gap-3 fs-7">
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted d-flex align-items-center gap-1">
                                <i class="ri-id-card-line text-primary"></i> NIP
                            </span>
                            <strong class="text-dark font-monospace">{{ $user->nip ?: '(Tidak ada)' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted d-flex align-items-center gap-1">
                                <i class="ri-phone-line text-primary"></i> No. HP / WA
                            </span>
                            <strong
                                class="text-dark font-monospace">{{ $user->phone_number ?: '(Belum diatur)' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom">
                            <span class="text-muted d-flex align-items-center gap-1">
                                <i class="ri-shield-check-line text-success"></i> Status Akun
                            </span>
                            <span
                                class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Aktif</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted d-flex align-items-center gap-1">
                                <i class="ri-calendar-line text-primary"></i> Bergabung
                            </span>
                            <span
                                class="text-dark">{{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Ubah Profil & Password --}}
        <div class="col-lg-8 col-12">
            <div class="card simpel-card border-0 shadow-sm radius-16 p-24 bg-white">
                <div class="d-flex align-items-center gap-2 mb-20 pb-16 border-bottom">
                    <i class="ri-user-settings-line text-primary fs-5"></i>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Pengaturan Profil &amp; Keamanan Akun</h6>
                </div>

                <form wire:submit.prevent="save">
                    {{-- Bagian 1: Data Identitas --}}
                    <div class="mb-24">
                        <span class="text-xs fw-bold text-uppercase text-secondary-light d-block mb-16">
                            1. Informasi Identitas &amp; Kontak
                        </span>

                        <div class="row g-3">
                            <div class="col-md-6 col-12">
                                <label for="name" class="form-label text-dark fw-medium fs-7 mb-1">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <input type="text" id="name" wire:model="form.name"
                                        class="form-control radius-8 ps-5 @error('form.name') is-invalid @enderror"
                                        placeholder="Masukkan nama lengkap Anda">
                                    <i
                                        class="ri-user-line position-absolute top-50 translate-middle-y start-0 ms-3 text-muted"></i>
                                </div>
                                @error('form.name')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-12">
                                <label for="email" class="form-label text-dark fw-medium fs-7 mb-1">
                                    Alamat Email Kedinasan <span class="text-danger">*</span>
                                </label>
                                <div class="position-relative">
                                    <input type="email" id="email" wire:model="form.email"
                                        class="form-control radius-8 ps-5 @error('form.email') is-invalid @enderror"
                                        placeholder="nama@acehtimurkab.go.id">
                                    <i
                                        class="ri-mail-line position-absolute top-50 translate-middle-y start-0 ms-3 text-muted"></i>
                                </div>
                                @error('form.email')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-12">
                                <label for="phone_number" class="form-label text-dark fw-medium fs-7 mb-1">
                                    Nomor WhatsApp / HP
                                </label>
                                <div class="position-relative">
                                    <input type="text" id="phone_number" wire:model="form.phone_number"
                                        class="form-control radius-8 ps-5 font-monospace @error('form.phone_number') is-invalid @enderror"
                                        placeholder="Contoh: 081234567890">
                                    <i
                                        class="ri-phone-line position-absolute top-50 translate-middle-y start-0 ms-3 text-muted"></i>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">Digunakan untuk keperluan kontak
                                    kedinasan &amp; verifikasi.</small>
                                @error('form.phone_number')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-12">
                                <label class="form-label text-dark fw-medium fs-7 mb-1">
                                    Nomor Induk Pegawai (NIP)
                                </label>
                                <div class="position-relative">
                                    <input type="text" value="{{ $user->nip ?: 'Tidak tercatat' }}"
                                        class="form-control radius-8 ps-5 bg-neutral-100" readonly disabled>
                                    <i
                                        class="ri-id-card-line position-absolute top-50 translate-middle-y start-0 ms-3 text-muted"></i>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">NIP terdaftar secara permanen pada
                                    sistem kepegawaian.</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-24">

                    {{-- Bagian 2: Ubah Kata Sandi --}}
                    <div class="mb-24">
                        <span class="text-xs fw-bold text-uppercase text-secondary-light d-block mb-12">
                            2. Keamanan Akun &amp; Kata Sandi
                        </span>

                        <div
                            class="alert alert-info py-12 px-16 radius-8 text-xs mb-16 d-flex align-items-center gap-2 border-0 bg-info-subtle text-info-800">
                            <i class="ri-information-line fs-5 text-info flex-shrink-0"></i>
                            <span>Kosongkan kolom kata sandi di bawah jika Anda tidak ingin mengubah kata sandi akun
                                saat ini.</span>
                        </div>

                        <div class="row g-3" x-data="{ showPass: false, showConfirm: false }">
                            <div class="col-md-6 col-12">
                                <label for="password" class="form-label text-dark fw-medium fs-7 mb-1">Kata Sandi
                                    Baru</label>
                                <div class="position-relative">
                                    <input :type="showPass ? 'text' : 'password'" id="password"
                                        wire:model="form.password"
                                        class="form-control radius-8 ps-5 pe-5 @error('form.password') is-invalid @enderror"
                                        placeholder="Minimal 8 karakter">
                                    <i
                                        class="ri-lock-2-line position-absolute top-50 translate-middle-y start-0 ms-3 text-muted"></i>
                                    <button type="button" @click="showPass = !showPass"
                                        class="btn btn-link position-absolute top-50 translate-middle-y end-0 me-2 text-muted p-0 text-decoration-none">
                                        <i :class="showPass ? 'ri-eye-off-line' : 'ri-eye-line'"></i>
                                    </button>
                                </div>
                                @error('form.password')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-12">
                                <label for="password_confirmation"
                                    class="form-label text-dark fw-medium fs-7 mb-1">Konfirmasi Kata Sandi Baru</label>
                                <div class="position-relative">
                                    <input :type="showConfirm ? 'text' : 'password'" id="password_confirmation"
                                        wire:model="form.password_confirmation"
                                        class="form-control radius-8 ps-5 pe-5"
                                        placeholder="Ketik ulang kata sandi baru">
                                    <i
                                        class="ri-lock-check-line position-absolute top-50 translate-middle-y start-0 ms-3 text-muted"></i>
                                    <button type="button" @click="showConfirm = !showConfirm"
                                        class="btn btn-link position-absolute top-50 translate-middle-y end-0 me-2 text-muted p-0 text-decoration-none">
                                        <i :class="showConfirm ? 'ri-eye-off-line' : 'ri-eye-line'"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-end gap-2 pt-16 border-top">
                        <button type="submit" class="btn btn-simple-gold d-inline-flex align-items-center w-100"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">
                                <i class="ri-save-line"></i> Simpan Perubahan Profil
                            </span>
                            <span wire:loading wire:target="save">
                                <span class="spinner-border spinner-border-sm me-1" role="status"
                                    aria-hidden="true"></span> Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

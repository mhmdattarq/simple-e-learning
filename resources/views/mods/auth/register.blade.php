<div class="auth-page-wrapper">
    {{-- Left Showcase Pane (Subtle & Elegant) --}}
    <div class="auth-hero-pane d-lg-flex d-none">
        <div class="auth-hero-inner">
            <h1 class="auth-hero-title">
                Pendaftaran Akun Peserta Pembelajaran
            </h1>

            <p class="auth-hero-subtitle">
                Bergabunglah dengan platform SIMPEL BKPSDM Kabupaten Aceh Timur untuk mengakses pembelajaran kelas mandiri,
                sertifikasi resmi, dan pengembangan kompetensi.
            </p>

            <div class="auth-preview-card">
                <div class="auth-preview-card-head">
                    <img src="{{ asset('mine/logo_aceh_timur.webp') }}" alt="Logo SIMPEL BKPSDM" style="width: 38px; height: 38px; object-fit: contain; flex-shrink: 0;">
                    <div class="auth-preview-info">
                        <strong>SIMPEL E-Learning</strong>
                        <small>BKPSDM Kabupaten Aceh Timur</small>
                    </div>
                </div>

                <div class="auth-pills-row">
                    <span class="auth-pill-item">
                        <i class="ri-user-follow-line"></i>
                        Registrasi Mandiri
                    </span>
                    <span class="auth-pill-item">
                        <i class="ri-book-open-line"></i>
                        Katalog Kelas Terbuka
                    </span>
                    <span class="auth-pill-item">
                        <i class="ri-shield-keyhole-line"></i>
                        Akses Akun Aman
                    </span>
                </div>
            </div>
        </div>

        <div class="auth-hero-footer">
            <p>Aksi Perubahan Kinerja 2026 · BKPSDM Aceh Timur</p>
        </div>
    </div>

    {{-- Right Authentication Form Pane --}}
    <div class="auth-form-pane">
        <div class="auth-form-container" style="max-width: 520px;">
            {{-- Top Brand & Back to Home --}}
            <div class="d-flex align-items-center justify-content-between mb-24">
                <a href="{{ route('landing') }}" class="brand text-decoration-none">
                    <img src="{{ asset('mine/logo_aceh_timur.webp') }}" alt="Logo SIMPEL BKPSDM" style="width: 44px; height: 44px; object-fit: contain; flex-shrink: 0;">
                    <div>
                        <strong>SIMPEL</strong>
                        <small>BKPSDM Aceh Timur</small>
                    </div>
                </a>
                <a href="{{ route('landing') }}" class="auth-back-btn">
                    <i class="ri-arrow-left-line"></i>
                    <span>Beranda</span>
                </a>
            </div>

            {{-- Title & Subtitle --}}
            <div class="mb-20">
                <h4 class="auth-clean-title">Pendaftaran Peserta / Siswa</h4>
                <p class="auth-clean-desc mb-0">Lengkapi formulir di bawah ini untuk mendaftarkan akun peserta
                    pembelajaran.</p>
            </div>

            @if ($errors->has('form.email') && str_contains($errors->first('form.email'), 'kesalahan sistem'))
                <div
                    class="alert alert-danger py-8 px-12 radius-8 text-xs mb-16 d-flex align-items-center gap-2 border-0 bg-danger-50 text-danger-600">
                    <i class="ri-error-warning-fill text-base flex-shrink-0"></i>
                    <span>{{ $errors->first('form.email') }}</span>
                </div>
            @endif

            {{-- Registration Form --}}
            <form wire:submit="register">
                <div class="row g-3 mb-20">
                    {{-- Nama Lengkap --}}
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold text-secondary-dark mb-4" for="reg-name">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <div class="auth-input-wrapper">
                            <span class="auth-field-icon">
                                <i class="ri-user-line"></i>
                            </span>
                            <input type="text" id="reg-name" wire:model="form.name"
                                class="form-control auth-input @error('form.name') is-invalid @enderror"
                                placeholder="Nama lengkap Anda">
                        </div>
                        @error('form.name')
                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- NIP (Opsional / ASN) --}}
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold text-secondary-dark mb-4" for="reg-nip">
                            NIP <span class="text-muted fw-normal fs-8">(Opsional / ASN)</span>
                        </label>
                        <div class="auth-input-wrapper">
                            <span class="auth-field-icon">
                                <i class="ri-id-card-line"></i>
                            </span>
                            <input type="text" id="reg-nip" wire:model="form.nip" maxlength="18"
                                class="form-control auth-input @error('form.nip') is-invalid @enderror"
                                placeholder="18 digit NIP (opsional)">
                        </div>
                        @error('form.nip')
                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Alamat Email --}}
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold text-secondary-dark mb-4" for="reg-email">
                            Alamat Email <span class="text-danger">*</span>
                        </label>
                        <div class="auth-input-wrapper">
                            <span class="auth-field-icon">
                                <i class="ri-mail-line"></i>
                            </span>
                            <input type="email" id="reg-email" wire:model="form.email"
                                class="form-control auth-input @error('form.email') is-invalid @enderror"
                                placeholder="nama@email.com" autocomplete="email">
                        </div>
                        @error('form.email')
                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nomor Telepon / WhatsApp --}}
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold text-secondary-dark mb-4" for="reg-phone">
                            No. Handphone / WhatsApp <span class="text-danger">*</span>
                        </label>
                        <div class="auth-input-wrapper">
                            <span class="auth-field-icon">
                                <i class="ri-whatsapp-line"></i>
                            </span>
                            <input type="tel" id="reg-phone" wire:model="form.phone_number"
                                class="form-control auth-input @error('form.phone_number') is-invalid @enderror"
                                placeholder="08xxxxxxxxxx" inputmode="tel">
                        </div>
                        @error('form.phone_number')
                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Alamat Lengkap / Domisili --}}
                    <div class="col-12">
                        <label class="form-label text-xs fw-semibold text-secondary-dark mb-4" for="reg-address">
                            Alamat Lengkap <span class="text-danger">*</span>
                        </label>
                        <div class="auth-input-wrapper position-relative">
                            <span class="auth-field-icon icon-top" style="position: absolute; left: 14px; top: 12px; transform: none; z-index: 5; pointer-events: none;">
                                <i class="ri-map-pin-line text-lg"></i>
                            </span>
                            <textarea id="reg-address" wire:model="form.address" rows="3"
                                class="form-control auth-input @error('form.address') is-invalid @enderror"
                                style="height: auto; min-height: 84px; padding-left: 44px !important; padding-top: 10px !important; padding-bottom: 10px !important; line-height: 1.5; resize: vertical;"
                                placeholder="Alamat domisili lengkap"></textarea>
                        </div>
                        @error('form.address')
                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kata Sandi --}}
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold text-secondary-dark mb-4" for="reg-password">
                            Kata Sandi <span class="text-danger">*</span>
                        </label>
                        <div class="auth-input-wrapper">
                            <span class="auth-field-icon">
                                <i class="ri-lock-line"></i>
                            </span>
                            <input type="password" id="reg-password" wire:model.defer="form.password"
                                class="form-control auth-input has-toggle @error('form.password') is-invalid @enderror"
                                placeholder="Minimal 6 karakter" autocomplete="new-password">
                            <span class="toggle-password ri-eye-line auth-toggle-icon" data-toggle="#reg-password"
                                title="Tampilkan/Sembunyikan sandi"></span>
                        </div>
                        @error('form.password')
                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Konfirmasi Kata Sandi --}}
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold text-secondary-dark mb-4"
                            for="reg-password-confirm">
                            Konfirmasi Sandi <span class="text-danger">*</span>
                        </label>
                        <div class="auth-input-wrapper">
                            <span class="auth-field-icon">
                                <i class="ri-lock-line"></i>
                            </span>
                            <input type="password" id="reg-password-confirm"
                                wire:model.defer="form.password_confirmation"
                                class="form-control auth-input has-toggle @error('form.password') is-invalid @enderror"
                                placeholder="Ulangi kata sandi" autocomplete="new-password">
                            <span class="toggle-password ri-eye-line auth-toggle-icon"
                                data-toggle="#reg-password-confirm" title="Tampilkan/Sembunyikan sandi"></span>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn-auth-primary w-100" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <i class="ri-user-add-line me-1"></i> Daftar Akun Peserta
                    </span>
                    <span wire:loading style="display: none;">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Mendaftarkan akun...
                    </span>
                </button>

                <div class="mt-32 center-border-horizontal text-center">
                    <span class="bg-base z-1 px-4">atau</span>
                </div>
                <div class="mt-32 d-flex align-items-center gap-3">
                    <a href="{{ $redirectTo ? route('auth.google.redirect', ['redirect' => $redirectTo]) : route('auth.google.redirect') }}" class="btn btn-secondary w-100 d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="ri-google-line"></i>
                        Buat Akun Dengan Google
                    </a>
                </div>

                {{-- Footer Info --}}
                <div class="text-center pt-16 mt-16 border-top">
                    <p class="text-xs text-muted mb-0">
                        Sudah memiliki akun?
                        <a href="{{ $redirectTo ? route('login', ['redirect' => $redirectTo]) : route('login') }}"
                            class="fw-semibold text-dark text-decoration-none hover-underline" wire:navigate>
                            Masuk ke Portal
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

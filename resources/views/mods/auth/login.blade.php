<div class="auth-page-wrapper">
    {{-- Left Showcase Pane (Subtle & Elegant) --}}
    <div class="auth-hero-pane d-lg-flex d-none">
        <div class="auth-hero-inner">
            <h1 class="auth-hero-title">
                Portal Pelatihan Mandiri Aparatur Sipil Negara
            </h1>

            <p class="auth-hero-subtitle">
                Tingkatkan kompetensi digital dan profesionalitas aparatur secara efektif, efisien, transparan, dan
                akuntabel.
            </p>

            <div class="auth-preview-card">
                <div class="auth-preview-card-head">
                    <div class="auth-preview-seal">S</div>
                    <div class="auth-preview-info">
                        <strong>SIMPEL E-Learning</strong>
                        <small>BKPSDM Kabupaten Aceh Timur</small>
                    </div>
                </div>

                <div class="auth-pills-row">
                    <span class="auth-pill-item">
                        <i class="ri-computer-line"></i>
                        Akses Mandiri 24/7
                    </span>
                    <span class="auth-pill-item">
                        <i class="ri-award-line"></i>
                        E-Sertifikat Resmi
                    </span>
                    <span class="auth-pill-item">
                        <i class="ri-shield-check-line"></i>
                        Integrasi SIASN BKN
                    </span>
                </div>
            </div>
        </div>

        <div class="auth-hero-footer">
            <p>Aksi Perubahan Kinerja 2026 · BKPSDM Aceh Timur</p>
        </div>
    </div>

    {{-- Right Authentication Form Pane (Clean & Airy) --}}
    <div class="auth-form-pane">
        <div class="auth-form-container">
            {{-- Top Brand & Back to Home --}}
            <div class="d-flex align-items-center justify-content-between mb-28">
                <a href="{{ route('landing') }}" class="brand text-decoration-none">
                    <div class="seal">S</div>
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
            <div class="mb-24">
                <h4 class="auth-clean-title">Masuk ke Akun</h4>
                {{-- <h2 class="auth-clean-title">Masuk ke Akun</h2> --}}
                <p class="auth-clean-desc">Silakan masukkan email atau NIP dan kata sandi Anda.</p>
            </div>

            @if (session()->has('success'))
                <div
                    class="alert alert-success py-8 px-12 radius-8 text-xs mb-16 d-flex align-items-center gap-2 border-0 bg-success-50 text-success-700"
                    style="background-color: #def4e9; color: #16845b;">
                    <i class="ri-checkbox-circle-fill text-base flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errorMessage || session()->has('error'))
                <div class="alert alert-danger py-2 px-3 radius-8 text-xs mb-16 d-flex align-items-center gap-2"
                    style="border-radius: 10px; background-color: #fef2f2 !important; border: 1px solid #fee2e2 !important; color: #b91c1c !important;">
                    <i class="ri-error-warning-fill fs-5 text-danger flex-shrink-0"></i>
                    <span class="fw-medium">{{ $errorMessage ?: session('error') }}</span>
                </div>
            @endif

            {{-- Login Form --}}
            <form wire:submit="authenticate">
                {{-- Email or NIP --}}
                <div class="mb-16">
                    <label class="form-label text-xs fw-semibold text-secondary-dark mb-6" for="identifier">
                        Email atau NIP
                    </label>
                    @php
                        $hasIdentifierValidationError = ($errors->has('identifier') && $errors->first('identifier') !== $errorMessage)
                            || ($errors->has('email') && $errors->first('email') !== $errorMessage);
                    @endphp
                    <div class="auth-input-wrapper">
                        <span class="auth-field-icon">
                            <i class="ri-mail-line"></i>
                        </span>
                        <input type="text" id="identifier" wire:model="identifier"
                            class="form-control auth-input @if ($hasIdentifierValidationError) is-invalid @endif"
                            placeholder="nama@email.com atau NIP" autocomplete="username">
                    </div>
                    @if ($hasIdentifierValidationError)
                        <div class="invalid-feedback d-block text-xs mt-1">
                            {{ $errors->first('identifier') ?: $errors->first('email') }}
                        </div>
                    @endif
                </div>

                {{-- Password --}}
                <div class="mb-16">
                    <div class="d-flex justify-content-between align-items-center mb-6">
                        <label class="form-label text-xs fw-semibold text-secondary-dark mb-0" for="your-password">
                            Kata Sandi
                        </label>
                        <a href="javascript:void(0)"
                            class="text-xs text-decoration-none fw-medium text-muted hover-underline">Lupa Sandi?</a>
                    </div>
                    <div class="auth-input-wrapper">
                        <span class="auth-field-icon">
                            <i class="ri-lock-line"></i>
                        </span>
                        <input type="password" id="your-password" wire:model.defer="password"
                            class="form-control auth-input has-toggle @error('password') is-invalid @enderror"
                            placeholder="Masukkan kata sandi" autocomplete="current-password">
                        <span class="toggle-password ri-eye-line auth-toggle-icon" data-toggle="#your-password"
                            title="Tampilkan/Sembunyikan sandi"></span>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="d-flex align-items-center justify-content-between mb-20">
                    <div class="form-check style-check d-flex align-items-center">
                        <input class="form-check-input border-secondary-light" type="checkbox" id="remember"
                            wire:model.defer="remember">
                        <label class="form-check-label text-xs text-secondary ms-2" for="remember">
                            Ingat saya di perangkat ini
                        </label>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn-auth-primary w-100" wire:loading.attr="disabled">
                    <span wire:loading.remove>Masuk ke Portal</span>
                    <span wire:loading style="display: none;">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Memproses...
                    </span>
                </button>

                {{-- Footer Info --}}
                <div class="text-center pt-16 border-top">
                    <p class="text-xs text-muted mb-0">
                        Belum memiliki akun?
                        <a href="{{ route('register') }}"
                            class="fw-semibold text-dark text-decoration-none hover-underline" wire:navigate>
                            Daftar Sekarang
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

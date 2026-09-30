<div class="auth-page-wrapper">
    {{-- Left Showcase Pane (Subtle & Elegant) --}}
    <div class="auth-hero-pane d-lg-flex d-none">
        <div class="auth-hero-inner">
            <h1 class="auth-hero-title">
                Pemulihan Akses Akun ASN
            </h1>

            <p class="auth-hero-subtitle">
                Amankan kembali akses akun pelatihan Anda secara mandiri melalui verifikasi email resmi yang terenkripsi.
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
                        <i class="ri-lock-password-line"></i>
                        Atur Ulang Sandi
                    </span>
                    <span class="auth-pill-item">
                        <i class="ri-shield-keyhole-line"></i>
                        Tautan Aman 60 Menit
                    </span>
                    <span class="auth-pill-item">
                        <i class="ri-mail-check-line"></i>
                        Verifikasi Email
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
                    <img src="{{ asset('mine/logo_aceh_timur.webp') }}" alt="Logo SIMPEL BKPSDM" style="width: 44px; height: 44px; object-fit: contain; flex-shrink: 0;">
                    <div>
                        <strong>SIMPEL</strong>
                        <small>BKPSDM Aceh Timur</small>
                    </div>
                </a>
                <a href="{{ route('login') }}" class="auth-back-btn" wire:navigate>
                    <i class="ri-arrow-left-line"></i>
                    <span>Masuk</span>
                </a>
            </div>

            {{-- Title & Subtitle --}}
            <div class="mb-24">
                <h4 class="auth-clean-title">Lupa Kata Sandi?</h4>
                <p class="auth-clean-desc">
                    Masukkan alamat email yang terdaftar pada akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
                </p>
            </div>

            {{-- Success State Notification --}}
            @if ($linkSent)
                <div class="alert alert-success py-12 px-14 radius-10 text-xs mb-20 d-flex align-items-start gap-2 border-0"
                    style="background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0 !important;">
                    <i class="ri-mail-send-fill fs-5 flex-shrink-0 text-success" style="line-height: 1;"></i>
                    <div>
                        <div class="fw-bold mb-1">Permintaan Terkirim!</div>
                        <span>{{ $statusMessage }}</span>
                    </div>
                </div>
            @endif

            {{-- Forgot Password Form --}}
            <form wire:submit="sendResetLink">
                {{-- Email Input --}}
                <div class="mb-20">
                    <label class="form-label text-xs fw-semibold text-secondary-dark mb-6" for="email">
                        Alamat Email Terdaftar
                    </label>
                    <div class="auth-input-wrapper">
                        <span class="auth-field-icon">
                            <i class="ri-mail-line"></i>
                        </span>
                        <input type="email" id="email" wire:model="email"
                            class="form-control auth-input @error('email') is-invalid @enderror"
                            placeholder="nama@email.com" autocomplete="email" required autofocus>
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <button type="submit" class="btn-auth-primary w-100" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <i class="ri-send-plane-fill me-1"></i> Kirim Tautan Pemulihan
                    </span>
                    <span wire:loading style="display: none;">
                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                        Mengirimkan...
                    </span>
                </button>

                {{-- Footer Info --}}
                <div class="text-center pt-24">
                    <p class="text-xs text-muted mb-0">
                        Sudah ingat kata sandi Anda?
                        <a href="{{ route('login') }}"
                            class="fw-semibold text-dark text-decoration-none hover-underline" wire:navigate>
                            Kembali ke Halaman Masuk
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

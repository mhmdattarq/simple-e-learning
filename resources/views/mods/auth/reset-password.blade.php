<div class="auth-page-wrapper">
    {{-- Left Showcase Pane (Subtle & Elegant) --}}
    <div class="auth-hero-pane d-lg-flex d-none">
        <div class="auth-hero-inner">
            <h1 class="auth-hero-title">
                Pembaruan Kata Sandi Aman
            </h1>

            <p class="auth-hero-subtitle">
                Pastikan Anda menggunakan kombinasi kata sandi yang kuat dan unik demi menjaga keamanan data kepegawaian dan sertifikat pembelajaran Anda.
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
                        <i class="ri-shield-keyhole-line"></i>
                        Enkripsi Kriptografi
                    </span>
                    <span class="auth-pill-item">
                        <i class="ri-device-line"></i>
                        Putus Sesi Perangkat Lain
                    </span>
                    <span class="auth-pill-item">
                        <i class="ri-check-double-line"></i>
                        Sekali Pakai (Single-Use)
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
            {{-- Top Brand & Back to Login --}}
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

            @if (!$tokenValid)
                {{-- Invalid or Expired Token State --}}
                <div class="text-center py-20">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-16"
                        style="width: 68px; height: 68px; background-color: #fef2f2; color: #dc2626; border: 2px solid #fecaca;">
                        <i class="ri-error-warning-fill" style="font-size: 36px;"></i>
                    </div>

                    <h4 class="auth-clean-title mb-8">Tautan Tidak Valid</h4>
                    <p class="text-secondary text-sm mb-24 px-12">
                        {{ $invalidReason ?: 'Tautan pemulihan kata sandi tidak valid atau telah kedaluwarsa (maksimal 60 menit).' }}
                    </p>

                    <div class="d-grid gap-2">
                        <a href="{{ route('password.request') }}" class="btn-auth-primary text-decoration-none py-12" wire:navigate>
                            <i class="ri-mail-send-line me-1"></i> Minta Tautan Baru
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-light py-10 text-xs fw-semibold radius-8" wire:navigate>
                            Kembali ke Halaman Masuk
                        </a>
                    </div>
                </div>
            @else
                {{-- Valid Token - Reset Form --}}
                <div class="mb-24">
                    <h4 class="auth-clean-title">Buat Kata Sandi Baru</h4>
                    <p class="auth-clean-desc">
                        Masukkan kata sandi baru untuk akun <strong>{{ $email }}</strong>.
                    </p>
                </div>

                @if ($errorMessage)
                    <div class="alert alert-danger py-10 px-12 radius-8 text-xs mb-16 d-flex align-items-center gap-2"
                        style="border-radius: 10px; background-color: #fef2f2 !important; border: 1px solid #fee2e2 !important; color: #b91c1c !important;">
                        <i class="ri-error-warning-fill fs-5 text-danger flex-shrink-0"></i>
                        <span class="fw-medium">{{ $errorMessage }}</span>
                    </div>
                @endif

                <form wire:submit="resetPassword">
                    {{-- Hidden/Readonly Email --}}
                    <input type="hidden" wire:model="email">

                    {{-- Password Baru --}}
                    <div class="mb-16">
                        <label class="form-label text-xs fw-semibold text-secondary-dark mb-6" for="password">
                            Kata Sandi Baru
                        </label>
                        <div x-data="{ show: false }" class="auth-input-wrapper">
                            <span class="auth-field-icon">
                                <i class="ri-lock-line"></i>
                            </span>
                            <input :type="show ? 'text' : 'password'" id="password" wire:model.defer="password"
                                class="form-control auth-input has-toggle @error('password') is-invalid @enderror"
                                placeholder="Minimal 8 karakter" autocomplete="new-password" required>
                            <span @click="show = !show" :class="show ? 'ri-eye-off-line' : 'ri-eye-line'"
                                class="auth-toggle-icon" style="cursor: pointer;" title="Tampilkan/Sembunyikan sandi"></span>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div class="mb-24">
                        <label class="form-label text-xs fw-semibold text-secondary-dark mb-6" for="password_confirmation">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <div x-data="{ show: false }" class="auth-input-wrapper">
                            <span class="auth-field-icon">
                                <i class="ri-lock-check-line"></i>
                            </span>
                            <input :type="show ? 'text' : 'password'" id="password_confirmation" wire:model.defer="password_confirmation"
                                class="form-control auth-input has-toggle @error('password_confirmation') is-invalid @enderror"
                                placeholder="Ulangi kata sandi baru" autocomplete="new-password" required>
                            <span @click="show = !show" :class="show ? 'ri-eye-off-line' : 'ri-eye-line'"
                                class="auth-toggle-icon" style="cursor: pointer;" title="Tampilkan/Sembunyikan sandi"></span>
                        </div>
                        @error('password_confirmation')
                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn-auth-primary w-100" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="ri-check-line me-1"></i> Simpan Kata Sandi Baru
                        </span>
                        <span wire:loading style="display: none;">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Menyimpan...
                        </span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

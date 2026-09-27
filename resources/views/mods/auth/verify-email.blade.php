<div class="auth-page-wrapper">
    {{-- Left Showcase Pane (Subtle & Elegant) --}}
    <div class="auth-hero-pane d-lg-flex d-none">
        <div class="auth-hero-inner">
            <h1 class="auth-hero-title">
                Verifikasi & Keamanan Akun ASN
            </h1>

            <p class="auth-hero-subtitle">
                Platform SIMPEL memastikan setiap akun aparatur terverifikasi secara resmi demi menjamin perlindungan data pribadi dan keabsahan sertifikasi kompetensi.
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
                        <i class="ri-shield-check-line"></i>
                        Autentikasi Aman
                    </span>
                    <span class="auth-pill-item">
                        <i class="ri-lock-line"></i>
                        Token Kriptografi
                    </span>
                    <span class="auth-pill-item">
                        <i class="ri-user-star-line"></i>
                        Aparatur Terdaftar
                    </span>
                </div>
            </div>
        </div>

        <div class="auth-hero-footer">
            <p>Aksi Perubahan Kinerja 2026 · BKPSDM Aceh Timur</p>
        </div>
    </div>

    {{-- Right Pane --}}
    <div class="auth-form-pane">
        <div class="auth-form-container" style="max-width: 480px;">
            {{-- Top Brand --}}
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

            @if ($isVerified)
                {{-- Success State --}}
                <div class="text-center py-24">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-20"
                        style="width: 72px; height: 72px; background-color: #ecfdf5; color: #059669; border: 2px solid #a7f3d0;">
                        <i class="ri-checkbox-circle-fill" style="font-size: 40px;"></i>
                    </div>

                    <h4 class="auth-clean-title mb-8">Aktivasi Email Berhasil!</h4>
                    <p class="text-secondary text-sm mb-24 px-12">
                        {{ $statusMessage ?: 'Alamat email Anda telah berhasil diverifikasi. Akun Anda kini aktif dan siap digunakan.' }}
                    </p>

                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn-auth-primary text-decoration-none py-12" wire:navigate>
                            <i class="ri-login-box-line me-1"></i> Masuk ke Portal Sekarang
                        </a>
                    </div>
                </div>
            @else
                {{-- Error / Expired State --}}
                <div class="text-center py-16 mb-20">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-16"
                        style="width: 68px; height: 68px; background-color: #fef2f2; color: #dc2626; border: 2px solid #fecaca;">
                        <i class="ri-error-warning-fill" style="font-size: 36px;"></i>
                    </div>

                    <h4 class="auth-clean-title mb-6">Verifikasi Gagal</h4>
                    <p class="text-muted text-xs mb-0 px-8">
                        {{ $errorMessage ?: 'Tautan verifikasi tidak valid atau telah kedaluwarsa.' }}
                    </p>
                </div>

                {{-- Resend Card --}}
                <div class="card border-0 shadow-sm radius-12 p-20 mb-20" style="background-color: #f8fafc; border: 1px solid #e2e8f0 !important;">
                    <h6 class="text-xs fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                        <i class="ri-mail-send-line text-warning"></i>
                        Kirim Ulang Tautan Aktivasi
                    </h6>
                    <p class="text-muted text-xs mb-14">
                        Masukkan alamat email Anda untuk menerima tautan aktivasi akun yang baru.
                    </p>

                    @if ($resendMessage)
                        <div class="alert alert-success py-8 px-12 radius-8 text-xs mb-12 d-flex align-items-center gap-2 border-0 bg-success-50 text-success-700">
                            <i class="ri-checkbox-circle-fill text-base flex-shrink-0"></i>
                            <span>{{ $resendMessage }}</span>
                        </div>
                    @endif

                    <form wire:submit="resendVerification">
                        <div class="mb-12">
                            <div class="auth-input-wrapper">
                                <span class="auth-field-icon">
                                    <i class="ri-mail-line"></i>
                                </span>
                                <input type="email" wire:model="resendEmail"
                                    class="form-control auth-input @error('resendEmail') is-invalid @enderror"
                                    placeholder="nama@email.com" autocomplete="email">
                            </div>
                            @error('resendEmail')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-dark w-100 py-2 radius-8 text-xs fw-semibold" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="resendVerification">
                                <i class="ri-send-plane-2-line me-1"></i> Kirim Tautan Baru
                            </span>
                            <span wire:loading wire:target="resendVerification" style="display: none;">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                Mengirim...
                            </span>
                        </button>
                    </form>
                </div>

                <div class="text-center pt-8 border-top">
                    <a href="{{ route('login') }}" class="text-xs text-muted text-decoration-none hover-underline" wire:navigate>
                        <i class="ri-arrow-left-line me-1"></i> Kembali ke Halaman Masuk
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

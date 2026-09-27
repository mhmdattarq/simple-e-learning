<div class="auth-page-wrapper">
    {{-- Left Showcase Pane (Subtle & Elegant) --}}
    <div class="auth-hero-pane d-lg-flex d-none">
        <div class="auth-hero-inner">
            <h1 class="auth-hero-title">
                Pendaftaran Akun Peserta Pelatihan ASN
            </h1>

            <p class="auth-hero-subtitle">
                Bergabunglah dengan platform SIMPEL BKPSDM Kabupaten Aceh Timur untuk mengakses pelatihan mandiri,
                sertifikasi resmi, dan pengembangan kompetensi aparatur.
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
                        <i class="ri-user-follow-line"></i>
                        Registrasi Mandiri
                    </span>
                    <span class="auth-pill-item">
                        <i class="ri-book-open-line"></i>
                        Katalog Pelatihan Terbuka
                    </span>
                    <span class="auth-pill-item">
                        <i class="ri-shield-keyhole-line"></i>
                        Data Terverifikasi ASN
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
            <div class="mb-20">
                <h4 class="auth-clean-title">Pendaftaran Peserta / Siswa</h4>
                <p class="auth-clean-desc mb-0">Lengkapi formulir di bawah ini untuk mendaftarkan akun peserta
                    pelatihan.</p>
            </div>

            @if ($errors->has('form.nip') && str_contains($errors->first('form.nip'), 'kesalahan sistem'))
                <div
                    class="alert alert-danger py-8 px-12 radius-8 text-xs mb-16 d-flex align-items-center gap-2 border-0 bg-danger-50 text-danger-600">
                    <i class="ri-error-warning-fill text-base flex-shrink-0"></i>
                    <span>{{ $errors->first('form.nip') }}</span>
                </div>
            @endif

            {{-- Registration Form --}}
            <form wire:submit="register">
                {{-- SECTION 1: IDENTITAS KEPEGAWAIAN --}}
                <div class="mb-16">
                    <div class="d-flex align-items-center gap-2 mb-12 pb-6 border-bottom">
                        <span
                            class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                            style="width: 20px; height: 20px; font-size: 10px; background-color: #071a33; color: #f3bc42;">1</span>
                        <span class="fw-bold text-xs text-dark" style="letter-spacing: 0.3px;">Identitas Kepegawaian
                            ASN</span>
                    </div>

                    <div class="row g-3">
                        {{-- NIP (18 Digit) --}}
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-secondary-dark mb-4" for="reg-nip">
                                NIP (18 Digit) <span class="text-danger">*</span>
                            </label>
                            <div class="auth-input-wrapper">
                                <span class="auth-field-icon">
                                    <i class="ri-fingerprint-line"></i>
                                </span>
                                <input type="text" id="reg-nip" wire:model="form.nip"
                                    class="form-control auth-input @error('form.nip') is-invalid @enderror"
                                    placeholder="Contoh: 199205052018011005" maxlength="18" inputmode="numeric">
                            </div>
                            @error('form.nip')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nama Lengkap & Gelar --}}
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-secondary-dark mb-4" for="reg-name">
                                Nama Lengkap & Gelar <span class="text-danger">*</span>
                            </label>
                            <div class="auth-input-wrapper">
                                <span class="auth-field-icon">
                                    <i class="ri-user-line"></i>
                                </span>
                                <input type="text" id="reg-name" wire:model="form.name"
                                    class="form-control auth-input @error('form.name') is-invalid @enderror"
                                    placeholder="Nama lengkap beserta gelar">
                            </div>
                            @error('form.name')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Instansi / OPD Asal --}}
                        <div class="col-md-12">
                            <label class="form-label text-xs fw-semibold text-secondary-dark mb-4" for="reg-opd">
                                Instansi / OPD Asal <span class="text-danger">*</span>
                            </label>
                            <div class="auth-input-wrapper">
                                <span class="auth-field-icon">
                                    <i class="ri-building-line"></i>
                                </span>
                                <input type="text" id="reg-opd" wire:model="form.opd_agency" list="opd-suggestions"
                                    class="form-control auth-input @error('form.opd_agency') is-invalid @enderror"
                                    placeholder="Contoh: Badan Kepegawaian dan Pengembangan SDM">
                                <datalist id="opd-suggestions">
                                    <option value="Sekretariat Daerah Kabupaten Aceh Timur"></option>
                                    <option value="Badan Kepegawaian dan Pengembangan SDM"></option>
                                    <option value="Badan Perencanaan Pembangunan Daerah"></option>
                                    <option value="Badan Pengelolaan Keuangan Daerah"></option>
                                    <option value="Dinas Pendidikan dan Kebudayaan"></option>
                                    <option value="Dinas Kesehatan"></option>
                                    <option value="Dinas Komunikasi dan Informatika"></option>
                                    <option value="Inspektorat Daerah"></option>
                                    <option value="RSUD dr. Zubir Mahmud"></option>
                                </datalist>
                            </div>
                            @error('form.opd_agency')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jabatan --}}
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-secondary-dark mb-4" for="reg-position">
                                Jabatan Saat Ini <span class="text-danger">*</span>
                            </label>
                            <div class="auth-input-wrapper">
                                <span class="auth-field-icon">
                                    <i class="ri-briefcase-line"></i>
                                </span>
                                <input type="text" id="reg-position" wire:model="form.position"
                                    class="form-control auth-input @error('form.position') is-invalid @enderror"
                                    placeholder="Contoh: Analis SDM Aparatur Ahli Pertama">
                            </div>
                            @error('form.position')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Pangkat / Golongan --}}
                        <div class="col-md-6">
                            <label class="form-label text-xs fw-semibold text-secondary-dark mb-4" for="reg-rank">
                                Pangkat / Golongan <span class="text-danger">*</span>
                            </label>
                            <div class="auth-input-wrapper">
                                <span class="auth-field-icon">
                                    <i class="ri-medal-line"></i>
                                </span>
                                <select id="reg-rank" wire:model="form.rank_class"
                                    class="form-select auth-input @error('form.rank_class') is-invalid @enderror"
                                    style="height: 46px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding-left: 44px; font-size: 13.5px;">
                                    <option value="">-- Pilih Pangkat/Golongan --</option>
                                    <optgroup label="Golongan IV (Pembina)">
                                        <option value="Pembina Utama - IV/e">Pembina Utama - IV/e</option>
                                        <option value="Pembina Utama Madya - IV/d">Pembina Utama Madya - IV/d</option>
                                        <option value="Pembina Utama Muda - IV/c">Pembina Utama Muda - IV/c</option>
                                        <option value="Pembina Tingkat I - IV/b">Pembina Tingkat I - IV/b</option>
                                        <option value="Pembina - IV/a">Pembina - IV/a</option>
                                    </optgroup>
                                    <optgroup label="Golongan III (Penata)">
                                        <option value="Penata Tingkat I - III/d">Penata Tingkat I - III/d</option>
                                        <option value="Penata - III/c">Penata - III/c</option>
                                        <option value="Penata Muda Tingkat I - III/b">Penata Muda Tingkat I - III/b
                                        </option>
                                        <option value="Penata Muda - III/a">Penata Muda - III/a</option>
                                    </optgroup>
                                    <optgroup label="Golongan II (Pengatur)">
                                        <option value="Pengatur Tingkat I - II/d">Pengatur Tingkat I - II/d</option>
                                        <option value="Pengatur - II/c">Pengatur - II/c</option>
                                        <option value="Pengatur Muda Tingkat I - II/b">Pengatur Muda Tingkat I - II/b
                                        </option>
                                        <option value="Pengatur Muda - II/a">Pengatur Muda - II/a</option>
                                    </optgroup>
                                    <optgroup label="Golongan I (Juru)">
                                        <option value="Juru Tingkat I - I/d">Juru Tingkat I - I/d</option>
                                        <option value="Juru - I/c">Juru - I/c</option>
                                        <option value="Juru Muda Tingkat I - I/b">Juru Muda Tingkat I - I/b</option>
                                        <option value="Juru Muda - I/a">Juru Muda - I/a</option>
                                    </optgroup>
                                    <optgroup label="Lainnya">
                                        <option value="PPPK">Pegawai Pemerintah dengan Perjanjian Kerja (PPPK)
                                        </option>
                                        <option value="PPNPN">Pegawai Non-PNS / PPNPN</option>
                                    </optgroup>
                                </select>
                            </div>
                            @error('form.rank_class')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: KONTAK & AKUN LOGIN --}}
                <div class="mb-20">
                    <div class="d-flex align-items-center gap-2 mb-12 pb-6 border-bottom">
                        <span
                            class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                            style="width: 20px; height: 20px; font-size: 10px; background-color: #071a33; color: #f3bc42;">2</span>
                        <span class="fw-bold text-xs text-dark" style="letter-spacing: 0.3px;">Kontak & Akun
                            Login</span>
                    </div>

                    <div class="row g-3">
                        {{-- Email --}}
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
                                No. WhatsApp Aktif <span class="text-danger">*</span>
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
                    <a href="{{ route('auth.google.redirect') }}" class="btn btn-secondary w-100 d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="ri-google-line"></i>
                        Buat Akun Dengan Google
                    </a>
                </div>

                {{-- Footer Info --}}
                <div class="text-center pt-16 mt-16 border-top">
                    <p class="text-xs text-muted mb-0">
                        Sudah memiliki akun?
                        <a href="{{ route('login') }}"
                            class="fw-semibold text-dark text-decoration-none hover-underline" wire:navigate>
                            Masuk ke Portal
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

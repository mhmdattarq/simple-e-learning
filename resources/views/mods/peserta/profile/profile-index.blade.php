<div class="py-5 bg-light" style="min-height: 85vh;">
    <div class="container py-lg-4 py-2">
        {{-- Breadcrumb & Back --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('landing') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold" wire:navigate>
                <i class="ri-arrow-left-line me-1"></i> Kembali ke Beranda
            </a>
            <span class="badge {{ $isComplete ? 'bg-success text-white' : 'bg-warning text-dark' }} fw-bold px-3 py-1_5 rounded-pill fs-8">
                <i class="{{ $isComplete ? 'ri-shield-check-line' : 'ri-alert-line' }} me-1"></i>
                {{ $isComplete ? 'Profil ASN Lengkap' : 'Profil ASN Belum Lengkap' }}
            </span>
        </div>

        <div class="row g-4 justify-content-center">
            {{-- Kolom Kiri: Ringkasan Pengguna & Status Profil --}}
            <div class="col-lg-4 col-md-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 90px;">
                    <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, #071a33 0%, #0c3158 100%);">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="rounded-circle shadow mb-3 border border-2 border-white" style="width: 80px; height: 80px; object-fit: cover;">
                        @else
                            <div class="seal mx-auto mb-3" style="width: 72px; height: 72px; font-size: 26px; border-radius: 20px;">
                                {{ strtoupper(substr($user->name ?: 'P', 0, 2)) }}
                            </div>
                        @endif

                        <h5 class="fw-bold text-white mb-1">{{ $user->name }}</h5>
                        <p class="text-white-50 fs-8 mb-2">{{ $user->email }}</p>

                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <span class="badge" style="background-color: #f3bc42; color: #071a33; font-weight: 700; font-size: 11px;">
                                {{ $user->role?->label() ?? 'Peserta' }}
                            </span>
                            @if ($isGoogleUser)
                                <span class="badge bg-white text-dark fs-8 d-inline-flex align-items-center gap-1">
                                    <i class="ri-google-fill text-danger"></i> Google Linked
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 bg-white">
                        <div class="mb-3">
                            <span class="text-xs text-muted d-block mb-1">Status Kepegawaian</span>
                            @if ($isComplete)
                                <div class="alert alert-success py-2 px-3 radius-8 text-xs mb-0 d-flex align-items-center gap-2 border-0 bg-success-50 text-success-700">
                                    <i class="ri-checkbox-circle-fill fs-6 flex-shrink-0"></i>
                                    <span>Data kepegawaian Anda telah lengkap dan memenuhi syarat untuk mendaftar kelas.</span>
                                </div>
                            @else
                                <div class="alert alert-warning py-2 px-3 radius-8 text-xs mb-0 d-flex align-items-center gap-2 border-0 bg-warning-50 text-warning-800"
                                    style="background-color: #fefce8; border: 1px solid #fef08a !important; color: #854d0e;">
                                    <i class="ri-error-warning-fill fs-6 flex-shrink-0 text-warning"></i>
                                    <span>Mohon lengkapi <strong>NIP, Instansi, Jabatan, dan Pangkat</strong> agar Anda dapat mendaftar sesi kelas resmi ASN.</span>
                                </div>
                            @endif
                        </div>

                        <hr class="my-3">

                        <div class="vstack gap-2 fs-7 text-secondary">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted"><i class="ri-fingerprint-line me-1 text-primary"></i>NIP:</span>
                                <strong class="text-dark font-monospace">{{ $user->nip ?: '(Belum diisi)' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted"><i class="ri-building-line me-1 text-primary"></i>Instansi:</span>
                                <strong class="text-dark text-truncate" style="max-width: 170px;">{{ $user->opd_agency ?: '(Belum diisi)' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted"><i class="ri-briefcase-line me-1 text-primary"></i>Jabatan:</span>
                                <strong class="text-dark text-truncate" style="max-width: 170px;">{{ $user->position ?: '(Belum diisi)' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted"><i class="ri-medal-line me-1 text-primary"></i>Pangkat:</span>
                                <strong class="text-dark text-truncate" style="max-width: 170px;">{{ $user->rank_class ?: '(Belum diisi)' }}</strong>
                            </div>
                        </div>

                        <div class="mt-4 pt-2">
                            <a href="{{ route('landing') }}#katalog-kelas" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                                <i class="ri-book-open-line me-1"></i> Jelajahi Katalog Kelas
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Form Edit Profil ASN --}}
            <div class="col-lg-8 col-md-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                        <div>
                            <h4 class="fw-bold text-dark mb-1">Data Kepegawaian ASN</h4>
                            <p class="text-muted fs-7 mb-0">Pastikan data yang Anda masukkan sesuai dengan basis data BKN / SIASN Aceh Timur.</p>
                        </div>
                    </div>

                    @if (session()->has('warning'))
                        <div class="alert alert-warning py-3 px-4 radius-10 mb-4 d-flex align-items-start gap-3 border-0 bg-warning-50 text-warning-900 shadow-xs"
                            style="background-color: #fffbeb !important; border: 1px solid #fef08a !important; color: #854d0e;">
                            <i class="ri-error-warning-fill fs-4 flex-shrink-0 text-warning mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-1" style="color: #854d0e;">Lengkapi Profil ASN Terlebih Dahulu</h6>
                                <p class="mb-0 fs-7" style="color: #713f12;">{{ session('warning') }}</p>
                            </div>
                        </div>
                    @endif

                    @if (session()->has('success'))
                        <div class="alert alert-success py-2 px-3 radius-8 text-xs mb-4 d-flex align-items-center gap-2 border-0 bg-success-50 text-success-700">
                            <i class="ri-checkbox-circle-fill fs-5 flex-shrink-0"></i>
                            <span class="fw-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form wire:submit="save">
                        {{-- SECTION 1: DATA IDENTITAS --}}
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label text-xs fw-semibold text-secondary-dark mb-1">
                                    Alamat Email Terdaftar
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="ri-mail-line"></i>
                                    </span>
                                    <input type="text" class="form-control bg-light text-muted border-start-0" value="{{ $form['email'] }}" disabled>
                                    @if ($isGoogleUser)
                                        <span class="input-group-text bg-light text-success border-start-0 text-xs">
                                            <i class="ri-google-fill me-1"></i> Terverifikasi Google
                                        </span>
                                    @endif
                                </div>
                                <small class="text-muted fs-8">Alamat email digunakan sebagai identitas akun dan tidak dapat diubah secara langsung.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-xs fw-semibold text-secondary-dark mb-1" for="user-name">
                                    Nama Lengkap & Gelar <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="ri-user-line"></i>
                                    </span>
                                    <input type="text" id="user-name" wire:model="form.name"
                                        class="form-control @error('form.name') is-invalid @enderror"
                                        placeholder="Nama lengkap beserta gelar">
                                </div>
                                @error('form.name')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-xs fw-semibold text-secondary-dark mb-1" for="user-nip">
                                    NIP (18 Digit Angka) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="ri-fingerprint-line"></i>
                                    </span>
                                    <input type="text" id="user-nip" wire:model="form.nip"
                                        class="form-control font-monospace @error('form.nip') is-invalid @enderror"
                                        placeholder="199205052018011005" maxlength="18" inputmode="numeric">
                                </div>
                                @error('form.nip')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-xs fw-semibold text-secondary-dark mb-1" for="user-phone">
                                    No. WhatsApp Aktif <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="ri-whatsapp-line"></i>
                                    </span>
                                    <input type="tel" id="user-phone" wire:model="form.phone_number"
                                        class="form-control @error('form.phone_number') is-invalid @enderror"
                                        placeholder="08xxxxxxxxxx">
                                </div>
                                @error('form.phone_number')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-xs fw-semibold text-secondary-dark mb-1" for="user-opd">
                                    Instansi / OPD Asal <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="ri-building-line"></i>
                                    </span>
                                    <input type="text" id="user-opd" wire:model="form.opd_agency" list="opd-suggestions"
                                        class="form-control @error('form.opd_agency') is-invalid @enderror"
                                        placeholder="Nama Dinas / Badan / Kantor">
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

                            <div class="col-md-6">
                                <label class="form-label text-xs fw-semibold text-secondary-dark mb-1" for="user-position">
                                    Jabatan Saat Ini <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="ri-briefcase-line"></i>
                                    </span>
                                    <input type="text" id="user-position" wire:model="form.position"
                                        class="form-control @error('form.position') is-invalid @enderror"
                                        placeholder="Contoh: Analis Kebijakan Ahli Pertama">
                                </div>
                                @error('form.position')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-xs fw-semibold text-secondary-dark mb-1" for="user-rank">
                                    Pangkat / Golongan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="ri-medal-line"></i>
                                    </span>
                                    <select id="user-rank" wire:model="form.rank_class"
                                        class="form-select @error('form.rank_class') is-invalid @enderror">
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
                                            <option value="Penata Muda Tingkat I - III/b">Penata Muda Tingkat I - III/b</option>
                                            <option value="Penata Muda - III/a">Penata Muda - III/a</option>
                                        </optgroup>
                                        <optgroup label="Golongan II (Pengatur)">
                                            <option value="Pengatur Tingkat I - II/d">Pengatur Tingkat I - II/d</option>
                                            <option value="Pengatur - II/c">Pengatur - II/c</option>
                                            <option value="Pengatur Muda Tingkat I - II/b">Pengatur Muda Tingkat I - II/b</option>
                                            <option value="Pengatur Muda - II/a">Pengatur Muda - II/a</option>
                                        </optgroup>
                                        <optgroup label="Golongan I (Juru)">
                                            <option value="Juru Tingkat I - I/d">Juru Tingkat I - I/d</option>
                                            <option value="Juru - I/c">Juru - I/c</option>
                                            <option value="Juru Muda Tingkat I - I/b">Juru Muda Tingkat I - I/b</option>
                                            <option value="Juru Muda - I/a">Juru Muda - I/a</option>
                                        </optgroup>
                                        <optgroup label="Lainnya">
                                            <option value="PPPK">Pegawai Pemerintah dengan Perjanjian Kerja (PPPK)</option>
                                            <option value="PPNPN">Pegawai Non-PNS / PPNPN</option>
                                        </optgroup>
                                    </select>
                                </div>
                                @error('form.rank_class')
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('landing') }}" class="btn btn-outline-secondary px-4 rounded-pill" wire:navigate>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-4 rounded-pill fw-semibold shadow-sm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save">
                                    <i class="ri-save-line me-1"></i> Simpan Data Profil
                                </span>
                                <span wire:loading wire:target="save" style="display: none;">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Riwayat Evaluasi & Kuis ASN --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white mt-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom flex-wrap gap-2">
                        <div>
                            <h4 class="fw-bold text-dark mb-1">Riwayat Evaluasi &amp; Kuis</h4>
                            <p class="text-muted fs-7 mb-0">Catatan permanen hasil pengerjaan kuis materi dan ujian akhir kelas Anda.</p>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fs-8">
                            {{ $quizAttempts->count() }} Evaluasi Dikerjakan
                        </span>
                    </div>

                    @if ($quizAttempts->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-xs">
                                <thead class="table-light">
                                    <tr>
                                        <th>Evaluasi &amp; Program Kelas</th>
                                        <th class="text-center" style="width: 130px;">Nilai Skor</th>
                                        <th class="text-center" style="width: 120px;">Status</th>
                                        <th class="text-center" style="width: 140px;">Waktu Selesai</th>
                                        <th class="text-center" style="width: 90px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($quizAttempts as $attempt)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark mb-1">
                                                    {{ $attempt->quiz?->title ?? 'Evaluasi Kuis' }}
                                                </div>
                                                <div class="text-muted text-xxs d-flex align-items-center gap-1 flex-wrap">
                                                    <span>{{ $attempt->quiz?->course?->title ?? '-' }}</span>
                                                    @if ($attempt->quiz?->isFinalQuiz())
                                                        <span class="badge bg-warning-subtle text-warning border border-warning text-xxs">
                                                            Ujian Akhir Kelas
                                                        </span>
                                                    @elseif ($attempt->quiz?->chapter)
                                                        <span class="badge bg-info-subtle text-info border border-info text-xxs">
                                                            Bab: {{ Str::limit($attempt->quiz->chapter->title, 20) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="fw-bold text-dark fs-7">
                                                    {{ $attempt->total_earned_score }} / {{ $attempt->total_possible_score }}
                                                </div>
                                                <span class="text-muted text-xxs">
                                                    ({{ number_format($attempt->percentage, 1) }}%)
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if ($attempt->is_passed)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 radius-6">
                                                        <i class="ri-checkbox-circle-fill me-1"></i>Lulus
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 radius-6">
                                                        <i class="ri-close-circle-fill me-1"></i>Belum Lulus
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center text-muted">
                                                {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') : '-' }}
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('peserta.evaluasi.kerjakan', ['course_id' => $attempt->quiz->course_id, 'quiz_id' => $attempt->quiz_id]) }}"
                                                    class="btn btn-sm btn-outline-primary py-1 px-2 radius-6 text-xxs fw-semibold"
                                                    title="Buka Lembar Hasil Evaluasi" wire:navigate>
                                                    <i class="ri-eye-line me-1"></i>Lihat
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted border rounded-3 bg-light">
                            <i class="ri-survey-line text-secondary mb-2 d-block" style="font-size: 40px; opacity: 0.5;"></i>
                            <h6 class="fw-bold text-dark mb-1 fs-7">Belum Ada Riwayat Evaluasi</h6>
                            <p class="text-muted text-xs mb-3">
                                Selesaikan materi pembelajaran di kelas yang Anda ikuti untuk mengerjakan kuis bab atau ujian akhir.
                            </p>
                            <a href="{{ route('landing') }}#katalog-kelas" class="btn btn-sm btn-outline-primary rounded-pill px-3" wire:navigate>
                                <i class="ri-book-open-line me-1"></i> Mulai Belajar Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

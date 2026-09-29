<div>
    {{-- Page Header --}}
    <section class="py-5 text-white"
        style="background: linear-gradient(135deg, #051427 0%, #071a33 50%, #0c3158 100%);">
        <div class="container py-lg-3 py-2">
            <div class="row align-items-center">
                <div class="col-lg-8 wow fadeInLeft" data-wow-delay="100ms">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge bg-gold text-navy fw-bold fs-8 px-3 py-1_5">
                            <i class="ri-customer-service-2-line me-1"></i>Layanan Bantuan &amp; Informasi
                        </span>
                    </div>
                    <h1 class="display-6 fw-extrabold text-white mb-3">
                        Hubungi Kami - <span class="text-gold">SIMPEL BKPSDM</span>
                    </h1>
                    <p class="text-white-80 fs-6 mb-0 pe-lg-4">
                        Punya pertanyaan mengenai kelas pelatihan, kurikulum pembelajaran, atau membutuhkan bantuan teknis?
                        Tim helpdesk BKPSDM Kabupaten Aceh Timur siap melayani dan mendampingi Anda.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <a href="{{ route('landing') }}" class="btn-simpel-outline-light">
                        <i class="ri-arrow-left-line"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact Info Cards --}}
    <section class="py-5 bg-white border-bottom">
        <div class="container py-lg-3 py-1">
            <div class="row g-4">
                {{-- Alamat --}}
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                    <div class="card h-100 border rounded-4 p-4 shadow-xs text-center transition-all hover-shadow"
                        style="background: #fafcff; border-color: #e2e8f0 !important;">
                        <div class="w-56-px h-56-px rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-3 mx-auto mb-3">
                            <i class="ri-map-pin-2-fill"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">Kantor BKPSDM</h6>
                        <p class="text-muted fs-7 mb-0">
                            Komplek Pusat Pemerintahan Pemkab Aceh Timur, Idi Rayeuk, Kab. Aceh Timur, Aceh
                        </p>
                    </div>
                </div>

                {{-- WhatsApp / Telepon --}}
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="150ms">
                    <div class="card h-100 border rounded-4 p-4 shadow-xs text-center transition-all hover-shadow"
                        style="background: #fafcff; border-color: #e2e8f0 !important;">
                        <div class="w-56-px h-56-px rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fs-3 mx-auto mb-3">
                            <i class="ri-whatsapp-fill"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">Layanan WhatsApp</h6>
                        <p class="text-muted fs-7 mb-1">
                            <a href="https://wa.me/6285260000000" target="_blank" class="text-dark fw-bold text-decoration-none">
                                +62 852-6000-xxxx
                            </a>
                        </p>
                        <small class="text-success fw-medium" style="font-size: 11px;">Respon Cepat Jam Kerja</small>
                    </div>
                </div>

                {{-- Email Resmi --}}
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                    <div class="card h-100 border rounded-4 p-4 shadow-xs text-center transition-all hover-shadow"
                        style="background: #fafcff; border-color: #e2e8f0 !important;">
                        <div class="w-56-px h-56-px rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center fs-3 mx-auto mb-3">
                            <i class="ri-mail-send-fill"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">Email Kedinasan</h6>
                        <p class="text-muted fs-7 mb-1">
                            <a href="mailto:bkpsdm@acehtimurkab.go.id" class="text-dark fw-semibold text-decoration-none">
                                bkpsdm@acehtimurkab.go.id
                            </a>
                        </p>
                        <small class="text-muted" style="font-size: 11px;">Dukungan surat &amp; teknis</small>
                    </div>
                </div>

                {{-- Jam Operasional --}}
                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="250ms">
                    <div class="card h-100 border rounded-4 p-4 shadow-xs text-center transition-all hover-shadow"
                        style="background: #fafcff; border-color: #e2e8f0 !important;">
                        <div class="w-56-px h-56-px rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center fs-3 mx-auto mb-3">
                            <i class="ri-time-fill"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-2">Jam Pelayanan</h6>
                        <p class="text-dark fs-7 fw-semibold mb-1">
                            Senin – Jumat
                        </p>
                        <small class="text-muted" style="font-size: 12px;">08.00 – 16.30 WIB</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Section: Form & FAQ --}}
    <section class="py-5 bg-light">
        <div class="container py-lg-4 py-2">
            <div class="row g-4">
                {{-- Form Kirim Pesan --}}
                <div class="col-lg-7 col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white">
                        <div class="mb-4">
                            <span class="badge bg-gold text-navy fw-bold fs-8 px-3 py-1 mb-2">
                                <i class="ri-send-plane-fill me-1"></i>Formulir Kontak
                            </span>
                            <h4 class="fw-bold text-dark mb-1">Kirim Pesan / Pertanyaan</h4>
                            <p class="text-muted fs-7 mb-0">Silakan isi formulir di bawah ini. Tim administrator SIMPEL akan segera merespon melalui email atau WhatsApp Anda.</p>
                        </div>

                        @if (session()->has('contact-success'))
                            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 radius-12 p-16 mb-4 border-0 shadow-sm" role="alert">
                                <i class="ri-checkbox-circle-fill fs-4 text-success flex-shrink-0"></i>
                                <div class="text-success fs-7 fw-medium">
                                    {{ session('contact-success') }}
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form wire:submit.prevent="sendMessage">
                            <div class="row g-3">
                                {{-- Nama --}}
                                <div class="col-md-6 col-12">
                                    <label for="contact-name" class="form-label text-dark fw-medium fs-7 mb-1">
                                        Nama Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <div class="position-relative">
                                        <input type="text" id="contact-name" wire:model="name"
                                            class="form-control radius-8 ps-5 @error('name') is-invalid @enderror"
                                            placeholder="Nama lengkap Anda">
                                        <i class="ri-user-line position-absolute top-50 translate-middle-y start-0 ms-3 text-muted"></i>
                                    </div>
                                    @error('name')
                                        <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6 col-12">
                                    <label for="contact-email" class="form-label text-dark fw-medium fs-7 mb-1">
                                        Alamat Email <span class="text-danger">*</span>
                                    </label>
                                    <div class="position-relative">
                                        <input type="email" id="contact-email" wire:model="email"
                                            class="form-control radius-8 ps-5 @error('email') is-invalid @enderror"
                                            placeholder="email@domain.com">
                                        <i class="ri-mail-line position-absolute top-50 translate-middle-y start-0 ms-3 text-muted"></i>
                                    </div>
                                    @error('email')
                                        <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- No HP --}}
                                <div class="col-md-6 col-12">
                                    <label for="contact-phone" class="form-label text-dark fw-medium fs-7 mb-1">
                                        Nomor WhatsApp / HP
                                    </label>
                                    <div class="position-relative">
                                        <input type="text" id="contact-phone" wire:model="phone"
                                            class="form-control radius-8 ps-5 font-monospace @error('phone') is-invalid @enderror"
                                            placeholder="Contoh: 081234567890">
                                        <i class="ri-phone-line position-absolute top-50 translate-middle-y start-0 ms-3 text-muted"></i>
                                    </div>
                                    @error('phone')
                                        <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Subjek / Kategori --}}
                                <div class="col-md-6 col-12">
                                    <label for="contact-subject" class="form-label text-dark fw-medium fs-7 mb-1">
                                        Kategori Pertanyaan <span class="text-danger">*</span>
                                    </label>
                                    <div class="position-relative">
                                        <select id="contact-subject" wire:model="subject"
                                            class="form-select radius-8 ps-5 @error('subject') is-invalid @enderror">
                                            <option value="">-- Pilih Topik Pertanyaan --</option>
                                            <option value="Informasi Pendaftaran Kelas">Informasi Pendaftaran Kelas</option>
                                            <option value="Kendala Akun & Login">Kendala Akun &amp; Login</option>
                                            <option value="Materi & Evaluasi Pembelajaran">Materi &amp; Evaluasi Pembelajaran</option>
                                            <option value="Penerbitan Sertifikat Diklat">Penerbitan Sertifikat Diklat</option>
                                            <option value="Kerja Sama & Instansi">Kerja Sama &amp; Instansi</option>
                                            <option value="Pertanyaan Umum Lainnya">Pertanyaan Umum Lainnya</option>
                                        </select>
                                        <i class="ri-questionnaire-line position-absolute top-50 translate-middle-y start-0 ms-3 text-muted"></i>
                                    </div>
                                    @error('subject')
                                        <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Pesan --}}
                                <div class="col-12">
                                    <label for="contact-message" class="form-label text-dark fw-medium fs-7 mb-1">
                                        Isi Pesan / Pertanyaan <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="contact-message" wire:model="message" rows="5"
                                        class="form-control radius-8 p-3 @error('message') is-invalid @enderror"
                                        placeholder="Tuliskan secara jelas kendala atau pertanyaan yang ingin Anda tanyakan kepada tim BKPSDM..."></textarea>
                                    @error('message')
                                        <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 pt-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2_5 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="sendMessage">
                                        <i class="ri-send-plane-2-line"></i> Kirim Pesan Sekarang
                                    </span>
                                    <span wire:loading wire:target="sendMessage">
                                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mengirimkan...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Kolom Kanan: Bantuan Cepat WhatsApp & FAQ --}}
                <div class="col-lg-5 col-12">
                    <div class="vstack gap-4">
                        {{-- WhatsApp Direct Box --}}
                        <div class="card border-0 shadow-sm rounded-4 p-4 text-white"
                            style="background: linear-gradient(135deg, #071a33 0%, #0d3866 100%);">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-success text-white px-2 py-1 fs-8 rounded-pill d-inline-flex align-items-center gap-1">
                                    <span class="w-8-px h-8-px rounded-circle bg-white d-inline-block"></span>
                                    Online Sekarang
                                </span>
                            </div>
                            <h5 class="fw-bold text-white mb-2">Konsultasi Cepat via WhatsApp</h5>
                            <p class="text-white-80 fs-7 mb-3">
                                Butuh respon cepat atau konsultasi langsung dengan staf kepegawaian BKPSDM Aceh Timur? Hubungi melalui kanal WhatsApp resmi kami.
                            </p>
                            <div>
                                <a href="https://wa.me/6285260000000?text=Halo%20Admin%20SIMPEL%20BKPSDM%2C%20saya%20ingin%20bertanya%20seputar%20pelatihan"
                                    target="_blank"
                                    class="btn btn-success rounded-pill px-4 py-2_5 fw-bold d-inline-flex align-items-center gap-2 shadow">
                                    <i class="ri-whatsapp-line fs-5"></i> Chat WhatsApp Helpdesk
                                </a>
                            </div>
                        </div>

                        {{-- FAQ Mini Accordion --}}
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="ri-question-answer-line text-primary fs-5"></i>
                                <h6 class="fw-bold text-dark mb-0 fs-6">Pertanyaan Populer (FAQ)</h6>
                            </div>

                            <div class="accordion accordion-flush" id="faqAccordion">
                                <div class="accordion-item border-bottom">
                                    <h2 class="accordion-header" id="faq1">
                                        <button class="accordion-button collapsed px-0 py-3 fw-semibold text-dark fs-7" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq1" aria-expanded="false">
                                            Siapa saja yang bisa mendaftar kelas?
                                        </button>
                                    </h2>
                                    <div id="collapseFaq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body px-0 py-2 text-muted fs-7">
                                            Seluruh ASN dan pegawai di lingkungan Pemerintah Kabupaten Aceh Timur serta instansi terkait dapat mendaftar kelas yang tersedia di SIMPEL.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item border-bottom">
                                    <h2 class="accordion-header" id="faq2">
                                        <button class="accordion-button collapsed px-0 py-3 fw-semibold text-dark fs-7" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq2" aria-expanded="false">
                                            Bagaimana jika lupa kata sandi akun?
                                        </button>
                                    </h2>
                                    <div id="collapseFaq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body px-0 py-2 text-muted fs-7">
                                            Anda dapat menggunakan fitur reset kata sandi pada halaman Masuk atau menghubungi kontak helpdesk BKPSDM dengan melampirkan NIP dan email terdaftar.
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq3">
                                        <button class="accordion-button collapsed px-0 py-3 fw-semibold text-dark fs-7" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq3" aria-expanded="false">
                                            Apakah materi diklat bisa diakses fleksibel?
                                        </button>
                                    </h2>
                                    <div id="collapseFaq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body px-0 py-2 text-muted fs-7">
                                            Ya, untuk kelas tipe Permanen, materi pembelajaran dan modul dapat diakses kapan saja secara mandiri selama 24 jam.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Map Section --}}
    <section class="py-5 bg-white border-top">
        <div class="container py-lg-3 py-1">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Lokasi Kantor BKPSDM Aceh Timur</h5>
                    <p class="text-muted fs-7 mb-0">Idi Rayeuk, Kabupaten Aceh Timur, Provinsi Aceh</p>
                </div>
                <a href="https://maps.google.com/?q=BKPSDM+Kabupaten+Aceh+Timur" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-1">
                    <i class="ri-external-link-line"></i> Buka di Google Maps
                </a>
            </div>
            <div class="rounded-4 overflow-hidden shadow-sm border" style="height: 380px;">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127116.80497554448!2d97.6841267425781!3d4.945532099999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3037f5d6067fa3b7%3A0x6a2c2ebdd7690623!2sBKPSDM%20Kabupaten%20Aceh%20Timur!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>
</div>

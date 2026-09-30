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

    @if ($settings->hasContactInfo())
        {{-- Contact Info Cards --}}
        <section class="py-5 bg-white border-bottom">
            <div class="container py-lg-3 py-1">
                <div class="row g-4 justify-content-center">
                    {{-- Alamat --}}
                    @if ($settings->office_title || $settings->address)
                        <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                            <div class="card h-100 border rounded-4 p-4 shadow-xs text-center transition-all hover-shadow"
                                style="background: #fafcff; border-color: #e2e8f0 !important;">
                                <div class="w-56-px h-56-px rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-3 mx-auto mb-3">
                                    <i class="ri-map-pin-2-fill"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-2">{{ $settings->office_title ?? 'Alamat Kantor' }}</h6>
                                @if ($settings->address)
                                    <p class="text-muted fs-7 mb-0">
                                        {{ $settings->address }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- WhatsApp / Telepon --}}
                    @if ($settings->whatsapp_number)
                        <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="150ms">
                            <div class="card h-100 border rounded-4 p-4 shadow-xs text-center transition-all hover-shadow"
                                style="background: #fafcff; border-color: #e2e8f0 !important;">
                                <div class="w-56-px h-56-px rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fs-3 mx-auto mb-3">
                                    <i class="ri-whatsapp-fill"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-2">{{ $settings->whatsapp_label ?? 'Layanan WhatsApp' }}</h6>
                                <p class="text-muted fs-7 mb-1">
                                    <a href="{{ $settings->whatsapp_url }}" target="_blank" class="text-dark fw-bold text-decoration-none">
                                        {{ $settings->whatsapp_number }}
                                    </a>
                                </p>
                                <small class="text-success fw-medium" style="font-size: 11px;">Respon Cepat Jam Kerja</small>
                            </div>
                        </div>
                    @endif

                    {{-- Email Resmi --}}
                    @if ($settings->email)
                        <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="200ms">
                            <div class="card h-100 border rounded-4 p-4 shadow-xs text-center transition-all hover-shadow"
                                style="background: #fafcff; border-color: #e2e8f0 !important;">
                                <div class="w-56-px h-56-px rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center fs-3 mx-auto mb-3">
                                    <i class="ri-mail-send-fill"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-2">Email Kedinasan</h6>
                                <p class="text-muted fs-7 mb-1">
                                    <a href="mailto:{{ $settings->email }}" class="text-dark fw-semibold text-decoration-none">
                                        {{ $settings->email }}
                                    </a>
                                </p>
                                <small class="text-muted" style="font-size: 11px;">Dukungan surat &amp; teknis</small>
                            </div>
                        </div>
                    @endif

                    {{-- Jam Operasional --}}
                    @if ($settings->service_days || $settings->service_hours)
                        <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="250ms">
                            <div class="card h-100 border rounded-4 p-4 shadow-xs text-center transition-all hover-shadow"
                                style="background: #fafcff; border-color: #e2e8f0 !important;">
                                <div class="w-56-px h-56-px rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center fs-3 mx-auto mb-3">
                                    <i class="ri-time-fill"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-2">Jam Pelayanan</h6>
                                @if ($settings->service_days)
                                    <p class="text-dark fs-7 fw-semibold mb-1">
                                        {{ $settings->service_days }}
                                    </p>
                                @endif
                                @if ($settings->service_hours)
                                    <small class="text-muted" style="font-size: 12px;">{{ $settings->service_hours }}</small>
                                @endif
                            </div>
                        </div>
                    @endif
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
                                            class="form-select ignore radius-8 ps-5 @error('subject') is-invalid @enderror"
                                            style="background-color: #ffffff; border: 1px solid #ced4da; padding-top: 0.6rem; padding-bottom: 0.6rem; font-size: 0.875rem; cursor: pointer;">
                                            <option value="">-- Pilih Topik Pertanyaan --</option>
                                            <option value="Informasi Pendaftaran Kelas">Informasi Pendaftaran Kelas</option>
                                            <option value="Kendala Akun & Login">Kendala Akun &amp; Login</option>
                                            <option value="Materi & Evaluasi Pembelajaran">Materi &amp; Evaluasi Pembelajaran</option>
                                            <option value="Penerbitan Sertifikat Diklat">Penerbitan Sertifikat Diklat</option>
                                            <option value="Kerja Sama & Instansi">Kerja Sama &amp; Instansi</option>
                                            <option value="Pertanyaan Umum Lainnya">Pertanyaan Umum Lainnya</option>
                                        </select>
                                        <i class="ri-questionnaire-line position-absolute top-50 translate-middle-y start-0 ms-3 text-muted pointer-events-none" style="pointer-events: none;"></i>
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
                                <button type="submit" class="btn btn-simple-gold rounded-pill px-4 py-2_5 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm"
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
                        @if ($settings->whatsapp_number)
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
                                    <a href="https://wa.me/{{ $settings->clean_whatsapp_number }}?text=Halo%20Admin%20SIMPEL%20BKPSDM%2C%20saya%20ingin%20bertanya%20seputar%20pelatihan"
                                        target="_blank"
                                        class="btn btn-success rounded-pill px-4 py-2_5 fw-bold d-inline-flex align-items-center gap-2 shadow">
                                        <i class="ri-whatsapp-line fs-5"></i> Chat WhatsApp Helpdesk
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- FAQ Mini Accordion --}}
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="ri-question-answer-line text-primary fs-5"></i>
                                <h6 class="fw-bold text-dark mb-0 fs-6">Pertanyaan Populer (FAQ)</h6>
                            </div>

                            <div class="accordion accordion-flush" id="faqAccordion">
                                @forelse ($faqs as $faq)
                                    <div class="accordion-item {{ ! $loop->last ? 'border-bottom' : '' }}">
                                        <h2 class="accordion-header" id="faq{{ $faq->id }}">
                                            <button class="accordion-button collapsed px-0 py-3 fw-semibold text-dark fs-7" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFaq{{ $faq->id }}" aria-expanded="false">
                                                {{ $faq->question }}
                                            </button>
                                        </h2>
                                        <div id="collapseFaq{{ $faq->id }}" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                            <div class="accordion-body px-0 py-2 text-muted fs-7">
                                                {!! nl2br(e($faq->answer)) !!}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted fs-7 py-3 text-center">Belum ada pertanyaan populer saat ini.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Map Section --}}
    @if ($settings->maps_embed_url)
        <section class="py-5 bg-white border-top">
            <div class="container py-lg-3 py-1">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Lokasi {{ $settings->office_title ?? 'Kantor' }}</h5>
                        @if ($settings->address)
                            <p class="text-muted fs-7 mb-0">{{ $settings->address }}</p>
                        @endif
                    </div>
                    <a href="{{ $settings->maps_url ?: 'https://www.google.com/maps/search/?api=1&query=' . urlencode(($settings->office_title ? $settings->office_title . ', ' : '') . ($settings->address ?: '')) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 d-flex align-items-center gap-1">
                        <i class="ri-external-link-line"></i> Buka di Google Maps
                    </a>
                </div>
                <div class="rounded-4 overflow-hidden shadow-sm border" style="height: 380px;">
                    <iframe
                        src="{{ $settings->maps_embed_url }}"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </section>
    @endif
@else
    {{-- Empty State (matching kelas-berbayar) --}}
    <section class="py-5 bg-light">
        <div class="container py-lg-4 py-2">
            <div class="card border border-simpel rounded-4 bg-white p-5 text-center shadow-xs">
                <div class="mb-3">
                    <div class="d-inline-flex p-3 rounded-circle bg-light text-navy">
                        <i class="ri-contacts-book-2-line display-5"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-navy mb-2">Informasi Kontak Belum Tersedia</h5>
                <p class="text-muted fs-7 max-w-500 mx-auto mb-4">
                    Informasi kontak dan layanan bantuan belum dikonfigurasi atau sedang dalam proses pembaruan oleh administrator.
                </p>
                <div>
                    <a href="{{ route('landing') }}" class="btn-simpel-cta-gold py-2 px-4 fs-7">
                        <i class="ri-home-4-line me-1"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </section>
@endif

    <style>
        select#contact-subject.ignore {
            display: block !important;
            background-color: #ffffff !important;
            border: 1px solid #ced4da !important;
            color: #212529 !important;
        }
        #contact-subject + .nice-select {
            display: none !important;
        }
    </style>
</div>

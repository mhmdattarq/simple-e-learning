<div>
    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Pengaturan Kontak &amp; FAQ</h5>
            <p class="text-muted mb-0">Kelola identitas kantor, kontak resmi, dan daftar tanya-jawab populer pada halaman
                bantuan.</p>
        </div>
        <div>
            <a href="{{ route('kontak') }}" target="_blank"
                class="btn btn-outline-primary btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1">
                <i class="ri-external-link-line"></i> Lihat Halaman Landing
            </a>
        </div>
    </div>

    @push('css')
        <style>
            .custom-nav-pills {
                border-color: #e2e8f0 !important;
                background: #ffffff !important;
            }

            .custom-nav-pills .nav-link {
                color: #475569 !important;
                border-radius: 8px !important;
                font-weight: 600 !important;
                font-size: 13.5px !important;
                padding: 8px 18px !important;
                display: inline-flex !important;
                align-items: center !important;
                gap: 6px !important;
                transition: all 0.2s ease !important;
            }

            .custom-nav-pills .nav-link:hover {
                color: #071a33 !important;
                background-color: #f8fafc !important;
            }

            .custom-nav-pills .nav-link.active {
                background-color: #f3bc42 !important;
                background: #f3bc42 !important;
                color: #071a33 !important;
                font-weight: 700 !important;
                box-shadow: 0 4px 14px rgba(243, 188, 66, 0.35) !important;
            }

            .custom-nav-pills .nav-link.active i {
                color: #071a33 !important;
            }

            .custom-nav-pills .nav-link.active .badge {
                background-color: #071a33 !important;
                color: #ffffff !important;
            }
        </style>
    @endpush

    {{-- Tab Navigation --}}
    <ul class="nav nav-pills custom-nav-pills gap-2 mb-24 bg-white p-2 rounded-3 border shadow-xs d-inline-flex">
        <li class="nav-item">
            <button type="button" class="nav-link px-4 py-2 fw-semibold {{ $activeTab === 'kontak' ? 'active' : '' }}"
                wire:click="setTab('kontak')">
                <i class="ri-contacts-book-2-line me-1"></i> Informasi Kantor &amp; Kontak
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link px-4 py-2 fw-semibold {{ $activeTab === 'faq' ? 'active' : '' }}"
                wire:click="setTab('faq')">
                <i class="ri-question-answer-line me-1"></i> Pertanyaan Populer (FAQ)
                <span class="badge bg-secondary-subtle text-secondary ms-1">{{ count($faqs) }}</span>
            </button>
        </li>
    </ul>

    {{-- TAB 1: INFORMASI KONTAK --}}
    @if ($activeTab === 'kontak')
        <div class="row g-4">
            <div class="col-lg-8 col-12">
                <div class="card simpel-card border-0 shadow-sm radius-16 p-24 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-4 pb-3 border-bottom">
                        <i class="ri-building-4-line text-primary fs-4"></i>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Identitas Kontak &amp; Layanan Kantor</h6>
                            <small class="text-muted">Perubahan pada form ini akan langsung ditampilkan pada kartu
                                informasi landing page.</small>
                        </div>
                    </div>

                    <form wire:submit.prevent="saveSettings">
                        <div class="row g-3">
                            {{-- Nama Kantor --}}
                            <div class="col-md-6 col-12">
                                <label for="office_title" class="form-label text-dark fw-semibold fs-7 mb-1">
                                    Nama / Judul Kantor
                                </label>
                                <input type="text" id="office_title" wire:model="office_title"
                                    class="form-control radius-8 @error('office_title') is-invalid @enderror"
                                    placeholder="Contoh: Kantor BKPSDM Kabupaten Aceh Timur">
                                @error('office_title')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email Kedinasan --}}
                            <div class="col-md-6 col-12">
                                <label for="email" class="form-label text-dark fw-semibold fs-7 mb-1">
                                    Alamat Email Kedinasan
                                </label>
                                <input type="email" id="email" wire:model="email"
                                    class="form-control radius-8 @error('email') is-invalid @enderror"
                                    placeholder="Contoh: bkpsdm@acehtimurkab.go.id">
                                @error('email')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- No WhatsApp --}}
                            <div class="col-md-6 col-12">
                                <label for="whatsapp_number" class="form-label text-dark fw-semibold fs-7 mb-1">
                                    Nomor WhatsApp Helpdesk
                                </label>
                                <input type="text" id="whatsapp_number" wire:model="whatsapp_number"
                                    class="form-control radius-8 font-monospace @error('whatsapp_number') is-invalid @enderror"
                                    placeholder="Contoh: 085260000000">
                                @error('whatsapp_number')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" style="font-size: 11px;">Format nomor lokal atau internasional
                                    (diawali 08 atau 628).</small>
                            </div>

                            {{-- Label WhatsApp --}}
                            <div class="col-md-6 col-12">
                                <label for="whatsapp_label" class="form-label text-dark fw-semibold fs-7 mb-1">
                                    Label Kartu WhatsApp
                                </label>
                                <input type="text" id="whatsapp_label" wire:model="whatsapp_label"
                                    class="form-control radius-8 @error('whatsapp_label') is-invalid @enderror"
                                    placeholder="Contoh: Layanan WhatsApp Helpdesk">
                                @error('whatsapp_label')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Hari Layanan --}}
                            <div class="col-md-6 col-12">
                                <label for="service_days" class="form-label text-dark fw-semibold fs-7 mb-1">
                                    Hari Pelayanan
                                </label>
                                <input type="text" id="service_days" wire:model="service_days"
                                    class="form-control radius-8 @error('service_days') is-invalid @enderror"
                                    placeholder="Contoh: Senin – Jumat">
                                @error('service_days')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Jam Layanan --}}
                            <div class="col-md-6 col-12">
                                <label for="service_hours" class="form-label text-dark fw-semibold fs-7 mb-1">
                                    Jam Operasional
                                </label>
                                <input type="text" id="service_hours" wire:model="service_hours"
                                    class="form-control radius-8 @error('service_hours') is-invalid @enderror"
                                    placeholder="Contoh: 08.00 – 16.30 WIB">
                                @error('service_hours')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Alamat Kantor --}}
                            <div class="col-12">
                                <label for="address" class="form-label text-dark fw-semibold fs-7 mb-1">
                                    Alamat Fisik Kantor
                                </label>
                                <textarea id="address" wire:model="address" rows="3"
                                    class="form-control radius-8 @error('address') is-invalid @enderror"
                                    placeholder="Contoh: Komplek Pusat Pemerintahan Pemkab Aceh Timur, Idi Rayeuk, Kab. Aceh Timur, Aceh"></textarea>
                                @error('address')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Embed Google Maps --}}
                            <div class="col-12">
                                <label for="maps_embed_url" class="form-label text-dark fw-semibold fs-7 mb-1">
                                    URL Embed Google Maps
                                </label>
                                <textarea id="maps_embed_url" wire:model.blur="maps_embed_url" rows="3"
                                    class="form-control radius-8 @error('maps_embed_url') is-invalid @enderror"
                                    placeholder="Tempel URL embed (https://www.google.com/maps/embed?...) atau seluruh kode <iframe>...</iframe> dari Google Maps"></textarea>
                                @error('maps_embed_url')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-3 pt-2">
                            <button type="submit"
                                class="btn btn-simple-gold fw-semibold d-inline-flex align-items-cente shadow-sm w-100"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveSettings">
                                    <i class="ri-save-3-line"></i> Simpan Perubahan Kontak
                                </span>
                                <span wire:loading wire:target="saveSettings">
                                    <span class="spinner-border spinner-border-sm me-1" role="status"
                                        aria-hidden="true"></span> Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Preview Pratinjau Cepat --}}
            <div class="col-lg-4 col-12">
                <div class="card simpel-card border-0 shadow-sm radius-16 p-20 bg-white">
                    <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                        <i class="ri-eye-line text-primary"></i> Pratinjau Tampilan Landing
                    </h6>

                    <div class="vstack gap-3">
                        <div class="p-3 bg-light rounded-3 border">
                            <small class="text-muted d-block fs-8 text-uppercase fw-semibold mb-1">Nama Kantor:</small>
                            <strong class="text-dark fs-7">{{ $office_title ?: '(Belum diatur)' }}</strong>
                            <p class="text-muted fs-8 mb-0 mt-1">{{ $address ?: '(Alamat belum diatur)' }}</p>
                        </div>

                        <div class="p-3 bg-light rounded-3 border">
                            <small class="text-muted d-block fs-8 text-uppercase fw-semibold mb-1">WhatsApp &amp;
                                Email:</small>
                            <div class="text-success fw-bold fs-7 mb-1"><i
                                    class="ri-whatsapp-line me-1"></i>{{ $whatsapp_number ?: '(Belum diatur)' }}</div>
                            <div class="text-primary fs-8"><i
                                    class="ri-mail-line me-1"></i>{{ $email ?: '(Belum diatur)' }}</div>
                        </div>

                        <div class="p-3 bg-light rounded-3 border">
                            <small class="text-muted d-block fs-8 text-uppercase fw-semibold mb-1">Jam
                                Pelayanan:</small>
                            <div class="text-dark fw-semibold fs-7">
                                {{ $service_days ?: '(Hari pelayanan belum diatur)' }}</div>
                            <small
                                class="text-muted fs-8">{{ $service_hours ?: '(Jam pelayanan belum diatur)' }}</small>
                        </div>

                        @if ($maps_embed_url)
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted d-block fs-8 text-uppercase fw-semibold mb-2">Pratinjau
                                    Peta:</small>
                                <div class="rounded-3 overflow-hidden shadow-xs border" style="height: 140px;">
                                    <iframe src="{{ $maps_embed_url }}" width="100%" height="100%"
                                        style="border:0;" loading="lazy"></iframe>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- TAB 2: KELOLA FAQ --}}
    @if ($activeTab === 'faq')
        <div class="card simpel-card border-0 shadow-sm radius-16">
            <div
                class="card-header bg-white pt-20 pb-0 px-20 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri-questionnaire-line text-simple fs-5"></i>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Daftar Pertanyaan Populer (FAQ)</h6>
                </div>
                <div>
                    <button type="button"
                        class="btn btn-simple-gold btn-sm rounded-pill px-3 d-inline-flex align-items-center gap-1 shadow-sm"
                        wire:click="openCreateFaqModal">
                        <i class="ri-add-line fs-6"></i> Tambah Pertanyaan FAQ
                    </button>
                </div>
            </div>

            <div class="card-body p-20">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">Urutan</th>
                                <th style="min-width: 250px;">Pertanyaan FAQ</th>
                                <th style="min-width: 350px;">Jawaban</th>
                                <th class="text-center" style="width: 120px;">Status</th>
                                <th class="text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($faqs as $faq)
                                <tr>
                                    <td class="text-center fw-bold text-dark font-monospace">
                                        #{{ $faq->order }}
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-7 mb-1">{{ $faq->question }}</div>
                                    </td>
                                    <td>
                                        <div class="text-muted fs-7" style="line-height: 1.5;">
                                            {{ Str::limit($faq->answer, 120) }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                            class="btn btn-sm rounded-pill px-3 py-1 fw-semibold {{ $faq->is_active ? 'btn-success-subtle text-success' : 'btn-secondary-subtle text-secondary' }}"
                                            style="font-size: 11px;"
                                            wire:click="toggleFaqActive({{ $faq->id }})"
                                            title="Klik untuk ubah status aktif/nonaktif">
                                            @if ($faq->is_active)
                                                <i class="ri-checkbox-circle-line me-1"></i> Aktif
                                            @else
                                                <i class="ri-close-circle-line me-1"></i> Nonaktif
                                            @endif
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary rounded-circle w-32-px h-32-px d-inline-flex align-items-center justify-content-center"
                                                wire:click="openEditFaqModal({{ $faq->id }})"
                                                title="Edit Pertanyaan">
                                                <i class="ri-edit-line"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger rounded-circle w-32-px h-32-px d-inline-flex align-items-center justify-content-center"
                                                wire:click="hookModalDeleteFaq({{ $faq->id }}, '{{ addslashes($faq->question) }}')"
                                                title="Hapus FAQ">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted fs-7">
                                        Belum ada data pertanyaan FAQ. Klik tombol "Tambah Pertanyaan FAQ" di atas untuk
                                        menambahkan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Tambah / Edit FAQ --}}
    <div wire:ignore.self class="modal fade" id="modalFaqForm" tabindex="-1" aria-labelledby="modalFaqFormLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow radius-16 overflow-hidden">
                <div class="modal-header border-bottom py-3 px-4 bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-questionnaire-fill text-primary fs-5"></i>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="modalFaqFormLabel">
                            {{ $faqId ? 'Edit Pertanyaan FAQ' : 'Tambah Pertanyaan FAQ Baru' }}
                        </h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form wire:submit.prevent="saveFaq">
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            {{-- Pertanyaan --}}
                            <div class="col-12">
                                <label for="faqQuestionInput" class="form-label text-dark fw-semibold fs-7 mb-1">
                                    Pertanyaan FAQ <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="faqQuestionInput" wire:model="faqQuestion"
                                    class="form-control radius-8 @error('faqQuestion') is-invalid @enderror"
                                    placeholder="Contoh: Bagaimana jika lupa kata sandi akun?">
                                @error('faqQuestion')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Jawaban --}}
                            <div class="col-12">
                                <label for="faqAnswerInput" class="form-label text-dark fw-semibold fs-7 mb-1">
                                    Jawaban Penjelasan <span class="text-danger">*</span>
                                </label>
                                <textarea id="faqAnswerInput" wire:model="faqAnswer" rows="5"
                                    class="form-control radius-8 @error('faqAnswer') is-invalid @enderror"
                                    placeholder="Tuliskan jawaban yang jelas dan membantu pengguna..."></textarea>
                                @error('faqAnswer')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Urutan --}}
                            <div class="col-md-6 col-12">
                                <label for="faqOrderInput" class="form-label text-dark fw-semibold fs-7 mb-1">
                                    Nomor Urutan Tampil <span class="text-danger">*</span>
                                </label>
                                <input type="number" id="faqOrderInput" wire:model="faqOrder" min="1"
                                    max="999"
                                    class="form-control radius-8 @error('faqOrder') is-invalid @enderror">
                                @error('faqOrder')
                                    <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" style="font-size: 11px;">Semakin kecil angka, semakin atas
                                    posisinya di accordion.</small>
                            </div>

                            {{-- Status Aktif --}}
                            <div class="col-md-6 col-12 d-flex flex-column justify-content-center">
                                <label class="form-label text-dark fw-semibold fs-7 mb-2">Status Publikasi</label>
                                <div
                                    class="form-check form-switch switch-primary d-flex align-items-center gap-2 m-0 p-0">
                                    <input class="form-check-input cursor-pointer m-0 flex-shrink-0" type="checkbox"
                                        role="switch" id="faqIsActiveInput" wire:model="faqIsActive"
                                        style="float: none;">
                                    <label
                                        class="form-check-label text-dark fw-semibold fs-7 cursor-pointer m-0 user-select-none"
                                        for="faqIsActiveInput">
                                        Tampilkan di Halaman Landing
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-2 px-4 bg-light d-flex justify-content-between">
                        <button type="button" class="btn btn-danger btn-sm px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-simple-gold btn-sm px-4 fw-semibold shadow-sm"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveFaq">
                                <i class="ri-check-line me-1"></i> Simpan FAQ
                            </span>
                            <span wire:loading wire:target="saveFaq">
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

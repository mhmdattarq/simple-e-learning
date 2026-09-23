<div>
    {{-- Header & Breadcrumb --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('materi.data') }}" class="btn btn-sm btn-light border px-2 py-1 radius-8 text-muted" wire:navigate>
                    <i class="ri-arrow-left-line"></i> Kembali ke Katalog
                </a>
                <span class="badge bg-secondary-subtle text-secondary px-2 py-1 radius-4 font-monospace fs-8">
                    {{ $course->code }}
                </span>
                @if ($course->isBatch())
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 radius-4 fs-8">
                        <i class="ri-calendar-line me-1"></i>Batch (Angkatan)
                    </span>
                @else
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 radius-4 fs-8">
                        <i class="ri-infinity-line me-1"></i>Permanen (Self-Paced)
                    </span>
                @endif
            </div>
            <h5 class="fw-bold text-dark mb-0">{{ $course->title }}</h5>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if ($isFrozen)
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 radius-8 fs-8">
                    <i class="ri-lock-2-line me-1"></i> Curriculum Frozen (Terkunci)
                </span>
            @else
                <button type="button" wire:click="openCreateChapter" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 radius-8 px-3 shadow-none">
                    <i class="ri-add-line fs-6"></i>
                    <span>Tambah Bab Baru</span>
                </button>
            @endif
        </div>
    </div>

    {{-- Banner Aturan Dicoding / Curriculum Status --}}
    @if ($isFrozen)
        <div class="alert alert-warning border border-warning-subtle shadow-sm radius-12 d-flex align-items-center gap-3 p-3 mb-24">
            <div class="flex-shrink-0 bg-warning text-white radius-8 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                <i class="ri-lock-2-fill fs-4"></i>
            </div>
            <div>
                <h6 class="fw-bold text-warning-emphasis mb-1 fs-6">Curriculum Freeze Aktif (Aturan Batch Dicoding)</h6>
                <p class="mb-0 fs-8 text-muted">
                    Pelatihan tipe Batch ini telah dimulai pada <strong>{{ $course->start_date?->format('d M Y') }}</strong>. 
                    Sesuai standar mutu pembelajaran sekuensial, struktur kurikulum (tambah/edit/hapus bab dan materi) 
                    <strong>dikunci secara otomatis</strong> demi menjaga integritas data riwayat belajar dan sertifikasi peserta ASN yang sedang aktif.
                </p>
            </div>
        </div>
    @else
        <div class="alert alert-info border border-info-subtle shadow-sm radius-12 d-flex align-items-center gap-3 p-3 mb-24">
            <div class="flex-shrink-0 bg-info text-white radius-8 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                <i class="ri-git-branch-line fs-4"></i>
            </div>
            <div>
                <h6 class="fw-bold text-info-emphasis mb-1 fs-6">Alur Silabus Sekuensial (Linear Continuity Lock)</h6>
                <p class="mb-0 fs-8 text-muted">
                    Peserta akan mempelajari materi secara berurutan. Materi ke-N baru terbuka setelah Materi ke-(N-1) ditandai selesai. 
                    Anda dapat menyusun urutan Bab dan Materi dengan menentukan nomor urut secara tepat.
                </p>
            </div>
        </div>
    @endif

    {{-- Silabus Tree / Accordion per Bab ala Dicoding --}}
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold text-dark mb-0 fs-6">
                    <i class="ri-list-ordered text-simple me-1"></i> Struktur Bab & Modul Pembelajaran
                </h6>
                <span class="text-muted fs-8">Total: <strong>{{ count($chapters) }} Bab</strong></span>
            </div>

            <div class="accordion curriculum-accordion d-flex flex-column gap-3" id="accordionCurriculum">
                @forelse ($chapters as $cIndex => $chap)
                    <div class="card border shadow-sm radius-12 overflow-hidden" wire:key="chapter-card-{{ $chap['id'] }}">
                        {{-- Chapter Header --}}
                        <div class="card-header bg-white p-16 d-flex align-items-center justify-content-between flex-wrap gap-2 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-primary text-white radius-6 px-2 py-1 font-monospace fs-8">
                                    Urutan #{{ $chap['order'] }}
                                </span>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0 fs-6">{{ $chap['title'] }}</h6>
                                    <small class="text-muted fs-9">{{ count($chap['lessons']) }} Unit Materi Pembelajaran</small>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-1">
                                @if (! $isFrozen)
                                    <a href="{{ route('materi.editor', ['course_id' => $course->id, 'chapter_id' => $chap['id']]) }}" class="btn btn-xs btn-outline-success d-inline-flex align-items-center gap-1 radius-6 px-2 py-1 fs-8 shadow-none" wire:navigate title="Tulis Materi Baru dengan Form Editor (Editor.js)">
                                        <i class="ri-edit-2-line"></i> + Tulis Materi
                                    </a>
                                    <button type="button" wire:click="openEditChapter({{ $chap['id'] }})" class="btn btn-xs btn-outline-secondary radius-6 px-2 py-1 fs-8 shadow-none" title="Edit Judul/Urutan Bab">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button type="button" wire:click="deleteChapter({{ $chap['id'] }})" wire:confirm="Apakah Anda yakin ingin menghapus bab ini beserta seluruh materinya?" class="btn btn-xs btn-outline-danger radius-6 px-2 py-1 fs-8 shadow-none" title="Hapus Bab">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                @else
                                    <span class="badge bg-light text-muted border px-2 py-1 fs-9">Terkunci</span>
                                @endif
                            </div>
                        </div>

                        {{-- Lessons List inside Chapter --}}
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse ($chap['lessons'] as $les)
                                    <div class="list-group-item p-16 d-flex align-items-center justify-content-between flex-wrap gap-2 hover-bg-light transition-all" wire:key="lesson-item-{{ $les['id'] }}">
                                        <div class="d-flex align-items-center gap-3">
                                            {{-- Content Type Icon --}}
                                            @if ($les['content_type'] === 'video')
                                                <div class="flex-shrink-0 bg-danger-subtle text-danger radius-8 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Video Pembelajaran">
                                                    <i class="ri-video-line fs-5"></i>
                                                </div>
                                            @elseif ($les['content_type'] === 'article')
                                                <div class="flex-shrink-0 bg-primary-subtle text-primary radius-8 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Artikel Naskah Editor.js">
                                                    <i class="ri-article-line fs-5"></i>
                                                </div>
                                            @else
                                                <div class="flex-shrink-0 bg-warning-subtle text-warning radius-8 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Slide Modul PDF/PPT">
                                                    <i class="ri-file-ppt-line fs-5"></i>
                                                </div>
                                            @endif

                                            <div>
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="badge bg-light text-muted border px-2 py-0 fs-9 font-monospace">
                                                        Materi {{ $chap['order'] }}.{{ $les['order'] }}
                                                    </span>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0 fs-9">
                                                        {{ $les['version'] }}
                                                    </span>
                                                    @if ($les['content_type'] === 'article')
                                                        <span class="badge bg-primary-subtle text-primary px-2 py-0 fs-9">
                                                            <i class="ri-edit-line me-1"></i>Editor.js
                                                        </span>
                                                    @endif
                                                </div>
                                                <h6 class="fw-semibold text-dark mb-0 fs-7">{{ $les['title'] }}</h6>
                                                <small class="text-muted fs-9">
                                                    Diperbarui: {{ $les['updated_at'] }}
                                                    @if (!empty($les['version_notes']))
                                                        · <em>({{ $les['version_notes'] }})</em>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>

                                        {{-- Lesson Actions --}}
                                        <div class="d-flex align-items-center gap-1">
                                            <button type="button" wire:click="previewLesson({{ $les['id'] }})" class="btn btn-sm btn-outline-info radius-6 px-2 py-1 fs-8 shadow-none" title="Pratinjau Materi Sisi Peserta">
                                                <i class="ri-eye-line me-1"></i> Preview
                                            </button>
                                            @if (! $isFrozen)
                                                <a href="{{ route('materi.editor', ['course_id' => $course->id, 'lesson_id' => $les['id']]) }}" class="btn btn-sm btn-outline-secondary radius-6 px-2 py-1 fs-8 shadow-none" wire:navigate title="Edit Materi dengan Form Editor (Editor.js)">
                                                    <i class="ri-pencil-line"></i>
                                                </a>
                                                <button type="button" wire:click="deleteLesson({{ $chap['id'] }}, {{ $les['id'] }})" wire:confirm="Hapus materi pembelajaran ini?" class="btn btn-sm btn-outline-danger radius-6 px-2 py-1 fs-8 shadow-none" title="Hapus Materi">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-20 text-center text-muted fs-8">
                                        <i class="ri-inbox-line fs-3 d-block text-muted mb-1"></i>
                                        Belum ada materi di dalam bab ini.
                                        @if (! $isFrozen)
                                            <br><a href="{{ route('materi.editor', ['course_id' => $course->id, 'chapter_id' => $chap['id']]) }}" class="btn btn-sm btn-link text-simple p-0 fs-8" wire:navigate>+ Tulis materi pertama dengan Form Editor (Editor.js)</a>
                                        @endif
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 shadow-sm radius-12 p-40 text-center">
                        <i class="ri-book-2-line text-muted display-4 mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1">Kurikulum Belum Memiliki Bab</h6>
                        <p class="text-muted fs-8 mb-3">Mulai buat struktur kurikulum pelatihan dengan menambahkan Bab pertama.</p>
                        @if (! $isFrozen)
                            <div>
                                <button type="button" wire:click="openCreateChapter" class="btn btn-sm btn-primary radius-8 px-3">
                                    <i class="ri-add-line me-1"></i> Tambah Bab Pertama
                                </button>
                            </div>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Sidebar Info Kursus & Ringkasan --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm radius-12 mb-20">
                <div class="card-header bg-white p-16 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 fs-7">
                        <i class="ri-information-line text-simple me-1"></i> Ringkasan Silabus Pelatihan
                    </h6>
                </div>
                <div class="card-body p-16">
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 fs-8">
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Kategori:</span>
                            <strong class="text-dark">{{ $course->category?->name ?? 'Umum' }}</strong>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Model Pembelajaran:</span>
                            <span class="badge {{ $course->isBatch() ? 'bg-warning-subtle text-warning' : 'bg-info-subtle text-info' }}">
                                {{ $course->isBatch() ? 'Batch (Angkatan)' : 'Permanen' }}
                            </span>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Metode Pelaksanaan:</span>
                            <strong class="text-dark text-capitalize">{{ $course->method ?? 'Daring' }}</strong>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Total Bab:</span>
                            <strong class="text-dark">{{ count($chapters) }} Bab</strong>
                        </li>
                        <li class="d-flex justify-content-between py-1 border-bottom">
                            <span class="text-muted">Total Materi:</span>
                            @php
                                $totalLessons = array_sum(array_map(fn($c) => count($c['lessons']), $chapters));
                            @endphp
                            <strong class="text-dark">{{ $totalLessons }} Modul</strong>
                        </li>
                        <li class="d-flex justify-content-between py-1">
                            <span class="text-muted">Widyaiswara / Mentor:</span>
                            @php
                                $mentors = $course->schedules->pluck('mentor.name')->filter()->unique();
                            @endphp
                            <strong class="text-dark">{{ $mentors->isNotEmpty() ? $mentors->implode(', ') : 'Belum Ditugaskan' }}</strong>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Panduan Editor.js & Dicoding --}}
            <div class="card border-0 shadow-sm radius-12 bg-light">
                <div class="card-body p-16 fs-8 text-muted">
                    <div class="d-flex align-items-center gap-2 text-dark fw-bold mb-2">
                        <i class="ri-lightbulb-line text-warning fs-5"></i>
                        <span>Tips Kurikulum ala Dicoding</span>
                    </div>
                    <ul class="ps-3 mb-0 d-flex flex-column gap-1">
                        <li>Gunakan <strong>Nomor Urut</strong> untuk menyusun gembok belajar sekuensial.</li>
                        <li>Format <strong>Editor.js</strong> menghasilkan blok teks bersih, responsif, dan mudah dibaca peserta melalui smartphone maupun desktop.</li>
                        <li>Tingkatkan <strong>Versi Modul</strong> (misal `Versi 1.1`) saat memperbarui materi agar ada jejak audit yang transparan bagi LAN dan Inspektorat.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH / EDIT BAB --}}
    @if ($showChapterModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" wire:keydown.escape="$set('showChapterModal', false)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content radius-16 border-0 shadow">
                    <div class="modal-header border-bottom p-20">
                        <h6 class="modal-title fw-bold text-dark fs-6">
                            {{ $chapterForm['id'] ? 'Edit Bab Kurikulum' : 'Tambah Bab Baru' }}
                        </h6>
                        <button type="button" class="btn-close shadow-none" wire:click="$set('showChapterModal', false)"></button>
                    </div>
                    <form wire:submit.prevent="saveChapter">
                        <div class="modal-body p-20">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark fs-8">Nomor Urut Bab <span class="text-danger">*</span></label>
                                <input type="number" min="1" wire:model="chapterForm.order" class="form-control form-control-sm radius-8" placeholder="Contoh: 1">
                                @error('chapterForm.order') <small class="text-danger fs-9">{{ $message }}</small> @enderror
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-semibold text-dark fs-8">Judul Bab Pembelajaran <span class="text-danger">*</span></label>
                                <input type="text" wire:model="chapterForm.title" class="form-control form-control-sm radius-8" placeholder="Contoh: Bab 1: Dasar Regulasi & Pengantar">
                                @error('chapterForm.title') <small class="text-danger fs-9">{{ $message }}</small> @enderror
                            </div>
                        </div>
                        <div class="modal-footer border-top p-16">
                            <button type="button" class="btn btn-sm btn-light border radius-8" wire:click="$set('showChapterModal', false)">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary radius-8 px-3">
                                <i class="ri-check-line me-1"></i> Simpan Bab
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif



    {{-- MODAL PREVIEW MATERI SISI PESERTA --}}
    @if ($showPreviewModal && $previewLesson)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6);" wire:keydown.escape="$set('showPreviewModal', false)">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content radius-16 border-0 shadow">
                    <div class="modal-header border-bottom p-20 bg-light d-flex align-items-center justify-content-between">
                        <div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0 fs-9 mb-1">
                                Simulasi Tampilan Sisi Peserta
                            </span>
                            <h6 class="modal-title fw-bold text-dark fs-6 mb-0">{{ $previewLesson['title'] }}</h6>
                        </div>
                        <button type="button" class="btn-close shadow-none" wire:click="$set('showPreviewModal', false)"></button>
                    </div>

                    <div class="modal-body p-24">
                        <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom text-muted fs-8">
                            <span><i class="ri-shield-check-line text-success me-1"></i>{{ $previewLesson['version'] }}</span>
                            <span>Terakhir diperbarui: {{ $previewLesson['updated_at'] }}</span>
                        </div>

                        {{-- Tampilan Sesuai Jenis Konten --}}
                        @if ($previewLesson['content_type'] === 'video')
                            <div class="ratio ratio-16x9 radius-12 overflow-hidden bg-dark mb-3">
                                <div class="d-flex flex-column align-items-center justify-content-center text-white p-4">
                                    <i class="ri-play-circle-fill display-3 text-danger mb-2"></i>
                                    <h6>Video Pembelajaran Tersemat</h6>
                                    <small class="text-light-emphasis">{{ $previewLesson['video_url'] ?? 'Tautan YouTube' }}</small>
                                </div>
                            </div>
                        @elseif ($previewLesson['content_type'] === 'document')
                            <div class="p-24 border radius-12 bg-light text-center mb-3">
                                <i class="ri-file-ppt-line display-4 text-warning mb-2"></i>
                                <h6 class="fw-bold text-dark mb-1">Slide Tayang Modul Materi</h6>
                                <p class="text-muted fs-8 mb-3">Berkas materi resmi dapat diunduh oleh peserta untuk dipelajari secara mandiri.</p>
                                <button type="button" class="btn btn-sm btn-outline-primary radius-8 px-3">
                                    <i class="ri-download-line me-1"></i> Unduh Slide Modul (PDF/PPT)
                                </button>
                            </div>
                        @endif

                        {{-- Naskah Bacaan --}}
                        <div class="p-3 border radius-12 bg-white">
                            <h6 class="fw-bold text-dark mb-2 fs-7">Uraian Materi:</h6>
                            <div class="text-dark fs-8 lh-lg">
                                {!! nl2br(e($previewLesson['body_text'] ?? 'Materi bacaan pembelajaran belum diisi.')) !!}
                            </div>
                        </div>

                        {{-- Simulasi Tombol Selesai ala Dicoding --}}
                        <div class="mt-24 pt-16 border-top d-flex align-items-center justify-content-between">
                            <span class="text-muted fs-8">
                                <i class="ri-lock-line me-1"></i> Materi berikutnya terkunci hingga materi ini diselesaikan.
                            </span>
                            <button type="button" class="btn btn-sm btn-success radius-8 px-3 disabled" title="Simulasi saja">
                                <i class="ri-checkbox-circle-line me-1"></i> Tandai Selesai & Lanjut
                            </button>
                        </div>
                    </div>

                    <div class="modal-footer border-top p-16">
                        <button type="button" class="btn btn-sm btn-light border radius-8" wire:click="$set('showPreviewModal', false)">Tutup Pratinjau</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

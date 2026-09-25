<div>
    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Beranda Eksekutif</h5>
            <p class="text-muted mb-0">Ringkasan strategis dan capaian pengembangan kompetensi ASN Kabupaten Aceh Timur.</p>
        </div>
    </div>

    {{-- Placeholder Card --}}
    <div class="card simpel-card border-0 shadow-sm radius-16 p-40 text-center bg-white">
        <div class="py-4">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px;">
                <i class="ri-dashboard-3-line fs-1"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">Beranda Eksekutif Pimpinan</h5>
            <p class="text-muted fs-7 mx-auto mb-4" style="max-width: 520px;">
                Modul ini sedang disiapkan untuk menyajikan ringkasan metrik eksekutif, grafik partisipasi per OPD, tingkat kelulusan, dan status pelatihan aktif secara terpusat.
            </p>
            <a href="{{ route('pimpinan.persetujuan.data') }}" class="btn btn-primary px-4 rounded-pill" wire:navigate>
                <i class="ri-checkbox-circle-line me-1"></i> Buka Antrean Persetujuan Rencana
            </a>
        </div>
    </div>
</div>

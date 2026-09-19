<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div>
    <aside class="sidebar">
        <button type="button" class="sidebar-close-btn">
            <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
        </button>
        <div class="brand-container">
            <a href="{{ route('dashboard') }}" class="brand text-decoration-none">
                <div class="seal">S</div>
                <div>
                    <strong>SIMPEL</strong>
                    <small>BKPSDM Aceh Timur</small>
                </div>
            </a>
        </div>
        <div class="sidebar-menu-area">
            <ul class="sidebar-menu" id="sidebar-menu">
                <li class="sidebar-menu-group-title">Siklus Pelatihan</li>
                <li class="active-page">
                    <a href="{{ route('dashboard') }}" class="active" title="Beranda">
                        <iconify-icon icon="solar:home-2-outline" class="menu-icon"></iconify-icon>
                        <span>Beranda</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" title="Perencanaan">
                        <iconify-icon icon="solar:clipboard-list-outline" class="menu-icon"></iconify-icon>
                        <span>Perencanaan</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" title="Pendaftaran">
                        <iconify-icon icon="solar:user-plus-rounded-outline" class="menu-icon"></iconify-icon>
                        <span>Pendaftaran</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" title="Verifikasi">
                        <iconify-icon icon="solar:check-read-outline" class="menu-icon"></iconify-icon>
                        <span>Verifikasi</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" title="Penjadwalan">
                        <iconify-icon icon="solar:calendar-date-outline" class="menu-icon"></iconify-icon>
                        <span>Penjadwalan</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" title="Absensi Elektronik">
                        <iconify-icon icon="solar:clock-circle-outline" class="menu-icon"></iconify-icon>
                        <span>Absensi Elektronik</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" title="Ruang Materi">
                        <iconify-icon icon="solar:book-bookmark-outline" class="menu-icon"></iconify-icon>
                        <span>Ruang Materi</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" title="Evaluasi & Kuis">
                        <iconify-icon icon="solar:star-fall-minimalistic-2-outline" class="menu-icon"></iconify-icon>
                        <span>Evaluasi & Kuis</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" title="Sertifikat & Pelaporan">
                        <iconify-icon icon="solar:diploma-verified-outline" class="menu-icon"></iconify-icon>
                        <span>Sertifikat & Pelaporan</span>
                    </a>
                </li>

                {{-- <li class="sidebar-menu-group-title">Master Data</li>
                <li>
                    <a href="javascript:void(0)" title="Kategori Diklat">
                        <iconify-icon icon="solar:widget-2-outline" class="menu-icon"></iconify-icon>
                        <span>Kategori Diklat</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" title="Kelola Pengguna">
                        <iconify-icon icon="solar:users-group-rounded-outline" class="menu-icon"></iconify-icon>
                        <span>Kelola Pengguna</span>
                    </a>
                </li> --}}
            </ul>
        </div>
        <div class="sidebar-foot">
            <div class="small motto-text">
                Aksi Perubahan Kinerja 2026<br>
                <strong class="text-light-emphasis">Efektif · Efisien · Transparan · Akuntabel</strong>
            </div>
        </div>
    </aside>
</div>

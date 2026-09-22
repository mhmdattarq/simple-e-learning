<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div wire:key="admin-sidebar-root">
    <aside class="sidebar">
            <button type="button" class="sidebar-close-btn">
                <i class="ri-close-line"></i>
            </button>
            <div class="brand-container">
                <a href="{{ route('admin.dashboard') }}" class="brand text-decoration-none">
                    <div class="seal">S</div>
                    <div>
                        <strong>SIMPEL</strong>
                        <small>BKPSDM Aceh Timur</small>
                    </div>
                </a>
            </div>
            <div class="sidebar-menu-area">
                <ul class="sidebar-menu" id="sidebar-menu">
                    <li class="sidebar-menu-group-title">MENU UTAMA</li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                            href="{{ route('admin.dashboard') }}" title="Beranda" wire:navigate>
                            <i class="ri-home-2-line menu-icon"></i>
                            <span>Beranda</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('perencanaan*') ? 'active' : '' }}"
                            href="{{ route('perencanaan.data') }}" title="Perencanaan" wire:navigate>
                            <i class="ri-file-list-3-line menu-icon"></i>
                            <span>Perencanaan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('pendaftaran*') ? 'active' : '' }}"
                            href="{{ route('pendaftaran.data') }}" title="Pendaftaran" wire:navigate>
                            <i class="ri-user-add-line menu-icon"></i>
                            <span>Pendaftaran</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" title="Verifikasi">
                            <i class="ri-checkbox-circle-line menu-icon"></i>
                            <span>Verifikasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" title="Penjadwalan">
                            <i class="ri-calendar-event-line menu-icon"></i>
                            <span>Penjadwalan</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" title="Absensi Elektronik">
                            <i class="ri-time-line menu-icon"></i>
                            <span>Absensi Elektronik</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" title="Ruang Materi">
                            <i class="ri-book-read-line menu-icon"></i>
                            <span>Ruang Materi</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" title="Evaluasi & Kuis">
                            <i class="ri-star-smile-line menu-icon"></i>
                            <span>Evaluasi & Kuis</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" title="Sertifikat & Pelaporan">
                            <i class="ri-award-line menu-icon"></i>
                            <span>Sertifikat & Pelaporan</span>
                        </a>
                    </li>

                    {{-- <li class="sidebar-menu-group-title">Master Data</li>
                <li>
                    <a href="javascript:void(0)" title="Kategori Diklat">
                        <i class="ri-apps-line menu-icon"></i>
                        <span>Kategori Diklat</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)" title="Kelola Pengguna">
                        <i class="ri-team-line menu-icon"></i>
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
</div>

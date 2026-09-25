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
                <a href="{{ auth()->user()?->isPimpinan() ? route('pimpinan.persetujuan.data') : route('admin.dashboard') }}" class="brand text-decoration-none">
                    <div class="seal">S</div>
                    <div>
                        <strong>SIMPEL</strong>
                        <small>BKPSDM Aceh Timur</small>
                    </div>
                </a>
            </div>
            <div class="sidebar-menu-area">
                <ul class="sidebar-menu" id="sidebar-menu">
                    @if (auth()->user()?->isPimpinan())
                        {{-- Menu Khusus Pimpinan (Eksekutif) --}}
                        <li class="sidebar-menu-group-title">MENU EKSEKUTIF</li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pimpinan.dashboard') ? 'active' : '' }}"
                                href="{{ route('pimpinan.dashboard') }}" title="Beranda Eksekutif" wire:navigate>
                                <i class="ri-dashboard-3-line menu-icon"></i>
                                <span>Beranda Eksekutif</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pimpinan.persetujuan*') ? 'active' : '' }}"
                                href="{{ route('pimpinan.persetujuan.data') }}" title="Persetujuan Rencana" wire:navigate>
                                <i class="ri-checkbox-circle-line menu-icon"></i>
                                <span>Persetujuan Rencana</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pimpinan.laporan*') ? 'active' : '' }}"
                                href="{{ route('pimpinan.laporan.data') }}" title="Laporan & Rekapitulasi" wire:navigate>
                                <i class="ri-file-chart-line menu-icon"></i>
                                <span>Laporan & Rekap</span>
                            </a>
                        </li>
                    @else
                        {{-- Menu Operasional Admin / Verifikator / Mentor --}}
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
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('verifikasi*') ? 'active' : '' }}"
                                href="{{ route('verifikasi.data') }}" title="Verifikasi" wire:navigate>
                                <i class="ri-checkbox-circle-line menu-icon"></i>
                                <span>Verifikasi</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('penjadwalan*') ? 'active' : '' }}"
                                href="{{ route('penjadwalan.data') }}" title="Penjadwalan" wire:navigate>
                                <i class="ri-calendar-event-line menu-icon"></i>
                                <span>Penjadwalan</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('absensi*') ? 'active' : '' }}"
                                href="{{ route('absensi.data') }}" title="Absensi Elektronik" wire:navigate>
                                <i class="ri-time-line menu-icon"></i>
                                <span>Absensi Elektronik</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('materi*') ? 'active' : '' }}"
                                href="{{ route('materi.data') }}" title="Ruang Materi" wire:navigate>
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

                        @if (auth()->user()?->isAdmin())
                            <li class="sidebar-menu-group-title">EKSEKUTIF</li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pimpinan.persetujuan*') ? 'active' : '' }}"
                                    href="{{ route('pimpinan.persetujuan.data') }}" title="Persetujuan Rencana" wire:navigate>
                                    <i class="ri-checkbox-circle-line menu-icon"></i>
                                    <span>Persetujuan Rencana</span>
                                </a>
                            </li>
                        @endif
                    @endif
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

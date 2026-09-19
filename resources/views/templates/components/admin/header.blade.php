<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div>
    <div class="navbar-header">
        <div class="row align-items-center justify-content-between">
            <div class="col-auto">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="d-none d-md-flex align-items-center gap-2">
                        <span class="simpel-badge simpel-badge-gold">BKPSDM Aceh Timur</span>
                        <span class="text-secondary-light small d-none d-lg-inline">Sistem Informasi Manajemen
                            Pelatihan</span>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    {{-- Search --}}
                    <form class="navbar-search d-none d-sm-block">
                        <input type="text" name="search" placeholder="Cari pelatihan, peserta, berkas...">
                        <i class="ri-search-line icon"></i>
                    </form>

                    {{-- Notifikasi --}}
                    <div class="dropdown">
                        <button
                            class="has-indicator w-40-px h-40-px bg-neutral-100 rounded-circle d-flex justify-content-center align-items-center"
                            type="button" data-bs-toggle="dropdown">
                            <i class="ri-notification-3-line icon text-xl text-primary-light"></i>
                            <span
                                class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                        </button>
                        <div class="dropdown-menu to-top dropdown-menu-lg p-0" style="width: 320px;">
                            <div class="py-12 px-16 border-bottom d-flex align-items-center justify-content-between"
                                style="background-color: #071a33; border-radius: 8px 8px 0 0;">
                                <h6 class="text-white fw-semibold mb-0 fs-6">Notifikasi SIMPEL</h6>
                                <span class="badge bg-warning text-dark">3 Baru</span>
                            </div>
                            <div class="p-2">
                                <a href="javascript:void(0)"
                                    class="dropdown-item p-10 rounded d-flex gap-2 border-bottom">
                                    <span
                                        class="w-36-px h-36-px rounded-circle d-flex justify-content-center align-items-center bg-warning-subtle text-warning flex-shrink-0">
                                        <i class="ri-user-add-fill"></i>
                                    </span>
                                    <div>
                                        <p class="mb-0 fw-semibold text-xs text-dark">Pendaftaran Baru: Nur Aini</p>
                                        <small class="text-muted" style="font-size: 11px;">Pelatihan Manajemen
                                            Administrator · 5m lalu</small>
                                    </div>
                                </a>
                                <a href="javascript:void(0)"
                                    class="dropdown-item p-10 rounded d-flex gap-2 border-bottom">
                                    <span
                                        class="w-36-px h-36-px rounded-circle d-flex justify-content-center align-items-center bg-success-subtle text-success flex-shrink-0">
                                        <i class="ri-checkbox-circle-fill"></i>
                                    </span>
                                    <div>
                                        <p class="mb-0 fw-semibold text-xs text-dark">Verifikasi Selesai: Fauzan</p>
                                        <small class="text-muted" style="font-size: 11px;">Berkas pendaftaran telah
                                            disetujui · 1j lalu</small>
                                    </div>
                                </a>
                                <a href="javascript:void(0)" class="dropdown-item p-10 rounded d-flex gap-2">
                                    <span
                                        class="w-36-px h-36-px rounded-circle d-flex justify-content-center align-items-center bg-info-subtle text-info flex-shrink-0">
                                        <i class="ri-award-fill"></i>
                                    </span>
                                    <div>
                                        <p class="mb-0 fw-semibold text-xs text-dark">Sertifikat Terbit: 12 Peserta</p>
                                        <small class="text-muted" style="font-size: 11px;">Pelatihan Pengelolaan
                                            Keuangan · 3j lalu</small>
                                    </div>
                                </a>
                            </div>
                            <div class="text-center py-2 border-top">
                                <a href="javascript:void(0)" class="fw-semibold text-xs text-primary">Lihat Semua
                                    Notifikasi</a>
                            </div>
                        </div>
                    </div>

                    {{-- User Profile --}}
                    <div class="dropdown">
                        <button class="d-flex align-items-center gap-2 border-0 bg-transparent p-0" type="button"
                            data-bs-toggle="dropdown">
                            <div class="sidebar-brand-seal"
                                style="width: 38px; height: 38px; font-size: 15px; border-radius: 10px;">
                                MS
                            </div>
                            <div class="d-none d-lg-flex flex-column text-start">
                                <span class="fw-bold text-dark fs-6" style="line-height: 1.2;">M. Suryasyah</span>
                                <small class="text-secondary-light" style="font-size: 11px;">Admin Diklat</small>
                            </div>
                            <i class="ri-arrow-down-s-line text-secondary-light d-none d-lg-block"></i>
                        </button>
                        <div class="dropdown-menu to-top dropdown-menu-sm">
                            <div class="py-12 px-16 radius-8 mb-12" style="background: #071a33; color: #fff;">
                                <h6 class="text-white fw-semibold mb-1" style="font-size: 14px;">Muhammad Suryasyah</h6>
                                <span class="badge"
                                    style="background: #f3bc42; color: #071a33; font-weight: 700;">Administrator
                                    Diklat</span>
                            </div>
                            <ul class="to-top-list list-unstyled p-0 m-0">
                                <li>
                                    <a class="dropdown-item text-black px-12 py-8 hover-text-primary d-flex align-items-center gap-2 rounded"
                                        href="javascript:void(0)">
                                        <i class="ri-user-line icon text-lg"></i>
                                        Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-black px-12 py-8 hover-text-primary d-flex align-items-center gap-2 rounded"
                                        href="javascript:void(0)">
                                        <i class="ri-settings-3-line icon text-lg"></i>
                                        Pengaturan Akun
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger px-12 py-8 hover-text-danger d-flex align-items-center gap-2 rounded"
                                        href="javascript:void(0)">
                                        <i class="ri-logout-box-r-line icon text-lg"></i> Keluar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

use Livewire\Component;

new class extends Component {
    public function logout()
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('landing');
    }
};
?>

<div>
    <header class="main-header">
        <nav class="main-menu">
            <div class="main-menu__wrapper">
                <div class="container">
                    <div class="main-menu__wrapper-inner">
                        <div class="main-menu__left">
                            <div class="main-menu__logo">
                                <a href="{{ route('landing') }}" class="brand text-decoration-none">
                                    <div class="seal">S</div>
                                    <div>
                                        <strong>SIMPEL</strong>
                                        <small>BKPSDM Aceh Timur</small>
                                    </div>
                                </a>
                            </div>

                        </div>
                        <div class="main-menu__main-menu-box">
                            <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                            <ul class="main-menu__list">
                                <li class="{{ request()->routeIs('landing') ? 'current' : '' }}">
                                    <a href="{{ route('landing') }}">Beranda</a>
                                </li>
                                <li class="{{ request()->routeIs('landing.kelas.batch') ? 'current' : '' }}">
                                    <a href="{{ route('landing.kelas.batch') }}">Kelas Batch</a>
                                </li>
                                <li class="{{ request()->routeIs('landing.kelas.permanen') ? 'current' : '' }}">
                                    <a href="{{ route('landing.kelas.permanen') }}">Kelas Permanen</a>
                                </li>
                                <li class="{{ request()->routeIs('landing.kelas.berbayar') ? 'current' : '' }}">
                                    <a href="{{ route('landing.kelas.berbayar') }}">Kelas Berbayar</a>
                                </li>
                                <li class="{{ request()->is('kontak*') ? 'current' : '' }}">
                                    <a href="javascript:void(0)" title="Segera Hadir">Kontak</a>
                                </li>
                            </ul>
                        </div>
                        <div class="main-menu__right">
                            <div class="main-menu__search-cart-box">
                                <div class="main-menu__search-box">
                                    <a href="#" class="main-menu__search searcher-toggler-box icon-search"></a>
                                </div>
                            </div>
                            @guest
                                <div class="main-menu__btn-boxes">
                                    <div class="main-menu__btn-box-1">
                                        <a href="{{ route('login') }}" class="thm-btn">Masuk</a>
                                    </div>
                                    <div class="main-menu__btn-box-2">
                                        <a href="{{ route('register') }}" class="thm-btn">Daftar</a>
                                    </div>
                                </div>
                            @else
                                <div class="dropdown">
                                    <button
                                        class="d-flex align-items-center gap-2 border-0 bg-transparent p-0 text-decoration-none"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                        style="cursor: pointer;">
                                        @if (auth()->user()?->avatar_url)
                                            <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle shadow-sm"
                                                style="width: 38px; height: 38px; object-fit: cover; border: 2px solid #f3bc42;">
                                        @else
                                            <div class="seal"
                                                style="width: 38px !important; height: 38px !important; font-size: 15px !important; border-radius: 10px !important;">
                                                {{ auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'MS' }}
                                            </div>
                                        @endif
                                        <div class="d-none d-lg-flex flex-column text-start">
                                            <span class="fw-bold text-dark fs-6"
                                                style="line-height: 1.2;">{{ auth()->user()->name }}</span>
                                            <small class="text-secondary d-flex align-items-center gap-1"
                                                style="font-size: 11px;">
                                                {{ auth()->user()?->role?->label() ?? 'Peserta' }}
                                                @if (! auth()->user()?->isAsnProfileComplete())
                                                    <span class="badge rounded-pill bg-warning text-dark p-0 px-1" style="font-size: 9px;">!</span>
                                                @endif
                                            </small>
                                        </div>
                                        <i class="ri-arrow-down-s-line text-secondary d-none d-lg-block"
                                            style="font-size: 18px;"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0"
                                        style="min-width: 260px; border-radius: 12px; overflow: hidden; margin-top: 10px; z-index: 1050;">
                                        <div class="py-3 px-3" style="background: #071a33; color: #fff;">
                                            <h6 class="text-white fw-semibold mb-1"
                                                style="font-size: 14px; line-height: 1.3;">{{ auth()->user()->name }}</h6>
                                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                                <span class="badge"
                                                    style="background: #f3bc42; color: #071a33; font-weight: 700; font-size: 11px;">{{ auth()->user()?->role?->label() ?? 'Peserta' }}</span>
                                                @if (auth()->user()->nip)
                                                    <small class="text-white-50 font-monospace" style="font-size: 11px;">·
                                                        {{ auth()->user()->nip }}</small>
                                                @endif
                                            </div>
                                            @if (! auth()->user()?->isAsnProfileComplete())
                                                <div class="mt-2 pt-2 border-top border-white border-opacity-10">
                                                    <a href="{{ route('peserta.profil') }}" class="badge bg-warning text-dark text-decoration-none d-inline-flex align-items-center gap-1 w-100 py-1_5 justify-content-center" style="font-size: 10.5px;">
                                                        <i class="ri-alert-fill"></i> Lengkapi Data ASN Anda
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <ul class="list-unstyled p-2 m-0">
                                            @if (auth()->user()->hasAdminAccess())
                                                <li>
                                                    <a class="dropdown-item px-3 py-2 text-dark d-flex align-items-center gap-2 rounded"
                                                        href="{{ route('admin.dashboard') }}" style="font-size: 13.5px;">
                                                        <i class="ri-dashboard-line text-primary"
                                                            style="font-size: 16px;"></i>
                                                        Panel Manajemen
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item px-3 py-2 text-dark d-flex align-items-center gap-2 rounded"
                                                    href="{{ route('peserta.profil') }}" style="font-size: 13.5px;">
                                                    <i class="ri-user-line text-primary" style="font-size: 16px;"></i>
                                                    Profil Saya
                                                    @if (! auth()->user()?->isAsnProfileComplete())
                                                        <span class="badge bg-warning text-dark ms-auto" style="font-size: 10px;">Lengkapi</span>
                                                    @endif
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item px-3 py-2 text-dark d-flex align-items-center gap-2 rounded"
                                                    href="javascript:void(0)" style="font-size: 13.5px;">
                                                    <i class="ri-settings-3-line text-muted" style="font-size: 16px;"></i>
                                                    Pengaturan Akun
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider my-1">
                                            </li>
                                            <li>
                                                <button type="button"
                                                    class="dropdown-item text-danger px-3 py-2 d-flex align-items-center gap-2 rounded border-0 bg-transparent w-100 text-start"
                                                    style="font-size: 13.5px; cursor: pointer;" data-bs-toggle="modal"
                                                    data-bs-target="#modalLogout">
                                                    <i class="ri-logout-box-r-line" style="font-size: 16px;"></i> Keluar
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <div class="stricky-header stricked-menu main-menu">
        <div class="sticky-header__content"></div><!-- /.sticky-header__content -->
    </div><!-- /.stricky-header -->
</div>

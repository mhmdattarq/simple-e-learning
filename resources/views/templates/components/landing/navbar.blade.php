<?php

use App\Models\Course;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public function logout()
    {
        $user = auth()->user();
        if ($user instanceof \App\Models\User) {
            \App\Repositories\EvaluasiRepo::finalizeActiveSessionsForUser($user);
        }

        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('landing');
    }

    #[Computed]
    public function activeMenu(): string
    {
        // 1. Direct class routes (Catalog, Detail, Materi, Evaluasi)
        if (request()->routeIs(['landing.kelas.bulanan*', 'landing.kelas.batch*', 'peserta.materi.bulanan*', 'peserta.materi.batch*', 'peserta.evaluasi.kerjakan.bulanan*', 'peserta.evaluasi.kerjakan.batch*']) || request()->is('bulanan*') || request()->is('kelas-batch*') || request()->routeIs('jadwal*') || request()->is('jadwal*')) {
            return 'bulanan';
        }

        if (request()->routeIs(['landing.kelas.bangkom_external*', 'landing.kelas.berbayar*', 'peserta.materi.bangkom_external*', 'peserta.materi.berbayar*', 'peserta.evaluasi.kerjakan.bangkom_external*', 'peserta.evaluasi.kerjakan.berbayar*']) || request()->is('bangkom-external*') || request()->is('kelas-berbayar*')) {
            return 'bangkom_external';
        }

        if (request()->routeIs(['landing.kelas.bangkom*', 'landing.kelas.permanen*', 'peserta.materi.bangkom*', 'peserta.materi.permanen*', 'peserta.evaluasi.kerjakan.bangkom*', 'peserta.evaluasi.kerjakan.permanen*']) || request()->is(['bangkom', 'bangkom/*']) || request()->is('kelas-permanen*')) {
            return 'bangkom';
        }

        // 2. Query param ?type=... (e.g. /kelas?type=bulanan)
        $queryType = request()->query('type');
        if (in_array($queryType, ['bulanan', 'batch'], true)) {
            return 'bulanan';
        }
        if (in_array($queryType, ['bangkom', 'permanent'], true)) {
            return 'bangkom';
        }
        if (in_array($queryType, ['bangkom_external', 'paid', 'berbayar'], true)) {
            return 'bangkom_external';
        }

        // 3. Detail, Materi, or Evaluasi page with generic /kelas/{course} pattern
        $courseParam = request()->route('course') ?? request()->route('id');
        if (! $courseParam && request()->is('kelas/*')) {
            $courseParam = request()->segment(2);
        }

        if ($courseParam) {
            $course = $courseParam instanceof Course
                ? $courseParam
                : Course::where('slug', $courseParam)->orWhere(fn ($q) => is_numeric($courseParam) ? $q->where('id', (int) $courseParam) : null)->first();

            if ($course) {
                if (in_array($course->type, ['bulanan', 'batch'], true)) {
                    return 'bulanan';
                }
                if (in_array($course->type, ['bangkom', 'permanent'], true)) {
                    return 'bangkom';
                }
                if (in_array($course->type, ['bangkom_external', 'paid', 'berbayar'], true)) {
                    return 'bangkom_external';
                }
            }
        }

        // 4. Standalone evaluasi: /evaluasi/kerjakan/{quiz}
        if (request()->routeIs('peserta.evaluasi.*') || request()->is('evaluasi/*')) {
            $quizParam = request()->route('quiz') ?? request()->route('quiz_id') ?? request()->segment(3);
            if ($quizParam) {
                $quiz = $quizParam instanceof \App\Models\Quiz
                    ? $quizParam
                    : \App\Models\Quiz::with('course')
                        ->where('slug', $quizParam)
                        ->orWhere(fn ($q) => is_numeric($quizParam) ? $q->where('id', (int) $quizParam) : null)
                        ->first();

                $type = $quiz?->course?->type;
                if (in_array($type, ['bulanan', 'batch'], true)) {
                    return 'bulanan';
                }
                if (in_array($type, ['bangkom', 'permanent'], true)) {
                    return 'bangkom';
                }
                if (in_array($type, ['bangkom_external', 'paid', 'berbayar'], true)) {
                    return 'bangkom_external';
                }
            }
        }

        // 5. Beranda
        if (request()->routeIs('landing') && ! request()->is('kelas*')) {
            return 'beranda';
        }

        // 6. Kontak
        if (request()->routeIs('kontak') || request()->is('kontak*') || request()->is('contact*')) {
            return 'kontak';
        }

        return '';
    }
};
?>

@php
    $activeMenu = $this->activeMenu;
@endphp

<div>
    <header class="main-header">
        <nav class="main-menu">
            <div class="main-menu__wrapper">
                <div class="container">
                    <div class="main-menu__wrapper-inner">
                        <div class="main-menu__left">
                            <div class="main-menu__logo">
                                <a href="{{ route('landing') }}" class="brand text-decoration-none">
                                    <img src="{{ asset('mine/logo_aceh_timur.webp') }}" alt="Logo SIMPEL BKPSDM" style="width: 44px; height: 44px; object-fit: contain; flex-shrink: 0;">
                                    <div>
                                        <strong>SIMPEL</strong>
                                        <small>BKPSDM Aceh Timur</small>
                                    </div>
                                </a>
                            </div>

                        </div>
                        <div class="main-menu__main-menu-box d-flex align-items-center">
                            @guest
                                <a href="{{ route('login') }}" class="btn btn-sm btn-simple-gold d-inline-flex d-xl-none align-items-center py-1 px-3 me-2 fw-bold text-decoration-none" style="font-size: 12.5px; border-radius: 8px;">
                                    Masuk
                                </a>
                            @endguest
                            <button type="button" class="mobile-nav__toggler d-xl-none d-flex align-items-center justify-content-center p-2 text-dark text-decoration-none border-0 bg-transparent shadow-none" aria-label="Buka Menu Navigasi" style="font-size: 24px; cursor: pointer;" onclick="toggleMobileDrawer(event)">
                                <i class="ri-menu-line"></i>
                            </button>
                            <ul class="main-menu__list">
                                <li class="{{ $activeMenu === 'beranda' ? 'current' : '' }}">
                                    <a href="{{ route('landing') }}">Beranda</a>
                                </li>
                                <li class="{{ $activeMenu === 'bulanan' ? 'current' : '' }}">
                                    <a href="{{ route('landing.kelas.bulanan') }}">Bulanan</a>
                                </li>
                                <li class="{{ $activeMenu === 'bangkom' ? 'current' : '' }}">
                                    <a href="{{ route('landing.kelas.bangkom') }}">Bangkom</a>
                                </li>
                                <li class="{{ $activeMenu === 'bangkom_external' ? 'current' : '' }}">
                                    <a href="{{ route('landing.kelas.bangkom_external') }}">Bangkom External</a>
                                </li>
                                <li class="{{ $activeMenu === 'kontak' ? 'current' : '' }}">
                                    <a href="{{ route('kontak') }}">Kontak</a>
                                </li>
                            </ul>
                        </div>
                        <div class="main-menu__right">
                            {{-- Search Box (Disembunyikan sementara) --}}
                            <div class="main-menu__search-cart-box d-none" style="display: none !important;">
                                <div class="main-menu__search-box">
                                    <a href="#" class="main-menu__search searcher-toggler-box icon-search"></a>
                                </div>
                            </div>
                            @guest
                                <div class="main-menu__btn-boxes d-none d-xl-flex">
                                    <div class="main-menu__btn-box-1">
                                        <a href="{{ route('login') }}" class="thm-btn">Masuk</a>
                                    </div>
                                    <div class="main-menu__btn-box-2">
                                        <a href="{{ route('register') }}" class="thm-btn">Daftar</a>
                                    </div>
                                </div>
                            @else
                                <div class="dropdown position-relative" x-data="{ open: false }" @click.outside="open = false">
                                    <button
                                        class="d-flex align-items-center gap-2 border-0 bg-transparent p-0 text-decoration-none"
                                        type="button"
                                        @click="open = !open"
                                        :aria-expanded="open.toString()"
                                        data-bs-toggle="dropdown" aria-expanded="false"
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
                                                @if (! auth()->user()?->isProfileComplete())
                                                    <span class="badge rounded-pill bg-warning text-dark p-0 px-1" style="font-size: 9px;">!</span>
                                                @endif
                                            </small>
                                        </div>
                                        <i class="ri-arrow-down-s-line text-secondary d-none d-lg-block"
                                            :style="open ? 'transform: rotate(180deg); transition: transform 0.2s;' : 'transition: transform 0.2s;'"
                                            style="font-size: 18px;"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0"
                                        x-show="open" x-cloak
                                        :class="{ 'show': open }"
                                        style="min-width: 260px; border-radius: 12px; overflow: hidden; margin-top: 10px; z-index: 1050; position: absolute; right: 0; left: auto !important; top: 100%;">
                                        <div class="py-3 px-3" style="background: #071a33; color: #fff;">
                                            <h6 class="text-white fw-semibold mb-1"
                                                style="font-size: 14px; line-height: 1.3;">{{ auth()->user()->name }}</h6>
                                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                                <span class="badge"
                                                    style="background: #f3bc42; color: #071a33; font-weight: 700; font-size: 11px;">{{ auth()->user()?->role?->label() ?? 'Peserta' }}</span>
                                                @if (auth()->user()->phone_number)
                                                    <small class="text-white-50 font-monospace" style="font-size: 11px;">·
                                                        {{ auth()->user()->phone_number }}</small>
                                                @endif
                                            </div>
                                            @if (! auth()->user()?->isProfileComplete())
                                                <div class="mt-2 pt-2 border-top border-white border-opacity-10">
                                                    <a href="{{ route('peserta.profil') }}" class="badge bg-warning text-dark text-decoration-none d-inline-flex align-items-center gap-1 w-100 py-1_5 justify-content-center" style="font-size: 10.5px;">
                                                        <i class="ri-alert-fill"></i> Lengkapi Profil Anda
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
                                                    @if (! auth()->user()?->isProfileComplete())
                                                        <span class="badge bg-warning text-dark ms-auto" style="font-size: 10px;">Lengkapi</span>
                                                    @endif
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

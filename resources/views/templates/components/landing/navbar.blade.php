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
                                <li class="{{ request()->routeIs('jadwal') ? 'current' : '' }}">
                                    <a href="{{ route('jadwal') }}">Jadwal</a>
                                </li>
                                <li class="{{ request()->routeIs('pelatihan.index') ? 'current' : '' }}">
                                    <a href="{{ route('pelatihan.index') }}">Katalog Pelatihan</a>
                                </li>
                                <li class="{{ request()->is('alur-pendaftaran*') ? 'current' : '' }}">
                                    <a href="#">Alur Pendaftaran</a>
                                </li>
                                {{-- <li>
                                    <a href="about.html">About</a>
                                </li> --}}
                                {{-- <li class="dropdown">
                                    <a href="#">Jadwal</a>
                                    <ul class="shadow-box">
                                        <li><a href="instructor.html">Instructors</a></li>
                                        <li><a href="instructor-carousel.html">Instructor Carousel</a></li>
                                        <li><a href="instructor-details.html">Instructor Details</a></li>
                                        <li><a href="events.html">Events</a></li>
                                        <li><a href="events-carousel.html">Event Carousel</a></li>
                                        <li><a href="event-details.html">Event Details</a></li>
                                        <li><a href="become-a-teacher.html">Become A Teacher</a></li>
                                        <li><a href="testimonials.html">Testimonials</a></li>
                                        <li><a href="testimonials-carousel.html">Testimonial Carousel</a></li>
                                        <li><a href="pricing.html">Pricing</a></li>
                                        <li><a href="gallery.html">Gallery</a></li>
                                        <li><a href="faq.html">FAQs</a></li>
                                        <li><a href="404.html">404 Error</a></li>
                                        <li><a href="coming-soon.html">Coming Soon</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a href="#">Course</a>
                                    <ul class="shadow-box">
                                        <li><a href="course.html">Course</a></li>
                                        <li><a href="course-carousel.html">Course Carousel</a></li>
                                        <li><a href="course-list.html">Course List</a></li>
                                        <li><a href="course-details.html">Course Details</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a href="#">Shop</a>
                                    <ul class="shadow-box">
                                        <li><a href="products.html">Products</a></li>
                                        <li><a href="product-details.html">Product Details</a></li>
                                        <li><a href="cart.html">Cart</a></li>
                                        <li><a href="checkout.html">Checkout</a></li>
                                        <li><a href="wishlist.html">Wishlist</a></li>
                                        <li><a href="sign-up.html">Sign Up</a></li>
                                        <li><a href="login.html">Login</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a href="#">Blog</a>
                                    <ul class="shadow-box">
                                        <li><a href="blog.html">Blog</a></li>
                                        <li><a href="blog-carousel.html">Blog Carousel</a></li>
                                        <li><a href="blog-list.html">Blog List</a></li>
                                        <li><a href="blog-details.html">Blog Details</a></li>
                                    </ul>
                                </li> --}}
                                <li class="{{ request()->is('kontak*') ? 'current' : '' }}">
                                    <a href="#">Kontak</a>
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
                                        <div class="seal"
                                            style="width: 38px !important; height: 38px !important; font-size: 15px !important; border-radius: 10px !important;">
                                            {{ auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'MS' }}
                                        </div>
                                        <div class="d-none d-lg-flex flex-column text-start">
                                            <span class="fw-bold text-dark fs-6"
                                                style="line-height: 1.2;">{{ auth()->user()->name }}</span>
                                            <small class="text-secondary"
                                                style="font-size: 11px;">{{ auth()->user()?->role?->label() ?? 'Peserta' }}</small>
                                        </div>
                                        <i class="ri-arrow-down-s-line text-secondary d-none d-lg-block"
                                            style="font-size: 18px;"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0"
                                        style="min-width: 250px; border-radius: 12px; overflow: hidden; margin-top: 10px; z-index: 1050;">
                                        <div class="py-3 px-3" style="background: #071a33; color: #fff;">
                                            <h6 class="text-white fw-semibold mb-1"
                                                style="font-size: 14px; line-height: 1.3;">{{ auth()->user()->name }}</h6>
                                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                                <span class="badge"
                                                    style="background: #f3bc42; color: #071a33; font-weight: 700; font-size: 11px;">{{ auth()->user()?->role?->label() ?? 'Peserta' }}</span>
                                                @if (auth()->user()->nip)
                                                    <small class="text-white-50" style="font-size: 11px;">·
                                                        {{ auth()->user()->nip }}</small>
                                                @endif
                                            </div>
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
                                                    href="{{ route('presensi.index') }}" style="font-size: 13.5px;">
                                                    <i class="ri-qr-code-line text-success" style="font-size: 16px;"></i>
                                                    Presensi Pelatihan
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item px-3 py-2 text-dark d-flex align-items-center gap-2 rounded"
                                                    href="javascript:void(0)" style="font-size: 13.5px;">
                                                    <i class="ri-user-line text-muted" style="font-size: 16px;"></i>
                                                    Profil Saya
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

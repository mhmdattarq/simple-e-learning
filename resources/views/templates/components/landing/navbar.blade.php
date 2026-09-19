<?php

use Livewire\Component;

new class extends Component {
    //
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
                            <div class="main-menu__category-box">
                                <div class="main-menu__category-btn">
                                    <i class="fas fa-th"></i>
                                    <p>Kategori Pelatihan</p>
                                    <span class="icon-down-arrow"></span>
                                </div>
                                <ul class="list-unstyled main-menu__category-sub-menu">
                                    <li>
                                        <a href="#">
                                            <div class="main-menu__category-icon">
                                                <img src="{{ asset('landing/assets/images/icon/categoyr-two-icon-1.png') }}"
                                                    alt="">
                                            </div>
                                            <div class="main-menu__category-content">
                                                <h5>Pelatihan mandiri</h5>
                                                <p>Buka 24 Jam</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="main-menu__category-icon">
                                                <img src="{{ asset('landing/assets/images/icon/categoyr-two-icon-2.png') }}"
                                                    alt="">
                                            </div>
                                            <div class="main-menu__category-content">
                                                <h5>Batch Berkala</h5>
                                                <p>Daring Terjadwal</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="main-menu__category-icon">
                                                <img src="{{ asset('landing/assets/images/icon/categoyr-two-icon-3.png') }}"
                                                    alt="">
                                            </div>
                                            <div class="main-menu__category-content">
                                                <h5>Penugasan Khusus</h5>
                                                <p>Rekomendasi OPD</p>
                                            </div>
                                        </a>
                                    </li>
                                    {{-- <li>
                                        <a href="#">
                                            <div class="main-menu__category-icon">
                                                <img src="{{ asset('landing/assets/images/icon/categoyr-two-icon-4.png') }}"
                                                    alt="">
                                            </div>
                                            <div class="main-menu__category-content">
                                                <h5>Health & <br> Wellness</h5>
                                                <p>3+ Courses</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="main-menu__category-icon">
                                                <img src="{{ asset('landing/assets/images/icon/categoyr-two-icon-5.png') }}"
                                                    alt="">
                                            </div>
                                            <div class="main-menu__category-content">
                                                <h5>Writing & <br> Communication</h5>
                                                <p>3+ Courses</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="main-menu__category-icon">
                                                <img src="{{ asset('landing/assets/images/icon/categoyr-two-icon-6.png') }}"
                                                    alt="">
                                            </div>
                                            <div class="main-menu__category-content">
                                                <h5>User Research & <br> Analytics</h5>
                                                <p>3+ Courses</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="main-menu__category-icon">
                                                <img src="{{ asset('landing/assets/images/icon/categoyr-two-icon-7.png') }}"
                                                    alt="">
                                            </div>
                                            <div class="main-menu__category-content">
                                                <h5>Digital <br> Marketing</h5>
                                                <p>3+ Courses</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="main-menu__category-icon">
                                                <img src="{{ asset('landing/assets/images/icon/categoyr-two-icon-8.png') }}"
                                                    alt="">
                                            </div>
                                            <div class="main-menu__category-content">
                                                <h5>Lifestyle & <br> Productivity</h5>
                                                <p>3+ Courses</p>
                                            </div>
                                        </a>
                                    </li> --}}
                                </ul>
                            </div>
                        </div>
                        <div class="main-menu__main-menu-box">
                            <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                            <ul class="main-menu__list">
                                <li class="{{ request()->routeIs('landing') ? 'current' : '' }}">
                                    <a href="{{ route('landing') }}">Beranda</a>
                                </li>
                                <li class="{{ request()->is('akademi*') ? 'current' : '' }}">
                                    <a href="#">Akademi</a>
                                </li>
                                <li class="{{ request()->is('katalog-pelatihan*') || request()->is('pelatihan*') ? 'current' : '' }}">
                                    <a href="#">Katalog Pelatihan</a>
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
                            <div class="main-menu__btn-boxes">
                                <div class="main-menu__btn-box-1">
                                    <a href="{{ route('login') }}" class="thm-btn">Masuk</a>
                                </div>
                                <div class="main-menu__btn-box-2">
                                    <a href="#" class="thm-btn">Daftar</a>
                                </div>
                            </div>
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

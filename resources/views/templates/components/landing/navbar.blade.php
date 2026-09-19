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
                                <a href="index.html"><img src="{{ asset('landing/assets/images/resources/logo-1.png') }}"
                                        alt=""></a>
                            </div>
                            <div class="main-menu__category-box">
                                <div class="main-menu__category-btn">
                                    <i class="fas fa-th"></i>
                                    <p>Category</p>
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
                                                <h5>Tech & <br> Programming</h5>
                                                <p>3+ Courses</p>
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
                                                <h5>Creative <br> Art</h5>
                                                <p>3+ Courses</p>
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
                                                <h5>Business & <br> Finance</h5>
                                                <p>3+ Courses</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
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
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="main-menu__main-menu-box">
                            <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                            <ul class="main-menu__list">
                                <li class="dropdown megamenu">
                                    <a href="index.html">Home </a>
                                    <ul>
                                        <li>
                                            <section class="home-showcase">
                                                <div class="container">
                                                    <div class="home-showcase__inner">
                                                        <div class="row">
                                                            <div class="col-lg-3">
                                                                <div class="home-showcase__item">
                                                                    <div class="home-showcase__image">
                                                                        <img src="{{ asset('landing/assets/images/home-showcase/home-showcase-1-1.jpg') }}"
                                                                            alt="">
                                                                        <div class="home-showcase__buttons">
                                                                            <a href="index.html"
                                                                                class="thm-btn home-showcase__buttons__item">
                                                                                <span class="icon-angles-right"></span>
                                                                                Multi Page
                                                                            </a>
                                                                            <a href="index-one-page.html"
                                                                                class="thm-btn home-showcase__buttons__item">
                                                                                <span class="icon-angles-right"></span>
                                                                                One Page
                                                                            </a>
                                                                        </div>
                                                                        <!-- /.home-showcase__buttons -->
                                                                    </div><!-- /.home-showcase__image -->
                                                                    <h3 class="home-showcase__title">Home
                                                                        Page
                                                                        01</h3>
                                                                    <!-- /.home-showcase__title -->
                                                                </div><!-- /.home-showcase__item -->
                                                            </div><!-- /.col-lg-3 -->
                                                            <div class="col-lg-3">
                                                                <div class="home-showcase__item">
                                                                    <div class="home-showcase__image">
                                                                        <img src="{{ asset('landing/assets/images/home-showcase/home-showcase-1-2.jpg') }}"
                                                                            alt="">
                                                                        <div class="home-showcase__buttons">
                                                                            <a href="index2.html"
                                                                                class="thm-btn home-showcase__buttons__item">
                                                                                <span class="icon-angles-right"></span>
                                                                                Multi Page
                                                                            </a>
                                                                            <a href="index2-one-page.html"
                                                                                class="thm-btn home-showcase__buttons__item">
                                                                                <span class="icon-angles-right"></span>
                                                                                One Page
                                                                            </a>
                                                                        </div>
                                                                        <!-- /.home-showcase__buttons -->
                                                                    </div><!-- /.home-showcase__image -->
                                                                    <h3 class="home-showcase__title">Home
                                                                        Page
                                                                        02
                                                                    </h3><!-- /.home-showcase__title -->
                                                                </div><!-- /.home-showcase__item -->
                                                            </div><!-- /.col-lg-3 -->
                                                            <div class="col-lg-3">
                                                                <div class="home-showcase__item">
                                                                    <div class="home-showcase__image">
                                                                        <img src="{{ asset('landing/assets/images/home-showcase/home-showcase-1-3.jpg') }}"
                                                                            alt="">
                                                                        <div class="home-showcase__buttons">
                                                                            <a href="index3.html"
                                                                                class="thm-btn home-showcase__buttons__item">
                                                                                <span class="icon-angles-right"></span>
                                                                                Multi Page
                                                                            </a>
                                                                            <a href="index3-one-page.html"
                                                                                class="thm-btn home-showcase__buttons__item">
                                                                                <span class="icon-angles-right"></span>
                                                                                View Page
                                                                            </a>
                                                                        </div>
                                                                        <!-- /.home-showcase__buttons -->
                                                                    </div><!-- /.home-showcase__image -->
                                                                    <h3 class="home-showcase__title">Home
                                                                        Page
                                                                        03
                                                                    </h3><!-- /.home-showcase__title -->
                                                                </div><!-- /.home-showcase__item -->
                                                            </div><!-- /.col-lg-3 -->
                                                            <div class="col-lg-3">
                                                                <div class="home-showcase__item">
                                                                    <div class="home-showcase__image">
                                                                        <img src="{{ asset('landing/assets/images/home-showcase/home-showcase-1-4.jpg') }}"
                                                                            alt="">
                                                                        <div class="home-showcase__buttons">
                                                                            <a href="index-dark.html"
                                                                                class="thm-btn home-showcase__buttons__item">
                                                                                <span class="icon-angles-right"></span>
                                                                                One Page
                                                                            </a>
                                                                        </div>
                                                                        <!-- /.home-showcase__buttons -->
                                                                    </div><!-- /.home-showcase__image -->
                                                                    <h3 class="home-showcase__title">Home
                                                                        Page
                                                                        04
                                                                    </h3><!-- /.home-showcase__title -->
                                                                </div><!-- /.home-showcase__item -->
                                                            </div><!-- /.col-lg-3 -->
                                                        </div><!-- /.row -->
                                                    </div><!-- /.home-showcase__inner -->

                                                </div><!-- /.container -->
                                            </section>
                                        </li>
                                    </ul>
                                </li>
                                <li>
                                    <a href="about.html">About</a>
                                </li>
                                <li class="dropdown">
                                    <a href="#">Pages</a>
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
                                </li>
                                <li>
                                    <a href="contact.html">Contact</a>
                                </li>
                            </ul>
                        </div>
                        <div class="main-menu__right">
                            <div class="main-menu__search-cart-box">
                                <div class="main-menu__search-box">
                                    <a href="#" class="main-menu__search searcher-toggler-box icon-search"></a>
                                </div>
                                <div class="main-menu__cart">
                                    <a href="#"><span class="fas fa-shopping-cart"></span></a>
                                </div>
                            </div>
                            <div class="main-menu__btn-boxes">
                                <div class="main-menu__btn-box-1">
                                    <a href="#" class="thm-btn">Login</a>
                                </div>
                                <div class="main-menu__btn-box-2">
                                    <a href="#" class="thm-btn">Register</a>
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

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? config('app.name') }}</title>
    <!-- favicons Icons -->
    {{-- <link rel="apple-touch-icon" sizes="180x180"
        href="{{ asset('landing/assets/images/favicons/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32"
        href="{{ asset('landing/assets/images/favicons/favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ asset('landing/assets/images/favicons/favicon-16x16.png') }}" />
    <link rel="manifest" href="{{ asset('landing/assets/images/favicons/site.webmanifest') }}" />
    <meta name="description" content="fistudy HTML 5 Template " /> --}}

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">


    <link
        href="https://fonts.googleapis.com/css2?family=Roboto+Serif:ital,opsz,wght@0,8..144,100..900;1,8..144,100..900&display=swap"
        rel="stylesheet">


    <link rel="stylesheet" href="{{ asset('landing/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/animate.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/custom-animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/swiper.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/font-awesome-all.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/jarallax.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/jquery.magnific-popup.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/odometer.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/assets/css/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/owl.theme.default.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/nice-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/jquery-ui.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/aos.css') }}" />


    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/banner.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/slider.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/footer.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/sliding-text.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/category.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/about.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/courses.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/why-choose.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/live-class.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/video-one.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/blog.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/counter.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/team.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/newsletter.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/testimonial.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/module-css/contact.css') }}" />

    <!-- template styles -->
    <link rel="stylesheet" href="{{ asset('landing/assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/assets/css/responsive.css') }}" />
    <!-- Remix Icon from admin -->
    <link rel="stylesheet" href="{{ asset('admin/assets/css/remixicon.css') }}" />

    <!-- App Custom CSS & JS (Classic Asset) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>

    <style>
        /* Custom Button SIMPEL E-Learning */
        .btn-simpel-cta-gold {
            background-color: var(--simpel-gold) !important;
            color: var(--simpel-navy) !important;
            border: 1.5px solid var(--simpel-gold) !important;
            border-radius: 10px !important;
            font-weight: 700 !important;
            padding: 11px 18px !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            transition: all 0.25s ease-in-out !important;
            position: relative !important;
            overflow: hidden !important;
            line-height: 1.2 !important;
        }

        .btn-simpel-cta-gold:hover,
        .btn-simpel-cta-gold:focus {
            background-color: var(--simpel-navy) !important;
            border-color: var(--simpel-navy) !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(7, 26, 51, 0.2) !important;
        }

        .btn-simpel-cta-gold:hover i,
        .btn-simpel-cta-gold:hover span,
        .btn-simpel-cta-gold:focus i,
        .btn-simpel-cta-gold:focus span {
            color: #ffffff !important;
        }

        .btn-simpel-cta-gold::before {
            display: none !important;
        }

        .btn-simpel-outline-navy {
            background-color: transparent !important;
            color: var(--simpel-navy) !important;
            border: 1.5px solid var(--simpel-navy) !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            padding: 9px 18px !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            transition: all 0.2s ease-in-out !important;
            line-height: 1.2 !important;
        }

        .btn-simpel-outline-navy:hover,
        .btn-simpel-outline-navy:focus {
            background-color: var(--simpel-navy) !important;
            border-color: var(--simpel-navy) !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(7, 26, 51, 0.15) !important;
        }

        .btn-simpel-outline-navy:hover i,
        .btn-simpel-outline-navy:focus i {
            color: var(--simpel-gold) !important;
        }

        .btn-simpel-outline-light {
            background-color: transparent !important;
            color: #ffffff !important;
            border: 1.5px solid rgba(255, 255, 255, 0.35) !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            padding: 12px 24px !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            transition: all 0.25s ease-in-out !important;
            line-height: 1.2 !important;
        }

        .btn-simpel-outline-light:hover,
        .btn-simpel-outline-light:focus {
            background-color: var(--simpel-gold) !important;
            border-color: var(--simpel-gold) !important;
            color: var(--simpel-navy) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(243, 188, 66, 0.25) !important;
        }

        .btn-simpel-outline-light:hover i,
        .btn-simpel-outline-light:hover span,
        .btn-simpel-outline-light:focus i,
        .btn-simpel-outline-light:focus span {
            color: var(--simpel-navy) !important;
        }

        /* Navbar Auth Buttons Consistency */
        .main-menu__btn-box-1 .thm-btn {
            background-color: transparent !important;
            border: 1.5px solid var(--simpel-navy) !important;
            color: var(--simpel-navy) !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            padding: 7px 20px 8px !important;
            transition: all 0.25s ease-in-out !important;
            font-size: 15px !important;
        }

        .main-menu__btn-box-1 .thm-btn:hover {
            background-color: var(--simpel-navy) !important;
            border-color: var(--simpel-navy) !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(7, 26, 51, 0.15) !important;
        }

        .main-menu__btn-box-1 .thm-btn::before {
            display: none !important;
        }

        .main-menu__btn-box-2 .thm-btn {
            background-color: var(--simpel-gold) !important;
            border: 1.5px solid var(--simpel-gold) !important;
            color: var(--simpel-navy) !important;
            border-radius: 10px !important;
            font-weight: 700 !important;
            padding: 7px 22px 8px !important;
            transition: all 0.25s ease-in-out !important;
            font-size: 15px !important;
        }

        .main-menu__btn-box-2 .thm-btn:hover {
            background-color: var(--simpel-navy) !important;
            border-color: var(--simpel-navy) !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(7, 26, 51, 0.2) !important;
        }

        .main-menu__btn-box-2 .thm-btn::before {
            display: none !important;
        }

        /* Universal safety override for any thm-btn styled with gold/navy */
        .thm-btn[style*="--simpel-gold"],
        .thm-btn[style*="#f3bc42"] {
            transition: all 0.25s ease-in-out !important;
        }

        .thm-btn[style*="--simpel-gold"]::before,
        .thm-btn[style*="#f3bc42"]::before {
            display: none !important;
        }

        .thm-btn[style*="--simpel-gold"]:hover,
        .thm-btn[style*="#f3bc42"]:hover {
            background-color: var(--simpel-navy) !important;
            border-color: var(--simpel-navy) !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(7, 26, 51, 0.2) !important;
        }

        .thm-btn[style*="--simpel-gold"]:hover *,
        .thm-btn[style*="#f3bc42"]:hover * {
            color: #ffffff !important;
        }

        .mobile-nav__content .main-menu__list > li.current > a {
            color: var(--fistudy-base, #f3bc42) !important;
            font-weight: 700 !important;
        }
    </style>

    @livewireStyles
</head>

<body class="custom-cursor">
    {{-- Notifikasi Toast Universal dari Admin --}}
    <livewire:admin.toast />

    @auth
        {{-- Modal Universal & Konfirmasi Aksi --}}
        <livewire:admin.modal />
    @endauth

    <div class="page-wrapper">
        <livewire:landing.navbar />

        {{ $slot }}

        <!--Site Footer Start-->
        <livewire:landing.footer />
        <!--Site Footer End-->
    </div><!-- /.page-wrapper -->


    <div class="mobile-nav__wrapper">
        <div class="mobile-nav__overlay mobile-nav__toggler"></div>
        <!-- /.mobile-nav__overlay -->
        <div class="mobile-nav__content">
            <span class="mobile-nav__close mobile-nav__toggler"><i class="fa fa-times"></i></span>

            <div class="logo-box">
                <a href="{{ route('landing') }}" class="brand text-decoration-none">
                    <div class="seal">S</div>
                    <div>
                        <strong class="text-white">SIMPEL</strong>
                        <small class="text-white-50">BKPSDM Aceh Timur</small>
                    </div>
                </a>
            </div>
            <!-- /.logo-box -->
            <div class="mobile-nav__container"></div>
            <!-- /.mobile-nav__container -->

            <ul class="mobile-nav__contact list-unstyled">
                <li>
                    <i class="fa fa-envelope"></i>
                    <a href="mailto:needhelp@packageName__.com">needhelp@fistudy.com</a>
                </li>
                <li>
                    <i class="fas fa-phone"></i>
                    <a href="tel:666-888-0000">666 888 0000</a>
                </li>
            </ul><!-- /.mobile-nav__contact -->
            <div class="mobile-nav__top">
                <div class="mobile-nav__social">
                    <a href="#" class="fab fa-twitter"></a>
                    <a href="#" class="fab fa-facebook-square"></a>
                    <a href="#" class="fab fa-pinterest-p"></a>
                    <a href="#" class="fab fa-instagram"></a>
                </div><!-- /.mobile-nav__social -->
            </div><!-- /.mobile-nav__top -->



        </div>
        <!-- /.mobile-nav__content -->
    </div>
    <!-- /.mobile-nav__wrapper -->

    <!-- Search Popup -->
    <div class="search-popup">
        <div class="color-layer"></div>
        <button class="close-search"><span class="far fa-times fa-fw"></span></button>
        <form method="post" action="blog.html">
            <div class="form-group">
                <input type="search" name="search-field" value="" placeholder="Search Here" required="">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>
    <!-- End Search Popup -->

    <a href="#" data-target="html" class="scroll-to-target scroll-to-top">
        <span class="scroll-to-top__wrapper"><span class="scroll-to-top__inner"></span></span>
        <span class="scroll-to-top__text"> Go Back Top</span>
    </a>


    <script data-navigate-once src="{{ asset('landing/assets/js/jquery-3.6.0.min.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/jarallax.min.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/jquery.ajaxchimp.min.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/jquery.appear.min.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/swiper.min.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/jquery.magnific-popup.min.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/jquery.validate.min.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/odometer.min.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/wNumb.min.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/wow.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/isotope.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/owl.carousel.min.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/jquery-ui.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/jquery.nice-select.min.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/marquee.min.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/aos.js') }}"></script>

    <script data-navigate-once src="{{ asset('landing/assets/js/gsap/gsap.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/gsap/ScrollTrigger.js') }}"></script>
    <script data-navigate-once src="{{ asset('landing/assets/js/gsap/SplitText.js') }}"></script>

    <!-- template js -->
    <script data-navigate-once src="{{ asset('landing/assets/js/script.js') }}"></script>
    <script data-navigate-once src="{{ asset('mine/script.js') }}"></script>
    @livewireScripts
</body>

</html>

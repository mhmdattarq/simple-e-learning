<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? config('app.name') }}</title>
    <!-- favicons Icons -->
    <link rel="apple-touch-icon" sizes="180x180"
        href="{{ asset('landing/assets/images/favicons/apple-touch-icon.png') }}" />
    <link rel="icon" type="image/png" sizes="32x32"
        href="{{ asset('landing/assets/images/favicons/favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16"
        href="{{ asset('landing/assets/images/favicons/favicon-16x16.png') }}" />
    <link rel="manifest" href="{{ asset('landing/assets/images/favicons/site.webmanifest') }}" />
    <meta name="description" content="fistudy HTML 5 Template " />

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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="custom-cursor">
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
                <a href="index.html" aria-label="logo image"><img
                        src="{{ asset('landing/assets/images/resources/logo-4.png') }}" width="105"
                        alt="" /></a>
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


    <script src="{{ asset('landing/assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/jarallax.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/jquery.ajaxchimp.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/jquery.appear.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/swiper.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/odometer.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/wNumb.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/wow.js') }}"></script>
    <script src="{{ asset('landing/assets/js/isotope.js') }}"></script>
    <script src="{{ asset('landing/assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('landing/assets/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/marquee.min.js') }}"></script>
    <script src="{{ asset('landing/assets/js/aos.js') }}"></script>




    <script src="{{ asset('landing/assets/js/gsap/gsap.js') }}"></script>
    <script src="{{ asset('landing/assets/js/gsap/ScrollTrigger.js') }}"></script>
    <script src="{{ asset('landing/assets/js/gsap/SplitText.js') }}"></script>




    <!-- template js -->
    <script src="{{ asset('landing/assets/js/script.js') }}"></script>
    @livewireScripts
</body>

</html>

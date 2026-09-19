<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div>
    <footer class="site-footer">
        <div class="site-footer__shape-bg"
            style="background-image: url({{ asset('landing/assets/images/shapes/site-footer-shape-bg.png') }});"></div>
        <div class="site-footer__logo-and-contact-box">
            <div class="container">
                <div class="site-footer__logo-and-contact-box-inner">
                    <div class="site-footer__logo-box wow fadeInLeft" data-wow-delay="100ms">
                        <div class="site-footer__logo">
                            <a href="{{ route('landing') }}" class="brand text-decoration-none">
                                <div class="seal">S</div>
                                <div>
                                    <strong class="text-white">SIMPEL</strong>
                                    <small class="text-white-50">BKPSDM Aceh Timur</small>
                                </div>
                            </a>
                        </div>
                        <p class="site-footer__text">
                            Sistem Informasi Manajemen Pelatihan Elektronik (SIMPEL) ASN<br>
                            BKPSDM Pemerintah Kabupaten Aceh Timur.
                        </p>
                    </div>
                    <div class="site-footer__contact-box wow fadeInRight" data-wow-delay="100ms">
                        <a href="#pelatihan">Daftar Pelatihan</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="site-footer__top">
            <div class="container">
                <div class="site-footer__top-inner">
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 wow fadeInUp" data-wow-delay="300ms">
                            <div class="site-footer__top-left">
                                <div class="site-footer__contact-info">
                                    <ul class="list-unstyled site-footer__contact-info-list">
                                        <li>
                                            <div class="site-footer__contact-info-icon-box">
                                                <div class="site-footer__contact-info-icon">
                                                    <span class="icon-envelope"></span>
                                                    <p class="site-footer__contact-info-icon-text">Surel Resmi:
                                                    </p>
                                                </div>
                                                <p class="site-footer__contact-info-text"><a
                                                        href="mailto:bkpsdm@acehtimurkab.go.id">bkpsdm@acehtimurkab.go.id</a></p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="site-footer__contact-info-icon-box">
                                                <div class="site-footer__contact-info-icon">
                                                    <span class="icon-location"></span>
                                                    <p class="site-footer__contact-info-icon-text">Alamat Kantor:</p>
                                                </div>
                                                <p class="site-footer__contact-info-text">Pusat Pemerintahan Kab. Aceh Timur, <br> Jl. Medan - Banda Aceh, Idi Rayeuk</p>
                                            </div>
                                        </li>
                                    </ul>
                                    <ul
                                        class="list-unstyled site-footer__contact-info-list site-footer__contact-info-list--two">
                                        <li>
                                            <div class="site-footer__contact-info-icon-box">
                                                <div class="site-footer__contact-info-icon">
                                                    <span class="icon-phone"></span>
                                                    <p class="site-footer__contact-info-icon-text">Layanan Pengaduan:
                                                    </p>
                                                </div>
                                                <p class="site-footer__contact-info-text"><a
                                                        href="tel:+626467000111">(0646) 7000-111</a></p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="site-footer__contact-info-icon-box">
                                                <div class="site-footer__contact-info-icon">
                                                    <span class="icon-clock"></span>
                                                    <p class="site-footer__contact-info-icon-text">Jam Layanan:
                                                    </p>
                                                </div>
                                                <p class="site-footer__contact-info-text">Senin - Jumat <br>
                                                    08:00 - 16:30 WIB</p>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="site-footer__app-and-social-box">
                                    <div class="site-footer__app-download">
                                        <h4 class="site-footer__app-and-social-title">Akses Mandiri</h4>
                                        <div class="site-footer__app-download-inner">
                                            <a href="#pelatihan" class="site-footer__app-box">
                                                <div class="site-footer__app-icon">
                                                    <img src="{{ asset('landing/assets/images/icon/google-play-icon.png') }}"
                                                        alt="">
                                                </div>
                                            </a>
                                            <a href="#pelatihan" class="site-footer__app-box">
                                                <div class="site-footer__app-icon">
                                                    <img src="{{ asset('landing/assets/images/icon/apple-icon.png') }}"
                                                        alt="">
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="site-footer__social-box">
                                        <h4 class="site-footer__app-and-social-title">Media Sosial Resmi:</h4>
                                        <div class="site-footer__social-box-inner">
                                            <a href="javascript:void(0)"><span class="fab fa-facebook-f"></span></a>
                                            <a href="javascript:void(0)"><span class="fab fa-instagram"></span></a>
                                            <a href="javascript:void(0)"><span class="fab fa-youtube"></span></a>
                                            <a href="javascript:void(0)"><span class="fab fa-twitter"></span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 wow fadeInUp" data-wow-delay="300ms">
                            <div class="site-footer__top-right">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-6">
                                        <div class="site-footer__links">
                                            <h4 class="site-footer__title">Navigasi Utama</h4>
                                            <ul class="site-footer__links-list list-unstyled">
                                                <li><a href="{{ route('landing') }}"> <span class="icon-plus"></span> Beranda</a>
                                                </li>
                                                <li><a href="#akademi"> <span class="icon-plus"></span> Akademi
                                                        Pelatihan</a></li>
                                                <li><a href="#pelatihan"> <span class="icon-plus"></span>
                                                        Katalog Pelatihan</a></li>
                                                <li><a href="#alur-pendaftaran"> <span class="icon-plus"></span> Alur Pendaftaran</a>
                                                </li>
                                                <li><a href="{{ route('login') }}"> <span class="icon-plus"></span>
                                                        Masuk ke Portal</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6">
                                        <div class="site-footer__useful-links">
                                            <h4 class="site-footer__title">Tautan Lembaga</h4>
                                            <ul class="site-footer__links-list list-unstyled">
                                                <li><a href="https://acehtimurkab.go.id" target="_blank"> <span class="icon-plus"></span>
                                                        Pemkab Aceh Timur</a>
                                                </li>
                                                <li><a href="https://bkpsdm.acehtimurkab.go.id" target="_blank"> <span class="icon-plus"></span>
                                                        BKPSDM Aceh Timur</a></li>
                                                <li><a href="https://siasn.bkn.go.id" target="_blank"> <span class="icon-plus"></span>
                                                        Portal SIASN BKN</a></li>
                                                <li><a href="https://lan.go.id" target="_blank"> <span class="icon-plus"></span>
                                                        LAN RI</a>
                                                </li>
                                                <li><a href="https://menpan.go.id" target="_blank"> <span class="icon-plus"></span>
                                                        KemenPAN-RB</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="site-footer__newsletter-box">
                                    <h4 class="site-footer__title">Informasi Pelatihan</h4>
                                    <form class="site-footer__newsletter-form" onsubmit="event.preventDefault();">
                                        <div class="site-footer__newsletter-input">
                                            <input type="email" placeholder="Masukkan Email Kedinasan">
                                        </div>
                                        <button type="submit" class="thm-btn"> <span
                                                class="icon-angles-right"></span> Langganan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="site-footer__bottom">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="site-footer__bottom-inner">
                            <div class="site-footer__copyright">
                                <p class="site-footer__copyright-text">Hak Cipta © 2026 <a href="{{ route('landing') }}">SIMPEL</a> · BKPSDM Pemerintah Kabupaten Aceh Timur. Seluruh Hak Cipta Dilindungi.</p>
                            </div>
                            <div class="site-footer__bottom-card-box">
                                <span class="text-white-50 text-xs" style="font-size: 12px; letter-spacing: 0.5px;">
                                    Aksi Perubahan Kinerja 2026 · Efektif · Efisien · Transparan · Akuntabel
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>

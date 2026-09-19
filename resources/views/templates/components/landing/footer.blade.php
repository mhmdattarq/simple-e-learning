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
                    <div class="row gy-4">
                        {{-- Kolom Kiri: Profil & Visi SIMPEL + Media Sosial Resmi --}}
                        <div class="col-xl-5 col-lg-5 wow fadeInUp" data-wow-delay="200ms">
                            <div class="site-footer__top-left">
                                <div class="site-footer__about-box pe-xl-3">
                                    <h4 class="site-footer__title">Tentang SIMPEL</h4>
                                    <p class="text-white-80 fs-7 lh-base mb-4">
                                        Portal akselerasi kompetensi dan pembelajaran mandiri terpadu bagi seluruh Aparatur Sipil Negara di lingkungan Pemerintah Kabupaten Aceh Timur guna mewujudkan birokrasi berkelas dunia.
                                    </p>
                                    <div class="d-flex flex-column gap-2 mb-4">
                                        <div class="d-flex align-items-center gap-2 text-white-80 fs-8">
                                            <i class="ri-checkbox-circle-fill text-gold fs-6"></i>
                                            <span>Pembelajaran Mandiri, Batch Berkala & Penugasan</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 text-white-80 fs-8">
                                            <i class="ri-checkbox-circle-fill text-gold fs-6"></i>
                                            <span>Konversi Jam Pelajaran (JP) Resmi MenPAN-RB</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 text-white-80 fs-8">
                                            <i class="ri-checkbox-circle-fill text-gold fs-6"></i>
                                            <span>E-Sertifikat Terintegrasi SIASN BKN</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="site-footer__social-box mt-3">
                                    <h4 class="site-footer__app-and-social-title" style="font-size: 16px; margin-bottom: 12px;">Media Sosial Resmi:</h4>
                                    <div class="site-footer__social-box-inner">
                                        <a href="javascript:void(0)" aria-label="Facebook"><span class="fab fa-facebook-f"></span></a>
                                        <a href="javascript:void(0)" aria-label="Instagram"><span class="fab fa-instagram"></span></a>
                                        <a href="javascript:void(0)" aria-label="YouTube"><span class="fab fa-youtube"></span></a>
                                        <a href="javascript:void(0)" aria-label="Twitter"><span class="fab fa-twitter"></span></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Kolom Kanan: Menu Utama, Tautan Lembaga, & Buletin --}}
                        <div class="col-xl-7 col-lg-7 wow fadeInUp" data-wow-delay="300ms">
                            <div class="site-footer__top-right" style="margin-left: 0;">
                                <div class="row g-4">
                                    {{-- Menu Utama (5 Menu Navbar) --}}
                                    <div class="col-xl-6 col-lg-6 col-md-6">
                                        <div class="site-footer__links">
                                            <h4 class="site-footer__title">Menu Utama</h4>
                                            <ul class="site-footer__links-list list-unstyled">
                                                <li>
                                                    <a href="{{ route('landing') }}"><span class="icon-plus"></span> Beranda</a>
                                                </li>
                                                <li>
                                                    <a href="#akademi"><span class="icon-plus"></span> Akademi</a>
                                                </li>
                                                <li>
                                                    <a href="#pelatihan"><span class="icon-plus"></span> Katalog Pelatihan</a>
                                                </li>
                                                <li>
                                                    <a href="#alur-pendaftaran"><span class="icon-plus"></span> Alur Pendaftaran</a>
                                                </li>
                                                <li>
                                                    <a href="#"><span class="icon-plus"></span> Kontak</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('login') }}"><span class="icon-plus"></span> Masuk ke Akun</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    {{-- Tautan Lembaga Eksternal --}}
                                    <div class="col-xl-6 col-lg-6 col-md-6">
                                        <div class="site-footer__useful-links" style="margin-left: 0;">
                                            <h4 class="site-footer__title">Tautan Lembaga</h4>
                                            <ul class="site-footer__links-list list-unstyled">
                                                <li>
                                                    <a href="https://acehtimurkab.go.id" target="_blank"><span class="icon-plus"></span> Pemkab Aceh Timur</a>
                                                </li>
                                                <li>
                                                    <a href="https://bkpsdm.acehtimurkab.go.id" target="_blank"><span class="icon-plus"></span> BKPSDM Aceh Timur</a>
                                                </li>
                                                <li>
                                                    <a href="https://siasn.bkn.go.id" target="_blank"><span class="icon-plus"></span> Portal SIASN BKN</a>
                                                </li>
                                                <li>
                                                    <a href="https://lan.go.id" target="_blank"><span class="icon-plus"></span> LAN RI</a>
                                                </li>
                                                <li>
                                                    <a href="https://menpan.go.id" target="_blank"><span class="icon-plus"></span> KemenPAN-RB</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Newsletter Box --}}
                                <div class="site-footer__newsletter-box mt-4">
                                    <h4 class="site-footer__title mb-2">Informasi Pelatihan</h4>
                                    <p class="text-white-70 fs-8 mb-3">Dapatkan notifikasi jadwal pembukaan batch diklat dan pembaruan beasiswa kedinasan.</p>
                                    <form class="site-footer__newsletter-form" onsubmit="event.preventDefault();">
                                        <div class="site-footer__newsletter-input">
                                            <input type="email" placeholder="Masukkan Email Kedinasan">
                                        </div>
                                        <button type="submit" class="thm-btn">
                                            <span class="icon-angles-right"></span> Langganan
                                        </button>
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

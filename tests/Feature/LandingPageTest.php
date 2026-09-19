<?php

test('landing page can be accessed successfully and displays core sections', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('landing/assets/css/bootstrap.min.css');
    $response->assertSee('landing/assets/js/script.js');
    $response->assertSee('Akselerasi Kompetensi');
    $response->assertSee('Jalur Akademi Pelatihan');
    $response->assertSee('Katalog Pelatihan Digital Terbuka');
    $response->assertSee('Alur Mudah Pendaftaran');
    $response->assertSee('Pelatihan Mandiri');
    $response->assertSee('Batch Berkala');
    $response->assertSee('Penugasan Khusus');
    $response->assertSee('SIMPEL');
    $response->assertSee('BKPSDM Aceh Timur');
    $response->assertDontSee('E-Sertifikat Digital & Integrasi SIASN BKN');
});

test('admin dashboard can be accessed on /admin and /dashboard', function () {
    $responseAdmin = $this->get('/admin');
    $responseAdmin->assertStatus(200);

    $responseDashboard = $this->get('/dashboard');
    $responseDashboard->assertStatus(200);
});

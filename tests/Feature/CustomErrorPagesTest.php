<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

test('non-existent route returns 404 with custom branded error page', function () {
    $response = $this->get('/halaman-yang-pasti-tidak-ada-123456');

    $response->assertStatus(404);
    $response->assertSee('KODE ERROR 404');
    $response->assertSee('Halaman Tidak Ditemukan');
    $response->assertSee('Kembali ke Beranda');
    $response->assertSee('SIMPEL — Sistem Informasi Manajemen Pembelajaran Elektronik');
});

test('unauthorized participant accessing admin panel returns 403 with custom error page', function () {
    $peserta = User::factory()->peserta()->create();

    $response = $this->actingAs($peserta)->get(route('admin.dashboard'));

    $response->assertStatus(403);
    $response->assertSee('KODE ERROR 403');
    $response->assertSee('Akses Ditolak');
});

test('custom error view templates compile and render without database queries', function () {
    $codes = ['403', '404', '419', '500', '503'];

    foreach ($codes as $code) {
        $view = view("errors.{$code}", [
            'exception' => new HttpException((int) $code, 'Pesan uji coba error.'),
        ])->render();

        expect($view)->toContain("KODE ERROR {$code}");
        expect($view)->toContain('BKPSDM Kabupaten Aceh Timur');
    }
});

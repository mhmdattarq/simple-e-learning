<?php

use App\Enums\RegistrationStatus;
use App\Livewire\Admin\Pendaftaran\PendaftaranData;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('admin sees full operational menu in sidebar', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));
    $response->assertStatus(200);

    // Sidebar section header
    $response->assertSee('MENU UTAMA');

    // Admin menus
    $response->assertSee(route('perencanaan.data'));
    $response->assertSee(route('pendaftaran.data'));
    $response->assertSee(route('verifikasi.data'));
    $response->assertSee(route('penjadwalan.data'));
    $response->assertSee(route('absensi.data'));
    $response->assertSee(route('materi.data'));
    $response->assertSee('Evaluasi & Kuis', false);
    $response->assertSee('Sertifikat & Pelaporan', false);

    // Executive section for admin backup
    $response->assertSee('EKSEKUTIF');
    $response->assertSee(route('pimpinan.persetujuan.data'));
});

test('mentor sees only designated mentor menus in sidebar', function () {
    $mentor = User::factory()->mentor()->create();

    $response = $this->actingAs($mentor)->get(route('admin.dashboard'));
    $response->assertStatus(200);

    // Mentor header
    $response->assertSee('MENU PENGAMPU');
    $response->assertSee('Beranda Mentor / Pengampu');

    // Designated Mentor Menus
    $response->assertSee(route('penjadwalan.data'));
    $response->assertSee(route('absensi.data'));
    $response->assertSee(route('materi.data'));
    $response->assertSee('Evaluasi & Kuis', false);

    // Menus that mentor should NOT see
    $response->assertDontSee(route('perencanaan.data'));
    $response->assertDontSee(route('verifikasi.data'));
    $response->assertDontSee(route('pendaftaran.data'));
    $response->assertDontSee('Sertifikat & Pelaporan', false);
    $response->assertDontSee('EKSEKUTIF');
    $response->assertDontSee(route('pimpinan.persetujuan.data'));
});

test('verifikator sees only dashboard and pendaftaran menus in sidebar', function () {
    $verifikator = User::factory()->verifikator()->create();

    $response = $this->actingAs($verifikator)->get(route('admin.dashboard'));
    $response->assertStatus(200);

    // Verifikator header
    $response->assertSee('MENU VERIFIKATOR');
    $response->assertSee('Beranda Verifikator Berkas');

    // Designated Verifikator Menus
    $response->assertSee(route('pendaftaran.data'));

    // Menus that verifikator should NOT see
    $response->assertDontSee(route('perencanaan.data'));
    $response->assertDontSee(route('penjadwalan.data'));
    $response->assertDontSee(route('absensi.data'));
    $response->assertDontSee(route('materi.data'));
    $response->assertDontSee('Evaluasi & Kuis', false);
    $response->assertDontSee('Sertifikat & Pelaporan', false);
    $response->assertDontSee('EKSEKUTIF');
    $response->assertDontSee(route('pimpinan.persetujuan.data'));
});

test('pimpinan sees executive menu in sidebar', function () {
    $pimpinan = User::factory()->pimpinan()->create();

    $response = $this->actingAs($pimpinan)->get(route('pimpinan.dashboard'));
    $response->assertStatus(200);

    // Pimpinan header
    $response->assertSee('MENU EKSEKUTIF');
    $response->assertSee(route('pimpinan.dashboard'));
    $response->assertSee(route('pimpinan.persetujuan.data'));
    $response->assertSee(route('pimpinan.laporan.data'));

    // Non-executive menus hidden
    $response->assertDontSee(route('perencanaan.data'));
    $response->assertDontSee(route('pendaftaran.data'));
    $response->assertDontSee(route('penjadwalan.data'));
    $response->assertDontSee(route('absensi.data'));
    $response->assertDontSee(route('materi.data'));
});

test('verifikator can access pendaftaran page and view registration detail', function () {
    $verifikator = User::factory()->verifikator()->create();
    $peserta = User::factory()->peserta()->create([
        'name' => 'Fadhil Pratama, S.IP',
        'nip' => '199503122019031002',
        'opd_agency' => 'Bappeda Aceh Timur',
    ]);

    $category = Category::create(['name' => 'Teknis', 'slug' => 'teknis']);
    $course = Course::create([
        'code' => 'PLT-TEST-001',
        'title' => 'Pelatihan Perencanaan Pembangunan Daerah',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 30,
        'status' => 'approved',
    ]);

    $registration = CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $course->id,
        'registration_number' => 'REG-2026-TEST-01',
        'status' => RegistrationStatus::Pending,
        'enrolled_at' => now(),
    ]);

    // Page access
    $response = $this->actingAs($verifikator)->get(route('pendaftaran.data'));
    $response->assertStatus(200);
    $response->assertSee('Tahap 2: Pendaftaran Diklat');
    $response->assertSee('Daftar Pendaftaran Peserta (Rekapitulasi ASN)');

    // Detail modal dispatch & data loading
    Livewire::actingAs($verifikator)
        ->test(PendaftaranData::class)
        ->call('showDetail', $registration->id)
        ->assertDispatched('openModal', id: 'modalDetailPendaftaran')
        ->assertSet('selectedDetail.registration_number', 'REG-2026-TEST-01')
        ->assertSet('selectedDetail.user_name', 'Fadhil Pratama, S.IP');
});

test('verifikator cannot manage or delete registration periods', function () {
    $verifikator = User::factory()->verifikator()->create();
    $category = Category::first() ?? Category::create(['name' => 'Teknis', 'slug' => 'teknis-2']);
    $course = Course::create([
        'code' => 'PLT-TEST-002',
        'title' => 'Pelatihan Manajemen Aset',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 20,
        'status' => 'approved',
    ]);

    // Attempting openPeriod as verifikator throws authorization exception
    Livewire::actingAs($verifikator)
        ->test(PendaftaranData::class)
        ->set('selectedCourseId', $course->id)
        ->set('registration_open_at', now()->format('Y-m-d\TH:i'))
        ->set('registration_close_at', now()->addDays(7)->format('Y-m-d\TH:i'))
        ->call('openPeriod')
        ->assertForbidden();
});

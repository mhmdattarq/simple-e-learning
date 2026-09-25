<?php

use App\Enums\CourseStatus;
use App\Livewire\Pimpinan\Dashboard\DashboardIndex as PimpinanDashboard;
use App\Livewire\Pimpinan\Laporan\LaporanIndex as PimpinanLaporan;
use App\Livewire\Pimpinan\Persetujuan\PersetujuanReview;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
});

test('unauthorized users cannot access pimpinan persetujuan pages', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();
    $course = Course::create([
        'code' => 'PLT-TEST-GUARD',
        'title' => 'Pelatihan Guard Test',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => now()->addMonth()->toDateString(),
        'end_date' => now()->addMonth()->addDays(5)->toDateString(),
        'method' => 'hybrid',
        'quota' => 20,
        'status' => CourseStatus::Submitted,
        'created_by' => $admin->id,
    ]);

    // Guest redirected to login
    $this->get(route('pimpinan.persetujuan.data'))->assertRedirect(route('login'));
    $this->get(route('pimpinan.persetujuan.review', $course->id))->assertRedirect(route('login'));

    // Peserta gets 403 Forbidden
    $peserta = User::factory()->peserta()->create();
    $this->actingAs($peserta)->get(route('pimpinan.persetujuan.data'))->assertStatus(403);
    $this->actingAs($peserta)->get(route('pimpinan.persetujuan.review', $course->id))->assertStatus(403);

    // Verifikator gets 403 Forbidden
    $verifikator = User::factory()->verifikator()->create();
    $this->actingAs($verifikator)->get(route('pimpinan.persetujuan.data'))->assertStatus(403);
    $this->actingAs($verifikator)->get(route('pimpinan.persetujuan.review', $course->id))->assertStatus(403);
});

test('pimpinan can access persetujuan data and sidebar shows executive boundaries', function () {
    $pimpinan = User::factory()->pimpinan()->create();

    $response = $this->actingAs($pimpinan)->get(route('pimpinan.persetujuan.data'));

    $response->assertStatus(200);
    $response->assertSee('Persetujuan Rencana Pelatihan');
    $response->assertSee('tablePersetujuan');

    // Sidebar check: Pimpinan sees executive menu, but NOT admin operational menu
    $response->assertSee('MENU EKSEKUTIF');
    $response->assertSee('Beranda Eksekutif');
    $response->assertSee('Persetujuan Rencana');
    $response->assertSeeText('Laporan & Rekap');
    $response->assertDontSee('MENU UTAMA');
});

test('pimpinan datatable returns only submitted training courses and supports sorting and search', function () {
    $pimpinan = User::factory()->pimpinan()->create();
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    // Draft course - should NOT appear in approval table
    Course::create([
        'code' => 'PLT-DRAFT',
        'title' => 'Pelatihan Masih Draft',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => now()->addMonth()->toDateString(),
        'end_date' => now()->addMonth()->addDays(5)->toDateString(),
        'method' => 'hybrid',
        'quota' => 20,
        'status' => CourseStatus::Draft,
        'created_by' => $admin->id,
    ]);

    // Submitted course - SHOULD appear in approval table
    $submittedCourse = Course::create([
        'code' => 'PLT-SUBMITTED',
        'title' => 'Pelatihan Menunggu Persetujuan Pimpinan',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => now()->addMonth()->toDateString(),
        'end_date' => now()->addMonth()->addDays(5)->toDateString(),
        'method' => 'hybrid',
        'quota' => 30,
        'status' => CourseStatus::Submitted,
        'created_by' => $admin->id,
    ]);

    $response = $this->actingAs($pimpinan)->getJson(route('pimpinan.persetujuan.dt'));

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['code'])->toBe('PLT-SUBMITTED');
    expect($data[0]['title'])->toContain('Pelatihan Menunggu Persetujuan Pimpinan');

    // Test DataTables ordering & column parameters (preventing SQL ambiguous column regressions)
    $dtResponse = $this->actingAs($pimpinan)->getJson(route('pimpinan.persetujuan.dt', [
        'draw' => 1,
        'columns' => [
            ['data' => '', 'name' => '', 'searchable' => 'false', 'orderable' => 'false'],
            ['data' => 'id', 'name' => 'courses.id', 'searchable' => 'false', 'orderable' => 'false'],
            ['data' => 'code', 'name' => 'courses.code', 'searchable' => 'true', 'orderable' => 'true'],
            ['data' => 'title', 'name' => 'courses.title', 'searchable' => 'true', 'orderable' => 'true'],
            ['data' => 'method_badge', 'name' => 'courses.method', 'searchable' => 'false', 'orderable' => 'false'],
            ['data' => 'schedule_formatted', 'name' => 'courses.start_date', 'searchable' => 'false', 'orderable' => 'true'],
            ['data' => 'creator_name', 'name' => 'courses.updated_at', 'searchable' => 'false', 'orderable' => 'true'],
            ['data' => 'status_badge', 'name' => 'courses.status', 'searchable' => 'false', 'orderable' => 'false'],
        ],
        'order' => [
            ['column' => 6, 'dir' => 'desc'],
        ],
        'start' => 0,
        'length' => 25,
        'search' => ['value' => 'Menunggu', 'regex' => 'false'],
    ]));

    $dtResponse->assertOk();
    expect($dtResponse->json('recordsTotal'))->toBe(1);
    expect($dtResponse->json('recordsFiltered'))->toBe(1);
});

test('pimpinan can view complete training plan details on the review page', function () {
    $pimpinan = User::factory()->pimpinan()->create();
    $admin = User::factory()->admin()->create(['name' => 'Admin Pengusul BKPSDM']);
    $category = Category::first();

    $course = Course::create([
        'code' => 'PLT-VIEW-DETAIL',
        'title' => 'Bimtek Pengelolaan Keuangan Daerah',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => now()->addMonth()->toDateString(),
        'end_date' => now()->addMonth()->addDays(4)->toDateString(),
        'method' => 'luring',
        'location' => 'Aula Bappeda Aceh Timur',
        'quota' => 40,
        'target_audience' => 'Bendahara & PPTK Perangkat Daerah',
        'budget_source' => 'DPA-BKPSDM TA 2026',
        'description' => 'Pelatihan intensif tata kelola keuangan berbasis SIPD.',
        'competencies' => 'Penguasaan pelaporan SPJ dan penatausahaan kas.',
        'status' => CourseStatus::Submitted,
        'created_by' => $admin->id,
    ]);

    $response = $this->actingAs($pimpinan)->get(route('pimpinan.persetujuan.review', $course->id));

    $response->assertOk();
    $response->assertSee('PLT-VIEW-DETAIL');
    $response->assertSee('Bimtek Pengelolaan Keuangan Daerah');
    $response->assertSee($category->name);
    $response->assertSee('Aula Bappeda Aceh Timur');
    $response->assertSee('40 Orang Peserta ASN');
    $response->assertSee('Bendahara & PPTK Perangkat Daerah');
    $response->assertSee('DPA-BKPSDM TA 2026');
    $response->assertSee('Pelatihan intensif tata kelola keuangan berbasis SIPD.');
    $response->assertSee('Penguasaan pelaporan SPJ dan penatausahaan kas.');
    $response->assertSee('Admin Pengusul BKPSDM');
    $response->assertSee('Lembar Keputusan Pimpinan');
    $response->assertSee('Setujui Rencana Pelatihan');
    $response->assertSee('Kembalikan untuk Revisi');
});

test('pimpinan can approve training plan on the review page and transitions status to approved', function () {
    $pimpinan = User::factory()->pimpinan()->create(['name' => 'Kepala BKPSDM Aceh Timur']);
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course = Course::create([
        'code' => 'PLT-APPROVE-PAGE',
        'title' => 'Pelatihan Kepemimpinan Administrator',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => now()->addMonth()->toDateString(),
        'end_date' => now()->addMonth()->addDays(5)->toDateString(),
        'method' => 'hybrid',
        'quota' => 30,
        'target_audience' => 'Pejabat Administrator Kab. Aceh Timur',
        'budget_source' => 'DPA-BKPSDM 2026',
        'status' => CourseStatus::Submitted,
        'created_by' => $admin->id,
    ]);

    Livewire::actingAs($pimpinan)
        ->test(PersetujuanReview::class, ['id' => $course->id])
        ->assertSee('Pelatihan Kepemimpinan Administrator')
        ->set('decisionNotes', 'Disetujui untuk dilaksanakan sesuai jadwal anggaran DPA 2026.')
        ->call('approve')
        ->assertRedirect(route('pimpinan.persetujuan.data'))
        ->assertSessionHas('alert-show', [
            'type' => 'success',
            'title' => 'Rencana Pelatihan Disetujui',
            'message' => 'Rencana pelatihan PLT-APPROVE-PAGE berhasil disetujui.',
        ]);

    $course->refresh();
    expect($course->status)->toBe(CourseStatus::Approved);
    expect($course->approved_by)->toBe($pimpinan->id);
    expect($course->approved_at)->not->toBeNull();
    expect($course->approval_notes)->toBe('Disetujui untuk dilaksanakan sesuai jadwal anggaran DPA 2026.');
});

test('pimpinan returning plan for revision on the review page requires notes and transitions back to draft', function () {
    $pimpinan = User::factory()->pimpinan()->create();
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course = Course::create([
        'code' => 'PLT-REJECT-PAGE',
        'title' => 'Pelatihan Pengadaan Barang dan Jasa',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => now()->addMonth()->toDateString(),
        'end_date' => now()->addMonth()->addDays(5)->toDateString(),
        'method' => 'hybrid',
        'quota' => 25,
        'status' => CourseStatus::Submitted,
        'created_by' => $admin->id,
    ]);

    // 1. Rejection without notes fails validation
    Livewire::actingAs($pimpinan)
        ->test(PersetujuanReview::class, ['id' => $course->id])
        ->set('decisionNotes', '')
        ->call('reject')
        ->assertHasErrors(['decisionNotes' => 'required']);

    // 2. Rejection with notes under 5 chars fails validation
    Livewire::actingAs($pimpinan)
        ->test(PersetujuanReview::class, ['id' => $course->id])
        ->set('decisionNotes', 'cek')
        ->call('reject')
        ->assertHasErrors(['decisionNotes' => 'min']);

    // 3. Rejection with valid notes succeeds
    Livewire::actingAs($pimpinan)
        ->test(PersetujuanReview::class, ['id' => $course->id])
        ->set('decisionNotes', 'Mohon perbaiki rincian standar biaya dan tanggal pelaksanaan.')
        ->call('reject')
        ->assertHasNoErrors()
        ->assertRedirect(route('pimpinan.persetujuan.data'))
        ->assertSessionHas('alert-show', [
            'type' => 'warning',
            'title' => 'Usulan Dikembalikan untuk Revisi',
            'message' => 'Rencana pelatihan PLT-REJECT-PAGE telah dikembalikan ke status Draft untuk direvisi.',
        ]);

    $course->refresh();
    expect($course->status)->toBe(CourseStatus::Draft);
    expect($course->approval_notes)->toBe('Mohon perbaiki rincian standar biaya dan tanggal pelaksanaan.');
});

test('cannot approve or reject course that is not in submitted status', function () {
    $pimpinan = User::factory()->pimpinan()->create();
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    // Already approved course
    $approvedCourse = Course::create([
        'code' => 'PLT-ALREADY-APPROVED',
        'title' => 'Pelatihan Sudah Disetujui',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => now()->addMonth()->toDateString(),
        'end_date' => now()->addMonth()->addDays(5)->toDateString(),
        'method' => 'hybrid',
        'quota' => 30,
        'status' => CourseStatus::Approved,
        'created_by' => $admin->id,
    ]);

    // Attempting to approve an already approved course fails policy authorization
    Livewire::actingAs($pimpinan)
        ->test(PersetujuanReview::class, ['id' => $approvedCourse->id])
        ->call('approve')
        ->assertForbidden();
});

test('pimpinan dashboard and laporan placeholder pages are accessible', function () {
    $pimpinan = User::factory()->pimpinan()->create();

    $this->actingAs($pimpinan)->get(route('pimpinan.dashboard'))->assertOk();
    $this->actingAs($pimpinan)->get(route('pimpinan.laporan.data'))->assertOk();

    Livewire::actingAs($pimpinan)
        ->test(PimpinanDashboard::class)
        ->assertOk()
        ->assertSee('Beranda Eksekutif Pimpinan');

    Livewire::actingAs($pimpinan)
        ->test(PimpinanLaporan::class)
        ->assertOk()
        ->assertSee('Laporan Eksekutif');
});

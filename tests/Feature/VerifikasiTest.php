<?php

use App\Enums\RegistrationStatus;
use App\Livewire\Admin\Verifikasi\VerifikasiData;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\User;
use App\Repositories\VerifikasiRepo;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
    $this->category = Category::first();
    $this->course = Course::create([
        'code' => 'TIK-2026-001',
        'title' => 'Digital Leadership & Tata Kelola SPBE',
        'category_id' => $this->category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 30,
        'status' => 'published',
    ]);
});

test('unauthorized users cannot access verifikasi routes', function () {
    // Guest
    $this->get(route('verifikasi.data'))
        ->assertRedirect(route('login'));

    // Peserta
    $peserta = User::factory()->peserta()->create();
    $this->actingAs($peserta)
        ->get(route('verifikasi.data'))
        ->assertStatus(403);
});

test('verifikator and admin can access verifikasi index page', function () {
    $verifikator = User::factory()->verifikator()->create();

    $this->actingAs($verifikator)
        ->get(route('verifikasi.data'))
        ->assertStatus(200)
        ->assertSee('Verifikasi Berkas Pelatihan ASN')
        ->assertSee('Tahap 3')
        ->assertSee('Daftar Verifikasi Berkas Peserta');

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('verifikasi.data'))
        ->assertStatus(200);
});

test('verifikasi datatable endpoint returns valid yajra json response', function () {
    $verifikator = User::factory()->verifikator()->create();
    $peserta = User::factory()->peserta()->create([
        'name' => 'Fauzan Akbar',
        'nip' => '199508172020121002',
    ]);

    CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-202609-0001',
        'status' => RegistrationStatus::Pending,
        'enrolled_at' => now(),
    ]);

    $this->actingAs($verifikator);

    $response = $this->getJson(route('verifikasi.dt'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'registration_number',
                    'user_name',
                    'user_nip',
                    'course_title',
                    'status_badge',
                    'enrolled_at_formatted',
                ],
            ],
        ])
        ->assertJsonFragment([
            'registration_number' => 'REG-202609-0001',
            'user_name' => 'Fauzan Akbar',
            'user_nip' => '199508172020121002',
        ]);
});

test('verifikator can approve registration with status verified and audit log recorded', function () {
    $verifikator = User::factory()->verifikator()->create(['name' => 'Budi Verifikator']);
    $peserta = User::factory()->peserta()->create(['name' => 'Siti Aminah']);

    $reg = CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-202609-0010',
        'status' => RegistrationStatus::Pending,
        'enrolled_at' => now(),
    ]);

    $this->actingAs($verifikator);

    Livewire::test(VerifikasiData::class)
        ->call('openVerifyModal', $reg->id)
        ->assertSet('verifyForm.id', $reg->id)
        ->assertSet('verifyForm.status', 'verified')
        ->set('verifyForm.status', 'verified')
        ->set('verifyForm.verification_notes', 'Berkas usulan lengkap dan sesuai persyaratan.')
        ->call('submitVerification')
        ->assertHasNoErrors()
        ->assertDispatched('closeModal', id: 'modalVerifikasiAction')
        ->assertDispatched('reloadDT')
        ->assertDispatched('alert-show', function ($event, $params) {
            return $params['data']['type'] === 'success';
        });

    $updated = $reg->fresh();
    expect($updated->status)->toBe(RegistrationStatus::Verified);
    expect($updated->verified_by)->toBe($verifikator->id);
    expect($updated->verified_at)->not->toBeNull();
    expect($updated->verification_notes)->toBe('Berkas usulan lengkap dan sesuai persyaratan.');
    expect($updated->verifier->name)->toBe('Budi Verifikator');
});

test('verifikator can request revision with status revision_required and mandatory notes', function () {
    $verifikator = User::factory()->verifikator()->create();
    $peserta = User::factory()->peserta()->create();

    $reg = CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-202609-0011',
        'status' => RegistrationStatus::Pending,
        'enrolled_at' => now(),
    ]);

    $this->actingAs($verifikator);

    // Validation should fail if notes are empty when requesting revision
    Livewire::test(VerifikasiData::class)
        ->call('openVerifyModal', $reg->id)
        ->set('verifyForm.status', 'revision_required')
        ->set('verifyForm.verification_notes', '')
        ->call('submitVerification')
        ->assertHasErrors(['verifyForm.verification_notes']);

    // Now supply notes
    Livewire::test(VerifikasiData::class)
        ->call('openVerifyModal', $reg->id)
        ->set('verifyForm.status', 'revision_required')
        ->set('verifyForm.verification_notes', 'Surat rekomendasi belum distempel basah instansi.')
        ->call('submitVerification')
        ->assertHasNoErrors()
        ->assertDispatched('closeModal', id: 'modalVerifikasiAction');

    $updated = $reg->fresh();
    expect($updated->status)->toBe(RegistrationStatus::RevisionRequired);
    expect($updated->verification_notes)->toBe('Surat rekomendasi belum distempel basah instansi.');
    expect($updated->verified_by)->toBe($verifikator->id);
});

test('verifikator can reject registration with status rejected and mandatory notes', function () {
    $verifikator = User::factory()->verifikator()->create();
    $peserta = User::factory()->peserta()->create();

    $reg = CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-202609-0012',
        'status' => RegistrationStatus::Pending,
        'enrolled_at' => now(),
    ]);

    $this->actingAs($verifikator);

    // Validation should fail if notes are empty when rejecting
    Livewire::test(VerifikasiData::class)
        ->call('openVerifyModal', $reg->id)
        ->set('verifyForm.status', 'rejected')
        ->set('verifyForm.verification_notes', '')
        ->call('submitVerification')
        ->assertHasErrors(['verifyForm.verification_notes']);

    // Now supply notes
    Livewire::test(VerifikasiData::class)
        ->call('openVerifyModal', $reg->id)
        ->set('verifyForm.status', 'rejected')
        ->set('verifyForm.verification_notes', 'Kuota peserta pelatihan untuk OPD terkait telah terpenuhi.')
        ->call('submitVerification')
        ->assertHasNoErrors()
        ->assertDispatched('closeModal', id: 'modalVerifikasiAction');

    $updated = $reg->fresh();
    expect($updated->status)->toBe(RegistrationStatus::Rejected);
    expect($updated->verification_notes)->toBe('Kuota peserta pelatihan untuk OPD terkait telah terpenuhi.');
});

test('verifikasi detail modal loads correctly with verifier information', function () {
    $verifikator = User::factory()->verifikator()->create(['name' => 'Ahmad Verifikator']);
    $peserta = User::factory()->peserta()->create(['name' => 'Teuku Umar']);

    $reg = CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-202609-0099',
        'status' => RegistrationStatus::Verified,
        'verified_by' => $verifikator->id,
        'verified_at' => now(),
        'verification_notes' => 'Disetujui tanpa catatan.',
        'enrolled_at' => now(),
    ]);

    $this->actingAs($verifikator);

    Livewire::test(VerifikasiData::class)
        ->call('showDetail', $reg->id)
        ->assertSet('selectedDetail.registration_number', 'REG-202609-0099')
        ->assertSet('selectedDetail.verifier_name', 'Ahmad Verifikator')
        ->assertSet('selectedDetail.verification_notes', 'Disetujui tanpa catatan.')
        ->assertDispatched('openModal', id: 'modalDetailVerifikasi');
});

test('verifikasi repository getStats aggregates status counts correctly', function () {
    $peserta = User::factory()->peserta()->create();

    CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-S1',
        'status' => RegistrationStatus::Pending,
        'enrolled_at' => now(),
    ]);

    $course2 = Course::create([
        'code' => 'TIK-2026-002',
        'title' => 'Pelatihan Kedua',
        'category_id' => $this->category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 20,
        'status' => 'published',
    ]);
    CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $course2->id,
        'registration_number' => 'REG-S2',
        'status' => RegistrationStatus::Verified,
        'enrolled_at' => now(),
    ]);

    $stats = VerifikasiRepo::getStats();

    expect($stats['total'])->toBe(2);
    expect($stats['pending'])->toBe(1);
    expect($stats['verified'])->toBe(1);
    expect($stats['revision_required'])->toBe(0);
    expect($stats['rejected'])->toBe(0);
});

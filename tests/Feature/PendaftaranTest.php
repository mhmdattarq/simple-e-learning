<?php

use App\Enums\RegistrationStatus;
use App\Livewire\Admin\Pendaftaran\PendaftaranData;
use App\Livewire\Peserta\Pendaftaran\PendaftaranCreate;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
    Storage::fake('public');
});

test('unauthorized users cannot access admin pendaftaran routes', function () {
    // Guest redirected to login
    $this->get(route('pendaftaran.data'))->assertRedirect(route('login'));

    // Peserta gets 403 Forbidden
    $peserta = User::factory()->peserta()->create();
    $this->actingAs($peserta)->get(route('pendaftaran.data'))->assertStatus(403);
});

test('guest is redirected to login when accessing course registration form', function () {
    $category = Category::first();
    $course = Course::create([
        'code' => 'PLT-REG-001',
        'title' => 'Pelatihan Uji Coba Pendaftaran',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 30,
        'status' => 'published',
    ]);

    $this->get(route('pelatihan.daftar', $course->id))->assertRedirect(route('login'));
});

test('authorized internal roles can access pendaftaran index page', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('pendaftaran.data'));

    $response->assertStatus(200);
    $response->assertSee('Tahap 2: Pendaftaran Diklat');
    $response->assertSee('tablePendaftaran');

    Livewire::actingAs($admin)
        ->test(PendaftaranData::class)
        ->assertOk()
        ->assertSee('Daftar Pendaftaran Peserta (Rekapitulasi ASN)');
});

test('pendaftaran datatable endpoint returns valid yajra json response', function () {
    $admin = User::factory()->admin()->create();
    $peserta = User::factory()->peserta()->create([
        'name' => 'Ahmad Dahlan, S.STP',
        'nip' => '199001012015011001',
        'opd_agency' => 'BKPSDM Kab. Aceh Timur',
    ]);

    $category = Category::first();
    $course = Course::create([
        'code' => 'PLT-2026-002',
        'title' => 'Pelatihan Kepemimpinan Pengawas',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => '2026-10-01',
        'end_date' => '2026-10-15',
        'method' => 'hybrid',
        'quota' => 25,
        'status' => 'published',
    ]);

    CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $course->id,
        'registration_number' => 'REG-202609-0001',
        'status' => RegistrationStatus::Pending,
        'enrolled_at' => now(),
    ]);

    $response = $this->actingAs($admin)->getJson(route('pendaftaran.dt'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'draw',
        'recordsTotal',
        'recordsFiltered',
        'data',
    ]);

    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['registration_number'])->toBe('REG-202609-0001');
    expect($data[0]['user_name'])->toBe('Ahmad Dahlan, S.STP');
    expect($data[0]['course_code'])->toBe('PLT-2026-002');
});

test('peserta registration validates required fields, NIP 18 digits, agreement and PDF file', function () {
    $peserta = User::factory()->peserta()->create([
        'nip' => null,
    ]);

    $category = Category::first();
    $course = Course::create([
        'code' => 'PLT-2026-003',
        'title' => 'Pelatihan Manajemen Risiko SPBE',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 40,
        'status' => 'published',
    ]);

    Livewire::actingAs($peserta)
        ->test(PendaftaranCreate::class, ['id' => $course->id])
        ->set('form.nip', '123') // Invalid NIP
        ->set('form.name', '')
        ->set('form.opd_agency', '')
        ->set('form.agreement', false)
        ->call('submit')
        ->assertHasErrors([
            'form.nip',
            'form.name',
            'form.opd_agency',
            'form.agreement',
            'recommendationLetter',
        ]);
});

test('peserta can successfully register to course with atomic transaction and file upload', function () {
    $peserta = User::factory()->peserta()->create([
        'email' => 'asn.peserta@acehtimurkab.go.id',
    ]);

    $category = Category::first();
    $course = Course::create([
        'code' => 'PLT-2026-004',
        'title' => 'Pelatihan Transformasi Digital Birokrasi',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 50,
        'status' => 'published',
    ]);

    $fakePdf = UploadedFile::fake()->create('surat_usulan_atasan.pdf', 500, 'application/pdf');

    Livewire::actingAs($peserta)
        ->test(PendaftaranCreate::class, ['id' => $course->id])
        ->set('form.nip', '198705052011011002')
        ->set('form.name', 'Cut Nurul, S.Sos, M.AP')
        ->set('form.opd_agency', 'Dinas Komunikasi dan Informatika')
        ->set('form.position', 'Pranata Humas Ahli Muda')
        ->set('form.rank_class', 'Penata Tk. I - III/d')
        ->set('form.phone_number', '081234567890')
        ->set('form.email', 'asn.peserta@acehtimurkab.go.id')
        ->set('form.agreement', true)
        ->set('recommendationLetter', $fakePdf)
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('registrationSuccess', true)
        ->assertSet('alreadyRegistered', true);

    // Assert CourseUser was created in database
    $registration = CourseUser::where('user_id', $peserta->id)
        ->where('course_id', $course->id)
        ->first();

    expect($registration)->not->toBeNull();
    expect($registration->status)->toBe(RegistrationStatus::Pending);
    expect($registration->registration_number)->toMatch('/^REG-\d{6}-\d{4}$/');
    expect($registration->recommendation_letter_path)->not->toBeNull();
    Storage::disk('public')->assertExists($registration->recommendation_letter_path);

    // Assert user profile was updated with submitted NIP and OPD
    $peserta->refresh();
    expect($peserta->nip)->toBe('198705052011011002');
    expect($peserta->opd_agency)->toBe('Dinas Komunikasi dan Informatika');
});

test('peserta cannot register twice to the same course', function () {
    $peserta = User::factory()->peserta()->create();
    $category = Category::first();
    $course = Course::create([
        'code' => 'PLT-2026-005',
        'title' => 'Pelatihan Kode Etik Aparatur',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 30,
        'status' => 'published',
    ]);

    // First registration
    CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $course->id,
        'registration_number' => 'REG-202609-0010',
        'status' => RegistrationStatus::Pending,
        'enrolled_at' => now(),
    ]);

    // Second registration attempt via Livewire mounts alreadyRegistered
    Livewire::actingAs($peserta)
        ->test(PendaftaranCreate::class, ['id' => $course->id])
        ->assertSet('alreadyRegistered', true)
        ->assertSee('Anda Telah Terdaftar pada Pelatihan Ini');
});

test('admin can view detail modal and delete pendaftaran record', function () {
    $admin = User::factory()->admin()->create();
    $peserta = User::factory()->peserta()->create();
    $category = Category::first();
    $course = Course::create([
        'code' => 'PLT-2026-006',
        'title' => 'Pelatihan Pelayanan Publik',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 20,
        'status' => 'published',
    ]);

    $fakeFile = UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf');
    $path = $fakeFile->store('recommendations', 'public');

    $reg = CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $course->id,
        'registration_number' => 'REG-202609-0099',
        'status' => RegistrationStatus::Pending,
        'recommendation_letter_path' => $path,
        'enrolled_at' => now(),
    ]);

    // Test detail modal
    Livewire::actingAs($admin)
        ->test(PendaftaranData::class)
        ->call('showDetail', $reg->id)
        ->assertDispatched('openModal', id: 'modalDetailPendaftaran')
        ->assertSet('selectedDetail.registration_number', 'REG-202609-0099');

    // Test delete
    Livewire::actingAs($admin)
        ->test(PendaftaranData::class)
        ->call('delete', ['id' => $reg->id])
        ->assertDispatched('closeModal', id: 'modalDelete')
        ->assertDispatched('reloadDT', data: 'dtTable');

    $this->assertDatabaseMissing('course_user', ['id' => $reg->id]);
    Storage::disk('public')->assertMissing($path);
});

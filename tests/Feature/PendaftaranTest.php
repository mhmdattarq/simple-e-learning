<?php

use App\Enums\CourseStatus;
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
    $peserta = User::factory()->peserta()->create();

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

test('peserta gets clear error message when registration period has not started or ended', function () {
    $peserta = User::factory()->peserta()->create();
    $category = Category::first();
    $fakePdf = UploadedFile::fake()->create('surat.pdf', 200, 'application/pdf');

    // Case 1: Registration not started yet
    $courseFuture = Course::create([
        'code' => 'PLT-FUT-001',
        'title' => 'Pelatihan Masa Depan',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 20,
        'status' => CourseStatus::Published,
        'registration_open_at' => now()->addDays(2),
        'registration_close_at' => now()->addDays(5),
    ]);

    Livewire::actingAs($peserta)
        ->test(PendaftaranCreate::class, ['id' => $courseFuture->id])
        ->set('form.nip', '199001012020011001')
        ->set('form.name', 'Peserta Baru')
        ->set('form.opd_agency', 'Dinas Pendidikan')
        ->set('form.agreement', true)
        ->set('recommendationLetter', $fakePdf)
        ->call('submit')
        ->assertHasErrors(['general' => 'Periode pendaftaran untuk pelatihan ini belum dimulai.']);

    // Case 2: Registration period ended
    $coursePast = Course::create([
        'code' => 'PLT-PAST-001',
        'title' => 'Pelatihan Masa Lalu',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 20,
        'status' => CourseStatus::Published,
        'registration_open_at' => now()->subDays(5),
        'registration_close_at' => now()->subDay(),
    ]);

    Livewire::actingAs($peserta)
        ->test(PendaftaranCreate::class, ['id' => $coursePast->id])
        ->set('form.nip', '199001012020011001')
        ->set('form.name', 'Peserta Baru')
        ->set('form.opd_agency', 'Dinas Pendidikan')
        ->set('form.agreement', true)
        ->set('recommendationLetter', $fakePdf)
        ->call('submit')
        ->assertHasErrors(['general' => 'Periode pendaftaran untuk pelatihan ini telah berakhir.']);
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

test('admin can export registration recap data to csv', function () {
    $admin = User::factory()->admin()->create();
    $peserta = User::factory()->peserta()->create([
        'name' => 'Dr. Cut Nyak Dien, M.Si',
        'nip' => '198505022010012003',
        'opd_agency' => 'Bappeda Aceh',
    ]);

    $category = Category::first();
    $course = Course::create([
        'code' => 'PLT-2026-EXP',
        'title' => 'Pelatihan Perencanaan Anggaran',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 25,
        'status' => 'published',
    ]);

    CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $course->id,
        'registration_number' => 'REG-202609-0888',
        'status' => RegistrationStatus::Pending,
        'enrolled_at' => now(),
    ]);

    // Guest cannot export
    $this->get(route('pendaftaran.export'))->assertRedirect(route('login'));

    // Peserta cannot export
    $this->actingAs($peserta)->get(route('pendaftaran.export'))->assertStatus(403);

    // Admin can export
    $response = $this->actingAs($admin)->get(route('pendaftaran.export'));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($response->headers->get('content-disposition'))->toContain('attachment; filename=rekap-pendaftaran-diklat-');

    $content = $response->streamedContent();
    expect($content)->toContain('Nama Lengkap ASN');
    expect($content)->toContain('Instansi / OPD');
    expect($content)->toContain('REG-202609-0888');
    expect($content)->toContain('Dr. Cut Nyak Dien, M.Si');
    expect($content)->toContain('Bappeda Aceh');
    expect($content)->toContain('PLT-2026-EXP');
});

test('peserta cannot register if course is draft, outside dates, or full quota', function () {
    $peserta = User::factory()->peserta()->create();
    $category = Category::first();

    // 1. Course is still draft
    $courseDraft = Course::create([
        'code' => 'PLT-DRAFT',
        'title' => 'Pelatihan Belum Dibuka',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 20,
        'status' => CourseStatus::Draft,
    ]);

    $fakePdf = UploadedFile::fake()->create('surat.pdf', 200, 'application/pdf');

    Livewire::actingAs($peserta)
        ->test(PendaftaranCreate::class, ['id' => $courseDraft->id])
        ->set('form.nip', '198705052011011002')
        ->set('form.name', 'Peserta Uji Coba')
        ->set('form.opd_agency', 'BKPSDM')
        ->set('form.position', 'Staff')
        ->set('form.rank_class', 'Penata Muda / III.a')
        ->set('form.phone_number', '081234567890')
        ->set('form.email', 'uji@acehtimurkab.go.id')
        ->set('form.agreement', true)
        ->set('recommendationLetter', $fakePdf)
        ->call('submit')
        ->assertHasErrors(['general']);

    // 2. Course quota is already reached
    $courseFull = Course::create([
        'code' => 'PLT-FULL',
        'title' => 'Pelatihan Kuota Penuh',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 1,
        'status' => CourseStatus::Published,
    ]);

    $anotherUser = User::factory()->peserta()->create();
    CourseUser::create([
        'user_id' => $anotherUser->id,
        'course_id' => $courseFull->id,
        'registration_number' => 'REG-202609-0001',
        'status' => RegistrationStatus::Pending,
        'enrolled_at' => now(),
    ]);

    Livewire::actingAs($peserta)
        ->test(PendaftaranCreate::class, ['id' => $courseFull->id])
        ->set('form.nip', '198705052011011002')
        ->set('form.name', 'Peserta Uji Coba')
        ->set('form.opd_agency', 'BKPSDM')
        ->set('form.position', 'Staff')
        ->set('form.rank_class', 'Penata Muda / III.a')
        ->set('form.phone_number', '081234567890')
        ->set('form.email', 'uji@acehtimurkab.go.id')
        ->set('form.agreement', true)
        ->set('recommendationLetter', $fakePdf)
        ->call('submit')
        ->assertHasErrors(['general']);
});

test('admin can view only approved courses in registration settings dropdown', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $courseDraft = Course::create([
        'code' => 'PLT-DRAFT-1',
        'title' => 'Pelatihan Draft Belum Disetujui',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => now()->addMonth()->toDateString(),
        'end_date' => now()->addMonth()->addDays(5)->toDateString(),
        'method' => 'hybrid',
        'quota' => 20,
        'status' => CourseStatus::Draft,
    ]);

    $coursePublished = Course::create([
        'code' => 'PLT-PUB-1',
        'title' => 'Pelatihan Sudah Dibuka',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => now()->addMonth()->toDateString(),
        'end_date' => now()->addMonth()->addDays(5)->toDateString(),
        'method' => 'hybrid',
        'quota' => 20,
        'status' => CourseStatus::Published,
    ]);

    $courseApproved = Course::create([
        'code' => 'PLT-APP-1',
        'title' => 'Pelatihan Siap Dibuka',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => now()->addMonth()->toDateString(),
        'end_date' => now()->addMonth()->addDays(5)->toDateString(),
        'method' => 'hybrid',
        'quota' => 30,
        'status' => CourseStatus::Approved,
    ]);

    Livewire::actingAs($admin)
        ->test(PendaftaranData::class)
        ->assertViewHas('settingCourses', function ($courses) use ($courseApproved, $courseDraft, $coursePublished) {
            return $courses->contains($courseApproved)
                && ! $courses->contains($courseDraft)
                && ! $courses->contains($coursePublished);
        })
        ->set('selectedCourseId', $courseApproved->id)
        ->assertSet('courseStats.code', 'PLT-APP-1')
        ->assertSet('courseStats.quota', 30)
        ->assertSee('Periode Pelatihan');

    $coursePermanent = Course::create([
        'code' => 'PLT-APP-PERM',
        'title' => 'Pelatihan Mandiri Disetujui',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 50,
        'status' => CourseStatus::Approved,
    ]);

    Livewire::actingAs($admin)
        ->test(PendaftaranData::class)
        ->set('selectedCourseId', $coursePermanent->id)
        ->assertSet('courseStats.is_permanent', true)
        ->assertDontSee('Periode Pelatihan');
});

test('admin opening registration period validates dates and course start date correctly', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $startDate = now()->addDays(20);

    $course = Course::create([
        'code' => 'PLT-VALIDATE-1',
        'title' => 'Pelatihan Uji Validasi Periode',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => $startDate->toDateString(),
        'end_date' => $startDate->copy()->addDays(5)->toDateString(),
        'method' => 'hybrid',
        'quota' => 25,
        'status' => CourseStatus::Approved,
    ]);

    // 1. Empty dates error
    Livewire::actingAs($admin)
        ->test(PendaftaranData::class)
        ->set('selectedCourseId', $course->id)
        ->set('registration_open_at', '')
        ->set('registration_close_at', '')
        ->call('openPeriod')
        ->assertHasErrors(['registration_open_at', 'registration_close_at']);

    // 2. Open >= Close error
    Livewire::actingAs($admin)
        ->test(PendaftaranData::class)
        ->set('selectedCourseId', $course->id)
        ->set('registration_open_at', '2026-10-10T10:00')
        ->set('registration_close_at', '2026-10-05T10:00')
        ->call('openPeriod')
        ->assertHasErrors(['registration_open_at' => 'Tanggal buka pendaftaran harus sebelum tanggal tutup pendaftaran.']);

    // 3. Close date after course start date error
    Livewire::actingAs($admin)
        ->test(PendaftaranData::class)
        ->set('selectedCourseId', $course->id)
        ->set('registration_open_at', $startDate->copy()->subDays(5)->format('Y-m-d\TH:i'))
        ->set('registration_close_at', $startDate->copy()->addDays(2)->format('Y-m-d\TH:i'))
        ->call('openPeriod')
        ->assertHasErrors(['registration_close_at' => 'Tanggal tutup pendaftaran tidak boleh melewati tanggal mulai pelatihan.']);

    // 4. Past start date cannot be opened
    $coursePast = Course::create([
        'code' => 'PLT-PAST-1',
        'title' => 'Pelatihan Masa Lalu',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => now()->subDays(5)->toDateString(),
        'end_date' => now()->subDays(2)->toDateString(),
        'method' => 'hybrid',
        'quota' => 25,
        'status' => CourseStatus::Approved,
    ]);

    Livewire::actingAs($admin)
        ->test(PendaftaranData::class)
        ->set('selectedCourseId', $coursePast->id)
        ->set('registration_open_at', now()->subDays(10)->format('Y-m-d\TH:i'))
        ->set('registration_close_at', now()->subDays(6)->format('Y-m-d\TH:i'))
        ->call('openPeriod')
        ->assertHasErrors(['registration_open_at' => 'Pelatihan tidak dapat dibuka karena tanggal mulai pelatihan telah lewat.']);
});

test('admin can open and close registration period successfully', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $startDate = now()->addDays(15);

    $course = Course::create([
        'code' => 'PLT-OPEN-CLOSE',
        'title' => 'Pelatihan Buka Tutup Pendaftaran',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => $startDate->toDateString(),
        'end_date' => $startDate->copy()->addDays(5)->toDateString(),
        'method' => 'hybrid',
        'quota' => 30,
        'status' => CourseStatus::Approved,
    ]);

    $openAt = $startDate->copy()->subDays(10)->format('Y-m-d\TH:i');
    $closeAt = $startDate->copy()->subDays(2)->format('Y-m-d\TH:i');

    // Open period
    Livewire::actingAs($admin)
        ->test(PendaftaranData::class)
        ->set('selectedCourseId', $course->id)
        ->set('registration_open_at', $openAt)
        ->set('registration_close_at', $closeAt)
        ->call('openPeriod')
        ->assertHasNoErrors()
        ->assertDispatched('alert-show');

    $course->refresh();
    expect($course->status)->toBe(CourseStatus::Published);
    expect($course->registration_open_at)->not->toBeNull();
    expect($course->registration_close_at)->not->toBeNull();

    // Close period
    Livewire::actingAs($admin)
        ->test(PendaftaranData::class)
        ->set('selectedCourseId', $course->id)
        ->call('closePeriod')
        ->assertHasNoErrors()
        ->assertDispatched('alert-show');

    $course->refresh();
    expect($course->status)->toBe(CourseStatus::Approved);
    expect($course->registration_close_at)->not->toBeNull();
});

test('verifikator cannot open or close registration period but can view registrations', function () {
    $verifikator = User::factory()->verifikator()->create();
    $category = Category::first();

    $course = Course::create([
        'code' => 'PLT-VERIF-TEST',
        'title' => 'Pelatihan Hak Akses Verifikator',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => now()->addDays(10)->toDateString(),
        'end_date' => now()->addDays(15)->toDateString(),
        'method' => 'hybrid',
        'quota' => 30,
        'status' => CourseStatus::Approved,
    ]);

    // Verifikator can access index page
    $this->actingAs($verifikator)->get(route('pendaftaran.data'))->assertOk();

    // Attempting to open registration period aborts 403 Forbidden
    Livewire::actingAs($verifikator)
        ->test(PendaftaranData::class)
        ->set('selectedCourseId', $course->id)
        ->set('registration_open_at', now()->addDay()->format('Y-m-d\TH:i'))
        ->set('registration_close_at', now()->addDays(5)->format('Y-m-d\TH:i'))
        ->call('openPeriod')
        ->assertForbidden();
});

test('modal detail pendaftaran displays complete participant, verifier and notes info', function () {
    $admin = User::factory()->admin()->create();
    $verifikator = User::factory()->verifikator()->create([
        'name' => 'Budi Santoso, S.Kom (Verifikator)',
    ]);
    $peserta = User::factory()->peserta()->create([
        'name' => 'Siti Rahmawati',
        'nip' => '199201012018012001',
        'opd_agency' => 'Inspektorat Daerah',
        'position' => 'Auditor Pertama',
        'rank_class' => 'Penata Muda / III.a',
        'phone_number' => '081299998888',
    ]);

    $category = Category::first();
    $course = Course::create([
        'code' => 'PLT-DETAIL-1',
        'title' => 'Pelatihan Audit Investigatif',
        'category_id' => $category->id,
        'type' => 'batch',
        'start_date' => now()->addMonth()->toDateString(),
        'end_date' => now()->addMonth()->addDays(5)->toDateString(),
        'method' => 'hybrid',
        'quota' => 20,
        'status' => CourseStatus::Published,
    ]);

    $reg = CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $course->id,
        'registration_number' => 'REG-202609-0777',
        'status' => RegistrationStatus::Verified,
        'enrolled_at' => now(),
        'verified_by' => $verifikator->id,
        'verified_at' => now(),
        'verification_notes' => 'Berkas surat usulan dan NIP telah valid dan memenuhi syarat.',
    ]);

    Livewire::actingAs($admin)
        ->test(PendaftaranData::class)
        ->call('showDetail', $reg->id)
        ->assertDispatched('openModal', id: 'modalDetailPendaftaran')
        ->assertSet('selectedDetail.user_name', 'Siti Rahmawati')
        ->assertSet('selectedDetail.user_nip', '199201012018012001')
        ->assertSet('selectedDetail.user_opd', 'Inspektorat Daerah')
        ->assertSet('selectedDetail.verifier_name', 'Budi Santoso, S.Kom (Verifikator)')
        ->assertSet('selectedDetail.verification_notes', 'Berkas surat usulan dan NIP telah valid dan memenuhi syarat.');
});

test('pendaftaran datatable supports multi-filtering by course, status, opd, and date range', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course1 = Course::create([
        'code' => 'PLT-FILT-1',
        'title' => 'Pelatihan Filter 1',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 30,
        'status' => CourseStatus::Published,
    ]);

    $course2 = Course::create([
        'code' => 'PLT-FILT-2',
        'title' => 'Pelatihan Filter 2',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 30,
        'status' => CourseStatus::Published,
    ]);

    $userA = User::factory()->peserta()->create(['opd_agency' => 'Dinas Kesehatan']);
    $userB = User::factory()->peserta()->create(['opd_agency' => 'Dinas Pendidikan']);

    CourseUser::create([
        'user_id' => $userA->id,
        'course_id' => $course1->id,
        'registration_number' => 'REG-FILT-0001',
        'status' => RegistrationStatus::Pending,
        'enrolled_at' => '2026-09-10 10:00:00',
    ]);

    CourseUser::create([
        'user_id' => $userB->id,
        'course_id' => $course2->id,
        'registration_number' => 'REG-FILT-0002',
        'status' => RegistrationStatus::Verified,
        'enrolled_at' => '2026-09-20 10:00:00',
    ]);

    // 1. Filter by course_id
    $resCourse = $this->actingAs($admin)->getJson(route('pendaftaran.dt', ['course_id' => $course1->id]));
    $resCourse->assertOk();
    expect($resCourse->json('data'))->toHaveCount(1);
    expect($resCourse->json('data')[0]['registration_number'])->toBe('REG-FILT-0001');

    // 2. Filter by status
    $resStatus = $this->actingAs($admin)->getJson(route('pendaftaran.dt', ['status' => 'verified']));
    $resStatus->assertOk();
    expect($resStatus->json('data'))->toHaveCount(1);
    expect($resStatus->json('data')[0]['registration_number'])->toBe('REG-FILT-0002');

    // 3. Filter by OPD
    $resOpd = $this->actingAs($admin)->getJson(route('pendaftaran.dt', ['opd' => 'Kesehatan']));
    $resOpd->assertOk();
    expect($resOpd->json('data'))->toHaveCount(1);
    expect($resOpd->json('data')[0]['registration_number'])->toBe('REG-FILT-0001');

    // 4. Filter by Date range
    $resDate = $this->actingAs($admin)->getJson(route('pendaftaran.dt', [
        'start_date' => '2026-09-15',
        'end_date' => '2026-09-25',
    ]));
    $resDate->assertOk();
    expect($resDate->json('data'))->toHaveCount(1);
    expect($resDate->json('data')[0]['registration_number'])->toBe('REG-FILT-0002');
});

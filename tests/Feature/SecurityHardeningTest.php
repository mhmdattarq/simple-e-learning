<?php

use App\Enums\CourseStatus;
use App\Enums\RegistrationStatus;
use App\Enums\Role;
use App\Livewire\Auth\Login;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\CourseUser;
use App\Models\User;
use App\Repositories\AbsensiRepo;
use App\Repositories\PimpinanRepo;
use App\Repositories\VerifikasiRepo;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
    Storage::fake('public');
    RateLimiter::clear('login');
});

test('route-level RBAC blocks cross-role access to unauthorized module prefixes', function () {
    $mentor = User::factory()->mentor()->create();
    $verifikator = User::factory()->verifikator()->create();

    // Verifikator cannot access planning, schedule, attendance, or curriculum
    $this->actingAs($verifikator)->get(route('perencanaan.data'))->assertStatus(403);
    $this->actingAs($verifikator)->get(route('penjadwalan.data'))->assertStatus(403);
    $this->actingAs($verifikator)->get(route('absensi.data'))->assertStatus(403);
    $this->actingAs($verifikator)->get(route('materi.data'))->assertStatus(403);

    // Mentor cannot access planning, registration, verification, or executive approval
    $this->actingAs($mentor)->get(route('perencanaan.data'))->assertStatus(403);
    $this->actingAs($mentor)->get(route('pendaftaran.data'))->assertStatus(403);
    $this->actingAs($mentor)->get(route('verifikasi.data'))->assertStatus(403);
    $this->actingAs($mentor)->get(route('pimpinan.dashboard'))->assertStatus(403);

    // Admin can access all operational modules
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->get(route('perencanaan.data'))->assertStatus(200);
    $this->actingAs($admin)->get(route('pendaftaran.data'))->assertStatus(200);
    $this->actingAs($admin)->get(route('verifikasi.data'))->assertStatus(200);
    $this->actingAs($admin)->get(route('penjadwalan.data'))->assertStatus(200);
    $this->actingAs($admin)->get(route('absensi.data'))->assertStatus(200);
    $this->actingAs($admin)->get(route('materi.data'))->assertStatus(200);
});

test('login authentication applies rate limiting after 5 consecutive failures', function () {
    $user = User::factory()->peserta()->create([
        'email' => 'asn@acehtimurkab.go.id',
        'password' => 'secret123',
    ]);

    // 5 failed login attempts
    for ($i = 0; $i < 5; $i++) {
        Livewire::test(Login::class)
            ->set('identifier', 'asn@acehtimurkab.go.id')
            ->set('password', 'wrong-password-'.$i)
            ->call('authenticate')
            ->assertHasErrors(['identifier']);
    }

    // 6th attempt is throttled
    Livewire::test(Login::class)
        ->set('identifier', 'asn@acehtimurkab.go.id')
        ->set('password', 'secret123')
        ->call('authenticate')
        ->assertSee('Terlalu banyak percobaan masuk');

    $this->assertGuest();
});

test('audit log model enforces strict append-only immutability', function () {
    $admin = User::factory()->admin()->create();

    $log = AuditLog::log(
        action: 'security.test',
        notes: 'Initial test entry',
        userId: $admin->id
    );

    expect($log->exists)->toBeTrue();
    expect($log->action)->toBe('security.test');

    // Attempting to update throws RuntimeException
    expect(fn () => $log->update(['notes' => 'Tampered notes']))
        ->toThrow(RuntimeException::class, 'Audit logs are append-only and cannot be updated.');

    // Attempting to delete throws RuntimeException
    expect(fn () => $log->delete())
        ->toThrow(RuntimeException::class, 'Audit logs are append-only and cannot be deleted.');
});

test('course plan approval and rejection record immutable audit log entries', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course = Course::create([
        'code' => 'AUDIT-PLT-01',
        'title' => 'Pelatihan Audit Keamanan',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 20,
        'status' => CourseStatus::Submitted,
    ]);

    // 1. Approve
    PimpinanRepo::approvePlan($course->id, 'Disetujui Pimpinan untuk pelaksanaan', $admin->id);

    $approvalLog = AuditLog::where('action', 'course.approved')
        ->where('auditable_id', $course->id)
        ->latest('id')
        ->first();

    expect($approvalLog)->not->toBeNull();
    expect($approvalLog->user_id)->toBe($admin->id);
    expect($approvalLog->new_values['status'])->toBe(CourseStatus::Approved->value);

    // 2. Reject another submitted course
    $course2 = Course::create([
        'code' => 'AUDIT-PLT-01B',
        'title' => 'Pelatihan Kedua untuk Ditolak',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 20,
        'status' => CourseStatus::Submitted,
    ]);

    PimpinanRepo::rejectPlan($course2->id, 'Perlu perbaikan kurikulum');

    $rejectionLog = AuditLog::where('action', 'course.rejected')
        ->where('auditable_id', $course2->id)
        ->latest('id')
        ->first();

    expect($rejectionLog)->not->toBeNull();
    expect($rejectionLog->notes)->toBe('Perlu perbaikan kurikulum');
});

test('registration verification records immutable audit log entries', function () {
    $verifikator = User::factory()->verifikator()->create();
    $peserta = User::factory()->peserta()->create();
    $category = Category::first();

    $course = Course::create([
        'code' => 'AUDIT-PLT-02',
        'title' => 'Pelatihan Verifikasi ASN',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 20,
        'status' => CourseStatus::Approved,
    ]);

    $reg = CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $course->id,
        'registration_number' => 'REG-AUDIT-001',
        'status' => RegistrationStatus::Pending,
        'enrolled_at' => now(),
    ]);

    VerifikasiRepo::verify($reg->id, RegistrationStatus::Verified, 'Berkas lengkap dan sesuai', $verifikator->id);

    $verificationLog = AuditLog::where('action', 'registration.verified')
        ->where('auditable_id', $reg->id)
        ->first();

    expect($verificationLog)->not->toBeNull();
    expect($verificationLog->user_id)->toBe($verifikator->id);
    expect($verificationLog->notes)->toBe('Berkas lengkap dan sesuai');
});

test('manual attendance correction records immutable audit log entries', function () {
    $admin = User::factory()->admin()->create();
    $mentor = User::factory()->mentor()->create();
    $peserta = User::factory()->peserta()->create();
    $category = Category::first();

    $course = Course::create([
        'code' => 'AUDIT-PLT-03',
        'title' => 'Pelatihan Presensi',
        'category_id' => $category->id,
        'type' => 'batch',
        'method' => 'hybrid',
        'quota' => 20,
        'status' => CourseStatus::Published,
    ]);

    $schedule = CourseSchedule::create([
        'course_id' => $course->id,
        'mentor_id' => $mentor->id,
        'session_title' => 'Sesi Evaluasi',
        'session_date' => now()->toDateString(),
        'start_time' => '08:00',
        'end_time' => '12:00',
        'room_or_link' => 'Ruang 101',
        'status' => 'scheduled',
    ]);

    AbsensiRepo::applyManualCorrection(
        $schedule->id,
        $peserta->id,
        'present',
        'Peserta hadir offline tetapi terkendala jaringan saat scan',
        $admin->id
    );

    $correctionLog = AuditLog::where('action', 'attendance.corrected')->first();

    expect($correctionLog)->not->toBeNull();
    expect($correctionLog->user_id)->toBe($admin->id);
    expect($correctionLog->notes)->toContain('Peserta hadir offline');
});

test('media upload rejects unauthorized MIME types and accepts allowed document and image types', function () {
    $admin = User::factory()->admin()->create();

    // Dangerous / executable files are rejected with 422
    $phpFile = UploadedFile::fake()->create('malicious.php', 50, 'application/x-php');
    $this->actingAs($admin)->postJson(route('materi.upload-media'), [
        'file' => $phpFile,
    ])->assertStatus(422)
        ->assertJson(['success' => false]);

    $shFile = UploadedFile::fake()->create('script.sh', 50, 'text/x-shellscript');
    $this->actingAs($admin)->postJson(route('materi.upload-media'), [
        'file' => $shFile,
    ])->assertStatus(422);

    // Valid PDF and images are accepted
    $pdfFile = UploadedFile::fake()->create('silabus.pdf', 500, 'application/pdf');
    $this->actingAs($admin)->postJson(route('materi.upload-media'), [
        'file' => $pdfFile,
    ])->assertStatus(200)
        ->assertJson(['success' => true]);

    $imageFile = UploadedFile::fake()->image('diagram.png', 400, 400);
    $this->actingAs($admin)->postJson(route('materi.upload-media'), [
        'file' => $imageFile,
    ])->assertStatus(200)
        ->assertJson(['success' => true]);
});

test('data export is restricted to admin and verifikator and generates audit log', function () {
    $mentor = User::factory()->mentor()->create();
    $verifikator = User::factory()->verifikator()->create();
    $admin = User::factory()->admin()->create();

    // Mentor cannot export ASN personal data
    $this->actingAs($mentor)->get(route('pendaftaran.export'))->assertStatus(403);

    // Verifikator can export and generates an audit log
    $this->actingAs($verifikator)->get(route('pendaftaran.export'))->assertStatus(200);

    $verifikatorExportLog = AuditLog::where('action', 'data.exported')
        ->where('user_id', $verifikator->id)
        ->first();

    expect($verifikatorExportLog)->not->toBeNull();
    expect($verifikatorExportLog->new_values['role'])->toBe(Role::Verifikator->value);

    // Admin can export
    $this->actingAs($admin)->get(route('pendaftaran.export'))->assertStatus(200);

    $adminExportLog = AuditLog::where('action', 'data.exported')
        ->where('user_id', $admin->id)
        ->first();

    expect($adminExportLog)->not->toBeNull();
});

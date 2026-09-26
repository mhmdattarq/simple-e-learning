<?php

use App\Livewire\Admin\Absensi\AbsensiData;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\CourseUser;
use App\Models\User;
use App\Repositories\AbsensiRepo;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
    $this->category = Category::first();

    $this->course = Course::create([
        'code' => 'TIK-2026-005',
        'title' => 'Keamanan Siber & Perlindungan Data Pribadi ASN',
        'category_id' => $this->category->id,
        'type' => 'batch',
        'method' => 'luring',
        'quota' => 30,
        'status' => 'published',
    ]);

    $this->admin = User::factory()->admin()->create();
    $this->mentor = User::factory()->mentor()->create([
        'name' => 'Dr. Ir. Cut Meutia, M.Kom',
        'nip' => '198108152005012003',
    ]);

    $this->otherMentor = User::factory()->mentor()->create([
        'name' => 'Fauzi Rahman, M.T',
        'nip' => '198506102008041002',
    ]);

    $this->schedule = CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Fundamental Kriptografi & Pengamanan Jaringan',
        'session_date' => now()->toDateString(),
        'start_time' => '08:30:00',
        'end_time' => '11:30:00',
        'room_or_link' => 'Laboratorium Komputer BKPSDM',
        'status' => 'ongoing',
        'created_by' => $this->admin->id,
    ]);

    $this->peserta = User::factory()->peserta()->create([
        'name' => 'Fauzan Ahmad, S.STP',
        'nip' => '199501022018011001',
    ]);

    // Daftarkan & verifikasi peserta ke kursus
    $this->enrollment = CourseUser::create([
        'user_id' => $this->peserta->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-2026-00099',
        'status' => 'verified',
        'verified_by' => $this->admin->id,
        'verified_at' => now(),
    ]);
});

test('unauthorized users cannot access absensi routes', function () {
    // Guest
    $this->get(route('absensi.data'))
        ->assertRedirect(route('login'));

    // Peserta
    $this->actingAs($this->peserta)
        ->get(route('absensi.data'))
        ->assertStatus(403);
});

test('admin and mentor can access absensi page', function () {
    $this->actingAs($this->admin)
        ->get(route('absensi.data'))
        ->assertStatus(200)
        ->assertSee('Tahap 5: Absensi Elektronik')
        ->assertSee('Mode Admin: Monitoring');

    $this->actingAs($this->mentor)
        ->get(route('absensi.data'))
        ->assertStatus(200)
        ->assertSee('Tahap 5: Absensi Elektronik')
        ->assertSee('Mode Mentor Pengampu Sesi');
});

test('absensi datatable endpoint returns valid yajra json response', function () {
    $response = $this->actingAs($this->admin)
        ->getJson(route('absensi.dt'));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                '*' => [
                    'id',
                    'session_title',
                    'course_title',
                    'mentor_name',
                    'session_date_formatted',
                    'time_range',
                    'token_badge',
                    'attendance_summary_badge',
                    'is_current_mentor',
                ],
            ],
        ])
        ->assertJsonFragment([
            'session_title' => e('Fundamental Kriptografi & Pengamanan Jaringan'),
        ]);
});

test('mentor only sees assigned sessions in datatable while admin sees all', function () {
    // Buat sesi kedua yang diampu oleh otherMentor
    $otherSchedule = CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->otherMentor->id,
        'session_title' => 'Audit Keamanan Sistem Informasi Pemda',
        'session_date' => now()->toDateString(),
        'start_time' => '13:00:00',
        'end_time' => '15:30:00',
        'room_or_link' => 'Laboratorium Komputer BKPSDM',
        'status' => 'scheduled',
        'created_by' => $this->admin->id,
    ]);

    // 1. Admin melihat kedua sesi
    $adminResponse = $this->actingAs($this->admin)->getJson(route('absensi.dt'));
    $adminResponse->assertStatus(200);
    expect(count($adminResponse->json('data')))->toBe(2);

    // 2. Mentor pertama hanya melihat sesinya sendiri (1 sesi)
    $mentorResponse = $this->actingAs($this->mentor)->getJson(route('absensi.dt'));
    $mentorResponse->assertStatus(200);
    $data = $mentorResponse->json('data');
    expect(count($data))->toBe(1)
        ->and($data[0]['session_title'])->toBe(e('Fundamental Kriptografi & Pengamanan Jaringan'))
        ->and($data[0]['is_current_mentor'])->toBeTrue();
});

test('assigned mentor can open attendance session token with validity minutes', function () {
    $this->actingAs($this->mentor);

    Livewire::test(AbsensiData::class)
        ->call('hookModalToken', $this->schedule->id)
        ->assertSet('selectedScheduleId', $this->schedule->id)
        ->set('tokenValidityMinutes', 30)
        ->call('submitOpenToken')
        ->assertDispatched('alert')
        ->assertDispatched('reloadDT');

    $this->schedule->refresh();
    expect($this->schedule->is_attendance_open)->toBeTrue()
        ->and($this->schedule->attendance_token)->not->toBeNull()
        ->and(strlen($this->schedule->attendance_token))->toBe(6)
        ->and($this->schedule->token_validity_minutes)->toBe(30)
        ->and($this->schedule->isAttendanceActive())->toBeTrue();
});

test('admin can also open attendance session token with validity minutes', function () {
    $this->actingAs($this->admin);

    Livewire::test(AbsensiData::class)
        ->call('hookModalToken', $this->schedule->id)
        ->assertSet('selectedScheduleId', $this->schedule->id)
        ->set('tokenValidityMinutes', 20)
        ->set('lateThresholdMinutes', 10)
        ->call('submitOpenToken')
        ->assertDispatched('alert')
        ->assertDispatched('reloadDT');

    $this->schedule->refresh();
    expect($this->schedule->is_attendance_open)->toBeTrue()
        ->and($this->schedule->attendance_token)->not->toBeNull()
        ->and($this->schedule->token_validity_minutes)->toBe(20)
        ->and($this->schedule->late_threshold_minutes)->toBe(10)
        ->and($this->schedule->isAttendanceActive())->toBeTrue();
});

test('other mentor cannot open token for session they do not teach', function () {
    $this->actingAs($this->otherMentor);

    Livewire::test(AbsensiData::class)
        ->call('hookModalToken', $this->schedule->id)
        ->assertDispatched('alert', function ($name, $params) {
            return ($params['type'] ?? '') === 'error' && str_contains($params['message'] ?? '', 'Akses ditolak');
        });

    $this->schedule->refresh();
    expect($this->schedule->is_attendance_open)->toBeFalse();
});

test('assigned mentor can close attendance session token early', function () {
    $this->actingAs($this->mentor);

    AbsensiRepo::openAttendanceSession($this->schedule, 15);
    expect($this->schedule->fresh()->isAttendanceActive())->toBeTrue();

    Livewire::test(AbsensiData::class)
        ->call('closeToken', $this->schedule->id)
        ->assertDispatched('alert')
        ->assertDispatched('reloadDT');

    $this->schedule->refresh();
    expect($this->schedule->is_attendance_open)->toBeFalse()
        ->and($this->schedule->isAttendanceActive())->toBeFalse();
});

test('admin can also close attendance session token and auto mark unattended as alpa', function () {
    $this->actingAs($this->admin);

    AbsensiRepo::openAttendanceSession($this->schedule, 15);
    expect($this->schedule->fresh()->isAttendanceActive())->toBeTrue();

    Livewire::test(AbsensiData::class)
        ->set('autoAlpaOnClose', true)
        ->call('closeToken', $this->schedule->id)
        ->assertDispatched('alert')
        ->assertDispatched('reloadDT');

    expect($this->schedule->fresh()->isAttendanceActive())->toBeFalse();

    // Peserta belum absen otomatis menjadi alpa
    $this->assertDatabaseHas('attendances', [
        'schedule_id' => $this->schedule->id,
        'user_id' => $this->peserta->id,
        'status' => 'alpa',
        'method' => 'manual',
    ]);
});

test('admin can perform manual attendance correction with reason', function () {
    $this->actingAs($this->admin);

    Livewire::test(AbsensiData::class)
        ->call('showAttendanceDetail', $this->schedule->id)
        ->assertSet('sheetScheduleId', $this->schedule->id)
        ->call('hookModalCorrection', $this->peserta->id, $this->peserta->name, 'hadir')
        ->set('correctionStatus', 'izin')
        ->set('correctionReason', 'Peserta mengajukan izin dinas luar dari BKPSDM.')
        ->call('submitCorrection')
        ->assertDispatched('close-modal-correction')
        ->assertDispatched('alert')
        ->assertDispatched('reloadDT');

    $this->assertDatabaseHas('attendances', [
        'schedule_id' => $this->schedule->id,
        'user_id' => $this->peserta->id,
        'status' => 'izin',
        'is_manual_correction' => true,
        'correction_reason' => 'Peserta mengajukan izin dinas luar dari BKPSDM.',
        'corrected_by' => $this->admin->id,
    ]);
});

test('absensi datatable server-side search input cari data works across fields', function () {
    // 1. Search by session title
    $response = $this->actingAs($this->admin)->getJson(route('absensi.dt', [
        'search' => ['value' => 'Kriptografi'],
    ]));
    $response->assertStatus(200);
    expect(count($response->json('data')))->toBe(1);

    // 2. Search by course code
    $responseCourse = $this->actingAs($this->admin)->getJson(route('absensi.dt', [
        'search' => ['value' => 'TIK-2026-005'],
    ]));
    expect(count($responseCourse->json('data')))->toBe(1);

    // 3. Search by mentor name
    $responseMentor = $this->actingAs($this->admin)->getJson(route('absensi.dt', [
        'search' => ['value' => 'Cut Meutia'],
    ]));
    expect(count($responseMentor->json('data')))->toBe(1);

    // 4. Search with non-existent keyword returns 0 records
    $responseEmpty = $this->actingAs($this->admin)->getJson(route('absensi.dt', [
        'search' => ['value' => 'ZzzzXyyyNonExistent'],
    ]));
    expect(count($responseEmpty->json('data')))->toBe(0);
});

test('absensi datatable filter status token works correctly', function () {
    // Awal: sesi belum dibuka (unopened)
    $responseUnopened = $this->actingAs($this->admin)->getJson(route('absensi.dt', [
        'token_status' => 'unopened',
    ]));
    expect(count($responseUnopened->json('data')))->toBe(1);

    // Filter active sebelum dibuka harus 0
    $responseActiveEmpty = $this->actingAs($this->admin)->getJson(route('absensi.dt', [
        'token_status' => 'active',
    ]));
    expect(count($responseActiveEmpty->json('data')))->toBe(0);

    // Buka token
    AbsensiRepo::openAttendanceSession($this->schedule, 15);

    // Sekarang filter active harus mengembalikan 1 sesi
    $responseActive = $this->actingAs($this->admin)->getJson(route('absensi.dt', [
        'token_status' => 'active',
    ]));
    expect(count($responseActive->json('data')))->toBe(1);

    // Filter unopened sekarang harus 0
    $responseUnopenedAfter = $this->actingAs($this->admin)->getJson(route('absensi.dt', [
        'token_status' => 'unopened',
    ]));
    expect(count($responseUnopenedAfter->json('data')))->toBe(0);

    // Kedaluwarsakan sesi
    $this->schedule->update(['token_expires_at' => now()->subMinute(), 'is_attendance_open' => false]);

    // Filter expired harus 1
    $responseExpired = $this->actingAs($this->admin)->getJson(route('absensi.dt', [
        'token_status' => 'expired',
    ]));
    expect(count($responseExpired->json('data')))->toBe(1);
});

test('cancelled sessions do not appear in absensi datatable', function () {
    $this->schedule->update(['status' => 'cancelled']);

    $response = $this->actingAs($this->admin)->getJson(route('absensi.dt'));
    $response->assertStatus(200);
    expect(count($response->json('data')))->toBe(0);
});

test('verified participant can check in with token or qr and records method correctly', function () {
    $token = AbsensiRepo::openAttendanceSession($this->schedule, 30, 15);

    // 1. Check in via token
    $result = AbsensiRepo::checkInPeserta($this->peserta, $token, 'token');
    expect($result['success'])->toBeTrue()
        ->and($result['attendance']->status)->toBe('hadir')
        ->and($result['attendance']->method)->toBe('token');

    $this->assertDatabaseHas('attendances', [
        'schedule_id' => $this->schedule->id,
        'user_id' => $this->peserta->id,
        'status' => 'hadir',
        'method' => 'token',
    ]);
});

test('participant check in records terlambat if check in exceeds late threshold', function () {
    // Sesi dibuka dengan toleransi keterlambatan 5 menit, tapi token dibuka 10 menit yang lalu
    $token = AbsensiRepo::openAttendanceSession($this->schedule, 30, 5);
    $this->schedule->update([
        'token_opened_at' => now()->subMinutes(10),
    ]);

    $result = AbsensiRepo::checkInPeserta($this->peserta, $token, 'qr');
    expect($result['success'])->toBeTrue()
        ->and($result['attendance']->status)->toBe('terlambat')
        ->and($result['attendance']->method)->toBe('qr');

    $this->assertDatabaseHas('attendances', [
        'schedule_id' => $this->schedule->id,
        'user_id' => $this->peserta->id,
        'status' => 'terlambat',
        'method' => 'qr',
    ]);
});

test('unverified participant or already checked in participant cannot check in again', function () {
    $token = AbsensiRepo::openAttendanceSession($this->schedule, 30, 15);

    // 1. Peserta tidak terverifikasi (status pending)
    $unverifiedUser = User::factory()->peserta()->create();
    CourseUser::create([
        'user_id' => $unverifiedUser->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-2026-PENDING',
        'status' => 'pending',
    ]);

    $resUnverified = AbsensiRepo::checkInPeserta($unverifiedUser, $token);
    expect($resUnverified['success'])->toBeFalse()
        ->and($resUnverified['message'])->toContain('tidak terdaftar sebagai peserta terverifikasi');

    // 2. Peserta berhasil check in pertama kali
    $resFirst = AbsensiRepo::checkInPeserta($this->peserta, $token);
    expect($resFirst['success'])->toBeTrue();

    // 3. Peserta mencoba check in kedua kali
    $resSecond = AbsensiRepo::checkInPeserta($this->peserta, $token);
    expect($resSecond['success'])->toBeFalse()
        ->and($resSecond['message'])->toContain('sudah tercatat melakukan presensi');
});

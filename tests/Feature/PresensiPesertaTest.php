<?php

use App\Livewire\Peserta\Absensi\PresensiIndex;
use App\Models\Attendance;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\CourseUser;
use App\Models\User;
use App\Repositories\AbsensiRepo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::factory()->create();

    $this->course = Course::factory()->create([
        'category_id' => $this->category->id,
        'title' => 'Pelatihan Transformasi Digital ASN',
        'type' => 'batch',
        'status' => 'published',
    ]);

    $this->mentor = User::factory()->mentor()->create([
        'name' => 'Prof. Dr. Ir. Teuku Iskandar, M.Eng',
        'nip' => '198005122003121002',
    ]);

    $this->schedule = CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Implementasi E-Government dan Keamanan Sistem',
        'session_date' => now()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '12:00:00',
        'room_or_link' => 'Aula Gedung Diklat BKPSDM',
        'status' => 'ongoing',
    ]);

    // Buka sesi absensi
    $this->token = AbsensiRepo::openAttendanceSession($this->schedule, 30, 15);

    // Peserta terverifikasi
    $this->peserta = User::factory()->peserta()->create([
        'name' => 'Cut Sarah Meutia, S.IP',
        'nip' => '199604152019032005',
    ]);

    $this->enrollment = CourseUser::create([
        'user_id' => $this->peserta->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-2026-0001',
        'status' => 'verified',
    ]);

    // Peserta lain yang belum terdaftar / belum diverifikasi
    $this->unverifiedPeserta = User::factory()->peserta()->create([
        'name' => 'M. Rizki Ramadhan',
        'nip' => '199801012020121001',
    ]);
});

test('guest is redirected to login when accessing presensi page', function () {
    $this->get(route('presensi.index'))
        ->assertRedirect(route('login'));
});

test('authenticated peserta can access presensi page', function () {
    $this->actingAs($this->peserta)
        ->get(route('presensi.index'))
        ->assertStatus(200)
        ->assertSee('Presensi Elektronik Pelatihan ASN')
        ->assertSee($this->peserta->name);
});

test('peserta can preview active session with valid token', function () {
    Livewire::actingAs($this->peserta)
        ->test(PresensiIndex::class)
        ->set('token', $this->token)
        ->assertSet('previewScheduleId', $this->schedule->id)
        ->assertSet('isVerifiedForCourse', true)
        ->assertSee($this->schedule->session_title)
        ->assertSee($this->course->title);
});

test('peserta can check in successfully using manual token', function () {
    Livewire::actingAs($this->peserta)
        ->test(PresensiIndex::class)
        ->set('token', $this->token)
        ->call('submitPresensi')
        ->assertSet('errorMessage', '')
        ->assertSee('Presensi Berhasil Dicatat!')
        ->assertSee('Hadir Tepat Waktu');

    $this->assertDatabaseHas('attendances', [
        'schedule_id' => $this->schedule->id,
        'user_id' => $this->peserta->id,
        'status' => 'hadir',
        'method' => 'token',
    ]);
});

test('peserta scanning QR code automatically captures token and records method as qr', function () {
    Livewire::actingAs($this->peserta)
        ->withQueryParams(['token' => $this->token])
        ->test(PresensiIndex::class)
        ->assertSet('token', $this->token)
        ->assertSet('method', 'qr')
        ->assertSet('previewScheduleId', $this->schedule->id)
        ->call('submitPresensi')
        ->assertSet('errorMessage', '')
        ->assertSee('Scan QR Code');

    $this->assertDatabaseHas('attendances', [
        'schedule_id' => $this->schedule->id,
        'user_id' => $this->peserta->id,
        'status' => 'hadir',
        'method' => 'qr',
    ]);
});

test('peserta checking in after late threshold is marked as terlambat', function () {
    // Geser waktu pembukaan token ke 20 menit yang lalu (lewat ambang toleransi 15 menit)
    $this->schedule->update([
        'token_opened_at' => now()->subMinutes(20),
        'late_threshold_minutes' => 15,
        'token_expires_at' => now()->addMinutes(10),
    ]);

    Livewire::actingAs($this->peserta)
        ->test(PresensiIndex::class)
        ->set('token', $this->token)
        ->call('submitPresensi')
        ->assertSet('errorMessage', '')
        ->assertSee('Hadir Terlambat');

    $this->assertDatabaseHas('attendances', [
        'schedule_id' => $this->schedule->id,
        'user_id' => $this->peserta->id,
        'status' => 'terlambat',
    ]);
});

test('unverified peserta cannot check in and receives friendly error message', function () {
    Livewire::actingAs($this->unverifiedPeserta)
        ->test(PresensiIndex::class)
        ->set('token', $this->token)
        ->assertSet('isVerifiedForCourse', false)
        ->call('submitPresensi')
        ->assertSee('Anda tidak terdaftar sebagai peserta terverifikasi pada pelatihan ini.');

    $this->assertDatabaseMissing('attendances', [
        'schedule_id' => $this->schedule->id,
        'user_id' => $this->unverifiedPeserta->id,
    ]);
});

test('peserta cannot check in with invalid or expired token', function () {
    Livewire::actingAs($this->peserta)
        ->test(PresensiIndex::class)
        ->set('token', '999999')
        ->assertSet('previewScheduleId', null)
        ->call('submitPresensi')
        ->assertSee('Kode token salah atau masa berlaku sesi absensi telah kedaluwarsa.');

    // Tutup sesi absensi lalu coba dengan token asli
    AbsensiRepo::closeAttendanceSession($this->schedule);

    Livewire::actingAs($this->peserta)
        ->test(PresensiIndex::class)
        ->set('token', $this->token)
        ->assertSet('previewScheduleId', null)
        ->call('submitPresensi')
        ->assertSee('Kode token salah atau masa berlaku sesi absensi telah kedaluwarsa.');
});

test('peserta cannot check in twice for the same session', function () {
    Attendance::create([
        'schedule_id' => $this->schedule->id,
        'user_id' => $this->peserta->id,
        'status' => 'hadir',
        'method' => 'token',
        'check_in_at' => now(),
    ]);

    Livewire::actingAs($this->peserta)
        ->test(PresensiIndex::class)
        ->set('token', $this->token)
        ->assertNotSet('existingAttendance', null)
        ->call('submitPresensi')
        ->assertSee('Anda sudah tercatat melakukan presensi untuk sesi ini');

    expect(Attendance::where('schedule_id', $this->schedule->id)->where('user_id', $this->peserta->id)->count())->toBe(1);
});

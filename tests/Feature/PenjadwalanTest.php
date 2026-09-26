<?php

use App\Livewire\Admin\Penjadwalan\PenjadwalanCreate;
use App\Livewire\Admin\Penjadwalan\PenjadwalanData;
use App\Livewire\Admin\Penjadwalan\PenjadwalanEdit;
use App\Models\Attendance;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\User;
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
        'type' => 'batch',
        'method' => 'luring',
        'quota' => 30,
        'status' => 'published',
    ]);

    $this->admin = User::factory()->admin()->create();
    $this->mentor = User::factory()->mentor()->create([
        'name' => 'Dr. Cut Meutia, M.Si',
        'nip' => '197905122003122001',
    ]);
});

test('unauthorized users cannot access penjadwalan routes', function () {
    // Guest
    $this->get(route('penjadwalan.data'))
        ->assertRedirect(route('login'));
    $this->get(route('penjadwalan.create'))
        ->assertRedirect(route('login'));

    // Peserta
    $peserta = User::factory()->peserta()->create();
    $this->actingAs($peserta)
        ->get(route('penjadwalan.data'))
        ->assertStatus(403);
    $this->actingAs($peserta)
        ->get(route('penjadwalan.create'))
        ->assertStatus(403);
});

test('admin and mentor can access penjadwalan pages', function () {
    $this->actingAs($this->admin)
        ->get(route('penjadwalan.data'))
        ->assertStatus(200)
        ->assertSee('Tahap 4: Penjadwalan Sesi Pelatihan')
        ->assertSee('Tambah Jadwal Sesi');

    $this->actingAs($this->admin)
        ->get(route('penjadwalan.create'))
        ->assertStatus(200)
        ->assertSee('Tambah Jadwal Sesi Baru')
        ->assertSee('Formulir Jadwal Sesi Pelatihan');

    $this->actingAs($this->mentor)
        ->get(route('penjadwalan.data'))
        ->assertStatus(200);
});

test('penjadwalan datatable endpoint returns valid yajra json response', function () {
    CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Pengenalan Arsitektur SPBE Pemerintah Daerah',
        'session_date' => '2026-10-05',
        'start_time' => '09:00',
        'end_time' => '11:30',
        'room_or_link' => 'Aula Gedung Diklat BKPSDM',
        'status' => 'scheduled',
        'created_by' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('penjadwalan.dt'));

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
                    'location_badge',
                    'status_badge',
                ],
            ],
        ])
        ->assertJsonFragment([
            'session_title' => 'Pengenalan Arsitektur SPBE Pemerintah Daerah',
            'course_title' => e($this->course->title),
            'mentor_name' => $this->mentor->name,
        ]);
});

test('admin can create new schedule session via PenjadwalanCreate page', function () {
    $this->actingAs($this->admin);

    Livewire::test(PenjadwalanCreate::class)
        ->set('form.course_id', $this->course->id)
        ->set('form.mentor_id', $this->mentor->id)
        ->set('form.session_title', 'Studi Kasus Transformasi Digital Layanan Kepegawaian')
        ->set('form.session_date', '2026-10-12')
        ->set('form.start_time', '08:30')
        ->set('form.end_time', '11:00')
        ->set('form.room_or_link', 'Ruang Rapat Utama BKPSDM')
        ->set('form.status', 'scheduled')
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('penjadwalan.data'));

    $this->assertDatabaseHas('course_schedules', [
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Studi Kasus Transformasi Digital Layanan Kepegawaian',
        'session_date' => '2026-10-12',
        'room_or_link' => 'Ruang Rapat Utama BKPSDM',
        'status' => 'scheduled',
    ]);
});

test('schedule creation rejects clashing mentor schedule (anti-bentrok)', function () {
    $this->actingAs($this->admin);

    // Existing session for this mentor: 09:00 - 11:00 on 2026-10-15
    CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Sesi Teori Dasar SPBE',
        'session_date' => '2026-10-15',
        'start_time' => '09:00',
        'end_time' => '11:00',
        'room_or_link' => 'Ruang Teori A',
        'status' => 'scheduled',
        'created_by' => $this->admin->id,
    ]);

    // Attempting to schedule same mentor overlapping: 10:00 - 12:00 on same date
    Livewire::test(PenjadwalanCreate::class)
        ->set('form.course_id', $this->course->id)
        ->set('form.mentor_id', $this->mentor->id)
        ->set('form.session_title', 'Sesi Praktik Bertabrakan')
        ->set('form.session_date', '2026-10-15')
        ->set('form.start_time', '10:00')
        ->set('form.end_time', '12:00')
        ->set('form.room_or_link', 'Ruang Lab B')
        ->call('formSubmit')
        ->assertHasErrors(['form.start_time']);

    $this->assertDatabaseMissing('course_schedules', [
        'session_title' => 'Sesi Praktik Bertabrakan',
    ]);
});

test('schedule creation rejects clashing physical room schedule (anti-bentrok)', function () {
    $this->actingAs($this->admin);

    $mentorLain = User::factory()->mentor()->create(['name' => 'Mentor Kedua']);

    // Existing session in Aula: 08:00 - 10:00 on 2026-10-20
    CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Sesi Pembukaan Diklat',
        'session_date' => '2026-10-20',
        'start_time' => '08:00',
        'end_time' => '10:00',
        'room_or_link' => 'Aula BKPSDM',
        'status' => 'scheduled',
        'created_by' => $this->admin->id,
    ]);

    // Another mentor attempting to book same physical room: 09:00 - 11:30 on same date
    Livewire::test(PenjadwalanCreate::class)
        ->set('form.course_id', $this->course->id)
        ->set('form.mentor_id', $mentorLain->id)
        ->set('form.session_title', 'Sesi Lain di Ruang Sama')
        ->set('form.session_date', '2026-10-20')
        ->set('form.start_time', '09:00')
        ->set('form.end_time', '11:30')
        ->set('form.room_or_link', 'Aula BKPSDM')
        ->call('formSubmit')
        ->assertHasErrors(['form.start_time']);

    $this->assertDatabaseMissing('course_schedules', [
        'session_title' => 'Sesi Lain di Ruang Sama',
    ]);
});

test('admin can access edit page and update existing schedule session via PenjadwalanEdit', function () {
    $this->actingAs($this->admin);

    $schedule = CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Judul Awal',
        'session_date' => '2026-10-25',
        'start_time' => '08:00',
        'end_time' => '10:00',
        'room_or_link' => 'Ruang 1',
        'status' => 'scheduled',
        'created_by' => $this->admin->id,
    ]);

    $this->get(route('penjadwalan.edit', $schedule->id))
        ->assertStatus(200)
        ->assertSee('Edit Jadwal Sesi Pelatihan')
        ->assertSee('Formulir Edit Jadwal Sesi');

    Livewire::test(PenjadwalanEdit::class, ['id' => $schedule->id])
        ->assertSet('form.session_title', 'Judul Awal')
        ->set('form.session_title', 'Judul Sesi Terkoreksi')
        ->set('form.room_or_link', 'https://zoom.us/j/999888777')
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('penjadwalan.data'));

    $this->assertDatabaseHas('course_schedules', [
        'id' => $schedule->id,
        'session_title' => 'Judul Sesi Terkoreksi',
        'room_or_link' => 'https://zoom.us/j/999888777',
    ]);
});

test('admin can delete schedule session via universal delete hook', function () {
    $this->actingAs($this->admin);

    $schedule = CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Sesi yang Akan Dihapus',
        'session_date' => '2026-10-30',
        'start_time' => '08:00',
        'end_time' => '10:00',
        'room_or_link' => 'Ruang 2',
        'status' => 'scheduled',
        'created_by' => $this->admin->id,
    ]);

    Livewire::test(PenjadwalanData::class)
        ->call('hookModalDelete', $schedule->id, $schedule->session_title)
        ->assertDispatched('modal-delete-setDeleteId')
        ->call('delete', ['id' => $schedule->id])
        ->assertDispatched('closeModal', id: 'modalDelete')
        ->assertDispatched('reloadDT');

    $this->assertDatabaseMissing('course_schedules', [
        'id' => $schedule->id,
    ]);
});

test('schedule creation rejects session date outside course period for batch courses', function () {
    $this->actingAs($this->admin);

    $batchCourse = Course::create([
        'code' => 'BATCH-PERIOD-01',
        'title' => 'Diklat Kepemimpinan Batch 1',
        'category_id' => $this->category->id,
        'type' => 'batch',
        'method' => 'luring',
        'quota' => 20,
        'status' => 'published',
        'start_date' => '2026-11-01',
        'end_date' => '2026-11-10',
    ]);

    // Session date 2026-11-15 is after end_date 2026-11-10
    Livewire::test(PenjadwalanCreate::class)
        ->set('form.course_id', $batchCourse->id)
        ->set('form.mentor_id', $this->mentor->id)
        ->set('form.session_title', 'Sesi di Luar Periode Diklat')
        ->set('form.session_date', '2026-11-15')
        ->set('form.start_time', '09:00')
        ->set('form.end_time', '11:00')
        ->set('form.room_or_link', 'Ruang A')
        ->call('formSubmit')
        ->assertHasErrors(['form.session_date']);

    $this->assertDatabaseMissing('course_schedules', [
        'session_title' => 'Sesi di Luar Periode Diklat',
    ]);
});

test('schedule creation rejects draft unapproved course', function () {
    $this->actingAs($this->admin);

    $draftCourse = Course::create([
        'code' => 'DRAFT-COURSE-01',
        'title' => 'Diklat Belum Disetujui Pimpinan',
        'category_id' => $this->category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 20,
        'status' => 'draft',
    ]);

    Livewire::test(PenjadwalanCreate::class)
        ->set('form.course_id', $draftCourse->id)
        ->set('form.mentor_id', $this->mentor->id)
        ->set('form.session_title', 'Sesi Kursus Draft')
        ->set('form.session_date', '2026-11-05')
        ->set('form.start_time', '09:00')
        ->set('form.end_time', '11:00')
        ->set('form.room_or_link', 'https://zoom.us')
        ->call('formSubmit')
        ->assertHasErrors(['form.course_id']);
});

test('admin can cancel schedule session with recorded reason', function () {
    $this->actingAs($this->admin);

    $schedule = CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Sesi yang Dibatalkan',
        'session_date' => '2026-10-28',
        'start_time' => '08:00',
        'end_time' => '10:00',
        'room_or_link' => 'Ruang 3',
        'status' => 'scheduled',
        'created_by' => $this->admin->id,
    ]);

    Livewire::test(PenjadwalanData::class)
        ->call('openCancelModal', $schedule->id, $schedule->session_title)
        ->assertSet('cancelScheduleId', $schedule->id)
        ->set('cancellationReason', 'Narasumber ditugaskan dinas luar mendadak oleh Kepala Dinas.')
        ->call('submitCancel')
        ->assertHasNoErrors()
        ->assertDispatched('closeModal', id: 'modalCancelSchedule')
        ->assertDispatched('reloadDT');

    $updated = $schedule->fresh();
    expect($updated->status)->toBe('cancelled');
    expect($updated->cancellation_reason)->toBe('Narasumber ditugaskan dinas luar mendadak oleh Kepala Dinas.');
});

test('schedule cannot be permanently deleted when attendances exist', function () {
    $this->actingAs($this->admin);

    $schedule = CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Sesi dengan Absensi',
        'session_date' => '2026-10-29',
        'start_time' => '08:00',
        'end_time' => '10:00',
        'room_or_link' => 'Ruang 4',
        'status' => 'completed',
        'attendance_token' => '123456',
        'created_by' => $this->admin->id,
    ]);

    $peserta = User::factory()->peserta()->create();
    Attendance::create([
        'schedule_id' => $schedule->id,
        'user_id' => $peserta->id,
        'status' => 'hadir',
        'check_in_at' => now(),
    ]);

    Livewire::test(PenjadwalanData::class)
        ->call('delete', ['id' => $schedule->id])
        ->assertDispatched('alert-show', function ($event, $params) {
            return $params['data']['type'] === 'warning' && str_contains($params['data']['message'], 'tidak dapat dihapus permanen');
        });

    $this->assertDatabaseHas('course_schedules', [
        'id' => $schedule->id,
    ]);
});

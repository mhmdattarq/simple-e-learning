<?php

use App\Livewire\Landing\PelatihanIndex;
use App\Livewire\Peserta\Materi\MateriBelajar;
use App\Models\Attendance;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\CourseUser;
use App\Models\Lesson;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
    $this->category = Category::first();

    $this->admin = User::factory()->admin()->create();
    $this->mentor = User::factory()->mentor()->create([
        'name' => 'Dr. Irfan, S.T.',
    ]);
    $this->peserta = User::factory()->peserta()->create([
        'name' => 'Ahmad Peserta',
        'nip' => '199501012022011001',
    ]);

    $this->course = Course::create([
        'code' => 'TIK-2026-WEB',
        'title' => 'Pelatihan Web GIS Kabupaten Aceh Timur',
        'category_id' => $this->category->id,
        'type' => 'batch',
        'method' => 'luring',
        'quota' => 25,
        'status' => 'published',
        'created_by' => $this->admin->id,
    ]);

    $this->schedule1 = CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Sesi 1: Pengenalan Web GIS & Spatial Data',
        'session_date' => now()->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '11:00:00',
        'room_or_link' => 'Lab Komputer 1',
        'attendance_token' => '112233',
        'is_attendance_open' => true,
        'token_expires_at' => now()->addHours(2),
        'status' => 'ongoing',
    ]);

    $this->schedule2 = CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Sesi 2: Pengolahan Peta & Geoserver',
        'session_date' => now()->addDay()->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '11:00:00',
        'room_or_link' => 'Lab Komputer 2',
        'attendance_token' => '445566',
        'is_attendance_open' => false,
        'status' => 'scheduled',
    ]);
});

test('guest sees Daftar Pelatihan button on catalog', function () {
    $this->get(route('pelatihan.index'))
        ->assertStatus(200)
        ->assertSee('Daftar Pelatihan')
        ->assertSee(route('pelatihan.daftar', $this->course->id));
});

test('logged in user not enrolled sees Daftar Pelatihan button', function () {
    Livewire::actingAs($this->peserta)
        ->test(PelatihanIndex::class)
        ->assertSee('Daftar Pelatihan')
        ->assertSee(route('pelatihan.daftar', $this->course->id));
});

test('enrolled user with pending status sees Menunggu Verifikasi', function () {
    CourseUser::create([
        'user_id' => $this->peserta->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-2026-0001',
        'status' => 'pending',
    ]);

    Livewire::actingAs($this->peserta)
        ->test(PelatihanIndex::class)
        ->assertSee('Menunggu Verifikasi')
        ->assertDontSee('Absen Sesi');
});

test('verified participant who has not attended sees Absen Sesi button', function () {
    CourseUser::create([
        'user_id' => $this->peserta->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-2026-0001',
        'status' => 'verified',
    ]);

    Livewire::actingAs($this->peserta)
        ->test(PelatihanIndex::class)
        ->assertSee('Absen Sesi')
        ->assertSee(route('presensi.index', ['token' => '112233']))
        ->assertDontSee('Akses Materi');
});

test('verified participant who attended session sees Akses Materi button', function () {
    CourseUser::create([
        'user_id' => $this->peserta->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-2026-0001',
        'status' => 'verified',
    ]);

    // Record attendance for session 1
    Attendance::create([
        'schedule_id' => $this->schedule1->id,
        'user_id' => $this->peserta->id,
        'status' => 'hadir',
        'check_in_at' => now(),
        'method' => 'token',
    ]);

    // Close session 1 attendance so it does not ask for attendance again
    $this->schedule1->update(['is_attendance_open' => false]);

    Livewire::actingAs($this->peserta)
        ->test(PelatihanIndex::class)
        ->assertSee('Akses Materi')
        ->assertSee(route('peserta.materi', $this->course->id));
});

test('unverified user cannot access peserta materi learning room', function () {
    $this->actingAs($this->peserta)
        ->get(route('peserta.materi', $this->course->id))
        ->assertStatus(403);
});

test('verified participant can access peserta materi room with session attendance gating', function () {
    CourseUser::create([
        'user_id' => $this->peserta->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-2026-0001',
        'status' => 'verified',
    ]);

    // Create Chapter & Lesson for Sesi 1
    $chapter1 = Chapter::create([
        'course_id' => $this->course->id,
        'schedule_id' => $this->schedule1->id,
        'title' => 'Bab 1: Konsep Dasar GIS',
        'order' => 1,
    ]);

    $lesson1 = Lesson::create([
        'chapter_id' => $chapter1->id,
        'title' => 'Pengenalan Shapefile dan GeoJSON',
        'order' => 1,
        'content_type' => 'article',
        'body_text' => 'Konten penjelasan materi Web GIS untuk ASN.',
    ]);

    // Create Chapter & Lesson for Sesi 2
    $chapter2 = Chapter::create([
        'course_id' => $this->course->id,
        'schedule_id' => $this->schedule2->id,
        'title' => 'Bab 2: Setup Geoserver & Layer',
        'order' => 1,
    ]);

    $lesson2 = Lesson::create([
        'chapter_id' => $chapter2->id,
        'title' => 'Publish WMS ke Portal',
        'order' => 1,
        'content_type' => 'article',
        'body_text' => 'Materi tingkat lanjut Sesi 2.',
    ]);

    // Participant attended Sesi 1 only
    Attendance::create([
        'schedule_id' => $this->schedule1->id,
        'user_id' => $this->peserta->id,
        'status' => 'hadir',
        'check_in_at' => now(),
    ]);

    // Component renders successfully
    Livewire::actingAs($this->peserta)
        ->test(MateriBelajar::class, ['id' => $this->course->id])
        ->assertStatus(200)
        ->assertSee('Bab 1: Konsep Dasar GIS')
        ->assertSee('Pengenalan Shapefile dan GeoJSON')
        ->assertSee('Hadir (Terbuka)')
        ->assertSee('Belum Absen (Terkunci)')
        ->assertDontSee('Portal Presensi') // Sembunyi karena peserta sudah melakukan absensi
        ->call('selectLesson', $lesson2->id) // Attempt to select locked Sesi 2 lesson
        ->assertDispatched('show-toast') // Warning dispatched
        ->call('toggleCompleteLesson', $lesson1->id); // Can complete unlocked Sesi 1 lesson

    // Verify progress recorded
    $this->assertDatabaseHas('lesson_user', [
        'user_id' => $this->peserta->id,
        'lesson_id' => $lesson1->id,
        'is_completed' => 1,
    ]);
});

test('portal presensi button is hidden in materi belajar when user has attended', function () {
    CourseUser::create([
        'user_id' => $this->peserta->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-2026-0002',
        'status' => 'verified',
    ]);

    // Close active attendance on schedule 1
    $this->schedule1->update(['is_attendance_open' => false]);

    // Belum ada attendance sama sekali -> Portal Presensi muncul
    Livewire::actingAs($this->peserta)
        ->test(MateriBelajar::class, ['id' => $this->course->id])
        ->assertSee('Portal Presensi');

    // Peserta melakukan absensi
    Attendance::create([
        'schedule_id' => $this->schedule1->id,
        'user_id' => $this->peserta->id,
        'status' => 'hadir',
        'check_in_at' => now(),
    ]);

    // Setelah absen -> tombol Portal Presensi HILANG
    Livewire::actingAs($this->peserta)
        ->test(MateriBelajar::class, ['id' => $this->course->id])
        ->assertDontSee('Portal Presensi');
});

test('participant follows sequential lesson flow with Sebelumnya and Selanjutnya buttons', function () {
    CourseUser::create([
        'user_id' => $this->peserta->id,
        'course_id' => $this->course->id,
        'registration_number' => 'REG-2026-0003',
        'status' => 'verified',
    ]);

    Attendance::create([
        'schedule_id' => $this->schedule1->id,
        'user_id' => $this->peserta->id,
        'status' => 'hadir',
        'check_in_at' => now(),
    ]);

    $chapter1 = Chapter::create([
        'course_id' => $this->course->id,
        'schedule_id' => $this->schedule1->id,
        'title' => 'Bab 1: Pengenalan Komputasi Awan',
        'order' => 1,
    ]);

    $lesson1 = Lesson::create([
        'chapter_id' => $chapter1->id,
        'title' => 'Materi 1.1: Konsep Cloud',
        'order' => 1,
        'content_type' => 'article',
        'body_text' => '<p>Konten Materi 1.1</p>',
    ]);

    $lesson2 = Lesson::create([
        'chapter_id' => $chapter1->id,
        'title' => 'Materi 1.2: Model Layanan IaaS PaaS SaaS',
        'order' => 2,
        'content_type' => 'article',
        'body_text' => '<p>Konten Materi 1.2</p>',
    ]);

    $chapter2 = Chapter::create([
        'course_id' => $this->course->id,
        'schedule_id' => $this->schedule1->id,
        'title' => 'Bab 2: Arsitektur Cloud ASN',
        'order' => 2,
    ]);

    $lesson3 = Lesson::create([
        'chapter_id' => $chapter2->id,
        'title' => 'Materi 2.1: Desain Keamanan',
        'order' => 1,
        'content_type' => 'article',
        'body_text' => '<p>Konten Materi 2.1</p>',
    ]);

    // Peserta pertama kali masuk: otomatis membuka lesson1
    $component = Livewire::actingAs($this->peserta)
        ->test(MateriBelajar::class, ['id' => $this->course->id])
        ->assertSet('selectedLessonId', $lesson1->id)
        ->assertSee('Sebelumnya')
        ->assertSee('Selanjutnya')
        ->assertDontSee('Tandai Selesai Belajar');

    // Peserta tidak bisa langsung loncat ke materi 1.2 atau 2.1 karena terkunci
    $component->call('selectLesson', $lesson2->id)
        ->assertDispatched('show-toast')
        ->assertSet('selectedLessonId', $lesson1->id);

    // Klik Selanjutnya -> Materi 1.1 ditandai selesai dan otomatis pindah ke Materi 1.2
    $component->call('nextLesson')
        ->assertSet('selectedLessonId', $lesson2->id)
        ->assertSee('Sebelumnya')
        ->assertSee('Tandai Selesai Belajar'); // Karena lesson 2 adalah materi terakhir di Bab 1

    // Verifikasi lesson 1 selesai di database
    $this->assertDatabaseHas('lesson_user', [
        'user_id' => $this->peserta->id,
        'lesson_id' => $lesson1->id,
        'is_completed' => 1,
    ]);

    // Peserta bisa kembali ke materi sebelumnya (Materi 1.1)
    $component->call('previousLesson')
        ->assertSet('selectedLessonId', $lesson1->id);

    // Dari materi 1.1, klik Selanjutnya kembali ke materi 1.2
    $component->call('nextLesson')
        ->assertSet('selectedLessonId', $lesson2->id);

    // Di materi 1.2 (akhir bab 1), klik Tandai Selesai Belajar memunculkan modal
    $component->call('promptCompleteChapter')
        ->assertSet('showCompleteModal', true)
        ->assertSee('Konfirmasi Selesai Bab')
        ->assertSee('Apakah Anda yakin menandai bab');

    // Konfirmasi selesai bab -> bab 1 tuntas dan otomatis maju ke Materi 2.1 (Bab 2)
    $component->call('confirmCompleteChapter')
        ->assertSet('showCompleteModal', false)
        ->assertSet('selectedLessonId', $lesson3->id)
        ->assertSee('Materi 2.1: Desain Keamanan');

    // Verifikasi kedua materi di Bab 1 selesai
    $this->assertDatabaseHas('lesson_user', [
        'user_id' => $this->peserta->id,
        'lesson_id' => $lesson2->id,
        'is_completed' => 1,
    ]);
});

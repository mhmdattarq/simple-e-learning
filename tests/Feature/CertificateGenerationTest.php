<?php

use App\Livewire\Peserta\Evaluasi\QuizKerjakan;
use App\Models\Category;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Repositories\CertificateRepo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::factory()->create();

    $this->course = Course::factory()->create([
        'category_id' => $this->category->id,
        'title' => 'Pelatihan Transformasi Digital ASN',
        'jam_pelajaran' => 32,
        'status' => 'published',
    ]);

    $this->finalQuiz = Quiz::factory()->create([
        'course_id' => $this->course->id,
        'title' => 'Evaluasi Akhir Kelas',
        'type' => 'final',
        'passing_score' => 70,
        'total_score' => 100,
    ]);

    $this->user = User::factory()->create([
        'name' => 'Ahmad Dahlan, S.STP',
        'role' => 'peserta',
    ]);
});

test('tamu / guest tidak dapat mengunduh sertifikat dan diarahkan ke login', function () {
    $response = $this->get(route('peserta.sertifikat.unduh-pdf', $this->course));
    $response->assertRedirect(route('login'));

    $responseImg = $this->get(route('peserta.sertifikat.unduh-gambar', $this->course));
    $responseImg->assertRedirect(route('login'));
});

test('peserta yang belum lulus evaluasi akhir mendapatkan response 403 forbidden', function () {
    $this->actingAs($this->user);

    // Belum pernah mengerjakan ujian
    $response = $this->get(route('peserta.sertifikat.unduh-pdf', $this->course));
    $response->assertStatus(403);

    // Mengerjakan ujian tetapi tidak lulus (skor di bawah KKM)
    QuizAttempt::create([
        'quiz_id' => $this->finalQuiz->id,
        'user_id' => $this->user->id,
        'total_earned_score' => 50,
        'total_possible_score' => 100,
        'percentage' => 50.0,
        'is_passed' => false,
        'submitted_at' => now(),
    ]);

    $responseFailed = $this->get(route('peserta.sertifikat.unduh-pdf', $this->course));
    $responseFailed->assertStatus(403);

    $responseImgFailed = $this->get(route('peserta.sertifikat.unduh-gambar', $this->course));
    $responseImgFailed->assertStatus(403);
});

test('peserta yang lulus evaluasi akhir dapat mengunduh sertifikat PDF standar A4 Landscape', function () {
    $this->actingAs($this->user);

    QuizAttempt::create([
        'quiz_id' => $this->finalQuiz->id,
        'user_id' => $this->user->id,
        'total_earned_score' => 95,
        'total_possible_score' => 100,
        'percentage' => 95.0,
        'is_passed' => true,
        'submitted_at' => now(),
    ]);

    $response = $this->get(route('peserta.sertifikat.unduh-pdf', $this->course));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
    expect($response->getContent())->toStartWith('%PDF-1.4');
    expect(strlen($response->getContent()))->toBeGreaterThan(100000);
});

test('peserta yang lulus evaluasi akhir dapat mengunduh sertifikat PNG resolusi tinggi', function () {
    $this->actingAs($this->user);

    QuizAttempt::create([
        'quiz_id' => $this->finalQuiz->id,
        'user_id' => $this->user->id,
        'total_earned_score' => 90,
        'total_possible_score' => 100,
        'percentage' => 90.0,
        'is_passed' => true,
        'submitted_at' => now(),
    ]);

    $response = $this->get(route('peserta.sertifikat.unduh-gambar', $this->course));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'image/png');
    // PNG file signature: 89 50 4E 47 0D 0A 1A 0A
    expect($response->getContent())->toStartWith("\x89PNG\r\n\x1a\n");
});

test('peserta yang lulus dapat membuka pratinjau sertifikat di browser', function () {
    $this->actingAs($this->user);

    QuizAttempt::create([
        'quiz_id' => $this->finalQuiz->id,
        'user_id' => $this->user->id,
        'total_earned_score' => 85,
        'total_possible_score' => 100,
        'percentage' => 85.0,
        'is_passed' => true,
        'submitted_at' => now(),
    ]);

    $response = $this->get(route('peserta.sertifikat.preview', $this->course));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
    expect($response->headers->get('Content-Disposition'))->toContain('inline');
});

test('admin dapat mengunduh sertifikat kelas untuk keperluan pratinjau tanpa harus lulus kuis', function () {
    $admin = User::factory()->create([
        'name' => 'Super Administrator',
        'role' => 'admin',
    ]);

    $this->actingAs($admin);

    $response = $this->get(route('peserta.sertifikat.unduh-pdf', $this->course));
    $response->assertStatus(200);
    expect($response->getContent())->toStartWith('%PDF-1.4');
});

test('CertificateRepo menghasilkan binary PDF dan PNG secara mandiri dengan nama file slugified', function () {
    $filenamePdf = CertificateRepo::getFilename($this->course, $this->user, 'pdf');
    $filenamePng = CertificateRepo::getFilename($this->course, $this->user, 'png');

    expect($filenamePdf)->toBe('sertifikat-pelatihan-transformasi-digital-asn-ahmad-dahlan-sstp.pdf');
    expect($filenamePng)->toBe('sertifikat-pelatihan-transformasi-digital-asn-ahmad-dahlan-sstp.png');

    $png = CertificateRepo::generatePng($this->course, $this->user);
    $pdf = CertificateRepo::generatePdf($this->course, $this->user);

    expect($png)->toStartWith("\x89PNG\r\n\x1a\n");
    expect($pdf)->toStartWith('%PDF-1.4');
});

test('komponen livewire QuizKerjakan menampilkan banner unduh sertifikat saat peserta lulus final quiz', function () {
    $attempt = QuizAttempt::create([
        'quiz_id' => $this->finalQuiz->id,
        'user_id' => $this->user->id,
        'total_earned_score' => 90,
        'total_possible_score' => 100,
        'percentage' => 90.0,
        'is_passed' => true,
        'submitted_at' => now(),
    ]);

    Livewire::actingAs($this->user)
        ->test(QuizKerjakan::class, ['course' => $this->course, 'quiz' => $this->finalQuiz])
        ->assertSee('Selamat! Sertifikat Kelulusan Anda Telah Terbit')
        ->assertSee('Unduh PDF')
        ->assertSee('Unduh Gambar (PNG)')
        ->assertSee(route('peserta.sertifikat.unduh-pdf', $this->course))
        ->assertSee(route('peserta.sertifikat.unduh-gambar', $this->course));
});

test('admin dapat mengunduh sertifikat atas nama peserta tertentu melalui parameter user_id', function () {
    $admin = User::factory()->create([
        'name' => 'Super Administrator',
        'role' => 'admin',
    ]);

    $response = $this->actingAs($admin)->get(route('peserta.sertifikat.unduh-pdf', [
        'course' => $this->course,
        'user_id' => $this->user->id,
    ]));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
    expect($response->headers->get('Content-Disposition'))->toContain('ahmad-dahlan-sstp');
});

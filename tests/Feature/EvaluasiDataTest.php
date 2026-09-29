<?php

use App\Livewire\Admin\Evaluasi\EvaluasiData;
use App\Livewire\Admin\Evaluasi\EvaluasiDetail;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::factory()->create(['name' => 'Manajemen ASN']);
    $this->course1 = Course::factory()->create([
        'category_id' => $this->category->id,
        'title' => 'Dasar Manajemen ASN',
        'type' => 'permanent',
        'status' => 'published',
    ]);
    $this->course2 = Course::factory()->create([
        'category_id' => $this->category->id,
        'title' => 'Kepemimpinan Administrator',
        'type' => 'batch',
        'status' => 'published',
    ]);

    $this->chapter1 = Chapter::create(['course_id' => $this->course1->id, 'title' => 'Bab 1: Kebijakan', 'order' => 1]);
    $this->chapter2 = Chapter::create(['course_id' => $this->course2->id, 'title' => 'Bab 1: Inovasi', 'order' => 1]);

    $this->admin = User::factory()->admin()->create();
    $this->peserta = User::factory()->peserta()->create();

    // Create Quiz 1: Chapter Quiz in Course 1
    $this->quiz1 = Quiz::factory()->chapterQuiz($this->chapter1)->create([
        'course_id' => $this->course1->id,
        'chapter_id' => $this->chapter1->id,
        'title' => 'Kuis Kebijakan Publik',
        'created_by' => $this->admin->id,
        'total_score' => 20,
    ]);
    $q1 = QuizQuestion::factory()->create(['quiz_id' => $this->quiz1->id, 'score' => 20, 'question_text' => 'Apa itu ASN?']);
    QuizOption::factory()->correct()->create(['question_id' => $q1->id, 'option_text' => 'Aparatur Sipil Negara']);
    QuizOption::factory()->create(['question_id' => $q1->id, 'option_text' => 'Anggota Serikat Negara', 'is_correct' => false]);

    // Create Quiz 2: Final Quiz in Course 1
    $this->quiz2 = Quiz::factory()->finalQuiz()->create([
        'course_id' => $this->course1->id,
        'title' => 'Ujian Akhir Manajemen ASN',
        'created_by' => $this->admin->id,
        'total_score' => 50,
    ]);

    // Create Quiz 3: Chapter Quiz in Course 2
    $this->quiz3 = Quiz::factory()->chapterQuiz($this->chapter2)->create([
        'course_id' => $this->course2->id,
        'chapter_id' => $this->chapter2->id,
        'title' => 'Evaluasi Inovasi Sektor Publik',
        'created_by' => $this->admin->id,
        'total_score' => 15,
    ]);
    $q3 = QuizQuestion::factory()->create(['quiz_id' => $this->quiz3->id, 'score' => 15, 'question_text' => 'Apa inovasi sektor publik?']);

    // Attempt on Quiz 1
    QuizAttempt::factory()->create([
        'quiz_id' => $this->quiz1->id,
        'user_id' => $this->peserta->id,
        'total_earned_score' => 20,
        'total_possible_score' => 20,
        'percentage' => 100.0,
        'is_passed' => true,
    ]);
});

test('unauthorized users cannot access evaluasi routes', function () {
    // Guest redirected to login
    $this->get(route('evaluasi.data'))->assertRedirect(route('login'));
    $this->get(route('evaluasi.dt'))->assertRedirect(route('login'));

    // Peserta gets 403 Forbidden
    $this->actingAs($this->peserta)->get(route('evaluasi.data'))->assertForbidden();
    $this->actingAs($this->peserta)->get(route('evaluasi.dt'))->assertForbidden();
});

test('admin can access evaluasi master table page', function () {
    $response = $this->actingAs($this->admin)->get(route('evaluasi.data'));

    $response->assertOk();
    $response->assertSee('Data Evaluasi &amp; Kuis', false);
    $response->assertSee('Daftar Evaluasi Kelas');
    $response->assertSee('tableEvaluasi');

    Livewire::actingAs($this->admin)
        ->test(EvaluasiData::class)
        ->assertOk()
        ->assertSee('Daftar Evaluasi Kelas');
});

test('evaluasi datatable endpoint returns valid yajra json response with course quizzes metrics', function () {
    $response = $this->actingAs($this->admin)
        ->getJson(route('evaluasi.dt'));

    $response->assertOk();
    $response->assertJsonStructure([
        'draw',
        'recordsTotal',
        'recordsFiltered',
        'data',
    ]);

    $data = $response->json('data');
    expect($data)->toHaveCount(2);

    $course1Row = collect($data)->firstWhere('id', $this->course1->id);
    expect($course1Row)->not->toBeNull();
    expect($course1Row['title'])->toBe('Dasar Manajemen ASN');
    expect($course1Row['quizzes_count'])->toBe(2);
    expect($course1Row['chapter_quizzes_count'])->toBe(1);
    expect($course1Row['final_quiz_exists'])->toBeTrue();
    expect($course1Row['questions_count'])->toBe(1);
    expect($course1Row['attempts_count'])->toBe(1);

    $course2Row = collect($data)->firstWhere('id', $this->course2->id);
    expect($course2Row)->not->toBeNull();
    expect($course2Row['title'])->toBe('Kepemimpinan Administrator');
    expect($course2Row['quizzes_count'])->toBe(1);
    expect($course2Row['chapter_quizzes_count'])->toBe(1);
    expect($course2Row['final_quiz_exists'])->toBeFalse();
    expect($course2Row['questions_count'])->toBe(1);
    expect($course2Row['attempts_count'])->toBe(0);
});

test('admin can access evaluasi detail page', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('evaluasi.detail', $this->course1->id));

    $response->assertOk();
    $response->assertSeeLivewire(EvaluasiDetail::class);
    $response->assertSee('Dasar Manajemen ASN');
});

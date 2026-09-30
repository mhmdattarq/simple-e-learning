<?php

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
    $this->course = Course::factory()->create([
        'category_id' => $this->category->id,
        'title' => 'Tata Kelola Kepegawaian',
        'type' => 'batch',
        'status' => 'published',
    ]);

    $this->chapter1 = Chapter::create(['course_id' => $this->course->id, 'title' => 'Bab 1: Regulasi', 'order' => 1]);
    $this->admin = User::factory()->admin()->create();
    $this->peserta = User::factory()->peserta()->create();

    // Create Chapter Quiz
    $this->chapterQuiz = Quiz::factory()->chapterQuiz($this->chapter1)->create([
        'course_id' => $this->course->id,
        'chapter_id' => $this->chapter1->id,
        'title' => 'Kuis Regulasi Kepegawaian',
        'created_by' => $this->admin->id,
        'total_score' => 20,
        'passing_score' => 70,
    ]);
    $q1 = QuizQuestion::factory()->create(['quiz_id' => $this->chapterQuiz->id, 'score' => 20, 'question_text' => 'Apa dasar hukum ASN?']);
    QuizOption::factory()->correct()->create(['question_id' => $q1->id, 'option_text' => 'UU No 20 Tahun 2023']);
    QuizOption::factory()->create(['question_id' => $q1->id, 'option_text' => 'UU Ketenagakerjaan', 'is_correct' => false]);

    // Create Final Quiz
    $this->finalQuiz = Quiz::factory()->finalQuiz()->create([
        'course_id' => $this->course->id,
        'title' => 'Ujian Akhir Kelulusan',
        'created_by' => $this->admin->id,
        'total_score' => 100,
        'passing_score' => 80,
    ]);
    $q2 = QuizQuestion::factory()->create(['quiz_id' => $this->finalQuiz->id, 'score' => 50, 'question_text' => 'Bagaimana sistem merit diterapkan?']);
    QuizOption::factory()->correct()->create(['question_id' => $q2->id, 'option_text' => 'Berdasarkan kualifikasi, kompetensi, dan kinerja']);

    // Create Quiz Attempt
    $this->attempt = QuizAttempt::factory()->create([
        'quiz_id' => $this->chapterQuiz->id,
        'user_id' => $this->peserta->id,
        'total_earned_score' => 20,
        'total_possible_score' => 20,
        'percentage' => 100.0,
        'is_passed' => true,
        'submitted_at' => now(),
    ]);
});

test('unauthorized users cannot access evaluasi detail or attempts datatable', function () {
    // Guest
    $this->get(route('evaluasi.detail', $this->course->id))->assertRedirect(route('login'));
    $this->get(route('evaluasi.detail.dt', $this->course->id))->assertRedirect(route('login'));

    // Peserta
    $this->actingAs($this->peserta)->get(route('evaluasi.detail', $this->course->id))->assertForbidden();
    $this->actingAs($this->peserta)->get(route('evaluasi.detail.dt', $this->course->id))->assertForbidden();
});

test('admin can access evaluasi detail page and sees 4 monitoring cards and questions', function () {
    $response = $this->actingAs($this->admin)->get(route('evaluasi.detail', $this->course->id));

    $response->assertOk();
    $response->assertSee('Tata Kelola Kepegawaian');
    $response->assertSeeText('Total Evaluasi');
    $response->assertSeeText('Kuis Bab (Formatif)');
    $response->assertSeeText('Ujian Akhir (Sumatif)');
    $response->assertSeeText('Partisipasi Peserta');
    $response->assertSee('Kuis Regulasi Kepegawaian');
    $response->assertSee('Ujian Akhir Kelulusan');
    $response->assertSee('Apa dasar hukum ASN?');
    $response->assertSee('UU No 20 Tahun 2023');
    $response->assertSee('Kunci Jawaban');

    Livewire::actingAs($this->admin)
        ->test(EvaluasiDetail::class, ['course_id' => $this->course->id])
        ->assertOk()
        ->assertSee('Tata Kelola Kepegawaian');
});

test('evaluasi attempts datatable returns valid json with participant scores', function () {
    $response = $this->actingAs($this->admin)
        ->getJson(route('evaluasi.detail.dt', $this->course->id));

    $response->assertOk();
    $response->assertJsonStructure([
        'draw',
        'recordsTotal',
        'recordsFiltered',
        'data',
    ]);

    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect(html_entity_decode($data[0]['user']['name']))->toBe($this->peserta->name);
    expect($data[0]['quiz']['title'])->toBe('Kuis Regulasi Kepegawaian');
    expect($data[0]['is_passed'])->toBeTrue();
    expect($data[0]['total_earned_score'])->toBe(20);
});

test('evaluasi attempts datatable supports multiple column searches without ambiguous id error', function () {
    $searchName = explode(' ', $this->peserta->name)[0];
    $queryParams = [
        'draw' => 1,
        'columns' => [
            0 => ['data' => '', 'name' => '', 'searchable' => 'false', 'orderable' => 'false', 'search' => ['value' => '']],
            1 => ['data' => 'user.name', 'name' => 'user.name', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => $searchName]],
            2 => ['data' => 'quiz.title', 'name' => 'quiz.title', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => 'Regulasi']],
        ],
        'order' => [
            0 => ['column' => 1, 'dir' => 'asc'],
        ],
        'start' => 0,
        'length' => 10,
        'search' => ['value' => ''],
    ];

    $response = $this->actingAs($this->admin)
        ->getJson(route('evaluasi.detail.dt', $this->course->id).'?'.http_build_query($queryParams));

    $response->assertOk();
    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect(html_entity_decode($data[0]['user']['name']))->toBe($this->peserta->name);
});

test('admin can trigger hookModalDelete and delete quiz from detail page', function () {
    Livewire::actingAs($this->admin)
        ->test(EvaluasiDetail::class, ['course_id' => $this->course->id])
        ->call('hookModalDelete', $this->finalQuiz->id, $this->finalQuiz->title)
        ->assertDispatched('modal-delete-setDeleteId')
        ->call('delete', ['id' => $this->finalQuiz->id])
        ->assertDispatched('closeModal', id: 'modalDelete')
        ->assertDispatched('alert-show')
        ->assertDispatched('reloadDT');

    expect(Quiz::find($this->finalQuiz->id))->toBeNull()
        ->and(Quiz::withTrashed()->find($this->finalQuiz->id))->not->toBeNull();
});

test('sidebar renders Data Evaluasi child item with active-page class when viewing evaluasi.detail page', function () {
    $response = $this->actingAs($this->admin)->get(route('evaluasi.detail', $this->course->id));

    $response->assertOk();
    $response->assertSee('Data Evaluasi &amp; Kuis', false);
    // Verifikasi bahwa elemen li yang membungkus link Data Evaluasi memiliki class active-page
    $content = $response->getContent();
    expect($content)->toMatch('/<li class="active-page">\s*<a href="[^"]*\/evaluasi\/data"[^>]*>\s*<i class="ri-circle-fill circle-icon"><\/i>\s*<span>Data Evaluasi &amp; Kuis<\/span>/');
});

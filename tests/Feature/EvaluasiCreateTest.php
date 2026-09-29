<?php

use App\Livewire\Admin\Evaluasi\EvaluasiCreate;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::factory()->create();
    $this->course = Course::factory()->create(['category_id' => $this->category->id]);
    $this->chapter1 = Chapter::create(['course_id' => $this->course->id, 'title' => 'Bab 1: Pengenalan', 'order' => 1]);
    $this->chapter2 = Chapter::create(['course_id' => $this->course->id, 'title' => 'Bab 2: Implementasi', 'order' => 2]);
    $this->admin = User::factory()->admin()->create();
    $this->peserta = User::factory()->peserta()->create();
});

test('non-admin user cannot access evaluasi create page', function () {
    $this->actingAs($this->peserta)
        ->get(route('evaluasi.create'))
        ->assertForbidden();
});

test('admin can access evaluasi create page', function () {
    $this->actingAs($this->admin)
        ->get(route('evaluasi.create'))
        ->assertOk()
        ->assertSeeLivewire(EvaluasiCreate::class)
        ->assertSee('Tambah Evaluasi &amp; Kuis', false);
});

test('can add and remove questions and options dynamically', function () {
    Livewire::actingAs($this->admin)
        ->test(EvaluasiCreate::class)
        ->assertCount('questions', 1)
        ->assertCount('questions.0.options', 4)
        // Add option up to 5
        ->call('addOption', 0)
        ->assertCount('questions.0.options', 5)
        // Cannot exceed 5 options
        ->call('addOption', 0)
        ->assertCount('questions.0.options', 5)
        // Remove options down to 2
        ->call('removeOption', 0, 4)
        ->call('removeOption', 0, 3)
        ->call('removeOption', 0, 2)
        ->assertCount('questions.0.options', 2)
        // Cannot have less than 2 options
        ->call('removeOption', 0, 1)
        ->assertCount('questions.0.options', 2)
        // Add new question
        ->call('addQuestion')
        ->assertCount('questions', 2)
        // Remove question
        ->call('removeQuestion', 1)
        ->assertCount('questions', 1);
});

test('validates required fields and score bounds between 1 and 20', function () {
    Livewire::actingAs($this->admin)
        ->test(EvaluasiCreate::class)
        ->set('course_id', '')
        ->set('title', '')
        ->set('questions.0.question_text', '')
        ->set('questions.0.score', 25) // Invalid > 20
        ->call('save')
        ->assertHasErrors(['course_id', 'title', 'questions.0.question_text', 'questions.0.score']);

    Livewire::actingAs($this->admin)
        ->test(EvaluasiCreate::class)
        ->set('questions.0.score', 0) // Invalid < 1
        ->call('save')
        ->assertHasErrors(['questions.0.score']);
});

test('validates correct answer selection in options', function () {
    Livewire::actingAs($this->admin)
        ->test(EvaluasiCreate::class)
        ->set('course_id', $this->course->id)
        ->set('target_type', 'chapter')
        ->set('chapter_id', $this->chapter1->id)
        ->set('title', 'Kuis Uji Coba')
        ->set('questions.0.question_text', 'Pertanyaan 1?')
        ->set('questions.0.score', 10)
        ->call('removeOption', 0, 3)
        ->call('removeOption', 0, 2)
        ->set('questions.0.options.0.option_text', 'Opsi A')
        ->set('questions.0.options.1.option_text', 'Opsi B')
        ->set('questions.0.options.0.is_correct', false)
        ->set('questions.0.options.1.is_correct', false)
        ->call('save')
        ->assertHasErrors(['questions.0.options']);
});

test('admin can successfully create a chapter quiz with dynamic scores and options', function () {
    Livewire::actingAs($this->admin)
        ->test(EvaluasiCreate::class)
        ->set('course_id', $this->course->id)
        ->set('target_type', 'chapter')
        ->set('chapter_id', $this->chapter1->id)
        ->set('title', 'Evaluasi Bab 1')
        ->set('description', 'Kuis untuk menguji pemahaman bab 1')
        ->set('time_limit_minutes', 15)
        ->set('passing_score', 70)
        // Question 1 (Score 10, 3 options, option 0 is correct)
        ->set('questions.0.question_text', 'Apa kepanjangan ASN?')
        ->set('questions.0.score', 10)
        ->set('questions.0.explanation', 'Aparatur Sipil Negara')
        ->call('removeOption', 0, 3) // remove 4th option -> 3 options
        ->set('questions.0.options.0.option_text', 'Aparatur Sipil Negara')
        ->set('questions.0.options.0.is_correct', true)
        ->set('questions.0.options.1.option_text', 'Abdi Sipil Nasional')
        ->set('questions.0.options.2.option_text', 'Anggota Serikat Negara')
        // Question 2 (Score 15, 2 options, option 1 is correct)
        ->call('addQuestion')
        ->set('questions.1.question_text', 'Nilai BerAKHLAK berjumlah 7?')
        ->set('questions.1.score', 15)
        ->call('removeOption', 1, 3)
        ->call('removeOption', 1, 2) // 2 options
        ->set('questions.1.options.0.option_text', 'Salah')
        ->set('questions.1.options.1.option_text', 'Benar')
        ->call('setCorrectOption', 1, 1)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('evaluasi.data'));

    $quiz = Quiz::where('course_id', $this->course->id)->where('type', 'chapter')->first();
    expect($quiz)->not->toBeNull()
        ->and($quiz->title)->toBe('Evaluasi Bab 1')
        ->and($quiz->chapter_id)->toBe($this->chapter1->id)
        ->and($quiz->total_score)->toBe(25) // 10 + 15
        ->and($quiz->questions)->toHaveCount(2);

    $q1 = $quiz->questions()->where('order', 1)->first();
    expect($q1->score)->toBe(10)
        ->and($q1->options)->toHaveCount(3)
        ->and($q1->getCorrectOption()->option_text)->toBe('Aparatur Sipil Negara');

    $q2 = $quiz->questions()->where('order', 2)->first();
    expect($q2->score)->toBe(15)
        ->and($q2->options)->toHaveCount(2)
        ->and($q2->getCorrectOption()->option_text)->toBe('Benar');
});

test('admin can create final quiz and cannot create duplicate final quiz for the same course', function () {
    Livewire::actingAs($this->admin)
        ->test(EvaluasiCreate::class)
        ->set('course_id', $this->course->id)
        ->set('target_type', 'final')
        ->set('title', 'Final Exam ASN')
        ->set('questions.0.question_text', 'Soal Final 1?')
        ->set('questions.0.score', 20)
        ->call('removeOption', 0, 3)
        ->call('removeOption', 0, 2)
        ->set('questions.0.options.0.option_text', 'A')
        ->set('questions.0.options.1.option_text', 'B')
        ->call('setCorrectOption', 0, 0)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('evaluasi.data'));

    expect($this->course->finalQuiz)->not->toBeNull()
        ->and($this->course->finalQuiz->title)->toBe('Final Exam ASN');

    // Attempting to create duplicate final quiz fails validation
    Livewire::actingAs($this->admin)
        ->test(EvaluasiCreate::class)
        ->set('course_id', $this->course->id)
        ->set('target_type', 'final')
        ->set('title', 'Final Exam ASN Kedua')
        ->set('questions.0.question_text', 'Soal Final Lain?')
        ->set('questions.0.score', 20)
        ->call('removeOption', 0, 3)
        ->call('removeOption', 0, 2)
        ->set('questions.0.options.0.option_text', 'A')
        ->set('questions.0.options.1.option_text', 'B')
        ->call('setCorrectOption', 0, 0)
        ->call('save')
        ->assertHasErrors(['target_type']);
});

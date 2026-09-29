<?php

use App\Livewire\Admin\Evaluasi\EvaluasiData;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::factory()->create();
    $this->course1 = Course::factory()->create(['category_id' => $this->category->id, 'title' => 'Dasar Manajemen ASN']);
    $this->course2 = Course::factory()->create(['category_id' => $this->category->id, 'title' => 'Kepemimpinan Administrator']);

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
});

test('non-admin cannot access evaluasi data page', function () {
    $this->actingAs($this->peserta)
        ->get(route('evaluasi.data'))
        ->assertForbidden();
});

test('admin can access evaluasi data page and sees quiz listings', function () {
    $this->actingAs($this->admin)
        ->get(route('evaluasi.data'))
        ->assertOk()
        ->assertSeeLivewire(EvaluasiData::class)
        ->assertSee('Data Evaluasi &amp; Kuis', false)
        ->assertSee('Kuis Kebijakan Publik')
        ->assertSee('Ujian Akhir Manajemen ASN')
        ->assertSee('Evaluasi Inovasi Sektor Publik');
});

test('can filter quizzes by search term, course, and type', function () {
    // 1. Filter by search term
    Livewire::actingAs($this->admin)
        ->test(EvaluasiData::class)
        ->set('search', 'Kebijakan')
        ->assertSee('Kuis Kebijakan Publik')
        ->assertDontSee('Ujian Akhir Manajemen ASN')
        ->assertDontSee('Evaluasi Inovasi Sektor Publik');

    // 2. Filter by course
    Livewire::actingAs($this->admin)
        ->test(EvaluasiData::class)
        ->set('course_id', (string) $this->course2->id)
        ->assertSee('Evaluasi Inovasi Sektor Publik')
        ->assertDontSee('Kuis Kebijakan Publik')
        ->assertDontSee('Ujian Akhir Manajemen ASN');

    // 3. Filter by type 'final'
    Livewire::actingAs($this->admin)
        ->test(EvaluasiData::class)
        ->set('type', 'final')
        ->assertSee('Ujian Akhir Manajemen ASN')
        ->assertDontSee('Kuis Kebijakan Publik')
        ->assertDontSee('Evaluasi Inovasi Sektor Publik');

    // 4. Reset filter
    Livewire::actingAs($this->admin)
        ->test(EvaluasiData::class)
        ->set('search', 'Inovasi')
        ->call('resetFilters')
        ->assertSet('search', '')
        ->assertSet('course_id', '')
        ->assertSet('type', 'all')
        ->assertSee('Kuis Kebijakan Publik')
        ->assertSee('Ujian Akhir Manajemen ASN')
        ->assertSee('Evaluasi Inovasi Sektor Publik');
});

test('can open and close question detail modal', function () {
    Livewire::actingAs($this->admin)
        ->test(EvaluasiData::class)
        ->assertSet('isDetailOpen', false)
        ->assertSet('selectedQuizId', null)
        ->call('openDetail', $this->quiz1->id)
        ->assertSet('isDetailOpen', true)
        ->assertSet('selectedQuizId', $this->quiz1->id)
        ->assertSee('Apa itu ASN?')
        ->assertSee('Aparatur Sipil Negara')
        ->assertSee('Kunci Jawaban')
        ->call('closeDetail')
        ->assertSet('isDetailOpen', false)
        ->assertSet('selectedQuizId', null);
});

test('can trigger delete hook and handle delete confirmation', function () {
    Livewire::actingAs($this->admin)
        ->test(EvaluasiData::class)
        ->call('hookModalDelete', $this->quiz3->id, $this->quiz3->title)
        ->assertDispatched('modal-delete-setDeleteId')
        ->call('delete', ['id' => $this->quiz3->id])
        ->assertDispatched('closeModal', id: 'modalDelete')
        ->assertDispatched('alert-show');

    expect(Quiz::find($this->quiz3->id))->toBeNull()
        ->and(Quiz::withTrashed()->find($this->quiz3->id))->not->toBeNull();
});

test('can delete quiz directly using deleteQuiz method', function () {
    Livewire::actingAs($this->admin)
        ->test(EvaluasiData::class)
        ->call('deleteQuiz', $this->quiz2->id)
        ->assertDispatched('alert-show');

    expect(Quiz::find($this->quiz2->id))->toBeNull();
});

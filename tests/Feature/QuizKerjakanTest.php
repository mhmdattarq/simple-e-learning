<?php

use App\Enums\CourseStatus;
use App\Livewire\Peserta\Evaluasi\QuizKerjakan;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::factory()->create();
    $this->course = Course::factory()->create([
        'category_id' => $this->category->id,
        'title' => 'Pelatihan Transformasi Digital',
        'type' => 'permanent',
        'start_date' => null,
        'end_date' => null,
        'status' => 'published',
    ]);

    $this->chapter = Chapter::create([
        'course_id' => $this->course->id,
        'title' => 'Bab 1: Pondasi Digital ASN',
        'order' => 1,
    ]);

    $this->lesson1 = Lesson::create([
        'chapter_id' => $this->chapter->id,
        'title' => 'Materi 1: Era Digital',
        'content_type' => 'text',
        'order' => 1,
    ]);

    $this->lesson2 = Lesson::create([
        'chapter_id' => $this->chapter->id,
        'title' => 'Materi 2: Keamanan Informasi',
        'content_type' => 'text',
        'order' => 2,
    ]);

    $this->admin = User::factory()->admin()->create();
    $this->peserta = User::factory()->peserta()->create();

    // Setup Quiz with 2 questions (Dynamic scoring: Q1=10, Q2=15 -> Total=25, KKM=70%)
    $this->quiz = Quiz::create([
        'course_id' => $this->course->id,
        'chapter_id' => $this->chapter->id,
        'type' => 'chapter',
        'title' => 'Evaluasi Bab 1 Digital',
        'description' => 'Uji pemahaman pondasi digital ASN',
        'time_limit_minutes' => 20,
        'total_score' => 25,
        'passing_score' => 70,
        'created_by' => $this->admin->id,
    ]);

    // Question 1: Score 10, 4 options
    $this->q1 = QuizQuestion::create([
        'quiz_id' => $this->quiz->id,
        'question_text' => 'Apa itu SPBE?',
        'score' => 10,
        'order' => 1,
    ]);
    $this->q1OptA = QuizOption::create(['question_id' => $this->q1->id, 'option_text' => 'Sistem Pemerintahan Berbasis Elektronik', 'is_correct' => true, 'order' => 1]);
    $this->q1OptB = QuizOption::create(['question_id' => $this->q1->id, 'option_text' => 'Sistem Pelayanan Bersama Elektronik', 'is_correct' => false, 'order' => 2]);
    $this->q1OptC = QuizOption::create(['question_id' => $this->q1->id, 'option_text' => 'Standar Pengelolaan Berkas Elektronik', 'is_correct' => false, 'order' => 3]);

    // Question 2: Score 15, 2 options (True / False style)
    $this->q2 = QuizQuestion::create([
        'quiz_id' => $this->quiz->id,
        'question_text' => 'Password wajib diganti secara berkala?',
        'score' => 15,
        'order' => 2,
    ]);
    $this->q2OptA = QuizOption::create(['question_id' => $this->q2->id, 'option_text' => 'Benar', 'is_correct' => true, 'order' => 1]);
    $this->q2OptB = QuizOption::create(['question_id' => $this->q2->id, 'option_text' => 'Salah', 'is_correct' => false, 'order' => 2]);
});

test('guest cannot access quiz taking page and is redirected to login', function () {
    $this->get(route('peserta.evaluasi.kerjakan', ['course' => $this->course, 'quiz_id' => $this->quiz->id]))
        ->assertRedirect(route('login'));
});

test('authenticated participant can access quiz taking page via http get', function () {
    DB::table('lesson_user')->insert([
        ['user_id' => $this->peserta->id, 'lesson_id' => $this->lesson1->id, 'is_completed' => true, 'completed_at' => now()],
        ['user_id' => $this->peserta->id, 'lesson_id' => $this->lesson2->id, 'is_completed' => true, 'completed_at' => now()],
    ]);

    $response = $this->actingAs($this->peserta)
        ->get(route('peserta.evaluasi.kerjakan', ['course' => $this->course, 'quiz' => $this->quiz]));

    $response->assertOk();
});

test('quiz is locked when participant has not completed all chapter lessons', function () {
    // Peserta only completed lesson 1 (lesson 2 still incomplete)
    DB::table('lesson_user')->insert([
        'user_id' => $this->peserta->id,
        'lesson_id' => $this->lesson1->id,
        'is_completed' => true,
        'completed_at' => now(),
    ]);

    Livewire::actingAs($this->peserta)
        ->test(QuizKerjakan::class, ['quiz_id' => $this->quiz->id, 'course_id' => $this->course->id])
        ->assertSet('quizState', 'locked')
        ->assertSee('Evaluasi Belum Dapat Diakses')
        ->assertSee('Kuis Bab "Bab 1: Pondasi Digital ASN" masih terkunci');
});

test('admin can bypass prerequisite lock', function () {
    Livewire::actingAs($this->admin)
        ->test(QuizKerjakan::class, ['quiz_id' => $this->quiz->id, 'course_id' => $this->course->id])
        ->assertSet('quizState', 'intro')
        ->assertSee('Mulai Kerjakan Evaluasi');
});

test('participant can start quiz when all lessons in chapter are completed', function () {
    // Complete all lessons in chapter
    DB::table('lesson_user')->insert([
        ['user_id' => $this->peserta->id, 'lesson_id' => $this->lesson1->id, 'is_completed' => true, 'completed_at' => now()],
        ['user_id' => $this->peserta->id, 'lesson_id' => $this->lesson2->id, 'is_completed' => true, 'completed_at' => now()],
    ]);

    Livewire::actingAs($this->peserta)
        ->test(QuizKerjakan::class, ['quiz_id' => $this->quiz->id, 'course_id' => $this->course->id])
        ->assertSet('quizState', 'intro')
        ->assertSee('Evaluasi Bab 1 Digital')
        ->assertSee('25 Poin')
        ->call('startQuiz')
        ->assertSet('quizState', 'playing')
        ->assertSet('currentQuestionIndex', 0)
        ->assertSet('timeRemainingSeconds', 1200); // 20 mins = 1200 secs
});

test('participant can answer questions, navigate between questions, and jump via grid', function () {
    DB::table('lesson_user')->insert([
        ['user_id' => $this->peserta->id, 'lesson_id' => $this->lesson1->id, 'is_completed' => true, 'completed_at' => now()],
        ['user_id' => $this->peserta->id, 'lesson_id' => $this->lesson2->id, 'is_completed' => true, 'completed_at' => now()],
    ]);

    Livewire::actingAs($this->peserta)
        ->test(QuizKerjakan::class, ['quiz_id' => $this->quiz->id, 'course_id' => $this->course->id])
        ->call('startQuiz')
        // Answer Question 1
        ->call('selectOption', $this->q1->id, $this->q1OptA->id)
        ->assertSet('userAnswers.'.$this->q1->id, $this->q1OptA->id)
        ->assertSeeHtml('opt-card-'.$this->q1->id.'-'.$this->q1OptA->id.'-1')
        ->assertSeeHtml('is-selected')
        // Navigate next
        ->call('nextQuestion')
        ->assertSet('currentQuestionIndex', 1)
        // Answer Question 2
        ->call('selectOption', $this->q2->id, $this->q2OptA->id)
        ->assertSet('userAnswers.'.$this->q2->id, $this->q2OptA->id)
        // Jump back to Question 0 via grid
        ->call('jumpToQuestion', 0)
        ->assertSet('currentQuestionIndex', 0)
        // Previous on 0 stays 0
        ->call('prevQuestion')
        ->assertSet('currentQuestionIndex', 0);
});

test('participant submits quiz and score is accurately calculated based on dynamic points 1 to 20', function () {
    DB::table('lesson_user')->insert([
        ['user_id' => $this->peserta->id, 'lesson_id' => $this->lesson1->id, 'is_completed' => true, 'completed_at' => now()],
        ['user_id' => $this->peserta->id, 'lesson_id' => $this->lesson2->id, 'is_completed' => true, 'completed_at' => now()],
    ]);

    // Q1 (10 pts) answered CORRECTLY ($q1OptA)
    // Q2 (15 pts) answered INCORRECTLY ($q2OptB)
    // Earned: 10 / 25 = 40.0% -> Below passing_score (70%) -> Not Passed
    Livewire::actingAs($this->peserta)
        ->test(QuizKerjakan::class, ['quiz_id' => $this->quiz->id, 'course_id' => $this->course->id])
        ->call('startQuiz')
        ->call('selectOption', $this->q1->id, $this->q1OptA->id)
        ->call('selectOption', $this->q2->id, $this->q2OptB->id)
        ->call('submitQuiz')
        ->assertSet('quizState', 'result')
        ->assertSee('Evaluasi Telah Selesai')
        ->assertSee('40.0%')
        ->assertSee('10');

    $attempt = QuizAttempt::where('quiz_id', $this->quiz->id)->where('user_id', $this->peserta->id)->first();
    expect($attempt)->not->toBeNull()
        ->and($attempt->total_earned_score)->toBe(10)
        ->and($attempt->total_possible_score)->toBe(25)
        ->and((float) $attempt->percentage)->toBe(40.00)
        ->and($attempt->is_passed)->toBeFalse()
        ->and($attempt->answers_data)->toHaveCount(2);
});

test('participant scoring 100% passes the evaluation', function () {
    $pesertaLulus = User::factory()->peserta()->create();

    DB::table('lesson_user')->insert([
        ['user_id' => $pesertaLulus->id, 'lesson_id' => $this->lesson1->id, 'is_completed' => true, 'completed_at' => now()],
        ['user_id' => $pesertaLulus->id, 'lesson_id' => $this->lesson2->id, 'is_completed' => true, 'completed_at' => now()],
    ]);

    // Q1 (10 pts) and Q2 (15 pts) BOTH answered CORRECTLY -> 25/25 = 100% -> Passed
    Livewire::actingAs($pesertaLulus)
        ->test(QuizKerjakan::class, ['quiz_id' => $this->quiz->id, 'course_id' => $this->course->id])
        ->call('startQuiz')
        ->call('selectOption', $this->q1->id, $this->q1OptA->id)
        ->call('selectOption', $this->q2->id, $this->q2OptA->id)
        ->call('submitQuiz')
        ->assertSet('quizState', 'result')
        ->assertSee('Selamat! Anda Lulus Evaluasi')
        ->assertSee('100.0%')
        ->assertSee('25');

    $attempt = QuizAttempt::where('quiz_id', $this->quiz->id)->where('user_id', $pesertaLulus->id)->first();
    expect($attempt->is_passed)->toBeTrue()
        ->and($attempt->total_earned_score)->toBe(25);
});

test('participant cannot retake quiz and directly sees permanent result upon revisiting', function () {
    // Existing attempt already in DB
    $attempt = QuizAttempt::factory()->create([
        'quiz_id' => $this->quiz->id,
        'user_id' => $this->peserta->id,
        'total_earned_score' => 25,
        'total_possible_score' => 25,
        'percentage' => 100.0,
        'is_passed' => true,
    ]);

    // Revisiting the quiz page immediately shows result state
    Livewire::actingAs($this->peserta)
        ->test(QuizKerjakan::class, ['quiz_id' => $this->quiz->id, 'course_id' => $this->course->id])
        ->assertSet('quizState', 'result')
        ->assertSee('Single Attempt (tanpa retake)')
        ->assertSee('Selamat! Anda Lulus Evaluasi')
        ->assertDontSee('Mulai Kerjakan Evaluasi');

    // Calling startQuiz or submitQuiz will not create another attempt
    Livewire::actingAs($this->peserta)
        ->test(QuizKerjakan::class, ['quiz_id' => $this->quiz->id, 'course_id' => $this->course->id])
        ->call('startQuiz')
        ->assertSet('quizState', 'result')
        ->call('submitQuiz')
        ->assertSet('quizState', 'result');

    expect(QuizAttempt::where('quiz_id', $this->quiz->id)->where('user_id', $this->peserta->id)->count())->toBe(1);
});

test('participant promptSubmit opens dedicated modal and confirm completes quiz', function () {
    DB::table('lesson_user')->insert([
        ['user_id' => $this->peserta->id, 'lesson_id' => $this->lesson1->id, 'is_completed' => true, 'completed_at' => now()],
        ['user_id' => $this->peserta->id, 'lesson_id' => $this->lesson2->id, 'is_completed' => true, 'completed_at' => now()],
    ]);

    Livewire::actingAs($this->peserta)
        ->test(QuizKerjakan::class, ['quiz_id' => $this->quiz->id, 'course_id' => $this->course->id])
        ->call('startQuiz')
        ->call('selectOption', $this->q1->id, $this->q1OptA->id)
        ->call('promptSubmit')
        ->assertSet('showSubmitConfirmation', true)
        ->assertSee('Konfirmasi Selesai & Kumpulkan')
        ->assertSee('1 dari 2')
        ->assertSee('soal yang belum Anda jawab!')
        ->call('submitQuiz')
        ->assertSet('showSubmitConfirmation', false)
        ->assertSet('quizState', 'result');

    expect(QuizAttempt::where('quiz_id', $this->quiz->id)->where('user_id', $this->peserta->id)->count())->toBe(1);
});

test('quiz option is_correct attribute is hidden from serialization to prevent client cheating', function () {
    $option = $this->q1OptA;
    $serialized = $option->toArray();

    // is_correct must NOT be in toArray() or json_encode()
    expect(array_key_exists('is_correct', $serialized))->toBeFalse();
    expect(json_encode($option))->not->toContain('is_correct');

    // But directly accessible in PHP server-side logic
    expect($option->is_correct)->toBeTrue();
});

test('participant cannot select an option that does not belong to the question', function () {
    DB::table('lesson_user')->insert([
        ['user_id' => $this->peserta->id, 'lesson_id' => $this->lesson1->id, 'is_completed' => true, 'completed_at' => now()],
        ['user_id' => $this->peserta->id, 'lesson_id' => $this->lesson2->id, 'is_completed' => true, 'completed_at' => now()],
    ]);

    Livewire::actingAs($this->peserta)
        ->test(QuizKerjakan::class, ['quiz_id' => $this->quiz->id, 'course_id' => $this->course->id])
        ->call('startQuiz')
        // Attempt to select q2's option for q1
        ->call('selectOption', $this->q1->id, $this->q2OptA->id)
        // Must be rejected and not stored in userAnswers for q1
        ->assertSet('userAnswers.'.$this->q1->id, null)
        // Non-existent option
        ->call('selectOption', $this->q1->id, 999999)
        ->assertSet('userAnswers.'.$this->q1->id, null);
});

test('participant retains active quiz state and answers upon page reload', function () {
    DB::table('lesson_user')->insert([
        ['user_id' => $this->peserta->id, 'lesson_id' => $this->lesson1->id, 'is_completed' => true, 'completed_at' => now()],
        ['user_id' => $this->peserta->id, 'lesson_id' => $this->lesson2->id, 'is_completed' => true, 'completed_at' => now()],
    ]);

    // Step 1: Start quiz and select answer for question 1
    Livewire::actingAs($this->peserta)
        ->test(QuizKerjakan::class, ['quiz_id' => $this->quiz->id, 'course_id' => $this->course->id])
        ->call('startQuiz')
        ->call('selectOption', $this->q1->id, $this->q1OptA->id)
        ->call('jumpToQuestion', 1)
        ->assertSet('quizState', 'playing')
        ->assertSet('currentQuestionIndex', 1)
        ->assertSet('userAnswers', [$this->q1->id => $this->q1OptA->id]);

    // Step 2: Simulate browser reload by remounting the component
    Livewire::actingAs($this->peserta)
        ->test(QuizKerjakan::class, ['quiz_id' => $this->quiz->id, 'course_id' => $this->course->id])
        ->assertSet('quizState', 'playing')
        ->assertSet('currentQuestionIndex', 1)
        ->assertSet('userAnswers', [$this->q1->id => $this->q1OptA->id])
        ->assertSee('Daftar Nomor Soal');
});

test('participant cannot access quiz if batch has not started yet', function () {
    $batchCourse = Course::factory()->create([
        'status' => CourseStatus::Published,
        'type' => 'batch',
        'start_date' => now()->addDays(3),
        'end_date' => now()->addDays(10),
        'category_id' => $this->category->id,
        'created_by' => $this->admin->id,
    ]);

    $batchQuiz = Quiz::create([
        'course_id' => $batchCourse->id,
        'created_by' => $this->admin->id,
        'title' => 'Kuis Batch Uji Coba',
        'type' => 'final',
        'passing_score' => 70,
    ]);

    Livewire::actingAs($this->peserta)
        ->test(QuizKerjakan::class, ['quiz_id' => $batchQuiz->id, 'course_id' => $batchCourse->id])
        ->assertSet('quizState', 'locked')
        ->assertSee('Evaluasi kuis belum dapat diakses karena batch pelatihan baru dibuka');
});

test('participant cannot access quiz if batch has already ended', function () {
    $batchCourse = Course::factory()->create([
        'status' => CourseStatus::Published,
        'type' => 'batch',
        'start_date' => now()->subDays(10),
        'end_date' => now()->subDays(1),
        'category_id' => $this->category->id,
        'created_by' => $this->admin->id,
    ]);

    $batchQuiz = Quiz::create([
        'course_id' => $batchCourse->id,
        'created_by' => $this->admin->id,
        'title' => 'Kuis Batch Kadaluarsa',
        'type' => 'final',
        'passing_score' => 70,
    ]);

    Livewire::actingAs($this->peserta)
        ->test(QuizKerjakan::class, ['quiz_id' => $batchQuiz->id, 'course_id' => $batchCourse->id])
        ->assertSet('quizState', 'locked')
        ->assertSee('Evaluasi kuis tidak dapat diakses lagi karena masa batch pelatihan telah berakhir');
});

test('participant active quiz session is automatically finalized upon logout', function () {
    $sessionKey = "quiz_progress_{$this->quiz->id}_{$this->peserta->id}";
    $answers = [
        $this->q1->id => $this->q1OptA->id, // Correct: +10 score
    ];

    $response = $this->actingAs($this->peserta)
        ->withSession([
            $sessionKey => [
                'started_at' => now()->subMinutes(5)->toDateTimeString(),
                'current_index' => 0,
                'answers' => $answers,
            ],
        ])
        ->post(route('logout'));

    $response->assertRedirect(route('login'));

    // Verify QuizAttempt was automatically created and evaluated
    $attempt = QuizAttempt::where('user_id', $this->peserta->id)->where('quiz_id', $this->quiz->id)->first();
    expect($attempt)->not->toBeNull();
    expect($attempt->total_earned_score)->toBe(10);
    expect($this->quiz->isAttemptedByUser($this->peserta->id))->toBeTrue();
});

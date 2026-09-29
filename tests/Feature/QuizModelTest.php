<?php

use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('quiz model can be created as chapter quiz and final quiz with correct relations', function () {
    $category = Category::factory()->create();
    $course = Course::factory()->create(['category_id' => $category->id]);
    $chapter = Chapter::create(['course_id' => $course->id, 'title' => 'Bab 1 Dasar ASN', 'order' => 1]);
    $admin = User::factory()->admin()->create();

    // 1. Chapter Quiz
    $chapterQuiz = Quiz::factory()->chapterQuiz($chapter)->create([
        'course_id' => $course->id,
        'chapter_id' => $chapter->id,
        'created_by' => $admin->id,
        'title' => 'Evaluasi Pemahaman Bab 1',
    ]);

    expect($chapterQuiz->isChapterQuiz())->toBeTrue()
        ->and($chapterQuiz->isFinalQuiz())->toBeFalse()
        ->and($chapterQuiz->course->id)->toBe($course->id)
        ->and($chapterQuiz->chapter->id)->toBe($chapter->id)
        ->and($chapter->quiz->id)->toBe($chapterQuiz->id)
        ->and($course->chapterQuizzes)->toHaveCount(1);

    // 2. Final Quiz
    $finalQuiz = Quiz::factory()->finalQuiz()->create([
        'course_id' => $course->id,
        'created_by' => $admin->id,
        'title' => 'Ujian Akhir Kelulusan',
    ]);

    expect($finalQuiz->isFinalQuiz())->toBeTrue()
        ->and($finalQuiz->isChapterQuiz())->toBeFalse()
        ->and($finalQuiz->chapter)->toBeNull()
        ->and($course->finalQuiz->id)->toBe($finalQuiz->id)
        ->and($course->quizzes)->toHaveCount(2);
});

test('quiz questions and options support dynamic scoring 1 to 20 and dynamic options count', function () {
    $quiz = Quiz::factory()->create(['total_score' => 0]);

    // Question 1: Score 15, 4 options (A-D)
    $q1 = QuizQuestion::factory()->create([
        'quiz_id' => $quiz->id,
        'question_text' => 'Berapa nilai dasar ASN BerAKHLAK?',
        'score' => 15,
        'order' => 1,
    ]);

    QuizOption::factory()->create(['question_id' => $q1->id, 'option_text' => '5 Nilai', 'is_correct' => false, 'order' => 1]);
    $optCorrect = QuizOption::factory()->correct()->create(['question_id' => $q1->id, 'option_text' => '7 Nilai Dasar', 'order' => 2]);
    QuizOption::factory()->create(['question_id' => $q1->id, 'option_text' => '9 Nilai', 'is_correct' => false, 'order' => 3]);
    QuizOption::factory()->create(['question_id' => $q1->id, 'option_text' => '10 Nilai', 'is_correct' => false, 'order' => 4]);

    // Question 2: Score 5, 2 options (Benar / Salah)
    $q2 = QuizQuestion::factory()->create([
        'quiz_id' => $quiz->id,
        'question_text' => 'Akuntabel adalah salah satu pilar BerAKHLAK?',
        'score' => 5,
        'order' => 2,
    ]);

    QuizOption::factory()->correct()->create(['question_id' => $q2->id, 'option_text' => 'Benar', 'order' => 1]);
    QuizOption::factory()->create(['question_id' => $q2->id, 'option_text' => 'Salah', 'is_correct' => false, 'order' => 2]);

    expect($q1->options)->toHaveCount(4)
        ->and($q1->getCorrectOption()->id)->toBe($optCorrect->id)
        ->and($q2->options)->toHaveCount(2);

    $totalCalculated = $quiz->recalculateTotalScore();
    expect($totalCalculated)->toBe(20)
        ->and($quiz->fresh()->total_score)->toBe(20);
});

test('quiz attempts enforce single attempt per user at database level with no retake', function () {
    $quiz = Quiz::factory()->create();
    $user = User::factory()->peserta()->create();

    // First attempt succeeds
    $attempt = QuizAttempt::factory()->create([
        'quiz_id' => $quiz->id,
        'user_id' => $user->id,
        'total_earned_score' => 85,
        'total_possible_score' => 100,
        'percentage' => 85.00,
        'is_passed' => true,
    ]);

    expect($quiz->isAttemptedByUser($user->id))->toBeTrue()
        ->and($quiz->getAttemptForUser($user->id)->id)->toBe($attempt->id)
        ->and($user->quizAttempts)->toHaveCount(1);

    // Duplicate attempt for the same quiz and user throws QueryException due to unique constraint
    expect(function () use ($quiz, $user) {
        QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'total_earned_score' => 90,
            'total_possible_score' => 100,
        ]);
    })->toThrow(QueryException::class);
});

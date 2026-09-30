<?php

namespace App\Livewire\Peserta\Evaluasi;

use App\Enums\CourseStatus;
use App\Models\CourseUser;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
#[Title('Evaluasi & Kuis - SIMPEL BKPSDM')]
class QuizKerjakan extends Component
{
    public int $quizId;

    public ?Quiz $quiz = null;

    /**
     * Status tampilan kuis:
     * - 'intro': Halaman pembuka / instruksi sebelum mulai
     * - 'playing': Mode interaktif pengerjaan soal
     * - 'result': Ringkasan hasil nilai kelulusan
     * - 'locked': Materi prasyarat belum selesai
     */
    public string $quizState = 'intro';

    public string $lockedReason = '';

    public int $currentQuestionIndex = 0;

    /**
     * Menyimpan pilihan jawaban peserta: [question_id => selected_option_id]
     *
     * @var array<int, int>
     */
    public array $userAnswers = [];

    public ?QuizAttempt $savedAttempt = null;

    public ?string $startedAt = null;

    public int $timeRemainingSeconds = 0;

    public bool $showSubmitConfirmation = false;

    public function mount(
        int|string|Quiz|null $quiz = null,
        int|string|null $quiz_id = null,
        int|string|Course|null $course = null,
        int|string|null $course_id = null
    ): void {
        $targetQuiz = $quiz ?? $quiz_id;

        if ($targetQuiz instanceof Quiz) {
            $this->quiz = $targetQuiz->loadMissing([
                'course.category',
                'chapter.lessons',
                'questions.options',
            ]);
            $this->quizId = $this->quiz->id;
        } else {
            $this->quiz = Quiz::with([
                'course.category',
                'chapter.lessons',
                'questions.options',
            ])
                ->where('slug', $targetQuiz)
                ->orWhere(fn ($q) => is_numeric($targetQuiz) ? $q->where('id', (int) $targetQuiz) : null)
                ->firstOrFail();

            $this->quizId = $this->quiz->id;
        }

        // Canonical redirect if accessed via URL with numeric id or standalone route
        if (request()->route() && ! empty($this->quiz->slug)) {
            $lastSegment = request()->segment(count(request()->segments()));
            $isStandalone = request()->routeIs('peserta.evaluasi.show') || request()->is('evaluasi/kerjakan/*');

            if (is_numeric($lastSegment) || $isStandalone) {
                $courseTarget = $course ?? $this->quiz->course;
                $this->redirect(route('peserta.evaluasi.kerjakan', ['course' => $courseTarget, 'quiz' => $this->quiz]), navigate: true);

                return;
            }
        }

        /** @var User $user */
        $user = Auth::user();

        // 1. Validasi Publikasi Kelas
        if ($this->quiz->course->status !== CourseStatus::Published && ! $user->hasAdminAccess()) {
            abort(404, 'Kelas evaluasi belum dipublikasikan.');
        }

        // 2. Auto-enroll peserta
        CourseUser::firstOrCreate(
            [
                'user_id' => $user->id,
                'course_id' => $this->quiz->course_id,
            ],
            [
                'status' => 'active',
                'enrolled_at' => now(),
            ]
        );

        // 3. Cek apakah peserta sudah pernah mengerjakan kuis (Single Attempt Rule)
        $existingAttempt = $this->quiz->getAttemptForUser($user->id);
        if ($existingAttempt) {
            $this->savedAttempt = $existingAttempt;
            $this->quizState = 'result';

            return;
        }

        // 4. Validasi Prasyarat Akses (Prerequisite Completion)
        if (! $user->hasAdminAccess()) {
            $lockStatus = $this->checkPrerequisiteLock($user);
            if ($lockStatus['is_locked']) {
                $this->quizState = 'locked';
                $this->lockedReason = $lockStatus['reason'];

                return;
            }
        }

        $this->quizState = 'intro';
    }

    /**
     * Memeriksa apakah materi prasyarat bab / kelas telah selesai dipelajari.
     *
     * @return array{is_locked: bool, reason: string}
     */
    protected function checkPrerequisiteLock(User $user): array
    {
        if ($this->quiz->isChapterQuiz() && $this->quiz->chapter) {
            $chapter = $this->quiz->chapter;
            $lessonIds = $chapter->lessons->pluck('id')->toArray();
            $totalLessons = count($lessonIds);

            if ($totalLessons > 0) {
                $completedCount = DB::table('lesson_user')
                    ->where('user_id', $user->id)
                    ->whereIn('lesson_id', $lessonIds)
                    ->where('is_completed', true)
                    ->count();

                if ($completedCount < $totalLessons) {
                    return [
                        'is_locked' => true,
                        'reason' => "Kuis Bab \"{$chapter->title}\" masih terkunci. Anda harus menyelesaikan seluruh materi ({$completedCount}/{$totalLessons}) pada bab ini terlebih dahulu.",
                    ];
                }
            }
        } elseif ($this->quiz->isFinalQuiz()) {
            // Final Quiz: Semua materi di seluruh bab kelas harus selesai
            $allCourseLessonIds = DB::table('lessons')
                ->join('chapters', 'lessons.chapter_id', '=', 'chapters.id')
                ->where('chapters.course_id', $this->quiz->course_id)
                ->pluck('lessons.id')
                ->toArray();

            $totalCourseLessons = count($allCourseLessonIds);

            if ($totalCourseLessons > 0) {
                $completedCount = DB::table('lesson_user')
                    ->where('user_id', $user->id)
                    ->whereIn('lesson_id', $allCourseLessonIds)
                    ->where('is_completed', true)
                    ->count();

                if ($completedCount < $totalCourseLessons) {
                    return [
                        'is_locked' => true,
                        'reason' => "Ujian Akhir Kelas masih terkunci. Anda harus menyelesaikan seluruh materi pembelajaran ({$completedCount}/{$totalCourseLessons}) di semua bab sebelum dapat menempuh ujian akhir.",
                    ];
                }
            }
        }

        return ['is_locked' => false, 'reason' => ''];
    }

    /**
     * Memulai sesi pengerjaan kuis.
     */
    public function startQuiz(): void
    {
        $user = Auth::user();

        // Validasi ganda mencegah retake
        if ($this->quiz->isAttemptedByUser($user->id)) {
            $this->savedAttempt = $this->quiz->getAttemptForUser($user->id);
            $this->quizState = 'result';

            return;
        }

        $this->quizState = 'playing';
        $this->startedAt = now()->toDateTimeString();
        $this->currentQuestionIndex = 0;
        $this->userAnswers = [];

        if ($this->quiz->time_limit_minutes) {
            $this->timeRemainingSeconds = $this->quiz->time_limit_minutes * 60;
        }
    }

    /**
     * Memilih opsi jawaban untuk suatu butir soal.
     */
    public function selectOption(int $questionId, int $optionId): void
    {
        if ($this->quizState !== 'playing') {
            return;
        }

        // Pastikan opsi benar-benar milik pertanyaan yang bersangkutan
        $validOption = QuizOption::where('id', $optionId)
            ->where('question_id', $questionId)
            ->exists();

        if ($validOption) {
            $this->userAnswers[$questionId] = $optionId;
        }
    }

    /**
     * Melompat ke nomor soal tertentu.
     */
    public function jumpToQuestion(int $index): void
    {
        if ($index >= 0 && $index < $this->questionsCount) {
            $this->currentQuestionIndex = $index;
        }
    }

    /**
     * Pindah ke soal berikutnya.
     */
    public function nextQuestion(): void
    {
        if ($this->currentQuestionIndex < $this->questionsCount - 1) {
            $this->currentQuestionIndex++;
        }
    }

    /**
     * Pindah ke soal sebelumnya.
     */
    public function prevQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    /**
     * Membuka modal konfirmasi pengumpulan jawaban reusable.
     */
    public function promptSubmit(): void
    {
        $this->showSubmitConfirmation = true;

        $unanswered = $this->questionsCount - $this->answeredCount;

        $msg = "Anda telah menjawab {$this->answeredCount} dari {$this->questionsCount} butir pertanyaan.";
        if ($unanswered > 0) {
            $msg .= "\n\nMasih ada {$unanswered} soal yang belum Anda jawab!";
        }
        $msg .= "\n\nPerhatian: Kuis ini menerapkan sistem Single Attempt. Jawaban yang dikumpulkan bersifat final dan tidak dapat diubah kembali.";

        $dtHook = [
            'id' => $this->quizId,
            'title' => 'Konfirmasi Selesai & Kumpulkan',
            'msg' => $msg,
            'msgBoxClass' => $unanswered > 0 ? 'bg-warning-subtle text-dark border-warning' : '',
            'icon' => 'ri-question-mark',
            'iconBadgeClass' => 'bg-warning-subtle text-warning',
            'iconBadgeStyle' => 'background-color: #fef3c7 !important; color: #d97706 !important; box-shadow: 0 8px 24px rgba(217, 119, 6, 0.2) !important;',
            'btnCancelText' => 'Periksa Lagi',
            'btnConfirmText' => 'Ya, Kumpulkan',
            'btnConfirmIcon' => 'ri-check-line',
            'btnConfirmClass' => 'simpel-modal-btn-success',
            'dispatch' => 'QuizKerjakan-submit',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    /**
     * Listener konfirmasi pengumpulan dari reusable modal.
     */
    #[On('QuizKerjakan-submit')]
    public function handleConfirmSubmit(): void
    {
        $this->submitQuiz();
    }

    /**
     * Membatalkan pengumpulan jawaban kuis.
     */
    public function cancelSubmit(): void
    {
        $this->showSubmitConfirmation = false;
    }

    /**
     * Menyelesaikan dan mengkalkulasi skor evaluasi secara permanen.
     */
    public function submitQuiz(): void
    {
        $this->showSubmitConfirmation = false;
        $user = Auth::user();

        // Pencegahan race condition retake
        if ($this->quiz->isAttemptedByUser($user->id)) {
            $this->savedAttempt = $this->quiz->getAttemptForUser($user->id);
            $this->quizState = 'result';

            return;
        }

        $earnedScore = 0;
        $answersData = [];

        // Ambil data pertanyaan dan opsi resmi langsung dari database untuk validasi & penilaian yang aman
        $questions = QuizQuestion::with(['options' => fn ($q) => $q->orderBy('order', 'asc')])
            ->where('quiz_id', $this->quiz->id)
            ->orderBy('order', 'asc')
            ->get();

        foreach ($questions as $question) {
            $correctOption = $question->options->firstWhere('is_correct', true)
                ?? QuizOption::where('question_id', $question->id)->where('is_correct', true)->first();
            $chosenOptionId = $this->userAnswers[$question->id] ?? null;

            $isCorrect = ($chosenOptionId && $correctOption && (int) $chosenOptionId === $correctOption->id);
            $scoreGained = $isCorrect ? (int) $question->score : 0;
            $earnedScore += $scoreGained;

            $answersData[] = [
                'question_id' => $question->id,
                'question_text' => $question->question_text,
                'chosen_option_id' => $chosenOptionId,
                'correct_option_id' => $correctOption?->id,
                'is_correct' => $isCorrect,
                'score_earned' => $scoreGained,
                'question_score' => $question->score,
            ];
        }

        $totalPossible = $this->quiz->total_score > 0 ? $this->quiz->total_score : max(1, (int) $questions->sum('score'));
        $percentage = round(($earnedScore / $totalPossible) * 100, 2);
        $isPassed = ($percentage >= $this->quiz->passing_score);

        $attempt = QuizAttempt::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $user->id,
            'total_earned_score' => $earnedScore,
            'total_possible_score' => $totalPossible,
            'percentage' => $percentage,
            'is_passed' => $isPassed,
            'answers_data' => $answersData,
            'started_at' => $this->startedAt ? Carbon::parse($this->startedAt) : now(),
            'submitted_at' => now(),
        ]);

        $this->savedAttempt = $attempt;
        $this->quizState = 'result';

        $this->dispatch('show-toast', [
            'type' => $isPassed ? 'success' : 'warning',
            'message' => $isPassed
                ? 'Selamat! Anda dinyatakan Lulus evaluasi ini dengan nilai '.$percentage.'%.'
                : 'Evaluasi telah selesai. Nilai Anda '.$percentage.'%.',
        ]);
    }

    #[Computed]
    public function currentQuestion(): ?QuizQuestion
    {
        return $this->quiz->questions[$this->currentQuestionIndex] ?? null;
    }

    #[Computed]
    public function questionsCount(): int
    {
        return $this->quiz ? $this->quiz->questions->count() : 0;
    }

    #[Computed]
    public function answeredCount(): int
    {
        return count(array_filter($this->userAnswers));
    }

    public function render()
    {
        return view('mods.peserta.evaluasi.quiz-kerjakan');
    }
}

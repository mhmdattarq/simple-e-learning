<?php

namespace App\Livewire\Admin\Evaluasi;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.app')]
#[Title('Tambah Evaluasi & Kuis - SIMPEL BKPSDM')]
class EvaluasiCreate extends Component
{
    public ?int $course_id = null;

    public string $target_type = 'chapter'; // 'chapter' | 'final'

    public ?int $chapter_id = null;

    public string $title = '';

    public string $description = '';

    public ?int $time_limit_minutes = 30;

    public int $passing_score = 70;

    /**
     * Butir pertanyaan dinamis beserta opsi jawaban.
     *
     * @var array<int, array{
     *     question_text: string,
     *     score: int,
     *     explanation: string,
     *     options: array<int, array{option_text: string, is_correct: bool}>
     * }>
     */
    public array $questions = [];

    public function mount(): void
    {
        // Inisialisasi butir soal pertama dengan 4 pilihan default (A, B, C, D)
        $this->questions = [
            $this->makeEmptyQuestion(),
        ];
    }

    /**
     * Membuat struktur data soal kosong baru.
     *
     * @return array{
     *     question_text: string,
     *     score: int,
     *     explanation: string,
     *     options: array<int, array{option_text: string, is_correct: bool}>
     * }
     */
    protected function makeEmptyQuestion(): array
    {
        return [
            'question_text' => '',
            'score' => 10,
            'explanation' => '',
            'options' => [
                ['option_text' => '', 'is_correct' => true],
                ['option_text' => '', 'is_correct' => false],
                ['option_text' => '', 'is_correct' => false],
                ['option_text' => '', 'is_correct' => false],
            ],
        ];
    }

    /**
     * Hook ketika pilihan kelas diubah.
     */
    public function updatedCourseId($value): void
    {
        $this->chapter_id = null;

        if (! empty($value)) {
            $course = Course::find($value);
            if ($course) {
                // Jika target adalah final quiz dan kelas sudah memiliki final quiz, otomatis alihkan ke chapter
                if ($this->target_type === 'final' && Quiz::where('course_id', $value)->where('type', 'final')->exists()) {
                    $this->target_type = 'chapter';
                }

                $this->suggestTitle();
            }
        }
    }

    /**
     * Hook ketika target evaluasi (Kuis Bab atau Final Quiz) diubah.
     */
    public function updatedTargetType($value): void
    {
        if ($value === 'final') {
            $this->chapter_id = null;
        }

        $this->suggestTitle();
    }

    /**
     * Hook ketika bab dipilih.
     */
    public function updatedChapterId($value): void
    {
        $this->suggestTitle();
    }

    /**
     * Memberikan rekomendasi judul otomatis agar mempermudah admin.
     */
    protected function suggestTitle(): void
    {
        if (! $this->course_id) {
            return;
        }

        $course = Course::find($this->course_id);
        if (! $course) {
            return;
        }

        if ($this->target_type === 'final') {
            $this->title = 'Ujian Akhir Kelas: '.$course->title;
        } elseif ($this->chapter_id) {
            $chapter = Chapter::find($this->chapter_id);
            if ($chapter) {
                $this->title = 'Evaluasi Bab: '.$chapter->title;
            }
        }
    }

    /**
     * Tambah butir soal baru.
     */
    public function addQuestion(): void
    {
        $this->questions[] = $this->makeEmptyQuestion();
    }

    /**
     * Hapus butir soal tertentu.
     */
    public function removeQuestion(int $questionIndex): void
    {
        if (count($this->questions) > 1) {
            unset($this->questions[$questionIndex]);
            $this->questions = array_values($this->questions);
        }
    }

    /**
     * Tambah opsi pilihan jawaban (maksimal 5 opsi: A s.d. E).
     */
    public function addOption(int $questionIndex): void
    {
        if (isset($this->questions[$questionIndex])) {
            if (count($this->questions[$questionIndex]['options']) < 5) {
                $this->questions[$questionIndex]['options'][] = [
                    'option_text' => '',
                    'is_correct' => false,
                ];
            }
        }
    }

    /**
     * Hapus opsi pilihan jawaban tertentu (minimal 2 opsi).
     */
    public function removeOption(int $questionIndex, int $optionIndex): void
    {
        if (isset($this->questions[$questionIndex]['options'])) {
            $options = &$this->questions[$questionIndex]['options'];
            if (count($options) > 2) {
                $wasCorrect = $options[$optionIndex]['is_correct'] ?? false;
                unset($options[$optionIndex]);
                $options = array_values($options);

                // Jika opsi yang dihapus adalah kunci jawaban, setel opsi pertama sebagai kunci
                if ($wasCorrect && count($options) > 0) {
                    $options[0]['is_correct'] = true;
                }
            }
        }
    }

    /**
     * Menetapkan satu opsi sebagai kunci jawaban yang benar.
     */
    public function setCorrectOption(int $questionIndex, int $optionIndex): void
    {
        if (isset($this->questions[$questionIndex]['options'])) {
            foreach ($this->questions[$questionIndex]['options'] as $idx => &$opt) {
                $opt['is_correct'] = ($idx === $optionIndex);
            }
        }
    }

    /**
     * Mendapatkan daftar kelas yang tersedia.
     */
    #[Computed]
    public function courses(): Collection
    {
        return Course::orderBy('title', 'asc')->get(['id', 'title', 'type', 'status']);
    }

    /**
     * Mendapatkan daftar bab dari kelas yang dipilih.
     */
    #[Computed]
    public function chapters(): Collection
    {
        if (! $this->course_id) {
            return collect();
        }

        return Chapter::where('course_id', $this->course_id)
            ->with('quiz')
            ->orderBy('order', 'asc')
            ->get();
    }

    /**
     * Cek apakah kelas yang dipilih sudah memiliki Ujian Akhir (Final Quiz).
     */
    #[Computed]
    public function hasFinalQuiz(): bool
    {
        if (! $this->course_id) {
            return false;
        }

        return Quiz::where('course_id', $this->course_id)->where('type', 'final')->exists();
    }

    /**
     * Menghitung total skor kuis secara realtime dari bobot masing-masing soal.
     */
    #[Computed]
    public function totalScore(): int
    {
        $sum = 0;
        foreach ($this->questions as $q) {
            $sum += (int) ($q['score'] ?? 0);
        }

        return $sum;
    }

    /**
     * Aturan validasi input kuis & butir soal.
     */
    protected function rules(): array
    {
        return [
            'course_id' => ['required', 'exists:courses,id'],
            'target_type' => ['required', 'in:chapter,final'],
            'chapter_id' => [
                'required_if:target_type,chapter',
                'nullable',
                'exists:chapters,id',
            ],
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:360'],
            'passing_score' => ['required', 'integer', 'min:0', 'max:100'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question_text' => ['required', 'string', 'min:3'],
            'questions.*.score' => ['required', 'integer', 'min:1', 'max:20'],
            'questions.*.explanation' => ['nullable', 'string', 'max:1000'],
            'questions.*.options' => ['required', 'array', 'min:2', 'max:5'],
            'questions.*.options.*.option_text' => ['required', 'string'],
        ];
    }

    /**
     * Pesan validasi dalam bahasa Indonesia.
     */
    protected function messages(): array
    {
        return [
            'course_id.required' => 'Kelas wajib dipilih.',
            'course_id.exists' => 'Kelas yang dipilih tidak ditemukan.',
            'target_type.required' => 'Penempatan evaluasi wajib ditentukan.',
            'chapter_id.required_if' => 'Bab materi wajib dipilih jika menargetkan Kuis Bab.',
            'chapter_id.exists' => 'Bab materi yang dipilih tidak valid.',
            'title.required' => 'Judul evaluasi / kuis wajib diisi.',
            'title.min' => 'Judul evaluasi minimal 3 karakter.',
            'title.max' => 'Judul evaluasi maksimal 255 karakter.',
            'time_limit_minutes.min' => 'Batas waktu minimal 1 menit.',
            'time_limit_minutes.max' => 'Batas waktu maksimal 360 menit (6 jam).',
            'passing_score.required' => 'Batas nilai kelulusan (KKM) wajib diisi.',
            'passing_score.min' => 'KKM minimal 0.',
            'passing_score.max' => 'KKM maksimal 100.',
            'questions.required' => 'Kuis minimal harus memiliki 1 butir pertanyaan.',
            'questions.min' => 'Kuis minimal harus memiliki 1 butir pertanyaan.',
            'questions.*.question_text.required' => 'Teks pertanyaan wajib diisi.',
            'questions.*.question_text.min' => 'Teks pertanyaan minimal 3 karakter.',
            'questions.*.score.required' => 'Bobot skor wajib diisi.',
            'questions.*.score.min' => 'Bobot skor minimal 1 poin.',
            'questions.*.score.max' => 'Bobot skor maksimal 20 poin.',
            'questions.*.options.min' => 'Setiap pertanyaan minimal memiliki 2 opsi pilihan jawaban.',
            'questions.*.options.max' => 'Setiap pertanyaan maksimal memiliki 5 opsi pilihan jawaban.',
            'questions.*.options.*.option_text.required' => 'Teks pilihan jawaban tidak boleh kosong.',
        ];
    }

    /**
     * Menyimpan data evaluasi dan pertanyaan ke database secara transaksional.
     */
    public function save(): mixed
    {
        return $this->formSubmit();
    }

    public function formSubmit()
    {
        $this->validate();

        // Validasi duplikasi kuis pada bab atau final quiz
        if ($this->target_type === 'final') {
            if (Quiz::where('course_id', $this->course_id)->where('type', 'final')->exists()) {
                $this->addError('target_type', 'Kelas ini sudah memiliki Ujian Akhir (Final Quiz).');

                return;
            }
        } elseif ($this->target_type === 'chapter') {
            if (Quiz::where('chapter_id', $this->chapter_id)->exists()) {
                $this->addError('chapter_id', 'Bab ini sudah memiliki kuis evaluasi sebelumnya.');

                return;
            }
        }

        // Validasi setiap pertanyaan wajib memiliki tepat 1 kunci jawaban benar
        foreach ($this->questions as $qIdx => $question) {
            $hasCorrect = false;
            foreach ($question['options'] as $option) {
                if (! empty($option['is_correct'])) {
                    $hasCorrect = true;
                    break;
                }
            }

            if (! $hasCorrect) {
                $this->addError("questions.{$qIdx}.options", 'Soal nomor '.($qIdx + 1).' wajib memiliki 1 kunci jawaban yang benar.');

                return;
            }
        }

        $totalCalculatedScore = $this->totalScore;

        DB::transaction(function () use ($totalCalculatedScore) {
            $quiz = Quiz::create([
                'course_id' => $this->course_id,
                'chapter_id' => $this->target_type === 'chapter' ? $this->chapter_id : null,
                'type' => $this->target_type,
                'title' => trim($this->title),
                'description' => ! empty($this->description) ? trim($this->description) : null,
                'time_limit_minutes' => $this->time_limit_minutes ?: null,
                'total_score' => $totalCalculatedScore,
                'passing_score' => $this->passing_score,
                'created_by' => Auth::id(),
            ]);

            foreach ($this->questions as $qIndex => $qData) {
                $question = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => trim($qData['question_text']),
                    'explanation' => ! empty($qData['explanation']) ? trim($qData['explanation']) : null,
                    'score' => (int) $qData['score'],
                    'order' => $qIndex + 1,
                ]);

                foreach ($qData['options'] as $oIndex => $oData) {
                    QuizOption::create([
                        'question_id' => $question->id,
                        'option_text' => trim($oData['option_text']),
                        'is_correct' => (bool) ($oData['is_correct'] ?? false),
                        'order' => $oIndex + 1,
                    ]);
                }
            }
        });

        session()->flash('alert-show', [
            'message' => 'Evaluasi kuis baru berhasil dibuat dan disimpan.',
            'type' => 'success',
        ]);

        return redirect()->route('evaluasi.data');
    }

    public function render()
    {
        return view('mods.admin.evaluasi.evaluasi-create');
    }
}

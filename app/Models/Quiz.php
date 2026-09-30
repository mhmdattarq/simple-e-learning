<?php

namespace App\Models;

use Database\Factories\QuizFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Quiz extends Model
{
    /** @use HasFactory<QuizFactory> */
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'time_limit_minutes' => 'integer',
            'total_score' => 'integer',
            'passing_score' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Quiz $quiz) {
            if (empty($quiz->slug) && ! empty($quiz->title)) {
                $baseSlug = Str::slug($quiz->title) ?: 'evaluasi';
                $slug = $baseSlug;
                $count = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$count++;
                }
                $quiz->slug = $slug;
            }
        });
    }

    /**
     * Use slug for route model binding and URL generation.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Resolve the route binding for the quiz by slug or numeric id.
     */
    public function resolveRouteBinding($value, $field = null): ?self
    {
        $field = $field ?? $this->getRouteKeyName();

        if ($field === 'slug') {
            return $this->where('slug', $value)
                ->when(is_numeric($value), fn ($q) => $q->orWhere('id', (int) $value))
                ->first();
        }

        return parent::resolveRouteBinding($value, $field);
    }

    /**
     * Course associated with this quiz.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Chapter associated with this quiz (null if final quiz).
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    /**
     * User who created this quiz.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Questions belonging to this quiz.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id')->orderBy('order', 'asc');
    }

    /**
     * User attempts for this quiz.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }

    /**
     * Check if quiz is a chapter quiz.
     */
    public function isChapterQuiz(): bool
    {
        return $this->type === 'chapter';
    }

    /**
     * Check if quiz is a final course quiz.
     */
    public function isFinalQuiz(): bool
    {
        return $this->type === 'final';
    }

    /**
     * Recalculate total available score from questions.
     */
    public function recalculateTotalScore(): int
    {
        $total = (int) $this->questions()->sum('score');
        $this->update(['total_score' => $total]);

        return $total;
    }

    /**
     * Get attempt for a specific user.
     */
    public function getAttemptForUser(?int $userId): ?QuizAttempt
    {
        if (! $userId) {
            return null;
        }

        return $this->attempts()->where('user_id', $userId)->first();
    }

    /**
     * Check if user has already attempted this quiz.
     */
    public function isAttemptedByUser(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return $this->attempts()->where('user_id', $userId)->exists();
    }
}

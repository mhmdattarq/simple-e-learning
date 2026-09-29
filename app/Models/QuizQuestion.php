<?php

namespace App\Models;

use Database\Factories\QuizQuestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    /** @use HasFactory<QuizQuestionFactory> */
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'order' => 'integer',
        ];
    }

    /**
     * Quiz that owns this question.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    /**
     * Available options for this question.
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class, 'question_id')->orderBy('order', 'asc');
    }

    /**
     * Get the correct option for this question.
     */
    public function getCorrectOption(): ?QuizOption
    {
        return $this->options->firstWhere('is_correct', true)
            ?? $this->options()->where('is_correct', true)->first();
    }
}

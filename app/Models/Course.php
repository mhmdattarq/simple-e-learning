<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'quota' => 'integer',
        ];
    }

    /**
     * Category of this course.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * User who created this course.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if course is permanent (open anytime).
     */
    public function isPermanent(): bool
    {
        return $this->type === 'permanent';
    }

    /**
     * Check if course is batch-based (scheduled period).
     */
    public function isBatch(): bool
    {
        return $this->type === 'batch';
    }
}

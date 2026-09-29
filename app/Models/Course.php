<?php

namespace App\Models;

use App\Enums\CourseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => CourseStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'registration_open_at' => 'datetime',
            'registration_close_at' => 'datetime',
            'price' => 'decimal:2',
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
     * Registrations (enrollments) for this course.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(CourseUser::class, 'course_id');
    }

    /**
     * Participants enrolled in this course.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_user')
            ->withPivot(['id', 'registration_number', 'status', 'notes', 'enrolled_at'])
            ->withTimestamps();
    }

    /**
     * Struktur bab silabus kurikulum (Tahap 6: Ruang Belajar).
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class, 'course_id')->orderBy('order', 'asc');
    }

    /**
     * Seluruh unit materi di dalam kursus melalui bab.
     */
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Chapter::class);
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

    /**
     * Check if course is paid (berbayar).
     */
    public function isPaid(): bool
    {
        return $this->type === 'paid' || $this->type === 'berbayar';
    }

    /**
     * Check if curriculum is frozen (Batch rule: locked once active/started).
     */
    public function isCurriculumFrozen(): bool
    {
        if ($this->isBatch() && $this->start_date) {
            return now()->startOfDay()->gte($this->start_date->startOfDay());
        }

        return false;
    }

    /**
     * Check if course is in draft status.
     */
    public function isDraft(): bool
    {
        return $this->status === CourseStatus::Draft;
    }

    /**
     * Check if course is published.
     */
    public function isPublished(): bool
    {
        return $this->status === CourseStatus::Published;
    }
}

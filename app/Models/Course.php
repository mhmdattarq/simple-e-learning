<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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
            ->withPivot(['id', 'registration_number', 'status', 'recommendation_letter_path', 'notes', 'enrolled_at'])
            ->withTimestamps();
    }

    /**
     * Jadwal sesi pelatihan (Tahap 4: Penjadwalan).
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(CourseSchedule::class, 'course_id');
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
     * Check if curriculum is frozen (Batch rule: locked once active/started).
     */
    public function isCurriculumFrozen(): bool
    {
        if ($this->isBatch() && $this->start_date) {
            return now()->startOfDay()->gte($this->start_date->startOfDay());
        }

        return false;
    }
}

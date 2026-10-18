<?php

namespace App\Models;

use App\Enums\CourseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => CourseStatus::class,
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'registration_open_at' => 'datetime',
            'registration_close_at' => 'datetime',
            'price' => 'decimal:2',
            'jam_pelajaran' => 'integer',
        ];
    }

    /**
     * Get jam pelajaran (JP) with fallback to 24.
     */
    public function getJpAttribute(): int
    {
        return (int) ($this->jam_pelajaran ?? 24);
    }

    /**
     * Use slug for route model binding and URL generation.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Resolve the route binding for the course by slug or numeric id.
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
     * All quizzes in this course.
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'course_id');
    }

    /**
     * Chapter quizzes in this course.
     */
    public function chapterQuizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'course_id')->where('type', 'chapter');
    }

    /**
     * Final quiz in this course.
     */
    public function finalQuiz(): HasOne
    {
        return $this->hasOne(Quiz::class, 'course_id')->where('type', 'final');
    }

    /**
     * Check if course is Bangkom (open anytime / self-paced).
     */
    public function isBangkom(): bool
    {
        return in_array($this->type, ['bangkom', 'permanent'], true);
    }

    /**
     * Check if course is Bulanan (scheduled period).
     */
    public function isBulanan(): bool
    {
        return in_array($this->type, ['bulanan', 'batch'], true);
    }

    /**
     * Check if course is Bangkom External (berbayar).
     */
    public function isBangkomExternal(): bool
    {
        return in_array($this->type, ['bangkom_external', 'paid', 'berbayar'], true);
    }

    /**
     * Backward compatibility alias for isBangkom().
     */
    public function isPermanent(): bool
    {
        return $this->isBangkom();
    }

    /**
     * Backward compatibility alias for isBulanan().
     */
    public function isBatch(): bool
    {
        return $this->isBulanan();
    }

    /**
     * Backward compatibility alias for isBangkomExternal().
     */
    public function isPaid(): bool
    {
        return $this->isBangkomExternal();
    }

    /**
     * Check if curriculum is frozen (Bulanan rule: locked once active/started).
     * Locking is based on exact datetime, not day-level comparison, so admin
     * can still manage curriculum up until the precise start_date moment.
     */
    public function isCurriculumFrozen(): bool
    {
        if ($this->isBulanan() && $this->start_date) {
            return now()->gte($this->start_date);
        }

        return false;
    }

    /**
     * Check if bulanan course has not started yet.
     */
    public function isBulananNotStarted(): bool
    {
        if ($this->isBulanan() && $this->start_date) {
            return now()->lt($this->start_date);
        }

        return false;
    }

    /**
     * Backward compatibility alias for isBulananNotStarted().
     */
    public function isBatchNotStarted(): bool
    {
        return $this->isBulananNotStarted();
    }

    /**
     * Check if bulanan course has ended.
     */
    public function isBulananEnded(): bool
    {
        if ($this->isBulanan() && $this->end_date) {
            return now()->gt($this->end_date);
        }

        return false;
    }

    /**
     * Backward compatibility alias for isBulananEnded().
     */
    public function isBatchEnded(): bool
    {
        return $this->isBulananEnded();
    }

    /**
     * Check if bulanan course is currently active (between start and end date).
     */
    public function isBulananActive(): bool
    {
        if (! $this->isBulanan()) {
            return true;
        }

        $started = ! $this->start_date || now()->gte($this->start_date);
        $notEnded = ! $this->end_date || now()->lte($this->end_date);

        return $started && $notEnded;
    }

    /**
     * Backward compatibility alias for isBulananActive().
     */
    public function isBatchActive(): bool
    {
        return $this->isBulananActive();
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

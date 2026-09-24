<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    /**
     * Pelatihan / Kursus pemilik bab ini.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Unit materi pembelajaran di dalam bab ini.
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'chapter_id')->orderBy('order', 'asc');
    }
}

<?php

namespace App\Models;

use Database\Factories\CourseExternalFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CourseExternal extends Model
{
    /** @use HasFactory<CourseExternalFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'courses_externals';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CourseExternal $courseExternal): void {
            if (empty($courseExternal->slug) && ! empty($courseExternal->name)) {
                $baseSlug = Str::slug($courseExternal->name) ?: 'bangkom-external';
                $slug = $baseSlug;
                $count = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$count++;
                }
                $courseExternal->slug = $slug;
            }
        });
    }

    /**
     * Kategori untuk rujukan bangkom eksternal.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Admin pembuat data rujukan eksternal.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Memeriksa apakah pelatihan eksternal ini gratis.
     */
    public function isGratis(): bool
    {
        return strtolower($this->type ?? 'gratis') === 'gratis';
    }

    /**
     * Memeriksa apakah pelatihan eksternal ini berbayar.
     */
    public function isBerbayar(): bool
    {
        return strtolower($this->type ?? '') === 'berbayar';
    }

    /**
     * Memeriksa apakah pelatihan berstatus tayang/published.
     */
    public function isPublished(): bool
    {
        return ($this->status ?? 'published') === 'published';
    }

    /**
     * Scope untuk hanya mengambil data yang siap tayang di publik.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Mendapatkan URL logo platform atau null jika belum diatur.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo)) {
            return null;
        }

        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://')) {
            return $this->logo;
        }

        if (file_exists(public_path($this->logo))) {
            return asset($this->logo);
        }

        return asset('storage/'.$this->logo);
    }

    /**
     * Mendapatkan alias tipe eksternal (gratis/berbayar).
     */
    public function getExternalTypeAttribute(): string
    {
        return $this->type ?? 'gratis';
    }
}

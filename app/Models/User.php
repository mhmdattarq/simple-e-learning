<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    /**
     * Role helper checks.
     */
    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function isMentor(): bool
    {
        return $this->role === Role::Mentor;
    }

    public function isVerifikator(): bool
    {
        return $this->role === Role::Verifikator;
    }

    public function isPimpinan(): bool
    {
        return $this->role === Role::Pimpinan;
    }

    public function isPeserta(): bool
    {
        return $this->role === Role::Peserta;
    }

    public function hasAdminAccess(): bool
    {
        return $this->role?->hasAdminAccess() ?? false;
    }

    /**
     * Course registrations for this user.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(CourseUser::class, 'user_id');
    }

    /**
     * Courses enrolled by this user.
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_user')
            ->withPivot(['id', 'registration_number', 'status', 'recommendation_letter_path', 'notes', 'enrolled_at'])
            ->withTimestamps();
    }
}

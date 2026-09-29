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
            'last_login_at' => 'datetime',
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

    public function isPeserta(): bool
    {
        return $this->role === Role::Peserta;
    }

    public function hasAdminAccess(): bool
    {
        return $this->role?->hasAdminAccess() ?? false;
    }

    public function isGoogleUser(): bool
    {
        return ! empty($this->google_id);
    }

    /**
     * Cek apakah data profil pengguna sudah lengkap (nama, email, phone_number, address).
     */
    public function isProfileComplete(): bool
    {
        return ! empty($this->name)
            && ! empty($this->email)
            && ! empty($this->phone_number)
            && ! empty($this->address);
    }

    /**
     * Alias untuk kompatibilitas selama proses refactor.
     */
    public function isAsnProfileComplete(): bool
    {
        return $this->isProfileComplete();
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
            ->withPivot(['id', 'registration_number', 'status', 'notes', 'enrolled_at'])
            ->withTimestamps();
    }

    /**
     * Quiz attempts by this user.
     */
    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class, 'user_id');
    }
}

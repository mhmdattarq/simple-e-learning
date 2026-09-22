<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseUser extends Model
{
    use HasFactory;

    protected $table = 'course_user';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => RegistrationStatus::class,
            'enrolled_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * User / ASN participant who registered.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Course applied for.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Verifikator who processed the application.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}

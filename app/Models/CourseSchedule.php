<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSchedule extends Model
{
    use HasFactory;

    protected $table = 'course_schedules';

    protected $fillable = [
        'course_id',
        'mentor_id',
        'session_title',
        'session_date',
        'start_time',
        'end_time',
        'room_or_link',
        'status',
        'created_by',
        'attendance_token',
        'token_validity_minutes',
        'token_expires_at',
        'is_attendance_open',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date:Y-m-d',
            'token_expires_at' => 'datetime',
            'is_attendance_open' => 'boolean',
            'token_validity_minutes' => 'integer',
        ];
    }

    /**
     * Pelatihan yang dijadwalkan.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Narasumber / Mentor pengampu sesi.
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * Pembuat jadwal sesi.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Data presensi peserta pada sesi ini.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'schedule_id');
    }

    /**
     * Cek apakah token absensi sedang aktif dibuka dan belum kedaluwarsa.
     */
    public function isAttendanceActive(): bool
    {
        return $this->is_attendance_open
            && ! empty($this->attendance_token)
            && $this->token_expires_at
            && $this->token_expires_at->isFuture();
    }

    /**
     * Cek apakah lokasi merupakan tautan daring (Zoom / Meet / URL).
     */
    public function isOnline(): bool
    {
        return filter_var($this->room_or_link, FILTER_VALIDATE_URL) !== false
            || str_starts_with($this->room_or_link, 'http://')
            || str_starts_with($this->room_or_link, 'https://');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date:Y-m-d',
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
     * Cek apakah lokasi merupakan tautan daring (Zoom / Meet / URL).
     */
    public function isOnline(): bool
    {
        return filter_var($this->room_or_link, FILTER_VALIDATE_URL) !== false
            || str_starts_with($this->room_or_link, 'http://')
            || str_starts_with($this->room_or_link, 'https://');
    }
}

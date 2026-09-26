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
        'token_opened_at',
        'token_validity_minutes',
        'late_threshold_minutes',
        'token_expires_at',
        'is_attendance_open',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date:Y-m-d',
            'token_opened_at' => 'datetime',
            'token_expires_at' => 'datetime',
            'is_attendance_open' => 'boolean',
            'token_validity_minutes' => 'integer',
            'late_threshold_minutes' => 'integer',
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
     * Bab materi pembelajaran yang diasosiasikan dengan sesi ini.
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class, 'schedule_id')->orderBy('order', 'asc');
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
     * Dapatkan label status absensi baku: Sedang Dibuka / Ditutup / Belum Dibuka.
     */
    public function getAttendanceStatus(): string
    {
        if ($this->isAttendanceActive()) {
            return 'Sedang Dibuka';
        }

        if ((! $this->is_attendance_open && ! empty($this->attendance_token)) || ($this->token_expires_at && $this->token_expires_at->isPast())) {
            return 'Ditutup';
        }

        return 'Belum Dibuka';
    }

    /**
     * Dapatkan badge status absensi.
     */
    public function getAttendanceStatusBadge(): string
    {
        return match ($this->getAttendanceStatus()) {
            'Sedang Dibuka' => '<span class="badge bg-success text-white px-2 py-1"><i class="ri-broadcast-line me-1"></i>Sedang Dibuka</span>',
            'Ditutup' => '<span class="badge bg-secondary text-white px-2 py-1"><i class="ri-lock-line me-1"></i>Ditutup</span>',
            default => '<span class="badge bg-light text-muted border px-2 py-1"><i class="ri-time-line me-1"></i>Belum Dibuka</span>',
        };
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

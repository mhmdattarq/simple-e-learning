<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'schedule_id',
        'user_id',
        'status',
        'check_in_at',
        'is_manual_correction',
        'correction_reason',
        'corrected_by',
    ];

    protected function casts(): array
    {
        return [
            'check_in_at' => 'datetime',
            'is_manual_correction' => 'boolean',
        ];
    }

    /**
     * Sesi jadwal terkait.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(CourseSchedule::class, 'schedule_id');
    }

    /**
     * Peserta yang hadir / diabsenkan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Petugas (Admin/Mentor) yang melakukan koreksi manual status.
     */
    public function corrector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    /**
     * HTML Badge status kehadiran.
     */
    public function getStatusBadge(): string
    {
        return match ($this->status) {
            'hadir' => '<span class="badge bg-success text-white px-2 py-1"><i class="ri-checkbox-circle-line me-1"></i>Hadir</span>',
            'terlambat' => '<span class="badge bg-warning text-dark px-2 py-1"><i class="ri-time-line me-1"></i>Terlambat</span>',
            'izin' => '<span class="badge bg-info text-white px-2 py-1"><i class="ri-information-line me-1"></i>Izin</span>',
            'sakit' => '<span class="badge bg-secondary text-white px-2 py-1"><i class="ri-first-aid-kit-line me-1"></i>Sakit</span>',
            'alpa' => '<span class="badge bg-danger text-white px-2 py-1"><i class="ri-close-circle-line me-1"></i>Alpa</span>',
            default => '<span class="badge bg-light text-muted px-2 py-1">Belum Absen</span>',
        };
    }
}

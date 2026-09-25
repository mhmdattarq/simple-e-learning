<?php

namespace App\Enums;

enum CourseStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';
    case Published = 'published';
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Archived = 'archived';

    /**
     * Get Indonesian human-friendly label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Submitted => 'Diajukan',
            self::Approved => 'Disetujui',
            self::Published => 'Dibuka',
            self::Ongoing => 'Berjalan',
            self::Completed => 'Selesai',
            self::Archived => 'Diarsipkan',
        };
    }

    /**
     * Get badge CSS classes for display.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-secondary text-white',
            self::Submitted => 'bg-info text-white',
            self::Approved => 'bg-primary text-white',
            self::Published => 'bg-success text-white',
            self::Ongoing => 'bg-warning text-dark',
            self::Completed => 'bg-dark text-white',
            self::Archived => 'bg-secondary-subtle text-secondary border',
        };
    }

    /**
     * Icon representation (Remix Icon).
     */
    public function icon(): string
    {
        return match ($this) {
            self::Draft => 'ri-draft-line',
            self::Submitted => 'ri-send-plane-line',
            self::Approved => 'ri-checkbox-circle-line',
            self::Published => 'ri-broadcast-line',
            self::Ongoing => 'ri-play-circle-line',
            self::Completed => 'ri-check-double-line',
            self::Archived => 'ri-archive-line',
        };
    }
}

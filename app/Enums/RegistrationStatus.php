<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case RevisionRequired = 'revision_required';
    case Rejected = 'rejected';
    case Active = 'active';
    case Completed = 'completed';

    /**
     * Get user-friendly label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Verifikasi',
            self::Verified => 'Diverifikasi / Lolos',
            self::RevisionRequired => 'Perlu Perbaikan',
            self::Rejected => 'Ditolak',
            self::Active => 'Aktif Belajar',
            self::Completed => 'Tuntas Kelas',
        };
    }

    /**
     * Get badge CSS classes for status display.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-warning text-dark',
            self::Verified => 'bg-info text-white',
            self::RevisionRequired => 'bg-orange text-white',
            self::Rejected => 'bg-danger text-white',
            self::Active => 'bg-primary text-white',
            self::Completed => 'bg-success text-white',
        };
    }
}

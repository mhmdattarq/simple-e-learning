<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Peserta = 'peserta';

    /**
     * Get label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator (Super Admin)',
            self::Peserta => 'Peserta Kelas',
        };
    }

    /**
     * Check if role has internal management dashboard access.
     */
    public function hasAdminAccess(): bool
    {
        return $this === self::Admin;
    }
}

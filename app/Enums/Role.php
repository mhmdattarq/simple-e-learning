<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Mentor = 'mentor';
    case Verifikator = 'verifikator';
    case Pimpinan = 'pimpinan';
    case Peserta = 'peserta';

    /**
     * Get label for display.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin Diklat',
            self::Mentor => 'Mentor / Widyaiswara',
            self::Verifikator => 'Verifikator Berkas',
            self::Pimpinan => 'Pimpinan / Eksekutif',
            self::Peserta => 'Peserta / Siswa ASN',
        };
    }

    /**
     * Check if role has internal management dashboard access.
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this, [self::Admin, self::Mentor, self::Verifikator, self::Pimpinan], true);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the singleton contact settings instance.
     */
    public static function getSettings(): self
    {
        return self::firstOrCreate(['id' => 1]);
    }

    /**
     * Get clean WhatsApp number formatted with country code.
     */
    public function getCleanWhatsappNumberAttribute(): ?string
    {
        if (! $this->whatsapp_number) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', (string) $this->whatsapp_number);
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        return $phone ?: null;
    }

    /**
     * Get direct WhatsApp URL.
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        return $this->clean_whatsapp_number ? 'https://wa.me/'.$this->clean_whatsapp_number : null;
    }

    /**
     * Determine if contact settings have any displayable information.
     */
    public function hasContactInfo(): bool
    {
        return ! empty($this->office_title)
            || ! empty($this->address)
            || ! empty($this->whatsapp_number)
            || ! empty($this->email)
            || ! empty($this->service_days)
            || ! empty($this->service_hours);
    }
}

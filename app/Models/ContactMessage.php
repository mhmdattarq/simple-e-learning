<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'replied_at' => 'datetime',
        ];
    }

    /**
     * Get formatted WhatsApp number with country code.
     */
    public function getCleanPhoneAttribute(): ?string
    {
        if (! $this->phone) {
            return null;
        }

        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Generate quick WhatsApp chat reply URL.
     */
    public function getWhatsappReplyUrlAttribute(): ?string
    {
        if (! $this->clean_phone) {
            return null;
        }

        $greeting = urlencode("Halo {$this->name}, terima kasih telah menghubungi helpdesk SIMPEL BKPSDM Aceh Timur terkait pertanyaan: \"{$this->subject}\". ");

        return "https://wa.me/{$this->clean_phone}?text={$greeting}";
    }

    /**
     * Generate mailto reply URL.
     */
    public function getEmailReplyUrlAttribute(): string
    {
        $subject = urlencode("Tanggapan SIMPEL BKPSDM: {$this->subject}");

        return "mailto:{$this->email}?subject={$subject}";
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(): void
    {
        if ($this->status === 'unread') {
            $this->update(['status' => 'read']);
        }
    }

    /**
     * Mark message as replied.
     */
    public function markAsReplied(?string $notes = null): void
    {
        $data = [
            'status' => 'replied',
            'replied_at' => now(),
        ];

        if ($notes !== null) {
            $data['admin_notes'] = $notes;
        }

        $this->update($data);
    }
}

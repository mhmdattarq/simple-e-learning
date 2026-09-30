<?php

namespace App\Repositories;

use App\Models\AuditLog;
use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ContactMessageRepo
{
    /**
     * Query builder for Yajra DataTables server-side rendering.
     */
    public static function getDt(?string $status = null): Builder
    {
        $query = ContactMessage::query()->latest('created_at');

        if ($status && in_array($status, ['unread', 'read', 'replied'], true)) {
            $query->where('status', $status);
        }

        return $query;
    }

    /**
     * Find message by ID.
     */
    public static function getById(int $id): ContactMessage
    {
        return ContactMessage::findOrFail($id);
    }

    /**
     * Mark message as read and log audit.
     */
    public static function markAsRead(int $id): ContactMessage
    {
        $message = self::getById($id);
        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);

            AuditLog::log(
                action: 'contact_message.read',
                auditable: $message,
                notes: "Admin menandai pesan dari {$message->name} sebagai dibaca",
                userId: Auth::id()
            );
        }

        return $message;
    }

    /**
     * Update message status.
     */
    public static function updateStatus(int $id, string $status, ?string $notes = null): array
    {
        try {
            $message = self::getById($id);
            $validStatuses = ['unread', 'read', 'replied'];

            if (! in_array($status, $validStatuses, true)) {
                return ['status' => false, 'message' => 'Status pesan tidak valid'];
            }

            $updateData = ['status' => $status];
            if ($status === 'replied') {
                $updateData['replied_at'] = now();
            }
            if ($notes !== null) {
                $updateData['admin_notes'] = $notes;
            }

            $message->update($updateData);

            AuditLog::log(
                action: 'contact_message.status_updated',
                auditable: $message,
                newValues: ['status' => $status, 'admin_notes' => $notes],
                notes: "Admin mengubah status pesan dari {$message->name} menjadi: {$status}",
                userId: Auth::id()
            );

            return ['status' => true, 'message' => 'Status pesan berhasil diperbarui', 'data' => $message];
        } catch (\Throwable $e) {
            Log::error('Error updating contact message status: '.$e->getMessage());

            return ['status' => false, 'message' => 'Gagal memperbarui status pesan: '.$e->getMessage()];
        }
    }

    /**
     * Delete contact message.
     */
    public static function delete(int $id): array
    {
        try {
            $message = ContactMessage::find($id);
            if (! $message) {
                return ['status' => false, 'message' => 'Pesan tidak ditemukan atau sudah dihapus sebelumnya.'];
            }

            $sender = $message->name;
            $subject = $message->subject;

            $message->delete();

            AuditLog::log(
                action: 'contact_message.deleted',
                notes: "Admin menghapus pesan dari {$sender} (Subjek: {$subject})",
                userId: Auth::id()
            );

            return ['status' => true, 'message' => "Pesan dari \"{$sender}\" berhasil dihapus"];
        } catch (\Throwable $e) {
            Log::error('Error deleting contact message: '.$e->getMessage());

            return ['status' => false, 'message' => 'Gagal menghapus pesan: '.$e->getMessage()];
        }
    }

    /**
     * Get aggregate statistics for dashboard & counters.
     *
     * @return array{total: int, unread: int, read: int, replied: int}
     */
    public static function getStats(): array
    {
        return [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::where('status', 'unread')->count(),
            'read' => ContactMessage::where('status', 'read')->count(),
            'replied' => ContactMessage::where('status', 'replied')->count(),
        ];
    }
}

<?php

namespace App\Repositories;

use App\Mail\ResetPasswordNotification;
use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetRepo
{
    /**
     * Masa berlaku token pemulihan kata sandi dalam menit.
     */
    public const TOKEN_EXPIRY_MINUTES = 60;

    /**
     * Mengirimkan tautan reset kata sandi dengan token terenkripsi SHA-256.
     * Menerapkan prinsip Anti-User Enumeration (selalu return true).
     */
    public static function sendResetLink(string $email, ?string $ip = null, ?string $userAgent = null): bool
    {
        $normalizedEmail = Str::lower(trim($email));
        $user = User::where('email', $normalizedEmail)->first();

        // Anti-User Enumeration: Jika email tidak ditemukan, tetap beri respon sukses tanpa bocorkan eksistensi
        if (! $user) {
            return true;
        }

        // Hapus token sebelumnya untuk email ini (mencegah penumpukan)
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // Buat token kriptografi acak 64 karakter
        $rawToken = Str::random(64);
        $tokenHash = hash('sha256', $rawToken);

        // Simpan versi hash ke database
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => $tokenHash,
            'created_at' => now(),
        ]);

        // Kirim email notifikasi secara asinkron (background queue)
        Mail::to($user->email)->queue(new ResetPasswordNotification($user, $rawToken));

        // Catat ke immutable audit log
        AuditLog::log(
            action: 'user.password_reset_requested',
            auditable: $user,
            oldValues: null,
            newValues: [
                'email' => $user->email,
                'ip' => $ip,
                'user_agent' => $userAgent ? Str::limit($userAgent, 255) : null,
            ],
            notes: 'Permintaan pemulihan kata sandi diajukan (menunggu verifikasi email).',
            userId: $user->id
        );

        return true;
    }

    /**
     * Memverifikasi validitas token pemulihan kata sandi.
     *
     * @return array{valid: bool, reason: ?string, user: ?User}
     */
    public static function verifyToken(string $email, string $rawToken): array
    {
        $normalizedEmail = Str::lower(trim($email));
        $record = DB::table('password_reset_tokens')->where('email', $normalizedEmail)->first();

        if (! $record) {
            return [
                'valid' => false,
                'reason' => 'Tautan pemulihan kata sandi tidak valid atau sudah pernah digunakan sebelumnya.',
                'user' => null,
            ];
        }

        // Cocokkan token menggunakan hash_equals untuk mitigasi timing attack
        $calculatedHash = hash('sha256', trim($rawToken));
        if (! hash_equals($record->token, $calculatedHash)) {
            return [
                'valid' => false,
                'reason' => 'Tautan pemulihan kata sandi tidak valid atau tidak cocok.',
                'user' => null,
            ];
        }

        // Cek kedaluwarsa token (Maksimal 60 menit)
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(self::TOKEN_EXPIRY_MINUTES)->isPast()) {
            return [
                'valid' => false,
                'reason' => 'Tautan pemulihan kata sandi telah kedaluwarsa (hanya berlaku 60 menit). Silakan ajukan permohonan baru.',
                'user' => null,
            ];
        }

        $user = User::where('email', $normalizedEmail)->first();
        if (! $user) {
            return [
                'valid' => false,
                'reason' => 'Akun pengguna untuk email tersebut tidak ditemukan.',
                'user' => null,
            ];
        }

        return [
            'valid' => true,
            'reason' => null,
            'user' => $user,
        ];
    }

    /**
     * Mengeksekusi pembaruan kata sandi baru, menghapus token (single-use),
     * dan memutus seluruh sesi aktif di perangkat lain.
     *
     * @return array{success: bool, message: string}
     */
    public static function resetPassword(string $email, string $rawToken, string $newPassword, ?string $ip = null): array
    {
        $verification = self::verifyToken($email, $rawToken);

        if (! $verification['valid']) {
            return [
                'success' => false,
                'message' => $verification['reason'] ?? 'Tautan pemulihan tidak valid.',
            ];
        }

        /** @var User $user */
        $user = $verification['user'];

        DB::transaction(function () use ($user, $newPassword, $ip) {
            // Update password dan putus sesi remember token
            $user->forceFill([
                'password' => Hash::make($newPassword),
                'remember_token' => Str::random(60),
            ])->save();

            // Invalidation of active sessions di perangkat lain
            DB::table('sessions')->where('user_id', $user->id)->delete();

            // Hapus token pemulihan (Single-Use Rule)
            DB::table('password_reset_tokens')->where('email', $user->email)->delete();

            // Catat audit trail
            AuditLog::log(
                action: 'user.password_reset_success',
                auditable: $user,
                oldValues: null,
                newValues: [
                    'email' => $user->email,
                    'ip' => $ip,
                ],
                notes: 'Kata sandi berhasil diperbarui melalui tautan pemulihan email. Seluruh sesi aktif di perangkat lain telah diputus.',
                userId: $user->id
            );
        });

        return [
            'success' => true,
            'message' => 'Kata sandi Anda berhasil diperbarui. Silakan masuk menggunakan kata sandi baru Anda.',
        ];
    }
}

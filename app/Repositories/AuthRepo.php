<?php

namespace App\Repositories;

use App\Enums\Role;
use App\Mail\VerifyEmailNotification;
use App\Models\AuditLog;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthRepo
{
    /**
     * Register a new ASN participant (Peserta / Siswa) with email verification.
     */
    public static function registerPeserta(array $data): ?User
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = User::create([
                    'name' => trim($data['name']),
                    'email' => Str::lower(trim($data['email'])),
                    'phone_number' => trim($data['phone_number']),
                    'address' => trim($data['address']),
                    'password' => Hash::make($data['password']),
                    'role' => Role::Peserta,
                    'email_verified_at' => null,
                ]);

                // Buat token verifikasi aman 64 karakter
                $rawToken = EmailVerification::createTokenFor($user);

                // Kirim email notifikasi aktivasi
                Mail::to($user->email)->send(new VerifyEmailNotification($user, $rawToken));

                // Catat ke immutable audit log
                AuditLog::log(
                    action: 'user.registered',
                    auditable: $user,
                    oldValues: null,
                    newValues: [
                        'email' => $user->email,
                        'name' => $user->name,
                        'phone_number' => $user->phone_number,
                        'address' => $user->address,
                    ],
                    notes: 'Pendaftaran mandiri akun peserta (menunggu verifikasi email).'
                );

                return $user;
            });
        } catch (\Exception $e) {
            Log::error('Pendaftaran akun peserta gagal', [
                'data' => [
                    'name' => $data['name'] ?? null,
                    'email' => $data['email'] ?? null,
                    'phone_number' => $data['phone_number'] ?? null,
                ],
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}

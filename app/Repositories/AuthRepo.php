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
                    'nip' => trim($data['nip']),
                    'email' => Str::lower(trim($data['email'])),
                    'password' => Hash::make($data['password']),
                    'role' => Role::Peserta,
                    'phone_number' => $data['phone_number'] ?? null,
                    'opd_agency' => $data['opd_agency'] ?? null,
                    'position' => $data['position'] ?? null,
                    'rank_class' => $data['rank_class'] ?? null,
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
                        'nip' => $user->nip,
                        'email' => $user->email,
                        'name' => $user->name,
                        'opd_agency' => $user->opd_agency,
                    ],
                    notes: 'Pendaftaran mandiri akun peserta ASN (menunggu verifikasi email).'
                );

                return $user;
            });
        } catch (\Exception $e) {
            Log::error('Pendaftaran akun peserta gagal', [
                'data' => [
                    'name' => $data['name'] ?? null,
                    'nip' => $data['nip'] ?? null,
                    'email' => $data['email'] ?? null,
                ],
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}

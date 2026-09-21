<?php

namespace App\Repositories;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthRepo
{
    /**
     * Register a new ASN participant (Peserta / Siswa).
     */
    public static function registerPeserta(array $data): ?User
    {
        try {
            return DB::transaction(function () use ($data) {
                return User::create([
                    'name' => $data['name'],
                    'nip' => $data['nip'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role' => Role::Peserta,
                    'phone_number' => $data['phone_number'] ?? null,
                    'opd_agency' => $data['opd_agency'] ?? null,
                    'position' => $data['position'] ?? null,
                    'rank_class' => $data['rank_class'] ?? null,
                ]);
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $dummyPeserta = [
            [
                'name' => 'Ahmad Fauzi, S.Kom',
                'email' => 'peserta1@simpel.go.id',
                'phone_number' => '081234567891',
                'address' => 'Jl. Medan - Banda Aceh No. 45, Idi Rayeuk, Aceh Timur',
                'role' => 'peserta',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siti Rahmawati, S.Tr.Pel',
                'email' => 'peserta2@simpel.go.id',
                'phone_number' => '081234567892',
                'address' => 'Jl. Cut Nyak Dien No. 12, Peureulak, Aceh Timur',
                'role' => 'peserta',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dedi Kurniawan, S.AP',
                'email' => 'peserta3@simpel.go.id',
                'phone_number' => '081234567893',
                'address' => 'Jl. Tgk Chik Di Tiro No. 8, Simpang Ulim, Aceh Timur',
                'role' => 'peserta',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($dummyPeserta as $user) {
            $exists = DB::table('users')->where('email', $user['email'])->first();

            if ($exists) {
                DB::table('users')->where('email', $user['email'])->update([
                    'name' => $user['name'],
                    'phone_number' => $user['phone_number'],
                    'address' => $user['address'],
                    'role' => $user['role'],
                    'password' => $user['password'],
                    'email_verified_at' => $user['email_verified_at'],
                    'remember_token' => $user['remember_token'],
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('users')->insert($user);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        DB::table('users')->whereIn('email', [
            'peserta1@simpel.go.id',
            'peserta2@simpel.go.id',
            'peserta3@simpel.go.id',
        ])->delete();
    }
};

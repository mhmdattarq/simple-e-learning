<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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

        // 1. Hapus akun dummy legacy dan akun dengan role lama yang tidak valid
        DB::table('users')->whereIn('email', [
            'peserta@simpel.go.id',
            'mentor@simpel.go.id',
            'verifikator@simpel.go.id',
            'pimpinan@simpel.go.id',
        ])->delete();

        // Bersihkan seluruh user yang role-nya di luar enum Role resmi (admin / peserta)
        DB::table('users')
            ->whereNotIn('role', ['admin', 'peserta'])
            ->delete();

        // 2. Pastikan akun Super Admin tunggal tersedia dan terverifikasi
        $adminData = [
            'name' => 'Muhammad Suryasyah, S.STP',
            'role' => 'admin',
            'phone_number' => '081269001001',
            'address' => 'Jl. Merdeka No. 1, Idi Rayeuk, Aceh Timur',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'updated_at' => now(),
        ];

        $adminExists = DB::table('users')->where('email', 'admin@simpel.go.id')->first();

        if ($adminExists) {
            DB::table('users')->where('email', 'admin@simpel.go.id')->update($adminData);
        } else {
            $adminData['email'] = 'admin@simpel.go.id';
            $adminData['created_at'] = now();
            DB::table('users')->insert($adminData);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu menghapus data admin saat rollback
    }
};

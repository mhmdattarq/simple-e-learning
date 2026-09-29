<?php

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        if (class_exists(UserSeeder::class)) {
            (new UserSeeder)->run();

            return;
        }

        $users = [
            [
                'name' => 'Muhammad Suryasyah, S.STP',
                'email' => 'admin@simpel.go.id',
                'role' => Role::Admin,
                'phone_number' => '081269001001',
                'address' => 'Jl. Merdeka No. 1, Idi Rayeuk, Aceh Timur',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Cut Mutia, S.Sos',
                'email' => 'peserta@simpel.go.id',
                'role' => Role::Peserta,
                'phone_number' => '081269005005',
                'address' => 'Jl. Medan - Banda Aceh Km. 370, Aceh Timur',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::whereIn('email', [
            'admin@simpel.go.id',
            'peserta@simpel.go.id',
        ])->delete();
    }
};

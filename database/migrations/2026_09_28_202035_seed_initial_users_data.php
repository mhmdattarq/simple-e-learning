<?php

use App\Enums\Role;
use App\Models\User;
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
        ])->delete();
    }
};

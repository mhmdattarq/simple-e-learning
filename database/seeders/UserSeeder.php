<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Muhammad Suryasyah, S.STP',
                'email' => 'admin@simpel.go.id',
                'role' => Role::Admin,
                'phone_number' => '081269001001',
                'address' => 'Jl. Merdeka No. 1, Idi Rayeuk, Aceh Timur',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $userData) {
            $userData['email_verified_at'] = now();
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}

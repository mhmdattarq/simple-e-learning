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
                'nip' => '198501012010011001',
                'role' => Role::Admin,
                'opd_agency' => 'BKPSDM Kabupaten Aceh Timur',
                'position' => 'Administrator Diklat (Super Admin)',
                'rank_class' => 'Penata Tk. I (III/d)',
                'phone_number' => '081269001001',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Cut Mutia, S.Sos',
                'email' => 'peserta@simpel.go.id',
                'nip' => '199205052018011005',
                'role' => Role::Peserta,
                'opd_agency' => 'Dinas Pendidikan & Kebudayaan',
                'position' => 'Staf Pelaksana',
                'rank_class' => 'Penata Muda (III/a)',
                'phone_number' => '081269005005',
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

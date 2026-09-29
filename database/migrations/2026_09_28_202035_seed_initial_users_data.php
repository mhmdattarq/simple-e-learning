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
                'nip' => '198501012010011001',
                'role' => Role::Admin,
                'opd_agency' => 'BKPSDM Kabupaten Aceh Timur',
                'position' => 'Administrator Diklat (Super Admin)',
                'rank_class' => 'Penata Tk. I (III/d)',
                'phone_number' => '081269001001',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
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

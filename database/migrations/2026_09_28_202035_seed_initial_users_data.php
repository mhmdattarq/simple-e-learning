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
                'position' => 'Administrator Diklat',
                'rank_class' => 'Penata Tk. I (III/d)',
                'phone_number' => '081269001001',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dr. Fauzan, M.Pd',
                'email' => 'mentor@simpel.go.id',
                'nip' => '198002022005011002',
                'role' => Role::Mentor,
                'opd_agency' => 'BKPSDM Kabupaten Aceh Timur',
                'position' => 'Widyaiswara Ahli Madya',
                'rank_class' => 'Pembina (IV/a)',
                'phone_number' => '081269002002',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Nur Aini, S.Kom',
                'email' => 'verifikator@simpel.go.id',
                'nip' => '198703032011012003',
                'role' => Role::Verifikator,
                'opd_agency' => 'BKPSDM Kabupaten Aceh Timur',
                'position' => 'Petugas Verifikasi Berkas',
                'rank_class' => 'Penata Muda Tk. I (III/b)',
                'phone_number' => '081269003003',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Teuku Dedi Iskandar, S.STP, M.SP',
                'email' => 'pimpinan@simpel.go.id',
                'nip' => '197504041999031004',
                'role' => Role::Pimpinan,
                'opd_agency' => 'BKPSDM Kabupaten Aceh Timur',
                'position' => 'Kepala BKPSDM Aceh Timur',
                'rank_class' => 'Pembina Utama Muda (IV/c)',
                'phone_number' => '081269004004',
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
            'mentor@simpel.go.id',
            'verifikator@simpel.go.id',
            'pimpinan@simpel.go.id',
            'peserta@simpel.go.id',
        ])->delete();
    }
};

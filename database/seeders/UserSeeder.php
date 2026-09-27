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
                'position' => 'Administrator Diklat',
                'rank_class' => 'Penata Tk. I (III/d)',
                'phone_number' => '081269001001',
                'password' => Hash::make('password'),
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

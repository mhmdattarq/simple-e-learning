<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin?->id;

        $catKepemimpinan = Category::where('slug', 'pelatihan-kepemimpinan')->first()?->id ?? 1;
        $catTeknis = Category::where('slug', 'pelatihan-teknis')->first()?->id ?? 2;
        $catFungsional = Category::where('slug', 'pelatihan-fungsional')->first()?->id ?? 3;

        $courses = [
            [
                'code' => 'PLT-2026-001',
                'title' => 'Pelatihan Kepemimpinan Administrator (PKA) Angkatan I',
                'category_id' => $catKepemimpinan,
                'type' => 'batch',
                'start_date' => '2026-10-01',
                'end_date' => '2026-11-15',
                'method' => 'hybrid',
                'location' => 'Aula Utama BKPSDM Aceh Timur & Zoom Meeting',
                'quota' => 40,
                'target_audience' => 'Pejabat Administrator (Eselon III) Instansi Daerah',
                'budget_source' => 'APBK Aceh Timur TA 2026',
                'description' => 'Program penguatan kepemimpinan visioner dan manajerial aparatur sipil negara dalam memimpin perubahan tata kelola pemerintahan.',
                'status' => 'published',
                'created_by' => $adminId,
            ],
            [
                'code' => 'PLT-2026-002',
                'title' => 'Pelatihan Teknis Digitalisasi Pelayanan Publik OPD',
                'category_id' => $catTeknis,
                'type' => 'permanent',
                'start_date' => null,
                'end_date' => null,
                'method' => 'daring',
                'location' => 'LMS Daring SIMPEL BKPSDM',
                'quota' => 100,
                'target_audience' => 'Pengelola IT dan Admin Pelayanan Publik OPD',
                'budget_source' => 'DPA-BKPSDM Aceh Timur TA 2026',
                'description' => 'Pembelajaran mandiri implementasi SPBE, keamanan siber dasar, dan integrasi sistem layanan kepegawaian digital.',
                'status' => 'published',
                'created_by' => $adminId,
            ],
            [
                'code' => 'PLT-2026-003',
                'title' => 'Bimtek Penyusunan Kerangka Acuan Kerja (KAK) dan Anggaran Berbasis Kinerja',
                'category_id' => $catTeknis,
                'type' => 'batch',
                'start_date' => '2026-10-10',
                'end_date' => '2026-10-14',
                'method' => 'luring',
                'location' => 'Ruang Rapat Setdakab Aceh Timur',
                'quota' => 35,
                'target_audience' => 'Perencana Muda & Kasubbag Program OPD',
                'budget_source' => 'APBK Aceh Timur TA 2026',
                'description' => 'Bimbingan teknis penyusunan KAK, analisis standar biaya masukan (SBM), serta justifikasi anggaran program perangkat daerah.',
                'status' => 'draft',
                'created_by' => $adminId,
            ],
            [
                'code' => 'PLT-2026-004',
                'title' => 'Pelatihan Fungsional Analis Kebijakan Tingkat Ahli Pertama',
                'category_id' => $catFungsional,
                'type' => 'permanent',
                'start_date' => null,
                'end_date' => null,
                'method' => 'daring',
                'location' => 'Ruang Belajar Mandiri Daring SIMPEL',
                'quota' => 50,
                'target_audience' => 'Pejabat Fungsional Tertentu (JFT) Analis Kebijakan',
                'budget_source' => 'DPA-BKPSDM Aceh Timur TA 2026',
                'description' => 'Modul peningkatan kompetensi penyusunan policy brief, policy paper, dan advokasi kebijakan publik daerah.',
                'status' => 'published',
                'created_by' => $adminId,
            ],
            [
                'code' => 'PLT-2026-005',
                'title' => 'Orientasi Pengenalan Nilai & Etika Instansi Pemerintah bagi PPPK',
                'category_id' => $catKepemimpinan,
                'type' => 'batch',
                'start_date' => '2026-11-01',
                'end_date' => '2026-11-10',
                'method' => 'luring',
                'location' => 'Gedung Idi Sport Center (ISC) Kabupaten Aceh Timur',
                'quota' => 120,
                'target_audience' => 'PPPK Formasi 2025/2026',
                'budget_source' => 'APBK Aceh Timur TA 2026',
                'description' => 'Pengenalan fungsi ASN BerAKHLAK, struktur birokrasi, disiplin kepegawaian, dan etika profesi pelayanan masyarakat.',
                'status' => 'published',
                'created_by' => $adminId,
            ],
        ];

        foreach ($courses as $item) {
            Course::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }
    }
}

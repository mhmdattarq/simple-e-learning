<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Kelas Kepemimpinan',
                'description' => 'Program penguatan kepemimpinan transformasional dan manajerial ASN BKPSDM Aceh Timur.',
            ],
            [
                'name' => 'Kelas Teknis',
                'description' => 'Peningkatan keahlian teknis operasional, digitalisasi, dan tata kelola instansi pemerintah.',
            ],
            [
                'name' => 'Kelas Fungsional',
                'description' => 'Pengembangan kompetensi profesional jabatan fungsional ASN sesuai regulasi kepegawaian.',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                ]
            );
        }
    }
}

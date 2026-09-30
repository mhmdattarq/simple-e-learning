<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DummyDataCleanerSeeder extends Seeder
{
    /**
     * Hapus tuntas seluruh data dummy kursus, kurikulum, kuis, dan file thumbnail dummy.
     * Aman dijalankan di production atau saat ingin mereset data dummy.
     */
    public function run(): void
    {
        $dummySlugs = [
            'dummy-pelayanan-publik-prima',
            'dummy-manajemen-proyek-kinerja',
            'dummy-kepemimpinan-adaptif',
            'dummy-core-values-berakhlak',
            'dummy-keamanan-siber-kesadaran-data',
            'dummy-manajemen-waktu-fokus-kerja',
        ];

        // 1. Ambil semua kursus dummy (termasuk yang mungkin ter-soft-delete)
        $courses = Course::withTrashed()->whereIn('slug', $dummySlugs)->get();

        $count = $courses->count();

        foreach ($courses as $course) {
            // Karena cascading foreign key sudah diatur di migrasi,
            // forceDelete() pada course akan otomatis menghapus:
            // chapters, lessons, quizzes, quiz_questions, quiz_options, dan attempts terkait.
            $course->forceDelete();
        }

        // 2. Bersihkan file thumbnail dummy di storage
        if (Storage::disk('public')->exists('courses/dummy')) {
            Storage::disk('public')->deleteDirectory('courses/dummy');
        }

        $this->command->info("Berhasil membersihkan {$count} kelas dummy beserta seluruh materi, kuis, dan file asetnya.");
    }
}

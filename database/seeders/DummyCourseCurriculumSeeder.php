<?php

namespace Database\Seeders;

use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DummyCourseCurriculumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0. Pastikan file thumbnail SVG tersedia di storage
        $this->ensureThumbnailsExist();

        // 1. Pastikan kategori dasar tersedia
        $this->call(CategorySeeder::class);

        $catKepemimpinan = Category::where('slug', 'kelas-kepemimpinan')->first() ?? Category::first();
        $catTeknis = Category::where('slug', 'kelas-teknis')->first() ?? Category::first();
        $catFungsional = Category::where('slug', 'kelas-fungsional')->first() ?? Category::first();

        // Admin creator
        $admin = User::first();
        $adminId = $admin ? $admin->id : null;

        // Base time reference
        $now = Carbon::now();

        // 2. Definisi Data Kursus
        $coursesData = [
            // ==========================================
            // KELAS BATCH 1: Pelayanan Publik Prima
            // ==========================================
            [
                'title' => 'Pelayanan Publik Prima (Service Excellence) bagi Aparatur',
                'slug' => 'dummy-pelayanan-publik-prima',
                'category_id' => $catFungsional->id,
                'type' => 'batch',
                'price' => 0,
                'status' => CourseStatus::Published,
                'start_date' => $now->copy()->addDays(7)->setTime(8, 0),
                'end_date' => $now->copy()->addDays(21)->setTime(16, 0),
                'registration_open_at' => $now->copy()->subDays(3)->setTime(0, 0),
                'registration_close_at' => $now->copy()->addDays(5)->setTime(23, 59),
                'thumbnail' => 'courses/dummy/batch_pelayanan_publik.svg',
                'description' => 'Pelatihan intensif berjadwal yang dirancang untuk membangun mindset pelayanan yang berorientasi pada kepuasan masyarakat. Peserta akan mempelajari etika komunikasi, teknik active listening, serta resolusi komplain secara solutif sesuai standar pelayanan publik modern.',
                'chapters' => [
                    [
                        'title' => 'Bab 1: Fondasi Pola Pikir Service Excellence',
                        'order' => 1,
                        'lessons' => [
                            [
                                'title' => 'Konsep Dasar & Pergeseran Paradigma Pelayanan Publik',
                                'content_type' => 'article',
                                'body_text' => '<h2>Membangun Orientasi Pelayanan yang Bernilai Tambah</h2>
<p>Dalam era transparansi dan keterbukaan informasi, aparatur pemerintah tidak lagi diposisikan sebagai pihak birokrat yang dilayani, melainkan fasilitator dan abdi masyarakat yang memberikan solusi tepat waktu.</p>
<h3>Prinsip Utama Pelayanan Prima:</h3>
<ul>
    <li><strong>Accessibility (Kemudahan Akses):</strong> Prosedur yang tidak berbelit-belit dan kejelasan alur pengurusan.</li>
    <li><strong>Responsiveness (Kecepatan & Ketanggapan):</strong> Merespons kebutuhan dan pertanyaan pemohon dengan segera tanpa menunda-nunda.</li>
    <li><strong>Empathy & Respect:</strong> Memperlakukan setiap warga dengan martabat yang sama tanpa memandang latar belakang sosial dan ekonomi.</li>
</ul>
<p>Kualitas pelayanan ditentukan bukan hanya dari hasil akhir, namun dari impresi serta pengalaman pemohon selama berinteraksi dengan petugas.</p>',
                            ],
                            [
                                'title' => 'Video Studi: Implementasi Komunikasi Efektif di Front Office',
                                'content_type' => 'video',
                                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                                'body_text' => '<p>Simak rekaman simulasi komunikasi persuasif dan etika bahasa tubuh petugas front office saat menghadapi antrian padat di kantor layanan publik.</p>',
                            ],
                        ],
                        'quiz' => [
                            'title' => 'Kuis Bab 1: Mindset Pelayanan',
                            'description' => 'Uji pemahaman dasar mengenai prinsip pelayanan prima dan orientasi kepuasan masyarakat.',
                            'time_limit_minutes' => 15,
                            'passing_score' => 70,
                            'questions' => [
                                [
                                    'question_text' => 'Manakah di bawah ini yang mencerminkan esensi utama dari pergeseran paradigma aparatur di era modern?',
                                    'explanation' => 'Aparatur masa kini bertransformasi dari birokrat kaku menjadi abdi negara yang mengutamakan kepuasan dan solusi nyata bagi masyarakat.',
                                    'score' => 15,
                                    'options' => [
                                        ['text' => 'Birokrat bertindak sebagai pelayan masyarakat dan pemecah masalah (solutif).', 'is_correct' => true],
                                        ['text' => 'Masyarakat harus menyesuaikan diri sepenuhnya dengan jam kerja dinas tanpa pengecualian.', 'is_correct' => false],
                                        ['text' => 'Mengurangi interaksi langsung dan menyerahkan semua proses ke vendor pihak ketiga.', 'is_correct' => false],
                                        ['text' => 'Menekankan kekuasaan hierarki administrasi di atas kemudahan prosedur.', 'is_correct' => false],
                                    ],
                                ],
                                [
                                    'question_text' => 'Ketika seorang warga merasa kebingungan di depan loket informasi, tindakan awal terbaik yang menunjukkan sikap proaktif adalah...',
                                    'explanation' => 'Menyapa lebih dahulu dengan ramah dan menanyakan kendala secara sopan merupakan ciri proaktif petugas pelayanan.',
                                    'score' => 15,
                                    'options' => [
                                        ['text' => 'Menyapa dengan senyum ramah dan menanyakan bantuan apa yang dibutuhkan.', 'is_correct' => true],
                                        ['text' => 'Menunggu sampai pemohon tersebut memberanikan diri mengetuk kaca loket.', 'is_correct' => false],
                                        ['text' => 'Memberikan brosur tanpa penjelasan dan menyuruhnya membaca sendiri.', 'is_correct' => false],
                                        ['text' => 'Meminta pemohon kembali esok hari saat antrian tidak ramai.', 'is_correct' => false],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Bab 2: Manajemen & Resolusi Komplain Warga',
                        'order' => 2,
                        'lessons' => [
                            [
                                'title' => 'Metode HEAT dalam Meredakan Situasi Tegang',
                                'content_type' => 'article',
                                'body_text' => '<h2>Teknik HEAT (Hear, Empathize, Apologize, Take Action)</h2>
<p>Ketika berhadapan dengan warga yang emosional akibat kendala berkas, petugas dituntut untuk tetap tenang dan tidak terpancing defensif.</p>
<ol>
    <li><strong>Hear (Dengarkan Sepenuhnya):</strong> Biarkan pemohon menyampaikan kekecewaannya hingga tuntas tanpa memotong pembicaraan.</li>
    <li><strong>Empathize (Tunjukkan Empati):</strong> Validasi perasaan mereka: <em>"Kami sangat memahami kekhawatiran dan ketidaknyamanan Bapak/Ibu terkait keterlambatan ini."</em></li>
    <li><strong>Apologize (Minta Maaf atas Ketidaknyamanan):</strong> Mohon maaf secara tulus atas situasi yang dialami tanpa menyalahkan instansi atau rekan kerja lain.</li>
    <li><strong>Take Action (Tindakan Solutif Segera):</strong> Tawarkan langkah konkret dan beri tenggat waktu penyelesaian yang jelas serta terukur.</li>
</ol>',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Bab 3: Etika Pelayanan Terpadu & Digitalisasi',
                        'order' => 3,
                        'lessons' => [
                            [
                                'title' => 'Standar Pelayanan Terpadu Satu Pintu (PTSP)',
                                'content_type' => 'article',
                                'body_text' => '<h2>Penerapan SOP dan Transparansi Biaya</h2>
<p>Pelayanan publik wajib berlandaskan asas kepastian hukum dan transparansi. Petugas tidak diperkenankan menerima gratifikasi dalam bentuk apa pun (hadiah, bingkisan, maupun uang terima kasih).</p>',
                            ],
                        ],
                    ],
                ],
                'final_quiz' => [
                    'title' => 'Evaluasi Akhir: Studi Kasus Pelayanan Publik Prima',
                    'description' => 'Evaluasi komprehensif kelulusan batch untuk menguji kesiapan peserta menghadapi dinamika pelayanan di lapangan.',
                    'time_limit_minutes' => 30,
                    'passing_score' => 75,
                    'questions' => [
                        [
                            'question_text' => 'Seorang warga datang dengan nada tinggi karena berkas izin usahanya belum selesai melampaui estimasi waktu SOP. Langkah awal apa yang paling tepat?',
                            'explanation' => 'Mendengarkan tanpa memotong dan memvalidasi kekhawatiran warga adalah cara menurunkan tensi sebelum mencari solusi.',
                            'score' => 20,
                            'options' => [
                                ['text' => 'Mendengarkan dengan tenang, mencatat nomor berkas, lalu memeriksa status kendala tanpa bersikap defensif.', 'is_correct' => true],
                                ['text' => 'Meminta petugas keamanan segera mengantar warga tersebut keluar gedung kantor.', 'is_correct' => false],
                                ['text' => 'Menyalahkan divisi teknis lain yang bertugas memproses verifikasi lapangan.', 'is_correct' => false],
                                ['text' => 'Menolak melayani warga tersebut sampai nada bicaranya kembali pelan.', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'Prinsip akuntabilitas dalam pelayanan publik menuntut petugas untuk...',
                            'explanation' => 'Akuntabilitas berarti setiap tindakan dan output kerja dapat dipertanggungjawabkan kepada publik sesuai ketentuan hukum yang berlaku.',
                            'score' => 20,
                            'options' => [
                                ['text' => 'Bekerja secara transparan dan dapat mempertanggungjawabkan kinerjanya kepada masyarakat serta hukum.', 'is_correct' => true],
                                ['text' => 'Menyelesaikan dokumen secepat mungkin meskipun mengabaikan validitas berkas lampiran.', 'is_correct' => false],
                                ['text' => 'Memprioritaskan pemohon yang memiliki hubungan kekerabatan atau rekomendasi personal.', 'is_correct' => false],
                                ['text' => 'Menyimpan seluruh catatan pelayanan sebagai rahasia mutlak yang tidak boleh diaudit.', 'is_correct' => false],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // KELAS BATCH 2: Manajemen Proyek & Kinerja
            // ==========================================
            [
                'title' => 'Manajemen Proyek & Eksekusi Program Berorientasi Hasil',
                'slug' => 'dummy-manajemen-proyek-kinerja',
                'category_id' => $catKepemimpinan->id,
                'type' => 'batch',
                'price' => 0,
                'status' => CourseStatus::Published,
                'start_date' => $now->copy()->addDays(14)->setTime(9, 0),
                'end_date' => $now->copy()->addDays(35)->setTime(17, 0),
                'registration_open_at' => $now->copy()->subDays(1)->setTime(0, 0),
                'registration_close_at' => $now->copy()->addDays(10)->setTime(23, 59),
                'thumbnail' => 'courses/dummy/batch_manajemen_proyek.svg',
                'description' => 'Program terstruktur untuk pengelola kegiatan dalam menyusun perencanaan berbasis hasil (results-based management), menyusun indikator kinerja (KPI/IKU), mengidentifikasi risiko kegiatan, dan menyusun laporan pertanggungjawaban yang akuntabel.',
                'chapters' => [
                    [
                        'title' => 'Bab 1: Kerangka Perencanaan Program & Sasaran SMART',
                        'order' => 1,
                        'lessons' => [
                            [
                                'title' => 'Merumuskan Target Kinerja SMART',
                                'content_type' => 'article',
                                'body_text' => '<h2>Kriteria SMART dalam Manajemen Program</h2>
<p>Banyak kegiatan gagal memberikan dampak nyata karena indikator capaian yang dibuat terlalu abstrak. Setiap sasaran harus memenuhi kaidah:</p>
<ul>
    <li><strong>Specific:</strong> Jelas, tidak multitafsir, dan menunjuk output yang tegas.</li>
    <li><strong>Measurable:</strong> Memiliki metrik pengukuran (kuantitas, persentase, atau indeks).</li>
    <li><strong>Achievable:</strong> Realistis untuk dicapai dengan sumber daya dan alokasi anggaran yang ada.</li>
    <li><strong>Relevant:</strong> Selaras dengan sasaran strategis instansi atau dinas.</li>
    <li><strong>Time-bound:</strong> Memiliki batas waktu mulai dan selesainya eksekusi yang pasti.</li>
</ul>',
                            ],
                        ],
                        'quiz' => [
                            'title' => 'Kuis Bab 1: Perencanaan SMART',
                            'description' => 'Uji pemahaman tentang perumusan indikator kinerja program yang terukur.',
                            'time_limit_minutes' => 15,
                            'passing_score' => 70,
                            'questions' => [
                                [
                                    'question_text' => 'Manakah contoh rumusan target kegiatan yang memenuhi kriteria SMART secara tepat?',
                                    'explanation' => 'Target harus memiliki angka kuantitatif, lokus yang jelas, dan batas waktu penyelesaian yang tegas.',
                                    'score' => 15,
                                    'options' => [
                                        ['text' => 'Meningkatkan 95% kepatuhan pelaporan tepat waktu pada 30 unit kerja sebelum akhir triwulan IV.', 'is_correct' => true],
                                        ['text' => 'Melaksanakan sosialisasi kepegawaian sebaik-baiknya kepada seluruh staf.', 'is_correct' => false],
                                        ['text' => 'Membuat aplikasi modern sesegera mungkin jika anggaran tersedia.', 'is_correct' => false],
                                        ['text' => 'Membantu pimpinan menyelesaikan seluruh tugas kantor secara berkala.', 'is_correct' => false],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Bab 2: Manajemen Risiko & Matriks Mitigasi',
                        'order' => 2,
                        'lessons' => [
                            [
                                'title' => 'Penyusunan Risk Register dan Profil Risiko',
                                'content_type' => 'article',
                                'body_text' => '<h2>Mengantisipasi Bottleneck Operasional</h2>
<p>Manajemen risiko adalah proses sistematis untuk mengidentifikasi kemungkinan kendala (probabilitas) dan dampak negatif yang ditimbulkannya terhadap jalannya program.</p>',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Bab 3: Evaluasi Dampak & Pelaporan Akuntabilitas',
                        'order' => 3,
                        'lessons' => [
                            [
                                'title' => 'Perbedaan Output vs Outcome dalam Evaluasi',
                                'content_type' => 'article',
                                'body_text' => '<h2>Menilai Keberhasilan Sejati Program</h2>
<p><strong>Output</strong> adalah keluaran langsung (misal: 100 orang dilatih), sedangkan <strong>Outcome</strong> adalah perubahan perilaku atau perbaikan kinerja nyata setelah pelatihan (misal: tingkat kesalahan data berkurang 40%).</p>',
                            ],
                        ],
                    ],
                ],
                'final_quiz' => [
                    'title' => 'Evaluasi Akhir: Manajemen Proyek Berbasis Hasil',
                    'description' => 'Ujian akhir batch untuk memastikan peserta menguasai siklus manajemen proyek dari perencanaan hingga evaluasi dampak.',
                    'time_limit_minutes' => 25,
                    'passing_score' => 75,
                    'questions' => [
                        [
                            'question_text' => 'Dalam evaluasi program, pernyataan "Meningkatnya kepuasan layanan publik dari skor 3.1 menjadi 4.5" dikategorikan sebagai...',
                            'explanation' => 'Peningkatan kepuasan publik adalah hasil dampak jangka menengah/panjang (outcome), bukan sekadar output fisik.',
                            'score' => 20,
                            'options' => [
                                ['text' => 'Outcome (Capaian Hasil/Dampak Nyata)', 'is_correct' => true],
                                ['text' => 'Output (Keluaran Langsung Kegiatan)', 'is_correct' => false],
                                ['text' => 'Input (Sumber Daya / Anggaran)', 'is_correct' => false],
                                ['text' => 'Milestone Administrasi', 'is_correct' => false],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // KELAS BATCH 3: Kepemimpinan Adaptif
            // ==========================================
            [
                'title' => 'Kepemimpinan Adaptif & Manajemen Perubahan Organisasi',
                'slug' => 'dummy-kepemimpinan-adaptif',
                'category_id' => $catKepemimpinan->id,
                'type' => 'batch',
                'price' => 0,
                'status' => CourseStatus::Published,
                'start_date' => $now->copy()->addDays(20)->setTime(8, 30),
                'end_date' => $now->copy()->addDays(45)->setTime(16, 30),
                'registration_open_at' => $now->copy()->subDays(2)->setTime(0, 0),
                'registration_close_at' => $now->copy()->addDays(15)->setTime(23, 59),
                'thumbnail' => 'courses/dummy/batch_kepemimpinan_adaptif.svg',
                'description' => 'Mempersiapkan pejabat struktural dan koordinator tim dalam memimpin transformasi budaya kerja, meredam resistensi staf saat inovasi baru diterapkan, serta mengoptimalkan kolaborasi lintas fungsi berbasis data.',
                'chapters' => [
                    [
                        'title' => 'Bab 1: Menghadapi Resistensi Perubahan (Change Management)',
                        'order' => 1,
                        'lessons' => [
                            [
                                'title' => 'Memahami Kurva Perubahan (Change Curve)',
                                'content_type' => 'article',
                                'body_text' => '<h2>Mengapa Staf Menolak Inovasi Baru?</h2>
<p>Resistensi terhadap sistem baru (seperti digitalisasi arsip atau e-office) bukanlah tanda ketidakmampuan, melainkan rasa cemas akan hilangnya kenyamanan kerja lama. Pemimpin harus mendengarkan kekhawatiran tersebut dan memberikan pendampingan yang intensif.</p>',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Bab 2: Teknik Coaching & Mentoring Tim',
                        'order' => 2,
                        'lessons' => [
                            [
                                'title' => 'Model GROW dalam Sesi One-on-One',
                                'content_type' => 'article',
                                'body_text' => '<h2>Membimbing Anggota Tim Menemukan Solusinya Sendiri</h2>
<p>Pendekatan <strong>GROW</strong> (Goal, Reality, Options, Will) memfasilitasi staf untuk mengidentifikasi hambatan kinerjanya dan merumuskan komitmen tindakan mandiri.</p>',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Bab 3: Pengambilan Keputusan Berbasis Data (Evidence-Based)',
                        'order' => 3,
                        'lessons' => [
                            [
                                'title' => 'Menghindari Bias Opini dalam Rapat Penentuan Kebijakan',
                                'content_type' => 'article',
                                'body_text' => '<h2>Data Valid Mengalahkan Asumsi Pribadi</h2>
<p>Setiap usulan intervensi program wajib didukung fakta tren statistik, riwayat audit kegiatan, dan masukan konstituen lapangan.</p>',
                            ],
                        ],
                    ],
                ],
                'final_quiz' => [
                    'title' => 'Evaluasi Akhir: Strategi Kepemimpinan Adaptif',
                    'description' => 'Ujian akhir simulasi kasus dinamika kepemimpinan tim dan manajemen konflik.',
                    'time_limit_minutes' => 30,
                    'passing_score' => 70,
                    'questions' => [
                        [
                            'question_text' => 'Ketika sebuah aplikasi baru diluncurkan dan beberapa staf senior enggan menggunakannya karena terbiasa cara manual, tindakan pemimpin adaptif adalah...',
                            'explanation' => 'Mengidentifikasi akar kesulitan dan memberikan pendampingan personal (mentoring) menciptakan penerimaan yang berkelanjutan dibanding ancaman hukuman.',
                            'score' => 20,
                            'options' => [
                                ['text' => 'Mengadakan sesi pendampingan bertahap dan menunjuk champion digital untuk membantu mereka beradaptasi.', 'is_correct' => true],
                                ['text' => 'Langsung memberikan sanksi surat peringatan tanpa menanyakan kendala teknis mereka.', 'is_correct' => false],
                                ['text' => 'Membatalkan seluruh rencana digitalisasi agar kondisi kantor kembali tenang.', 'is_correct' => false],
                                ['text' => 'Membiarkan staf tersebut tetap menggunakan cara manual selamanya.', 'is_correct' => false],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // KELAS PERMANEN 1: Core Values BerAKHLAK
            // ==========================================
            [
                'title' => 'Budaya Kerja & Core Values BerAKHLAK',
                'slug' => 'dummy-core-values-berakhlak',
                'category_id' => $catFungsional->id,
                'type' => 'permanent',
                'price' => 0,
                'status' => CourseStatus::Published,
                'start_date' => null,
                'end_date' => null,
                'registration_open_at' => null,
                'registration_close_at' => null,
                'thumbnail' => 'courses/dummy/permanent_berakhlak.svg',
                'description' => 'Kursus mandiri (self-paced) untuk mendalami implementasi nilai-nilai dasar ASN BerAKHLAK (Berorientasi Pelayanan, Akuntabel, Kompeten, Harmonis, Loyal, Adaptif, Kolaboratif) dalam rutinitas kerja harian dan kode etik kedinasan.',
                'chapters' => [
                    [
                        'title' => 'Bab 1: Berorientasi Pelayanan & Akuntabel',
                        'order' => 1,
                        'lessons' => [
                            [
                                'title' => 'Panduan Perilaku Nilai Pelayanan & Integritas Akuntabilitas',
                                'content_type' => 'article',
                                'body_text' => '<h2>Nilai Berorientasi Pelayanan</h2>
<p>Komitmen memberikan pelayanan prima demi kepuasan masyarakat:</p>
<ul>
    <li>Memahami dan memenuhi kebutuhan masyarakat.</li>
    <li>Ramah, cekatan, solutif, dan dapat diandalkan.</li>
    <li>Melakukan perbaikan tiada henti (continuous improvement).</li>
</ul>
<h2>Nilai Akuntabel</h2>
<p>Bertanggung jawab atas kepercayaan yang diberikan dengan tidak menyalahgunakan wewenang jabatan dan menjaga barang milik negara secara efektif dan efisien.</p>',
                            ],
                        ],
                        'quiz' => [
                            'title' => 'Kuis Bab 1: Pelayanan & Akuntabilitas',
                            'description' => 'Uji pemahaman penerapan nilai Berorientasi Pelayanan dan Akuntabel.',
                            'time_limit_minutes' => 10,
                            'passing_score' => 70,
                            'questions' => [
                                [
                                    'question_text' => 'Perilaku menggunakan kendaraan dinas kantor untuk kepentingan liburan keluarga pribadi bertentangan dengan pilar...',
                                    'explanation' => 'Nilai Akuntabel melarang penggunaan fasilitas dan barang milik negara untuk kepentingan di luar kedinasan.',
                                    'score' => 15,
                                    'options' => [
                                        ['text' => 'Akuntabel (Penggunaan kekayaan negara secara bertanggung jawab)', 'is_correct' => true],
                                        ['text' => 'Adaptif', 'is_correct' => false],
                                        ['text' => 'Kompeten', 'is_correct' => false],
                                        ['text' => 'Harmonis', 'is_correct' => false],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Bab 2: Kompeten, Harmonis, & Loyal',
                        'order' => 2,
                        'lessons' => [
                            [
                                'title' => 'Membangun Suasana Kerja Kondusif dan Saling Menghargai',
                                'content_type' => 'article',
                                'body_text' => '<h2>Sinergi Keberagaman</h2>
<p>Nilai <strong>Harmonis</strong> diwujudkan dengan menghargai setiap orang apa pun latar belakangnya, suka menolong orang lain, dan membangun lingkungan kerja yang kondusif.</p>',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Bab 3: Adaptif & Kolaboratif di Era Transformasi',
                        'order' => 3,
                        'lessons' => [
                            [
                                'title' => 'Kolaborasi Lintas Lembaga dan Keterbukaan terhadap Inovasi',
                                'content_type' => 'article',
                                'body_text' => '<h2>Bekerja Tanpa Sekat Ego Sektoral</h2>
<p>Tantangan masa depan menuntut kerja sama antar-organisasi pemerintah guna mewujudkan satu kesatuan pelayanan terintegrasi bagi masyarakat.</p>',
                            ],
                        ],
                    ],
                ],
                'final_quiz' => [
                    'title' => 'Evaluasi Akhir: Nilai Dasar ASN BerAKHLAK',
                    'description' => 'Uji sertifikasi mandiri penguasaan 7 nilai dasar budaya kerja BerAKHLAK.',
                    'time_limit_minutes' => 20,
                    'passing_score' => 70,
                    'questions' => [
                        [
                            'question_text' => 'Sikap aktif mempelajari regulasi baru dan membagikan ilmunya kepada rekan sejawat merupakan perwujudan dari pilar...',
                            'explanation' => 'Meningkatkan kompetensi diri dan membantu orang lain belajar adalah panduan perilaku pilar Kompeten.',
                            'score' => 20,
                            'options' => [
                                ['text' => 'Kompeten', 'is_correct' => true],
                                ['text' => 'Loyal', 'is_correct' => false],
                                ['text' => 'Kolaboratif', 'is_correct' => false],
                                ['text' => 'Harmonis', 'is_correct' => false],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // KELAS PERMANEN 2: Keamanan Siber & Privasi Data
            // ==========================================
            [
                'title' => 'Keamanan Informasi & Kesadaran Siber (Cybersecurity)',
                'slug' => 'dummy-keamanan-siber-kesadaran-data',
                'category_id' => $catTeknis->id,
                'type' => 'permanent',
                'price' => 0,
                'status' => CourseStatus::Published,
                'start_date' => null,
                'end_date' => null,
                'registration_open_at' => null,
                'registration_close_at' => null,
                'thumbnail' => 'courses/dummy/permanent_keamanan_siber.svg',
                'description' => 'Modul praktis perlindungan data institusi pemerintah: mewaspadai social engineering (phishing), manajemen kredensial akses yang aman, otentikasi multi-faktor (2FA), serta kepatuhan terhadap UU Perlindungan Data Pribadi (UU PDP).',
                'chapters' => [
                    [
                        'title' => 'Bab 1: Mengenal Rekayasa Sosial & Serangan Phishing',
                        'order' => 1,
                        'lessons' => [
                            [
                                'title' => 'Ciri-Ciri Email & Pesan WhatsApp Phishing',
                                'content_type' => 'article',
                                'body_text' => '<h2>Waspada Pesan Tipuan Berkedok Resmi</h2>
<p>Serangan siber paling umum tidak meretas server secara langsung, melainkan menipu pengguna melalui rekayasa sosial (social engineering).</p>
<h3>Indikator Utama Pesan Palsu:</h3>
<ul>
    <li>Menciptakan urgensi mendesak: <em>"Akun Anda akan dibekukan dalam 1 jam!"</em></li>
    <li>Domain pengirim tidak resmi (misal: <em>admin@bkpsdm-update-link.com</em> bukan <em>@domain.go.id</em>).</li>
    <li>Meminta mengunduh file lampiran APK atau login ke tautan formulir asing.</li>
</ul>',
                            ],
                        ],
                        'quiz' => [
                            'title' => 'Kuis Bab 1: Deteksi Serangan Phishing',
                            'description' => 'Evaluasi kepekaan dalam membedakan komunikasi resmi instansi dan upaya phishing.',
                            'time_limit_minutes' => 15,
                            'passing_score' => 70,
                            'questions' => [
                                [
                                    'question_text' => 'Anda menerima pesan WhatsApp dari nomor tidak dikenal mengaku dari tim IT dinas meminta kode OTP SMS Anda untuk verifikasi server. Sikap Anda?',
                                    'explanation' => 'Kode OTP bersifat rahasia dan tidak boleh dibagikan kepada siapa pun termasuk pihak yang mengaku tim teknis internal.',
                                    'score' => 15,
                                    'options' => [
                                        ['text' => 'Menolak tegas dan segera melaporkannya ke penanggung jawab IT resmi karena OTP rahasia mutlak.', 'is_correct' => true],
                                        ['text' => 'Memberikan kode OTP tersebut karena yang meminta mengaku tim IT dinas.', 'is_correct' => false],
                                        ['text' => 'Meneruskan pesan tersebut ke grup dinas tanpa konfirmasi.', 'is_correct' => false],
                                        ['text' => 'Mengklik tautan apa pun yang dilampirkan nomor tersebut.', 'is_correct' => false],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Bab 2: Higienitas Kata Sandi & Otentikasi Ganda (2FA)',
                        'order' => 2,
                        'lessons' => [
                            [
                                'title' => 'Standar Pembuatan Password Kuat & Penggunaan Passphrase',
                                'content_type' => 'article',
                                'body_text' => '<h2>Mengapa "Password123" Berbahaya?</h2>
<p>Kombinasi kata sandi minimal 12 karakter yang memadukan huruf besar, huruf kecil, angka, dan simbol jauh lebih tahan terhadap serangan brute-force.</p>',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Bab 3: Kepatuhan UU Perlindungan Data Pribadi (UU PDP)',
                        'order' => 3,
                        'lessons' => [
                            [
                                'title' => 'Penanganan Data Rahasia Warga (NIK, Rekam Medis, Gaji)',
                                'content_type' => 'article',
                                'body_text' => '<h2>Tanggung Jawab Pemrosesan Data Publik</h2>
<p>Aparatur dilarang menyebarkan tangkapan layar (screenshot) dokumen berisikan data pribadi pemohon ke media sosial publik tanpa persetujuan sah pemegang data.</p>',
                            ],
                        ],
                    ],
                ],
                'final_quiz' => [
                    'title' => 'Evaluasi Akhir: Keamanan Informasi Aparatur',
                    'description' => 'Ujian akhir komprehensif menguji pemahaman tata kelola data aman dan proteksi digital.',
                    'time_limit_minutes' => 20,
                    'passing_score' => 75,
                    'questions' => [
                        [
                            'question_text' => 'Tindakan meninggalkan komputer kerja yang sedang login ke sistem kepegawaian dalam kondisi layar terbuka tanpa lock-screen saat jam makan siang berisiko terhadap...',
                            'explanation' => 'Mengunci layar (Win + L / lock) adalah prosedur dasar keamanan fisik agar akun tidak disalahgunakan orang tak berwenang.',
                            'score' => 20,
                            'options' => [
                                ['text' => 'Pelanggaran keamanan fisik (unauthorized physical access) terhadap kerahasiaan data.', 'is_correct' => true],
                                ['text' => 'Kerusakan fisik pada monitor komputer.', 'is_correct' => false],
                                ['text' => 'Pemborosan daya listrik instansi saja tanpa risiko data.', 'is_correct' => false],
                                ['text' => 'Peningkatan performa pemrosesan server dinas.', 'is_correct' => false],
                            ],
                        ],
                    ],
                ],
            ],

            // ==========================================
            // KELAS PERMANEN 3: Manajemen Waktu & Fokus
            // ==========================================
            [
                'title' => 'Manajemen Waktu & Fokus Kerja Produktif',
                'slug' => 'dummy-manajemen-waktu-fokus-kerja',
                'category_id' => $catTeknis->id,
                'type' => 'permanent',
                'price' => 0,
                'status' => CourseStatus::Published,
                'start_date' => null,
                'end_date' => null,
                'registration_open_at' => null,
                'registration_close_at' => null,
                'thumbnail' => 'courses/dummy/permanent_manajemen_waktu.svg',
                'description' => 'Membangun ritme kerja yang efisien tanpa stres berlebih. Membedah Matriks Eisenhower untuk memilih prioritas penting vs mendesak, penerapan teknik Pomodoro dalam pekerjaan administrasi, serta strategi menghindari burnout.',
                'chapters' => [
                    [
                        'title' => 'Bab 1: Matriks Prioritas Eisenhower',
                        'order' => 1,
                        'lessons' => [
                            [
                                'title' => 'Membedakan Tugas Penting vs Tugas Mendesak',
                                'content_type' => 'article',
                                'body_text' => '<h2>4 Kuadran Manajemen Waktu</h2>
<ul>
    <li><strong>Kuadran I (Penting & Mendesak):</strong> Krisis, deadline hari ini, kendala mendadak. <em>Lakukan segera!</em></li>
    <li><strong>Kuadran II (Penting tapi Tidak Mendesak):</strong> Perencanaan strategis, pelatihan diri, mitigasi masalah. <em>Jadwalkan!</em> (Kunci sukses jangka panjang).</li>
    <li><strong>Kuadran III (Tidak Penting tapi Mendesak):</strong> Interupsi yang bisa didelegasikan. <em>Delegasikan!</em></li>
    <li><strong>Kuadran IV (Tidak Penting & Tidak Mendesak):</strong> Menjelajah medsos berlebihan. <em>Eliminasi!</em></li>
</ul>',
                            ],
                        ],
                        'quiz' => [
                            'title' => 'Kuis Bab 1: Matriks Eisenhower',
                            'description' => 'Uji pemetaan prioritas tugas pekerjaan sehari-hari.',
                            'time_limit_minutes' => 10,
                            'passing_score' => 70,
                            'questions' => [
                                [
                                    'question_text' => 'Aktivitas mengikuti pelatihan kompetensi peningkatan kapasitas staf sebelum menghadapi perubahan sistem baru masuk ke kuadran...',
                                    'explanation' => 'Pengembangan kapasitas merupakan investasi penting untuk mencegah krisis di masa depan namun tidak bersifat deadline darurat hari ini.',
                                    'score' => 15,
                                    'options' => [
                                        ['text' => 'Kuadran II: Penting tapi Tidak Mendesak (Perlu Dijadwalkan)', 'is_correct' => true],
                                        ['text' => 'Kuadran I: Mendesak dan Kritis', 'is_correct' => false],
                                        ['text' => 'Kuadran III: Interupsi yang Harus Didelegasikan', 'is_correct' => false],
                                        ['text' => 'Kuadran IV: Pemborosan Waktu', 'is_correct' => false],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Bab 2: Mengelola Distraksi Digital & Deep Work',
                        'order' => 2,
                        'lessons' => [
                            [
                                'title' => 'Teknik Pomodoro untuk Pekerjaan Analisis',
                                'content_type' => 'article',
                                'body_text' => '<h2>Fokus 25 Menit Tanpa Interupsi</h2>
<p>Bekerja dalam blok waktu 25 menit fokus penuh, dilanjutkan istirahat sejenak 5 menit, terbukti meningkatkan ketelitian dalam memeriksa dokumen regulasi dan data angka.</p>',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Bab 3: Pencegahan Burnout & Menjaga Keseimbangan Kerja',
                        'order' => 3,
                        'lessons' => [
                            [
                                'title' => 'Mengenali Gejala Kelelahan Mental (Mental Fatigue)',
                                'content_type' => 'article',
                                'body_text' => '<h2>Membangun Ketahanan Mental yang Berkelanjutan</h2>
<p>Produktivitas sejati bukanlah bekerja tanpa istirahat hingga larut malam, melainkan konsistensi energi kerja dengan manajemen pemulihan fisik dan pikiran yang seimbang.</p>',
                            ],
                        ],
                    ],
                ],
                'final_quiz' => [
                    'title' => 'Evaluasi Akhir: Efektivitas Manajemen Waktu',
                    'description' => 'Evaluasi mandiri mengenai strategi pengaturan ritme kerja dan pemulihan fokus.',
                    'time_limit_minutes' => 15,
                    'passing_score' => 70,
                    'questions' => [
                        [
                            'question_text' => 'Manfaat utama dari memprioritaskan waktu untuk kegiatan Kuadran II (Penting tapi Tidak Mendesak) adalah...',
                            'explanation' => 'Investasi di kuadran II mencegah timbulnya kepanikan dan krisis mendesak di masa depan.',
                            'score' => 20,
                            'options' => [
                                ['text' => 'Mengurangi timbulnya krisis mendadak dan meningkatkan kualitas perencanaan masa depan.', 'is_correct' => true],
                                ['text' => 'Membuat staf tidak perlu bekerja sama sekali di minggu depan.', 'is_correct' => false],
                                ['text' => 'Menghilangkan kebutuhan akan dokumentasi kegiatan resmi.', 'is_correct' => false],
                                ['text' => 'Memastikan seluruh panggilan telepon diabaikan.', 'is_correct' => false],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // 3. Eksekusi Penyimpanan ke Database
        foreach ($coursesData as $cData) {
            // Update atau buat kursus
            $course = Course::updateOrCreate(
                ['slug' => $cData['slug']],
                [
                    'title' => $cData['title'],
                    'category_id' => $cData['category_id'],
                    'type' => $cData['type'],
                    'price' => $cData['price'],
                    'status' => $cData['status'],
                    'start_date' => $cData['start_date'],
                    'end_date' => $cData['end_date'],
                    'registration_open_at' => $cData['registration_open_at'],
                    'registration_close_at' => $cData['registration_close_at'],
                    'thumbnail' => $cData['thumbnail'],
                    'description' => $cData['description'],
                    'created_by' => $adminId,
                ]
            );

            // Simpan Bab & Materi
            if (! empty($cData['chapters'])) {
                foreach ($cData['chapters'] as $chData) {
                    $chapter = Chapter::updateOrCreate(
                        [
                            'course_id' => $course->id,
                            'title' => $chData['title'],
                        ],
                        [
                            'order' => $chData['order'],
                        ]
                    );

                    // Lessons
                    if (! empty($chData['lessons'])) {
                        foreach ($chData['lessons'] as $lIndex => $lData) {
                            Lesson::updateOrCreate(
                                [
                                    'chapter_id' => $chapter->id,
                                    'title' => $lData['title'],
                                ],
                                [
                                    'order' => $lIndex + 1,
                                    'content_type' => $lData['content_type'],
                                    'video_url' => $lData['video_url'] ?? null,
                                    'body_text' => $lData['body_text'] ?? null,
                                    'version' => 'Versi 1.0',
                                ]
                            );
                        }
                    }

                    // Chapter Quiz (Kuis Bab jika ada)
                    if (! empty($chData['quiz'])) {
                        $qData = $chData['quiz'];
                        $quiz = Quiz::updateOrCreate(
                            [
                                'course_id' => $course->id,
                                'chapter_id' => $chapter->id,
                                'type' => 'chapter',
                            ],
                            [
                                'slug' => Str::slug($course->slug.'-'.$qData['title']),
                                'title' => $qData['title'],
                                'description' => $qData['description'] ?? null,
                                'time_limit_minutes' => $qData['time_limit_minutes'] ?? 15,
                                'passing_score' => $qData['passing_score'] ?? 70,
                                'created_by' => $adminId,
                            ]
                        );

                        $this->seedQuizQuestions($quiz, $qData['questions'] ?? []);
                        $quiz->recalculateTotalScore();
                    }
                }
            }

            // Final Course Quiz (Evaluasi Akhir)
            if (! empty($cData['final_quiz'])) {
                $fqData = $cData['final_quiz'];
                $finalQuiz = Quiz::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'chapter_id' => null,
                        'type' => 'final',
                    ],
                    [
                        'slug' => Str::slug($course->slug.'-'.$fqData['title']),
                        'title' => $fqData['title'],
                        'description' => $fqData['description'] ?? null,
                        'time_limit_minutes' => $fqData['time_limit_minutes'] ?? 30,
                        'passing_score' => $fqData['passing_score'] ?? 75,
                        'created_by' => $adminId,
                    ]
                );

                $this->seedQuizQuestions($finalQuiz, $fqData['questions'] ?? []);
                $finalQuiz->recalculateTotalScore();
            }
        }
    }

    /**
     * Helper untuk memasukkan butir soal & opsi acak ke kuis
     */
    protected function seedQuizQuestions(Quiz $quiz, array $questions): void
    {
        foreach ($questions as $qIndex => $questionItem) {
            $question = QuizQuestion::updateOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionItem['question_text'],
                ],
                [
                    'score' => $questionItem['score'] ?? 10,
                    'explanation' => $questionItem['explanation'] ?? null,
                    'order' => $qIndex + 1,
                ]
            );

            // Acak urutan opsi jawaban agar dinamis dan tidak kaku
            $options = $questionItem['options'] ?? [];
            shuffle($options);

            // Bersihkan opsi lama jika ada re-seed
            $question->options()->delete();

            foreach ($options as $optIndex => $opt) {
                QuizOption::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct' => $opt['is_correct'],
                    'order' => $optIndex + 1,
                ]);
            }
        }
    }

    /**
     * Memastikan seluruh berkas thumbnail SVG dummy tersedia di storage publik.
     */
    protected function ensureThumbnailsExist(): void
    {
        $dir = 'courses/dummy';
        if (! Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        $thumbnails = [
            'batch_pelayanan_publik.svg' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 450" width="100%" height="100%">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#0f766e" />
      <stop offset="100%" stop-color="#115e59" />
    </linearGradient>
    <linearGradient id="accentGrad" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#2dd4bf" />
      <stop offset="100%" stop-color="#34d399" />
    </linearGradient>
  </defs>
  <rect width="800" height="450" fill="url(#bgGrad)" />
  <circle cx="720" cy="80" r="180" fill="#2dd4bf" opacity="0.08" />
  <circle cx="80" cy="380" r="140" fill="#14b8a6" opacity="0.12" />
  <path d="M-50,200 Q200,100 400,280 T850,220" fill="none" stroke="#2dd4bf" stroke-width="2" opacity="0.15" />
  <g transform="translate(60, 60)">
    <rect width="130" height="34" rx="17" fill="#134e4a" stroke="#2dd4bf" stroke-width="1.5" />
    <circle cx="20" cy="17" r="5" fill="#2dd4bf" />
    <text x="34" y="22" font-family="system-ui, -apple-system, sans-serif" font-size="13" font-weight="700" fill="#ccfbf1" letter-spacing="1">KELAS BATCH</text>
  </g>
  <g transform="translate(560, 160)">
    <circle cx="90" cy="90" r="85" fill="#134e4a" stroke="#2dd4bf" stroke-width="3" opacity="0.7"/>
    <path d="M50,110 C50,80 80,60 90,60 C100,60 130,80 130,110 C130,135 90,150 90,150 C90,150 50,135 50,110 Z" fill="url(#accentGrad)" />
    <circle cx="78" cy="92" r="4" fill="#0f766e" />
    <circle cx="102" cy="92" r="4" fill="#0f766e" />
    <path d="M80,105 Q90,118 100,105" fill="none" stroke="#0f766e" stroke-width="3" stroke-linecap="round" />
  </g>
  <g transform="translate(60, 150)">
    <text font-family="system-ui, -apple-system, sans-serif" font-size="16" font-weight="600" fill="#5eead4" letter-spacing="1.5">PELATIHAN KOMPETENSI PELAYANAN</text>
    <text y="50" font-family="system-ui, -apple-system, sans-serif" font-size="34" font-weight="800" fill="#ffffff">Pelayanan Publik</text>
    <text y="92" font-family="system-ui, -apple-system, sans-serif" font-size="34" font-weight="800" fill="url(#accentGrad)">Prima &amp; Terpadu</text>
    <text y="145" font-family="system-ui, -apple-system, sans-serif" font-size="16" fill="#99f6e4" opacity="0.9">
      <tspan x="0" dy="0">Standar keramahan, active listening, dan</tspan>
      <tspan x="0" dy="24">teknik penyelesaian keluhan masyarakat secara solutif.</tspan>
    </text>
    <g transform="translate(0, 210)">
      <rect width="110" height="28" rx="6" fill="#134e4a" />
      <text x="12" y="19" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="600" fill="#ccfbf1">3 Bab Modul</text>
      <rect x="120" width="130" height="28" rx="6" fill="#134e4a" />
      <text x="132" y="19" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="600" fill="#ccfbf1">Evaluasi Studi Kasus</text>
    </g>
  </g>
</svg>
SVG,
            'batch_manajemen_proyek.svg' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 450" width="100%" height="100%">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#1e1b4b" />
      <stop offset="100%" stop-color="#312e81" />
    </linearGradient>
    <linearGradient id="accentGrad" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#818cf8" />
      <stop offset="100%" stop-color="#60a5fa" />
    </linearGradient>
  </defs>
  <rect width="800" height="450" fill="url(#bgGrad)" />
  <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#4338ca" stroke-width="0.8" opacity="0.3"/>
  </pattern>
  <rect width="800" height="450" fill="url(#grid)" />
  <circle cx="700" cy="100" r="160" fill="#6366f1" opacity="0.1" />
  <g transform="translate(60, 60)">
    <rect width="130" height="34" rx="17" fill="#312e81" stroke="#818cf8" stroke-width="1.5" />
    <circle cx="20" cy="17" r="5" fill="#818cf8" />
    <text x="34" y="22" font-family="system-ui, -apple-system, sans-serif" font-size="13" font-weight="700" fill="#e0e7ff" letter-spacing="1">KELAS BATCH</text>
  </g>
  <g transform="translate(550, 150)">
    <rect width="180" height="150" rx="16" fill="#3730a3" stroke="#818cf8" stroke-width="2" opacity="0.8"/>
    <rect x="25" y="80" width="20" height="45" rx="4" fill="#60a5fa" />
    <rect x="55" y="55" width="20" height="70" rx="4" fill="#818cf8" />
    <rect x="85" y="40" width="20" height="85" rx="4" fill="#a5b4fc" />
    <rect x="115" y="25" width="20" height="100" rx="4" fill="#c7d2fe" />
    <circle cx="150" cy="35" r="14" fill="#4f46e5" stroke="#a5b4fc" stroke-width="2"/>
    <circle cx="150" cy="35" r="6" fill="#818cf8" />
  </g>
  <g transform="translate(60, 150)">
    <text font-family="system-ui, -apple-system, sans-serif" font-size="16" font-weight="600" fill="#a5b4fc" letter-spacing="1.5">MANAJEMEN KINERJA &amp; AKUNTABILITAS</text>
    <text y="50" font-family="system-ui, -apple-system, sans-serif" font-size="34" font-weight="800" fill="#ffffff">Manajemen Proyek &amp;</text>
    <text y="92" font-family="system-ui, -apple-system, sans-serif" font-size="34" font-weight="800" fill="url(#accentGrad)">Eksekusi Berorientasi Hasil</text>
    <text y="145" font-family="system-ui, -apple-system, sans-serif" font-size="16" fill="#c7d2fe" opacity="0.9">
      <tspan x="0" dy="0">Kerangka kerja SMART, mitigasi risiko kegiatan,</tspan>
      <tspan x="0" dy="24">dan pelaporan akuntabilitas kinerja program.</tspan>
    </text>
    <g transform="translate(0, 210)">
      <rect width="110" height="28" rx="6" fill="#312e81" />
      <text x="12" y="19" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="600" fill="#e0e7ff">3 Bab Modul</text>
      <rect x="120" width="130" height="28" rx="6" fill="#312e81" />
      <text x="132" y="19" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="600" fill="#e0e7ff">Evaluasi LogFrame</text>
    </g>
  </g>
</svg>
SVG,
            'batch_kepemimpinan_adaptif.svg' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 450" width="100%" height="100%">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#7c2d12" />
      <stop offset="100%" stop-color="#9a3412" />
    </linearGradient>
    <linearGradient id="accentGrad" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#fb923c" />
      <stop offset="100%" stop-color="#fde047" />
    </linearGradient>
  </defs>
  <rect width="800" height="450" fill="url(#bgGrad)" />
  <circle cx="730" cy="110" r="170" fill="#ea580c" opacity="0.15" />
  <circle cx="100" cy="400" r="120" fill="#c2410c" opacity="0.12" />
  <g transform="translate(60, 60)">
    <rect width="130" height="34" rx="17" fill="#431407" stroke="#fb923c" stroke-width="1.5" />
    <circle cx="20" cy="17" r="5" fill="#fb923c" />
    <text x="34" y="22" font-family="system-ui, -apple-system, sans-serif" font-size="13" font-weight="700" fill="#ffedd5" letter-spacing="1">KELAS BATCH</text>
  </g>
  <g transform="translate(560, 160)">
    <circle cx="85" cy="85" r="80" fill="#431407" stroke="#fb923c" stroke-width="2.5" opacity="0.8"/>
    <polygon points="85,30 97,85 85,80" fill="#fbbf24" />
    <polygon points="85,140 73,85 85,90" fill="#9a3412" />
    <circle cx="85" cy="85" r="10" fill="#fed7aa" />
  </g>
  <g transform="translate(60, 150)">
    <text font-family="system-ui, -apple-system, sans-serif" font-size="16" font-weight="600" fill="#fdba74" letter-spacing="1.5">KEPEMIMPINAN &amp; TRANSFORMASI</text>
    <text y="50" font-family="system-ui, -apple-system, sans-serif" font-size="34" font-weight="800" fill="#ffffff">Kepemimpinan Adaptif &amp;</text>
    <text y="92" font-family="system-ui, -apple-system, sans-serif" font-size="34" font-weight="800" fill="url(#accentGrad)">Manajemen Perubahan</text>
    <text y="145" font-family="system-ui, -apple-system, sans-serif" font-size="16" fill="#fed7aa" opacity="0.9">
      <tspan x="0" dy="0">Navigasi resistensi tim, coaching berkala,</tspan>
      <tspan x="0" dy="24">dan pengambilan keputusan berbasis data akurat.</tspan>
    </text>
    <g transform="translate(0, 210)">
      <rect width="110" height="28" rx="6" fill="#431407" />
      <text x="12" y="19" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="600" fill="#ffedd5">3 Bab Modul</text>
      <rect x="120" width="130" height="28" rx="6" fill="#431407" />
      <text x="132" y="19" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="600" fill="#ffedd5">Studi Kasus Tim</text>
    </g>
  </g>
</svg>
SVG,
            'permanent_berakhlak.svg' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 450" width="100%" height="100%">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#881337" />
      <stop offset="100%" stop-color="#9f1239" />
    </linearGradient>
    <linearGradient id="accentGrad" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#f43f5e" />
      <stop offset="100%" stop-color="#fb7185" />
    </linearGradient>
  </defs>
  <rect width="800" height="450" fill="url(#bgGrad)" />
  <circle cx="700" cy="120" r="150" fill="#be123c" opacity="0.2" />
  <circle cx="120" cy="360" r="110" fill="#e11d48" opacity="0.1" />
  <g transform="translate(60, 60)">
    <rect width="165" height="34" rx="17" fill="#4c0519" stroke="#fb7185" stroke-width="1.5" />
    <circle cx="20" cy="17" r="5" fill="#fb7185" />
    <text x="34" y="22" font-family="system-ui, -apple-system, sans-serif" font-size="13" font-weight="700" fill="#ffe4e6" letter-spacing="1">KELAS PERMANEN</text>
  </g>
  <g transform="translate(560, 150)">
    <path d="M85,30 L150,55 L150,115 C150,155 85,185 85,185 C85,185 20,155 20,115 L20,55 Z" fill="#4c0519" stroke="#fb7185" stroke-width="2.5" opacity="0.85"/>
    <polygon points="85,65 92,85 112,85 96,98 102,118 85,106 68,118 74,98 58,85 78,85" fill="url(#accentGrad)" />
  </g>
  <g transform="translate(60, 150)">
    <text font-family="system-ui, -apple-system, sans-serif" font-size="16" font-weight="600" fill="#fda4af" letter-spacing="1.5">INTEGRITAS &amp; NILAI DASAR</text>
    <text y="50" font-family="system-ui, -apple-system, sans-serif" font-size="34" font-weight="800" fill="#ffffff">Budaya Kerja &amp;</text>
    <text y="92" font-family="system-ui, -apple-system, sans-serif" font-size="34" font-weight="800" fill="url(#accentGrad)">Core Values BerAKHLAK</text>
    <text y="145" font-family="system-ui, -apple-system, sans-serif" font-size="16" fill="#fecdd3" opacity="0.9">
      <tspan x="0" dy="0">Panduan perilaku 7 pilar: Berorientasi Pelayanan, Akuntabel,</tspan>
      <tspan x="0" dy="24">Kompeten, Harmonis, Loyal, Adaptif, dan Kolaboratif.</tspan>
    </text>
    <g transform="translate(0, 210)">
      <rect width="110" height="28" rx="6" fill="#4c0519" />
      <text x="12" y="19" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="600" fill="#ffe4e6">3 Bab Modul</text>
      <rect x="120" width="130" height="28" rx="6" fill="#4c0519" />
      <text x="132" y="19" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="600" fill="#ffe4e6">Kuis Pemahaman</text>
    </g>
  </g>
</svg>
SVG,
            'permanent_keamanan_siber.svg' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 450" width="100%" height="100%">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#090d16" />
      <stop offset="100%" stop-color="#0f172a" />
    </linearGradient>
    <linearGradient id="accentGrad" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#06b6d4" />
      <stop offset="100%" stop-color="#38bdf8" />
    </linearGradient>
  </defs>
  <rect width="800" height="450" fill="url(#bgGrad)" />
  <path d="M500,50 L560,110 L650,110 L700,60" fill="none" stroke="#0891b2" stroke-width="1.5" opacity="0.3"/>
  <circle cx="560" cy="110" r="4" fill="#22d3ee" opacity="0.5"/>
  <circle cx="650" cy="110" r="4" fill="#22d3ee" opacity="0.5"/>
  <path d="M520,380 L600,300 L720,300" fill="none" stroke="#0891b2" stroke-width="1.5" opacity="0.3"/>
  <g transform="translate(60, 60)">
    <rect width="165" height="34" rx="17" fill="#164e63" stroke="#22d3ee" stroke-width="1.5" />
    <circle cx="20" cy="17" r="5" fill="#22d3ee" />
    <text x="34" y="22" font-family="system-ui, -apple-system, sans-serif" font-size="13" font-weight="700" fill="#cffafe" letter-spacing="1">KELAS PERMANEN</text>
  </g>
  <g transform="translate(560, 150)">
    <rect width="160" height="150" rx="20" fill="#155e75" stroke="#38bdf8" stroke-width="2" opacity="0.8"/>
    <path d="M55,60 L55,40 C55,25 65,15 80,15 C95,15 105,25 105,40 L105,60" fill="none" stroke="#38bdf8" stroke-width="6" stroke-linecap="round"/>
    <circle cx="80" cy="95" r="10" fill="#090d16" />
    <polygon points="76,95 84,95 86,115 74,115" fill="#090d16" />
  </g>
  <g transform="translate(60, 150)">
    <text font-family="system-ui, -apple-system, sans-serif" font-size="16" font-weight="600" fill="#67e8f9" letter-spacing="1.5">LITERASI DIGITAL &amp; KEAMANAN INFORMASI</text>
    <text y="50" font-family="system-ui, -apple-system, sans-serif" font-size="34" font-weight="800" fill="#ffffff">Keamanan Siber &amp;</text>
    <text y="92" font-family="system-ui, -apple-system, sans-serif" font-size="34" font-weight="800" fill="url(#accentGrad)">Kesadaran Data Pribadi</text>
    <text y="145" font-family="system-ui, -apple-system, sans-serif" font-size="16" fill="#a5f3fc" opacity="0.9">
      <tspan x="0" dy="0">Antisipasi rekayasa sosial (phishing), proteksi password,</tspan>
      <tspan x="0" dy="24">otentikasi 2FA, dan kepatuhan UU Perlindungan Data Pribadi.</tspan>
    </text>
    <g transform="translate(0, 210)">
      <rect width="110" height="28" rx="6" fill="#164e63" />
      <text x="12" y="19" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="600" fill="#cffafe">3 Bab Modul</text>
      <rect x="120" width="130" height="28" rx="6" fill="#164e63" />
      <text x="132" y="19" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="600" fill="#cffafe">Evaluasi Phishing</text>
    </g>
  </g>
</svg>
SVG,
            'permanent_manajemen_waktu.svg' => <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 450" width="100%" height="100%">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#2e1065" />
      <stop offset="100%" stop-color="#4c1d95" />
    </linearGradient>
    <linearGradient id="accentGrad" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#a855f7" />
      <stop offset="100%" stop-color="#c084fc" />
    </linearGradient>
  </defs>
  <rect width="800" height="450" fill="url(#bgGrad)" />
  <circle cx="710" cy="110" r="160" fill="#7c3aed" opacity="0.15" />
  <circle cx="90" cy="390" r="120" fill="#6d28d9" opacity="0.1" />
  <g transform="translate(60, 60)">
    <rect width="165" height="34" rx="17" fill="#3b0764" stroke="#c084fc" stroke-width="1.5" />
    <circle cx="20" cy="17" r="5" fill="#c084fc" />
    <text x="34" y="22" font-family="system-ui, -apple-system, sans-serif" font-size="13" font-weight="700" fill="#f3e8ff" letter-spacing="1">KELAS PERMANEN</text>
  </g>
  <g transform="translate(560, 150)">
    <circle cx="85" cy="85" r="75" fill="#3b0764" stroke="#c084fc" stroke-width="2.5" opacity="0.85"/>
    <line x1="85" y1="20" x2="85" y2="30" stroke="#a855f7" stroke-width="3" stroke-linecap="round"/>
    <line x1="85" y1="140" x2="85" y2="150" stroke="#a855f7" stroke-width="3" stroke-linecap="round"/>
    <line x1="20" y1="85" x2="30" y2="85" stroke="#a855f7" stroke-width="3" stroke-linecap="round"/>
    <line x1="140" y1="85" x2="150" y2="85" stroke="#a855f7" stroke-width="3" stroke-linecap="round"/>
    <line x1="85" y1="85" x2="85" y2="45" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round"/>
    <line x1="85" y1="85" x2="120" y2="85" stroke="#c084fc" stroke-width="3" stroke-linecap="round"/>
    <circle cx="85" cy="85" r="6" fill="#a855f7" />
  </g>
  <g transform="translate(60, 150)">
    <text font-family="system-ui, -apple-system, sans-serif" font-size="16" font-weight="600" fill="#d8b4fe" letter-spacing="1.5">PRODUKTIVITAS &amp; WELLBEING</text>
    <text y="50" font-family="system-ui, -apple-system, sans-serif" font-size="34" font-weight="800" fill="#ffffff">Manajemen Waktu &amp;</text>
    <text y="92" font-family="system-ui, -apple-system, sans-serif" font-size="34" font-weight="800" fill="url(#accentGrad)">Fokus Kerja Produktif</text>
    <text y="145" font-family="system-ui, -apple-system, sans-serif" font-size="16" fill="#e9d5ff" opacity="0.9">
      <tspan x="0" dy="0">Metode Eisenhower, teknik Pomodoro, penanganan distraksi</tspan>
      <tspan x="0" dy="24">digital, serta pencegahan burnout di lingkungan kerja.</tspan>
    </text>
    <g transform="translate(0, 210)">
      <rect width="110" height="28" rx="6" fill="#3b0764" />
      <text x="12" y="19" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="600" fill="#f3e8ff">3 Bab Modul</text>
      <rect x="120" width="130" height="28" rx="6" fill="#3b0764" />
      <text x="132" y="19" font-family="system-ui, -apple-system, sans-serif" font-size="12" font-weight="600" fill="#f3e8ff">Asesmen Waktu</text>
    </g>
  </g>
</svg>
SVG,
        ];

        foreach ($thumbnails as $filename => $content) {
            $path = "{$dir}/{$filename}";
            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, trim($content));
            }
        }
    }
}

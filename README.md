# SIMPEL — Sistem Informasi Manajemen Pelatihan
### Platform E-Learning & Manajemen Siklus Pelatihan ASN Terpadu
**Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDM) Kabupaten Aceh Timur**

---

## 📌 Ringkasan Eksekutif

**SIMPEL** (*Sistem Informasi Manajemen Pelatihan*) adalah platform *Learning Management System* (LMS) modern berbasis web yang mengintegrasikan seluruh siklus pelatihan ASN: mulai dari perumusan rencana diklat, katalog publik multi-kategori, pendaftaran & verifikasi berkas, ruang belajar mandiri sekuensial (*continuity lock system*), absensi digital berbasis token, bank soal evaluasi kuis, hingga penerbitan e-Sertifikat resmi ber-QR Code.

### 3 Pilar Model Bisnis
1. **Model Digitalent (Katalog & Pendaftaran Publik):**
   * Eksplorasi katalog pelatihan publik dengan filter **3 Kategori Pelatihan** (*Pelatihan Kepemimpinan*, *Pelatihan Teknis*, *Pelatihan Fungsional*).
   * Ketersediaan fleksibel: **Permanen (Buka Terus / Self-Paced)** dan **Periode Tertentu (Batch / Terjadwal)**.
2. **Model Dicoding (Sisi Pembelajaran Siswa):**
   * Alur belajar linear sekuensial (*continuity lock*). Materi ke-$N$ terkunci otomatis dan baru terbuka setelah Materi ke-$(N-1)$ ditandai tuntas.
   * Evaluasi kuis pilihan ganda terintegrasi dengan ambang batas kelulusan (*passing grade* $\ge 70$).
   * Penerbitan e-Sertifikat otomatis saat progres belajar tuntas 100% dan lulus kuis.
3. **Model SIMPEL BKPSDM (Siklus Tata Kelola 8 Tahap):**
   * Mendigitalisasi tata kelola diklat ASN sesuai regulasi BKPSDM Kabupaten Aceh Timur.
   * Antarmuka bersih, elegan, dan profesional berpedoman pada tema warna resmi **Navy Blue (`#071a33`)** & **Gold Amber (`#f3bc42`)**.

---

## 🔄 Alur Siklus 8 Tahap Pelatihan ASN

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│ 1. Perencanaan  │  ──►  │ 2. Pendaftaran │  ──►  │ 3. Verifikasi   │  ──►  │ 4. Penjadwalan  │
└─────────────────┘       └─────────────────┘       └─────────────────┘       └─────────────────┘
                                                                                       │
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐                ▼
│ 8. Sertifikat   │  ◄──  │ 7. Evaluasi     │  ◄──  │ 6. Ruang Belajar│  ◄──  ┌─────────────────┐
│    & Pelaporan  │       │    (Kuis)       │       │    (Dicoding)   │       │ 5. Absensi      │
└─────────────────┘       └─────────────────┘       └─────────────────┘       │    Elektronik   │
                                                                              └─────────────────┘
```

1. **Tahap 1 — Perencanaan:** Admin merumuskan program pelatihan (kode diklat, kuota, model waktu permanen/batch, KAK, dan anggaran).
2. **Tahap 2 — Pendaftaran:** Peserta memilih pelatihan di katalog publik, melengkapi biodata profil ASN, dan mengunggah surat usulan.
3. **Tahap 3 — Verifikasi:** Verifikator memeriksa keabsahan berkas usulan (*Diverifikasi / Perlu Perbaikan / Ditolak*).
4. **Tahap 4 — Penjadwalan:** Penetapan sesi kelas, ruangan fisik / tautan Zoom, tanggal pelaksanaan, dan mentor pengampu.
5. **Tahap 5 — Absensi Elektronik:** Mentor mengaktifkan token kehadiran sesi; peserta melakukan check-in mandiri.
6. **Tahap 6 — Ruang Materi (Core Dicoding):** Peserta mempelajari materi modul berurutan (video, artikel, dokumen) dengan tombol reaktif *"Tandai Selesai"*.
7. **Tahap 7 — Evaluasi:** Peserta mengisi survei kepuasan pelatihan dan mengerjakan kuis pilihan ganda.
8. **Tahap 8 — Sertifikat & Pelaporan:** Sistem menerbitkan e-Sertifikat PDF ber-QR Code bagi peserta yang tuntas 100% dan lulus evaluasi.

---

## 👥 Pengguna & Peran Hak Akses (User Roles)

| Peran (Role) | Key Enum | Tanggung Jawab Utama |
| :--- | :--- | :--- |
| **Super Admin / Admin Diklat** | `admin` | Mengelola program diklat, input materi, jadwal kelas, bank soal, verifikasi berkas, dan konfigurasi blangko sertifikat. |
| **Mentor / Widyaiswara** | `mentor` | Mengunggah materi pembelajaran, membuka sesi token absensi kelas, dan memantau progres belajar siswa. |
| **Verifikator Berkas** | `verifikator` | Memvalidasi kelengkapan dokumen pendaftaran peserta (*Diverifikasi, Perlu Perbaikan, Ditolak*). |
| **Pimpinan / Eksekutif** | `pimpinan` | Memantau dashboard statistik eksekutif (serapan kuota, kelulusan, dan tren diklat aparatur). |
| **Peserta / Siswa ASN** | `peserta` | Menjelajahi katalog, mendaftar diklat, belajar di ruang kelas sekuensial, absen via token, mengerjakan kuis, dan mengunduh sertifikat. |

---

## 🛠️ Tech Stack & Standar Arsitektur (Atta Stack)

* **Backend & Framework:** PHP 8.4 · Laravel 12/13
* **Reaktivitas UI:** Livewire 3/4 · Alpine.js (Navigasi Single Page Application via `wire:navigate`)
* **Frontend UI:** Bootstrap 5.3 + Custom CSS SIMPEL Design Tokens
* **Tabel Interaktif:** Yajra DataTables Server-side via AJAX (Skrip ATC terisolasi)
* **Dokumen & Verifikasi:** `barryvdh/laravel-dompdf` (Cetak Sertifikat) · `simplesoftwareio/simple-qrcode` (QR Code Validasi)
* **Testing:** Pest PHP Test Framework
* **Code Formatter:** Laravel Pint

### Prinsip Arsitektur Atta Stack (`PRD-LW.md`):
* **Strict Separation of Concerns (SoC):**
  * `app/Models/`: Definisi skema tabel, casting, dan relasi Eloquent murni.
  * `app/Repositories/`: **Satu-satunya tempat** logika query database, transaksi (`DB::transaction`), manipulasi data, dan logging.
  * `app/Http/Controllers/`: **Ultra-Thin Controller**. HANYA melayani response JSON Yajra DataTables (`dataDt()`) dan unduhan binary PDF. Tidak menangani form CRUD atau render HTML!
  * `app/Livewire/`: Mengatur state UI, validasi form terpusat (`$this->form = [...]`), interaktivitas, dan event dispatch.
* **Pemisahan Tampilan:**
  * `resources/views/templates/`: Master layout (`app`, `auth`, `landing`), header sticky, sidebar, modal universal, toast alert.
  * `resources/views/mods/`: Antarmuka per modul bisnis (`admin/`, `auth/`, `landing/`) beserta skrip Action & Table Controller (`atc/`).

---

## 💻 Panduan Instalasi & Menjalankan Proyek

### 1. Prasyarat Sistem
* PHP $\ge 8.4$ (ekstensi: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `gd`, `zip`)
* Composer $\ge 2.2$
* Node.js $\ge 20.x$ & NPM
* Database MySQL atau MariaDB

### 2. Kloning & Pengaturan Dependensi
```bash
# Kloning repositori
git clone https://github.com/mhmdattarq/simple-e-learning.git
cd simple-e-learning

# Pasang dependensi PHP
composer install

# Pasang dependensi Node.js
npm install
```

### 3. Konfigurasi Lingkungan (.env)
```bash
# Salin file konfigurasi environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

Sesuaikan parameter koneksi database di file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simple_e_learning
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi & Data Seeder Awal
```bash
# Jalankan migrasi tabel dan seeding akun default
php artisan migrate --seed

# Buat symbolic link ke direktori storage berkas
php artisan storage:link
```

### 5. Kompilasi Aset Frontend
```bash
# Untuk mode pengembangan (Hot Reload)
npm run dev

# Atau kompilasi untuk mode produksi
npm run build
```

### 6. Menjalankan Server Lokal
```bash
php artisan serve
```
Akses platform melalui browser pada URL: **`http://127.0.0.1:8000`** atau virtual host lokal Anda.

---

## 🔑 Akun Demo Pengujian (Default Seeders)

Semua akun default menggunakan kata sandi: **`password`**

| Peran (Role) | Identitas Login (Email / NIP) | Nama Lengkap | Instansi Asal |
| :--- | :--- | :--- | :--- |
| **Admin Diklat** | `admin@simpel.go.id` / `198501012010011001` | Muhammad Suryasyah, S.STP | BKPSDM Kab. Aceh Timur |
| **Mentor / Widyaiswara** | `mentor@simpel.go.id` / `198002022005011002` | Dr. Fauzan, M.Pd | BKPSDM Kab. Aceh Timur |
| **Verifikator Berkas** | `verifikator@simpel.go.id` / `198703032011012003` | Nur Aini, S.Kom | BKPSDM Kab. Aceh Timur |
| **Pimpinan / Eksekutif** | `pimpinan@simpel.go.id` / `197504041999031004` | Teuku Dedi Iskandar, S.STP, M.SP | Kepala BKPSDM Kab. Aceh Timur |
| **Peserta ASN** | `peserta@simpel.go.id` / `199205052018011005` | Cut Mutia, S.Sos | Dinas Pendidikan & Kebudayaan |

> **Smart Dual-Identifier Login:** Form login mendukung deteksi otomatis: jika input berupa angka 18 digit maka diverifikasi sebagai **NIP**, selain itu diproses sebagai **Email**.

---

## 🧪 Pengujian Otomatis & Pemformatan Kode

```bash
# Menjalankan seluruh test suite Pest
php artisan test --compact

# Menjalankan test suite spesifik
vendor/bin/pest tests/Feature/AuthTest.php
vendor/bin/pest tests/Feature/PerencanaanTest.php

# Memeriksa dan merapikan standar format kode (Laravel Pint)
vendor/bin/pint --dirty --format agent
```

---

## 📁 Struktur Direktori Kunci

```
simple-e-learning/
├── app/
│   ├── Enums/               # Definisi Enum PHP (Role)
│   ├── Http/
│   │   ├── Controllers/     # Ultra-thin Controller (Yajra DT endpoint dataDt)
│   │   └── Middleware/      # RoleMiddleware authorization check
│   ├── Livewire/
│   │   ├── Admin/           # Komponen admin per modul (Data, Create, Edit)
│   │   ├── Auth/            # Komponen Login & Register Peserta
│   │   └── Landing/         # Komponen Landing Page Publik
│   ├── Models/              # Eloquent models & schema representations
│   └── Repositories/        # Single Point of Access DB operations (CRUD & DT)
├── resources/
│   ├── css/                 # SIMPEL custom tokens & styling overrides
│   └── views/
│       ├── mods/            # Business views (admin/, auth/, landing/)
│       │   └── admin/
│       │       └── perencanaan/atc/  # Action & Table Controller (DataTables JS)
│       └── templates/       # Global scaffolding (layouts/, components/ header, sidebar, modal, toast)
├── routes/
│   └── web.php              # Definisi rute aplikasi & proteksi role
└── tests/                   # Automated Pest feature & unit tests
```

---

## 📜 Lisensi & Pengembang

Proyek ini dikembangkan untuk **Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDM) Kabupaten Aceh Timur**.  
Hak Cipta © 2026 BKPSDM Kabupaten Aceh Timur. Seluruh hak cipta dilindungi undang-undang.

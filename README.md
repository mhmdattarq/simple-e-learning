# SIMPEL — Sistem Informasi Manajemen Pelatihan

### Platform E-Learning & Manajemen Siklus Pelatihan ASN Terpadu

**Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDM) Kabupaten Aceh Timur**

---

## Pengguna & Peran Hak Akses (User Roles)

| Peran (Role)                   | Key Enum      | Tanggung Jawab Utama                                                                                                                   |
| :----------------------------- | :------------ | :------------------------------------------------------------------------------------------------------------------------------------- |
| **Super Admin / Admin Diklat** | `admin`       | Mengelola program diklat, input materi, jadwal kelas, bank soal, verifikasi berkas, dan konfigurasi blangko sertifikat.                |
| **Mentor / Widyaiswara**       | `mentor`      | Mengunggah materi pembelajaran, membuka sesi token absensi kelas, dan memantau progres belajar siswa.                                  |
| **Verifikator Berkas**         | `verifikator` | Memvalidasi kelengkapan dokumen pendaftaran peserta (_Diverifikasi, Perlu Perbaikan, Ditolak_).                                        |
| **Pimpinan / Eksekutif**       | `pimpinan`    | Memantau dashboard statistik eksekutif (serapan kuota, kelulusan, dan tren diklat aparatur).                                           |
| **Peserta / Siswa ASN**        | `peserta`     | Menjelajahi katalog, mendaftar diklat, belajar di ruang kelas sekuensial, absen via token, mengerjakan kuis, dan mengunduh sertifikat. |

---

## Akun Demo Pengujian (Default Seeders)

Semua akun default menggunakan kata sandi: **`password`**

| Peran (Role)             | Identitas Login (Email / NIP)                     | Nama Lengkap                     | Instansi Asal                 |
| :----------------------- | :------------------------------------------------ | :------------------------------- | :---------------------------- |
| **Admin Diklat**         | `admin@simpel.go.id` / `198501012010011001`       | Muhammad Suryasyah, S.STP        | BKPSDM Kab. Aceh Timur        |
| **Mentor / Widyaiswara** | `mentor@simpel.go.id` / `198002022005011002`      | Dr. Fauzan, M.Pd                 | BKPSDM Kab. Aceh Timur        |
| **Verifikator Berkas**   | `verifikator@simpel.go.id` / `198703032011012003` | Nur Aini, S.Kom                  | BKPSDM Kab. Aceh Timur        |
| **Pimpinan / Eksekutif** | `pimpinan@simpel.go.id` / `197504041999031004`    | Teuku Dedi Iskandar, S.STP, M.SP | Kepala BKPSDM Kab. Aceh Timur |
| **Peserta ASN**          | `peserta@simpel.go.id` / `199205052018011005`     | Cut Mutia, S.Sos                 | Dinas Pendidikan & Kebudayaan |

> **Smart Dual-Identifier Login:** Form login mendukung deteksi otomatis: jika input berupa angka 18 digit maka diverifikasi sebagai **NIP**, selain itu diproses sebagai **Email**.

---

## Lisensi & Pengembang

Proyek ini dikembangkan untuk **Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDM) Kabupaten Aceh Timur**.  
Hak Cipta © 2026 BKPSDM Kabupaten Aceh Timur. Seluruh hak cipta dilindungi undang-undang.

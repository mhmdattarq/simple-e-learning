# Draft Rencana Fitur: Verifikasi & Kelengkapan Profil Wajib (Strict Profile Enforcement)

> **Status:** Draft / Pending Approval (Menunggu arahan resmi dari PM & Client)  
> **Tanggal Dibuat:** 17 Oktober 2026  
> **Kategori:** User Onboarding & Access Control (OAuth / Google Login)

---

## 1. Latar Belakang & Masalah
Saat peserta mendaftar atau masuk menggunakan akun Google (OAuth), data yang diperoleh secara otomatis dari akun Google umumnya hanya:
- Nama Lengkap
- Alamat Email
- Foto Profil / Avatar

Data spesifik kepegawaian atau identitas resmi pelatihan (seperti **Nomor Telepon / WhatsApp**, **Alamat**, **NIP**, **Instansi**, dan **Jabatan**) masih kosong (`null`).

Jika peserta langsung diizinkan mendaftar dan mengikuti pelatihan tanpa melengkapi profil terlebih dahulu:
1. Data peserta pada laporan pelatihan dan database menjadi tidak lengkap.
2. Sertifikat pelatihan resmi yang diterbitkan tidak memiliki data identitas lengkap yang valid.
3. Administrator kesulitan melakukan verifikasi dan komunikasi kepada peserta.

---

## 2. Pendekatan Desain: Opsi 2 (Ketat Selektif)
Peserta tetap diberi kebebasan untuk melihat katalog publik kelas, namun **akses belajar, pendaftaran kelas, dan pengerjaan kuis/ujian dikunci secara ketat** sampai profil dilengkapi.

### Alur Kerja (Workflow):
```
[User Login via Google / Register]
               │
               ▼
   [Cek: Profil Lengkap?]
    ├─── YES ───► Diarahkan ke Halaman Tujuan / Katalog Kelas
    └─── NO  ───► Diarahkan ke Halaman Profil (/profil) dengan notifikasi wajib melengkapi data
               │
               ▼
    [Aktivitas Peserta di Katalog]
    ├── Buka Detail Kelas: BISA (Melihat deskripsi & silabus)
    ├── Klik 'Daftar Kelas' / 'Mulai Belajar': DIKUNCI ──► Redirect ke /profil
    ├── Akses Ruang Materi (/materi): DIKUNCI (Middleware) ──► Redirect ke /profil
    └── Akses Ujian / Kuis (/evaluasi): DIKUNCI (Middleware) ──► Redirect ke /profil
```

---

## 3. Spesifikasi Teknis Implementasi

### A. Kriteria Validasi Profil Lengkap (`User::isProfileComplete()`)
File: `app/Models/User.php`
- [x] **Nama Lengkap** (`name`)
- [x] **Email** (`email`)
- [x] **Nomor HP / WhatsApp Aktif** (`phone_number`)
- [x] **Alamat Domisili / Kantor** (`address`)
- *(Opsional sesuai arahan PM)*: **NIP** (`nip`), **Instansi** (`agency`), **Jabatan** (`position`).

### B. Titik Penguncian (Enforcement Points)

#### 1. Pasca-Login Google (`GoogleAuthController@callback`)
- **Lokasi:** `app/Http/Controllers/Auth/GoogleAuthController.php`
- **Tindakan:**
  ```php
  if (! $user->isProfileComplete()) {
      session()->flash('alert-show', [
          'type' => 'warning',
          'title' => 'Lengkapi Profil Anda',
          'message' => 'Silakan lengkapi data profil (Nomor HP dan Alamat) Anda sebelum dapat mengakses materi dan ujian pelatihan.',
      ]);

      return redirect()->route('peserta.profil');
  }
  ```

#### 2. Middleware Akses Materi & Kuis (`EnsureProfileComplete`)
- **Tujuan:** Mencegah user mengakses langsung via bookmark, riwayat browser, atau URL langsung ke ruang materi & evaluasi.
- **Rute yang Diproteksi di `routes/web.php`:**
  - `peserta.materi.*` (`/{jenis}/{course}/materi`)
  - `peserta.evaluasi.*` (`/{jenis}/{course}/evaluasi/{quiz}`)
- **Tindakan:** Jika `! $request->user()->isProfileComplete()`, redirect ke `route('peserta.profil')` dengan flash message peringatan.

#### 3. Komponen Detail Kelas (`KelasDetail`)
- **Lokasi:** `app/Livewire/Landing/Kelas/KelasDetail.php` & view terkait.
- **Tindakan:**
  - Pada method pendaftaran / `enroll()`: Tambahkan pengecekan `auth()->user()->isProfileComplete()`.
  - Jika belum lengkap, jangan buat record `course_user`, tapi kirim notifikasi dan redirect ke `/profil`.
  - Pada tampilan UI: Tombol pendaftaran dapat menampilkan indikator/badge *"Harap Lengkapi Profil untuk Mendaftar"*.

---

## 4. Checklist Eksekusi (Ketika Fitur Disetujui)
- [ ] Buat Middleware `EnsureProfileComplete` via Artisan: `php artisan make:middleware EnsureProfileComplete --no-interaction`.
- [ ] Daftarkan middleware pada rute ruang materi dan kuis di `routes/web.php`.
- [ ] Sesuaikan logika pengalihan di `GoogleAuthController.php`.
- [ ] Beri proteksi pada aksi enroll di Livewire `KelasDetail.php`.
- [ ] Buat unit & feature test di Pest untuk menguji skenario user belum lengkap dan sudah lengkap.
- [ ] Jalankan `vendor/bin/pint --dirty --format agent` dan verifikasi seluruh test suite.

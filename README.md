# SIM Keanggotaan IMM (Ikatan Mahasiswa Muhammadiyah)

Sistem Informasi Manajemen (SIM) Keanggotaan Ikatan Mahasiswa Muhammadiyah (IMM) adalah platform web terpadu berbasis Laravel untuk pengelolaan data kader, verifikasi pendaftaran anggota baru, manajemen agenda perkaderan & sesi kegiatan, presensi & penilaian instruktur, penerbitan e-KTA digital, arsip dokumen, serta generate e-sertifikat otomatis berbasis antrean background job.

---

## 🚀 Fitur Utama Berdasarkan Peran

### 1. Pengunjung / Guest (Publik)
- **Landing Page Interaktif**: Menampilkan profil organisasi, 3 pilar pergerakan, data statistik, program kerja, dan 3 kegiatan terbaru (di-cache dengan key `kegiatan.terbaru`).
- **Detail Kegiatan**: Halaman informasi lengkap per kegiatan beserta rekomendasi kegiatan lainnya.
- **Pendaftaran Calon Anggota**: Form pendaftaran online dengan unggah foto profil (publik) dan dokumen identitas KTP/KTM ke penyimpanan privat (`storage/app/private/pendaftaran`).

### 2. Kader (Anggota Aktif)
- **Dashboard Kader**: Ringkasan profil, statistik kehadiran, dan kegiatan mendatang.
- **E-KTA Digital**: Kartu Tanda Anggota digital interaktif dengan efek 3D Flip (sisi depan & belakang), QR code verifikasi, fitur cetak langsung, dan ekspor PDF resmi standar CR80.
- **Materi Perkaderan**: Mengakses, mengunduh, dan menyimpan modul/materi dari kegiatan yang dihadiri.
- **E-Sertifikat**: Melihat dan mengunduh sertifikat resmi kegiatan yang telah diterbitkan oleh pengurus.
- **Riwayat Keaktifan**: Grafik persentase kehadiran dan riwayat partisipasi kegiatan.
- **E-Arsip Mandiri**: Mengunggah, mengkategorikan, dan mengunduh dokumen penting organisasi pribadi ke penyimpanan privat.

### 3. Instruktur (Pemandu Perkaderan)
- **Manajemen Kegiatan & Sesi**: Membuat agenda kegiatan serta membagi kegiatan menjadi beberapa sesi pertemuan (`SesiKegiatan`).
- **Pencatatan Presensi Lapangan**: Otoritas utama pencatatan absensi peserta (`Hadir`, `Izin`, `Alfa`).
- **Verifikasi Kehadiran**: Memvalidasi bukti kehadiran kader per sesi kegiatan.
- **Penilaian Kegiatan Multi-Sesi**: Memberikan nilai kualitatif/kuantitatif (A–D) pada kader sebagai syarat kelulusan perkaderan.
- **Unggah Materi Kegiatan**: Membagikan slide, dokumen modul, dan bahan bacaan untuk peserta.
- **Laporan Kegiatan & Berita Acara**: Menyusun dan mengunduh laporan pelaksanaan kegiatan dalam format PDF.

### 4. Admin (Sekretariat & Pengurus Cabang)
- **Dashboard Eksekutif**: Monitoring metrik keanggotaan, pendaftaran pending, dan kegiatan aktif.
- **Validasi Pendaftaran Anggota**: Memeriksa dokumen identitas (preview/download aman), menyetujui pendaftaran secara transaksional (`DB::transaction` + `lockForUpdate`), serta otomatis membuat akun login `User` dan profil `Anggota`.
- **Manajemen Anggota & Penomoran NIA**: Pengelolaan biodata kader, aktivasi status, serta generate Nomor Induk Anggota (NIA) satuan maupun massal (*bulk generation*).
- **Penerbitan E-Sertifikat Otomatis**: Generate sertifikat massal berbasis antrean background job (`GenerateCertificateJob`), pengecekan kelayakan otomatis (`CertificateEligibility`), dan kustomisasi template sertifikat (`sertifikat_settings.json`).
- **Manajemen Berkas & Arsip**: Akses dokumen arsip privat seluruh kader.
- **Rekapitulasi & Ekspor Laporan**: Ekspor data keanggotaan dan statistik ke format PDF dan Excel.

---

## 🛠️ Tech Stack & Arsitektur

- **Framework**: Laravel 13 (PHP 8.4)
- **Database**: MariaDB 10.11+
- **Frontend / Styling**: Tailwind CSS, Alpine.js, Blade Views, Vite
- **PDF & Dokumen**: Barryvdh DomPDF (`barryvdh/laravel-dompdf`)
- **Manipulasi Gambar**: Intervention Image v3
- **Containerization**: Docker & Docker Compose
- **Job & Queue**: Database Queue Driver (Asynchronous Certificate Generation)
- **Penyimpanan Berkas**:
  - **Public Disk** (`storage/app/public`): `foto_profil`, `kegiatan_thumbnails`, `sertifikat` (wajib `storage:link`).
  - **Private / Local Disk** (`storage/app/private`): `pendaftaran` (dokumen KTP/KTM), `arsip` (berkas kader), `sertifikat_settings.json`.

---

## 📦 Panduan Instalasi Cepat (Docker)

### 1. Prasyarat
- Docker Engine & Docker Compose
- Node.js & npm (di mesin host untuk kompilasi frontend Vite)

### 2. Langkah Setup Pertama Kali

```bash
# 1. Masuk ke direktori proyek
cd ~/Developments/sim-Keanggotaan-imm-docker

# 2. Salin environment file
cp .env.example .env
cat .env.docker.example >> .env

# 3. Jalankan container Docker (PHP 8.4 app, MariaDB, phpMyAdmin, Queue Worker)
docker compose up -d --build

# 4. Install dependensi PHP di dalam container
docker compose exec app composer install

# 5. Generate application key
docker compose exec app php artisan key:generate

# 6. Jalankan migrasi database
docker compose exec app php artisan migrate

# 7. Buat symbolic link public storage
docker compose exec app php artisan storage:link

# 8. Install & kompilasi dependensi frontend di host
npm install
npm run build
```

Aplikasi siap dibuka di:
- **Aplikasi Web**: [http://localhost:8000](http://localhost:8000)
- **phpMyAdmin**: [http://localhost:8080](http://localhost:8080)

---

## 🧪 Data Dummy & Seeder

Untuk mengisi database dengan data pengujian lengkap beserta berkas PDF/gambar dummy:

```bash
# Reset database dan jalankan seeder
docker compose exec app php artisan migrate:fresh --seed

# Buat dummy files (foto profil, KTP, arsip PDF, background sertifikat)
docker compose exec app php artisan demo:seed-files
```

### Akun Bawaan (Default Demo Credentials)
- **Admin**: `admin@imm.or.id` (atau cek email di tabel `users`) / Password: `password`
- **Instruktur**: Cek tabel `users` dengan `role = 'instruktur'` / Password: `password`
- **Kader**: Cek tabel `users` dengan `role = 'kader'` / Password: `password`

---

## ⚡ Perintah Penting Pengembang (Cheat Sheet)

```bash
# Menjalankan unit & feature testing
docker compose exec app php artisan test

# Menjalankan test tertentu
docker compose exec app php artisan test --filter=CertificateEligibilityTest

# Format kode PHP sesuai standar Pint
docker compose exec app vendor/bin/pint --dirty

# Menjalankan background worker antrean sertifikat
docker compose exec app php artisan queue:work

# Memeriksa daftar route aktif
docker compose exec app php artisan route:list

# Development frontend (HMR Vite)
npm run dev
```

---

## 📚 Dokumentasi Lanjutan

Untuk panduan arsitektur teknis lebih mendalam dan buku panduan pengoperasian:
- [Panduan Fungsional Developer (`support-for-developer/dokumentasi.md`)](support-for-developer/dokumentasi.md)
- [Panduan Pengoperasian User Manual (`support-for-developer/pengoperasian.md`)](support-for-developer/pengoperasian.md)
- [Panduan Dasar Pemrograman Laravel (`support-for-developer/basics/`)](support-for-developer/basics/)
- [Panduan Deployment Produksi (`DEPLOYMENT.md`)](DEPLOYMENT.md)
- [Instruksi AI Agent (`AGENTS.md`)](AGENTS.md)


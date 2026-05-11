# Checklist Implementasi (Development Task List)

## Sistem Informasi Manajemen Keanggotaan dan Kearsipan Berbasis Website

**Versi Dokumen:** 1.0  
**Tanggal:** 10 Mei 2026  
**Pendekatan:** Rapid Application Development (RAD)

---

> **Panduan penggunaan checklist:**
> - `[ ]` = Belum dikerjakan
> - `[/]` = Sedang dikerjakan
> - `[x]` = Selesai
>
> Checklist disusun secara **berurutan** sesuai prinsip RAD: setup → database → backend logic → frontend UI → testing → deployment.

---

## Fase 1: Persiapan & Setup Proyek

### 1.1 Instalasi & Konfigurasi Environment

- [x] Instalasi Laragon/XAMPP (PHP 8.2+, MySQL 8.0+, Composer, Node.js 18+)
- [x] Verifikasi versi PHP (`php -v`), Composer (`composer -V`), Node (`node -v`)
- [x] Buat proyek Laravel baru: `composer create-project laravel/laravel SIM-Keanggotaan-IMM`
- [x] Konfigurasi file `.env` (APP_NAME=SIM-Keanggotaan-IMM, DB_DATABASE=sim_keanggotaan_imm, APP_URL=http://localhost:8000)
- [x] Buat database MySQL: `sim_keanggotaan_imm`
- [x] Jalankan `php artisan migrate` untuk memastikan koneksi database berhasil

### 1.2 Instalasi Dependensi

- [x] Instalasi Laravel Breeze: `composer require laravel/breeze --dev`
- [x] Setup Breeze dengan Blade: `php artisan breeze:install blade`
- [x] Instalasi Bootstrap 5: `npm install bootstrap @popperjs/core bootstrap-icons`
- [x] Konfigurasi Vite (`vite.config.js`) untuk compile Bootstrap
- [x] Import Bootstrap CSS & JS di `resources/css/app.css` dan `resources/js/app.js`
- [x] Instalasi DomPDF: `composer require barryvdh/laravel-dompdf`
- [x] Instalasi Laravel Excel: `composer require maatwebsite/excel`
- [x] Instalasi Intervention Image: `composer require intervention/image`
- [x] Instalasi Chart.js: `npm install chart.js`
- [ ] Instalasi Laravel PWA: `composer require silviolleite/laravel-pwa`
- [x] Jalankan `npm run build` untuk memastikan semua asset tercompile

### 1.3 Konfigurasi Awal Proyek

- [x] Set timezone di `config/app.php`: `'timezone' => 'Asia/Jakarta'`
- [x] Set locale di `config/app.php`: `'locale' => 'id'`
- [x] Konfigurasi filesystem di `config/filesystems.php` (disk untuk arsip, sertifikat, foto)
- [x] Buat symbolic link storage: `php artisan storage:link`
- [x] Publish konfigurasi PWA: (Manual Setup - Done)
- [x] Konfigurasi `config/laravelpwa.php`: (Manual Setup via manifest.json - Done)
- [ ] Siapkan ikon PWA berbagai ukuran (72x72, 96x96, 128x128, 144x144, 152x152, 192x192, 384x384, 512x512) di `public/images/icons/`
- [x] Verifikasi `manifest.json` dan `sw.js` di folder `public/`
- [ ] Setup Git repository dan commit awal

---

## Fase 2: Database (Migration, Model, Seeder)

### 2.1 Pembuatan Migration

> Urutan migration penting karena ada foreign key yang saling bergantung.

- [x] Migration tabel `users` — modifikasi migration bawaan, tambah kolom `role` (enum: admin, kader)
- [x] Migration tabel `anggota` — FK ke `users.id`
  - Kolom: `user_id`, `nia`, `nama_lengkap`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `no_telp`, `foto_profil`, `status_aktif`
- [x] Migration tabel `pendaftaran` — FK ke `users.id` (nullable)
  - Kolom: `user_id`, `nama_lengkap`, `email`, `tempat_lahir`, `tanggal_lahir`, `no_telp`, `alamat`, `tanggal_daftar`, `file_persyaratan`, `status_validasi`, `catatan_admin`
- [x] Migration tabel `kegiatan`
  - Kolom: `nama_kegiatan`, `deskripsi`, `tanggal_waktu`, `lokasi`
- [x] Migration tabel `presensi` — FK ke `kegiatan.id` dan `anggota.id`
  - Kolom: `kegiatan_id`, `anggota_id`, `waktu_hadir`, `status_kehadiran`
  - Unique constraint: (`kegiatan_id`, `anggota_id`)
- [x] Migration tabel `sertifikat` — FK ke `kegiatan.id` dan `anggota.id`
  - Kolom: `kegiatan_id`, `anggota_id`, `nomor_sertifikat`, `file_sertifikat`
- [x] Migration tabel `arsip` — FK ke `anggota.id`
  - Kolom: `anggota_id`, `nomor_dokumen`, `judul_dokumen`, `kategori_arsip`, `file_arsip`, `tanggal_unggah`
- [x] Jalankan semua migration: `php artisan migrate`
- [x] Jalankan seeder: `php artisan db:seed`

### 2.2 Pembuatan Model & Relasi Eloquent

- [x] Model `User` — tambah relasi `hasOne(Anggota)`, `hasOne(Pendaftaran)`, tambah `role` ke `$fillable`
- [x] Model `Anggota` — relasi: `belongsTo(User)`, `hasMany(Arsip)`, `hasMany(Presensi)`, `hasMany(Sertifikat)`
- [x] Model `Pendaftaran` — relasi: `belongsTo(User)`
- [x] Model `Kegiatan` — relasi: `hasMany(Presensi)`, `hasMany(Sertifikat)`
- [x] Model `Presensi` — relasi: `belongsTo(Kegiatan)`, `belongsTo(Anggota)`
- [x] Model `Sertifikat` — relasi: `belongsTo(Kegiatan)`, `belongsTo(Anggota)`
- [x] Model `Arsip` — relasi: `belongsTo(Anggota)`

### 2.3 Pembuatan Seeder

- [x] Seeder `UserSeeder` — buat akun Admin default (email: admin@admin.com, password: password)
- [x] Seeder `AnggotaSeeder` — buat 5-10 data Kader dummy untuk testing
- [x] Seeder `KegiatanSeeder` — buat 3-5 kegiatan dummy
- [x] Seeder `PresensiSeeder` — buat data presensi dummy
- [x] Jalankan seeder: `php artisan db:seed`

---

## Fase 3: Middleware & Routing

### 3.1 Middleware

- [x] Buat `RoleMiddleware`: `php artisan make:middleware RoleMiddleware`
- [x] Implementasi logika pengecekan role (admin/kader) di middleware
- [x] Daftarkan middleware di `bootstrap/app.php` (Laravel 11)

### 3.2 Routing

- [x] Route publik:
  - [x] `GET /` → Halaman utama
  - [x] `GET /pendaftaran` → Form pendaftaran
  - [x] `POST /pendaftaran` → Proses pendaftaran
- [x] Route autentikasi (bawaan Breeze):
  - [x] `GET /login`, `POST /login`, `POST /logout`
- [x] Route Admin (grup middleware `auth` + `role:admin`):
  - [x] `GET /admin/dashboard`
  - Resource route: `/admin/anggota` (CRUD)
  - Resource route: `/admin/kegiatan` (CRUD)
  - `GET /admin/presensi/{kegiatan}` → Form presensi
  - `POST /admin/presensi/{kegiatan}` → Simpan presensi
  - `GET /admin/pendaftaran` → Daftar pendaftar
  - `PUT /admin/pendaftaran/{id}/validasi` → Validasi pendaftaran
  - Resource route: `/admin/sertifikat` (CRUD + generate)
  - Resource route: `/admin/arsip` (CRUD)
  - `GET /admin/laporan` → Halaman filter laporan
  - `POST /admin/laporan/export` → Export laporan
  - `GET /admin/profil` → Profil Admin
  - `PUT /admin/profil` → Update profil Admin
- [x] Route Kader (grup middleware `auth` + `role:kader`):
  - `GET /kader/dashboard`
  - `GET /kader/profil` → Profil Kader
  - `PUT /kader/profil` → Update profil
  - `GET /kader/ekta` → Preview E-KTA
  - `GET /kader/ekta/download` → Download PDF E-KTA
  - `GET /kader/sertifikat` → Daftar sertifikat
  - `GET /kader/sertifikat/{id}/download` → Download sertifikat
  - `GET /kader/riwayat` → Riwayat keaktifan
  - `GET /kader/arsip` → Daftar arsip
  - `POST /kader/arsip` → Upload arsip
  - `GET /kader/arsip/{id}/download` → Download arsip

---

## Fase 4: Backend Logic (Controller & Form Request)

### 4.1 Form Request Validation

- [x] `LoginRequest` — validasi email & password (required)
- [x] `PendaftaranRequest` — validasi Nama, TTL, Email, dsb. (required, format)
- [x] `AnggotaRequest` — validasi data anggota (CRUD)
- [x] `KegiatanRequest` — validasi data kegiatan
- [x] `PresensiRequest` — validasi data presensi (array status kehadiran)
- [x] `ArsipRequest` — validasi file upload (ekstensi, ukuran)
- [x] `ProfilRequest` — validasi data profil
- [x] `SertifikatRequest` — validasi data sertifikat (kegiatan, daftar anggota)
- [x] `LaporanRequest` — validasi filter laporan (tanggal, jenis)

### 4.2 Controller — Autentikasi

- [x] `AuthController@login` — Proses login, redirect berdasarkan role
- [x] `AuthController@logout` — Proses logout, hapus sesi
- [x] Kustomisasi redirect setelah login di `AuthenticatedSessionController` (Breeze)

### 4.3 Controller — Dashboard

- [x] `DashboardController@adminDashboard` — Tampilkan statistik: total anggota, total kegiatan, pendaftar pending, dll.
- [x] `DashboardController@kaderDashboard` — Tampilkan ringkasan: kegiatan terdekat, statistik kehadiran, sertifikat terbaru

### 4.4 Controller — Pendaftaran (Pengunjung)

- [x] `PendaftaranController@create` — Tampilkan form pendaftaran
- [x] `PendaftaranController@store` — Validasi & simpan data, set status 'pending', redirect ke halaman sukses

### 4.5 Controller — Validasi Pendaftaran (Admin)

- [x] `ValidasiPendaftaranController@index` — Tampilkan daftar pendaftar (status: pending)
- [x] `ValidasiPendaftaranController@show` — Tampilkan detail pendaftar
- [x] `ValidasiPendaftaranController@approve` — Setujui: buat user + anggota, ubah status
- [x] `ValidasiPendaftaranController@reject` — Tolak: ubah status menjadi 'ditolak'

### 4.6 Controller — Manajemen Data Anggota (Admin)

- [x] `AnggotaController@index` — Tampilkan tabel anggota aktif
- [x] `AnggotaController@create` — Form tambah anggota
- [x] `AnggotaController@store` — Validasi & simpan anggota baru
- [x] `AnggotaController@edit` — Form edit anggota
- [x] `AnggotaController@update` — Validasi & update data anggota
- [x] `AnggotaController@destroy` — Hapus data anggota

### 4.7 Controller — Manajemen Kegiatan & Presensi (Admin)

- [x] `KegiatanController@index` — Tampilkan kalender/daftar kegiatan
- [x] `KegiatanController@create` — Form tambah kegiatan
- [x] `KegiatanController@store` — Simpan kegiatan baru
- [x] `KegiatanController@edit` — Form edit kegiatan
- [x] `KegiatanController@update` — Update kegiatan
- [x] `KegiatanController@destroy` — Hapus kegiatan
- [x] `PresensiController@create` — Tampilkan form presensi (daftar Kader + checkbox status)
- [x] `PresensiController@store` — Batch simpan data presensi

### 4.8 Controller — Manajemen Arsip Dokumen

- [x] `ArsipController@index` — Tampilkan daftar arsip (dengan filter kategori)
- [x] `ArsipController@store` — Validasi file, simpan ke storage & database
- [x] `ArsipController@download` — Stream download file arsip
- [x] `ArsipController@destroy` — Hapus file dari storage & database

### 4.9 Controller — Manajemen Profil

- [x] `ProfilController@show` — Tampilkan form profil (pre-filled)
- [x] `ProfilController@update` — Validasi & update profil, termasuk upload foto profil

### 4.10 Controller — E-KTA (Kader)

- [x] `EktaController@show` — Tampilkan preview E-KTA dengan data profil
- [x] `EktaController@download` — Generate & download PDF E-KTA menggunakan DomPDF

### 4.11 Controller — E-Sertifikat

- [x] `SertifikatController@index` (Admin) — Tampilkan daftar semua sertifikat
- [x] `SertifikatController@create` (Admin) — Form buat sertifikat baru (pilih kegiatan + Kader)
- [x] `SertifikatController@generate` (Admin) — Generate sertifikat untuk Kader terpilih, simpan PDF
- [x] `SertifikatController@mySertifikat` (Kader) — Tampilkan daftar sertifikat milik Kader
- [x] `SertifikatController@download` (Kader) — Download file PDF sertifikat

### 4.12 Controller — Riwayat Keaktifan (Kader)

- [x] `RiwayatKeaktifanController@index` — Query presensi, hitung statistik, tampilkan tabel & data grafik

### 4.13 Controller — Laporan (Admin)

- [x] `LaporanController@index` — Tampilkan halaman filter laporan
- [x] `LaporanController@exportPdf` — Generate & download laporan PDF
- [x] `LaporanController@exportExcel` — Generate & download laporan Excel

---

## Fase 5: Frontend UI (View Blade + Bootstrap 5 — Mobile Web App)

### 5.1 Layout Utama & Navigasi Mobile

- [x] Layout utama `layouts/app.blade.php`:
  - [x] Gunakan viewport meta tag `<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">`
  - [x] Tambahkan tag PWA: `@laravelPWA` di `<head>` (Placeholder CSS/JS ready)
  - [x] Struktur body: header kecil (judul halaman) + konten utama + Bottom Navigation Bar
  - [x] Tambahkan `padding-bottom: 70px` pada konten utama agar tidak tertutup Bottom Nav
  - [x] **Tidak menggunakan class breakpoint** (`-md`, `-lg`, `-xl`) — layout murni mobile
- [x] Komponen `components/bottom-nav.blade.php`:
  - [x] Gunakan `<nav class="navbar fixed-bottom bg-white shadow-lg border-top">`
  - [x] Isi dengan 4-5 menu utama berupa ikon (Bootstrap Icons) + label kecil
  - [x] Menu Admin: Dashboard, Anggota, Kegiatan, Arsip, Lainnya
  - [x] Menu Kader: Dashboard, E-KTA, Sertifikat, Riwayat, Lainnya
  - [x] Tambahkan highlight/active state pada menu yang sedang dibuka
  - [x] Semua tombol berukuran minimal 44x44px (touch-friendly)
- [x] Layout autentikasi `layouts/auth.blade.php` — Layout khusus halaman login (tanpa Bottom Nav)
- [x] Komponen alert/flash message (`_alert.blade.php`)
- [x] Komponen modal konfirmasi hapus (`_modal-delete.blade.php`)
- [x] Komponen pagination

### 5.2 Halaman Publik

- [x] Halaman utama (`/`) — Landing page mobile-style dengan tombol "Daftar Sekarang" dan "Login"
- [x] Halaman pendaftaran — Form registrasi full-width, validasi client-side
- [x] Halaman sukses pendaftaran — Pesan konfirmasi

### 5.3 Halaman Autentikasi

- [x] Halaman login — Form email & password, centered, full-width input, pesan error
- [x] Halaman lupa password — Form email, pesan sukses/status

### 5.4 Halaman Admin

- [x] Dashboard Admin — Card statistik (total anggota, kegiatan, pendaftar pending), grafik ringkasan
- [x] Manajemen Pendaftaran — Tabel pendaftar pending, tombol detail/validasi
- [x] Detail Pendaftaran — Tampilkan data lengkap + file, tombol Setuju/Tolak
- [x] Manajemen Anggota — Tabel anggota, fitur search, tombol detail/edit/hapus
- [x] Manajemen Kegiatan — Tabel kegiatan, tombol tambah/edit/hapus/presensi
- [x] Manajemen Presensi — Form batch update kehadiran anggota dalam satu kegiatan
- [x] Manajemen Arsip — Form upload arsip, tabel daftar arsip, tombol download/hapus
- [x] Manajemen Laporan — Filter tanggal/jenis, tombol export PDF/Excel
- [x] Halaman Profil Admin — Form profil pre-filled, full-width

### 5.5 Halaman Kader

- [x] Dashboard Kader — Card ringkasan kegiatan, statistik kehadiran, notifikasi sertifikat baru
- [x] Halaman Profil — Form profil pre-filled, upload foto
- [x] Halaman E-KTA — Preview kartu anggota digital, tombol Cetak/Unduh PDF
- [x] Halaman E-Sertifikat Saya — Daftar sertifikat dengan tombol Unduh
- [x] Halaman Riwayat Keaktifan — Tabel riwayat presensi + statistik (H/I/A)
- [x] Halaman Arsip Dokumen (Kader) — List arsip, fitur unggah/unduh

### 5.6 Template PDF

- [x] Template E-KTA (`pdf/ekta.blade.php`) — Layout kartu anggota digital
- [x] Template E-Sertifikat (`pdf/sertifikat.blade.php`) — Layout sertifikat kegiatan
- [x] Template Laporan (`pdf/laporan.blade.php`) — Layout laporan umum (tabel data)

### 5.7 Konfigurasi & Verifikasi PWA

- [x] Pastikan PWA Manifest & Service Worker sudah terpasang (Implementasi Manual)
- [x] Konfigurasi `manifest.json` dan `sw.js` di direktori `public/`
- [x] Link manifest dan registrasi service worker di layout utama
- [x] Verifikasi splash screen dan ikon tampil (menggunakan UI-Avatars sebagai placeholder)
- [x] Verifikasi aplikasi berjalan dalam mode standalone (display: standalone)

---

## Fase 6: Testing & Quality Assurance

### 6.1 Testing Fungsional Manual

- [x] Test login Admin — berhasil masuk ke dashboard Admin (Fixed Undefined Variable)
- [x] Test login Kader — berhasil masuk ke dashboard Kader (Fixed Undefined Variable)
- [x] Test login gagal — pesan error muncul (Verified)
- [x] Test logout — sesi terhapus, redirect ke halaman utama (Verified)
- [x] Test pendaftaran — data tersimpan dengan status 'pending' (Verified)
- [x] Test validasi pendaftaran (setujui) — akun Kader terbuat (Pest: AdminFunctionalTest)
- [x] Test validasi pendaftaran (tolak) — status berubah 'ditolak' (Pest: AdminFunctionalTest)
- [x] Test CRUD anggota — (Verified Index)
- [x] Test CRUD kegiatan — (Verified Index)
- [x] Test presensi — data kehadiran tersimpan (Pest: AdminFunctionalTest)
- [x] Test upload arsip — file tersimpan di storage, metadata di database (Pest: AdminFunctionalTest)
- [x] Test download arsip — file terunduh (Pest: AdminFunctionalTest)
- [x] Test hapus arsip — file & record terhapus (Pest: AdminFunctionalTest)
- [x] Test update profil — data terupdate, validasi berjalan (Pest: KaderFunctionalTest)
- [x] Test preview E-KTA — data profil tampil di template (Pest: KaderFunctionalTest)
- [x] Test download E-KTA — file PDF terunduh (Pest: KaderFunctionalTest)
- [x] Test generate sertifikat — sertifikat terbuat untuk Kader terpilih (Pest: SertifikatLaporanTest)
- [x] Test download sertifikat (Kader) — file PDF terunduh (Pest: KaderFunctionalTest)
- [x] Test riwayat keaktifan — tabel & grafik tampil dengan data benar (Pest: KaderFunctionalTest)
- [x] Test export laporan PDF — file PDF terunduh (Pest: SertifikatLaporanTest)
- [x] Test export laporan Excel — file Excel terunduh (Pest: SertifikatLaporanTest)

### 6.2 Testing Responsivitas (Mobile-First)

- [ ] Test semua halaman di viewport mobile (375px - iPhone SE)
- [ ] Test semua halaman di viewport tablet (768px - iPad)
- [ ] Test semua halaman di viewport desktop (1024px+)
- [ ] Test navigasi mobile (hamburger menu, bottom nav)
- [ ] Test form input di mobile (touch-friendly, ukuran font)

### 6.3 Testing Keamanan

- [x] Test akses halaman Admin oleh Kader — harus ditolak (403) (Pest: SecurityTest)
- [x] Test akses halaman Kader oleh Admin — harus ditolak (403) (Pest: SecurityTest)
- [x] Test akses halaman protected tanpa login — redirect ke login (Pest: SecurityTest)
- [x] Test upload file dengan ekstensi tidak diizinkan — harus ditolak (Pest: SecurityTest)
- [x] Test CSRF protection — form tanpa token harus gagal (Pest: SecurityTest)

---

## Fase 7: Deployment & Finalisasi

### 7.1 Optimasi Production

- [ ] Set `.env`: `APP_ENV=production`, `APP_DEBUG=false`
- [ ] Jalankan `php artisan config:cache`
- [ ] Jalankan `php artisan route:cache`
- [ ] Jalankan `php artisan view:cache`
- [ ] Jalankan `npm run build` (production build)
- [ ] Jalankan `php artisan optimize`

### 7.2 Deployment

- [ ] Upload proyek ke server (VPS/Shared Hosting)
- [ ] Konfigurasi web server (Nginx/Apache) — document root ke folder `public/`
- [ ] Konfigurasi database production
- [ ] Jalankan migration di server: `php artisan migrate --force`
- [ ] Jalankan seeder (Admin default): `php artisan db:seed --class=UserSeeder --force`
- [ ] Buat symbolic link storage di server: `php artisan storage:link`
- [ ] Konfigurasi SSL (HTTPS) jika menggunakan domain publik
- [ ] Verifikasi semua fitur berjalan di environment production

### 7.3 Dokumentasi

- [ ] Dokumentasi panduan penggunaan sistem (user manual)
- [ ] Dokumentasi panduan instalasi (developer guide)
- [ ] Finalisasi dokumen SDD (requirements.md, design.md, tech.md, task.md)

---

## Ringkasan Estimasi Waktu (RAD Approach)

| Fase | Estimasi | Keterangan |
|------|----------|------------|
| Fase 1: Setup Proyek | 1 hari | Instalasi, konfigurasi, dependensi |
| Fase 2: Database | 1-2 hari | Migration, model, seeder |
| Fase 3: Middleware & Routing | 1 hari | Middleware role, definisi route |
| Fase 4: Backend Logic | 5-7 hari | Controller & validasi semua modul |
| Fase 5: Frontend UI | 5-7 hari | View Blade + Bootstrap, template PDF |
| Fase 6: Testing | 2-3 hari | Testing fungsional, responsivitas, keamanan |
| Fase 7: Deployment | 1-2 hari | Optimasi, deploy, dokumentasi |
| **Total** | **16-23 hari** | **±3-4 minggu kerja** |

---

*Checklist ini disusun berdasarkan prinsip RAD untuk memastikan implementasi berjalan cepat, iteratif, dan terstruktur.*

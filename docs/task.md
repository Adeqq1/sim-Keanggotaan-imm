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

- [ ] Migration tabel `users` — modifikasi migration bawaan, tambah kolom `role` (enum: admin, kader)
- [ ] Migration tabel `anggota` — FK ke `users.id`
  - Kolom: `user_id`, `nia`, `nama_lengkap`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `no_telp`, `foto_profil`, `status_aktif`
- [ ] Migration tabel `pendaftaran` — FK ke `users.id` (nullable)
  - Kolom: `user_id`, `nama_lengkap`, `email`, `tempat_lahir`, `tanggal_lahir`, `no_telp`, `alamat`, `tanggal_daftar`, `file_persyaratan`, `status_validasi`, `catatan_admin`
- [ ] Migration tabel `kegiatan`
  - Kolom: `nama_kegiatan`, `deskripsi`, `tanggal_waktu`, `lokasi`
- [x] Migration tabel `presensi` — FK ke `kegiatan.id` dan `anggota.id`
  - Kolom: `kegiatan_id`, `anggota_id`, `waktu_hadir`, `status_kehadiran`
  - Unique constraint: (`kegiatan_id`, `anggota_id`)
- [x] Migration tabel `sertifikat` — FK ke `kegiatan.id` dan `anggota.id`
  - Kolom: `kegiatan_id`, `anggota_id`, `nomor_sertifikat`, `file_sertifikat`
- [x] Migration tabel `arsip` — FK ke `anggota.id`
  - Kolom: `anggota_id`, `nomor_dokumen`, `judul_dokumen`, `kategori_arsip`, `file_arsip`, `tanggal_unggah`
- [ ] Jalankan semua migration: `php artisan migrate`

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
- [ ] Jalankan seeder: `php artisan db:seed`

---

## Fase 3: Middleware & Routing

### 3.1 Middleware

- [ ] Buat `RoleMiddleware`: `php artisan make:middleware RoleMiddleware`
- [ ] Implementasi logika pengecekan role (admin/kader) di middleware
- [ ] Daftarkan middleware di `bootstrap/app.php` (Laravel 11)

### 3.2 Routing

- [ ] Route publik:
  - `GET /` → Halaman utama
  - `GET /pendaftaran` → Form pendaftaran
  - `POST /pendaftaran` → Proses pendaftaran
- [ ] Route autentikasi (bawaan Breeze):
  - `GET /login`, `POST /login`, `POST /logout`
- [ ] Route Admin (grup middleware `auth` + `role:admin`):
  - `GET /admin/dashboard`
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
- [ ] Route Kader (grup middleware `auth` + `role:kader`):
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

- [ ] `LoginRequest` — validasi email & password (required)
- [ ] `PendaftaranRequest` — validasi Nama, TTL, Email, dsb. (required, format)
- [ ] `AnggotaRequest` — validasi data anggota (CRUD)
- [ ] `KegiatanRequest` — validasi data kegiatan
- [ ] `PresensiRequest` — validasi data presensi (array status kehadiran)
- [ ] `ArsipRequest` — validasi file upload (ekstensi, ukuran)
- [ ] `ProfilRequest` — validasi data profil
- [ ] `SertifikatRequest` — validasi data sertifikat (kegiatan, daftar anggota)
- [ ] `LaporanRequest` — validasi filter laporan (tanggal, jenis)

### 4.2 Controller — Autentikasi

- [ ] `AuthController@login` — Proses login, redirect berdasarkan role
- [ ] `AuthController@logout` — Proses logout, hapus sesi
- [ ] Kustomisasi redirect setelah login di `AuthenticatedSessionController` (Breeze)

### 4.3 Controller — Dashboard

- [ ] `DashboardController@adminDashboard` — Tampilkan statistik: total anggota, total kegiatan, pendaftar pending, dll.
- [ ] `DashboardController@kaderDashboard` — Tampilkan ringkasan: kegiatan terdekat, statistik kehadiran, sertifikat terbaru

### 4.4 Controller — Pendaftaran (Pengunjung)

- [ ] `PendaftaranController@create` — Tampilkan form pendaftaran
- [ ] `PendaftaranController@store` — Validasi & simpan data, set status 'pending', redirect ke halaman sukses

### 4.5 Controller — Validasi Pendaftaran (Admin)

- [ ] `ValidasiPendaftaranController@index` — Tampilkan daftar pendaftar (status: pending)
- [ ] `ValidasiPendaftaranController@show` — Tampilkan detail pendaftar
- [ ] `ValidasiPendaftaranController@approve` — Setujui: buat user + anggota, ubah status
- [ ] `ValidasiPendaftaranController@reject` — Tolak: ubah status menjadi 'ditolak'

### 4.6 Controller — Manajemen Data Anggota (Admin)

- [ ] `AnggotaController@index` — Tampilkan tabel anggota aktif
- [ ] `AnggotaController@create` — Form tambah anggota
- [ ] `AnggotaController@store` — Validasi & simpan anggota baru
- [ ] `AnggotaController@edit` — Form edit anggota
- [ ] `AnggotaController@update` — Validasi & update data anggota
- [ ] `AnggotaController@destroy` — Hapus data anggota

### 4.7 Controller — Manajemen Kegiatan & Presensi (Admin)

- [ ] `KegiatanController@index` — Tampilkan kalender/daftar kegiatan
- [ ] `KegiatanController@create` — Form tambah kegiatan
- [ ] `KegiatanController@store` — Simpan kegiatan baru
- [ ] `KegiatanController@edit` — Form edit kegiatan
- [ ] `KegiatanController@update` — Update kegiatan
- [ ] `KegiatanController@destroy` — Hapus kegiatan
- [ ] `PresensiController@create` — Tampilkan form presensi (daftar Kader + checkbox status)
- [ ] `PresensiController@store` — Batch simpan data presensi

### 4.8 Controller — Manajemen Arsip Dokumen

- [ ] `ArsipController@index` — Tampilkan daftar arsip (dengan filter kategori)
- [ ] `ArsipController@store` — Validasi file, simpan ke storage & database
- [ ] `ArsipController@download` — Stream download file arsip
- [ ] `ArsipController@destroy` — Hapus file dari storage & database

### 4.9 Controller — Manajemen Profil

- [ ] `ProfilController@show` — Tampilkan form profil (pre-filled)
- [ ] `ProfilController@update` — Validasi & update profil, termasuk upload foto profil

### 4.10 Controller — E-KTA (Kader)

- [ ] `EktaController@show` — Tampilkan preview E-KTA dengan data profil
- [ ] `EktaController@download` — Generate & download PDF E-KTA menggunakan DomPDF

### 4.11 Controller — E-Sertifikat

- [ ] `SertifikatController@index` (Admin) — Tampilkan daftar semua sertifikat
- [ ] `SertifikatController@create` (Admin) — Form buat sertifikat baru (pilih kegiatan + Kader)
- [ ] `SertifikatController@generate` (Admin) — Generate sertifikat untuk Kader terpilih, simpan PDF
- [ ] `SertifikatController@mySertifikat` (Kader) — Tampilkan daftar sertifikat milik Kader
- [ ] `SertifikatController@download` (Kader) — Download file PDF sertifikat

### 4.12 Controller — Riwayat Keaktifan (Kader)

- [ ] `RiwayatKeaktifanController@index` — Query presensi, hitung statistik, tampilkan tabel & data grafik

### 4.13 Controller — Laporan (Admin)

- [ ] `LaporanController@index` — Tampilkan halaman filter laporan
- [ ] `LaporanController@exportPdf` — Generate & download laporan PDF
- [ ] `LaporanController@exportExcel` — Generate & download laporan Excel

---

## Fase 5: Frontend UI (View Blade + Bootstrap 5 — Mobile Web App)

### 5.1 Layout Utama & Navigasi Mobile

- [ ] Layout utama `layouts/app.blade.php`:
  - [ ] Gunakan viewport meta tag `<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">`
  - [ ] Tambahkan tag PWA: `@laravelPWA` di `<head>`
  - [ ] Struktur body: header kecil (judul halaman) + konten utama + Bottom Navigation Bar
  - [ ] Tambahkan `padding-bottom: 70px` pada konten utama agar tidak tertutup Bottom Nav
  - [ ] **Tidak menggunakan class breakpoint** (`-md`, `-lg`, `-xl`) — layout murni mobile
- [ ] Komponen `components/bottom-nav.blade.php`:
  - [ ] Gunakan `<nav class="navbar fixed-bottom bg-white shadow-lg border-top">`
  - [ ] Isi dengan 4-5 menu utama berupa ikon (Bootstrap Icons) + label kecil
  - [ ] Menu Admin: Dashboard, Anggota, Kegiatan, Arsip, Lainnya
  - [ ] Menu Kader: Dashboard, E-KTA, Sertifikat, Riwayat, Lainnya
  - [ ] Tambahkan highlight/active state pada menu yang sedang dibuka
  - [ ] Semua tombol berukuran minimal 44x44px (touch-friendly)
- [ ] Layout autentikasi `layouts/auth.blade.php` — Layout khusus halaman login (tanpa Bottom Nav)
- [ ] Komponen alert/flash message (`_alert.blade.php`)
- [ ] Komponen modal konfirmasi hapus (`_modal-delete.blade.php`)
- [ ] Komponen pagination

### 5.2 Halaman Publik

- [ ] Halaman utama (`/`) — Landing page mobile-style dengan tombol "Daftar Sekarang" dan "Login"
- [ ] Halaman pendaftaran — Form registrasi full-width, validasi client-side
- [ ] Halaman sukses pendaftaran — Pesan konfirmasi

### 5.3 Halaman Autentikasi

- [ ] Halaman login — Form email & password, centered, full-width input, pesan error

### 5.4 Halaman Admin

- [ ] Dashboard Admin — Card statistik (total anggota, kegiatan, pendaftar pending), grafik ringkasan
- [ ] Halaman Validasi Pendaftaran — List card pendaftar, tombol Setujui/Tolak, modal detail
- [ ] Halaman Data Anggota — List card/tabel CRUD, tombol Tambah/Edit/Hapus, modal form
- [ ] Halaman Kegiatan — List card kegiatan, form CRUD
- [ ] Halaman Presensi — Form presensi dengan daftar Kader dan checkbox (Hadir/Izin/Alfa)
- [ ] Halaman Arsip Dokumen — List arsip, tombol Unggah/Unduh/Hapus, form upload file
- [ ] Halaman Manajemen E-Sertifikat — List sertifikat, form generate baru (multi-select Kader)
- [ ] Halaman Laporan — Form filter (rentang tanggal, jenis laporan), tombol Cetak PDF / Export Excel
- [ ] Halaman Profil Admin — Form profil pre-filled, full-width

### 5.5 Halaman Kader

- [ ] Dashboard Kader — Card ringkasan kegiatan, statistik kehadiran, notifikasi sertifikat baru
- [ ] Halaman Profil — Form profil pre-filled, upload foto
- [ ] Halaman E-KTA — Preview kartu anggota digital, tombol Cetak/Unduh PDF
- [ ] Halaman E-Sertifikat Saya — Daftar sertifikat dengan tombol Unduh
- [ ] Halaman Riwayat Keaktifan — Tabel riwayat presensi + grafik Chart.js
- [ ] Halaman Arsip Dokumen (Kader) — List arsip, fitur unggah/unduh

### 5.6 Template PDF

- [ ] Template E-KTA (`pdf/ekta.blade.php`) — Layout kartu anggota digital
- [ ] Template E-Sertifikat (`pdf/sertifikat.blade.php`) — Layout sertifikat kegiatan
- [ ] Template Laporan (`pdf/laporan.blade.php`) — Layout laporan umum (tabel data)

### 5.7 Konfigurasi & Verifikasi PWA

- [ ] Pastikan `@laravelPWA` directive sudah ada di `<head>` layout utama
- [ ] Test "Add to Home Screen" di Chrome Android (DevTools → Application → Manifest)
- [ ] Verifikasi splash screen dan ikon tampil dengan benar saat dibuka dari Homescreen
- [ ] Verifikasi aplikasi berjalan dalam mode standalone (tanpa address bar browser)

---

## Fase 6: Testing & Quality Assurance

### 6.1 Testing Fungsional Manual

- [ ] Test login Admin — berhasil masuk ke dashboard Admin
- [ ] Test login Kader — berhasil masuk ke dashboard Kader
- [ ] Test login gagal — pesan error muncul
- [ ] Test logout — sesi terhapus, redirect ke halaman utama
- [ ] Test pendaftaran — data tersimpan dengan status 'pending'
- [ ] Test validasi pendaftaran (setujui) — akun Kader terbuat
- [ ] Test validasi pendaftaran (tolak) — status berubah 'ditolak'
- [ ] Test CRUD anggota — tambah, edit, hapus berhasil
- [ ] Test CRUD kegiatan — tambah, edit, hapus berhasil
- [ ] Test presensi — data kehadiran tersimpan
- [ ] Test upload arsip — file tersimpan di storage, metadata di database
- [ ] Test download arsip — file terunduh
- [ ] Test hapus arsip — file & record terhapus
- [ ] Test update profil — data terupdate, validasi berjalan
- [ ] Test preview E-KTA — data profil tampil di template
- [ ] Test download E-KTA — file PDF terunduh
- [ ] Test generate sertifikat — sertifikat terbuat untuk Kader terpilih
- [ ] Test download sertifikat (Kader) — file PDF terunduh
- [ ] Test riwayat keaktifan — tabel & grafik tampil dengan data benar
- [ ] Test export laporan PDF — file PDF terunduh
- [ ] Test export laporan Excel — file Excel terunduh

### 6.2 Testing Responsivitas (Mobile-First)

- [ ] Test semua halaman di viewport mobile (375px - iPhone SE)
- [ ] Test semua halaman di viewport tablet (768px - iPad)
- [ ] Test semua halaman di viewport desktop (1024px+)
- [ ] Test navigasi mobile (hamburger menu, bottom nav)
- [ ] Test form input di mobile (touch-friendly, ukuran font)

### 6.3 Testing Keamanan

- [ ] Test akses halaman Admin oleh Kader — harus ditolak (403)
- [ ] Test akses halaman Kader oleh Admin — harus ditolak (403)
- [ ] Test akses halaman protected tanpa login — redirect ke login
- [ ] Test upload file dengan ekstensi tidak diizinkan — harus ditolak
- [ ] Test CSRF protection — form tanpa token harus gagal

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

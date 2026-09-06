# Dokumentasi Fitur Fungsional Website SIM Keanggotaan IMM

> **Baru di Laravel / kerja database?** Mulai dari dokumen dasar pemrograman:
> - [support-for-developer/README.md](./README.md)
> - [basics/00-laravel-map.md](./basics/00-laravel-map.md)
> - [basics/01-database-tables-migrations.md](./basics/01-database-tables-migrations.md)
> - [basics/02-models-and-relations.md](./basics/02-models-and-relations.md)
> - [basics/03-routes-controllers-views.md](./basics/03-routes-controllers-views.md)
> - [basics/04-create-new-feature-checklist.md](./basics/04-create-new-feature-checklist.md)
> - [basics/05-seeder-dan-jalankan-script.md](./basics/05-seeder-dan-jalankan-script.md)

---

Dokumentasi ini dibuat sebagai pedoman kerja untuk programmer dan AI agent saat mengubah, memperbaiki, atau mengembangkan fitur di aplikasi SIM Keanggotaan IMM. Fokus dokumen ini adalah **fitur fungsional yang benar-benar ada di source code**, bukan asumsi.

## 1. Gambaran Singkat Aplikasi

Aplikasi ini adalah Sistem Informasi Manajemen (SIM) Keanggotaan Ikatan Mahasiswa Muhammadiyah (IMM) berbasis Laravel 13 (PHP 8.4) dengan 4 aktor utama:

- **Guest**: pengunjung umum yang dapat melihat landing page, detail agenda kegiatan, dan mengisi formulir pendaftaran calon anggota.
- **Admin**: pengelola utama sistem (verifikasi pendaftaran, penerbitan NIA, generate sertifikat, manajemen arsip, dan ekspor laporan organisasi).
- **Instruktur**: pemandu/fasilitator perkaderan (pengelolaan sesi kegiatan, pengisian presensi, verifikasi kehadiran, penilaian kelulusan kader, dan unggah materi kegiatan).
- **Kader**: anggota aktif yang sudah memiliki akun login (akses E-KTA digital, materi perkaderan, unduh sertifikat resmi, riwayat keaktifan, dan arsip dokumen mandiri).

Referensi role:
- `app/Enums/RoleEnum.php`
- `app/Http/Middleware/RoleMiddleware.php`

### Area Utama Aplikasi

- **Public Area**: landing page (`/`), detail kegiatan (`/kegiatan/{kegiatan}`), dan pendaftaran anggota (`/pendaftaran`).
- **Admin Area**: dashboard admin (`/admin/dashboard`), validasi pendaftaran, anggota & NIA, kegiatan, presensi, sertifikat antrean, arsip, dan laporan.
- **Instruktur Area**: menggunakan prefix route `/admin` bersama admin, namun hak aksesnya dibatasi khusus untuk modul kegiatan, sesi pertemuan, presensi & verifikasi kehadiran, penilaian kader, dan materi kegiatan.
- **Kader Area**: dashboard kader (`/kader/dashboard`), E-KTA digital, riwayat keaktifan, materi perkaderan, e-sertifikat, dan arsip dokumen pribadi.

Referensi route utama:
- `routes/web.php`
- `routes/auth.php`

---

## 2. Arsitektur Akses dan Alur Login

### 2.1 Role dan Pembatasan Akses

Sistem memakai middleware `role` yang memeriksa nilai `auth()->user()->role`:
- Jika role tidak sesuai, request dihentikan dengan status HTTP `403 (Forbidden)`.
- Otorisasi halaman presensi: Admin dapat melihat presensi dalam mode *read-only*, sedangkan pembuatan/pengisian presensi hanya diizinkan untuk role `instruktur`.

Referensi:
- `app/Http/Middleware/RoleMiddleware.php`

### 2.2 Login dan Redirect Per Role

Setelah berhasil login, user diarahkan secara dinamis melalui helper `User::getDashboardRoute()`:
- **admin** -> `admin.dashboard` (`/admin/dashboard`)
- **instruktur** -> `admin.kegiatan.index` (`/admin/kegiatan`)
- **kader** atau default -> `kader.dashboard` (`/kader/dashboard`)

Referensi:
- `app/Models/User.php`
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`

### 2.3 Registrasi, Password Reset, dan Profil

Fitur auth meliputi:
- Form pendaftaran mandiri calon anggota (`/pendaftaran`)
- Login dan Logout (`/login`, `/logout`)
- Lupa password dan reset token via email (`/forgot-password`, `/reset-password`)
- Manajemen profil user dan biodata anggota (`/profile`)

---

## 3. Modul Public / Guest

### 3.1 Landing Page
- **Route**: `GET /` (`landing`)
- **Controller**: `LandingController@index`
- **Perilaku Utama**:
  - Menampilkan **3 kegiatan terbaru**.
  - Cache kegiatan terbaru disimpan dengan key `kegiatan.terbaru` selama 1 jam (`Cache::remember`).
  - Setiap operasi pembuatan, pengubahan, atau penghapusan kegiatan di `KegiatanController` wajib menghapus cache ini (`Cache::forget('kegiatan.terbaru')`).
  - Konten profil, pilar, statistik, dan kontak organisasi dikonfigurasi di `config/landing.php`.

### 3.2 Detail Kegiatan Publik
- **Route**: `GET /kegiatan/{kegiatan}` (`kegiatan.show`)
- **Controller**: `LandingController@show`
- **Perilaku Utama**:
  - Menampilkan informasi lengkap kegiatan (judul, deskripsi, lokasi, waktu pelaksanaan, banner).
  - Menyajikan rekomendasi 3 kegiatan lainnya selain kegiatan yang sedang dibuka.

### 3.3 Pendaftaran Anggota Publik
- **Route**:
  - `GET /pendaftaran` (`pendaftaran`)
  - `POST /pendaftaran` (`pendaftaran.store`) — dilindungi rate limiting `throttle:pendaftaran`
  - `GET /pendaftaran/sukses` (`pendaftaran.success`)
- **Controller**: `PendaftaranController`
- **Validasi**: `PendaftaranRequest`
  - Nama, email, nomor HP, asal komisariat, tahun daftar wajib diisi.
  - Pilihan role yang dituju (`kader` atau `instruktur`).
  - Foto profil (`jpg/jpeg/png`, max 2MB) disimpan di public disk `storage/app/public/foto_profil`.
  - Dokumen identitas (KTP/KTM, format `pdf/jpg/jpeg/png`, max 2MB) disimpan di private disk `storage/app/private/pendaftaran`.
- **Status Awal**: `status_validasi = 'pending'`, `tanggal_daftar = now()`. Password calon kader di-hash dengan aman.

---

## 4. Modul Admin

### 4.1 Dashboard Admin
- **Route**: `GET /admin/dashboard` (`admin.dashboard`)
- **Controller**: `DashboardController@adminDashboard`
- **Metrik Utama**: Total anggota aktif, total kegiatan, total pendaftaran pending, total arsip, dan persentase rata-rata kehadiran. Menampilkan juga 5 kegiatan mendatang.

### 4.2 Validasi & Approval Pendaftaran
- **Route**:
  - `GET /admin/pendaftaran` (`admin.pendaftaran.index`)
  - `GET /admin/pendaftaran/{id}` (`admin.pendaftaran.show`)
  - `GET /admin/pendaftaran/{pendaftaran}/dokumen-identitas` (`admin.pendaftaran.document.download`)
  - `GET /admin/pendaftaran/{pendaftaran}/dokumen-identitas/preview` (`admin.pendaftaran.document.preview`)
  - `POST /admin/pendaftaran/{id}/validate` (`admin.pendaftaran.validate`)
- **Controller**: `ValidasiPendaftaranController`
- **Perilaku Keamanan & Preview**:
  - Dokumen identitas disajikan langsung dari disk private dengan header keamanan (`no-cache`, `nosniff`, dan pembatasan MIME type).
- **Alur Transaksional Approval (`disetujui`)**:
  - Menggunakan `DB::transaction()` dan `lockForUpdate()` pada record pendaftaran untuk mencegah duplikasi atau race condition.
  - Membuat akun login `User` (dengan role sesuai formulir/pilihan admin).
  - Membuat profil `Anggota` yang terhubung langsung dengan `user_id`.
  - Memperbarui status pendaftaran menjadi `disetujui`.
  - Jika terjadi error pada salah satu tahap, seluruh operasi di-rollback otomatis.
- **Alur Penolakan (`ditolak`)**:
  - Memperbarui `status_validasi = 'ditolak'` beserta `catatan_admin`. Dokumen privat pendaftar yang ditolak dibersihkan secara aman.

### 4.3 Manajemen Anggota & Penomoran NIA
- **Route**:
  - `GET /admin/anggota` (`admin.anggota.index`)
  - `GET /admin/anggota/create`, `POST /admin/anggota` (`admin.anggota.create`, `admin.anggota.store`)
  - `GET /admin/anggota/{anggota}`, `GET /admin/anggota/{anggota}/edit`, `PUT/PATCH /admin/anggota/{anggota}`
  - `DELETE /admin/anggota/{anggota}` (`admin.anggota.destroy`)
  - `POST /admin/anggota/{anggota}/generate-nia` (`admin.anggota.generate-nia`)
  - `POST /admin/anggota/generate-nia-bulk` (`admin.anggota.generate-nia-bulk`)
- **Controller**: `AnggotaController`
- **Fitur Utama**:
  - **Generator NIA Otomatis**: Menghasilkan Nomor Induk Anggota unik dengan format standar cabang (satuan maupun massal/bulk).
  - **Optimasi Foto WebP**: Upload foto profil otomatis dikonversi ke format WebP terkompresi menggunakan Intervention Image v3.
  - **Manajemen Role**: Admin dapat mengubah role user antara `kader` dan `instruktur`, dengan proteksi admin tidak dapat mendemosi dirinya sendiri.

### 4.4 Manajemen Kegiatan & Sesi Pertemuan
- **Route**:
  - Resource `admin/kegiatan` (`KegiatanController`)
  - Sesi Kegiatan:
    - `GET /admin/kegiatan/{kegiatan}/sesi` (`admin.kegiatan.sesi.index`)
    - `POST /admin/kegiatan/{kegiatan}/sesi` (`admin.kegiatan.sesi.store`)
    - `PATCH /admin/kegiatan/{kegiatan}/sesi/{sesiKegiatan}` (`admin.kegiatan.sesi.update`)
    - `DELETE /admin/kegiatan/{kegiatan}/sesi/{sesiKegiatan}` (`admin.kegiatan.sesi.destroy`)
- **Perilaku Utama**:
  - Kegiatan dapat memiliki satu sesi tunggal atau banyak sesi (`SesiKegiatan`).
  - Menggunakan Route Model Binding dengan scope bindings (`scopeBindings()`).
  - Setiap perubahan kegiatan menginvalidasi cache `kegiatan.terbaru`.

### 4.5 Presensi, Verifikasi Kehadiran & Penilaian
- **Route**:
  - `GET /admin/presensi` (`admin.presensi.index`)
  - `GET /admin/presensi/{kegiatan}` (`admin.presensi.show`)
  - `GET /admin/presensi/{kegiatan}/sesi/{sesiKegiatan}` (`admin.presensi.sesi.show`)
  - `POST /admin/presensi/{kegiatan}/{sesiKegiatan?}` (`admin.presensi.store`) — *Instruktur Only*
  - `PATCH /admin/presensi/{kegiatan}/sesi/{sesiKegiatan}/{presensi}/verifikasi` (`admin.presensi.verifikasi.update`) — *Instruktur Only*
  - `GET /admin/kegiatan/{kegiatan}/penilaian` (`admin.kegiatan.penilaian.index`) — *Admin & Instruktur*
  - `PUT /admin/kegiatan/{kegiatan}/penilaian/{anggota}` (`admin.kegiatan.penilaian.update`) — *Instruktur Only*
- **Controller**: `PresensiController`, `PenilaianKegiatanController`
- **Perilaku**:
  - Presensi dicatat dengan `updateOrCreate` pada kombinasi kegiatan/sesi dan anggota.
  - Instruktur memverifikasi kehadiran (`status_verifikasi = 'terverifikasi'`).
  - Untuk kegiatan multi-sesi, instruktur memasukkan penilaian mutu kelulusan (A, B, C, D) melalui `PenilaianKegiatanController`.

### 4.6 Penerbitan E-Sertifikat (Background Job)
- **Route**:
  - `GET /admin/sertifikat` (`admin.sertifikat.index`)
  - `GET /admin/sertifikat/create` (`admin.sertifikat.create`)
  - `POST /admin/sertifikat/generate` (`admin.sertifikat.generate`)
  - `GET /admin/sertifikat/generation/{batchId}` (`admin.sertifikat.generation.status`)
  - `GET /admin/sertifikat/settings` (`admin.sertifikat.settings`)
  - `POST /admin/sertifikat/settings` (`admin.sertifikat.settings.update`)
  - `GET /admin/sertifikat/{sertifikat}/download` (`admin.sertifikat.download`)
- **Controller**: `SertifikatController`
- **Mekanisme Kelayakan & Antrean**:
  - `CertificateEligibility` & `VerifiedAttendance` mengevaluasi apakah kader memenuhi syarat:
    1. Status anggota aktif dan berperan sebagai kader.
    2. Kehadiran terverifikasi (`hadir` + `terverifikasi`).
    3. Untuk kegiatan multi-sesi: memenuhi jumlah minimal sesi terverifikasi dan lulus penilaian (grade A–D).
    4. Belum pernah diterbitkan sertifikat untuk kegiatan yang sama.
  - Sertifikat di-generate secara asinkron via `GenerateCertificateJob` menggunakan batching antrean.
  - File PDF sertifikat disimpan di `storage/app/public/sertifikat`.
  - Pengaturan template background disimpan di `storage/app/private/sertifikat_settings.json` dan aset gambar di `public/images/sertificate-asset/bg-sertificate.jpg`.
  - *Catatan*: Fitur pengajuan/klaim mandiri oleh kader telah dinonaktifkan; sertifikat diterbitkan secara terpusat oleh Admin/sistem.

### 4.7 Laporan Organisasi & Berita Acara Kegiatan
- **Route**:
  - Rekapitulasi Umum: `GET /admin/laporan`, `POST /admin/laporan/export-pdf`, `POST /admin/laporan/export-excel` (`LaporanController`)
  - Laporan Kegiatan (Berita Acara):
    - `GET /admin/laporan-kegiatan` (`admin.laporan-kegiatan.index`)
    - `GET /admin/kegiatan/{kegiatan}/laporan-kegiatan/create`
    - `POST /admin/kegiatan/{kegiatan}/laporan-kegiatan`
    - `GET /admin/laporan-kegiatan/{laporanKegiatan}`
    - `GET /admin/laporan-kegiatan/{laporanKegiatan}/edit`, `PUT/PATCH /admin/laporan-kegiatan/{laporanKegiatan}`
    - `DELETE /admin/laporan-kegiatan/{laporanKegiatan}`
    - `GET /admin/laporan-kegiatan/{laporanKegiatan}/lampiran`
    - `GET /admin/laporan-kegiatan/{laporanKegiatan}/download` (`LaporanKegiatanController@downloadPdf`)
- **Perilaku**:
  - Admin membuat berita acara kegiatan beserta unggahan berkas lampiran.
  - Instruktur dapat mengunduh berkas laporan dan rekap presensi dalam format PDF resmi.

### 4.8 Ringkasan Materi Kegiatan
- **Route**: `GET /admin/materi-kegiatan` (`admin.materi-kegiatan.index`)
- **Controller**: `MateriKegiatanController@adminIndex`
- **Perilaku**: Menyajikan rekapitulasi seluruh modul/materi perkaderan yang telah diunggah oleh para instruktur.

### 4.9 Manajemen Arsip
- **Route**:
  - `GET /admin/arsip` (`admin.arsip.index`)
  - `DELETE /admin/arsip/{arsip}` (`admin.arsip.destroy`)
  - `GET /admin/arsip/{arsip}/download` (`admin.arsip.download`)
- **Controller**: `ArsipController`
- **Penyimpanan**: Berkas arsip disimpan pada disk privat di `storage/app/private/arsip` dan dilayani dengan otorisasi controller.

---

## 5. Modul Instruktur

Instruktur mengelola aspek teknis perkaderan di lapangan melalui route ber-prefix `/admin`:
- **Kegiatan & Sesi**: Membuat/mengubah agenda dan mendefinisikan sesi pertemuan.
- **Pencatatan Presensi**: Mengisi absensi peserta (`Hadir`, `Izin`, `Alfa`).
- **Verifikasi Kehadiran**: Memvalidasi presensi kader per sesi.
- **Penilaian Perkaderan**: Memberikan nilai A–D pada peserta kegiatan.
- **Materi Kegiatan**:
  - `Resource admin/kegiatan/{kegiatan}/materi-kegiatan` (`MateriKegiatanController`)
  - Mengunggah slide, dokumen PDF, dan modul materi perkaderan (tersimpan di `storage/app/private/materi_kegiatan`).
- **Laporan Kegiatan**: Mengunduh rekap presensi dan dokumen berita acara kegiatan dalam bentuk PDF.

---

## 6. Modul Kader

Prefix route: `/kader` (dilindungi middleware `auth` dan `role:kader`).

### 6.1 Dashboard Kader
- **Route**: `GET /kader/dashboard` (`kader.dashboard`)
- **Konten**: Informasi profil kader, statistik total kegiatan yang dihadiri, sertifikat yang dimiliki, dan daftar agenda kegiatan mendatang.

### 6.2 E-KTA Digital
- **Route**: `GET /kader/ekta` (`kader.ekta`), `GET /kader/ekta/download` (`kader.ekta.download`)
- **Controller**: `EktaController`
- **Fitur**:
  - Tampilan kartu digital interaktif dengan efek 3D Flip (sisi depan dan belakang).
  - Dilengkapi QR Code verifikasi digital dan motto Tri Kompetensi IMM.
  - Fitur cetak langsung browser dan unduh file PDF 2 halaman standar kartu identitas CR80.

### 6.3 E-Sertifikat Saya
- **Route**: `GET /kader/sertifikat` (`kader.sertifikat.index`), `GET /kader/sertifikat/{sertifikat}/download` (`kader.sertifikat.download`)
- **Controller**: `SertifikatController`
- **Perilaku**: Kader dapat melihat seluruh sertifikat resmi yang telah diterbitkan untuknya dan mengunduh berkas PDF beresolusi tinggi.

### 6.4 Riwayat Keaktifan
- **Route**: `GET /kader/riwayat` (`kader.riwayat.index`)
- **Controller**: `RiwayatKeaktifanController`
- **Konten**: Riwayat lengkap partisipasi kegiatan, status kehadiran terverifikasi, dan grafik persentase keaktifan.

### 6.5 E-Arsip Mandiri
- **Route**: `GET /kader/arsip`, `GET /kader/arsip/create`, `POST /kader/arsip`, `GET /kader/arsip/{arsip}/download`
- **Controller**: `ArsipController`
- **Penyimpanan**: Berkas diunggah ke private disk `storage/app/private/arsip` dan hanya dapat diunduh oleh pemilik akun yang bersangkutan atau admin.

### 6.6 Materi Perkaderan
- **Route**:
  - `GET /kader/materi` (`kader.materi.index`)
  - `GET /kader/materi/tersimpan` (`kader.materi.saved.index`)
  - `POST /kader/materi/{materi_kegiatan}/simpan` (`kader.materi.save`)
  - `GET /kader/materi/{materi_kegiatan}/unduh` (`kader.materi.download`)
- **Controller**: `MateriKegiatanController`
- **Aturan Otorisasi**: Kader hanya dapat melihat, menyimpan bookmark, dan mengunduh materi jika memiliki presensi `hadir` pada kegiatan yang bersangkutan.

### 6.7 Profil User
- **Route**: `GET /profile`, `PATCH /profile`, `DELETE /profile` (`ProfileController`)
- **Fitur**: Memperbarui nama, email, password, biodata anggota, serta upload foto profil dengan konversi otomatis ke WebP.

---

## 7. Sistem Pendukung Lintas Modul

### 7.1 Queue & Background Job
- **Job**: `GenerateCertificateJob`
- **Driver**: Database Queue Driver
- **Penggunaan**: Pemrosesan massal pembuatan sertifikat PDF tanpa membebani thread HTTP request utama.

### 7.2 Isolasi Penyimpanan Berkas (Storage Isolation)
- **Public Disk** (`storage/app/public`):
  - `foto_profil/`: Foto anggota dalam format `.webp`
  - `kegiatan_thumbnails/`: Gambar banner kegiatan
  - `sertifikat/`: Berkas PDF sertifikat yang diterbitkan
- **Private Disk** (`storage/app/private`):
  - `pendaftaran/`: Berkas KTP/KTM pendaftar
  - `arsip/`: Dokumen arsip kader & organisasi
  - `materi_kegiatan/`: Berkas materi/modul perkaderan
  - `laporan_kegiatan/`: Lampiran berita acara kegiatan
  - `sertifikat_settings.json`: File konfigurasi template background sertifikat
- **Command Generator Data Dummy**: `php artisan demo:seed-files` menghasilkan file dummy valid untuk pengujian lokal.

### 7.3 Dokumen & Gambar
- **PDF Generation**: `barryvdh/laravel-dompdf`
- **Excel Export**: `maatwebsite/excel`
- **Image Processing**: `intervention/image` v3 (GD Driver)

---

## 8. Peta 12 Model Eloquent & Relasi

1. **`User`**: Akun login (`admin`, `kader`, `instruktur`). Relasi: `hasOne(Anggota)`, `hasOne(Pendaftaran)`.
2. **`Anggota`**: Profil biodata kader/instruktur. Relasi: `belongsTo(User)`, `hasMany(Presensi)`, `hasMany(Sertifikat)`, `hasMany(Arsip)`, `hasMany(PenilaianKegiatan)`.
3. **`Pendaftaran`**: Formulir pendaftaran calon anggota. Relasi: `belongsTo(User)`.
4. **`Kegiatan`**: Data agenda organisasi/perkaderan. Relasi: `hasMany(SesiKegiatan)`, `hasMany(Presensi)`, `hasMany(MateriKegiatan)`, `hasMany(Sertifikat)`, `hasOne(LaporanKegiatan)`, `hasMany(PenilaianKegiatan)`.
5. **`SesiKegiatan`**: Sesi pertemuan per kegiatan. Relasi: `belongsTo(Kegiatan)`, `hasMany(Presensi)`.
6. **`Presensi`**: Catatan absensi per kegiatan/sesi & anggota. Relasi: `belongsTo(Kegiatan)`, `belongsTo(SesiKegiatan)`, `belongsTo(Anggota)`.
7. **`PenilaianKegiatan`**: Nilai evaluasi perkaderan (A–D). Relasi: `belongsTo(Kegiatan)`, `belongsTo(Anggota)`.
8. **`MateriKegiatan`**: Berkas materi/slide perkaderan. Relasi: `belongsTo(Kegiatan)`.
9. **`LaporanKegiatan`**: Berita acara & laporan pelaksanaan kegiatan. Relasi: `belongsTo(Kegiatan)`.
10. **`Sertifikat`**: Data sertifikat terbit. Relasi: `belongsTo(Kegiatan)`, `belongsTo(Anggota)`.
11. **`Arsip`**: Berkas dokumen digital. Relasi: `belongsTo(Anggota)`.
12. **`KegiatanTahunAngkatan`**: Data referensi angkatan/tahun kaderisasi.

---

## 9. Panduan Menjalankan Pengujian (Testing)

Semua feature dan unit test dijalankan di dalam container Docker menggunakan Pest PHP:

```bash
# Menjalankan seluruh test suite (85 tests)
docker compose exec app php artisan test

# Menjalankan grup feature test tertentu
docker compose exec app php artisan test tests/Feature/PresensiVerificationTest.php
docker compose exec app php artisan test tests/Feature/PenilaianKegiatanTest.php
docker compose exec app php artisan test tests/Feature/LaporanKegiatanTest.php
docker compose exec app php artisan test tests/Feature/MateriKegiatanTest.php
docker compose exec app php artisan test tests/Feature/NiaGeneratorTest.php
```

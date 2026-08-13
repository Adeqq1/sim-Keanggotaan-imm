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

Dokumentasi ini dibuat sebagai pedoman kerja untuk programmer junior atau model AI yang lebih murah saat mengubah, memperbaiki, atau mengembangkan fitur di aplikasi ini. Fokus dokumen ini adalah **fitur fungsional yang benar-benar ada di source code**, bukan asumsi.

## 1. Gambaran singkat aplikasi

Aplikasi ini adalah sistem informasi keanggotaan IMM berbasis Laravel dengan 4 aktor utama:

- **Guest**: pengunjung umum yang bisa melihat landing page, detail kegiatan, dan mengisi pendaftaran.
- **Admin**: pengelola utama sistem.
- **Instruktur**: pengelola kegiatan dan presensi.
- **Kader**: anggota yang sudah punya akun login.

Referensi role:
- `app/Enums/RoleEnum.php:5`
- `app/Http/Middleware/RoleMiddleware.php:16`

### Area utama aplikasi

- **Public area**: landing page, detail kegiatan, pendaftaran anggota.
- **Admin area**: dashboard admin, validasi pendaftaran, anggota, kegiatan, presensi, sertifikat, arsip, laporan.
- **Instruktur area**: memakai prefix route `/admin`, tetapi hak aksesnya hanya untuk modul kegiatan dan presensi.
- **Kader area**: dashboard kader, E-KTA, riwayat keaktifan, arsip, dan sertifikat pribadi.

Referensi route utama:
- `routes/web.php:18`
- `routes/web.php:26`
- `routes/web.php:58`
- `routes/web.php:74`

---

## 2. Arsitektur akses dan alur login

### 2.1 Role dan pembatasan akses

Sistem memakai middleware `role` sederhana yang hanya mengecek `user()->role` ada di daftar role yang diizinkan.

- Jika role tidak sesuai, request akan di-`403`.
- Tidak ada policy/gate kompleks di area ini; banyak proteksi dilakukan langsung di middleware atau controller.

Referensi:
- `app/Http/Middleware/RoleMiddleware.php:16`

### 2.2 Login dan redirect per role

Setelah login, user diarahkan berdasarkan role:

- **admin** -> `admin.dashboard`
- **instruktur** -> `admin.kegiatan.index`
- **kader** atau fallback default -> `kader.dashboard`

Ini penting saat mengubah alur auth, route name, atau dashboard.

Referensi:
- `app/Models/User.php:48`
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php:25`

### 2.3 Register, reset password, verifikasi email

Fitur auth bawaan Breeze masih ada:

- login
- register
- lupa password
- reset password
- verifikasi email
- ubah password
- logout

Referensi:
- `routes/auth.php:14`
- `routes/auth.php:38`
- `routes/auth.php:55`

Catatan penting:
- Route verifikasi email ada, tetapi perilaku wajib-verifikasi perlu dicek lagi jika nanti ingin dijadikan keharusan bisnis.

---

## 3. Modul Public / Guest

## 3.1 Landing page

### Tujuan fitur
Landing page berfungsi sebagai halaman publik utama website IMM. Halaman ini menampilkan branding organisasi, CTA pendaftaran, dan daftar kegiatan terbaru.

### Route
- `GET /` -> `landing`

Referensi:
- `routes/web.php:18`
- `app/Http/Controllers/LandingController.php:14`

### Perilaku utama
- Menampilkan **3 kegiatan terbaru**.
- Data kegiatan terbaru disimpan ke cache dengan key `kegiatan.terbaru` selama 1 jam.
- Jika isi cache rusak / bukan array, controller akan reset cache dan ambil ulang dari database.

Referensi:
- `app/Http/Controllers/LandingController.php:16`
- `app/Http/Controllers/LandingController.php:20`
- `app/Http/Controllers/LandingController.php:26`

### Sumber data penting
Sebagian konten landing page tidak ditulis langsung di Blade, tetapi di file config:
- statistik
- program kerja
- pilar
- misi
- kontak
- social links

Referensi:
- `config/landing.php:3`
- `config/landing.php:17`
- `config/landing.php:92`

### Hal yang perlu diingat saat mengubah fitur
- Jika mengubah teks landing, cek dulu apakah datanya berasal dari `config/landing.php`.
- Jika mengubah logika CRUD kegiatan, **jangan hilangkan invalidasi cache** `kegiatan.terbaru`.

### Test terkait
- `tests/Feature/LandingPageTest.php:10`
- `tests/Feature/LandingPageTest.php:71`

---

## 3.2 Detail kegiatan publik

### Tujuan fitur
Menampilkan detail satu kegiatan untuk publik.

### Route
- `GET /kegiatan/{kegiatan}` -> `kegiatan.show`

Referensi:
- `routes/web.php:19`
- `app/Http/Controllers/LandingController.php:34`

### Perilaku utama
- Menampilkan 1 kegiatan berdasarkan route model binding.
- Menampilkan **3 rekomendasi kegiatan lain** selain kegiatan yang sedang dibuka.

Referensi:
- `app/Http/Controllers/LandingController.php:36`

### Test terkait
- `tests/Feature/LandingPageTest.php:45`

---

## 3.3 Pendaftaran anggota publik

### Tujuan fitur
Calon anggota bisa mengisi formulir pendaftaran dari website.

### Route
- `GET /pendaftaran`
- `POST /pendaftaran`
- `GET /pendaftaran/sukses`

Referensi:
- `routes/web.php:21`
- `routes/web.php:22`
- `routes/web.php:23`
- `app/Http/Controllers/PendaftaranController.php:10`

### Data yang disimpan
Record dibuat ke tabel `pendaftaran` dengan status awal:
- `tanggal_daftar = now()`
- `status_validasi = pending`

Referensi:
- `app/Http/Controllers/PendaftaranController.php:24`
- `app/Http/Controllers/PendaftaranController.php:25`

### Validasi penting
Form request yang dipakai: `PendaftaranRequest`.

Validasi penting:
- biodata utama wajib diisi
- jenis dokumen identitas wajib berupa `ktp` atau `ktm`
- dokumen identitas wajib diunggah
- dokumen identitas hanya boleh `pdf/jpg/jpeg/png`
- ukuran maksimum 2 MB

Referensi:
- `app/Http/Requests/PendaftaranRequest.php:23`
- `app/Http/Requests/PendaftaranRequest.php:26`

### File upload
Dokumen identitas disimpan ke penyimpanan privat:
- `storage/app/private/pendaftaran`

Dokumen hanya dapat diunduh admin melalui route `admin.pendaftaran.document.download`.
Dokumen identitas langsung dihapus dan path database dikosongkan ketika pendaftaran ditolak.
Dokumen pada pendaftaran yang disetujui mengikuti umur record `pendaftaran`; belum ada retensi otomatis.

Referensi:
- `app/Http/Controllers/PendaftaranController.php:19`

### Hal yang perlu diingat saat mengubah fitur
- Jangan ubah nama field request sembarangan karena admin approval memakai data dari record pendaftaran ini untuk membuat `User` dan `Anggota`.
- Jika menambah field baru pada form pendaftaran, cek juga proses approve di admin.

---

## 4. Modul Admin

## 4.1 Dashboard admin

### Tujuan fitur
Menampilkan ringkasan statistik operasional untuk admin.

### Route
- `GET /admin/dashboard`

Referensi:
- `routes/web.php:29`
- `app/Http/Controllers/DashboardController.php:14`

### Statistik yang ditampilkan
- total anggota aktif
- total kegiatan
- total pendaftar pending
- total arsip
- total arsip

Referensi:
- `app/Http/Controllers/DashboardController.php:18`

### Data tambahan
- menampilkan 5 kegiatan mendatang

Referensi:
- `app/Http/Controllers/DashboardController.php:24`

### Dampak perubahan
Jika ada perubahan definisi bisnis “anggota aktif”, “pending”, atau sumber statistik lain, update dashboard ini juga.

---

## 4.2 Validasi pendaftaran oleh admin

### Tujuan fitur
Admin memproses pendaftaran yang masuk: menyetujui atau menolak.

### Route
- `GET /admin/pendaftaran`
- `GET /admin/pendaftaran/{id}`
- `POST /admin/pendaftaran/{id}/validate`

Referensi:
- `routes/web.php:32`
- `routes/web.php:33`
- `routes/web.php:34`
- `app/Http/Controllers/ValidasiPendaftaranController.php:17`

### Alur approve
Jika admin memilih `disetujui`:
1. Generate password random 8 karakter.
2. Buat `User` baru dengan role `kader`.
3. Buat `Anggota` baru berdasarkan data pendaftaran.
4. Update record pendaftaran menjadi `disetujui`.
5. Kirim email ke user yang berisi akun/password awal.

Referensi:
- `app/Http/Controllers/ValidasiPendaftaranController.php:37`
- `app/Http/Controllers/ValidasiPendaftaranController.php:41`
- `app/Http/Controllers/ValidasiPendaftaranController.php:42`
- `app/Http/Controllers/ValidasiPendaftaranController.php:49`
- `app/Http/Controllers/ValidasiPendaftaranController.php:59`
- `app/Http/Controllers/ValidasiPendaftaranController.php:66`

### Alur reject
Jika admin memilih `ditolak`:
- update `status_validasi = ditolak`
- simpan `catatan_admin`

Referensi:
- `app/Http/Controllers/ValidasiPendaftaranController.php:71`

### Validasi penting
Form request: `ValidasiPendaftaranRequest`

Aturan penting:
- `status` hanya `disetujui` atau `ditolak`
- `catatan_admin` wajib jika ditolak
- saat approve, email pendaftar tidak boleh sudah dipakai di tabel `users`

Referensi:
- `app/Http/Requests/ValidasiPendaftaranRequest.php:26`
- `app/Http/Requests/ValidasiPendaftaranRequest.php:37`

### Hal yang perlu diingat saat mengubah fitur
- Fitur ini adalah **jembatan utama** antara calon anggota dan akun kader.
- Jika struktur data `Pendaftaran`, `User`, atau `Anggota` berubah, fitur approve harus ikut dicek.
- Setelah approval, admin memberi tahu pendaftar melalui kanal operasional. Pendaftar
  dapat memakai fitur `Lupa Password` untuk menetapkan password pertama.

### Test terkait
- `tests/Feature/AdminFunctionalTest.php:13`
- `tests/Feature/AdminFunctionalTest.php:57`
- `tests/Feature/AdminFunctionalTest.php:81`

---

## 4.3 CRUD anggota

### Tujuan fitur
Admin mengelola data anggota yang sudah ada.

### Route
- Resource `admin/anggota`

Referensi:
- `routes/web.php:37`
- `app/Http/Controllers/AnggotaController.php:12`

### Fitur utama
- lihat daftar anggota
- tambah anggota
- lihat detail anggota
- edit anggota
- hapus anggota
- ubah role user anggota tertentu

### Validasi penting
Form request: `AnggotaRequest`

Aturan penting:
- `nia` boleh nullable, tetapi harus unique jika diisi
- `foto_profil` menerima JPG/JPEG/PNG, maksimum 2 MB dan 2048 x 2048 piksel
- `role` hanya boleh role tertentu dan tidak boleh `admin` dari form umum

Referensi:
- `app/Http/Requests/AnggotaRequest.php:25`
- `app/Http/Requests/AnggotaRequest.php:35`
- `app/Http/Requests/AnggotaRequest.php:38`

### Perilaku utama
- Saat create: controller hanya membuat record `Anggota`, tidak otomatis membuat `User`.
- Saat update: bila anggota punya relasi user, role user bisa ikut diupdate.
- Admin tidak boleh menurunkan role akun admin miliknya sendiri.

Referensi:
- `app/Http/Controllers/AnggotaController.php:24`
- `app/Http/Controllers/AnggotaController.php:33`
- `app/Http/Controllers/AnggotaController.php:68`
- `app/Http/Controllers/AnggotaController.php:71`
- `app/Http/Controllers/AnggotaController.php:56`

### File upload
Foto profil baru dikonversi di server menjadi WebP lalu disimpan ke:
- `storage/app/public/foto_profil`

Path database foto baru berakhiran `.webp`. Saat mengganti foto, file baru ditulis dan path database diperbarui terlebih dahulu; foto lama baru dihapus setelah transaksi berhasil. Foto lama JPG/PNG tetap dapat ditampilkan dan tidak dimigrasikan otomatis.

Referensi:
- `app/Http/Controllers/AnggotaController.php:28`
- `app/Http/Controllers/AnggotaController.php:60`

### Hal yang perlu diingat saat mengubah fitur
- Ada dua jalur terbentuknya anggota:
  1. lewat approve pendaftaran
  2. lewat create manual admin
- Jalur create manual bisa membuat anggota **tanpa akun login**.
- Menghapus anggota **tidak otomatis menghapus user**.

Referensi:
- `app/Http/Controllers/AnggotaController.php:85`

---

## 4.4 CRUD kegiatan

### Tujuan fitur
Admin dan instruktur mengelola data kegiatan.

### Route
- Resource `admin/kegiatan`

Referensi:
- `routes/web.php:60`
- `app/Http/Controllers/KegiatanController.php:12`

### Fitur utama
- daftar kegiatan
- tambah kegiatan
- edit kegiatan
- hapus kegiatan

### Validasi penting
Form request: `KegiatanRequest`

Aturan penting:
- nama, lokasi, tanggal-waktu wajib
- thumbnail image `jpg/jpeg/png`, max 2 MB

Referensi:
- `app/Http/Requests/KegiatanRequest.php:23`

### File upload
Thumbnail disimpan ke:
- `storage/app/public/kegiatan_thumbnails`

Referensi:
- `app/Http/Controllers/KegiatanController.php:28`
- `app/Http/Controllers/KegiatanController.php:59`

### Side effects penting
Setiap create, update, delete kegiatan harus menghapus cache landing:
- `kegiatan.terbaru`

Referensi:
- `app/Http/Controllers/KegiatanController.php:40`
- `app/Http/Controllers/KegiatanController.php:69`
- `app/Http/Controllers/KegiatanController.php:81`

### Hal yang perlu diingat saat mengubah fitur
- Modul ini mempengaruhi landing page, presensi, sertifikat, dashboard, dan laporan.
- Jika ubah field kegiatan, cek juga tampilan landing, detail kegiatan, presensi, dan export laporan.

### Test terkait
- `tests/Feature/InstrukturFunctionalTest.php:20`
- `tests/Feature/LandingPageTest.php:87`

---

## 4.5 Presensi kegiatan

### Tujuan fitur
Instruktur mencatat dan mengonfirmasi kehadiran anggota pada kegiatan. Admin hanya dapat melihat hasil presensi.

### Route
- `GET /admin/presensi/{kegiatan}`
- `POST /admin/presensi/{kegiatan}`

Referensi:
- `routes/web.php:63`
- `routes/web.php:64`
- `app/Http/Controllers/PresensiController.php:12`

### Perilaku utama
- Halaman GET menampilkan anggota aktif untuk admin dan instruktur.
- Form edit dan POST hanya tersedia untuk instruktur.
- Status `hadir` yang dipilih instruktur langsung tersimpan bersama `waktu_hadir`.
- Penyimpanan dilakukan dengan `updateOrCreate` berdasarkan kombinasi:
  - `kegiatan_id`
  - `anggota_id`
- Jika status `hadir`, `waktu_hadir` diisi `now()`.
- Jika status bukan `hadir`, `waktu_hadir` diisi `null`.

Referensi:
- `app/Http/Controllers/PresensiController.php:14`
- `app/Http/Controllers/PresensiController.php:24`
- `app/Http/Controllers/PresensiController.php:27`

### Validasi dan otorisasi
- `PresensiRequest` mengotorisasi hanya role `instruktur`.
- Payload memakai bentuk nested `presensi[][anggota_id]` dan `presensi[][status_kehadiran]`.
- ID anggota harus nyata dan unik dalam satu request; status hanya `hadir`, `izin`, atau `alfa`.
- Admin tetap dapat membuka halaman dalam mode read-only, sedangkan kader menerima `403`.

Referensi:
- `app/Http/Controllers/PresensiController.php:20`
- `app/Http/Requests/PresensiRequest.php:23`
- `tests/Feature/AdminFunctionalTest.php:119`

### Dampak perubahan
Modul ini berkaitan langsung dengan:
- dashboard kader
- riwayat keaktifan
- sertifikat

---

## 4.6 Sertifikat

Modul sertifikat adalah salah satu modul paling kompleks di aplikasi ini karena melibatkan:
- presensi
- queue job
- file PDF
- pengaturan background

### 4.6.1 Generate sertifikat oleh admin

#### Tujuan fitur
Admin menghasilkan sertifikat untuk banyak anggota pada satu kegiatan.

#### Route
- `GET /admin/sertifikat`
- `GET /admin/sertifikat/create`
- `POST /admin/sertifikat/generate`
- `GET /admin/sertifikat/{sertifikat}/download`

Referensi:
- `routes/web.php:44`
- `routes/web.php:45`
- `routes/web.php:48`
- `routes/web.php:49`

#### Validasi penting
Form request: `SertifikatRequest`

Aturan penting:
- `kegiatan_id` wajib valid
- `anggota_ids` wajib array
- setiap `anggota_id` harus ada di database

Referensi:
- `app/Http/Requests/SertifikatRequest.php:23`

#### Perilaku utama
- Controller akan loop semua `anggota_ids`.
- Untuk setiap anggota, controller dispatch `GenerateCertificateJob`.
- File sertifikat PDF disimpan ke `storage/app/public/sertifikat`.
- Database `sertifikat` memakai `updateOrCreate` per pasangan `kegiatan_id + anggota_id`.

Referensi:
- `app/Http/Controllers/SertifikatController.php:59`
- `app/Http/Controllers/SertifikatController.php:65`
- `app/Http/Controllers/SertifikatController.php:47`
- `app/Http/Controllers/SertifikatController.php:50`

#### Format nomor sertifikat
Nomor dibuat dengan format:
- `CERT-{kegiatan_id}-{anggota_id}-{YYYYMMDD}`

Referensi:
- `app/Http/Controllers/SertifikatController.php:40`

#### Hal yang perlu diingat saat mengubah fitur
- Proses generate berjalan lewat queue.
- Jangan mengubah format data sertifikat tanpa mengecek proses download dan riwayat kader.

### 4.6.2 Sertifikat milik kader

#### Tujuan fitur
Kader melihat daftar sertifikat miliknya, mengajukan klaim setelah memenuhi syarat kehadiran, dan mengunduh sertifikat yang target kegiatannya masih dihadiri.

#### Route
- `GET /kader/sertifikat`
- `POST /kader/sertifikat/{presensi}/klaim`
- `GET /kader/sertifikat/{sertifikat}/download`

Referensi:
- `routes/web.php:82`
- `routes/web.php:87`

#### Aturan akses
- Jika user tidak punya data anggota -> redirect error.
- Kader hanya dapat mengunduh sertifikat miliknya setelah memiliki presensi `hadir` pada minimal tiga kegiatan berbeda.
- Kegiatan pada sertifikat yang diunduh juga wajib masih memiliki presensi `hadir` milik kader.
- Admin tetap dapat mengunduh melalui route admin tanpa syarat tiga kehadiran.

Referensi:
- `app/Http/Controllers/SertifikatController.php:75`
- `app/Http/Controllers/SertifikatController.php:88`

### 4.6.3 Data klaim historis

Klaim kader tersedia melalui satu POST tanpa body dan tanpa upload bukti. Kader harus memiliki presensi `hadir` pada minimal tiga kegiatan berbeda, lalu dapat memilih presensi `hadir` miliknya sebagai target. Sistem mengantrekan pembuatan sertifikat tanpa approval kedua. Route verifikasi setuju/tolak tetap tidak tersedia.

Kolom `status_klaim` dan `bukti_kehadiran`, beserta file bukti yang sudah tersimpan, dipertahankan untuk retensi data historis. Aplikasi tidak mengubah, menghapus, atau memproses ulang data tersebut melalui alur baru.

### 4.6.4 Sertifikat di riwayat kader

Halaman riwayat kader menampilkan progres kegiatan `hadir` terkonfirmasi. Tombol klaim hanya muncul pada presensi `hadir` milik kader yang sudah memiliki minimal tiga kegiatan hadir dan belum memiliki sertifikat target. Form klaim hanya memakai CSRF dan POST tanpa file atau multipart. Tombol unduh hanya muncul ketika syarat global dan kehadiran target masih terpenuhi; pengecekan server tetap menjadi otorisasi akhir.

### 4.6.5 Pengaturan background sertifikat

#### Tujuan fitur
Admin mengatur apakah sertifikat memakai background dan mengganti file background-nya.

#### Route
- `GET /admin/sertifikat/settings`
- `POST /admin/sertifikat/settings`

Referensi:
- `routes/web.php:46`
- `routes/web.php:47`
- `app/Http/Controllers/SertifikatController.php:227`

#### Penyimpanan setting
- Toggle penggunaan background disimpan ke file:
  - `storage/app/sertifikat_settings.json`

Referensi:
- `app/Http/Controllers/SertifikatController.php:218`
- `app/Http/Controllers/SertifikatController.php:245`

#### Penyimpanan gambar background
File background sertifikat disimpan langsung ke public path:
- `public/images/sertificate-asset/bg-sertificate.jpg`

Referensi:
- `app/Http/Controllers/SertifikatController.php:229`
- `app/Http/Controllers/SertifikatController.php:251`

Catatan penting:
- Ejaan folder/file saat ini adalah **`sertificate`**, bukan `certificate`.
- Jangan ubah ejaan ini parsial tanpa audit menyeluruh.

#### Validasi dan pengolahan gambar
- file harus image `jpg/jpeg/png`
- max 4 MB
- jika GD tersedia, gambar di-crop/resize ke 1122x794
- jika GD tidak ada, file disalin langsung apa adanya

Referensi:
- `app/Http/Controllers/SertifikatController.php:238`
- `app/Http/Controllers/SertifikatController.php:258`
- `app/Http/Controllers/SertifikatController.php:264`
- `app/Http/Controllers/SertifikatController.php:276`

### Test terkait modul sertifikat
- `tests/Feature/SertifikatLaporanTest.php:10`
- `tests/Feature/KlaimSertifikatTest.php:16`
- `tests/Feature/SertifikatSettingsTest.php:38`

---

## 4.7 Arsip

### Tujuan fitur
Menyimpan dokumen arsip anggota atau organisasi.

### Route admin
- resource `admin/arsip` tanpa create/edit/update/show
- `GET /admin/arsip/{arsip}/download`

Referensi:
- `routes/web.php:40`
- `routes/web.php:41`

### Fitur admin
- melihat daftar arsip
- upload arsip
- download arsip
- hapus arsip

Referensi:
- `app/Http/Controllers/ArsipController.php:11`

### Validasi penting
Form request: `ArsipRequest`

Aturan penting:
- `anggota_id` wajib
- `judul_dokumen`, `kategori_arsip`, `tanggal_unggah`, `file_arsip` wajib
- file hanya `pdf/doc/docx/jpg/jpeg/png`
- max 5 MB

Referensi:
- `app/Http/Requests/ArsipRequest.php:23`

### File upload
Disimpan ke:
- `storage/app/public/arsip`

Referensi:
- `app/Http/Controllers/ArsipController.php:29`

### Nama file saat download
Nama download dibuat dari `judul_dokumen` dan extension file asli.

Referensi:
- `app/Http/Controllers/ArsipController.php:41`

### Caveat penting
Method `download()` saat ini tidak memeriksa apakah kader adalah pemilik arsip tersebut. Jadi jika nanti fitur arsip kader ingin diamankan lebih ketat, area ini harus ditinjau.

Referensi:
- `app/Http/Controllers/ArsipController.php:39`

### Test terkait
- `tests/Feature/AdminFunctionalTest.php:142`
- `tests/Feature/SecurityTest.php:33`

---

## 4.8 Laporan

### Tujuan fitur
Admin bisa mengekspor laporan PDF atau Excel berdasarkan rentang tanggal.

### Route
- `GET /admin/laporan`
- `POST /admin/laporan/export-pdf`
- `POST /admin/laporan/export-excel`

Referensi:
- `routes/web.php:52`
- `routes/web.php:53`
- `routes/web.php:54`
- `app/Http/Controllers/LaporanController.php:16`

### Jenis laporan yang didukung
- `kegiatan`
- `anggota`
- `pendaftaran`
- `arsip`

Referensi:
- `app/Http/Requests/LaporanRequest.php:23`

### Aturan query per jenis
- `kegiatan` -> berdasarkan `tanggal_waktu`
- `anggota` -> berdasarkan `created_at`
- `pendaftaran` -> berdasarkan `tanggal_daftar`
- `arsip` -> berdasarkan `tanggal_unggah`

Referensi:
- `app/Http/Controllers/LaporanController.php:55`

### Output
- PDF dibuat dari view `pdf.laporan`
- Excel dibuat lewat `LaporanExport`

Referensi:
- `app/Http/Controllers/LaporanController.php:30`
- `app/Http/Controllers/LaporanController.php:49`
- `app/Exports/LaporanExport.php:54`

### Hal yang perlu diingat saat mengubah fitur
- Jika menambah jenis laporan baru, harus update minimal:
  - validasi request
  - query pengambilan data
  - template PDF
  - export Excel
  - form pilihan di UI

---

## 5. Modul Instruktur

Instruktur tidak punya prefix route khusus sendiri. Mereka memakai area `/admin`, tetapi hanya untuk modul tertentu.

### Modul yang bisa diakses instruktur
- kegiatan
- presensi

Referensi:
- `routes/web.php:57`
- `routes/web.php:60`
- `routes/web.php:63`
- `routes/web.php:67`

### Redirect setelah login
Instruktur diarahkan ke:
- `admin.kegiatan.index`

Referensi:
- `app/Models/User.php:52`

### Test terkait
- `tests/Feature/InstrukturFunctionalTest.php:20`
- `tests/Feature/InstrukturFunctionalTest.php:99`

---

## 6. Modul Kader

## 6.1 Dashboard kader

### Tujuan fitur
Menampilkan ringkasan aktivitas kader.

### Route
- `GET /kader/dashboard`

Referensi:
- `routes/web.php:75`
- `app/Http/Controllers/DashboardController.php:32`

### Perilaku utama
Jika user belum punya data anggota:
- redirect ke form pendaftaran

Referensi:
- `app/Http/Controllers/DashboardController.php:37`

### Statistik utama
- total kehadiran hadir
- total sertifikat

Referensi:
- `app/Http/Controllers/DashboardController.php:41`

### Data tambahan
- menampilkan 3 kegiatan mendatang

Referensi:
- `app/Http/Controllers/DashboardController.php:46`

---

## 6.2 E-KTA

### Tujuan fitur
Kader melihat kartu anggota digital dan mengunduh versi PDF.

### Route
- `GET /kader/ekta`
- `GET /kader/ekta/download`

Referensi:
- `routes/web.php:78`
- `routes/web.php:79`
- `app/Http/Controllers/EktaController.php:9`

### Aturan utama
- Jika user tidak punya data anggota -> redirect error.
- File PDF bernama `E-KTA_{nia atau id}.pdf`.

Referensi:
- `app/Http/Controllers/EktaController.php:13`
- `app/Http/Controllers/EktaController.php:30`

### Test terkait
- `tests/Feature/KaderFunctionalTest.php:44`

---

## 6.3 Riwayat keaktifan

### Tujuan fitur
Kader melihat riwayat presensi dan ketersediaan sertifikat per kegiatan.

### Route
- `GET /kader/riwayat`

Referensi:
- `routes/web.php:90`
- `app/Http/Controllers/RiwayatKeaktifanController.php:10`

### Perilaku utama
- Ambil seluruh presensi milik kader.
- Eager load relasi `kegiatan`.
- Ambil semua sertifikat milik kader, lalu `keyBy('kegiatan_id')`.
- Hitung statistik `hadir`, `izin`, `alfa`.
- Hitung progres kelayakan dengan `COUNT(DISTINCT kegiatan_id)` untuk status `hadir`.
- Tampilkan tombol klaim tanpa upload hanya setelah minimal tiga kegiatan hadir berbeda.

Referensi:
- `app/Http/Controllers/RiwayatKeaktifanController.php:18`
- `app/Http/Controllers/RiwayatKeaktifanController.php:23`
- `app/Http/Controllers/RiwayatKeaktifanController.php:27`

### Keterkaitan fitur lain
Dari halaman ini user bisa:
- melihat status kehadiran
- melihat ketersediaan sertifikat
- download sertifikat yang sudah tersedia

### Test terkait
- `tests/Feature/KaderFunctionalTest.php:95`

---

## 6.4 Arsip kader

### Tujuan fitur
Kader dapat melihat arsip dan mengunggah arsip.

### Route
- `GET /kader/arsip`
- `POST /kader/arsip`
- `GET /kader/arsip/{arsip}/download`

Referensi:
- `routes/web.php:93`
- `routes/web.php:94`
- `routes/web.php:95`
- `app/Http/Controllers/ArsipController.php:18`

### Catatan penting
- Upload kader memakai `ArsipRequest` yang tetap mewajibkan `anggota_id`.
- Jadi saat mengubah form atau API kader, pastikan field ini tetap dikirim jika belum mengubah backend.

Referensi:
- `app/Http/Requests/ArsipRequest.php:25`

---

## 6.5 Profil user

### Tujuan fitur
Semua user login bisa mengubah profil akun dan biodata anggota, serta menghapus akun.

### Route
- `GET /profile`
- `PATCH /profile`
- `DELETE /profile`

Referensi:
- `routes/web.php:98`
- `routes/web.php:99`
- `routes/web.php:100`
- `app/Http/Controllers/ProfileController.php:18`

### Perilaku utama update profil
- update `name` dan `email`
- jika email berubah -> `email_verified_at = null`
- jika user punya data anggota -> update biodata anggota juga
- jika upload foto baru -> konversi ke WebP, simpan dan update database, lalu hapus foto lama setelah transaksi berhasil

Referensi:
- `app/Http/Controllers/ProfileController.php:34`
- `app/Http/Controllers/ProfileController.php:39`
- `app/Http/Controllers/ProfileController.php:45`
- `app/Http/Controllers/ProfileController.php:54`

### Validasi penting
Form request: `ProfileUpdateRequest`

Aturan penting:
- name/email wajib
- email unique kecuali milik user aktif
- biodata anggota wajib jika memang diedit lewat form
- foto profil menerima JPG/JPEG/PNG, maksimum 2 MB dan 2048 x 2048 piksel; hasil upload disimpan sebagai WebP

Referensi:
- `app/Http/Requests/ProfileUpdateRequest.php:25`

### Hapus akun
- user wajib mengisi password saat ini
- setelah delete, user logout dan session diinvalidasi

Referensi:
- `app/Http/Controllers/ProfileController.php:73`
- `app/Http/Controllers/ProfileController.php:79`

### Test terkait
- `tests/Feature/KaderFunctionalTest.php:10`
- `tests/Feature/ProfileTest.php:1`

---

## 7. Sistem pendukung lintas modul

## 7.1 Queue

### Yang memakai queue atau proses async
- generate sertifikat: `GenerateCertificateJob`
- composer dev stack sudah menjalankan queue listener

Referensi:
- `app/Jobs/GenerateCertificateJob.php:12`
- `composer.json:52`

### Dampak perubahan
Jika ada bug pada fitur sertifikat, cek apakah queue worker berjalan. Persetujuan pendaftaran
membuat akun dan profil anggota tanpa mengirim email onboarding otomatis. Admin perlu
memberi tahu pendaftar melalui kanal operasional, lalu pendaftar dapat memakai fitur
`Lupa Password` untuk menetapkan password pertama.

Perintah dev lokal yang sudah menyalakan queue:
- `composer run dev`

---

## 7.2 Penyimpanan file

Folder penting yang dipakai fitur:

- `storage/app/private/pendaftaran`
- `storage/app/public/foto_profil`
- `storage/app/public/kegiatan_thumbnails`
- `storage/app/public/bukti_kehadiran` (file historis; tidak ada upload baru)
- `storage/app/public/sertifikat`
- `storage/app/private/arsip` (file arsip bersifat privat dan diakses melalui route, bukan URL publik)
- `storage/app/sertifikat_settings.json`
- `public/images/sertificate-asset/bg-sertificate.jpg`

Saat mengubah fitur upload atau deployment, cek semua path ini.

**Penting:** Jika Anda membutuhkan dummy data (terutama PDF dan gambar) yang dapat diunduh tanpa rusak/broken saat pengembangan, jalankan `php artisan demo:seed-files`. Rincian penggunaannya dijelaskan pada dokumen `support-for-developer/troubleshooting/SEED_DATA_DUMMY.md`.

---

## 7.3 PDF dan Excel

Library yang dipakai:
- PDF: `barryvdh/laravel-dompdf`
- Excel: `maatwebsite/excel`

Dipakai untuk:
- E-KTA
- sertifikat
- laporan PDF
- laporan Excel

Referensi:
- `composer.json:13`
- `composer.json:17`

---

## 7.4 PWA / service worker

### Tujuan fitur
Aplikasi punya dukungan PWA dasar.

### Komponen utama
- `public/manifest.json`
- `public/sw.js`

Referensi:
- `public/manifest.json:1`
- `public/sw.js:1`

### Perilaku nyata saat ini
Service worker hanya meng-cache:
- `/`

Referensi:
- `public/sw.js:2`

### Catatan penting
- Jangan klaim aplikasi sudah punya offline support penuh.
- Implementasi PWA saat ini masih minimal.

---

## 7.5 Frontend assets

Vite memakai beberapa entrypoint, bukan hanya satu file CSS/JS.

Entry utama:
- `resources/css/app.css`
- `resources/css/landing.css`
- `resources/js/app.js`

Referensi:
- `vite.config.js:7`

Catatan penting:
- UI utama banyak memakai **Bootstrap**, bukan Tailwind.
- Namun view auth Breeze masih membawa gaya utilitas yang terasa seperti Tailwind.

Referensi:
- `resources/css/app.css:1`

---

## 8. Peta hubungan antar modul

Agar tidak salah saat refactor, pahami hubungan berikut:

### Pendaftaran -> Approval -> User + Anggota
- pendaftaran publik masuk ke tabel `pendaftaran`
- admin approve -> membuat `users` + `anggota`
- user hasil approve masuk sebagai `kader`

### Kegiatan -> Landing + Presensi + Sertifikat + Laporan
- kegiatan tampil di landing
- kegiatan dipakai untuk presensi
- kegiatan dipakai untuk generate sertifikat
- kegiatan dipakai di export laporan

### Presensi -> Riwayat -> Sertifikat
- presensi menentukan status kehadiran kader
- riwayat menampilkan status itu
- kader dapat mengklaim sertifikat setelah tiga kegiatan berbeda berstatus `hadir`
- admin tetap dapat membuat sertifikat secara eksplisit melalui modul sertifikat

### Anggota -> E-KTA + Profil + Sertifikat + Arsip
- data anggota dipakai untuk E-KTA
- profil user bisa mengubah biodata anggota
- sertifikat terhubung ke anggota
- arsip terhubung ke anggota

---

## 9. Daftar caveat penting sebelum mengubah fitur

Bagian ini wajib dibaca sebelum refactor besar.

### 9.1 Arsip kader belum terlihat aman sepenuhnya
- Download arsip tidak mengecek kepemilikan file di controller.
- Jika ingin membatasi arsip per kader, perbaiki sisi query + authorization.

Referensi:
- `app/Http/Controllers/ArsipController.php:39`

### 9.2 Anggota manual != akun login
- Create anggota manual oleh admin tidak otomatis membuat user.
- Jangan anggap semua anggota pasti punya akun.

Referensi:
- `app/Http/Controllers/AnggotaController.php:24`
- `app/Http/Controllers/AnggotaController.php:33`

### 9.3 Sertifikat sangat bergantung ke queue dan file system
- Bug sertifikat sering terkait worker queue, penyimpanan file, atau library image/PDF.
- Saat debug, cek queue, storage path, dan ketersediaan GD/Intervention.

Referensi:
- `app/Http/Controllers/SertifikatController.php:65`
- `app/Http/Controllers/SertifikatController.php:132`
- `app/Http/Controllers/SertifikatController.php:258`

### 9.4 Cache landing bisa membuat perubahan terasa "tidak masuk"
- Jika data kegiatan diubah tetapi landing tidak berubah, cek invalidasi cache `kegiatan.terbaru`.

Referensi:
- `app/Http/Controllers/LandingController.php:16`
- `app/Http/Controllers/KegiatanController.php:40`

### 9.5 Beberapa konten landing masih placeholder bisnis
- `config/landing.php` masih punya beberapa TODO dan placeholder.
- Saat mengubah copy/branding, bedakan antara perubahan teknis dan perubahan konten bisnis.

Referensi:
- `config/landing.php:7`
- `config/landing.php:15`
- `config/landing.php:75`
- `config/landing.php:89`

---

## 10. Rekomendasi cara aman saat mengubah fitur

Urutan kerja yang aman untuk developer junior atau AI:

1. **Cek route dulu** di `routes/web.php`.
2. **Cek controller utama** untuk melihat alur data yang benar.
3. **Cek Form Request** untuk validasi nyata.
4. **Cek model terkait** untuk relasi dan helper method.
5. **Cek test feature terkait** sebelum mengubah logika.
6. Jika modul menyentuh file, queue, cache, atau PDF, cek side effect-nya juga.

Contoh:
- ingin ubah klaim sertifikat -> baca `routes/web.php`, `SertifikatController`, `Presensi`, `tests/Feature/KlaimSertifikatTest.php`
- ingin ubah approval pendaftaran -> baca `ValidasiPendaftaranController`, `PendaftaranRequest`, `ValidasiPendaftaranRequest`, `AdminFunctionalTest`
- ingin ubah landing -> baca `LandingController`, `config/landing.php`, `LandingPageTest`

---

## 11. Test yang paling berguna sebagai pagar perubahan

Jika hanya ingin smoke-check perubahan secara cepat, file test yang paling bernilai biasanya:

- `tests/Feature/LandingPageTest.php`
- `tests/Feature/AdminFunctionalTest.php`
- `tests/Feature/InstrukturFunctionalTest.php`
- `tests/Feature/KaderFunctionalTest.php`
- `tests/Feature/KlaimSertifikatTest.php`
- `tests/Feature/SertifikatLaporanTest.php`
- `tests/Feature/SertifikatSettingsTest.php`
- `tests/Feature/SecurityTest.php`

Catatan:
- Feature test memakai `RefreshDatabase` secara global.

Referensi:
- `tests/Pest.php:17`

---

## 12. Ringkasan paling penting

Jika hanya mengingat 12 hal, ingat ini:

1. Ada 4 aktor: guest, admin, instruktur, kader.
2. Instruktur memakai prefix route `/admin`, bukan area terpisah.
3. Approval pendaftaran membuat `User` + `Anggota` sekaligus.
4. Create anggota manual tidak otomatis membuat akun login.
5. Landing mengambil 3 kegiatan terbaru dari cache `kegiatan.terbaru`.
6. Kegiatan mempengaruhi landing, presensi, sertifikat, dan laporan.
7. Presensi adalah basis riwayat keaktifan.
8. Hanya instruktur yang dapat menyimpan presensi; admin hanya dapat melihatnya.
9. Klaim kader membutuhkan tiga kegiatan berbeda berstatus `hadir` dan diproses melalui queue tanpa upload bukti.
10. Admin tetap dapat membuat sertifikat secara eksplisit melalui queue.
11. Data klaim dan file bukti lama dipertahankan tanpa diaktifkan kembali.
12. Background sertifikat disimpan di path public dengan ejaan `sertificate`.
12. Arsip kader perlu perhatian khusus untuk authorization download.
13. PWA ada, tetapi offline cache saat ini masih minimal.

---

## 13. Penutup

Dokumen ini sebaiknya diperbarui setiap kali ada perubahan besar pada:
- role dan authorization
- alur pendaftaran
- modul sertifikat
- penyimpanan file
- format laporan
- dashboard
- struktur route utama

Jika ada konflik antara dokumen ini dan kode, **anggap kode sebagai sumber kebenaran utama**, lalu perbarui dokumen ini.

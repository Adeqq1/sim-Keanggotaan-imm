# Dokumen Analisis Kebutuhan Sistem (Requirements)

## Sistem Informasi Manajemen Keanggotaan dan Kearsipan Berbasis Website

**Versi Dokumen:** 1.0  
**Tanggal:** 10 Mei 2026  
**Pendekatan:** Spec-Driven Development (SDD) dengan Rapid Application Development (RAD)

---

## 1. Gambaran Umum Sistem

Sistem Informasi Manajemen Keanggotaan dan Kearsipan adalah aplikasi berbasis website yang dirancang untuk mengelola data keanggotaan (kader), kegiatan organisasi, presensi, arsip dokumen, serta penerbitan dokumen digital seperti E-KTA (Kartu Tanda Anggota digital) dan E-Sertifikat. Sistem ini dibangun dengan pendekatan **Mobile-First** agar dapat diakses secara optimal melalui perangkat seluler maupun desktop.

---

## 2. Identifikasi Aktor

Berdasarkan Use Case Diagram, terdapat **tiga (3) aktor utama** dalam sistem:

| No | Aktor | Deskripsi |
|----|-------|-----------|
| 1 | **Admin** | Pengelola utama sistem yang memiliki hak akses penuh untuk mengelola data anggota, memvalidasi pendaftaran, mengelola kegiatan & presensi, mengelola arsip dokumen, mengelola E-Sertifikat, mengelola profil, serta mencetak laporan. |
| 2 | **Kader** | Anggota organisasi yang telah tervalidasi. Kader dapat mengelola kegiatan & presensi, mengelola arsip dokumen, mengelola profil pribadi, mencetak E-KTA, mengunduh E-Sertifikat, dan melihat riwayat keaktifan. |
| 3 | **Pengunjung** | Pengguna publik yang belum terdaftar dalam sistem. Pengunjung hanya dapat melakukan pendaftaran sebagai calon anggota (kader). |

---

## 3. Daftar Use Case

Berikut adalah daftar lengkap use case yang teridentifikasi dari Use Case Diagram:

### 3.1 Use Case Khusus Admin

| No | Use Case | Deskripsi |
|----|----------|-----------|
| UC-01 | **Mencetak Laporan** | Admin dapat mencetak atau mengekspor laporan dalam format PDF atau Excel berdasarkan filter tertentu (rentang tanggal, jenis laporan). |
| UC-02 | **Memvalidasi Pendaftaran** | Admin menerima daftar pendaftar berstatus "Menunggu Validasi", lalu dapat menyetujui (mengubah status menjadi "Kader" dan membuat akun) atau menolak pendaftaran. |
| UC-03 | **Mengelola Data Anggota** | Admin dapat melihat tabel seluruh data anggota (Kader) yang aktif, serta melakukan aksi tambah, edit, dan hapus data anggota. |
| UC-04 | **Mengelola E-Sertifikat** | Admin dapat melihat daftar E-Sertifikat yang pernah dibuat, membuat sertifikat baru dengan memilih nama-nama Kader penerima, dan meng-generate sertifikat secara otomatis. |

### 3.2 Use Case Bersama (Admin & Kader)

| No | Use Case | Deskripsi |
|----|----------|-----------|
| UC-05 | **Mengelola Kegiatan & Presensi** | Admin dapat membuat kegiatan baru, melihat kalender/daftar agenda kegiatan, memilih kegiatan yang sudah berjalan, menampilkan daftar Kader, dan mencatat kehadiran (Hadir/Izin/Alfa). Kader dapat melihat kegiatan yang tersedia. |
| UC-06 | **Mengelola Arsip Dokumen** | Pengguna dapat mengakses menu arsip dokumen, mengunggah dokumen baru (dengan validasi ekstensi/ukuran file), mengunduh dokumen yang tersimpan, atau menghapus dokumen (dengan konfirmasi). |
| UC-07 | **Mengelola Profil** | Pengguna dapat membuka menu "Profil Saya", melihat form yang terisi data saat ini, mengubah informasi, lalu menyimpan perubahan. Sistem akan memvalidasi data sebelum menyimpan. |

### 3.3 Use Case Khusus Kader

| No | Use Case | Deskripsi |
|----|----------|-----------|
| UC-08 | **Mencetak E-KTA** | Kader dapat mengakses menu E-KTA, melihat preview Kartu Tanda Anggota digital yang telah diisi data profil secara otomatis, lalu mencetak atau mengunduh dalam format PDF. |
| UC-09 | **Mengunduh E-Sertifikat** | Kader dapat mengakses menu "E-Sertifikat Saya", melihat daftar sertifikat kegiatan yang telah di-generate oleh Admin, lalu mengunduh file PDF sertifikat yang diinginkan. |
| UC-10 | **Melihat Riwayat Keaktifan** | Kader dapat melihat rekam jejak presensi dan partisipasi kegiatan dalam bentuk tabel riwayat atau grafik statistik kehadiran. |

### 3.4 Use Case Khusus Pengunjung

| No | Use Case | Deskripsi |
|----|----------|-----------|
| UC-11 | **Melakukan Pendaftaran** | Pengunjung dapat membuka menu pendaftaran, mengisi form registrasi calon anggota (Nama, TTL, Email, dsb.), lalu mengirimkan pendaftaran. Data disimpan dengan status "Pending" dan menunggu validasi Admin. |

### 3.5 Use Case Autentikasi (Semua Aktor Teregistrasi)

| No | Use Case | Deskripsi |
|----|----------|-----------|
| UC-12 | **Login** | Pengguna (Admin/Kader) membuka halaman login, memasukkan username & password, sistem memvalidasi input dan melakukan autentikasi database. Jika berhasil, diarahkan ke dashboard sesuai role; jika gagal, kembali ke halaman login. |
| UC-13 | **Logout** | Pengguna yang sudah login mengeklik menu "Logout". Sistem memvalidasi sesi login, menampilkan konfirmasi logout. Jika dikonfirmasi, sesi dihapus dan pengguna diarahkan ke halaman utama. Jika dibatalkan, pengguna kembali ke dashboard. |

**Catatan Relasi:**
- Seluruh use case (kecuali Melakukan Pendaftaran) memiliki relasi **«include»** terhadap use case **Login**, yang berarti pengguna wajib login terlebih dahulu sebelum mengakses fitur tersebut.
- Use case **Login** memiliki relasi **«extend»** terhadap use case **Logout**, yang berarti fitur logout merupakan ekstensi opsional dari sesi login yang aktif.

---

## 4. Kebutuhan Fungsional

Berdasarkan analisis Use Case Diagram dan Activity Diagram, berikut adalah daftar kebutuhan fungsional sistem:

### KF-01: Autentikasi dan Otorisasi
- Sistem **harus** menyediakan halaman login dengan validasi input (field wajib diisi).
- Sistem **harus** melakukan autentikasi terhadap database pengguna.
- Sistem **harus** mengarahkan pengguna ke dashboard yang sesuai berdasarkan role (Admin/Kader).
- Sistem **harus** menyediakan fitur logout dengan konfirmasi sebelum menghapus sesi.
- Sistem **harus** melindungi seluruh halaman (kecuali pendaftaran dan halaman publik) dengan middleware autentikasi.

### KF-02: Pendaftaran Anggota Baru
- Sistem **harus** menyediakan form registrasi calon anggota yang dapat diakses publik (oleh Pengunjung).
- Sistem **harus** memvalidasi kelengkapan dan format data yang diisi pada form pendaftaran.
- Sistem **harus** menyimpan data pendaftar ke database dengan status awal **"Pending"**.
- Sistem **harus** menampilkan pesan sukses dan instruksi menunggu validasi Admin setelah pendaftaran berhasil.
- Sistem **harus** menampilkan pesan error jika data tidak valid atau tidak lengkap.

### KF-03: Validasi Pendaftaran
- Sistem **harus** menampilkan daftar pendaftar yang berstatus **"Menunggu Validasi"** kepada Admin.
- Admin **harus** dapat melihat detail data pendaftar.
- Admin **harus** dapat menyetujui pendaftaran (mengubah status menjadi "Kader" dan membuat akun kader).
- Admin **harus** dapat menolak pendaftaran (mengubah status menjadi "Ditolak" dan menghapus dari antrean).
- Sistem **harus** memperbarui tampilan tabel dan menampilkan pesan konfirmasi setelah aksi dilakukan.

### KF-04: Manajemen Data Anggota
- Sistem **harus** menampilkan tabel berisi seluruh data anggota (Kader) yang aktif.
- Admin **harus** dapat menambah, mengedit, dan menghapus data anggota.
- Sistem **harus** memperbarui tampilan tabel dan menampilkan pesan status setelah perubahan dilakukan.

### KF-05: Manajemen Kegiatan dan Presensi
- Sistem **harus** menampilkan kalender atau daftar agenda kegiatan.
- Admin **harus** dapat membuat kegiatan baru melalui form.
- Admin **harus** dapat memilih kegiatan yang sudah berjalan dan melihat daftar Kader.
- Admin **harus** dapat mencentang kehadiran setiap Kader dengan status **Hadir**, **Izin**, atau **Alfa**.
- Admin **harus** dapat membatalkan proses pencatatan presensi.
- Sistem **harus** merekam data presensi ke database.

### KF-06: Manajemen Arsip Dokumen
- Sistem **harus** menampilkan daftar dokumen atau file yang telah diunggah.
- Pengguna **harus** dapat mengunggah dokumen baru.
- Sistem **harus** memvalidasi ekstensi dan ukuran file yang diunggah.
- Sistem **harus** menyimpan file ke storage dan mencatat metadata ke database.
- Pengguna **harus** dapat mengunduh dokumen yang tersimpan.
- Pengguna **harus** dapat menghapus dokumen dengan konfirmasi terlebih dahulu.
- Sistem **harus** memperbarui tampilan daftar arsip dan menampilkan pesan sukses setelah perubahan.

### KF-07: Manajemen Profil
- Sistem **harus** menampilkan form profil yang telah terisi data pengguna saat ini.
- Pengguna **harus** dapat mengubah informasi pada form profil.
- Sistem **harus** memvalidasi data sebelum menyimpan perubahan.
- Sistem **harus** menampilkan pesan error pada field yang tidak valid.
- Sistem **harus** menampilkan pesan sukses jika perubahan berhasil disimpan.

### KF-08: Penerbitan E-KTA (Kartu Tanda Anggota Digital)
- Sistem **harus** menarik data profil Kader dari database saat menu E-KTA diakses.
- Sistem **harus** meletakkan data profil ke dalam template desain KTA digital.
- Sistem **harus** menampilkan preview E-KTA di layar.
- Kader **harus** dapat mencetak atau mengunduh E-KTA dalam format **PDF**.

### KF-09: Manajemen E-Sertifikat
- Sistem **harus** menampilkan daftar E-Sertifikat yang pernah dibuat atau didistribusikan.
- Admin **harus** dapat membuat sertifikat baru dengan memilih nama-nama Kader penerima.
- Sistem **harus** memproses data dan menggabungkan nama Kader ke dalam template desain sertifikat.
- Sistem **harus** menyimpan record E-Sertifikat ke profil masing-masing Kader.
- Kader **harus** dapat melihat daftar sertifikat yang dimiliki melalui menu "E-Sertifikat Saya".
- Kader **harus** dapat mengunduh file PDF E-Sertifikat dari storage.

### KF-10: Riwayat Keaktifan
- Sistem **harus** menampilkan rekam jejak presensi dan partisipasi kegiatan milik Kader.
- Data **harus** disajikan dalam bentuk tabel riwayat atau grafik statistik kehadiran.

### KF-11: Pencetakan Laporan
- Sistem **harus** menyediakan halaman filter laporan berdasarkan rentang tanggal dan jenis laporan.
- Sistem **harus** mengambil data dari database sesuai dengan filter yang dipilih.
- Sistem **harus** dapat mengonversi data menjadi format **PDF** atau **Excel**.
- Sistem **harus** mengunduh file laporan ke perangkat Admin.

---

## 5. Kebutuhan Non-Fungsional

| No | Kategori | Deskripsi |
|----|----------|-----------|
| KNF-01 | **Responsivitas** | Sistem harus dirancang dengan pendekatan Mobile-First, sehingga tampilan optimal di perangkat seluler (≥ 320px) dan tetap responsif di tablet serta desktop. |
| KNF-02 | **Keamanan** | Sistem harus mengimplementasikan autentikasi berbasis sesi dengan hashing password. Akses ke setiap fitur harus dikontrol berdasarkan role pengguna (Admin/Kader). |
| KNF-03 | **Performa** | Halaman utama dan dashboard harus dapat dimuat dalam waktu kurang dari 3 detik pada koneksi standar. |
| KNF-04 | **Usabilitas** | Antarmuka pengguna harus intuitif dan mudah digunakan, dengan navigasi yang jelas dan pesan feedback (sukses/error) yang informatif. |
| KNF-05 | **Ketersediaan** | Sistem harus dapat diakses 24/7 melalui browser modern (Chrome, Firefox, Safari, Edge). |
| KNF-06 | **Skalabilitas** | Struktur database dan arsitektur aplikasi harus mampu menampung pertumbuhan data anggota dan arsip dokumen tanpa degradasi performa yang signifikan. |
| KNF-07 | **Validasi Data** | Seluruh form input harus memiliki validasi sisi klien (client-side) dan sisi server (server-side) untuk menjaga integritas data. |
| KNF-08 | **Manajemen File** | Sistem harus mendukung unggah dan unduh file dengan validasi ekstensi dan ukuran. File harus disimpan secara terstruktur di storage server. |

---

## 6. Matriks Hak Akses (Access Control Matrix)

| Fitur / Use Case | Pengunjung | Kader | Admin |
|---|:---:|:---:|:---:|
| Melakukan Pendaftaran | ✅ | ❌ | ❌ |
| Login | ❌ | ✅ | ✅ |
| Logout | ❌ | ✅ | ✅ |
| Mengelola Profil | ❌ | ✅ | ✅ |
| Melihat Riwayat Keaktifan | ❌ | ✅ | ❌ |
| Mencetak E-KTA | ❌ | ✅ | ❌ |
| Mengunduh E-Sertifikat | ❌ | ✅ | ❌ |
| Mengelola Kegiatan & Presensi | ❌ | ✅* | ✅ |
| Mengelola Arsip Dokumen | ❌ | ✅* | ✅ |
| Mengelola Data Anggota | ❌ | ❌ | ✅ |
| Memvalidasi Pendaftaran | ❌ | ❌ | ✅ |
| Mengelola E-Sertifikat | ❌ | ❌ | ✅ |
| Mencetak Laporan | ❌ | ❌ | ✅ |

> **Keterangan:** ✅* = Akses terbatas (Kader hanya dapat melihat, bukan mengelola penuh seperti Admin).

---

*Dokumen ini disusun berdasarkan analisis Use Case Diagram dan Activity Diagram dari rancangan sistem.*

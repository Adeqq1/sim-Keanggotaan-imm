# Pengujian Manual E-Sertifikat

Dokumen ini memetakan pengujian manual e-sertifikat dari pencatatan absensi sampai PDF diunduh oleh kader. Gunakan satu baris catatan hasil untuk setiap kasus agar bukti pengujian mudah dilacak.

## Ringkasan Alur Aktif

```text
Instruktur membuat/memilih sesi
    -> mencatat anggota sebagai Hadir
    -> memverifikasi kehadiran
    -> memberi nilai A-D jika kegiatan multi-sesi
Admin memilih kegiatan dan anggota
    -> sistem mengantrekan pembuatan sertifikat
Queue membuat PDF dan data sertifikat
    -> Admin memeriksa/mengunduh PDF
    -> Kader melihat dan mengunduh sertifikat miliknya
```

> **Catatan klaim:** fitur pengajuan/klaim sertifikat oleh kader sudah dinonaktifkan. Tidak ada tombol atau endpoint aktif untuk klaim. Data `status_klaim` dan `bukti_kehadiran` hanya data historis dan tidak menentukan kelayakan sertifikat. Pengujian klaim pada dokumen ini memastikan fitur lama tidak muncul dan tidak dapat digunakan.

## Aturan Kelayakan

| Kondisi | Satu sesi | Multi-sesi |
|---|---:|---:|
| Anggota aktif dan akun berperan sebagai kader | Wajib | Wajib |
| Status kehadiran | `hadir` | `hadir` pada setiap sesi yang dihitung |
| Status verifikasi | `terverifikasi` | `terverifikasi` |
| Waktu pemeriksaan terisi | Wajib | Wajib |
| Jumlah sesi unik terverifikasi | 1 | Sesuai minimum kegiatan, minimal 3 |
| Penilaian A-D | Tidak diperlukan | Wajib |
| Sertifikat kegiatan yang sama belum ada | Wajib | Wajib |

Presensi historis berstatus verifikasi `legacy` tetap dihitung. Status `pending`, `ditolak`, `izin`, dan `alfa` tidak dihitung.

## Ruang Lingkup dan Peran

| Peran | Aktivitas |
|---|---|
| Instruktur | Mengelola sesi, mencatat absensi, memverifikasi kehadiran, dan memberi nilai kegiatan multi-sesi |
| Admin | Melihat absensi, membuat e-sertifikat, memeriksa hasil, dan mengunduh semua sertifikat |
| Kader | Melihat riwayat, melihat sertifikat sendiri, dan mengunduh sertifikat sendiri yang kehadirannya masih memenuhi syarat |

## Persiapan Lingkungan

### Prasyarat

- [ ] Aplikasi dapat dibuka di <http://localhost:8000>.
- [ ] Container `app`, `db`, dan `queue` berjalan.
- [ ] Migrasi database sudah dijalankan.
- [ ] Tautan storage sudah dibuat.
- [ ] Tersedia masing-masing satu akun Admin, Instruktur, dan Kader.
- [ ] Akun Kader terhubung dengan data Anggota berstatus aktif.
- [ ] Password dan identitas akun uji dicatat di bagian Data Uji.

Perintah pemeriksaan:

```bash
docker compose ps
docker compose exec app php artisan migrate:status
docker compose exec app php artisan storage:link
docker compose exec app php artisan queue:failed
```

Jika database uji boleh dihapus seluruhnya, data awal dapat dibuat ulang dengan:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

> Perintah `migrate:fresh --seed` menghapus seluruh database yang sedang terhubung. Seeder utama membuat akun dengan email acak dan password `password`; lihat akun aktual melalui phpMyAdmin di <http://localhost:8080>. Data sertifikat bawaan dapat menunjuk file dummy, sehingga sertifikat untuk pengujian ini tetap harus dibuat melalui alur UI.

### Data Uji

| Data | Nilai |
|---|---|
| Nama/Email Admin |  |
| Nama/Email Instruktur |  |
| Nama/Email Kader |  |
| Nama/ID Anggota |  |
| Kegiatan satu sesi | `Uji Sertifikat Satu Sesi` |
| Kegiatan multi-sesi | `Uji Sertifikat Multi Sesi` |
| Minimum multi-sesi | `3` |
| Browser |  |
| Tanggal pengujian |  |
| Penguji |  |

### Format Pencatatan

Gunakan status `Lulus`, `Gagal`, atau `Tidak Diuji`.

| Kasus | Status | Bukti/tautan tangkapan layar | Catatan |
|---|---|---|---|
|  |  |  |  |

## A. Pengujian E2E Satu Sesi

### ES-01 - Membuat Kegiatan dan Sesi

**Pelaksana:** Admin atau Instruktur

1. [ ] Login sebagai Admin atau Instruktur.
2. [ ] Buka `/admin/kegiatan/create`.
3. [ ] Isi nama `Uji Sertifikat Satu Sesi`, tanggal/waktu, lokasi, dan field wajib lainnya.
4. [ ] Pilih jenis pelaksanaan `Satu Sesi`.
5. [ ] Simpan kegiatan.
6. [ ] Buka pengelolaan sesi kegiatan tersebut.
7. [ ] Tambahkan satu sesi dengan urutan `1`, nama sesi, dan waktu mulai.
8. [ ] Coba tambahkan sesi kedua.

**Hasil yang diharapkan:**

- [ ] Kegiatan berhasil dibuat.
- [ ] Minimum sesi terverifikasi menjadi `1`.
- [ ] Sesi pertama berhasil dibuat.
- [ ] Sesi kedua ditolak dengan pesan `Kegiatan satu sesi hanya boleh memiliki satu sesi.`

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### ES-02 - Mencatat Absensi

**Pelaksana:** Instruktur

1. [ ] Login sebagai Instruktur.
2. [ ] Buka `/admin/presensi`.
3. [ ] Pilih kegiatan dan sesi dari ES-01.
4. [ ] Pastikan anggota uji tampil dalam daftar anggota aktif.
5. [ ] Pilih `Hadir` untuk anggota uji.
6. [ ] Klik `Simpan Presensi`.
7. [ ] Muat ulang halaman.

**Hasil yang diharapkan:**

- [ ] Muncul pesan `Presensi berhasil disimpan.`
- [ ] Status anggota tetap `Hadir` setelah halaman dimuat ulang.
- [ ] Bagian verifikasi kehadiran tersedia.
- [ ] Status verifikasi awal adalah `pending`.

> Periksa setiap baris sebelum menyimpan. Anggota yang belum memiliki presensi menggunakan pilihan awal `alfa` pada UI dan ikut tersimpan saat tombol simpan ditekan.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### ES-03 - Memastikan Presensi Pending Belum Layak

**Pelaksana:** Admin

1. [ ] Biarkan verifikasi anggota pada status `pending`.
2. [ ] Login sebagai Admin.
3. [ ] Buka `/admin/sertifikat/create`.
4. [ ] Pilih kegiatan ES-01 dan anggota uji.
5. [ ] Kirim form pembuatan sertifikat.

**Hasil yang diharapkan:**

- [ ] Pembuatan ditolak.
- [ ] Form menampilkan bahwa anggota tidak memenuhi syarat.
- [ ] Tidak ada sertifikat baru pada `/admin/sertifikat`.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### ES-04 - Memverifikasi Kehadiran

**Pelaksana:** Instruktur

1. [ ] Kembali ke halaman presensi sesi.
2. [ ] Ubah status verifikasi anggota menjadi `Terverifikasi`.
3. [ ] Simpan keputusan.
4. [ ] Muat ulang halaman.

**Hasil yang diharapkan:**

- [ ] Muncul pesan `Keputusan verifikasi berhasil disimpan.`
- [ ] Status tetap `terverifikasi` setelah halaman dimuat ulang.
- [ ] Informasi pemeriksa dan waktu pemeriksaan terisi.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### ES-05 - Memastikan Klaim Kader Nonaktif

**Pelaksana:** Kader

1. [ ] Login sebagai Kader uji.
2. [ ] Buka `/kader/riwayat`.
3. [ ] Periksa baris riwayat kegiatan ES-01.
4. [ ] Buka `/kader/sertifikat`.
5. [ ] Pastikan tidak ada tombol atau form `Klaim Sertifikat`.
6. [ ] Jika menguji endpoint secara langsung, kirim `POST` ke `/kader/sertifikat/{id-presensi}/klaim` dengan alat HTTP yang menyertakan sesi dan CSRF yang valid.

**Hasil yang diharapkan:**

- [ ] Tidak ada aksi klaim pada halaman kader.
- [ ] Sebelum diterbitkan Admin, status menjelaskan sertifikat belum tersedia atau belum diterbitkan.
- [ ] Endpoint klaim lama menghasilkan `404`.
- [ ] Tidak ada sertifikat yang dibuat oleh langkah ini.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### ES-06 - Membuat Sertifikat oleh Admin

**Pelaksana:** Admin

1. [ ] Pastikan service `queue` berjalan melalui `docker compose ps`.
2. [ ] Login sebagai Admin.
3. [ ] Buka `/admin/sertifikat/create`.
4. [ ] Pilih kegiatan ES-01.
5. [ ] Pilih hanya anggota uji yang sudah terverifikasi.
6. [ ] Klik tombol pembuatan sertifikat satu kali.
7. [ ] Catat waktu pengiriman.

**Hasil yang diharapkan:**

- [ ] Admin diarahkan ke `/admin/sertifikat`.
- [ ] Muncul pesan `Sertifikat sedang dibuat di latar belakang.`
- [ ] Pesan hanya menandakan job berhasil diantrekan, bukan PDF sudah selesai.
- [ ] Setelah menunggu beberapa detik dan memuat ulang halaman, satu sertifikat baru muncul.
- [ ] Nomor mengikuti pola `CERT-{id-kegiatan}-{id-anggota}-{YYYYMMDD}`.
- [ ] Tidak ada duplikasi untuk pasangan kegiatan dan anggota tersebut.

Jika hasil tidak muncul, periksa:

```bash
docker compose logs queue
docker compose exec app php artisan queue:failed
```

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### ES-07 - Memeriksa PDF Satu Sesi sebagai Admin

**Pelaksana:** Admin

1. [ ] Klik unduh pada sertifikat yang baru dibuat.
2. [ ] Buka PDF menggunakan pembaca PDF.
3. [ ] Periksa jumlah halaman, orientasi, teks, dan tata letak.

**Hasil yang diharapkan:**

- [ ] Unduhan berhasil dan file dapat dibuka.
- [ ] PDF menggunakan A4 landscape.
- [ ] PDF hanya terdiri dari satu halaman.
- [ ] Nama kader, nama kegiatan, lokasi, tanggal, nomor sertifikat, dan nama instruktur/pimpinan benar.
- [ ] Tidak ada teks terpotong, tumpang tindih, atau karakter rusak.
- [ ] Background tampil sesuai pengaturan sertifikat.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### ES-08 - Melihat dan Mengunduh sebagai Kader

**Pelaksana:** Kader

1. [ ] Login sebagai Kader uji.
2. [ ] Buka `/kader/riwayat` dan periksa status kegiatan.
3. [ ] Buka `/kader/sertifikat`.
4. [ ] Cari sertifikat kegiatan ES-01.
5. [ ] Klik tombol unduh.

**Hasil yang diharapkan:**

- [ ] Riwayat menunjukkan sertifikat diterbitkan oleh Admin.
- [ ] Hanya sertifikat milik kader login yang tampil.
- [ ] Tombol unduh tersedia karena kehadiran kegiatan masih memenuhi syarat.
- [ ] PDF berhasil diunduh dan sama dengan hasil unduhan Admin.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

## B. Pengujian E2E Multi-Sesi

### EM-01 - Membuat Kegiatan Multi-Sesi

**Pelaksana:** Admin atau Instruktur

1. [ ] Buat kegiatan `Uji Sertifikat Multi Sesi`.
2. [ ] Pilih jenis `Multi Sesi`.
3. [ ] Isi minimum sesi terverifikasi `3`.
4. [ ] Buat tiga sesi berbeda dengan urutan `1`, `2`, dan `3`.

**Hasil yang diharapkan:**

- [ ] Kegiatan dan ketiga sesi berhasil dibuat.
- [ ] Minimum kurang dari `3` ditolak jika dicoba.
- [ ] Urutan sesi yang sama dalam kegiatan yang sama ditolak.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### EM-02 - Mencatat dan Memverifikasi Tiga Sesi

**Pelaksana:** Instruktur

1. [ ] Pada sesi 1, catat anggota uji sebagai `Hadir`, simpan, lalu verifikasi.
2. [ ] Pada sesi 2, catat anggota uji sebagai `Hadir`, simpan, lalu verifikasi.
3. [ ] Pada sesi 3, catat anggota uji sebagai `Hadir`, simpan, tetapi biarkan verifikasi `pending`.
4. [ ] Buka halaman penilaian kegiatan.

**Hasil yang diharapkan sebelum sesi 3 diverifikasi:**

- [ ] Anggota belum muncul sebagai peserta yang dapat dinilai.
- [ ] Admin belum dapat membuat sertifikat untuk anggota tersebut.

Lanjutkan:

5. [ ] Verifikasi kehadiran sesi 3 menjadi `terverifikasi`.
6. [ ] Buka kembali halaman penilaian.

**Hasil yang diharapkan setelah sesi 3 diverifikasi:**

- [ ] Anggota muncul pada daftar penilaian.
- [ ] Jumlah sesi yang dihitung adalah tiga sesi unik pada kegiatan ini.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### EM-03 - Memastikan Nilai Wajib

**Pelaksana:** Admin dan Instruktur

1. [ ] Sebelum memberi nilai, login sebagai Admin.
2. [ ] Coba buat sertifikat untuk kegiatan EM-01 dan anggota uji.
3. [ ] Pastikan pembuatan ditolak.
4. [ ] Login sebagai Instruktur.
5. [ ] Buka halaman penilaian kegiatan.
6. [ ] Pilih nilai, misalnya `B - Bagus`.
7. [ ] Simpan penilaian.

**Hasil yang diharapkan:**

- [ ] Sertifikat tidak dapat dibuat tanpa nilai.
- [ ] Setelah disimpan, muncul pesan `Penilaian berhasil disimpan.`
- [ ] Nilai yang dipilih tetap tampil setelah halaman dimuat ulang.
- [ ] Nilai A, B, C, dan D semuanya merupakan nilai valid.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### EM-04 - Membuat dan Memeriksa PDF Multi-Sesi

**Pelaksana:** Admin dan Kader

1. [ ] Login sebagai Admin.
2. [ ] Buat sertifikat untuk kegiatan EM-01 dan anggota uji.
3. [ ] Tunggu queue selesai lalu muat ulang daftar sertifikat.
4. [ ] Unduh dan buka PDF.
5. [ ] Login sebagai Kader dan unduh PDF yang sama.

**Hasil yang diharapkan:**

- [ ] Hanya satu sertifikat dibuat.
- [ ] PDF menggunakan A4 landscape dan terdiri dari dua halaman.
- [ ] Halaman pertama berisi penghargaan kegiatan.
- [ ] Halaman kedua menampilkan nilai snapshot, misalnya `B - Bagus`.
- [ ] Nama, kegiatan, nomor, tanggal, dan tata letak benar pada kedua halaman.
- [ ] Kader dapat melihat dan mengunduh sertifikat miliknya.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

## C. Pengujian Negatif dan Keamanan

### EN-01 - Status Selain Hadir Tidak Layak

1. [ ] Uji anggota dengan status `izin`.
2. [ ] Uji anggota dengan status `alfa`.
3. [ ] Coba menetapkan `terverifikasi` pada presensi tersebut.
4. [ ] Coba buat sertifikat sebagai Admin.

**Hasil yang diharapkan:** verifikasi non-hadir ditolak dengan pesan `Hanya presensi hadir yang dapat diverifikasi atau ditolak.`, dan sertifikat tidak dibuat.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### EN-02 - Verifikasi Ditolak Tidak Layak

1. [ ] Catat anggota sebagai `Hadir`.
2. [ ] Ubah verifikasi menjadi `Ditolak`.
3. [ ] Coba buat sertifikat.

**Hasil yang diharapkan:** anggota dinyatakan tidak memenuhi syarat dan sertifikat tidak dibuat.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### EN-03 - Anggota Nonaktif atau Bukan Kader

1. [ ] Uji anggota nonaktif.
2. [ ] Uji anggota aktif yang akunnya bukan role `kader`, jika tersedia.
3. [ ] Coba membuat sertifikat.

**Hasil yang diharapkan:** anggota tidak dapat dipilih atau ditolak oleh validasi server; tidak ada sertifikat baru.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### EN-04 - Batch Campuran Harus Gagal Seluruhnya

1. [ ] Siapkan satu anggota yang memenuhi syarat.
2. [ ] Siapkan satu anggota yang tidak memenuhi syarat.
3. [ ] Pilih keduanya dalam satu pengiriman form.

**Hasil yang diharapkan:** seluruh batch ditolak, nama anggota yang tidak layak disebutkan, dan anggota yang layak juga belum mendapat sertifikat dari batch tersebut.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### EN-05 - Penerbitan Duplikat

1. [ ] Pilih pasangan kegiatan dan anggota yang sertifikatnya sudah berhasil dibuat.
2. [ ] Coba buat ulang sertifikat.
3. [ ] Muat ulang daftar Admin dan Kader.

**Hasil yang diharapkan:** pengiriman ditolak atau diproses tanpa membuat duplikat; jumlah sertifikat dan file canonical tetap satu.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### EN-06 - Otorisasi Berdasarkan Peran

1. [ ] Sebagai Admin, coba kirim penyimpanan presensi atau verifikasi melalui URL langsung.
2. [ ] Sebagai Instruktur, coba buka `/admin/sertifikat/create`.
3. [ ] Sebagai Kader, coba buka `/admin/sertifikat`.
4. [ ] Sebagai tamu, buka salah satu halaman tersebut.

**Hasil yang diharapkan:** role yang tidak berwenang menerima `403`; tamu diarahkan ke login; Admin tetap dapat melihat presensi secara read-only.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### EN-07 - Kader Tidak Boleh Mengunduh Sertifikat Orang Lain

1. [ ] Catat URL unduh sertifikat milik kader lain.
2. [ ] Login sebagai Kader uji.
3. [ ] Buka URL tersebut secara langsung.

**Hasil yang diharapkan:** server menghasilkan `403` dan file tidak terunduh.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### EN-08 - Unduhan Terkunci Setelah Kehadiran Tidak Lagi Layak

1. [ ] Setelah sertifikat terbit, ubah salah satu presensi wajib menjadi `pending`, `ditolak`, `izin`, atau `alfa`.
2. [ ] Login sebagai Kader dan buka `/kader/sertifikat`.
3. [ ] Coba URL unduh sertifikat secara langsung.
4. [ ] Login sebagai Admin dan coba unduh sertifikat yang sama.

**Hasil yang diharapkan:** tombol unduh Kader terkunci/hilang dan URL Kader menghasilkan `403`; Admin masih dapat mengunduh file selama file tersedia.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### EN-09 - Kelayakan Berubah Saat Job Mengantre

1. [ ] Hentikan sementara worker dengan `docker compose stop queue`.
2. [ ] Buat sertifikat untuk anggota yang saat itu memenuhi syarat.
3. [ ] Sebelum worker dijalankan, ubah verifikasi menjadi `ditolak` atau nonaktifkan anggota.
4. [ ] Jalankan worker kembali dengan `docker compose start queue`.
5. [ ] Muat ulang daftar sertifikat dan periksa log queue/aplikasi.

**Hasil yang diharapkan:** job memeriksa ulang kelayakan, sertifikat tidak dibuat, dan log mencatat bahwa generation dilewati. UI tidak memiliki status job gagal per sertifikat, sehingga pemeriksaan log diperlukan.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### EN-10 - Worker Mati dan Pemulihan Queue

1. [ ] Hentikan worker queue.
2. [ ] Kirim pembuatan sertifikat yang valid.
3. [ ] Pastikan toast latar belakang muncul tetapi sertifikat belum tampil.
4. [ ] Jalankan kembali worker queue.
5. [ ] Tunggu dan muat ulang daftar sertifikat.

**Hasil yang diharapkan:** job tetap mengantre saat worker mati dan sertifikat dibuat setelah worker aktif kembali. Jika gagal tiga kali, job dapat diperiksa dengan `php artisan queue:failed`.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### EN-11 - File Sertifikat Hilang

1. [ ] Gunakan sertifikat khusus pengujian.
2. [ ] Catat path file sebelum pengujian.
3. [ ] Pindahkan sementara file dari `storage/app/public/sertifikat/` di lingkungan disposable.
4. [ ] Coba unduh sebagai Admin dan sebagai Kader.
5. [ ] Kembalikan file setelah pengujian.

**Hasil yang diharapkan:** kedua endpoint unduh menghasilkan `404`, bukan file kosong atau error server `500`.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

## D. Pengujian Pengaturan Tampilan PDF

### EP-01 - PDF Tanpa Background

1. [ ] Login sebagai Admin dan buka `/admin/sertifikat/settings`.
2. [ ] Nonaktifkan penggunaan background dan simpan.
3. [ ] Buat sertifikat untuk pasangan kegiatan/anggota baru yang layak.
4. [ ] Buka PDF.

**Hasil yang diharapkan:** muncul pesan `Pengaturan sertifikat berhasil diperbarui.` dan PDF baru dibuat tanpa background tetapi tetap terbaca dan tertata.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

### EP-02 - Upload Background

1. [ ] Unggah JPG atau PNG valid berukuran maksimum 4 MB.
2. [ ] Aktifkan background dan simpan.
3. [ ] Buat sertifikat baru lalu buka PDF.
4. [ ] Coba unggah file non-gambar atau gambar lebih dari 4 MB.

**Hasil yang diharapkan:** background valid tampil pada PDF baru; file tidak valid ditolak dengan pesan validasi; sertifikat lama tidak dibuat ulang secara otomatis.

**Hasil aktual:** Status: .......... Bukti: .......... Catatan: ..........

## E. Kriteria Selesai

Pengujian dinyatakan selesai jika:

- [ ] Alur satu sesi lulus dari absensi sampai unduhan Kader.
- [ ] Alur multi-sesi lulus dan PDF menampilkan nilai pada halaman kedua.
- [ ] Presensi pending, ditolak, izin, dan alfa tidak dapat menghasilkan sertifikat.
- [ ] Klaim legacy tidak tersedia dan endpoint lamanya menghasilkan `404`.
- [ ] Satu kegiatan dan satu anggota hanya menghasilkan satu sertifikat.
- [ ] Queue dapat memproses job dan kegagalan dapat ditemukan melalui log/failed jobs.
- [ ] Admin dan Kader dapat mengunduh sesuai kewenangannya.
- [ ] Kader tidak dapat melihat atau mengunduh sertifikat anggota lain.
- [ ] Isi dan tata letak PDF sudah diperiksa secara visual.
- [ ] Seluruh kasus gagal memiliki bukti dan catatan reproduksi.

## Ringkasan Hasil

| Kelompok | Lulus | Gagal | Tidak Diuji | Catatan |
|---|---:|---:|---:|---|
| Persiapan |  |  |  |  |
| E2E satu sesi |  |  |  |  |
| E2E multi-sesi |  |  |  |  |
| Negatif dan keamanan |  |  |  |  |
| Tampilan PDF |  |  |  |  |
| **Total** |  |  |  |  |

## Referensi Implementasi

- Route: `routes/web.php`
- Presensi: `app/Http/Controllers/PresensiController.php`
- Penilaian: `app/Http/Controllers/PenilaianKegiatanController.php`
- Kelayakan: `app/Services/CertificateEligibility.php` dan `app/Services/VerifiedAttendance.php`
- Generate dan download: `app/Http/Controllers/SertifikatController.php`
- Queue: `app/Jobs/GenerateCertificateJob.php` dan `compose.yaml`
- Template PDF: `resources/views/pdf/sertifikat.blade.php`

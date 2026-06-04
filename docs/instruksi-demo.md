# Instruksi Demo & Indikator Keberhasilan

## Sistem Informasi Manajemen Keanggotaan dan Kearsipan IMM

**Versi Dokumen:** 1.0
**Tanggal:** 19 Mei 2026
**Tujuan:** Panduan resmi pelaksanaan demo aplikasi kepada klien, lengkap dengan skenario uji, indikator keberhasilan per fitur, dan rekomendasi pengembangan lanjutan.

---

## 1. Ringkasan Eksekutif

Dokumen ini disusun sebagai **rujukan formal** untuk demo aplikasi SIM-Keanggotaan IMM bersama klien (LPAIK / pengurus IMM). Dokumen mencakup tiga bagian utama:

1. **Persiapan demo** — pra-syarat teknis, akun, dan data uji yang harus disiapkan sebelum sesi demo dimulai.
2. **Skenario demo** — alur peragaan fitur secara berurutan untuk tiga aktor (Pengunjung, Admin, Kader), lengkap dengan langkah konkret dan **indikator keberhasilan** pada setiap titik.
3. **Saran perbaikan** — daftar peningkatan teknis & fungsional yang direkomendasikan untuk fase pengembangan berikutnya.

Demo dinyatakan **berhasil** apabila seluruh skenario inti pada Bagian 4 selesai tanpa error fatal dan seluruh indikator keberhasilan tercapai.

---

## 2. Pra-Syarat Teknis Sebelum Demo

### 2.1 Lingkungan Aplikasi

| Komponen | Kondisi yang Harus Dipenuhi |
|----------|----------------------------|
| Server lokal (Laragon) | Apache + MySQL **berjalan** (status hijau di Laragon) |
| URL akses | `http://sim-keanggotaan-imm.test` dapat diakses dari browser host & smartphone (jika satu jaringan) |
| Database | Migrasi & seeder sudah dijalankan: `php artisan migrate:fresh --seed` |
| Asset frontend | Sudah di-build: `npm run build` (atau `npm run dev` aktif untuk live mode) |
| Symbolic link storage | Sudah dibuat: `php artisan storage:link` |
| Test suite | `php artisan test --compact` → **47 passed** |
| Browser | Chrome / Edge versi terbaru (untuk demo Desktop) + simulator mobile (DevTools) |
| Smartphone | Disarankan satu unit tersedia untuk demo PWA dan tampilan mobile asli |

### 2.2 Akun Demo Default

Akun berikut tersedia setelah `db:seed` dijalankan dan **wajib** digunakan saat demo (tidak perlu register manual):

| Peran | Email | Password | Keterangan |
|-------|-------|----------|------------|
| **Admin** | `admin@admin.com` | `password` | Akses penuh seluruh modul |
| **Kader** | `kader@example.com` | `password` | Profil sudah terisi, NIA `1234567890` |

> Jika klien meminta uji coba data baru, pengurus dapat mendemonstrasikan fitur registrasi (lihat Skenario 4.1) dan memvalidasinya dari sisi Admin.

### 2.3 Data Uji yang Sudah Tersedia (Hasil Seeder)

| Modul | Data Awal |
|-------|-----------|
| Users | 1 Admin + 1 Kader |
| Anggota | 1 record (terhubung ke akun Kader) |
| Kegiatan | 2 record: *Darul Arqam Dasar (DAD)* (akan datang) & *Kajian Rutin Mingguan* (sudah lewat) |
| Presensi | Otomatis ter-generate untuk seluruh anggota terhadap setiap kegiatan |
| Sertifikat | Kosong → akan diisi saat skenario demo Admin |
| Arsip | Kosong → akan diisi saat skenario demo |

### 2.4 Checklist Pra-Demo (H-1)

- [ ] Pull versi terbaru: `git pull origin main`
- [ ] Install dependency: `composer install` & `npm install`
- [ ] Reset database & seeder: `php artisan migrate:fresh --seed`
- [ ] Build asset: `npm run build`
- [ ] Clear cache: `php artisan optimize:clear`
- [ ] Jalankan test: `php artisan test --compact` → pastikan **47 passed**
- [ ] Buka URL `http://sim-keanggotaan-imm.test` → halaman landing tampil normal
- [ ] Login Admin → dashboard tampil tanpa error
- [ ] Login Kader → dashboard tampil tanpa error
- [ ] Siapkan **2 file dummy** untuk demo upload arsip (`.pdf` ≤ 2MB, `.jpg` ≤ 2MB)
- [ ] Siapkan **1 foto profil** untuk demo update profil (`.jpg` / `.png` ≤ 2MB)
- [ ] Pastikan koneksi internet stabil (untuk asset eksternal seperti UI-Avatars)

---

## 3. Format Indikator Keberhasilan

Setiap langkah pada skenario demo memiliki tiga jenis indikator yang dipakai sebagai rujukan formal:

| Simbol | Arti |
|--------|------|
| 🎯 **Aksi** | Tindakan yang dilakukan presenter pada UI |
| ✅ **Indikator Berhasil** | Bukti visual / fungsional bahwa langkah berhasil |
| 📊 **Bukti Verifikasi** | Konfirmasi tambahan yang dapat dicek (record DB, file di storage, dst.) |

Demo **dinyatakan berhasil per langkah** ketika semua indikator ✅ tercapai.

---

## 4. Skenario Demo (Alur Lengkap)

> **Urutan demo direkomendasikan:** Pengunjung → Admin (validasi) → Kader (anggota baru). Alur ini menunjukkan siklus penuh dari registrasi sampai penerbitan sertifikat, sehingga klien melihat sistem bekerja end-to-end.

---

### 4.1 Skenario A — Pengunjung Mendaftar sebagai Calon Anggota

**Aktor:** Pengunjung publik
**Tujuan:** Membuktikan sistem dapat menerima pendaftaran online dan menyimpannya dengan status `pending`.

| # | 🎯 Aksi | ✅ Indikator Berhasil | 📊 Bukti Verifikasi |
|---|--------|---------------------|---------------------|
| 1 | Buka URL `http://sim-keanggotaan-imm.test` | Halaman landing tampil dengan tombol **"Daftar Sekarang"** dan **"Login"** | URL aktif, tidak ada error 500 |
| 2 | Klik **Daftar Sekarang** | Halaman form registrasi (`/pendaftaran`) terbuka, menampilkan field nama, email, TTL, no. telp, alamat, file persyaratan | Field wajib bertanda `*` |
| 3 | Isi form dengan data uji baru, contoh: <br>• Nama: *Calon Kader Demo* <br>• Email: `caloncoba@example.com` <br>• TTL: *Yogyakarta, 2002-08-17* <br>• Lampirkan file `.pdf` ≤ 2MB | Validasi client-side aktif (field merah jika dikosongkan) | Tidak ada field yang lolos kosong |
| 4 | Klik **Kirim Pendaftaran** | Redirect ke halaman sukses dengan pesan: *"Pendaftaran berhasil, silakan tunggu validasi dari Admin."* | Pesan sukses muncul, URL berubah ke `/pendaftaran/sukses` |
| 5 | (Opsional) Verifikasi DB | — | Record baru muncul di tabel `pendaftaran` dengan `status_validasi = 'pending'` |

**🟢 Hasil Akhir Skenario A:** Pendaftaran tersimpan dan menunggu validasi.

---

### 4.2 Skenario B — Admin Memvalidasi Pendaftaran

**Aktor:** Admin
**Tujuan:** Membuktikan Admin dapat menyetujui calon anggota dan akun Kader otomatis terbuat.

| # | 🎯 Aksi | ✅ Indikator Berhasil | 📊 Bukti Verifikasi |
|---|--------|---------------------|---------------------|
| 1 | Buka `/login`, login sebagai `admin@admin.com` / `password` | Redirect ke `/admin/dashboard` | Bottom-nav khusus Admin (Dashboard, Anggota, Kegiatan, Arsip, Lainnya) tampil |
| 2 | Dashboard menampilkan kartu statistik | Total Anggota, Total Kegiatan, Pendaftar Pending tampil dengan angka ≥ 1 | Angka pendaftar pending = 1 (data dari Skenario A) |
| 3 | Buka menu **Pendaftaran** (`/admin/pendaftaran`) | Tabel berisi nama *Calon Kader Demo* dengan badge `pending` | Tombol **Detail** aktif |
| 4 | Klik **Detail** pada baris tersebut | Halaman detail menampilkan seluruh data pendaftar + link file persyaratan | File dapat dibuka/diunduh |
| 5 | Klik **Setujui** | Modal konfirmasi muncul → klik **Ya** → flash message sukses muncul | Status berubah `disetujui` |
| 6 | Verifikasi otomatisasi | Record baru muncul di tabel `users` (role: kader) dan tabel `anggota` dengan NIA ter-generate | Akun Kader siap login |
| 7 | Buka menu **Anggota** (`/admin/anggota`) | Anggota baru *Calon Kader Demo* tampil di tabel | Tombol Detail / Edit / Hapus aktif (3 dots) |
| 8 | (Skenario alternatif) Pada pendaftar lain, klik **Tolak** | Status berubah `ditolak`, pendaftar tidak lagi tampil di antrean default | Badge berubah, flash message muncul |

**🟢 Hasil Akhir Skenario B:** Akun Kader baru terbentuk dan siap digunakan.

---

### 4.3 Skenario C — Admin Mengelola Kegiatan & Mencatat Presensi

**Aktor:** Admin
**Tujuan:** Membuktikan alur pembuatan kegiatan dan pencatatan presensi massal berjalan utuh.

| # | 🎯 Aksi | ✅ Indikator Berhasil | 📊 Bukti Verifikasi |
|---|--------|---------------------|---------------------|
| 1 | Buka menu **Kegiatan** | Tabel menampilkan 2 kegiatan seeder + indikator status (lampau/akan datang) | Tombol **Tambah Kegiatan** terlihat |
| 2 | Klik **Tambah Kegiatan**, isi: <br>• Nama: *Bedah Buku Demo* <br>• Tanggal/Waktu: hari ini <br>• Lokasi: *Sekretariat IMM* | Kegiatan baru tersimpan, redirect ke index, flash message sukses | Data muncul di tabel paling atas |
| 3 | Klik tombol **Presensi** pada kegiatan *Kajian Rutin Mingguan* | Form presensi menampilkan daftar seluruh anggota aktif dengan radio Hadir/Izin/Alfa | Setiap anggota memiliki tiga opsi |
| 4 | Tandai status berbeda untuk tiap anggota → klik **Simpan** | Flash message sukses, redirect ke daftar kegiatan | Data presensi tersimpan |
| 5 | Verifikasi DB | Tabel `presensi` ter-update sesuai pilihan | `status_kehadiran` sesuai input |

**🟢 Hasil Akhir Skenario C:** Kegiatan baru terdaftar dan presensi tercatat akurat.

---

### 4.4 Skenario D — Admin Menerbitkan E-Sertifikat

**Aktor:** Admin
**Tujuan:** Membuktikan sistem dapat men-generate sertifikat PDF untuk Kader peserta kegiatan.

| # | 🎯 Aksi | ✅ Indikator Berhasil | 📊 Bukti Verifikasi |
|---|--------|---------------------|---------------------|
| 1 | Buka menu **Lainnya → Sertifikat** | Halaman daftar sertifikat tampil (kosong di awal) | Tombol **Buat Sertifikat** aktif |
| 2 | Klik **Buat Sertifikat** | Form muncul dengan dropdown kegiatan + multi-select Kader | Daftar Kader sesuai data anggota aktif |
| 3 | Pilih kegiatan *Kajian Rutin Mingguan* + centang minimal 1 Kader → klik **Generate** | Proses ±2 detik → flash message sukses → daftar sertifikat berisi record baru | Nomor sertifikat ter-generate unik |
| 4 | Klik **Unduh** pada salah satu sertifikat | File `sertifikat-*.pdf` ter-download | PDF berisi nama Kader, nama kegiatan, nomor sertifikat |
| 5 | Verifikasi storage | File tersimpan di `storage/app/public/sertifikat/` | Path konsisten dengan kolom `file_sertifikat` |

**🟢 Hasil Akhir Skenario D:** Sertifikat digital tersedia untuk diunduh oleh Kader bersangkutan.

---

### 4.5 Skenario E — Admin Mengelola Arsip & Mencetak Laporan

**Aktor:** Admin
**Tujuan:** Membuktikan modul arsip & laporan ekspor PDF/Excel berjalan.

| # | 🎯 Aksi | ✅ Indikator Berhasil | 📊 Bukti Verifikasi |
|---|--------|---------------------|---------------------|
| 1 | Buka menu **Arsip** | Daftar arsip tampil (kosong di awal) | Form upload tersedia |
| 2 | Upload 1 file `.pdf` ≤ 2MB dengan judul *SK Pengurus 2026* dan kategori *Surat Keputusan* | Flash message sukses, file muncul di tabel | Tombol **Unduh** aktif |
| 3 | Klik **Unduh** | File asli ter-download dengan benar | Hash/isi file identik |
| 4 | Coba upload file dengan ekstensi tidak diizinkan (mis. `.exe`) | Sistem menolak dengan pesan validasi | Tidak ada record baru di DB |
| 5 | Buka menu **Laporan** | Form filter (rentang tanggal + jenis laporan) tampil | Dropdown jenis laporan terisi |
| 6 | Pilih jenis *Data Anggota*, klik **Export PDF** | File `laporan-anggota-*.pdf` ter-download dengan tabel data | PDF terbuka di reader |
| 7 | Pilih jenis *Presensi*, klik **Export Excel** | File `.xlsx` ter-download, dapat dibuka di Excel/LibreOffice | Sheet berisi kolom yang sesuai |

**🟢 Hasil Akhir Skenario E:** Arsip terkelola dan laporan dapat diekspor dalam dua format.

---

### 4.6 Skenario F — Kader Login & Mengakses Fitur Pribadi

**Aktor:** Kader (gunakan akun seeder `kader@example.com` / `password`)
**Tujuan:** Membuktikan pemisahan hak akses dan fitur khusus Kader berjalan.

| # | 🎯 Aksi | ✅ Indikator Berhasil | 📊 Bukti Verifikasi |
|---|--------|---------------------|---------------------|
| 1 | Logout dari Admin → login ulang dengan akun Kader | Redirect ke `/kader/dashboard` | Bottom-nav khusus Kader (Dashboard, E-KTA, Sertifikat, Riwayat, Lainnya) tampil |
| 2 | Buka menu **E-KTA** | Halaman menampilkan kartu anggota digital dengan foto, NIA, nama, TTL | Tombol **Unduh PDF** aktif |
| 3 | Klik **Unduh PDF** | File `e-kta-*.pdf` ter-download, layout sesuai template | KTA berisi data profil yang benar |
| 4 | Buka menu **Sertifikat** | Daftar sertifikat milik Kader tampil (hasil Skenario D) | Tombol **Unduh** aktif |
| 5 | Klik **Unduh** | File PDF sertifikat ter-download | Identik dengan file di sisi Admin |
| 6 | Buka menu **Riwayat** | Tabel riwayat presensi + grafik statistik H/I/A tampil | Persentase kehadiran konsisten dengan data |
| 7 | Buka menu **Profil** | Form profil pre-filled, tombol upload foto aktif | Foto sebelumnya tampil sebagai preview |
| 8 | Update nomor telepon → klik **Simpan** | Flash message sukses, data ter-update | Field nomor telepon menampilkan nilai baru |

**🟢 Hasil Akhir Skenario F:** Fitur khusus Kader berfungsi dan data profil dapat dikelola sendiri.

---

### 4.7 Skenario G — Pengujian Keamanan & Hak Akses

**Aktor:** Admin & Kader
**Tujuan:** Membuktikan otorisasi role berfungsi (sesuai matriks akses pada `requirements.md`).

| # | 🎯 Aksi | ✅ Indikator Berhasil |
|---|--------|---------------------|
| 1 | Login sebagai Kader, akses URL `/admin/dashboard` secara langsung | Sistem menolak (HTTP 403 / redirect) |
| 2 | Login sebagai Admin, akses URL `/kader/ekta` | Sistem menolak (HTTP 403 / redirect) |
| 3 | Logout → akses URL `/admin/anggota` tanpa login | Redirect ke halaman login |
| 4 | (Sudah dibuktikan via test otomatis) Submit form tanpa CSRF token | Form ditolak (HTTP 419) |

**🟢 Hasil Akhir Skenario G:** Pemisahan hak akses berjalan sesuai spesifikasi.

---

### 4.8 Skenario H — Demo PWA & Tampilan Mobile

**Aktor:** Presenter (idealnya menggunakan smartphone)
**Tujuan:** Membuktikan aplikasi berjalan layaknya aplikasi mobile native.

| # | 🎯 Aksi | ✅ Indikator Berhasil |
|---|--------|---------------------|
| 1 | Buka aplikasi di smartphone (browser Chrome/Safari) | Layout mobile-first tampil, tidak ada scroll horizontal |
| 2 | Buka menu Chrome **Add to Home Screen** | Ikon aplikasi tertambah di home screen smartphone |
| 3 | Buka aplikasi dari home screen | Tampil fullscreen tanpa address bar (mode standalone) |
| 4 | Coba navigasi via Bottom Navigation Bar | Setiap tombol berukuran cukup besar (touch-friendly), highlight aktif berfungsi |

**🟢 Hasil Akhir Skenario H:** Aplikasi terinstal sebagai PWA dan terasa seperti aplikasi native.

---

## 5. Indikator Keberhasilan Demo (Ringkasan Eksekutif)

Demo dianggap **berhasil dipresentasikan** apabila seluruh poin berikut tercapai:

| # | Kriteria | Status |
|---|----------|--------|
| 1 | Aplikasi dapat diakses tanpa error 500 selama sesi demo | ☐ |
| 2 | Skenario A–F selesai tanpa error fatal | ☐ |
| 3 | Validasi role (Skenario G) berhasil membedakan akses Admin & Kader | ☐ |
| 4 | Minimal 1 dokumen PDF (E-KTA / Sertifikat / Laporan) berhasil diunduh dan dibuka oleh klien | ☐ |
| 5 | Aplikasi dapat ditampilkan dalam mode mobile (PWA) atau di simulator mobile | ☐ |
| 6 | Pertanyaan klien terhadap fitur inti dapat dijawab dengan demonstrasi langsung pada UI | ☐ |
| 7 | Tidak ada data uji yang hilang setelah demo selesai (database tetap konsisten) | ☐ |

> Disarankan satu anggota tim mencatat hasil tiap kriteria selama demo berlangsung sebagai berita acara.

---

## 6. Troubleshooting Saat Demo (Plan B)

Persiapkan langkah berikut bila terjadi kendala di tengah demo:

| Masalah | Penanganan Cepat |
|---------|------------------|
| Halaman menampilkan error 500 | Buka terminal, jalankan `php artisan optimize:clear`, refresh halaman |
| Tampilan CSS rusak | Jalankan `npm run build` ulang, atau pastikan `npm run dev` masih aktif |
| File PDF tidak ter-generate | Cek log `storage/logs/laravel.log`; pastikan ekstensi PHP `gd`/`mbstring` aktif |
| Login gagal terus-menerus | Jalankan ulang seeder: `php artisan migrate:fresh --seed` |
| Data demo terlanjur "kotor" | Reset cepat: `php artisan migrate:fresh --seed` (durasi ±10 detik) |
| Storage symlink hilang | `php artisan storage:link` |
| Asset tidak muncul saat diakses via tunnel (Ngrok) | Pastikan middleware `ForceHttpsForTunnel` aktif (sudah ada di project) |

---

## 7. Saran Perbaikan & Roadmap Berikutnya

Berikut rekomendasi peningkatan yang dapat dipertimbangkan setelah demo, dikelompokkan berdasarkan prioritas.

### 7.1 Prioritas Tinggi (Pra-Produksi)

1. **Notifikasi email otomatis**
   Saat ini approval/penolakan pendaftaran tidak mengirim notifikasi. Rekomendasi: gunakan **Laravel Notifications + Mailable** untuk mengirim email otomatis ke calon Kader saat status berubah.
2. **Lengkapi seeder produksi**
   `KegiatanSeeder` & `AnggotaSeeder` saat ini hanya berisi 1–2 record. Untuk demo skala lebih besar atau UAT, tambahkan factory + state agar mudah men-generate ratusan record realistis.
3. **Audit log / activity log**
   Modul Admin (terutama validasi pendaftaran, hapus anggota, hapus arsip) belum mencatat siapa melakukan aksi apa. Rekomendasi: tambahkan package `spatie/laravel-activitylog` dan tampilkan riwayat di dashboard Admin.
4. **Soft delete pada modul kritis**
   Saat ini `destroy()` melakukan hard delete pada `anggota`, `kegiatan`, `arsip`. Aktifkan `SoftDeletes` agar data dapat dipulihkan dan jejak audit terjaga.
5. **Konfirmasi password sebelum aksi destruktif**
   Tambahkan modal "Masukkan password Anda" sebelum hapus anggota / hapus arsip / tolak pendaftaran agar lebih aman bila perangkat tertinggal aktif.
6. **PWA: ikon resmi & offline fallback**
   `progress-overview.md` mencatat ikon PWA masih placeholder (UI-Avatars). Sebelum produksi, siapkan ikon ukuran 72px–512px dan halaman offline minimal (`offline.blade.php`).

### 7.2 Prioritas Menengah (Quality of Life)

7. **Search & filter pada tabel data**
   Modul `anggota`, `kegiatan`, `arsip`, dan `pendaftaran` perlu fitur pencarian (server-side) dan filter (status, kategori, tanggal). Tambahkan paginasi dengan `simplePaginate()` untuk performa.
8. **Dashboard analitik lebih kaya**
   Dashboard Admin saat ini hanya menampilkan total. Tambahkan grafik tren pendaftaran bulanan, persentase kehadiran rata-rata, dan kegiatan terdekat.
9. **Bulk action**
   Untuk validasi pendaftaran dan generate sertifikat, sediakan opsi *select all* + bulk approve / bulk generate agar Admin tidak mengerjakan satu-per-satu.
10. **Versi cetak laporan yang lebih bersih**
    Template `laporan.blade.php` saat ini cukup minimal. Tambahkan kop surat IMM, tanda tangan ketua, dan halaman cover untuk laporan resmi.
11. **Pengingat kegiatan & jadwal otomatis**
    Tambahkan scheduler (`app/Console/Kernel.php` / `routes/console.php`) yang mengirim reminder H-1 sebelum kegiatan via email atau notifikasi WhatsApp (via webhook).
12. **Validasi NIA otomatis**
    Saat ini NIA harus diisi manual. Buat helper untuk men-generate NIA berdasarkan format standar IMM (mis. `IMM-{tahun}-{nomor urut}`).

### 7.3 Prioritas Rendah (Polish & Future Ideas)

13. **Tema gelap (dark mode)**
    Mahasiswa cenderung membuka aplikasi di malam hari. Tambahkan toggle dark mode menggunakan class `data-bs-theme="dark"` Bootstrap 5.
14. **Multi-bahasa**
    Saat ini hanya tersedia Bahasa Indonesia. Pertimbangkan dukungan Bahasa Arab atau Inggris untuk dokumen yang akan dipublikasikan ke jaringan IMM internasional.
15. **Integrasi penyimpanan cloud (S3 / R2)**
    Storage saat ini menggunakan disk lokal. Bila adopsi membesar, migrasikan ke object storage agar tidak bergantung pada VPS.
16. **API publik (read-only)**
    Sediakan endpoint API (Sanctum + Eloquent Resources) agar data publik (kegiatan, sertifikat valid) dapat diakses dari sistem lain.
17. **Verifikasi sertifikat via QR Code**
    Tempelkan QR Code pada sertifikat PDF yang mengarah ke endpoint publik `cek-sertifikat/{nomor}` untuk memvalidasi keasliannya.
18. **Tambah peran (role) Sekretaris / Bendahara**
    Saat ini hanya ada Admin & Kader. Strukur organisasi IMM seringkali membutuhkan peran perantara dengan akses parsial.

### 7.4 Hutang Teknis (Technical Debt) yang Sebaiknya Dilunasi

19. **Konsolidasi dokumentasi**
    Saat ini ada beberapa file informal di root proyek (`ANALISIS_ERROR.md`, `CRUD_ANGGOTA_RESOLVED.md`, `GEMINI.md`). Pindahkan ke `docs/` atau `support-for-developer/` agar root tetap rapi.
20. **CI / CD**
    Tambahkan GitHub Actions workflow yang menjalankan `composer install`, `npm run build`, dan `php artisan test` pada setiap push/PR.
21. **Code style otomatis**
    Project sudah menggunakan Pint. Tambahkan pre-commit hook (Husky + lint-staged) agar setiap commit otomatis ter-format.
22. **Seeder PWA icon**
    Tambahkan task otomatis (atau script Node) untuk men-generate seluruh ukuran ikon PWA dari satu file source.
23. **Coverage testing untuk UI mobile**
    Pest 4 mendukung browser testing. Tambahkan smoke test untuk halaman kritis (login, dashboard, E-KTA) agar regresi UI cepat tertangkap.

---

## 8. Lampiran — Quick Reference Saat Demo

### 8.1 Daftar URL Penting

| Tujuan | URL |
|--------|-----|
| Landing | `/` |
| Pendaftaran publik | `/pendaftaran` |
| Login | `/login` |
| Dashboard Admin | `/admin/dashboard` |
| Validasi pendaftaran | `/admin/pendaftaran` |
| Manajemen anggota | `/admin/anggota` |
| Manajemen kegiatan | `/admin/kegiatan` |
| Sertifikat (Admin) | `/admin/sertifikat` |
| Arsip (Admin) | `/admin/arsip` |
| Laporan | `/admin/laporan` |
| Dashboard Kader | `/kader/dashboard` |
| E-KTA | `/kader/ekta` |
| Sertifikat saya | `/kader/sertifikat` |
| Riwayat keaktifan | `/kader/riwayat` |
| Arsip Kader | `/kader/arsip` |
| Profil | `/profile` |

### 8.2 Perintah Cepat (Cheat Sheet Terminal)

```bash
# Reset & isi ulang data demo
php artisan migrate:fresh --seed

# Bersihkan seluruh cache
php artisan optimize:clear

# Build asset frontend
npm run build

# Live mode asset (ideal untuk demo)
npm run dev

# Jalankan seluruh test
php artisan test --compact

# Lihat seluruh route Admin
php artisan route:list --path=admin

# Lihat seluruh route Kader
php artisan route:list --path=kader
```

---

*Dokumen ini disusun sebagai pegangan resmi tim presenter demo. Setiap perubahan signifikan pada alur fitur wajib diturunkan ke pembaruan dokumen ini agar selalu konsisten dengan implementasi terkini.*

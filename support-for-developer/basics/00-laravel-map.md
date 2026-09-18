# 00 — Peta project Laravel

Bayangkan Laravel seperti restoran:

| Konsep | Analogi restoran | Folder di project ini |
|--------|------------------|-----------------------|
| Route | Menu / pintu masuk | `routes/web.php`, `routes/auth.php` |
| Controller | Pelayan / manajer dapur | `app/Http/Controllers/` |
| Model | Kartu resep untuk 1 tabel | `app/Models/` |
| Migration | Cetak biru bikin tabel DB | `database/migrations/` |
| View (Blade) | Tampilan makanan / UI | `resources/views/` |
| Config | Aturan rumah | `config/` |
| Middleware | Satpam | `app/Http/Middleware/` |
| Jobs / Queues | Dapur pesanan massal latar belakang | `app/Jobs/` |
| Services | Ahli logika khusus | `app/Services/` |

## Folder penting

```text
sim-Keanggotaan-imm-docker/
├── app/
│   ├── Enums/                 # daftar nilai tetap (RoleEnum, dll)
│   ├── Http/Controllers/      # logika request web
│   ├── Http/Middleware/       # cek otentikasi & role akses
│   ├── Http/Requests/         # validasi form request
│   ├── Jobs/                  # background queue (GenerateCertificateJob)
│   ├── Models/                # tabel ↔ objek Eloquent ORM (12 model)
│   └── Services/              # business logic (CertificateEligibility, VerifiedAttendance)
├── database/
│   ├── migrations/            # struktur tabel versi database
│   ├── factories/             # pembuat data dummy model
│   └── seeders/               # pengisi data awal / demo
├── resources/views/           # template antarmuka Blade HTML & Tailwind
├── routes/
│   ├── web.php                # route aplikasi (public, admin, instruktur, kader)
│   └── auth.php               # route autentikasi (login, password reset)
├── storage/app/
│   ├── public/                # file publik (foto profil, thumbnail kegiatan, sertifikat)
│   └── private/               # file privat (KTP pendaftaran, arsip, materi, settings)
├── support-for-developer/     # dokumentasi lengkap developer
└── compose.yaml / Dockerfile  # konfigurasi Docker lokal (PHP 8.4, MariaDB)
```

## 12 Model Utama di Aplikasi Ini

| Tabel | Model | Arti & Kegunaan |
|-------|-------|-----------------|
| `users` | `User` | Akun autentikasi login + peran (`admin`, `kader`, `instruktur`) |
| `anggota` | `Anggota` | Profil biodata anggota/kader yang terhubung ke akun user |
| `pendaftaran` | `Pendaftaran` | Data formulir pendaftaran calon anggota baru |
| `kegiatan` | `Kegiatan` | Agenda acara/kegiatan perkaderan organisasi |
| `sesi_kegiatan` | `SesiKegiatan` | Sesi pertemuan spesifik dalam suatu kegiatan |
| `presensi` | `Presensi` | Catatan absensi kehadiran anggota di kegiatan/sesi |
| `penilaian_kegiatan` | `PenilaianKegiatan` | Nilai evaluasi mutu kader (A–D) oleh instruktur |
| `materi_kegiatan` | `MateriKegiatan` | Berkas modul/slide materi perkaderan |
| `laporan_kegiatan` | `LaporanKegiatan` | Berita acara dan laporan pelaksanaan kegiatan |
| `sertifikat` | `Sertifikat` | Berkas e-sertifikat resmi hasil generate sistem |
| `arsip` | `Arsip` | Berkas dokumen digital organisasi/kader |
| `kegiatan_tahun_angkatan` | `KegiatanTahunAngkatan` | Data pemetaan tahun angkatan perkaderan |

## Gambar Relasi Inti

```text
User 1 ── 1 Anggota
User 1 ── 1 Pendaftaran

Kegiatan 1 ── * SesiKegiatan 1 ── * Presensi * ── 1 Anggota
Kegiatan 1 ── * MateriKegiatan
Kegiatan 1 ── * PenilaianKegiatan * ── 1 Anggota
Kegiatan 1 ── 1 LaporanKegiatan
Kegiatan 1 ── * Sertifikat * ── 1 Anggota

Anggota 1 ── * Arsip
```

## Alur Request (Sederhana)

```text
URL di browser
   → routes/web.php
   → Middleware (auth & role)
   → Controller Method
   → Model / Database / Service
   → View Blade (HTML + Tailwind)
   → Browser
```

## Aturan Praktis

1. **Ubah struktur DB?** Buat file migration baru.
2. **Objek data bisnis?** Buat/update Model Eloquent.
3. **Halaman / aksi baru?** Tambah Route + Controller (+ View jika perlu).
4. **Siapa yang boleh akses?** Proteksi di middleware `role` (`role:admin`, `role:instruktur`, `role:kader`).
5. **Jangan edit migration lama** yang sudah pernah berjalan; buat migration baru.

Berikutnya: [01 — Tabel database & migration](./01-database-tables-migrations.md)


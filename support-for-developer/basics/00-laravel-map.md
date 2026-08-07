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

## Folder penting

```text
sim-Keanggotaan-imm-docker/
├── app/
│   ├── Enums/                 # daftar nilai tetap (role, dll)
│   ├── Http/Controllers/      # logika request
│   ├── Http/Middleware/       # cek akses (role, auth)
│   └── Models/                # tabel ↔ objek PHP
├── database/
│   ├── migrations/            # buat/ubah tabel
│   └── seeders/               # data dummy (jika ada)
├── resources/views/           # halaman Blade HTML
├── routes/
│   ├── web.php                # route website utama
│   └── auth.php               # route login/register
├── public/                    # entry point (index.php, asset)
├── support-for-developer/     # dokumentasi untuk manusia/AI junior
└── compose.yaml / Dockerfile  # stack Docker lokal
```

## Tabel utama di aplikasi ini

| Tabel | Model | Arti |
|-------|-------|------|
| `users` | `User` | akun login + role |
| `anggota` | `Anggota` | profil anggota yang terhubung ke user |
| `pendaftaran` | `Pendaftaran` | data form pendaftaran |
| `kegiatan` | `Kegiatan` | event / kegiatan |
| `presensi` | `Presensi` | kehadiran anggota di kegiatan |
| `sertifikat` | `Sertifikat` | sertifikat |
| `arsip` | `Arsip` | file arsip anggota |

## Gambar relasi inti

```text
users 1 ── 1 anggota
users 1 ── 1 pendaftaran

anggota 1 ── * presensi * ── 1 kegiatan
anggota 1 ── * sertifikat * ── 1 kegiatan
anggota 1 ── * arsip
```

Artinya:

- 1 akun login bisa punya 1 profil anggota
- 1 anggota bisa hadir di banyak kegiatan (`presensi`)
- 1 kegiatan bisa punya banyak baris presensi

## Alur request (sederhana)

```text
URL di browser
   → routes/web.php
   → method Controller
   → Model / Database
   → View Blade HTML
   → Browser
```

Contoh:

1. User buka `/admin/anggota`
2. Route mengarah ke `AnggotaController@index`
3. Controller ambil `Anggota::with('user')->get()`
4. Controller kembalikan view Blade beserta datanya

## Aturan praktis

1. **Ubah struktur DB?** buat migration.
2. **Objek data bisnis?** buat/update Model.
3. **Halaman / aksi baru?** tambah Route + Controller (+ View jika perlu).
4. **Siapa yang boleh akses?** middleware / cek role.
5. **Jangan edit migration lama** yang sudah pernah jalan di production; buat migration baru untuk mengubah tabel.

Berikutnya: [01 — Tabel database & migration](./01-database-tables-migrations.md)

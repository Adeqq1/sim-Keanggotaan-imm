# Data Dummy dan Seed File Upload

Agar halaman detail, download, dan gambar profil tidak rusak (broken link) saat proses development, jalankan command seed untuk mem-provisioning data file dummy (PDF dan gambar) yang deterministik.

Command yang direkomendasikan untuk menambahkan dataset demo tanpa menghapus data yang sudah ada:

```bash
docker compose exec -e APP_ENV=local app php artisan db:seed --class=DemoSeeder
```

`DemoSeeder` idempoten, memprovisikan record beserta file, dan menolak berjalan pada environment `production`. Command dapat dijalankan ulang tanpa menggandakan dataset demo.

Untuk hanya me-refresh file dengan path `demo/`:

```bash
docker compose exec -e APP_ENV=local app php artisan demo:seed-files
```

Data yang dibuat:
- **Database**: akun tetap, anggota, pendaftaran, kegiatan satu/multi-sesi, target angkatan, presensi, penilaian, materi, laporan, arsip, dan sertifikat eligible.
- **Public disk (`storage/app/public/`)**: foto profil, thumbnail kegiatan, bukti kehadiran historis, dan sertifikat PDF.
- **Private disk (`storage/app/private/`)**: dokumen identitas pendaftaran, materi, lampiran laporan, dan arsip kader.

Kredensial utama:

| Peran | Email | Password |
|---|---|---|
| Admin | `admin@admin.com` | `password` |
| Instruktur | `instruktur@example.com` | `password` |
| Kader utama | `kader@example.com` | `password` |

File demo memakai namespace `demo/`; pembersihan tidak menyentuh file upload pengguna. Untuk mengakses file public di browser lokal, jalankan `docker compose exec app php artisan storage:link`.

# Data Dummy dan Seed File Upload

Agar halaman detail, download, dan gambar profil tidak rusak (broken link) saat proses development, jalankan command seed untuk mem-provisioning data file dummy (PDF dan gambar) yang deterministik.

Command yang direkomendasikan saat pertama kali setup:

```bash
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan demo:seed-files
```

**Penting:** `migrate:fresh --seed` akan menghapus seluruh data database Anda dan menggantinya dengan factory dataset. `demo:seed-files` setelahnya akan melengkapi dataset tersebut dengan file gambar dan PDF sungguhan di disk server.

Apabila Anda tidak ingin menghapus data database yang ada, Anda dapat menjalankan command files saja, yang secara aman hanya me-refresh file dengan path `demo/`:

```bash
docker compose exec app php artisan demo:seed-files
```

Data yang dibuat:
- **Public disk (`storage/app/public/`)**: foto profil anggota, thumbnail kegiatan, dan bukti kehadiran presensi. Command ini juga meng-generate file sertifikat sungguhan memakai template PDF aplikasi.
- **Private disk (`storage/app/private/`)**: dokumen identitas pendaftaran pada `pendaftaran/` dan arsip pribadi milik kader pada `arsip/` untuk pengujian hak akses download. Keduanya *tidak* boleh berada di public disk.

Command ini aman dijalankan ulang (idempoten) dan hanya membersihkan folder namespace `demo/` tanpa menyentuh file hasil upload manual developer. Untuk mengakses file public di browser lokal, pastikan Anda juga telah menjalankan `docker compose exec app php artisan storage:link`.

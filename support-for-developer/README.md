# Support for Developer — SIM Keanggotaan IMM

Folder ini adalah **buku panduan developer** untuk project ini.

## Isi folder

| Dokumen | Kegunaan |
|---------|----------|
| [dokumentasi.md](./dokumentasi.md) | Fitur fungsional yang sudah ada (route, role, modul) |
| [basics/](./basics/) | **Dasar pemrograman Laravel** (tabel, model, relasi, seeder, alur CRUD) |
| [troubleshooting/](./troubleshooting/) | Catatan bugfix dan checklist |

## Mulai di sini jika baru

1. [Peta project Laravel](./basics/00-laravel-map.md)
2. [Membuat tabel dengan migration](./basics/01-database-tables-migrations.md)
3. [Model dan relasi antar tabel](./basics/02-models-and-relations.md)
4. [Route → Controller → View](./basics/03-routes-controllers-views.md)
5. [Checklist buat fitur baru](./basics/04-create-new-feature-checklist.md)
6. [Seeder: isi data dummy & jalankan script](./basics/05-seeder-dan-jalankan-script.md)

## Quick start Docker (clone ini)

```bash
cd ~/Developments/sim-Keanggotaan-imm-docker
docker compose up -d
docker compose ps
# App: http://localhost:8000
# phpMyAdmin: http://localhost:8080
```

Frontend watch (terminal kedua):

```bash
npm run dev
```

Perintah artisan yang sering dipakai (di dalam container app jika perlu):

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan migrate:status
docker compose exec app php artisan make:model NamaModel -m
docker compose exec app php artisan route:list
```

> Catatan: jika nama service compose bukan `app`, ganti dengan nama service asli dari `docker compose ps`.

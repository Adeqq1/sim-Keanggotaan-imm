# 05 — Seeder: isi data dummy & jalankan script

**Seeder** dipakai untuk mengisi database dengan data contoh (dummy), supaya development/testing tidak mulai dari database kosong.

Di project ini ada 2 cara isi data:

1. **Seeder / Factory** → data baris di database (`users`, `anggota`, `kegiatan`, dll)
2. **Artisan command file** → file dummy (foto/PDF) lewat `demo:seed-files`

## 1) Bedanya: Migration vs Seeder vs Factory

| Konsep | Fungsi |
|--------|--------|
| **Migration** | buat/ubah **struktur tabel** |
| **Seeder** | isi **data** ke tabel |
| **Factory** | generator data palsu (nama, email, tanggal, dll) yang dipanggil seeder |

Alur umum:

```text
Migration → tabel siap
Seeder (+ Factory) → tabel terisi data dummy
```

## 2) File seeder di project ini

Lokasi:

```text
database/seeders/
├── DatabaseSeeder.php     # seeder utama (pintu masuk)
├── UserSeeder.php         # contoh user manual
├── AnggotaSeeder.php      # contoh anggota manual
├── KegiatanSeeder.php     # contoh kegiatan manual
└── PresensiSeeder.php     # contoh presensi manual
```

Factory ada di:

```text
database/factories/
├── UserFactory.php
├── AnggotaFactory.php
├── KegiatanFactory.php
├── PendaftaranFactory.php
├── PresensiFactory.php
├── SertifikatFactory.php
└── ArsipFactory.php
```

## 3) Cara menjalankan seeder

### A. Paling sering dipakai (reset DB + seed)

**Hati-hati: ini menghapus SEMUA data database.**

```bash
cd ~/Developments/sim-Keanggotaan-imm-docker

docker compose exec app php artisan migrate:fresh --seed
```

Artinya:

1. drop semua tabel
2. jalankan migration ulang
3. jalankan `DatabaseSeeder`

### B. Seed saja (tanpa hapus struktur)

```bash
docker compose exec app php artisan db:seed
```

Ini menjalankan `DatabaseSeeder` di database yang sudah ada.

### C. Jalankan 1 class seeder saja

```bash
docker compose exec app php artisan db:seed --class=UserSeeder
docker compose exec app php artisan db:seed --class=KegiatanSeeder
```

### D. Seed file dummy (foto/PDF)

Setelah data DB ada:

```bash
docker compose exec app php artisan demo:seed-files
docker compose exec app php artisan storage:link
```

Setup lokal yang direkomendasikan:

```bash
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan demo:seed-files
docker compose exec app php artisan storage:link
```

## 4) Apa yang dibuat `DatabaseSeeder`?

Isi ringkas `database/seeders/DatabaseSeeder.php`:

- 1 user **admin**
- 5 user **kader** + profil `anggota`
- 2 user **instruktur**
- 3 `kegiatan` (masa lalu)
- beberapa `pendaftaran` (pending / approved / rejected)
- `presensi` untuk kombinasi kegiatan × anggota
- `sertifikat` + `arsip` per anggota

Semua itu banyak memakai **factory**, bukan hardcode satu-satu.

## 5) Cara kerja factory (singkat)

Contoh ide:

```php
// buat 5 kader
User::factory()->count(5)->kader()->create();

// buat 1 admin
User::factory()->admin()->create();

// buat kegiatan yang sudah lewat
Kegiatan::factory()->past()->count(3)->create();
```

State factory yang sudah ada di project:

| Factory | State berguna |
|---------|----------------|
| `UserFactory` | `admin()`, `kader()`, `instruktur()` |
| `AnggotaFactory` | `inactive()`, `tanpaNia()` |
| `KegiatanFactory` | `past()` |
| `PendaftaranFactory` | `approved()`, `rejected()`, `instruktur()` |
| `PresensiFactory` | `hadir()` |

Password default factory user biasanya: **`password`**

## 6) Buat seeder baru

### Langkah

```bash
docker compose exec app php artisan make:seeder ContohSeeder
```

File baru:

```text
database/seeders/ContohSeeder.php
```

Contoh isi sederhana:

```php
<?php

namespace Database\Seeders;

use App\Models\Kegiatan;
use Illuminate\Database\Seeder;

class ContohSeeder extends Seeder
{
    public function run(): void
    {
        Kegiatan::create([
            'nama_kegiatan' => 'Rapat Mingguan',
            'deskripsi' => 'Rapat evaluasi kader.',
            'tanggal_waktu' => now()->addDays(3),
            'lokasi' => 'Sekretariat IMM',
        ]);

        // atau pakai factory:
        // Kegiatan::factory()->count(5)->create();
    }
}
```

### Daftarkan di `DatabaseSeeder` (opsional)

Kalau mau ikut jalan saat `db:seed` / `migrate:fresh --seed`, panggil di `DatabaseSeeder`:

```php
public function run(): void
{
    $this->call([
        UserSeeder::class,
        KegiatanSeeder::class,
        ContohSeeder::class,
    ]);
}
```

Atau biarkan terpisah dan jalankan manual:

```bash
docker compose exec app php artisan db:seed --class=ContohSeeder
```

## 7) Seeder manual vs factory

### Manual (`create([...])`)

Cocok untuk data tetap/login demo:

```php
User::create([
    'name' => 'Administrator SIM-IMM',
    'email' => 'admin@admin.com',
    'password' => Hash::make('password'),
    'role' => 'admin',
]);
```

Lihat contoh: `UserSeeder`, `KegiatanSeeder`.

### Factory

Cocok untuk banyak data acak:

```php
User::factory()->count(20)->kader()->create();
```

Lihat contoh: `DatabaseSeeder`.

## 8) Urutan seed yang aman

Kalau tabel saling berhubungan, isi induk dulu:

```text
1. users
2. anggota / pendaftaran
3. kegiatan
4. presensi / sertifikat / arsip
```

Salah urutan = error foreign key (misalnya buat `presensi` sebelum ada `anggota`/`kegiatan`).

## 9) Troubleshooting cepat

| Gejala | Kemungkinan | Solusi |
|--------|-------------|--------|
| `Class ...Seeder not found` | autoload belum refresh | `composer dump-autoload` di container |
| error foreign key | urutan seed salah / data induk kosong | seed parent dulu |
| data dobel terus nambah | `db:seed` dijalankan ulang | pakai `migrate:fresh --seed` atau cek dulu sebelum create |
| foto/PDF broken | file dummy belum di-seed | jalankan `demo:seed-files` + `storage:link` |
| lupa password login | pakai default factory | coba email dari DB, password `password` |

Cek data cepat:

```bash
docker compose exec app php artisan tinker
```

Lalu di tinker:

```php
\App\Models\User::count();
\App\Models\User::select('email','role')->get();
\App\Models\Anggota::count();
```

## 10) Checklist setup lokal dari nol

```bash
cd ~/Developments/sim-Keanggotaan-imm-docker
docker compose up -d

# 1) struktur + data dummy
docker compose exec app php artisan migrate:fresh --seed

# 2) file dummy (opsional tapi recommended)
docker compose exec app php artisan demo:seed-files
docker compose exec app php artisan storage:link

# 3) frontend
npm run dev
```

Buka:

- App: `http://localhost:8000`
- phpMyAdmin: `http://localhost:8080`

## 11) Peringatan penting

1. **Jangan** jalankan `migrate:fresh --seed` di production.
2. Seeder untuk development/testing, bukan pengganti migration.
3. Kalau seeder butuh file fisik, ingat seed file terpisah (`demo:seed-files`).
4. Data random factory bisa beda setiap dijalankan (kecuali difix seed random).

## Lihat juga

- Catatan file dummy: [../troubleshooting/SEED_DATA_DUMMY.md](../troubleshooting/SEED_DATA_DUMMY.md)
- Buat tabel dulu: [01-database-tables-migrations.md](./01-database-tables-migrations.md)
- Model/relasi: [02-models-and-relations.md](./02-models-and-relations.md)
- Checklist fitur baru: [04-create-new-feature-checklist.md](./04-create-new-feature-checklist.md)

Kembali ke index: [../README.md](../README.md)

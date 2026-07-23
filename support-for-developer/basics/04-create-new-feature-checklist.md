# 04 — Checklist buat fitur baru (end-to-end)

Pakai checklist ini setiap kali menambah fitur yang butuh tabel baru atau memperluas tabel yang ada.

## Langkah 0 — Pahami fiturnya

Tulis 3 kalimat:

1. Siapa yang pakai? (admin / instruktur / kader / guest)
2. Data apa yang disimpan?
3. Halaman/aksi apa yang dibutuhkan? (list, create, edit, delete, approve...)

Contoh:

> Admin bisa menulis catatan privat untuk seorang anggota. Tiap catatan milik satu `anggota`. Hanya admin yang boleh create/list/delete catatan.

## Langkah 1 — Desain tabel

Gambar dulu kolomnya:

| kolom | tipe | catatan |
|-------|------|---------|
| id | bigint | PK |
| anggota_id | FK | wajib |
| judul | string | wajib |
| isi | text | wajib |
| created_at / updated_at | timestamps | otomatis |

Tanya:

- butuh soft delete?
- ada unique constraint?
- cascade saat delete?

## Langkah 2 — Migration

```bash
docker compose exec app php artisan make:model CatatanKader -m
```

Edit migration → `Schema::create(...)`.

Jalankan:

```bash
docker compose exec app php artisan migrate
```

Cek di phpMyAdmin (`http://localhost:8080`) atau:

```bash
docker compose exec app php artisan migrate:status
```

## Langkah 3 — Model

Di `app/Models/CatatanKader.php`:

- set `$table`
- set kolom fillable
- tambah `belongsTo(Anggota::class)`

Di `app/Models/Anggota.php`:

- tambah `hasMany(CatatanKader::class)`

## Langkah 4 — Controller

```bash
docker compose exec app php artisan make:controller CatatanKaderController --resource
```

Implement method yang dibutuhkan saja (`index`, `store`, `destroy` cukup untuk catatan sederhana).

Validasi setiap write.

## Langkah 5 — Route

Di `routes/web.php` di dalam group middleware yang benar:

```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('catatan-kader', CatatanKaderController::class)
        ->only(['index', 'store', 'destroy']);
});
```

Cek:

```bash
docker compose exec app php artisan route:list --name=catatan
```

## Langkah 6 — View

Buat:

```text
resources/views/admin/catatan-kader/index.blade.php
```

Isi minimal:

- list catatan
- form create dengan `@csrf`
- form delete dengan `@csrf` + `@method('DELETE')`
- flash success message

## Langkah 7 — Cek otorisasi lagi

- middleware role di route sudah benar
- controller tidak bocor data privat anggota lain
- untuk halaman kader, filter dengan `auth()->user()->anggota`

Contoh query aman untuk kader:

```php
$anggotaId = auth()->user()->anggota?->id;
$notes = CatatanKader::where('anggota_id', $anggotaId)->latest()->get();
```

## Langkah 8 — Tes manual

- [ ] buka list page sebagai admin → 200
- [ ] create data valid → tersimpan
- [ ] create data invalid → muncul error validasi
- [ ] delete data → terhapus
- [ ] buka sebagai kader/instruktur → 403 jika khusus admin
- [ ] cascade delete relasi jalan (opsional)

## Langkah 9 — Docs / rapi-rapi

- update catatan fitur di `support-for-developer/dokumentasi.md` jika ini fitur produk nyata
- simpan catatan troubleshooting di `support-for-developer/troubleshooting/` jika ketemu bug

## Template arsitektur siap salin

```text
Fitur: Catatan Kader
Aktor: admin
Tabel: catatan_kader(anggota_id, judul, isi)
Model: CatatanKader belongsTo Anggota; Anggota hasMany CatatanKader
Route: admin.catatan-kader.index|store|destroy
Controller: CatatanKaderController
View: admin/catatan-kader/index.blade.php
Middleware: auth + role:admin
```

## Contoh nyata di codebase

| Mau belajar | Baca |
|-------------|------|
| Tabel profil anggota | `database/migrations/2026_05_10_235500_create_anggota_table.php` + `app/Models/Anggota.php` |
| Pola join table (presensi) | `...create_presensi_table.php` + `app/Models/Presensi.php` |
| User ↔ profil | `app/Models/User.php` (`hasOne` anggota) |
| Gaya CRUD admin | `app/Http/Controllers/AnggotaController.php` |
| Gaya form public | `app/Http/Controllers/PendaftaranController.php` |
| Perilaku fitur yang ada | `support-for-developer/dokumentasi.md` |

## Aturan emas

1. Migration dulu, baru model, lalu route/controller/view.
2. Jangan simpan struktur penting hanya di UI DB lokal.
3. Selalu validasi input request.
4. Selalu lindungi dengan role.
5. Pakai penamaan yang sudah dipakai codebase (`anggota`, `kegiatan`, `presensi`).
6. Buat fitur kecil dulu, lalu ditest.

Kalau fitur butuh data dummy untuk testing, lanjut ke: [05 — Seeder & jalankan script](./05-seeder-dan-jalankan-script.md)

Kembali ke index: [../README.md](../README.md)

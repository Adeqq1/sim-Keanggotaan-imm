# 02 — Model dan relasi antar tabel

**Model** adalah class PHP yang mewakili satu tabel database.

| Tabel | File model |
|-------|------------|
| `anggota` | `app/Models/Anggota.php` |
| `presensi` | `app/Models/Presensi.php` |
| `users` | `app/Models/User.php` |

## 1) Buat model

```bash
docker compose exec app php artisan make:model CatatanKader
# atau sekaligus dengan migration:
docker compose exec app php artisan make:model CatatanKader -m
```

## 2) Struktur model minimal (gaya project ini)

Project ini memakai atribut modern `#[Fillable([...])]`.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['anggota_id', 'judul', 'isi'])]
class CatatanKader extends Model
{
    use HasFactory;

    protected $table = 'catatan_kader';

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }
}
```

### Bagian penting

| Bagian | Fungsi |
|--------|--------|
| `protected $table` | wajib di sini karena nama tabel Indonesia / bukan plural English |
| `#[Fillable([...])]` | kolom yang boleh diisi mass assignment (`create`, `update`) |
| `protected $casts` | konversi tipe otomatis (`date`, `boolean`, `datetime`) |
| method relasi | menghubungkan tabel lewat PHP |

## 3) Jenis relasi dasar

### A) belongsTo (anak → induk)

`Presensi` milik `Anggota` dan `Kegiatan`.

```php
// app/Models/Presensi.php
public function anggota(): BelongsTo
{
    return $this->belongsTo(Anggota::class);
}

public function kegiatan(): BelongsTo
{
    return $this->belongsTo(Kegiatan::class);
}
```

Cara pakai:

```php
$presensi = Presensi::with(['anggota', 'kegiatan'])->first();
echo $presensi->anggota->nama_lengkap;
echo $presensi->kegiatan->nama; // field tergantung kolom kegiatan
```

### B) hasMany (induk → banyak anak)

```php
// app/Models/Anggota.php
public function presensi(): HasMany
{
    return $this->hasMany(Presensi::class);
}
```

Cara pakai:

```php
$anggota = Anggota::with('presensi')->find(1);
foreach ($anggota->presensi as $row) {
    // ...
}
```

### C) hasOne (induk → satu anak)

```php
// app/Models/User.php
public function anggota(): HasOne
{
    return $this->hasOne(Anggota::class);
}
```

Cara pakai:

```php
$user = auth()->user();
$profile = $user->anggota; // satu profil atau null
```

## 4) Peta relasi nyata di app ini

```text
User
 ├─ hasOne Anggota
 └─ hasOne Pendaftaran

Anggota
 ├─ belongsTo User
 ├─ hasMany Presensi
 ├─ hasMany Sertifikat
 └─ hasMany Arsip

Kegiatan
 ├─ hasMany Presensi
 └─ hasMany Sertifikat

Presensi
 ├─ belongsTo Kegiatan
 └─ belongsTo Anggota
```

## 5) Create / read / update / delete dengan Eloquent

```php
// CREATE
$anggota = Anggota::create([
    'user_id' => $user->id,
    'nama_lengkap' => 'Budi',
    'tempat_lahir' => 'Jakarta',
    'tanggal_lahir' => '2000-01-01',
    'alamat' => 'Jl. Contoh',
    'no_telp' => '0812...',
    'status_aktif' => true,
]);

// READ
$list = Anggota::where('status_aktif', true)->orderBy('nama_lengkap')->get();
$one = Anggota::findOrFail($id);

// UPDATE
$one->update(['no_telp' => '0813...']);

// DELETE
$one->delete();
```

## 6) Selalu eager-load relasi saat list

Buruk (masalah N+1 query):

```php
$rows = Presensi::all();
foreach ($rows as $row) {
    echo $row->anggota->nama_lengkap; // query setiap loop
}
```

Baik:

```php
$rows = Presensi::with(['anggota', 'kegiatan'])->get();
```

## 7) Hubungkan dua sisi relasi

Jika `CatatanKader belongsTo Anggota`, tambahkan juga reverse di `Anggota`:

```php
// Anggota.php
public function catatanKader(): HasMany
{
    return $this->hasMany(CatatanKader::class);
}
```

Maka keduanya bisa dipakai:

```php
$catatan->anggota;
$anggota->catatanKader;
```

## 8) Kesalahan umum

1. Lupa `protected $table = '...'` → Laravel mencari nama tabel yang salah.
2. Lupa kolom di fillable → nilai diabaikan saat `create/update`.
3. Migration foreign key tidak dibuat → relasi kadang jalan, tapi integritas DB lemah.
4. Salah jenis relasi (`hasMany` padahal harusnya `belongsTo`).
5. Edit migration lama yang sudah diterapkan → rekan/production rusak.

Berikutnya: [03 — Route, controller, view](./03-routes-controllers-views.md)

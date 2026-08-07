# 03 — Route, Controller, View (alur request dasar)

## 1) Route = pintu masuk URL

File utama: `routes/web.php`

Contoh sederhana:

```php
use App\Http\Controllers\AnggotaController;

Route::get('/admin/anggota', [AnggotaController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.anggota.index');
```

Artinya:

- URL `GET /admin/anggota`
- ditangani oleh `AnggotaController@index`
- hanya **admin** yang sudah login
- nama route: `admin.anggota.index` (untuk `route('admin.anggota.index')`)

### Method HTTP umum

| Method | Dipakai untuk |
|--------|---------------|
| GET | tampilkan halaman / list |
| POST | buat data |
| PUT/PATCH | update data |
| DELETE | hapus data |

Pola penamaan resource di project ini:

```text
admin.anggota.index    list
admin.anggota.create   form create
admin.anggota.store    simpan create
admin.anggota.show     detail
admin.anggota.edit     form edit
admin.anggota.update   simpan edit
admin.anggota.destroy  hapus
```

Cek route:

```bash
docker compose exec app php artisan route:list
```

## 2) Controller = logika

Generate:

```bash
docker compose exec app php artisan make:controller Admin/ContohController
```

Pola dasar:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function index()
    {
        $anggota = Anggota::with('user')->latest()->paginate(10);

        return view('admin.anggota.index', compact('anggota'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_telp' => ['required', 'string', 'max:30'],
            // ...
        ]);

        Anggota::create($data);

        return redirect()
            ->route('admin.anggota.index')
            ->with('success', 'Anggota berhasil dibuat');
    }
}
```

### Tugas controller

- validasi input
- create/read/update/delete lewat model
- memilih view yang dirender
- redirect + flash message

**Jangan** taruh HTML berat di controller. HTML ada di Blade view.

## 3) Validasi (selalu)

```php
$data = $request->validate([
    'nama_lengkap' => ['required', 'string', 'max:255'],
    'tanggal_lahir' => ['required', 'date'],
    'status_aktif' => ['nullable', 'boolean'],
]);
```

Jika invalid, Laravel otomatis kembali ke form dengan error.

## 4) View (Blade)

View ada di `resources/views/`.

Controller:

```php
return view('admin.anggota.index', compact('anggota'));
```

Mencari file:

```text
resources/views/admin/anggota/index.blade.php
```

Blade sederhana:

```blade
@extends('layouts.app')

@section('content')
  <h1>Daftar Anggota</h1>

  @foreach ($anggota as $row)
    <div>{{ $row->nama_lengkap }} - {{ $row->user?->email }}</div>
  @endforeach

  {{ $anggota->links() }}
@endsection
```

### Potongan Blade yang berguna

```blade
{{ $value }}                 {{-- output aman (escape) --}}
{!! $html !!}                {{-- raw html (hati-hati) --}}
@if ($cond) ... @endif
@foreach ($items as $item) ... @endforeach
@csrf                        {{-- wajib di form POST --}}
@error('nama_lengkap') {{ $message }} @enderror
{{ route('admin.anggota.index') }}
```

Contoh form:

```blade
<form method="POST" action="{{ route('admin.anggota.store') }}">
  @csrf
  <input name="nama_lengkap" value="{{ old('nama_lengkap') }}">
  <button type="submit">Simpan</button>
</form>
```

## 5) Auth + role middleware di project ini

App ini cek role lewat middleware (lihat `RoleMiddleware`).

Pola umum:

```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // khusus admin
});

Route::middleware(['auth', 'role:admin,instruktur'])->group(function () {
    // admin atau instruktur
});
```

Role yang dipakai: `admin`, `instruktur`, `kader` (+ halaman public guest).

## 6) Alur mini lengkap

1. migration membuat tabel
2. model memetakan tabel + relasi
3. route menghubungkan URL ke controller
4. controller validasi + pakai model
5. view menampilkan data / form
6. middleware melindungi akses

Berikutnya: [04 — Checklist buat fitur baru](./04-create-new-feature-checklist.md)

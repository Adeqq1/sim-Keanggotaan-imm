# ⚡ Quick Fix: Missing Route Parameter Error

## 🔍 Error

```
Missing required parameter for [Route: admin.anggota.update] 
[URI: admin/anggota/{anggotum}] [Missing parameter: anggotum]
```

Dan:

```
Call to a member function format() on null
```

## ✅ Sudah Diperbaiki!

### 1. Route Parameter Fix

**Masalah:** Passing model object ke route, tapi Laravel expect ID

**Sebelum:**
```blade
route('admin.anggota.edit', $anggota)
```

**Sesudah:**
```blade
route('admin.anggota.edit', $anggota->id)
```

### 2. Null Safety Fix

**Masalah:** `tanggal_lahir` bisa null

**Sebelum:**
```blade
{{ $anggota->tanggal_lahir->format('d F Y') }}
```

**Sesudah:**
```blade
{{ $anggota->tanggal_lahir?->format('d F Y') ?? '-' }}
```

## 🎯 File yang Diperbaiki

- ✅ `resources/views/admin/anggota/index.blade.php`
- ✅ `resources/views/admin/anggota/show.blade.php`
- ✅ `resources/views/admin/anggota/edit.blade.php`

## 🧪 Test Sekarang

```bash
# Clear cache
php artisan view:clear
php artisan route:clear

# Test di browser
http://sim-keanggotaan-imm.test/admin/anggota
```

Klik:
- ✅ Detail → Harus berfungsi
- ✅ Edit → Harus berfungsi
- ✅ Hapus → Harus berfungsi

---

*Kembali ke: [Troubleshooting](README.md)*

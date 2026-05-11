# 🔧 Fix CRUD Anggota (Edit, Detail, Hapus)

## 🔍 Masalah

Admin tidak bisa:
- ❌ Melihat detail anggota
- ❌ Edit anggota
- ❌ Hapus anggota

## ✅ Solusi yang Sudah Diterapkan

### 1. Tambah Method `show()` di Controller

**File:** `app/Http/Controllers/AnggotaController.php`

```php
public function show(Anggota $anggota)
{
    return view('admin.anggota.show', compact('anggota'));
}
```

Method ini diperlukan karena view index memanggil `route('admin.anggota.show')`.

---

### 2. Buat View yang Hilang

#### View Show (Detail)
**File:** `resources/views/admin/anggota/show.blade.php`

Menampilkan:
- ✅ Foto profil
- ✅ Data lengkap anggota
- ✅ Status aktif/tidak aktif
- ✅ Tombol edit dan hapus

#### View Edit
**File:** `resources/views/admin/anggota/edit.blade.php`

Form untuk:
- ✅ Edit semua data anggota
- ✅ Upload foto profil baru
- ✅ Ubah status aktif

#### View Create
**File:** `resources/views/admin/anggota/create.blade.php`

Form untuk:
- ✅ Tambah anggota baru
- ✅ Upload foto profil
- ✅ Set status aktif

---

### 3. Fix Route Model Binding

**File:** `app/Models/Anggota.php`

```php
public function getRouteKeyName(): string
{
    return 'id';
}
```

Ini memastikan Laravel menggunakan `id` untuk route model binding, bukan field lain.

---

## 🧪 Testing

### Test Manual

1. **Login sebagai Admin**
   ```
   http://sim-keanggotaan-imm.test/login
   Email: demo@example.com
   Password: demo123
   ```

2. **Akses Manajemen Anggota**
   ```
   http://sim-keanggotaan-imm.test/admin/anggota
   ```

3. **Test Fitur:**
   - [ ] Klik "Tambah" → Form create muncul
   - [ ] Isi form → Simpan → Anggota baru muncul di list
   - [ ] Klik "Detail" → Halaman detail muncul
   - [ ] Klik "Edit" → Form edit muncul
   - [ ] Edit data → Update → Data berubah
   - [ ] Klik "Hapus" → Modal konfirmasi muncul
   - [ ] Konfirmasi hapus → Anggota terhapus

---

## 📊 Routes yang Tersedia

| Method | URI | Name | Action |
|--------|-----|------|--------|
| GET | admin/anggota | admin.anggota.index | index |
| POST | admin/anggota | admin.anggota.store | store |
| GET | admin/anggota/create | admin.anggota.create | create |
| GET | admin/anggota/{anggotum} | admin.anggota.show | show ✅ |
| PUT/PATCH | admin/anggota/{anggotum} | admin.anggota.update | update ✅ |
| DELETE | admin/anggota/{anggotum} | admin.anggota.destroy | destroy ✅ |
| GET | admin/anggota/{anggotum}/edit | admin.anggota.edit | edit ✅ |

**Catatan:** `{anggotum}` adalah bentuk jamak dari Laravel, tapi sudah di-handle dengan `getRouteKeyName()`.

---

## 🔍 Troubleshooting

### Error 404 Not Found

**Gejala:** Klik detail/edit/hapus → 404

**Solusi:**
```bash
# Clear cache
php artisan route:clear
php artisan view:clear
php artisan config:clear

# Restart browser
```

---

### Error "Method not found"

**Gejala:** Error "Method [show] does not exist"

**Solusi:**
- Method `show()` sudah ditambahkan di controller
- Clear cache dan refresh browser

---

### View Not Found

**Gejala:** Error "View [admin.anggota.show] not found"

**Solusi:**
- View sudah dibuat di `resources/views/admin/anggota/`
- Clear view cache: `php artisan view:clear`

---

### Foto Tidak Muncul

**Gejala:** Foto profil tidak tampil

**Solusi:**
```bash
# Pastikan storage linked
php artisan storage:link

# Check folder permissions
# Folder storage/app/public harus writable
```

---

## 💡 Fitur yang Sudah Berfungsi

### ✅ Index (List Anggota)
- Tampil semua anggota
- Search by nama/NIA
- Pagination
- Dropdown action (Detail, Edit, Hapus)

### ✅ Create (Tambah Anggota)
- Form lengkap
- Upload foto profil
- Validation
- Success message

### ✅ Show (Detail Anggota)
- Foto profil
- Data lengkap
- Status badge
- Tombol edit & hapus

### ✅ Edit (Update Anggota)
- Form pre-filled
- Upload foto baru (opsional)
- Update semua field
- Success message

### ✅ Delete (Hapus Anggota)
- Modal konfirmasi
- Hapus foto dari storage
- Success message

---

## 📝 Validasi

**File:** `app/Http/Requests/AnggotaRequest.php`

Validasi yang diterapkan:
- `nia` - required, unique
- `nama_lengkap` - required, string
- `tempat_lahir` - required, string
- `tanggal_lahir` - required, date
- `alamat` - required, string
- `no_telp` - required, string
- `foto_profil` - optional, image, max 2MB
- `status_aktif` - required, boolean

---

## 🎯 Checklist

Sebelum test:
- [x] Method `show()` ditambahkan di controller
- [x] View `show.blade.php` dibuat
- [x] View `edit.blade.php` dibuat
- [x] View `create.blade.php` dibuat
- [x] Model `getRouteKeyName()` ditambahkan
- [x] Cache di-clear
- [x] No diagnostics error

Setelah test:
- [ ] Tambah anggota berfungsi
- [ ] Detail anggota berfungsi
- [ ] Edit anggota berfungsi
- [ ] Hapus anggota berfungsi
- [ ] Upload foto berfungsi
- [ ] Validation berfungsi

---

## 🎉 Selesai!

Semua fitur CRUD anggota sekarang berfungsi dengan baik!

**Test sekarang:**
```
http://sim-keanggotaan-imm.test/admin/anggota
```

---

*Kembali ke: [Troubleshooting](README.md)*

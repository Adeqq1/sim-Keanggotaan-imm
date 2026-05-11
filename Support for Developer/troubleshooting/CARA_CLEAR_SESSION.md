# 🧹 Cara Clear Session & Cache Laravel

## Masalah yang Diselesaikan
Ketika berpindah dari `php artisan serve` (localhost:8000) ke virtual host Laragon (sim-keanggotaan-imm.test) atau sebaliknya, session lama masih tersimpan sehingga menyebabkan masalah seperti:
- Tidak bisa akses `/login` (karena masih dianggap sudah login)
- Redirect loop
- Session tidak sinkron

## 🚀 Cara Menggunakan Script

### Opsi 1: Script Lengkap (Recommended)

**Double-click file ini:**
```
clear-sessions.bat
```

Script ini akan:
1. ✅ Menghapus semua session dari database
2. ✅ Clear configuration cache
3. ✅ Clear route cache
4. ✅ Clear application cache
5. ✅ Clear view cache
6. ✅ Menampilkan instruksi selanjutnya

### Opsi 2: Script Cepat

**Double-click file ini:**
```
quick-clear.bat
```

Versi lebih ringkas tanpa banyak output.

### Opsi 3: Manual via Terminal

Buka terminal di folder project dan jalankan:

```bash
# Clear session dari database
php artisan tinker --execute "DB::table('sessions')->truncate();"

# Clear semua cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

## 📋 Langkah Lengkap Setelah Clear Session

1. **Jalankan script:**
   ```
   Double-click: clear-sessions.bat
   ```

2. **Clear browser cookies:**
   - Tekan `F12` (Developer Tools)
   - Tab **Application** → **Cookies**
   - Hapus cookies untuk `sim-keanggotaan-imm.test`

3. **Tutup browser sepenuhnya:**
   - Tutup SEMUA window browser
   - Jangan hanya tutup tab

4. **Buka browser baru:**
   - Buka browser fresh
   - Atau gunakan mode Incognito (Ctrl+Shift+N)

5. **Test akses:**
   ```
   http://sim-keanggotaan-imm.test/login
   ```

## 🎯 Kapan Harus Clear Session?

Clear session ketika:
- ✅ Berpindah dari `php artisan serve` ke virtual host
- ✅ Berpindah dari virtual host ke `php artisan serve`
- ✅ Tidak bisa akses halaman login
- ✅ Terjadi redirect loop
- ✅ Session tidak sinkron (data user berbeda)
- ✅ Setelah mengubah file `.env`
- ✅ Setelah mengubah konfigurasi session

## 💡 Tips Mencegah Masalah Session

### 1. Pilih Satu Environment
Jangan gunakan `php artisan serve` dan virtual host bersamaan untuk project yang sama.

**Untuk Development:**
```bash
# Gunakan virtual host (recommended)
http://sim-keanggotaan-imm.test
```

**Atau:**
```bash
# Gunakan php artisan serve
php artisan serve
http://localhost:8000
```

### 2. Gunakan Session Driver yang Tepat

**Untuk Development (Single User):**
```env
# File .env
SESSION_DRIVER=file
```

**Untuk Production (Multiple Users):**
```env
# File .env
SESSION_DRIVER=database
```

### 3. Set SESSION_DOMAIN dengan Benar

**Untuk localhost:**
```env
SESSION_DOMAIN=null
```

**Untuk virtual host dengan subdomain:**
```env
SESSION_DOMAIN=.sim-keanggotaan-imm.test
```

## 🔧 Troubleshooting

### Script Tidak Jalan / Error

**Error: "php is not recognized"**
- Pastikan PHP sudah ada di PATH
- Atau jalankan dari terminal Laragon

**Error: "DB::table not found"**
- Pastikan database sudah running
- Cek koneksi database di `.env`

**Error: "sessions table not found"**
- Jalankan: `php artisan migrate`

### Masih Tidak Bisa Akses /login

1. **Cek status autentikasi:**
   ```
   http://sim-keanggotaan-imm.test/debug-auth
   ```

2. **Gunakan browser Incognito:**
   - Ctrl+Shift+N (Chrome)
   - Ctrl+Shift+P (Firefox)

3. **Clear browser cache:**
   - Ctrl+Shift+Delete
   - Pilih "All time"
   - Centang semua opsi

4. **Restart Laragon:**
   - Stop all services
   - Start all services

## 📝 Catatan Penting

- ⚠️ Clear session akan **logout semua user** yang sedang login
- ⚠️ Jangan jalankan di **production** tanpa peringatan ke user
- ⚠️ Backup database sebelum clear session di production
- ✅ Aman dijalankan di **development/local**

## 🎓 Penjelasan Teknis

### Kenapa Session Conflict?

Ketika menggunakan `php artisan serve` (localhost:8000), Laravel menyimpan session dengan cookie domain `localhost`. Ketika berpindah ke virtual host (sim-keanggotaan-imm.test), browser tidak mengirim cookie dari `localhost` karena domain berbeda.

Namun, session di **database** masih ada dan bisa menyebabkan konflik jika:
1. Session ID sama tapi domain berbeda
2. User data tidak sinkron
3. Middleware `guest` masih mendeteksi user login

### Solusi Permanen

Gunakan **satu environment** saja untuk development:
- Virtual host Laragon (recommended)
- Lebih mirip production
- Tidak perlu jalankan command
- Support multiple projects

## 📚 File Terkait

- `clear-sessions.bat` - Script lengkap dengan output detail
- `quick-clear.bat` - Script cepat tanpa banyak output
- `clear-sessions.sh` - Untuk Linux/Mac
- `DEBUG_LOGIN_ISSUE.md` - Troubleshooting detail masalah login
- `LARAGON_SETUP.md` - Setup lengkap virtual host Laragon

---

**Selamat! Masalah session sudah teratasi! 🎉**

Jika masih ada masalah, lihat file `DEBUG_LOGIN_ISSUE.md` untuk troubleshooting lebih detail.

# Debug Login Issue - Virtual Host Laragon

## Status Saat Ini
- ✅ http://sim-keanggotaan-imm.test → **BISA DIAKSES**
- ❌ http://sim-keanggotaan-imm.test/login → **TIDAK BISA DIAKSES**
- ✅ http://sim-keanggotaan-imm.test/pendaftaran → **BISA DIAKSES**

## Analisis Masalah

Route `/login` menggunakan middleware `guest` yang akan redirect user yang sudah login. Kemungkinan masalah:

1. **User sudah terautentikasi** - Session dari `php artisan serve` masih aktif
2. **Session cookie domain mismatch** - Cookie dari localhost:8000 tidak cocok dengan .test domain
3. **Session tidak ter-clear** antara localhost:8000 dan virtual host

## Langkah Testing

### 1. Cek Status Autentikasi
Buka browser dan akses:
```
http://sim-keanggotaan-imm.test/debug-auth
```

Anda akan melihat output JSON seperti ini:
```json
{
  "authenticated": true/false,
  "user": {...} atau null,
  "session_driver": "database",
  "session_domain": null,
  "app_url": "http://sim-keanggotaan-imm.test"
}
```

**Jika `authenticated: true`** → Masalahnya adalah user sudah login, middleware `guest` redirect dari `/login`

**Jika `authenticated: false`** → Ada masalah lain dengan routing atau middleware

### 2. Clear Browser Data
Untuk memastikan tidak ada session lama:

1. Buka **Developer Tools** (F12)
2. Pilih tab **Application** (Chrome) atau **Storage** (Firefox)
3. Klik **Cookies** → pilih `http://sim-keanggotaan-imm.test`
4. Hapus semua cookies (terutama yang namanya mengandung "session")
5. Klik **Clear site data** atau **Clear storage**
6. Tutup browser sepenuhnya
7. Buka browser baru dan coba akses lagi

### 3. Test dengan Browser Incognito/Private
Buka browser dalam mode Incognito/Private dan coba akses:
```
http://sim-keanggotaan-imm.test/login
```

Jika berhasil di Incognito → Masalahnya adalah session/cookie lama

## Solusi

### Solusi 1: Logout dari Semua Session

Jika user sudah login, logout dulu dengan mengakses:
```
http://sim-keanggotaan-imm.test/
```
Kemudian klik tombol logout jika ada, atau akses langsung:
```
POST http://sim-keanggotaan-imm.test/logout
```

### Solusi 2: Clear Session dari Database

Jalankan di terminal:
```bash
php artisan tinker --execute "DB::table('sessions')->truncate();"
```

Atau manual via phpMyAdmin/HeidiSQL:
```sql
TRUNCATE TABLE sessions;
```

### Solusi 3: Tambahkan SESSION_DOMAIN ke .env

Jika masalahnya adalah cookie domain, tambahkan ke file `.env`:
```env
SESSION_DOMAIN=.sim-keanggotaan-imm.test
```

Kemudian clear config:
```bash
php artisan config:clear
```

**CATATAN:** Tanda titik (.) di depan domain penting untuk subdomain support

### Solusi 4: Gunakan Session Driver File (Temporary)

Untuk testing, ubah di `.env`:
```env
SESSION_DRIVER=file
```

Kemudian:
```bash
php artisan config:clear
```

Jika berhasil dengan `file` driver, berarti masalahnya di database session.

### Solusi 5: Periksa Middleware Guest Redirect

Middleware `guest` akan redirect user yang sudah login ke dashboard. Cek kemana redirect dilakukan dengan membuat route test:

```php
// Tambahkan di routes/web.php
Route::get('/test-guest', function () {
    return 'Guest middleware works!';
})->middleware('guest');
```

Akses `http://sim-keanggotaan-imm.test/test-guest`:
- Jika muncul "Guest middleware works!" → Middleware OK, user belum login
- Jika redirect ke dashboard → User sudah login

## Solusi Permanen

Setelah menemukan masalahnya, lakukan:

### 1. Pastikan Tidak Ada Session Conflict

Jangan jalankan `php artisan serve` dan virtual host bersamaan untuk project yang sama. Pilih salah satu:

**Gunakan Virtual Host (Recommended):**
```bash
# Jangan jalankan php artisan serve
# Akses langsung via http://sim-keanggotaan-imm.test
```

**Atau gunakan php artisan serve:**
```bash
php artisan serve
# Akses via http://localhost:8000
```

### 2. Set SESSION_DOMAIN yang Benar

Untuk virtual host, tambahkan di `.env`:
```env
# Untuk single domain
SESSION_DOMAIN=null

# Atau untuk support subdomain
SESSION_DOMAIN=.sim-keanggotaan-imm.test
```

### 3. Clear All Cache

Setiap kali ganti environment (localhost:8000 ↔ .test), jalankan:
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

### 4. Restart Browser

Setelah clear cache Laravel, restart browser untuk memastikan cookie lama tidak digunakan.

## Kemungkinan Error Lain

### Error: "Page not found" atau 404
Cek file `.htaccess` di root project sudah ada dan isinya benar:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### Error: "Too many redirects"
Ini terjadi jika ada redirect loop. Cek:
1. Middleware `guest` tidak conflict dengan middleware lain
2. `APP_URL` di `.env` sudah benar
3. Tidak ada redirect di `.htaccess` yang conflict

### Error: 500 Internal Server Error
Cek Apache error log:
1. Klik kanan Laragon → Apache → error.log
2. Lihat error terakhir
3. Biasanya masalah permission atau .htaccess

## Setelah Selesai Testing

Hapus route debug dari `routes/web.php`:
```php
// Hapus route ini:
Route::get('/debug-auth', function () { ... });
Route::get('/test-guest', function () { ... });
```

Kemudian:
```bash
php artisan route:clear
```

## Kesimpulan

Masalah paling umum adalah **user sudah terautentikasi** dari session sebelumnya (localhost:8000). Middleware `guest` akan redirect user yang sudah login, sehingga `/login` tidak bisa diakses.

**Solusi tercepat:**
1. Clear browser cookies
2. Truncate tabel sessions
3. Restart browser
4. Akses http://sim-keanggotaan-imm.test/login

Jika masih bermasalah, laporkan hasil dari `/debug-auth` untuk analisis lebih lanjut.

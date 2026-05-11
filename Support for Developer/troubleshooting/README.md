# 🔧 Troubleshooting

Tools dan dokumentasi untuk mengatasi masalah development SIM Keanggotaan IMM.

---

## 📁 File yang Tersedia

### 📖 Dokumentasi

#### 1. **LARAGON_SETUP.md** - Setup Virtual Host Laragon
**Kapan digunakan:**
- Virtual host tidak bisa diakses
- Setup project pertama kali di Laragon
- Pindah dari `php artisan serve` ke virtual host

**Isi:**
- Setup virtual host Laragon
- Konfigurasi Apache mod_rewrite
- Verifikasi file hosts
- Troubleshooting virtual host

---

#### 2. **DEBUG_LOGIN_ISSUE.md** - Troubleshooting Masalah Login
**Kapan digunakan:**
- Route `/login` tidak bisa diakses
- Homepage bisa diakses tapi login tidak
- Redirect loop di halaman login

**Isi:**
- Analisis masalah middleware `guest`
- Langkah testing autentikasi
- Solusi session conflict
- Troubleshooting lengkap

---

#### 3. **CARA_CLEAR_SESSION.md** - Panduan Clear Session & Cache
**Kapan digunakan:**
- Session conflict antara localhost:8000 dan virtual host
- Setelah mengubah file `.env`
- Setelah mengubah konfigurasi session
- Tidak bisa login/logout
- Data user tidak sinkron

**Isi:**
- Cara menggunakan script clear session
- Penjelasan masalah session conflict
- Tips mencegah masalah session
- Troubleshooting session

---

### 🛠️ Script

#### 1. **clear-sessions.bat** (Windows) ⭐ RECOMMENDED
**Fungsi:**
- Clear session dari database
- Clear configuration cache
- Clear route cache
- Clear application cache
- Clear view cache

**Cara pakai:**
```bash
# Double-click file ini
clear-sessions.bat

# Atau via terminal
cd "Support for Developer/troubleshooting"
clear-sessions.bat
```

---

#### 2. **quick-clear.bat** (Windows)
**Fungsi:**
- Versi ringkas dari clear-sessions.bat
- Tanpa output detail
- Lebih cepat

**Cara pakai:**
```bash
# Double-click file ini
quick-clear.bat
```

---

#### 3. **clear-sessions.sh** (Linux/Mac)
**Fungsi:**
- Sama dengan clear-sessions.bat
- Untuk Linux/Mac

**Cara pakai:**
```bash
# Via terminal
bash clear-sessions.sh
```

---

## 🎯 Quick Start

### Masalah: Virtual Host Tidak Bisa Diakses
```bash
1. Baca: LARAGON_SETUP.md
2. Cek Apache mod_rewrite aktif
3. Cek file .htaccess di root project
4. Restart Laragon
```

### Masalah: Route /login Tidak Bisa Diakses
```bash
1. Jalankan: clear-sessions.bat
2. Clear browser cookies
3. Restart browser
4. Test akses: http://sim-keanggotaan-imm.test/login
```

### Masalah: Session Conflict
```bash
1. Jalankan: clear-sessions.bat
2. Pilih satu environment (virtual host atau php artisan serve)
3. Clear browser cookies
4. Restart browser
```

---

## 📊 Troubleshooting Flowchart

```
Masalah Development?
│
├─ Virtual host tidak bisa diakses?
│  └─ Baca: LARAGON_SETUP.md
│
├─ Route /login tidak bisa diakses?
│  └─ Baca: DEBUG_LOGIN_ISSUE.md
│     └─ Jalankan: clear-sessions.bat
│
├─ Session conflict?
│  └─ Baca: CARA_CLEAR_SESSION.md
│     └─ Jalankan: clear-sessions.bat
│
└─ Masalah lain?
   └─ Cek dokumentasi di folder ini
```

---

## ⚠️ Kapan Harus Clear Session?

Clear session ketika:
- ✅ Berpindah dari `php artisan serve` ke virtual host
- ✅ Berpindah dari virtual host ke `php artisan serve`
- ✅ Tidak bisa akses halaman login
- ✅ Terjadi redirect loop
- ✅ Session tidak sinkron
- ✅ Setelah mengubah file `.env`
- ✅ Setelah mengubah konfigurasi session

---

## 🔍 Masalah Umum & Solusi

### 1. Virtual Host Tidak Bisa Diakses
**Gejala:** http://sim-keanggotaan-imm.test tidak bisa dibuka

**Solusi:**
1. Cek Laragon sudah running
2. Cek Apache mod_rewrite aktif
3. Cek file `.htaccess` di root project
4. Restart Laragon

**Dokumentasi:** `LARAGON_SETUP.md`

---

### 2. Route /login Tidak Bisa Diakses
**Gejala:** Homepage bisa diakses, tapi `/login` tidak

**Solusi:**
1. Jalankan `clear-sessions.bat`
2. Clear browser cookies
3. Restart browser
4. Test dengan Incognito mode

**Dokumentasi:** `DEBUG_LOGIN_ISSUE.md`

---

### 3. Session Conflict
**Gejala:** Data user berbeda antara localhost:8000 dan virtual host

**Solusi:**
1. Jalankan `clear-sessions.bat`
2. Pilih satu environment saja
3. Clear browser cookies
4. Restart browser

**Dokumentasi:** `CARA_CLEAR_SESSION.md`

---

### 4. Redirect Loop
**Gejala:** Halaman terus redirect tanpa henti

**Solusi:**
1. Jalankan `clear-sessions.bat`
2. Cek middleware di routes
3. Cek APP_URL di `.env`
4. Clear browser cookies

---

### 5. Error 500 Internal Server Error
**Gejala:** Halaman menampilkan error 500

**Solusi:**
1. Cek Apache error log
2. Cek file `.htaccess`
3. Cek permission folder storage
4. Jalankan `clear-sessions.bat`

---

## 💡 Tips Mencegah Masalah

### 1. Gunakan Satu Environment
- Pilih virtual host ATAU `php artisan serve`
- Jangan gunakan keduanya bersamaan
- Recommended: Virtual host (lebih mirip production)

### 2. Clear Cache Setelah Perubahan
Setelah mengubah:
- File `.env` → `php artisan config:clear`
- Routes → `php artisan route:clear`
- Config → `php artisan config:clear`
- Views → `php artisan view:clear`

### 3. Gunakan Browser Incognito untuk Testing
- Tidak ada cache
- Tidak ada cookies lama
- Fresh session

---

## 🆘 Masih Bermasalah?

### 1. Cek Log
```bash
# Laravel log
storage/logs/laravel.log

# Apache error log (via Laragon)
Klik kanan Laragon → Apache → error.log
```

### 2. Debug Mode
```env
# File .env
APP_DEBUG=true
APP_ENV=local
```

### 3. Test dengan Command
```bash
# Test route
php artisan route:list --path=login

# Test config
php artisan config:show app.url
php artisan config:show session.driver

# Test database
php artisan tinker --execute "DB::connection()->getPdo();"
```

---

## 📞 Checklist Troubleshooting

- [ ] Laragon sudah running
- [ ] Apache mod_rewrite aktif
- [ ] File .htaccess ada di root project
- [ ] Database sudah running
- [ ] File .env sudah dikonfigurasi
- [ ] Session sudah di-clear
- [ ] Browser cookies sudah di-clear
- [ ] Browser sudah di-restart

---

## 🎉 Selesai!

Semua tools troubleshooting sudah siap digunakan.

**Rekomendasi:**
1. Baca dokumentasi yang sesuai dengan masalah
2. Jalankan script yang diperlukan
3. Test dengan browser Incognito
4. Jika masih bermasalah, cek log

**Happy Troubleshooting! 🔧**

---

*Kembali ke: [Support for Developer](../README.md)*

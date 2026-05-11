# 👨‍💻 Developer Guide - SIM Keanggotaan IMM

Panduan lengkap untuk developer dalam development, troubleshooting, dan deployment project.

---

## 📁 Struktur Dokumentasi

```
📁 Support for Developer/
│
├── 📖 README.md (Navigasi utama)
│
├── 📁 troubleshooting/
│   ├── 📖 README.md
│   ├── 📄 LARAGON_SETUP.md
│   ├── 📄 DEBUG_LOGIN_ISSUE.md
│   ├── 📄 CARA_CLEAR_SESSION.md
│   ├── 🔧 clear-sessions.bat
│   ├── 🔧 quick-clear.bat
│   └── 🔧 clear-sessions.sh
│
└── 📁 Deployment on local computer/
    ├── 📖 README.md
    ├── 📄 INDEX_SHARE_PROJECT.md
    ├── 📄 QUICK_START_SHARE.md
    ├── 📄 SHARE_PROJECT_GRATIS.md
    ├── 📄 README_SHARE_PROJECT.md
    ├── 📄 TEMPLATE_PESAN_TEMAN.md
    ├── 📄 CHEATSHEET_SHARE.md
    ├── 🚀 share-project.bat
    ├── 🚀 share-localtunnel.bat
    ├── 🚀 share-ngrok.bat
    └── 🚀 setup-demo-account.bat
```

---

## 🎯 Quick Access

### 🔧 Troubleshooting

**Masalah Virtual Host?**
```
📂 Support for Developer/troubleshooting/
📄 LARAGON_SETUP.md
```

**Masalah Login?**
```
📂 Support for Developer/troubleshooting/
📄 DEBUG_LOGIN_ISSUE.md
🔧 clear-sessions.bat
```

**Masalah Session?**
```
📂 Support for Developer/troubleshooting/
📄 CARA_CLEAR_SESSION.md
🔧 clear-sessions.bat
```

---

### 🚀 Deployment

**Mau Share Project?**
```
📂 Support for Developer/Deployment on local computer/
📄 QUICK_START_SHARE.md (Baca ini dulu)
🚀 share-project.bat (Jalankan ini)
```

**Butuh Template Pesan?**
```
📂 Support for Developer/Deployment on local computer/
📄 TEMPLATE_PESAN_TEMAN.md
```

---

## 🚀 Quick Start

### Development

#### 1. Setup Virtual Host Laragon
```bash
# Baca panduan:
Support for Developer/troubleshooting/LARAGON_SETUP.md

# Akses project:
http://sim-keanggotaan-imm.test
```

#### 2. Clear Session (Jika Perlu)
```bash
# Jalankan script:
Support for Developer/troubleshooting/clear-sessions.bat
```

---

### Deployment

#### 1. Setup Akun Demo
```bash
# Jalankan script:
Support for Developer/Deployment on local computer/setup-demo-account.bat
```

#### 2. Share Project
```bash
# Jalankan menu utama:
Support for Developer/Deployment on local computer/share-project.bat

# Pilih metode (LocalTunnel/Ngrok)
```

#### 3. Kirim ke Teman
```bash
# Copy template:
Support for Developer/Deployment on local computer/TEMPLATE_PESAN_TEMAN.md
```

---

## 📚 Dokumentasi Lengkap

### 🔧 Troubleshooting

| Dokumen | Deskripsi | Kapan Digunakan |
|---------|-----------|-----------------|
| **LARAGON_SETUP.md** | Setup virtual host Laragon | Virtual host tidak bisa diakses |
| **DEBUG_LOGIN_ISSUE.md** | Troubleshooting masalah login | Route /login tidak bisa diakses |
| **CARA_CLEAR_SESSION.md** | Panduan clear session & cache | Session conflict, redirect loop |

### 🚀 Deployment

| Dokumen | Deskripsi | Untuk Siapa |
|---------|-----------|-------------|
| **QUICK_START_SHARE.md** | Panduan cepat 5 menit | ⭐ Pemula |
| **SHARE_PROJECT_GRATIS.md** | Panduan lengkap semua metode | Yang mau explore |
| **TEMPLATE_PESAN_TEMAN.md** | Template pesan WA/Telegram | Saat kirim ke teman |
| **CHEATSHEET_SHARE.md** | Quick reference | Quick lookup |

---

## 🛠️ Script Tools

### 🔧 Troubleshooting Scripts

| Script | Fungsi | Platform |
|--------|--------|----------|
| **clear-sessions.bat** | Clear session & cache (lengkap) | Windows |
| **quick-clear.bat** | Clear session & cache (cepat) | Windows |
| **clear-sessions.sh** | Clear session & cache | Linux/Mac |

**Cara pakai:**
```bash
cd "Support for Developer/troubleshooting"
clear-sessions.bat
```

---

### 🚀 Deployment Scripts

| Script | Fungsi | Kapan Digunakan |
|--------|--------|-----------------|
| **share-project.bat** | Menu utama (all-in-one) | ⭐ Recommended |
| **share-localtunnel.bat** | Share via LocalTunnel | Demo cepat (1-2 jam) |
| **share-ngrok.bat** | Share via Ngrok | Presentasi (seharian) |
| **setup-demo-account.bat** | Setup akun demo | Sebelum share |

**Cara pakai:**
```bash
cd "Support for Developer/Deployment on local computer"
share-project.bat
```

---

## 💡 Tips & Best Practices

### Development

#### ✅ DO
- Gunakan virtual host Laragon (bukan `php artisan serve`)
- Clear session saat berpindah environment
- Test di browser Incognito
- Commit code secara berkala
- Tulis test untuk fitur baru

#### ❌ DON'T
- Jangan gunakan localhost:8000 dan virtual host bersamaan
- Jangan commit file `.env`
- Jangan commit folder `vendor/` dan `node_modules/`
- Jangan skip migration

---

### Deployment

#### ✅ DO
- Buat akun demo (jangan pakai data real)
- Test akses sendiri dulu
- Laptop harus tetap nyala
- Gunakan WiFi (bukan hotspot)
- Minta feedback dari teman

#### ❌ DON'T
- Jangan share data sensitif
- Jangan sleep/hibernate laptop
- Jangan tutup terminal tunnel
- Jangan lupa ganti URL di template

---

## 🔍 Troubleshooting Umum

### Masalah Virtual Host

**Gejala:** http://sim-keanggotaan-imm.test tidak bisa diakses

**Solusi:**
```bash
1. Baca: Support for Developer/troubleshooting/LARAGON_SETUP.md
2. Cek Apache mod_rewrite aktif
3. Cek file .htaccess di root project
4. Restart Laragon
```

---

### Masalah Login

**Gejala:** Route /login tidak bisa diakses

**Solusi:**
```bash
1. Jalankan: Support for Developer/troubleshooting/clear-sessions.bat
2. Clear browser cookies
3. Restart browser
4. Test dengan Incognito mode
```

---

### Masalah Session

**Gejala:** Session conflict, redirect loop

**Solusi:**
```bash
1. Jalankan: Support for Developer/troubleshooting/clear-sessions.bat
2. Pilih satu environment (virtual host atau php artisan serve)
3. Clear browser cookies
4. Restart browser
```

---

## 📞 Checklist

### Sebelum Development
- [ ] Laragon sudah running
- [ ] Virtual host bisa diakses
- [ ] Database sudah migrate
- [ ] Composer dependencies sudah install
- [ ] NPM dependencies sudah install
- [ ] File .env sudah dikonfigurasi

### Sebelum Share Project
- [ ] Akun demo sudah dibuat
- [ ] Project sudah di-test
- [ ] Tunnel sudah jalan
- [ ] URL sudah dicopy
- [ ] Template pesan sudah disiapkan
- [ ] Laptop terkoneksi WiFi

---

## 🆘 Butuh Bantuan?

### Masalah Development
👉 Buka: `Support for Developer/troubleshooting/README.md`

### Masalah Deployment
👉 Buka: `Support for Developer/Deployment on local computer/README.md`

### Dokumentasi Lengkap
👉 Buka: `Support for Developer/README.md`

---

## 🎓 Learning Resources

### Laravel
- Official Docs: https://laravel.com/docs
- Laracasts: https://laracasts.com
- Laravel News: https://laravel-news.com

### Tailwind CSS
- Official Docs: https://tailwindcss.com/docs
- Tailwind UI: https://tailwindui.com

### Pest PHP
- Official Docs: https://pestphp.com/docs

---

## 🎉 Selamat Coding!

Semua tools dan dokumentasi sudah tersedia untuk membantu development dan deployment project kamu.

**Happy Coding! 🚀**

---

## 📋 Quick Reference

### Command Sering Dipakai

```bash
# Development
php artisan serve
php artisan migrate
php artisan db:seed
php artisan test

# Clear cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# Deployment
npm install -g localtunnel
lt --port 80 --subdomain sim-imm
ngrok http 80 --host-header=sim-keanggotaan-imm.test

# Troubleshooting
clear-sessions.bat
php artisan route:list
php artisan config:show app.url
```

---

*Project: SIM Keanggotaan IMM*  
*Tech Stack: Laravel 13 + PHP 8.4 + Tailwind CSS + MySQL*  
*Last updated: 2026-05-11*

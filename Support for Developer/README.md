# 📚 Support for Developer

Dokumentasi dan tools untuk membantu developer dalam development dan troubleshooting project SIM Keanggotaan IMM.

---

## 📁 Struktur Folder

```
📁 Support for Developer/
├── 📖 README.md (file ini)
│
├── 📁 troubleshooting/
│   ├── 🔧 Script untuk troubleshooting
│   └── 📖 Dokumentasi troubleshooting
│
└── 📁 Deployment on local computer/
    ├── 🚀 Script untuk deployment lokal
    └── 📖 Dokumentasi deployment
```

---

## 🔧 Troubleshooting

**Folder:** `troubleshooting/`

Berisi tools dan dokumentasi untuk mengatasi masalah development:

### 📄 Dokumentasi
- **`LARAGON_SETUP.md`** - Setup virtual host Laragon
- **`DEBUG_LOGIN_ISSUE.md`** - Troubleshooting masalah login
- **`CARA_CLEAR_SESSION.md`** - Panduan clear session & cache

### 🛠️ Script
- **`clear-sessions.bat`** - Clear session & cache (lengkap)
- **`quick-clear.bat`** - Clear session & cache (cepat)
- **`clear-sessions.sh`** - Clear session & cache (Linux/Mac)

### 🎯 Kapan Digunakan?
- ✅ Masalah login tidak bisa diakses
- ✅ Session conflict antara localhost:8000 dan virtual host
- ✅ Redirect loop
- ✅ Virtual host tidak bisa diakses
- ✅ Setelah mengubah .env atau konfigurasi

---

## 🚀 Deployment on Local Computer

**Folder:** `Deployment on local computer/`

Berisi tools dan dokumentasi untuk share project ke teman/dosen tanpa hosting:

### 📄 Dokumentasi
- **`INDEX_SHARE_PROJECT.md`** - Navigasi lengkap (mulai dari sini)
- **`QUICK_START_SHARE.md`** - Panduan cepat 5 menit ⭐
- **`SHARE_PROJECT_GRATIS.md`** - Panduan lengkap semua metode
- **`README_SHARE_PROJECT.md`** - Ringkasan & perbandingan
- **`TEMPLATE_PESAN_TEMAN.md`** - Template pesan untuk teman
- **`CHEATSHEET_SHARE.md`** - Quick reference

### 🛠️ Script
- **`share-project.bat`** - Menu utama (all-in-one) ⭐
- **`share-localtunnel.bat`** - Share via LocalTunnel
- **`share-ngrok.bat`** - Share via Ngrok
- **`setup-demo-account.bat`** - Setup akun demo

### 🎯 Kapan Digunakan?
- ✅ Mau demo project ke teman
- ✅ Presentasi ke dosen/pembimbing
- ✅ Testing dengan user real
- ✅ Mau dapat feedback
- ✅ Portfolio online tanpa hosting

---

## 🎯 Quick Start

### Masalah Session/Login?
```bash
cd "Support for Developer/troubleshooting"
clear-sessions.bat
```

### Mau Share Project?
```bash
cd "Support for Developer/Deployment on local computer"
share-project.bat
```

---

## 📖 Panduan Lengkap

### Troubleshooting
1. Buka folder: `troubleshooting/`
2. Baca: `LARAGON_SETUP.md` untuk setup virtual host
3. Baca: `DEBUG_LOGIN_ISSUE.md` untuk masalah login
4. Baca: `CARA_CLEAR_SESSION.md` untuk clear session

### Deployment
1. Buka folder: `Deployment on local computer/`
2. Baca: `INDEX_SHARE_PROJECT.md` untuk navigasi
3. Baca: `QUICK_START_SHARE.md` untuk panduan cepat
4. Jalankan: `share-project.bat` untuk mulai share

---

## 🆘 Butuh Bantuan?

### Masalah Virtual Host Laragon
👉 `troubleshooting/LARAGON_SETUP.md`

### Masalah Login Tidak Bisa Diakses
👉 `troubleshooting/DEBUG_LOGIN_ISSUE.md`

### Masalah Session
👉 `troubleshooting/CARA_CLEAR_SESSION.md`

### Mau Share Project
👉 `Deployment on local computer/QUICK_START_SHARE.md`

---

## 💡 Tips

### Development
- Gunakan virtual host Laragon (bukan `php artisan serve`)
- Clear session saat berpindah environment
- Selalu test di browser Incognito untuk memastikan

### Deployment
- Pakai LocalTunnel untuk demo cepat (no signup)
- Pakai Ngrok untuk presentasi (lebih stabil)
- Laptop harus tetap nyala saat demo
- Buat akun demo, jangan pakai data real

---

## 📞 Checklist

### Sebelum Development
- [ ] Laragon sudah running
- [ ] Virtual host bisa diakses
- [ ] Database sudah migrate
- [ ] Session sudah clear (jika perlu)

### Sebelum Share Project
- [ ] Akun demo sudah dibuat
- [ ] Tunnel sudah jalan
- [ ] URL sudah dicopy
- [ ] Template pesan sudah disiapkan
- [ ] Laptop terkoneksi WiFi

---

## 🎉 Selamat Menggunakan!

Semua tools dan dokumentasi sudah tersedia untuk membantu development dan deployment project kamu.

**Happy Coding! 🚀**

---

*Project: SIM Keanggotaan IMM*  
*Last updated: 2026-05-11*

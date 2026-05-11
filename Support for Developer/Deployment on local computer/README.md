# 🚀 Deployment on Local Computer

Tools dan dokumentasi untuk share project Laravel ke teman/dosen tanpa hosting (100% GRATIS).

---

## 📁 File yang Tersedia

### 📖 Dokumentasi

#### 1. **INDEX_SHARE_PROJECT.md** - Navigasi Lengkap ⭐ MULAI DARI SINI
**Isi:**
- Navigasi semua file
- Workflow lengkap
- Perbandingan metode
- Quick reference

---

#### 2. **QUICK_START_SHARE.md** - Panduan Cepat 5 Menit ⭐ RECOMMENDED
**Isi:**
- Setup LocalTunnel (paling mudah)
- Setup Ngrok (paling stabil)
- Setup akun demo
- Template pesan untuk teman

**Untuk siapa:**
- Pemula yang baru pertama kali
- Yang mau langsung praktek
- Yang tidak mau baca panjang

---

#### 3. **SHARE_PROJECT_GRATIS.md** - Panduan Lengkap Semua Metode
**Isi:**
- Laravel Herd + Expose
- Ngrok (detail)
- Cloudflare Tunnel
- LocalTunnel
- Serveo
- Perbandingan lengkap
- Konfigurasi Laravel
- Tips keamanan

**Untuk siapa:**
- Yang mau explore semua opsi
- Yang mau setup jangka panjang
- Yang butuh detail teknis

---

#### 4. **README_SHARE_PROJECT.md** - Ringkasan & Perbandingan
**Isi:**
- Ringkasan semua file
- Perbandingan metode
- Quick start
- Troubleshooting

**Untuk siapa:**
- Quick reference
- Yang sudah familiar

---

#### 5. **TEMPLATE_PESAN_TEMAN.md** - Template Pesan
**Isi:**
- Template WhatsApp/Telegram
- Template formal (dosen)
- Template grup
- Template Instagram/LinkedIn
- Tips menulis pesan

**Untuk siapa:**
- Saat mau kirim URL ke teman
- Butuh template siap pakai

---

#### 6. **CHEATSHEET_SHARE.md** - Quick Reference
**Isi:**
- Command paling sering dipakai
- Template pesan singkat
- Troubleshooting cepat
- Checklist

**Untuk siapa:**
- Quick reference
- Yang sudah pernah setup

---

### 🛠️ Script

#### 1. **share-project.bat** - Menu Utama (All-in-One) ⭐ RECOMMENDED
**Fungsi:**
- Menu interaktif
- Pilih metode (LocalTunnel/Ngrok)
- Setup akun demo
- Lihat panduan

**Cara pakai:**
```bash
# Double-click file ini
share-project.bat

# Atau via terminal
cd "Support for Developer/Deployment on local computer"
share-project.bat
```

---

#### 2. **share-localtunnel.bat** - Share via LocalTunnel
**Fungsi:**
- Share project via LocalTunnel
- Paling mudah (no signup)
- Auto install jika belum ada

**Kapan digunakan:**
- Demo cepat ke teman (1-2 jam)
- Tidak mau daftar akun
- Setup cepat 5 menit

**Cara pakai:**
```bash
# Double-click file ini
share-localtunnel.bat
```

---

#### 3. **share-ngrok.bat** - Share via Ngrok
**Fungsi:**
- Share project via Ngrok
- Paling stabil
- Perlu signup (gratis)

**Kapan digunakan:**
- Presentasi ke dosen (seharian)
- Butuh koneksi stabil
- Demo penting

**Cara pakai:**
```bash
# Setup dulu (sekali aja):
# 1. Download: https://ngrok.com/download
# 2. Daftar: https://dashboard.ngrok.com/signup
# 3. Setup: ngrok config add-authtoken YOUR_TOKEN

# Kemudian jalankan:
share-ngrok.bat
```

---

#### 4. **setup-demo-account.bat** - Setup Akun Demo
**Fungsi:**
- Buat akun admin demo
- Buat akun kader demo
- Auto cek jika sudah ada

**Kapan digunakan:**
- Sebelum share ke teman
- Butuh akun untuk testing
- Jangan pakai data real

**Cara pakai:**
```bash
# Double-click file ini
setup-demo-account.bat
```

**Akun yang dibuat:**
- Admin: demo@example.com / demo123
- Kader: kader@example.com / demo123

---

## 🎯 Quick Start

### Pertama Kali (Setup)

#### Opsi 1: LocalTunnel (Paling Mudah)
```bash
# 1. Install Node.js dari: https://nodejs.org
# 2. Install LocalTunnel
npm install -g localtunnel

# 3. Setup akun demo
setup-demo-account.bat

# 4. Share project
share-localtunnel.bat
```

#### Opsi 2: Ngrok (Paling Stabil)
```bash
# 1. Download: https://ngrok.com/download
# 2. Daftar: https://dashboard.ngrok.com/signup
# 3. Setup authtoken
ngrok config add-authtoken YOUR_TOKEN

# 4. Setup akun demo
setup-demo-account.bat

# 5. Share project
share-ngrok.bat
```

---

### Sudah Pernah Setup (Tinggal Pakai)

```bash
# 1. Jalankan menu utama
share-project.bat

# 2. Pilih metode (LocalTunnel/Ngrok)

# 3. Copy URL yang muncul

# 4. Kirim ke teman (pakai template dari TEMPLATE_PESAN_TEMAN.md)
```

---

## 📊 Perbandingan Metode

| Metode | Setup | Stabil | Signup | URL Tetap | Rekomendasi |
|--------|-------|--------|--------|-----------|-------------|
| **LocalTunnel** | 5 min | ⭐⭐⭐ | ❌ | ❌ | Demo cepat (1-2 jam) |
| **Ngrok** | 10 min | ⭐⭐⭐⭐⭐ | ✅ | ❌ | Presentasi (seharian) |
| **Cloudflare** | 30 min | ⭐⭐⭐⭐⭐ | ✅ | ✅ | Jangka panjang (minggu) |

---

## 🎓 Workflow Lengkap

### 1️⃣ Persiapan (Sekali Aja)
```bash
# Pilih metode dan install tools
# LocalTunnel: npm install -g localtunnel
# Ngrok: Download + setup authtoken
```

### 2️⃣ Setup Akun Demo
```bash
setup-demo-account.bat
```

### 3️⃣ Share Project
```bash
share-project.bat
# Pilih metode (LocalTunnel/Ngrok)
```

### 4️⃣ Copy URL
```
URL muncul di terminal:
https://sim-imm.loca.lt (LocalTunnel)
https://abc123.ngrok-free.app (Ngrok)
```

### 5️⃣ Kirim ke Teman
```bash
# Copy template dari:
TEMPLATE_PESAN_TEMAN.md

# Ganti [PASTE_URL_TUNNEL_DISINI] dengan URL
# Kirim via WhatsApp/Telegram
```

### 6️⃣ Selesai Demo
```bash
# Tekan Ctrl+C di terminal tunnel
# Laptop bisa dimatikan
```

---

## 💡 Tips & Trik

### Untuk Demo Cepat (1-2 jam)
✅ Pakai **LocalTunnel**
- Tidak perlu daftar
- Setup 5 menit
- Langsung jalan

### Untuk Presentasi (Seharian)
✅ Pakai **Ngrok**
- Lebih stabil
- Dashboard monitoring
- HTTPS otomatis

### Untuk Portfolio (Jangka Panjang)
✅ Pakai **Cloudflare Tunnel**
- URL tetap
- Gratis unlimited
- Panduan di: SHARE_PROJECT_GRATIS.md

---

## ⚠️ Hal Penting

### ✅ DO
- Laptop harus tetap nyala
- Terkoneksi internet (WiFi, bukan hotspot)
- Laragon harus running
- Buat akun demo (jangan pakai data real)
- Test akses sendiri dulu
- Minta feedback dari teman

### ❌ DON'T
- Jangan sleep/hibernate laptop
- Jangan pakai hotspot HP (kuota cepat habis)
- Jangan share data sensitif/real
- Jangan lupa ganti URL di template
- Jangan tutup terminal tunnel

---

## 🔧 Troubleshooting

### "npm is not recognized"
**Solusi:** Install Node.js dari https://nodejs.org

### "ngrok is not recognized"
**Solusi:** 
1. Download dari https://ngrok.com/download
2. Extract ngrok.exe ke folder ini
3. Atau tambahkan ke PATH

### "Connection refused"
**Solusi:**
1. Pastikan Laragon running
2. Cek http://sim-keanggotaan-imm.test bisa diakses
3. Restart Laragon

### "Invalid host header"
**Solusi:** Tambahkan flag `--host-header=sim-keanggotaan-imm.test`

### Teman tidak bisa akses
**Solusi:**
1. Cek laptop masih nyala
2. Cek internet masih connect
3. Cek URL masih aktif (buka sendiri dulu)
4. Cek Laragon masih running

---

## 📞 Checklist Sebelum Share

- [ ] Laragon sudah running
- [ ] http://sim-keanggotaan-imm.test bisa diakses
- [ ] Akun demo sudah dibuat
- [ ] Tunnel sudah jalan
- [ ] URL sudah dicopy
- [ ] Template pesan sudah disiapkan
- [ ] Laptop terkoneksi WiFi
- [ ] Laptop tidak akan sleep

---

## 🎉 Selamat Mencoba!

Sekarang kamu bisa share project ke teman tanpa bayar hosting!

**Rekomendasi:**
1. Baca: `QUICK_START_SHARE.md` (5 menit)
2. Jalankan: `share-project.bat`
3. Copy template: `TEMPLATE_PESAN_TEMAN.md`
4. Kirim ke teman!

**Happy Sharing! 🚀**

---

*Kembali ke: [Support for Developer](../README.md)*

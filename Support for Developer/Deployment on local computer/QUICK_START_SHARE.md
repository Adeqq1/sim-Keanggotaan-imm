# 🚀 Quick Start: Share Project ke Teman (5 Menit)

## Pilih Metode (Pilih Salah Satu)

### 🔥 Metode 1: LocalTunnel (PALING MUDAH - NO SIGNUP)

#### Step 1: Install LocalTunnel
```bash
npm install -g localtunnel
```

#### Step 2: Jalankan Script
```bash
# Double-click file ini:
share-localtunnel.bat
```

#### Step 3: Copy URL & Share
```
URL muncul: https://sim-imm.loca.lt
Kirim ke teman!
```

**SELESAI!** ✅

---

### ⭐ Metode 2: Ngrok (PALING STABIL)

#### Step 1: Download & Setup Ngrok
1. Download: https://ngrok.com/download
2. Extract `ngrok.exe` ke folder project
3. Daftar gratis: https://dashboard.ngrok.com/signup
4. Copy authtoken dari dashboard
5. Setup:
   ```bash
   ngrok config add-authtoken YOUR_AUTH_TOKEN
   ```

#### Step 2: Jalankan Script
```bash
# Double-click file ini:
share-ngrok.bat
```

#### Step 3: Copy URL & Share
```
URL muncul: https://abc123.ngrok-free.app
Kirim ke teman!
```

**SELESAI!** ✅

---

## 🎯 Setup Akun Demo (Optional)

Biar teman bisa langsung login:

```bash
# Double-click file ini:
setup-demo-account.bat
```

Akun yang dibuat:
- **Admin**: demo@example.com / demo123
- **Kader**: kader@example.com / demo123

---

## 📱 Template Pesan untuk Teman

Copy-paste ini ke WhatsApp/Telegram:

```
Halo! Coba lihat project SIM Keanggotaan IMM aku ya! 🚀

🌐 URL: https://YOUR_TUNNEL_URL_HERE

Login sebagai Admin:
👤 Email: demo@example.com
🔑 Password: demo123

Login sebagai Kader:
👤 Email: kader@example.com
🔑 Password: demo123

Fitur yang bisa dicoba:
✅ Dashboard admin & kader
✅ Manajemen anggota
✅ Kegiatan & presensi
✅ E-KTA digital
✅ Sertifikat
✅ Laporan

Note: Laptop aku harus nyala ya, kalau mati nanti gak bisa diakses 😅

Kasih feedback ya! 🙏
```

---

## ⚠️ Checklist Sebelum Share

- [ ] Laragon sudah running
- [ ] http://sim-keanggotaan-imm.test bisa diakses
- [ ] Akun demo sudah dibuat
- [ ] Laptop terkoneksi WiFi (bukan hotspot HP)
- [ ] Laptop tidak akan sleep/hibernate
- [ ] Database sudah ada data sample

---

## 🔧 Troubleshooting Cepat

### "npm is not recognized"
Install Node.js: https://nodejs.org

### "ngrok is not recognized"
Pastikan ngrok.exe ada di folder project atau di PATH

### "Connection refused"
Pastikan Laragon sudah running

### Teman tidak bisa akses
- Cek laptop masih nyala
- Cek internet masih connect
- Cek URL masih aktif (buka sendiri dulu)

---

## 🎉 Selesai!

Sekarang teman-teman bisa akses project kamu dari mana aja!

**Rekomendasi:**
- Untuk demo cepat: Pakai **LocalTunnel** (no signup)
- Untuk presentasi: Pakai **Ngrok** (lebih stabil)

Laptop harus tetap nyala selama demo ya! 💻

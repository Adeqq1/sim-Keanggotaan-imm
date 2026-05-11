# 📋 Cheatsheet: Share Project (Quick Reference)

## ⚡ Super Quick Start

```bash
# 1. Install (pilih salah satu)
npm install -g localtunnel

# 2. Jalankan
lt --port 80 --subdomain sim-imm

# 3. Copy URL, kirim ke teman
```

---

## 🎯 Command Paling Sering Dipakai

### LocalTunnel
```bash
# Install
npm install -g localtunnel

# Jalankan
lt --port 80 --subdomain sim-imm

# Atau pakai script
share-localtunnel.bat
```

### Ngrok
```bash
# Setup (sekali aja)
ngrok config add-authtoken YOUR_TOKEN

# Jalankan
ngrok http 80 --host-header=sim-keanggotaan-imm.test

# Atau pakai script
share-ngrok.bat
```

### Setup Demo Account
```bash
# Via script
setup-demo-account.bat

# Via command
php artisan tinker --execute "User::create(['name'=>'Demo Admin','email'=>'demo@example.com','password'=>bcrypt('demo123'),'role'=>'admin']);"
```

### Clear Session
```bash
# Via script
clear-sessions.bat

# Via command
php artisan tinker --execute "DB::table('sessions')->truncate();"
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

---

## 📱 Template Pesan Singkat

```
Halo! Coba project aku:
🌐 [URL_TUNNEL]

Login Admin:
📧 demo@example.com
🔑 demo123

Login Kader:
📧 kader@example.com
🔑 demo123
```

---

## 🔧 Troubleshooting Cepat

| Masalah | Solusi |
|---------|--------|
| npm not found | Install Node.js dari nodejs.org |
| ngrok not found | Download dari ngrok.com/download |
| Connection refused | Pastikan Laragon running |
| Invalid host header | Tambah: `--host-header=sim-keanggotaan-imm.test` |
| Teman tidak bisa akses | Cek laptop nyala & internet connect |

---

## 📊 Perbandingan Cepat

| Metode | Setup | Stabil | Signup |
|--------|-------|--------|--------|
| LocalTunnel | 5 min | ⭐⭐⭐ | ❌ |
| Ngrok | 10 min | ⭐⭐⭐⭐⭐ | ✅ |

---

## ⚠️ Checklist

- [ ] Laragon running
- [ ] Akun demo dibuat
- [ ] Tunnel jalan
- [ ] URL dicopy
- [ ] Laptop tidak sleep

---

## 🎯 File Penting

| File | Fungsi |
|------|--------|
| `share-project.bat` | Menu utama |
| `QUICK_START_SHARE.md` | Panduan 5 menit |
| `TEMPLATE_PESAN_TEMAN.md` | Template WA |

---

## 💡 Tips

- Demo cepat → LocalTunnel
- Presentasi → Ngrok
- Laptop harus nyala
- Pakai WiFi, bukan hotspot

---

*Buka INDEX_SHARE_PROJECT.md untuk navigasi lengkap*

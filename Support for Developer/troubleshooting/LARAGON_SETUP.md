# Panduan Setup Virtual Host Laragon

## Masalah yang Diperbaiki
Aplikasi dapat diakses melalui `php artisan serve` (localhost:8000) tetapi tidak dapat diakses melalui virtual host Laragon (sim-keanggotaan-imm.test).

## Solusi yang Diterapkan

### 1. File .htaccess di Root Project
Telah dibuat file `.htaccess` di root project untuk mengarahkan semua request ke folder `public`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### 2. Cache Telah Dibersihkan
Semua cache Laravel telah dibersihkan:
- Configuration cache
- Route cache
- Application cache

### 3. Konfigurasi APP_URL
File `.env` sudah dikonfigurasi dengan benar:
```
APP_URL=http://sim-keanggotaan-imm.test
```

## Langkah-Langkah Verifikasi di Laragon

### 1. Pastikan Apache mod_rewrite Aktif
1. Buka Laragon
2. Klik kanan pada Laragon tray icon
3. Pilih **Apache** → **httpd.conf**
4. Cari baris: `#LoadModule rewrite_module modules/mod_rewrite.so`
5. Hapus tanda `#` jika ada (uncomment)
6. Simpan file
7. Restart Apache dari Laragon

### 2. Verifikasi Virtual Host Configuration
1. Buka Laragon
2. Klik kanan pada Laragon tray icon
3. Pilih **Apache** → **sites-enabled**
4. Cari file `sim-keanggotaan-imm.test.conf` atau `auto.sim-keanggotaan-imm.test.conf`
5. Pastikan isinya seperti ini:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/laragon/www/sim-keanggotaan-imm/"
    ServerName sim-keanggotaan-imm.test
    ServerAlias *.sim-keanggotaan-imm.test
    <Directory "C:/laragon/www/sim-keanggotaan-imm/">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**PENTING:** Pastikan `DocumentRoot` mengarah ke **root folder project**, bukan ke folder `public`. File `.htaccess` di root akan menangani redirect ke folder `public`.

### 3. Verifikasi File hosts
1. Buka Laragon
2. Klik kanan pada Laragon tray icon
3. Pilih **Tools** → **Edit hosts file**
4. Pastikan ada baris:
```
127.0.0.1    sim-keanggotaan-imm.test
```

### 4. Restart Laragon
1. Stop semua service di Laragon
2. Start kembali semua service
3. Atau klik **Menu** → **Reload**

### 5. Test Akses
Buka browser dan akses:
- http://sim-keanggotaan-imm.test
- http://sim-keanggotaan-imm.test/login
- http://sim-keanggotaan-imm.test/pendaftaran

## Troubleshooting

### Masalah Khusus: Route /login Tidak Bisa Diakses

**Gejala:** Homepage dan route lain bisa diakses, tapi `/login` tidak bisa (redirect atau blank)

**Penyebab:** User masih terautentikasi dari session sebelumnya (misalnya dari `php artisan serve`). Middleware `guest` akan redirect user yang sudah login.

**Solusi Cepat:**

1. **Jalankan script clear-sessions:**
   ```bash
   # Windows
   clear-sessions.bat
   
   # Linux/Mac
   bash clear-sessions.sh
   ```

2. **Atau manual - Clear session dari database:**
   ```bash
   php artisan tinker --execute "DB::table('sessions')->truncate();"
   ```

3. **Clear browser cookies:**
   - Tekan F12 → Application/Storage → Cookies
   - Hapus semua cookies untuk domain sim-keanggotaan-imm.test
   - Atau gunakan mode Incognito/Private

4. **Restart browser sepenuhnya** (tutup semua window)

5. **Test akses:** http://sim-keanggotaan-imm.test/login

**Debug lebih lanjut:**
- Akses http://sim-keanggotaan-imm.test/debug-auth untuk cek status autentikasi
- Lihat file `DEBUG_LOGIN_ISSUE.md` untuk panduan lengkap

### Jika Masih Tidak Bisa Akses

#### 1. Cek Permission Folder
Pastikan folder project memiliki permission yang benar:
```bash
# Di terminal Laragon
chmod -R 755 storage bootstrap/cache
```

#### 2. Regenerate Virtual Host
1. Buka Laragon
2. Klik kanan pada project di menu Laragon
3. Pilih **Delete** (hanya menghapus virtual host, bukan project)
4. Klik kanan lagi dan pilih **Create Virtual Host**

#### 3. Cek Apache Error Log
1. Buka Laragon
2. Klik kanan pada Laragon tray icon
3. Pilih **Apache** → **error.log**
4. Lihat error terakhir untuk debugging

#### 4. Clear Browser Cache
- Tekan `Ctrl + Shift + Delete`
- Atau coba akses dengan mode Incognito/Private

#### 5. Flush DNS
Buka Command Prompt sebagai Administrator dan jalankan:
```cmd
ipconfig /flushdns
```

### Jika Muncul Error 500

Jalankan perintah berikut:
```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

Pastikan juga file `.env` sudah dikonfigurasi dengan benar dan database sudah running.

## Perbedaan php artisan serve vs Virtual Host

| Aspek | php artisan serve | Virtual Host Laragon |
|-------|-------------------|---------------------|
| Server | PHP Built-in Server | Apache/Nginx |
| Port | 8000 (default) | 80 (default HTTP) |
| Domain | localhost | Custom domain (.test) |
| .htaccess | Tidak digunakan | Digunakan |
| Performance | Lebih lambat | Lebih cepat |
| Production-like | Tidak | Ya |
| Multiple Projects | Harus ganti port | Bisa banyak domain |

## Rekomendasi

Untuk development dan final project, **gunakan Virtual Host Laragon** karena:
1. Lebih mirip dengan environment production
2. Mendukung multiple projects dengan domain berbeda
3. Performance lebih baik
4. Mendukung fitur Apache/Nginx seperti .htaccess
5. Tidak perlu menjalankan `php artisan serve` setiap kali development

## Catatan Penting

- Setiap kali mengubah file `.env`, jalankan: `php artisan config:clear`
- Setiap kali mengubah routes, jalankan: `php artisan route:clear`
- Jangan commit file cache ke Git (sudah ada di .gitignore)
- File `.htaccess` di root project **harus ada** untuk virtual host bekerja dengan benar

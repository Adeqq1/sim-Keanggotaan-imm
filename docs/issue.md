# ISSUE: Hapus Fitur Sending Mail

**Prioritas:** Medium
**Estimasi Waktu:** 30-45 menit
**Target:** Junior Programmer / AI Model Ekonomis

---

## 📋 Deskripsi

Proyek saat ini memiliki fitur pengiriman email otomatis saat admin memvalidasi pendaftaran calon kader:
- **Email disetujui** — dikirim saat admin menyetujui pendaftaran, berisi password sementara dan link login
- **Email ditolak** — dikirim saat admin menolak pendaftaran, berisi catatan alasan penolakan

**Tujuan:** Hapus fitur pengiriman email ini sepenuhnya. Proses validasi pendaftaran (setujui/tolak) tetap berjalan normal — hanya bagian kirim email-nya yang dihapus.

---

## 🔍 Context & Scope

### Yang DIHAPUS ✅
- 2 file Mailable class
- 2 file template email Blade
- 1 folder email templates
- 2 private method pengirim email di Controller
- Import statement yang sudah tidak terpakai
### Yang TIDAK BOLEH disentuh 🚫
- **`.env` & `.env.example`** — kredensial email disimpan di sini. JANGAN disentuh, biarin apa adanya.
- **`config/mail.php`** — Laravel default config, harmless karena `MAIL_MAILER=log` di `.env`
- **`routes/auth.php`** — built-in auth routes, tidak mengirim email apapun (MustVerifyEmail sudah disabled)
- **`vendor/`** — tidak perlu composer remove apapun
- **Method `validatePendaftaran()`** — logika bisnis setujui/tolak tetap harus jalan (buat User, buat Anggota, update status)

---

## ✅ Tahapan Implementasi

Kerjakan **berurutan**. Setiap tahap selesai → test dulu sebelum lanjut.

---

### 🔴 TAHAP 1 — Hapus Method Kirim Email di Controller

**File:** `app/Http/Controllers/ValidasiPendaftaranController.php`

**Langkah 1.1:** Hapus 5 baris `use` statement yang sudah tidak diperlukan (baris 5, 6, 11, 12, 15):

```php
// HAPUS 5 baris ini:
use App\Mail\PendaftaranDisetujuiMail;
use App\Mail\PendaftaranDitolakMail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
```

> ⚠️ Jangan hapus `use Illuminate\Support\Facades\DB;` dan `use Illuminate\Support\Facades\Hash;` — masih dipakai.

**Langkah 1.2:** Hapus pemanggilan method `kirimEmailDisetujui(...)` di **baris 72**:

```php
// SEBELUM (baris 70-74):
            });

            $this->kirimEmailDisetujui($pendaftar->fresh());

            return redirect()->route('admin.pendaftaran.index')->with('success', 'Pendaftaran disetujui.');

// SESUDAH:
            });

            return redirect()->route('admin.pendaftaran.index')->with('success', 'Pendaftaran disetujui.');
```

**Langkah 1.3:** Hapus pemanggilan method `kirimEmailDitolak(...)` di **baris 86**:

```php
// SEBELUM (baris 84-88):
        ]);

        $this->kirimEmailDitolak($pendaftar->fresh());

        return redirect()->route('admin.pendaftaran.index')->with('success', 'Pendaftaran ditolak.');

// SESUDAH:
        ]);

        return redirect()->route('admin.pendaftaran.index')->with('success', 'Pendaftaran ditolak.');
```

**Langkah 1.4:** Hapus kedua private method di bagian bawah class:

```php
// HAPUS SELURUH BLOK INI (baris 91-126):
    private function kirimEmailDisetujui(Pendaftaran $pendaftaran): void
    {
        try {
            // Password di-enkripsi agar tidak tersimpan plain-text di kolom payload tabel jobs.
            $passwordEncrypted = Crypt::encryptString(self::DEFAULT_KADER_PASSWORD);

            Mail::to($pendaftaran->email)
                ->queue(new PendaftaranDisetujuiMail($pendaftaran, $passwordEncrypted));
        } catch (\Throwable $e) {
            Log::error('Gagal mendispatch email persetujuan pendaftaran ke queue', [
                'pendaftaran_id' => $pendaftaran->id,
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function kirimEmailDitolak(Pendaftaran $pendaftaran): void
    {
        try {
            Mail::to($pendaftaran->email)
                ->queue(new PendaftaranDitolakMail($pendaftaran));
        } catch (\Throwable $e) {
            Log::error('Gagal mendispatch email penolakan pendaftaran ke queue', [
                'pendaftaran_id' => $pendaftaran->id,
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
```

> 💡 **Tips verifikasi:** Buka file `ValidasiPendaftaranController.php`, Ctrl+F `Mail`, `kirimEmail`, `Crypt::`, `Log::` — harusnya **tidak ada satupun** yang tersisa.

---

### 🔴 TAHAP 2 — Hapus Mailable Class

Hapus 2 file Mailable class:

```bash
# Jalankan dari root project:
rm app/Mail/PendaftaranDisetujuiMail.php
rm app/Mail/PendaftaranDitolakMail.php
```

Setelah dihapus, cek apakah folder `app/Mail/` kosong. Jika kosong, hapus foldernya:

```bash
# Cek dulu:
ls app/Mail/

# Jika output kosong (no such file or empty), hapus folder:
rmdir app/Mail/
```

> 💡 **Tips:** `rmdir` hanya bisa menghapus folder kosong (lebih aman daripada `rm -rf`).

---

### 🔴 TAHAP 3 — Hapus Template Email Blade

Hapus file dan folder template email:

```bash
# Hapus 2 file template:
rm resources/views/emails/pendaftaran/disetujui.blade.php
rm resources/views/emails/pendaftaran/ditolak.blade.php

# Hapus folder pendaftaran (harusnya sudah kosong):
rmdir resources/views/emails/pendaftaran/

# Hapus folder emails (harusnya sudah kosong):
rmdir resources/views/emails/
```

> ⚠️ **Hati-hati:** Jangan sampai menghapus folder `resources/views/` — pastiin path yang diketik benar. Double-check sebelum execute.

---

### 🔴 TAHAP 4 — Verifikasi .env (TIDAK USAH DIUBAH)

**`.env` dan `.env.example` TIDAK PERLU disentuh.**

Alasannya:
- `MAIL_MAILER=log` sudah diset di `.env` — artinya semua email ditulis ke log file, bukan dikirim via SMTP beneran.
- Kredensial yang ada di file itu tetap disimpan apa adanya (biar gak lupa).
- Setelah Tahap 1-3 selesai, kode yang manggil `Mail::` sudah tidak ada → email tidak akan pernah dikirim sama sekali, apapun isi `.env`-nya.

---

### 🔴 TAHAP 5 — Verifikasi & Testing

**5.1 — Cari referensi yang tersisa:**

```bash
# Harusnya TIDAK ADA output:
grep -rn "PendaftaranDisetujuiMail\|PendaftaranDitolakMail" app/ routes/ resources/views/ --include="*.php"
grep -rn "kirimEmail\|->queue(" app/ --include="*.php"
```

**5.2 — Test fungsional:**

Buka aplikasi dan lakukan:

| Skenario | Yang Diharapkan |
|----------|-----------------|
| Login sebagai admin → buka halaman validasi pendaftaran | Halaman tampil normal |
| Admin klik **Setujui** pada salah satu pendaftar | Pendaftaran berubah status jadi "disetujui", user & anggota terbuat di database, **tidak ada email terkirim**, tidak ada error 500 |
| Admin klik **Tolak** pada pendaftar lain, isi catatan | Pendaftaran berubah status jadi "ditolak", catatan_admin tersimpan, **tidak ada email terkirim**, tidak ada error 500 |
| Fitur lain (CRUD anggota, kegiatan, dll) | Tetap berfungsi normal, tidak terpengaruh |

---

## 📊 Daftar File yang Disentuh

| No | File | Aksi | Jenis |
|----|------|------|-------|
| 1 | `app/Http/Controllers/ValidasiPendaftaranController.php` | **Edit** — hapus 5 import, 2 pemanggilan method, 2 private method | 🔧 Modify |
| 2 | `app/Mail/PendaftaranDisetujuiMail.php` | **Hapus file** | 🗑️ Delete |
| 3 | `app/Mail/PendaftaranDitolakMail.php` | **Hapus file** | 🗑️ Delete |
| 4 | `app/Mail/` (folder) | **Hapus folder** jika kosong | 🗑️ Delete |
| 5 | `resources/views/emails/pendaftaran/disetujui.blade.php` | **Hapus file** | 🗑️ Delete |
| 6 | `resources/views/emails/pendaftaran/ditolak.blade.php` | **Hapus file** | 🗑️ Delete |
| 7 | `resources/views/emails/pendaftaran/` (folder) | **Hapus folder** jika kosong | 🗑️ Delete |
| 8 | `resources/views/emails/` (folder) | **Hapus folder** jika kosong | 🗑️ Delete |

**Total: 8 entri (1 edit + 7 delete)**

---

## ⚠️ Peringatan Khusus

1. **`.env` dan `.env.example` JANGAN disentuh** — biarin semua isinya apa adanya.

2. Method `validatePendaftaran()` di Controller **TIDAK BOLEH dihapus** — hanya bagian kirim email-nya saja. Kalau method ini dihapus, fitur validasi pendaftaran akan rusak total.

3. `config/mail.php` **TIDAK USAH disentuh** — ini file bawaan Laravel.

4. Jangan hapus folder `resources/views/` — pastikan path hapus spesifik ke folder `emails` saja.

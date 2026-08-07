# 01 — Membuat tabel dengan migration

**Migration** adalah file PHP berversi yang membuat atau mengubah tabel database.

Kenapa tidak edit DB manual saja?
- teman satu tim dapat struktur yang sama
- Docker / staging / production tetap konsisten
- bisa di-rollback jika salah

## 1) Buat file migration

Dari root project (contoh Docker):

```bash
docker compose exec app php artisan make:migration create_contoh_table
```

Atau buat model + migration sekaligus:

```bash
docker compose exec app php artisan make:model Contoh -m
```

Laravel akan membuat file seperti:

```text
database/migrations/2026_07_22_210000_create_contoh_table.php
```

## 2) Tulis struktur tabel

Contoh gaya project ini (tabel `anggota`):

```php
public function up(): void
{
    Schema::create('anggota', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->string('nia')->unique()->nullable();
        $table->string('nama_lengkap');
        $table->string('tempat_lahir');
        $table->date('tanggal_lahir');
        $table->text('alamat');
        $table->string('no_telp');
        $table->string('foto_profil')->nullable();
        $table->boolean('status_aktif')->default(true);
        $table->timestamps(); // created_at + updated_at
    });
}

public function down(): void
{
    Schema::dropIfExists('anggota');
}
```

### Helper kolom yang sering dipakai

| Kode | Arti |
|------|------|
| `$table->id()` | primary key `id` |
| `$table->string('name')` | teks pendek (varchar) |
| `$table->text('alamat')` | teks panjang |
| `$table->boolean('status_aktif')` | true/false |
| `$table->date('tanggal_lahir')` | tanggal saja |
| `$table->dateTime('waktu_hadir')` | tanggal + jam |
| `$table->enum('status', ['a','b'])` | pilihan terbatas |
| `$table->timestamps()` | `created_at`, `updated_at` |
| `$table->foreignId('user_id')->constrained()` | FK ke `users.id` |
| `->nullable()` | boleh kosong |
| `->unique()` | tidak boleh duplikat |
| `->default(true)` | nilai default |

## 3) Menghubungkan tabel (foreign key)

Project ini banyak memakai foreign key.

Contoh dari `presensi`:

```php
$table->foreignId('kegiatan_id')->constrained('kegiatan')->onDelete('cascade');
$table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');
$table->unique(['kegiatan_id', 'anggota_id']);
```

Artinya:

- tiap baris presensi milik 1 kegiatan + 1 anggota
- jika kegiatan/anggota dihapus, baris presensi terkait ikut terhapus (`cascade`)
- 1 anggota tidak boleh punya 2 presensi untuk kegiatan yang sama (`unique`)

### Konvensi penamaan foreign key

| Kolom | Mengarah ke |
|-------|-------------|
| `user_id` | `users.id` |
| `anggota_id` | `anggota.id` |
| `kegiatan_id` | `kegiatan.id` |

Default Laravel: kolom `x_id` → tabel `x` (atau plural bahasa Inggris).  
Project ini memakai **nama tabel singular Indonesia**, jadi tulis nama tabel secara eksplisit:

```php
// lebih aman di project ini
$table->foreignId('anggota_id')->constrained('anggota');
```

## 4) Jalankan migration

```bash
# terapkan migration baru
docker compose exec app php artisan migrate

# cek status
docker compose exec app php artisan migrate:status

# rollback batch terakhir (hati-hati)
docker compose exec app php artisan migrate:rollback
```

## 5) Mengubah tabel yang sudah ada

**Jangan** rewrite migration lama yang sudah pernah dijalankan.

Buat migration baru:

```bash
docker compose exec app php artisan make:migration add_role_to_pendaftaran_table --table=pendaftaran
```

Contoh:

```php
public function up(): void
{
    Schema::table('pendaftaran', function (Blueprint $table) {
        $table->string('role')->nullable()->after('status');
    });
}

public function down(): void
{
    Schema::table('pendaftaran', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
```

## 6) Latihan mini

Buat tabel `catatan_kader`:

| kolom | tipe |
|-------|------|
| id | bigint PK |
| anggota_id | FK → anggota |
| judul | string |
| isi | text |
| timestamps | ya |

Sketsa:

```php
Schema::create('catatan_kader', function (Blueprint $table) {
    $table->id();
    $table->foreignId('anggota_id')->constrained('anggota')->onDelete('cascade');
    $table->string('judul');
    $table->text('isi');
    $table->timestamps();
});
```

Lanjut ke Model di dokumen berikutnya.

Berikutnya: [02 — Model dan relasi](./02-models-and-relations.md)

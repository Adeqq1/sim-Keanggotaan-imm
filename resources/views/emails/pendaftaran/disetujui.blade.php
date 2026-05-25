<x-mail::message>
# Selamat, {{ $nama }}!

Pendaftaran Anda sebagai anggota **Ikatan Mahasiswa Muhammadiyah (IMM)** telah **disetujui** oleh admin.

Silakan login menggunakan kredensial berikut:

- **Email:** {{ $email }}
- **Password sementara:** `{{ $password }}`

> Mohon segera ganti password setelah login pertama untuk keamanan akun Anda.

<x-mail::button :url="$loginUrl">
Login Sekarang
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>

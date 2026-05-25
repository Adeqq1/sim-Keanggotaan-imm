<x-mail::message>
# Halo, {{ $nama }}

Mohon maaf, pendaftaran Anda sebagai anggota **Ikatan Mahasiswa Muhammadiyah (IMM)** belum dapat disetujui saat ini.

**Catatan dari admin:**

> {{ $catatan }}

Anda dapat memperbaiki data dan mendaftar kembali melalui tombol di bawah ini.

<x-mail::button :url="$pendaftaranUrl">
Daftar Ulang
</x-mail::button>

Terima kasih atas minat Anda,<br>
{{ config('app.name') }}
</x-mail::message>

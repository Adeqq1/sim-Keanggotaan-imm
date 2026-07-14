<x-mail::message>
# Halo, {{ $user->name }}!

Selamat, pendaftaran Anda sebagai kader IMM telah disetujui oleh admin.

Berikut adalah informasi akun Anda untuk login ke dalam sistem:

**Email:** {{ $user->email }}
@if ($password)
**Password Sementara:** {{ $password }}

Silakan klik tombol di bawah ini untuk login, dan **segera ganti password Anda** setelah berhasil login untuk menjaga keamanan akun.
@else

Gunakan password yang Anda buat saat mengisi formulir pendaftaran untuk login.
@endif

<x-mail::button :url="route('login')">
Login Sekarang
</x-mail::button>

Terima kasih,<br>
{{ config('app.name') }}
</x-mail::message>

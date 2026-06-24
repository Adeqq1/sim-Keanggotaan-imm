@extends('layouts.auth')

@section('content')
    <div class="mb-4 text-center">
        <h4 class="fw-bold">Verifikasi Email</h4>
        <p class="text-muted small">
            Terima kasih sudah mendaftar! Sebelum memulai, mohon verifikasi alamat email kamu dengan mengklik
            tautan yang baru saja kami kirimkan. Jika kamu tidak menerima email, kami akan dengan senang hati
            mengirimkan yang lain.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success small py-2 mb-3">
            Tautan verifikasi baru telah dikirim ke alamat email yang kamu daftarkan.
        </div>
    @endif

    <div class="d-grid gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div class="d-grid">
                <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">Kirim Ulang Email Verifikasi</button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="d-grid">
                <button type="submit" class="btn btn-outline-secondary py-2">Keluar</button>
            </div>
        </form>
    </div>
@endsection

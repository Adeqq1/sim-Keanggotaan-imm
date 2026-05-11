@extends('layouts.auth')

@section('content')
    <div class="text-center py-4">
        <i class="bi bi-check-circle-fill display-1 text-success mb-4"></i>
        <h4 class="fw-bold">Pendaftaran Berhasil!</h4>
        <p class="text-muted px-3">Data Anda telah kami terima dan sedang dalam proses validasi oleh Admin. Mohon tunggu informasi selanjutnya melalui Email atau WhatsApp.</p>
        
        <div class="d-grid gap-2 mt-5">
            <a href="/" class="btn btn-primary">Kembali ke Beranda</a>
        </div>
    </div>
@endsection

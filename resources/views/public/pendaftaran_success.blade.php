@extends('layouts.auth')

@section('content')
    <div class="text-center py-4">
        <i class="bi bi-check-circle-fill display-1 text-success mb-4"></i>
        <h4 class="fw-bold">Pendaftaran Berhasil!</h4>
       <p class="text-muted px-3">Pendaftaran sedang divalidasi oleh Admin. Mohon tunggu kurun waktu 1x24 jam.</p>
        <div class="d-grid gap-2 mt-5">
            <a href="/" class="btn btn-primary">Kembali ke Beranda</a>
        </div>
    </div>
@endsection

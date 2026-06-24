@extends('layouts.auth')

@section('content')
    <div class="mb-4 text-center">
        <h4 class="fw-bold">Buat Akun</h4>
        <p class="text-muted small">Isi data berikut untuk mendaftar</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Nama --}}
        <div class="mb-3">
            <label class="form-label small fw-bold" for="name">Nama Lengkap</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-person text-muted"></i></span>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-control bg-light border-0 @error('name') is-invalid @enderror"
                       value="{{ old('name') }}"
                       required
                       autofocus
                       autocomplete="name">
            </div>
            @error('name')
                <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label class="form-label small fw-bold" for="email">Email</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control bg-light border-0 @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       required
                       autocomplete="username">
            </div>
            @error('email')
                <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label class="form-label small fw-bold" for="password">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-lock text-muted"></i></span>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control bg-light border-0 @error('password') is-invalid @enderror"
                       required
                       autocomplete="new-password">
            </div>
            @error('password')
                <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="mb-4">
            <label class="form-label small fw-bold" for="password_confirmation">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-lock-fill text-muted"></i></span>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-control bg-light border-0"
                       required
                       autocomplete="new-password">
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">Daftar Sekarang</button>
        </div>
    </form>

    <div class="text-center mt-4">
        <p class="small text-muted mb-0">Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Masuk</a></p>
    </div>
@endsection

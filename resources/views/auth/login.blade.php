@extends('layouts.auth')

@section('content')
    <div class="mb-4 text-center">
        <h4 class="fw-bold">Selamat Datang</h4>
        <p class="text-muted small">Silakan masuk untuk melanjutkan</p>
    </div>

    <!-- Session Status -->
    @if(session('status'))
        <div class="alert alert-success small mb-4 border-0 shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label class="form-label small fw-bold">Email</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" name="email" class="form-control bg-light border-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
            </div>
            @error('email') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label class="form-label small fw-bold">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" name="password" class="form-control bg-light border-0 @error('password') is-invalid @enderror" required>
            </div>
            @error('password') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
        </div>

        <!-- Remember Me -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label class="form-check-label small text-muted" for="remember_me">Ingat Saya</label>
            </div>
            @if (Route::has('password.request'))
                <a class="small text-decoration-none" href="{{ route('password.request') }}">Lupa Password?</a>
            @endif
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">Masuk Sekarang</button>
        </div>
    </form>
    
    <div class="text-center mt-4">
        <p class="small text-muted mb-0">Belum punya akun? <a href="{{ route('pendaftaran') }}" class="text-decoration-none fw-bold">Daftar Kader</a></p>
    </div>
@endsection

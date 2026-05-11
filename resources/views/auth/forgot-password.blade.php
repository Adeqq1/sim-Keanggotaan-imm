@extends('layouts.auth')

@section('content')
    <div class="mb-4 text-center">
        <h4 class="fw-bold">Lupa Password</h4>
        <p class="text-muted small">Masukkan email Anda untuk menerima link reset password.</p>
    </div>

    <!-- Session Status -->
    @if(session('status'))
        <div class="alert alert-success small mb-4 border-0 shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <label class="form-label small fw-bold">Email</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" name="email" class="form-control bg-light border-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
            </div>
            @error('email') <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">Kirim Link Reset</button>
            <a href="{{ route('login') }}" class="btn btn-link text-muted small text-decoration-none">Kembali ke Login</a>
        </div>
    </form>
@endsection

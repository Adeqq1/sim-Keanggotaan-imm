@extends('layouts.auth')

@section('content')
    <div class="mb-4 text-center">
        <h4 class="fw-bold">Reset Password</h4>
        <p class="text-muted small">Masukkan password baru kamu</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        {{-- Password Reset Token --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Email --}}
        <div class="mb-3">
            <label class="form-label small fw-bold" for="email">Email</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control bg-light border-0 @error('email') is-invalid @enderror"
                       value="{{ old('email', $request->email) }}"
                       required
                       autofocus
                       autocomplete="username">
            </div>
            @error('email')
                <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label class="form-label small fw-bold" for="password">Password Baru</label>
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
                       class="form-control bg-light border-0 @error('password_confirmation') is-invalid @enderror"
                       required
                       autocomplete="new-password">
            </div>
            @error('password_confirmation')
                <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">Reset Password</button>
        </div>
    </form>
@endsection

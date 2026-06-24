@extends('layouts.auth')

@section('content')
    <div class="mb-4 text-center">
        <h4 class="fw-bold">Konfirmasi Password</h4>
        <p class="text-muted small">Ini adalah area aman. Harap konfirmasi password kamu sebelum melanjutkan.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        {{-- Password --}}
        <div class="mb-4">
            <label class="form-label small fw-bold" for="password">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-0"><i class="bi bi-lock text-muted"></i></span>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control bg-light border-0 @error('password') is-invalid @enderror"
                       required
                       autocomplete="current-password">
            </div>
            @error('password')
                <div class="invalid-feedback d-block small mt-1 text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">Konfirmasi</button>
        </div>
    </form>
@endsection

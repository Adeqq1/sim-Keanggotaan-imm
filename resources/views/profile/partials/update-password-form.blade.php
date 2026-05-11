<section>
    <header>
        <h5 class="fw-bold">Keamanan Akun</h5>
        <p class="text-muted small">Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk tetap aman.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="mb-3">
            <label class="form-label small fw-bold">Kata Sandi Saat Ini</label>
            <input type="password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
            @error('current_password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Kata Sandi Baru</label>
            <input type="password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
            @error('password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary px-4 fw-bold">Perbarui Sandi</button>

            @if (session('status') === 'password-updated')
                <p class="text-success small mb-0 animated fadeIn"><i class="bi bi-check-circle me-1"></i> Tersimpan.</p>
            @endif
        </div>
    </form>
</section>

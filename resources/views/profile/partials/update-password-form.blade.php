<section>
    <header class="profile-panel-heading">
        <span class="profile-panel-icon profile-panel-icon--security"><i class="bi bi-shield-lock"></i></span>
        <div>
            <h5 class="fw-bold mb-1">Keamanan Akun</h5>
            <p class="text-muted small mb-0">Perbarui kata sandi secara berkala untuk menjaga akun tetap aman.</p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="profile-form">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="current_password" class="form-label small fw-bold">Kata Sandi Saat Ini</label>
            <input type="password" name="current_password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password">
            @error('current_password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label small fw-bold">Kata Sandi Baru</label>
            <input type="password" name="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
            @error('password', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label small fw-bold">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="profile-form-actions">
            <button type="submit" class="btn btn-primary btn-ui px-4">Perbarui Sandi</button>

            @if (session('status') === 'password-updated')
                <p class="text-success small mb-0"><i class="bi bi-check-circle me-1"></i> Kata sandi diperbarui.</p>
            @endif
        </div>
    </form>
</section>

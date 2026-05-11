<section>
    <header>
        <h5 class="fw-bold">Informasi Profil</h5>
        <p class="text-muted small">Perbarui informasi akun dan alamat email Anda.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-4">
        @csrf
        @method('patch')

        <div class="mb-4 text-center">
            @php
                $foto = $user->anggota && $user->anggota->foto_profil ? Storage::url($user->anggota->foto_profil) : null;
            @endphp
            @if($foto)
                <img src="{{ $foto }}" class="rounded-circle border border-3 border-light shadow-sm mb-3" width="100" height="100" style="object-fit: cover;">
            @else
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fs-1 fw-bold mx-auto mb-3 shadow-sm" style="width: 100px; height: 100px;">
                    {{ substr($user->name, 0, 1) }}
                </div>
            @endif
            <div class="mt-2">
                <label class="form-label small fw-bold">Foto Profil</label>
                <input type="file" name="foto_profil" class="form-control form-control-sm mx-auto @error('foto_profil') is-invalid @enderror" style="max-width: 250px;">
                @error('foto_profil') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Username / Nama Panggilan</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $user->anggota->nama_lengkap ?? '') }}" required>
            @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label small fw-bold">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $user->anggota->tempat_lahir ?? '') }}">
            </div>
            <div class="col-6 mb-3">
                <label class="form-label small fw-bold">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', $user->anggota && $user->anggota->tanggal_lahir ? $user->anggota->tanggal_lahir->format('Y-m-d') : '') }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Nomor Telepon (WA)</label>
            <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp', $user->anggota->no_telp ?? '') }}">
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold">Alamat</label>
            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $user->anggota->alamat ?? '') }}</textarea>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Perubahan</button>

            @if (session('status') === 'profile-updated')
                <p class="text-success small mb-0 animated fadeIn"><i class="bi bi-check-circle me-1"></i> Tersimpan.</p>
            @endif
        </div>
    </form>
</section>

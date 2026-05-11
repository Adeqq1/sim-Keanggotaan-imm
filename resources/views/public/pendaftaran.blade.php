@extends('layouts.auth')

@section('content')
    <div class="mb-4 text-center">
        <h4 class="fw-bold">Form Pendaftaran</h4>
        <p class="text-muted small">Silakan lengkapi data diri Anda</p>
    </div>

    <form method="POST" action="{{ route('pendaftaran.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label small fw-bold">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap') }}" required>
            @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            <div class="col-6 mb-3">
                <label class="form-label small fw-bold">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir') }}" required>
            </div>
            <div class="col-6 mb-3">
                <label class="form-label small fw-bold">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Nomor Telepon (WA)</label>
            <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp') }}" placeholder="08..." required>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Alamat</label>
            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" required>{{ old('alamat') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold">File Persyaratan (PDF/JPG)</label>
            <input type="file" name="file_persyaratan" class="form-control @error('file_persyaratan') is-invalid @enderror">
            <div class="form-text small">Opsional: KTP/Kartu Mahasiswa</div>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Kirim Pendaftaran</button>
            <a href="/" class="btn btn-link text-muted small text-decoration-none">Kembali ke Beranda</a>
        </div>
    </form>
@endsection

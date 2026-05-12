<x-app-layout>
    <x-slot name="header">
        Tambah Anggota
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.anggota.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.anggota.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-bold">Foto Profil</label>
                    <input type="file" name="foto_profil" class="form-control @error('foto_profil') is-invalid @enderror" accept="image/*">
                    @error('foto_profil')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Format: JPG, PNG, max 2MB</small>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">NIA <span class="text-danger">*</span></label>
                        <input type="text" name="nia" class="form-control @error('nia') is-invalid @enderror" value="{{ old('nia') }}" required>
                        @error('nia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap') }}" required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir') }}" required>
                        @error('tempat_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}" required>
                        @error('tanggal_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">No. Telepon <span class="text-danger">*</span></label>
                        <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp') }}" required>
                        @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <select name="status_aktif" class="form-select @error('status_aktif') is-invalid @enderror" required>
                            <option value="1" {{ old('status_aktif') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status_aktif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('admin.anggota.index') }}" class="btn btn-danger">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

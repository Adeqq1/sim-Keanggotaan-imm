<x-app-layout>
    <x-slot name="header">
        Tambah Anggota
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.anggota.index') }}" class="btn btn-outline-secondary btn-ui btn-ui-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.anggota.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-bold">Foto Profil</label>
                    
                    <div id="admin-create-preview-wrapper" class="mb-2 d-none">
                        <img id="admin-create-photo-preview"
                             src=""
                             alt="Preview Foto Profil"
                             class="rounded shadow-sm"
                             width="100" height="100"
                             style="object-fit: cover;">
                    </div>

                    <input type="file"
                           name="foto_profil"
                           id="admin_create_foto_profil_input"
                           class="form-control @error('foto_profil') is-invalid @enderror"
                           accept="image/jpeg,image/png">
                    @error('foto_profil')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">JPG, JPEG, atau PNG, maksimum 2 MB dan 2048 x 2048 piksel. Disimpan sebagai WebP.</small>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">NIA <span class="text-danger">*</span></label>
                        <input type="text" name="nia" class="form-control @error('nia') is-invalid @enderror" value="{{ old('nia') }}" required>
                        @error('nia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap') }}" required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir') }}" required>
                        @error('tempat_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}" required>
                        @error('tanggal_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">No. Telepon <span class="text-danger">*</span></label>
                        <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp') }}" required>
                        @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Dipakai untuk login akun anggota.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <select name="status_aktif" class="form-select @error('status_aktif') is-invalid @enderror" required>
                            <option value="1" {{ old('status_aktif', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status_aktif') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status_aktif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Peran <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            @foreach(App\Enums\RoleEnum::cases() as $role)
                                @if($role !== App\Enums\RoleEnum::ADMIN)
                                    <option value="{{ $role->value }}" {{ old('role', 'kader') == $role->value ? 'selected' : '' }}>
                                        {{ ucfirst($role->value) }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-ui">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="{{ route('admin.anggota.index') }}" class="btn btn-outline-secondary btn-ui">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('admin_create_foto_profil_input');
            const wrapper = document.getElementById('admin-create-preview-wrapper');
            const preview = document.getElementById('admin-create-photo-preview');

            if (input && wrapper && preview) {
                input.addEventListener('change', function (event) {
                    const file = event.target.files && event.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            preview.src = e.target.result;
                            wrapper.classList.remove('d-none');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        wrapper.classList.add('d-none');
                        preview.src = '';
                    }
                });
            }
        });
    </script>
</x-app-layout>

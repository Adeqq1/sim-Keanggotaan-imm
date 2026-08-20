<x-app-layout>
    <x-slot name="header">
        Ubah Anggota
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.anggota.index') }}" class="btn btn-outline-secondary btn-ui btn-ui-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Form generate NIA diletakkan di LUAR form update agar tidak nested --}}
    @if(empty($anggota->nia))
        <form id="form-generate-nia"
              action="{{ route('admin.anggota.generate-nia', $anggota) }}"
              method="POST"
              class="d-none">
            @csrf
        </form>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.anggota.update', $anggota->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="form-label fw-bold">Foto Profil</label>
                    
                    <div class="mb-2 position-relative d-inline-block">
                        <img id="admin-photo-preview"
                             src="{{ $anggota->foto_profil ? Storage::url($anggota->foto_profil) : '' }}"
                             alt="Foto Profil {{ $anggota->nama_lengkap }}"
                             class="rounded shadow-sm {{ $anggota->foto_profil ? '' : 'd-none' }}"
                             width="100" height="100"
                             style="object-fit: cover;"
                             onerror="this.classList.add('d-none'); document.getElementById('admin-photo-fallback')?.classList.remove('d-none'); document.getElementById('admin-photo-fallback')?.classList.add('d-flex');">
                        <div id="admin-photo-fallback"
                             class="rounded bg-light align-items-center justify-content-center text-primary fw-bold shadow-sm {{ $anggota->foto_profil ? 'd-none' : 'd-flex' }}"
                             style="width: 100px; height: 100px; font-size: 2rem;">
                            {{ substr($anggota->nama_lengkap, 0, 1) }}
                        </div>
                    </div>
                    
                    <input type="file"
                           name="foto_profil"
                           id="admin_foto_profil_input"
                           class="form-control @error('foto_profil') is-invalid @enderror"
                           accept="image/jpeg,image/png">
                    @error('foto_profil')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">JPG, JPEG, atau PNG, maksimum 2 MB dan 2048 x 2048 piksel. Disimpan sebagai WebP. Kosongkan jika tidak ingin mengubah.</small>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">NIA</label>
                        <div class="input-group responsive-input-group">
                            <input type="text" name="nia" id="nia" class="form-control @error('nia') is-invalid @enderror" value="{{ old('nia', $anggota->nia) }}" placeholder="8 digit angka" maxlength="8">
                            @if(empty($anggota->nia))
                                {{-- form="form-generate-nia" mengaitkan button ini ke form di luar, menghindari nested form --}}
                                <button type="submit"
                                        form="form-generate-nia"
                                        class="btn btn-outline-primary btn-ui"
                                        title="Buat NIA otomatis">
                                    <i class="bi bi-magic"></i> Buat NIA
                                </button>
                            @else
                                <span class="input-group-text bg-light text-muted" title="NIA sudah terisi">
                                    <i class="bi bi-check-circle-fill text-success me-1"></i> Terisi
                                </span>
                            @endif
                        </div>
                        @error('nia')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: 8 digit angka (contoh: 24260001). Kosongkan bila belum ada.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $anggota->nama_lengkap) }}" required>
                        @error('nama_lengkap')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $anggota->tempat_lahir) }}" required>
                        @error('tempat_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', $anggota->tanggal_lahir?->format('Y-m-d')) }}" required>
                        @error('tanggal_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $anggota->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">No. Telepon <span class="text-danger">*</span></label>
                        <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp', $anggota->no_telp) }}" required>
                        @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <select name="status_aktif" class="form-select @error('status_aktif') is-invalid @enderror" required>
                            <option value="1" {{ old('status_aktif', $anggota->status_aktif) == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status_aktif', $anggota->status_aktif) == '0' ? 'selected' : '' }}>Tidak Aktif</option>
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
                                    <option value="{{ $role->value }}" {{ old('role', $anggota->user?->role) == $role->value ? 'selected' : '' }}>
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

                <div class="d-flex flex-column flex-sm-row gap-2">
                    <button type="submit" class="btn btn-primary btn-ui">
                        <i class="bi bi-save"></i> Perbarui
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
            const input = document.getElementById('admin_foto_profil_input');
            const preview = document.getElementById('admin-photo-preview');
            const fallback = document.getElementById('admin-photo-fallback');

            if (input && preview && fallback) {
                input.addEventListener('change', function (event) {
                    const file = event.target.files && event.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            preview.src = e.target.result;
                            preview.classList.remove('d-none');
                            fallback.classList.add('d-none');
                            fallback.classList.remove('d-flex');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>
</x-app-layout>

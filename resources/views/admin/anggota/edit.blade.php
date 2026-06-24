<x-app-layout>
    <x-slot name="header">
        Edit Anggota
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.anggota.index') }}" class="btn btn-outline-secondary btn-sm">
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
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <form action="{{ route('admin.anggota.update', $anggota->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="form-label fw-bold">Foto Profil</label>
                    
                    @if($anggota->foto_profil)
                        <div class="mb-2">
                            <img src="{{ Storage::url($anggota->foto_profil) }}" class="rounded shadow-sm" width="100" height="100" style="object-fit: cover;">
                        </div>
                    @endif
                    
                    <input type="file" name="foto_profil" class="form-control @error('foto_profil') is-invalid @enderror" accept="image/*">
                    @error('foto_profil')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Format: JPG, PNG, max 2MB. Kosongkan jika tidak ingin mengubah.</small>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">NIA</label>
                        <div class="input-group">
                            <input type="text" name="nia" id="nia" class="form-control @error('nia') is-invalid @enderror" value="{{ old('nia', $anggota->nia) }}" placeholder="8 digit angka" maxlength="8">
                            @if(empty($anggota->nia))
                                {{-- form="form-generate-nia" mengaitkan button ini ke form di luar, menghindari nested form --}}
                                <button type="submit"
                                        form="form-generate-nia"
                                        class="btn btn-outline-primary"
                                        title="Generate NIA otomatis">
                                    <i class="bi bi-magic"></i> Generate NIA
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
                        @enderror>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tempat Lahir <span class="text-danger">*</span></label>
                        <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $anggota->tempat_lahir) }}" required>
                        @error('tempat_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', $anggota->tanggal_lahir?->format('Y-m-d')) }}" required>
                        @error('tanggal_lahir')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Alamat <span class="text-danger">*</span></label>
                        <textarea name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $anggota->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">No. Telepon <span class="text-danger">*</span></label>
                        <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp', $anggota->no_telp) }}" required>
                        @error('no_telp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <select name="status_aktif" class="form-select @error('status_aktif') is-invalid @enderror" required>
                            <option value="1" {{ old('status_aktif', $anggota->status_aktif) == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status_aktif', $anggota->status_aktif) == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('status_aktif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror>
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

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update
                    </button>
                    <a href="{{ route('admin.anggota.index') }}" class="btn btn-danger">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

@extends('layouts.auth')

@section('auth-card-class', 'auth-card--pendaftaran')

@section('content')
    <div class="mb-4 text-center">
        <h4 class="fw-bold">Form Pendaftaran</h4>
        <p class="text-muted small">Silakan lengkapi data diri Anda</p>
    </div>

    @if (session('error'))
        <div class="alert alert-danger small" role="alert">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('pendaftaran.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label small fw-bold">Nama Lengkap <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap') }}" required>
                @error('nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label small fw-bold">Email <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label small fw-bold">Password Akun <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                <div class="form-text small">Password ini akan digunakan untuk login setelah pendaftaran disetujui.</div>
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label small fw-bold">Konfirmasi Password <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
            </div>

            <div class="col-12 col-md-6" x-data="{ role: @js(old('role')), komisariatId: @js(old('komisariat_id')) }">
                <label for="role" class="form-label small fw-bold">Daftar Sebagai <span class="text-danger" aria-hidden="true">*</span></label>
                <select id="role" name="role" x-model="role" class="form-select @error('role') is-invalid @enderror" @change="if (role !== 'kader') komisariatId = ''" required>
                    <option value="">Pilih jenis pendaftaran</option>
                    <option value="kader" @selected(old('role') === 'kader')>Kader</option>
                    <option value="instruktur" @selected(old('role') === 'instruktur')>Instruktur</option>
                </select>
                <div class="form-text small">Pilihan ini akan menentukan role akun Anda jika pendaftaran disetujui admin.</div>
                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror

                <div x-cloak x-show="role === 'kader'" class="pendaftaran-komisariat mt-3">
                    <label for="komisariat_id" class="form-label small fw-bold">Komisariat <span class="text-danger" aria-hidden="true">*</span></label>
                    <select id="komisariat_id" name="komisariat_id" x-model="komisariatId" class="form-select @error('komisariat_id') is-invalid @enderror" :disabled="role !== 'kader'" :required="role === 'kader'">
                        <option value="">Pilih komisariat</option>
                        @foreach (\App\Models\Pendaftaran::KOMISARIAT as $value => $label)
                            <option value="{{ $value }}" @selected(old('komisariat_id') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="form-text small">Hanya wajib diisi jika Anda mendaftar sebagai Kader.</div>
                    @error('komisariat_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <noscript>
                    <style>.pendaftaran-komisariat[x-cloak] { display: block !important; }</style>
                </noscript>
            </div>

            <div class="col-12 col-md-6">
                <label for="tahun_daftar" class="form-label small fw-bold">Tahun Daftar <span class="text-danger" aria-hidden="true">*</span></label>
                <input id="tahun_daftar" type="number" name="tahun_daftar" inputmode="numeric" min="2016" max="{{ now()->year }}" step="1" class="form-control @error('tahun_daftar') is-invalid @enderror" value="{{ old('tahun_daftar') }}" aria-describedby="tahun_daftar_help" required>
                <div id="tahun_daftar_help" class="form-text small">Masukkan tahun 2016 sampai {{ now()->year }}.</div>
                @error('tahun_daftar') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label small fw-bold">Tempat Lahir <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir') }}" required>
                @error('tempat_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label small fw-bold">Tanggal Lahir <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir') }}" required>
                @error('tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label small fw-bold">Nomor Telepon (WA) <span class="text-danger" aria-hidden="true">*</span></label>
                <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp') }}" placeholder="08..." required>
                @error('no_telp') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label class="form-label small fw-bold">Alamat <span class="text-danger" aria-hidden="true">*</span></label>
                <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" required>{{ old('alamat') }}</textarea>
                @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 col-md-6">
                <label for="jenis_dokumen_identitas" class="form-label small fw-bold">Jenis Dokumen Identitas <span class="text-danger" aria-hidden="true">*</span></label>
                <select id="jenis_dokumen_identitas" name="jenis_dokumen_identitas" class="form-select @error('jenis_dokumen_identitas') is-invalid @enderror" required>
                    <option value="">Pilih jenis dokumen</option>
                    @foreach (\App\Models\Pendaftaran::JENIS_DOKUMEN_IDENTITAS as $value => $label)
                        <option value="{{ $value }}" @selected(old('jenis_dokumen_identitas') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('jenis_dokumen_identitas')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-6">
                <label for="file_persyaratan" class="form-label small fw-bold">Dokumen Identitas <span class="text-danger" aria-hidden="true">*</span></label>
                <input id="file_persyaratan" type="file" name="file_persyaratan" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" aria-describedby="file_persyaratan_help" class="form-control @error('file_persyaratan') is-invalid @enderror" required>
                <div id="file_persyaratan_help" class="form-text small">Unggah satu file KTP atau KTM dalam format PDF, JPG, JPEG, atau PNG. Maksimum 2 MB.</div>
                @error('file_persyaratan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Kirim Pendaftaran</button>
            <a href="/" class="btn btn-link text-muted small text-decoration-none">Kembali ke Beranda</a>
        </div>
    </form>
@endsection

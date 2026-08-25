<section>
    <header class="profile-panel-heading">
        <span class="profile-panel-icon"><i class="bi bi-person-vcard"></i></span>
        <div>
            <h5 class="fw-bold mb-1">Informasi Profil</h5>
            <p class="text-muted small mb-0">Perbarui identitas akun dan data yang tersimpan di sistem.</p>
        </div>
    </header>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="profile-form">
        @csrf
        @method('patch')

        @if($user->anggota)
            <input id="foto_profil" type="file" name="foto_profil" class="visually-hidden" accept="image/jpeg,image/png" disabled data-profile-photo-input>
        @endif

        <div class="profile-form-section">
            <span class="profile-form-section-label">Akun</span>
            <div class="mb-3">
                <div class="profile-field" data-profile-field>
                    <div class="profile-field-heading">
                        <label for="profile_name" class="form-label small fw-bold mb-0">Username / Nama Panggilan</label>
                        <button type="button" class="profile-field-edit" data-profile-edit aria-controls="profile_name" aria-label="Edit Username / Nama Panggilan">Edit</button>
                    </div>
                    <input id="profile_name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required @unless($errors->has('name'))readonly @endunless autocomplete="name" @error('name') aria-invalid="true" aria-describedby="profile_name_error" @enderror>
                    @error('name')<div id="profile_name_error" class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <div class="profile-field" data-profile-field>
                    <div class="profile-field-heading">
                        <label for="profile_email" class="form-label small fw-bold mb-0">Email</label>
                        <button type="button" class="profile-field-edit" data-profile-edit aria-controls="profile_email" aria-label="Edit Email">Edit</button>
                    </div>
                    <input id="profile_email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required @unless($errors->has('email'))readonly @endunless autocomplete="username" @error('email') aria-invalid="true" aria-describedby="profile_email_error" @enderror>
                    @error('email')<div id="profile_email_error" class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        @if($user->anggota)
            <div class="profile-form-section">
                <span class="profile-form-section-label">Data pribadi</span>
                <div class="mb-3">
                    <div class="profile-field" data-profile-field>
                        <div class="profile-field-heading">
                            <label for="profile_full_name" class="form-label small fw-bold mb-0">Nama Lengkap</label>
                            <button type="button" class="profile-field-edit" data-profile-edit aria-controls="profile_full_name" aria-label="Edit Nama Lengkap">Edit</button>
                        </div>
                        <input id="profile_full_name" type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $user->anggota->nama_lengkap ?? '') }}" required @unless($errors->has('nama_lengkap'))readonly @endunless @error('nama_lengkap') aria-invalid="true" aria-describedby="profile_full_name_error" @enderror>
                        @error('nama_lengkap')<div id="profile_full_name_error" class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-sm-6 mb-3">
                        <div class="profile-field" data-profile-field>
                            <div class="profile-field-heading">
                                <label for="profile_birth_place" class="form-label small fw-bold mb-0">Tempat Lahir</label>
                                <button type="button" class="profile-field-edit" data-profile-edit aria-controls="profile_birth_place" aria-label="Edit Tempat Lahir">Edit</button>
                            </div>
                            <input id="profile_birth_place" type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $user->anggota->tempat_lahir ?? '') }}" required @unless($errors->has('tempat_lahir'))readonly @endunless @error('tempat_lahir') aria-invalid="true" aria-describedby="profile_birth_place_error" @enderror>
                            @error('tempat_lahir')<div id="profile_birth_place_error" class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 mb-3">
                        <div class="profile-field" data-profile-field>
                            <div class="profile-field-heading">
                                <label for="profile_birth_date" class="form-label small fw-bold mb-0">Tanggal Lahir</label>
                                <button type="button" class="profile-field-edit" data-profile-edit aria-controls="profile_birth_date" aria-label="Edit Tanggal Lahir">Edit</button>
                            </div>
                            <input id="profile_birth_date" type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', $user->anggota && $user->anggota->tanggal_lahir ? $user->anggota->tanggal_lahir->format('Y-m-d') : '') }}" required @unless($errors->has('tanggal_lahir'))readonly @endunless @error('tanggal_lahir') aria-invalid="true" aria-describedby="profile_birth_date_error" @enderror>
                            @error('tanggal_lahir')<div id="profile_birth_date_error" class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="profile-field" data-profile-field>
                        <div class="profile-field-heading">
                            <label for="profile_phone" class="form-label small fw-bold mb-0">Nomor Telepon (WA)</label>
                            <button type="button" class="profile-field-edit" data-profile-edit aria-controls="profile_phone" aria-label="Edit Nomor Telepon">Edit</button>
                        </div>
                        <input id="profile_phone" type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror" value="{{ old('no_telp', $user->anggota->no_telp ?? '') }}" required @unless($errors->has('no_telp'))readonly @endunless @error('no_telp') aria-invalid="true" aria-describedby="profile_phone_error" @enderror>
                        @error('no_telp')<div id="profile_phone_error" class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <div class="profile-field" data-profile-field>
                        <div class="profile-field-heading">
                            <label for="profile_address" class="form-label small fw-bold mb-0">Alamat</label>
                            <button type="button" class="profile-field-edit" data-profile-edit aria-controls="profile_address" aria-label="Edit Alamat">Edit</button>
                        </div>
                        <textarea id="profile_address" name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" required @unless($errors->has('alamat'))readonly @endunless @error('alamat') aria-invalid="true" aria-describedby="profile_address_error" @enderror>{{ old('alamat', $user->anggota->alamat ?? '') }}</textarea>
                        @error('alamat')<div id="profile_address_error" class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        @endif

        <div class="profile-form-actions">
            <button type="submit" class="btn btn-primary btn-ui px-4">Simpan Perubahan</button>
            @if (session('status') === 'profile-updated')
                <p class="text-success small mb-0"><i class="bi bi-check-circle me-1"></i> Perubahan tersimpan.</p>
            @endif
        </div>
    </form>
</section>

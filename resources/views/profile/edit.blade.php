<x-app-layout>
    <x-slot name="header">
        Pengaturan Profil
    </x-slot>

    <div class="profile-page-layout">
        <aside class="profile-summary-panel">
            <div class="profile-summary-avatar-wrap">
                <div class="profile-summary-avatar">
                @if($user->profile_photo_url)
                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" width="112" height="112" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                    <span class="profile-summary-avatar__fallback" style="display: none;">{{ $user->initials }}</span>
                @else
                    <span class="profile-summary-avatar__fallback">{{ $user->initials }}</span>
                @endif
                </div>
                @if($anggota)
                    <button type="button" class="profile-summary-avatar-edit" data-profile-photo-edit aria-controls="foto_profil" aria-label="Edit Foto Profil">
                        <i class="bi bi-pencil"></i><span>Edit Foto</span>
                    </button>
                    @error('foto_profil')
                        <div class="invalid-feedback d-block profile-summary-photo-error">{{ $message }}</div>
                    @enderror
                @endif
            </div>
            <h2 class="profile-summary-name">{{ $user->name }}</h2>
            <p class="profile-summary-email">{{ $user->email }}</p>
            <span class="badge {{ $user->role_color }} profile-summary-role">{{ \App\Enums\RoleEnum::labelFor($user->role) }}</span>

            @if($anggota)
                <div class="profile-summary-meta">
                    <div>
                        <span>NIA</span>
                        <strong>{{ $anggota->nia ?? 'Belum tersedia' }}</strong>
                    </div>
                    <div>
                        <span>Status anggota</span>
                        <strong>{{ $anggota->status_aktif ? 'Aktif' : 'Tidak aktif' }}</strong>
                    </div>
                </div>
            @else
                <div class="profile-summary-note">
                    <i class="bi bi-info-circle"></i>
                    <span>Profil ini hanya mengelola informasi akun dan keamanan.</span>
                </div>
            @endif
        </aside>

        <div class="profile-content-column">
            <div class="profile-panel profile-information-panel">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="profile-panel profile-security-panel">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- <div class="p-4 bg-white shadow-sm border-0 mt-4 mb-5" style="border-radius: 15px;">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div> -->
    </div>
</x-app-layout>

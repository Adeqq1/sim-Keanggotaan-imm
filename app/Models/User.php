<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\RoleEnum;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the anggota associated with the user.
     */
    public function anggota(): HasOne
    {
        return $this->hasOne(Anggota::class);
    }

    /**
     * Get the pendaftaran associated with the user.
     */
    public function pendaftaran(): HasOne
    {
        return $this->hasOne(Pendaftaran::class);
    }

    /**
     * Determine if the user is an instruktur.
     */
    public function isInstruktur(): bool
    {
        return $this->role === 'instruktur';
    }

    /**
     * Get the dashboard route name based on user role.
     */
    public function getDashboardRoute(): string
    {
        if ($this->role === 'admin') {
            return 'admin.dashboard';
        }
        if ($this->role === 'instruktur') {
            return 'admin.kegiatan.index';
        }

        return 'kader.dashboard';
    }

    /**
     * Get the role color badge class.
     */
    public function getRoleColorAttribute(): string
    {
        return RoleEnum::badgeClassFor($this->role);
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        $path = $this->anggota?->foto_profil;

        return filled($path) && Storage::disk('public')->exists($path)
            ? asset('storage/'.$path)
            : null;
    }

    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->name ?: 'User')) ?: ['User'];

        return Str::upper(collect($words)->take(2)->map(fn (string $word) => mb_substr($word, 0, 1))->implode(''));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

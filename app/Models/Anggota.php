<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'nia', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'no_telp', 'foto_profil', 'status_aktif', 'komisariat_id', 'tahun_daftar'])]
class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    protected $casts = [
        'tanggal_lahir' => 'date',
        'status_aktif' => 'boolean',
        'tahun_daftar' => 'integer',
    ];

    /**
     * Get the route key name for Laravel.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function arsip(): HasMany
    {
        return $this->hasMany(Arsip::class);
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    public function jumlahKegiatanHadir(): int
    {
        return $this->presensi()
            ->where('status_kehadiran', 'hadir')
            ->distinct()
            ->count('kegiatan_id');
    }

    public function sertifikat(): HasMany
    {
        return $this->hasMany(Sertifikat::class);
    }

    public function materiTersimpan(): BelongsToMany
    {
        return $this->belongsToMany(MateriKegiatan::class, 'materi_tersimpan')->withTimestamps();
    }

    public function getFotoProfilUrlAttribute(): ?string
    {
        return filled($this->foto_profil) && Storage::disk('public')->exists($this->foto_profil)
            ? asset('storage/'.$this->foto_profil)
            : null;
    }

    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->nama_lengkap ?: 'Anggota')) ?: ['Anggota'];

        return Str::upper(collect($words)->take(2)->map(fn (string $word) => mb_substr($word, 0, 1))->implode(''));
    }
}

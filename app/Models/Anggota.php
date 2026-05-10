<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'nia', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'no_telp', 'foto_profil', 'status_aktif'])]
class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    protected $casts = [
        'tanggal_lahir' => 'date',
        'status_aktif' => 'boolean',
    ];

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

    public function sertifikat(): HasMany
    {
        return $this->hasMany(Sertifikat::class);
    }
}

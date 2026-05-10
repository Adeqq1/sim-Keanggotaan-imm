<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama_kegiatan', 'deskripsi', 'tanggal_waktu', 'lokasi'])]
class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';

    protected $casts = [
        'tanggal_waktu' => 'datetime',
    ];

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    public function sertifikat(): HasMany
    {
        return $this->hasMany(Sertifikat::class);
    }
}

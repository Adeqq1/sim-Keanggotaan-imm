<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

#[Fillable(['nama_kegiatan', 'deskripsi', 'tanggal_waktu', 'lokasi', 'thumbnail', 'jenis_pelaksanaan', 'minimum_sesi_terverifikasi'])]
class Kegiatan extends Model
{
    use HasFactory;

    public const BELUM_DITETAPKAN = 'belum_ditetapkan';
    public const SATU_SESI = 'satu_sesi';
    public const MULTI_SESI = 'multi_sesi';

    protected $table = 'kegiatan';

    protected $casts = [
        'tanggal_waktu' => 'datetime',
        'minimum_sesi_terverifikasi' => 'integer',
    ];

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    public function sesiKegiatans(): HasMany
    {
        return $this->hasMany(SesiKegiatan::class)->orderBy('urutan');
    }

    public function sertifikat(): HasMany
    {
        return $this->hasMany(Sertifikat::class);
    }

    public function materiKegiatans(): HasMany
    {
        return $this->hasMany(MateriKegiatan::class);
    }

    public function laporanKegiatan(): HasOne
    {
        return $this->hasOne(LaporanKegiatan::class);
    }

    /**
     * Get the URL of the activity's thumbnail, falling back to a placeholder image.
     */
    public function getThumbnailUrlAttribute(): string
    {
        return filled($this->thumbnail) && Storage::disk('public')->exists($this->thumbnail)
            ? asset('storage/'.$this->thumbnail)
            : asset('images/placeholder-kegiatan.png');
    }
}

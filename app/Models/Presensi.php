<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

#[Fillable(['kegiatan_id', 'anggota_id', 'sesi_kegiatan_id', 'waktu_hadir', 'status_kehadiran', 'bukti_kehadiran', 'status_klaim', 'status_verifikasi', 'pemeriksa_id', 'diperiksa_pada'])]
class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $casts = [
        'waktu_hadir' => 'datetime',
        'diperiksa_pada' => 'datetime',
    ];

    protected $attributes = ['status_verifikasi' => 'pending'];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    public function sesiKegiatan(): BelongsTo
    {
        return $this->belongsTo(SesiKegiatan::class);
    }

    public function pemeriksa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemeriksa_id');
    }

    public function scopeTerverifikasi(Builder $query): Builder
    {
        return $query->where('status_kehadiran', 'hadir')
            ->where(function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->where('status_verifikasi', 'terverifikasi')->whereNotNull('diperiksa_pada');
                })->orWhere('status_verifikasi', 'legacy');
            });
    }
}

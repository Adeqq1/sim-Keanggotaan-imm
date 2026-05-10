<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kegiatan_id', 'anggota_id', 'waktu_hadir', 'status_kehadiran'])]
class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $casts = [
        'waktu_hadir' => 'datetime',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }
}

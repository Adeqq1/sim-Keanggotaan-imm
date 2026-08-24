<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kegiatan_id', 'urutan', 'nama_sesi', 'mulai_pada'])]
class SesiKegiatan extends Model
{
    use HasFactory;

    protected $table = 'sesi_kegiatan';

    protected $casts = ['mulai_pada' => 'datetime', 'urutan' => 'integer'];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }
}

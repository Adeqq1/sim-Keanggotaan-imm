<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kegiatan_id', 'anggota_id', 'nilai'])]
class PenilaianKegiatan extends Model
{
    use HasFactory;

    public const NILAI_LABELS = [
        'A' => 'Sangat Bagus',
        'B' => 'Bagus',
        'C' => 'Cukup',
        'D' => 'Kurang',
    ];

    protected $table = 'penilaian_kegiatan';

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }
}

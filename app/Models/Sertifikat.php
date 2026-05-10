<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kegiatan_id', 'anggota_id', 'nomor_sertifikat', 'file_sertifikat'])]
class Sertifikat extends Model
{
    use HasFactory;

    protected $table = 'sertifikat';

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }
}

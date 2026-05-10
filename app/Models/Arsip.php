<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['anggota_id', 'nomor_dokumen', 'judul_dokumen', 'kategori_arsip', 'file_arsip', 'tanggal_unggah'])]
class Arsip extends Model
{
    use HasFactory;

    protected $table = 'arsip';

    protected $casts = [
        'tanggal_unggah' => 'date',
    ];

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }
}

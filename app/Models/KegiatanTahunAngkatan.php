<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kegiatan_id', 'tahun_daftar'])]
class KegiatanTahunAngkatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan_tahun_angkatan';

    protected $casts = ['tahun_daftar' => 'integer'];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }
}

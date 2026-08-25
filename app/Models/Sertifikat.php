<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['kegiatan_id', 'anggota_id', 'nomor_sertifikat', 'file_sertifikat', 'tipe_sertifikat', 'nilai_snapshot'])]
class Sertifikat extends Model
{
    use HasFactory;

    public const SATU_SESI = 'satu_sesi';

    public const MULTI_SESI = 'multi_sesi';

    public const MINIMUM_KEGIATAN_HADIR = 3;

    protected $table = 'sertifikat';

    protected function casts(): array
    {
        return [
            'nilai_snapshot' => 'string',
        ];
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }
}

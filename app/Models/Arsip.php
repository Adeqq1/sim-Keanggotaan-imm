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

    public const KATEGORI = [
        'surat_masuk' => 'Surat Masuk',
        'surat_keluar' => 'Surat Keluar',
        'proposal' => 'Proposal',
        'lpj' => 'Laporan Pertanggung Jawaban (LPJ)',
        'surat_keputusan' => 'Surat Keputusan',
        'lainnya' => 'Lain-lain',
    ];

    public const KATEGORI_UNGGAH_KADER = [
        'proposal',
        'lpj',
        'surat_keputusan',
    ];

    public function getKategoriLabelAttribute(): string
    {
        return self::KATEGORI[$this->kategori_arsip] ?? ucfirst(str_replace('_', ' ', $this->kategori_arsip));
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }
}

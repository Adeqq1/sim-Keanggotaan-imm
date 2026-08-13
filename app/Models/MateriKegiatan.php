<?php

namespace App\Models;

use Database\Factories\MateriKegiatanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['kegiatan_id', 'judul', 'deskripsi', 'file_materi'])]
class MateriKegiatan extends Model
{
    /** @use HasFactory<MateriKegiatanFactory> */
    use HasFactory;

    protected $table = 'materi_kegiatan';

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function disimpanOleh(): BelongsToMany
    {
        return $this->belongsToMany(Anggota::class, 'materi_tersimpan')->withTimestamps();
    }
}

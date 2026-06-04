<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

#[Fillable(['kegiatan_id', 'anggota_id', 'waktu_hadir', 'status_kehadiran', 'bukti_kehadiran', 'status_klaim'])]
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

    public function approveClaim(): void
    {
        DB::transaction(function () {
            $this->update([
                'status_klaim' => 'disetujui',
                'status_kehadiran' => 'hadir',
                'waktu_hadir' => $this->waktu_hadir ?? now(),
            ]);
        });
    }

    public function rejectClaim(): void
    {
        DB::transaction(function () {
            if ($this->bukti_kehadiran) {
                Storage::disk('public')->delete($this->bukti_kehadiran);
            }

            $this->update([
                'status_klaim' => 'ditolak',
                'bukti_kehadiran' => null,
            ]);
        });
    }
}

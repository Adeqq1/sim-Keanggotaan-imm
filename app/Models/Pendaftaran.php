<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'nama_lengkap', 'email', 'tempat_lahir', 'tanggal_lahir', 'no_telp', 'alamat', 'tanggal_daftar', 'file_persyaratan', 'status_validasi', 'catatan_admin'])]
class Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran';

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_daftar' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

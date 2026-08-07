<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidasiPendaftaranRequest;
use App\Models\Anggota;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ValidasiPendaftaranController extends Controller
{
    public function index()
    {
        $pendaftarans = Pendaftaran::where('status_validasi', 'pending')->latest()->paginate(6);

        return view('admin.pendaftaran.index', compact('pendaftarans'));
    }

    public function show($id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);

        return view('admin.pendaftaran.show', compact('pendaftaran'));
    }

    public function prosesValidasiPendaftaran(ValidasiPendaftaranRequest $request, $id)
    {
        $pendaftar = Pendaftaran::findOrFail($id);
        $validated = $request->validated();
        $status = $validated['status'];

        if ($status === 'disetujui') {
            $role = $validated['role'];
            $temporaryPassword = $pendaftar->password === null ? Str::password(8) : null;

            try {
                DB::transaction(function () use ($pendaftar, $temporaryPassword, $role) {
                    $pendaftar = Pendaftaran::query()->lockForUpdate()->findOrFail($pendaftar->id);

                    if ($pendaftar->status_validasi !== 'pending') {
                        throw ValidationException::withMessages([
                            'status' => 'Pendaftaran ini sudah diproses.',
                        ]);
                    }

                    if (User::where('email', $pendaftar->email)->exists()) {
                        throw ValidationException::withMessages([
                            'email' => 'Email pendaftar sudah terdaftar sebagai pengguna.',
                        ]);
                    }

                    $user = User::create([
                        'name' => $pendaftar->nama_lengkap,
                        'email' => $pendaftar->email,
                        'password' => $pendaftar->password ?? Hash::make($temporaryPassword),
                        'role' => $role,
                    ]);

                    Anggota::create([
                        'user_id' => $user->id,
                        'nama_lengkap' => $pendaftar->nama_lengkap,
                        'tempat_lahir' => $pendaftar->tempat_lahir,
                        'tanggal_lahir' => $pendaftar->tanggal_lahir,
                        'no_telp' => $pendaftar->no_telp,
                        'alamat' => $pendaftar->alamat,
                        'status_aktif' => true,
                    ]);

                    $pendaftar->update([
                        'user_id' => $user->id,
                        'role' => $role,
                        'status_validasi' => 'disetujui',
                        'catatan_admin' => 'Pendaftaran disetujui.',
                    ]);
                });
            } catch (UniqueConstraintViolationException) {
                throw ValidationException::withMessages([
                    'email' => 'Email pendaftar sudah terdaftar sebagai pengguna.',
                ]);
            }

            return redirect()->route('admin.pendaftaran.index')->with('success', 'Pendaftaran disetujui.');
        }

        $pendaftar->update([
            'password' => null,
            'status_validasi' => 'ditolak',
            'catatan_admin' => $validated['catatan_admin'],
        ]);

        return redirect()->route('admin.pendaftaran.index')->with('success', 'Pendaftaran ditolak.');
    }
}

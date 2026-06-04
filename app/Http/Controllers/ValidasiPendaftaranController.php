<?php

namespace App\Http\Controllers;

use App\Mail\PendaftaranDisetujuiMail;
use App\Models\Anggota;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ValidasiPendaftaranController extends Controller
{
    public function index()
    {
        $pendaftarans = Pendaftaran::where('status_validasi', 'pending')->latest()->paginate(10);

        return view('admin.pendaftaran.index', compact('pendaftarans'));
    }

    public function show($id)
    {
        $pendaftaran = Pendaftaran::findOrFail($id);

        return view('admin.pendaftaran.show', compact('pendaftaran'));
    }

    public function validatePendaftaran(Request $request, $id)
    {
        $pendaftar = Pendaftaran::findOrFail($id);
        $status = $request->status;

        if ($status === 'disetujui') {
            $password = Str::password(8);
            $user = null;

            DB::transaction(function () use ($pendaftar, $password, &$user) {
                $user = User::create([
                    'name' => $pendaftar->nama_lengkap,
                    'email' => $pendaftar->email,
                    'password' => Hash::make($password),
                    'role' => 'kader',
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
                    'status_validasi' => 'disetujui',
                    'catatan_admin' => 'Pendaftaran disetujui.',
                ]);
            });

            Mail::to($user->email)->send(new PendaftaranDisetujuiMail($user, $password));

            return redirect()->route('admin.pendaftaran.index')->with('success', 'Pendaftaran disetujui.');
        }

        $request->validate([
            'catatan_admin' => 'required|string',
        ]);

        $pendaftar->update([
            'status_validasi' => 'ditolak',
            'catatan_admin' => $request->catatan_admin,
        ]);

        return redirect()->route('admin.pendaftaran.index')->with('success', 'Pendaftaran ditolak.');
    }
}

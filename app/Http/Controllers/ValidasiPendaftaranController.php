<?php

namespace App\Http\Controllers;

use App\Mail\PendaftaranDisetujuiMail;
use App\Mail\PendaftaranDitolakMail;
use App\Models\Anggota;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        $passwordSementara = 'password';

        if ($status === 'disetujui') {
            DB::transaction(function () use ($pendaftar, $passwordSementara) {
                $user = User::create([
                    'name' => $pendaftar->nama_lengkap,
                    'email' => $pendaftar->email,
                    'password' => Hash::make($passwordSementara),
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

            $this->kirimEmailDisetujui($pendaftar->fresh(), $passwordSementara);

            return redirect()->route('admin.pendaftaran.index')->with('success', 'Pendaftaran disetujui.');
        }

        $request->validate([
            'catatan_admin' => 'required|string',
        ]);

        $pendaftar->update([
            'status_validasi' => 'ditolak',
            'catatan_admin' => $request->catatan_admin,
        ]);

        $this->kirimEmailDitolak($pendaftar->fresh());

        return redirect()->route('admin.pendaftaran.index')->with('success', 'Pendaftaran ditolak.');
    }

    private function kirimEmailDisetujui(Pendaftaran $pendaftaran, string $passwordSementara): void
    {
        try {
            Mail::to($pendaftaran->email)
                ->queue(new PendaftaranDisetujuiMail($pendaftaran, $passwordSementara));
        } catch (\Throwable $e) {
            Log::error('Gagal kirim email persetujuan pendaftaran', [
                'pendaftaran_id' => $pendaftaran->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function kirimEmailDitolak(Pendaftaran $pendaftaran): void
    {
        try {
            Mail::to($pendaftaran->email)
                ->queue(new PendaftaranDitolakMail($pendaftaran));
        } catch (\Throwable $e) {
            Log::error('Gagal kirim email penolakan pendaftaran', [
                'pendaftaran_id' => $pendaftaran->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

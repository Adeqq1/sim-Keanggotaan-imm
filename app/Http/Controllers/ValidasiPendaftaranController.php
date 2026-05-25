<?php

namespace App\Http\Controllers;

use App\Mail\PendaftaranDisetujuiMail;
use App\Mail\PendaftaranDitolakMail;
use App\Models\Anggota;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ValidasiPendaftaranController extends Controller
{
    /**
     * Password default sementara untuk kader yang baru disetujui.
     * Kader wajib mengganti password ini setelah login pertama.
     *
     * @todo Ganti dengan Str::password() dan kirim via email — lihat issue terpisah.
     */
    private const DEFAULT_KADER_PASSWORD = 'password';

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
            DB::transaction(function () use ($pendaftar) {
                $user = User::create([
                    'name' => $pendaftar->nama_lengkap,
                    'email' => $pendaftar->email,
                    'password' => Hash::make(self::DEFAULT_KADER_PASSWORD),
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

            $this->kirimEmailDisetujui($pendaftar->fresh());

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

    private function kirimEmailDisetujui(Pendaftaran $pendaftaran): void
    {
        try {
            // Password di-enkripsi agar tidak tersimpan plain-text di kolom payload tabel jobs.
            $passwordEncrypted = Crypt::encryptString(self::DEFAULT_KADER_PASSWORD);

            Mail::to($pendaftaran->email)
                ->queue(new PendaftaranDisetujuiMail($pendaftaran, $passwordEncrypted));
        } catch (\Throwable $e) {
            // try/catch di sini hanya menangkap kegagalan dispatch ke queue (misal: DB down).
            // Kegagalan SMTP yang sebenarnya ditangani oleh method failed() di Mailable.
            Log::error('Gagal mendispatch email persetujuan pendaftaran ke queue', [
                'pendaftaran_id' => $pendaftaran->id,
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function kirimEmailDitolak(Pendaftaran $pendaftaran): void
    {
        try {
            Mail::to($pendaftaran->email)
                ->queue(new PendaftaranDitolakMail($pendaftaran));
        } catch (\Throwable $e) {
            // try/catch di sini hanya menangkap kegagalan dispatch ke queue (misal: DB down).
            // Kegagalan SMTP yang sebenarnya ditangani oleh method failed() di Mailable.
            Log::error('Gagal mendispatch email penolakan pendaftaran ke queue', [
                'pendaftaran_id' => $pendaftaran->id,
                'exception' => $e::class,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

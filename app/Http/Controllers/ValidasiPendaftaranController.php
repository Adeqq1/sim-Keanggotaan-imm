<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidasiPendaftaranRequest;
use App\Models\Anggota;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

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

    public function downloadDokumenIdentitas(Pendaftaran $pendaftaran)
    {
        $path = $pendaftaran->file_persyaratan;

        if (! is_string($path) || $path === '' || ! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $extension = preg_replace('/[^a-z0-9]/', '', strtolower(pathinfo($path, PATHINFO_EXTENSION))) ?? '';
        $jenisDokumen = Pendaftaran::JENIS_DOKUMEN_IDENTITAS[$pendaftaran->jenis_dokumen_identitas] ?? 'Dokumen';
        $filename = strtolower($jenisDokumen).'-pendaftaran-'.$pendaftaran->id.($extension === '' ? '' : '.'.$extension);

        return Storage::disk('local')->download($path, $filename, [
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
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

        $filePath = $pendaftar->file_persyaratan;

        $pendaftar->update([
            'password' => null,
            'status_validasi' => 'ditolak',
            'catatan_admin' => $validated['catatan_admin'],
            'file_persyaratan' => null,
        ]);

        if (is_string($filePath) && $filePath !== '') {
            try {
                if (! Storage::disk('local')->delete($filePath)) {
                    report(new RuntimeException(sprintf(
                        'Dokumen pendaftaran ID %d gagal dihapus setelah ditolak: %s',
                        $pendaftar->id,
                        $filePath,
                    )));
                }
            } catch (Throwable $exception) {
                report(new RuntimeException(sprintf(
                    'Dokumen pendaftaran ID %d gagal dihapus setelah ditolak: %s',
                    $pendaftar->id,
                    $filePath,
                ), 0, $exception));
            }
        }

        return redirect()->route('admin.pendaftaran.index')->with('success', 'Pendaftaran ditolak.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnggotaRequest;
use App\Models\Anggota;
use App\Services\NiaGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AnggotaController extends Controller
{
    public function index()
    {
        $anggotas = Anggota::with('user')->latest()->paginate(10);

        return view('admin.anggota.index', compact('anggotas'));
    }

    public function create()
    {
        return view('admin.anggota.create');
    }

    public function store(AnggotaRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('foto_profil')) {
            $path = $request->file('foto_profil')->store('foto_profil', 'public');
            $validated['foto_profil'] = $path;
        }

        Anggota::create($validated);

        return redirect()->route('admin.anggota.index')->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function show(Anggota $anggota)
    {
        $anggota->load('user');

        return view('admin.anggota.show', compact('anggota'));
    }

    public function edit(Anggota $anggota)
    {
        $anggota->load('user');

        return view('admin.anggota.edit', compact('anggota'));
    }

    public function update(AnggotaRequest $request, Anggota $anggota)
    {
        $validated = $request->validated();

        if ($anggota->user_id === auth()->id() && isset($validated['role']) && $validated['role'] !== 'admin') {
            abort(403, 'Anda tidak bisa menurunkan role akun Anda sendiri.');
        }

        if ($request->hasFile('foto_profil')) {
            if ($anggota->foto_profil) {
                Storage::disk('public')->delete($anggota->foto_profil);
            }
            $path = $request->file('foto_profil')->store('foto_profil', 'public');
            $validated['foto_profil'] = $path;
        }

        DB::transaction(function () use ($anggota, $validated) {
            $anggota->update($validated);

            if (isset($validated['role']) && $anggota->user) {
                $anggota->user->update(['role' => $validated['role']]);
            }
        });

        return redirect()->route('admin.anggota.index')->with('success', 'Anggota berhasil diupdate.');
    }

    public function destroy(Anggota $anggota)
    {
        if ($anggota->foto_profil) {
            Storage::disk('public')->delete($anggota->foto_profil);
        }

        // Jika anggota dihapus, user-nya juga mungkin perlu dihapus atau di-disable
        // Tapi di migration anggota ada onDelete('cascade') dari users?
        // Cek migration: $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        // Jadi menghapus User akan menghapus Anggota.
        // Tapi menghapus Anggota tidak menghapus User secara otomatis.
        // Di sini kita hapus Anggota record.

        $anggota->delete();

        return redirect()->route('admin.anggota.index')->with('success', 'Anggota berhasil dihapus.');
    }

    /**
     * Generate NIA untuk satu anggota (hanya jika NIA masih kosong).
     */
    public function generateNia(Anggota $anggota, NiaGenerator $generator)
    {
        try {
            $generator->generateForAnggota($anggota);

            return redirect()->route('admin.anggota.edit', $anggota)
                ->with('success', 'NIA berhasil di-generate: '.$anggota->fresh()->nia);
        } catch (\RuntimeException $e) {
            return redirect()->route('admin.anggota.edit', $anggota)
                ->with('warning', $e->getMessage());
        }
    }

    /**
     * Generate NIA massal untuk semua anggota yang belum memiliki NIA.
     */
    public function generateBulkNia(NiaGenerator $generator)
    {
        $anggotas = Anggota::whereNull('nia')
            ->orWhere('nia', '')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $jumlahDiproses = 0;
        $jumlahGagal = 0;

        foreach ($anggotas as $anggota) {
            try {
                $generator->generateForAnggota($anggota);
                $jumlahDiproses++;
            } catch (\Throwable $e) {
                $jumlahGagal++;
                Log::warning('Gagal generate NIA untuk anggota ID: '.$anggota->id, [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($jumlahDiproses > 0) {
            $pesan = "Berhasil generate NIA untuk {$jumlahDiproses} anggota.";
            if ($jumlahGagal > 0) {
                $pesan .= " Namun, {$jumlahGagal} anggota gagal diproses.";
            }

            return redirect()->route('admin.anggota.index')->with('success', $pesan);
        }

        if ($jumlahGagal > 0) {
            return redirect()->route('admin.anggota.index')
                ->with('warning', "Gagal generate NIA untuk {$jumlahGagal} anggota. Silakan periksa log sistem.");
        }

        return redirect()->route('admin.anggota.index')
            ->with('success', 'Tidak ada anggota yang perlu di-generate NIA-nya.');
    }
}

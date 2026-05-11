<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnggotaRequest;
use App\Models\Anggota;
use Illuminate\Support\Facades\Storage;

class AnggotaController extends Controller
{
    public function index()
    {
        $anggotas = Anggota::latest()->paginate(10);

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

    public function edit(Anggota $anggota)
    {
        return view('admin.anggota.edit', compact('anggota'));
    }

    public function update(AnggotaRequest $request, Anggota $anggota)
    {
        $validated = $request->validated();

        if ($request->hasFile('foto_profil')) {
            if ($anggota->foto_profil) {
                Storage::disk('public')->delete($anggota->foto_profil);
            }
            $path = $request->file('foto_profil')->store('foto_profil', 'public');
            $validated['foto_profil'] = $path;
        }

        $anggota->update($validated);

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
}

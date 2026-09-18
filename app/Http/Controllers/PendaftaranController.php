<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendaftaranRequest;
use App\Models\Pendaftaran;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class PendaftaranController extends Controller
{
    public function create()
    {
        return view('public.pendaftaran');
    }

    public function store(PendaftaranRequest $request)
    {
        $validated = $request->validated();

        $validated['password'] = Hash::make($validated['password']);

        $path = $request->file('file_persyaratan')->store('pendaftaran', 'local');

        if ($path === false) {
            throw ValidationException::withMessages([
                'file_persyaratan' => 'Dokumen identitas gagal diunggah. Silakan coba lagi.',
            ]);
        }

        $validated['file_persyaratan'] = $path;
        $validated['tanggal_daftar'] = now();
        $validated['status_validasi'] = 'pending';

        try {
            Pendaftaran::create($validated);
        } catch (Throwable $exception) {
            if (! Storage::disk('local')->delete($path)) {
                report(new RuntimeException('Dokumen pendaftaran yatim gagal dibersihkan.'));
            }

            throw $exception;
        }

        return redirect()->route('pendaftaran.success');
    }

    public function success()
    {
        return view('public.pendaftaran_success');
    }
}

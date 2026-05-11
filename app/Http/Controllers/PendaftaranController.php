<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendaftaranRequest;
use App\Models\Pendaftaran;

class PendaftaranController extends Controller
{
    public function create()
    {
        return view('public.pendaftaran');
    }

    public function store(PendaftaranRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('file_persyaratan')) {
            $path = $request->file('file_persyaratan')->store('pendaftaran', 'public');
            $validated['file_persyaratan'] = $path;
        }

        $validated['tanggal_daftar'] = now();
        $validated['status_validasi'] = 'pending';

        Pendaftaran::create($validated);

        return redirect()->route('pendaftaran.success');
    }

    public function success()
    {
        return view('public.pendaftaran_success');
    }
}

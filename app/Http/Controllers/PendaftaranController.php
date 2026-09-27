<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendaftaranRequest;
use App\Models\Pendaftaran;
use App\Services\ProfilePhoto;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

        $path = app(ProfilePhoto::class)->storeUpload($request->file('file_persyaratan'), 'pendaftaran', 'local', 'file_persyaratan', 90);

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

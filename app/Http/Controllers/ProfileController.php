<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Anggota;
use App\Services\ProfilePhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'anggota' => $request->user()->anggota,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, ProfilePhoto $profilePhoto): RedirectResponse
    {
        $user = $request->user();
        $anggota = $user->anggota;
        $anggotaId = $anggota?->getKey();
        $validated = $request->validated();
        $newPhotoPath = null;

        if ($request->hasFile('foto_profil') && ! $anggota) {
            throw ValidationException::withMessages([
                'foto_profil' => 'Foto profil hanya tersedia untuk akun anggota.',
            ]);
        }

        if ($request->hasFile('foto_profil')) {
            $newPhotoPath = $profilePhoto->store($request->file('foto_profil'));
        }

        try {
            $oldPhotoPath = DB::transaction(function () use ($user, $anggotaId, $validated, $newPhotoPath) {
                $lockedAnggota = $anggotaId !== null
                    ? Anggota::query()->lockForUpdate()->findOrFail($anggotaId)
                    : null;
                $oldPhotoPath = $lockedAnggota?->foto_profil;

                $user->fill([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);

                if ($user->isDirty('email')) {
                    $user->email_verified_at = null;
                }

                $user->save();

                if ($lockedAnggota) {
                    $anggotaData = [
                        'nama_lengkap' => $validated['nama_lengkap'],
                        'tempat_lahir' => $validated['tempat_lahir'],
                        'tanggal_lahir' => $validated['tanggal_lahir'],
                        'no_telp' => $validated['no_telp'],
                        'alamat' => $validated['alamat'],
                    ];

                    if ($newPhotoPath !== null) {
                        $anggotaData['foto_profil'] = $newPhotoPath;
                    }

                    $lockedAnggota->update($anggotaData);
                    $user->setRelation('anggota', $lockedAnggota);
                }

                return $oldPhotoPath;
            });
        } catch (Throwable $exception) {
            $this->deletePhotoAfterFailure($newPhotoPath, $exception);

            throw $exception;
        }

        $this->deleteReplacedPhoto($oldPhotoPath, $newPhotoPath, $anggotaId);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $photoPath = $user->anggota?->foto_profil;

        Auth::logout();

        $user->delete();
        $this->deletePhoto($photoPath);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    private function deletePhotoAfterFailure(?string $path, Throwable $exception): void
    {
        if ($path === null) {
            return;
        }

        try {
            if (Storage::disk('public')->exists($path) && ! Storage::disk('public')->delete($path)) {
                report(new RuntimeException('File foto profil baru gagal dibersihkan.', 0, $exception));
            }
        } catch (Throwable $cleanupException) {
            report($cleanupException);
        }
    }

    private function deleteReplacedPhoto(?string $oldPath, ?string $newPath, ?int $anggotaId): void
    {
        if ($oldPath === null || $newPath === null || $oldPath === $newPath || $anggotaId === null) {
            return;
        }

        try {
            $disk = Storage::disk('public');

            if ($disk->exists($oldPath) && ! $disk->delete($oldPath)) {
                report(new RuntimeException("Foto profil lama gagal dihapus untuk anggota {$anggotaId}: {$oldPath}"));
            }
        } catch (Throwable $exception) {
            report(new RuntimeException("Foto profil lama gagal dihapus untuk anggota {$anggotaId}: {$oldPath}", 0, $exception));
        }
    }

    private function deletePhoto(?string $path): void
    {
        if ($path === null) {
            return;
        }

        try {
            $disk = Storage::disk('public');

            if ($disk->exists($path) && ! $disk->delete($path)) {
                report(new RuntimeException("Foto profil gagal dihapus: {$path}"));
            }
        } catch (Throwable $exception) {
            report(new RuntimeException("Foto profil gagal dihapus: {$path}", previous: $exception));
        }
    }
}

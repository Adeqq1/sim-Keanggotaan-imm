<?php

namespace App\Http\Controllers;

use App\Http\Requests\MateriKegiatanRequest;
use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\MateriKegiatan;
use App\Models\Presensi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class MateriKegiatanController extends Controller
{
    public function adminIndex(): View
    {
        $materis = MateriKegiatan::query()->with('kegiatan')->latest()->paginate(12);

        return view('admin.kegiatan.materi-index', compact('materis'));
    }

    public function index(Kegiatan $kegiatan): View
    {
        return view('admin.kegiatan.materi.index', [
            'kegiatan' => $kegiatan,
            'materis' => $kegiatan->materiKegiatans()->latest()->paginate(6),
        ]);
    }

    public function create(Kegiatan $kegiatan): View
    {
        return view('admin.kegiatan.materi.create', compact('kegiatan'));
    }

    public function store(MateriKegiatanRequest $request, Kegiatan $kegiatan): RedirectResponse
    {
        $data = $request->safe()->except('file_materi');
        $path = $this->storeFile($request->file('file_materi'));

        try {
            DB::transaction(function () use ($kegiatan, $data, $path): void {
                $lockedKegiatan = Kegiatan::query()->lockForUpdate()->findOrFail($kegiatan->id);
                $lockedKegiatan->materiKegiatans()->create([...$data, 'file_materi' => $path]);
            });
        } catch (Throwable $exception) {
            $this->deleteFile($path, "materi baru kegiatan {$kegiatan->id}");
            throw $exception;
        }

        return redirect()->route('admin.kegiatan.materi-kegiatan.index', $kegiatan)
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(Kegiatan $kegiatan, MateriKegiatan $materiKegiatan): View
    {
        return view('admin.kegiatan.materi.edit', compact('kegiatan', 'materiKegiatan'));
    }

    public function update(MateriKegiatanRequest $request, Kegiatan $kegiatan, MateriKegiatan $materiKegiatan): RedirectResponse
    {
        $data = $request->safe()->except('file_materi');
        $newPath = $request->hasFile('file_materi') ? $this->storeFile($request->file('file_materi')) : null;
        $oldPath = null;

        try {
            DB::transaction(function () use ($kegiatan, $materiKegiatan, $data, $newPath, &$oldPath): void {
                Kegiatan::query()->lockForUpdate()->findOrFail($kegiatan->id);
                $locked = MateriKegiatan::query()->lockForUpdate()->findOrFail($materiKegiatan->id);
                $oldPath = $locked->file_materi;
                $locked->update($newPath ? [...$data, 'file_materi' => $newPath] : $data);
            });
        } catch (Throwable $exception) {
            if ($newPath) {
                $this->deleteFile($newPath, "pengganti materi {$materiKegiatan->id}");
            }
            throw $exception;
        }

        if ($newPath && $oldPath !== $newPath) {
            $this->deleteFile($oldPath, "materi {$materiKegiatan->id}");
        }

        return redirect()->route('admin.kegiatan.materi-kegiatan.index', $kegiatan)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan, MateriKegiatan $materiKegiatan): RedirectResponse
    {
        $id = $materiKegiatan->id;
        $path = DB::transaction(function () use ($kegiatan, $id): string {
            Kegiatan::query()->lockForUpdate()->findOrFail($kegiatan->id);
            $locked = MateriKegiatan::query()->lockForUpdate()->findOrFail($id);
            $path = $locked->file_materi;
            $locked->delete();

            return $path;
        });

        $this->deleteFile($path, "materi {$id}");

        return redirect()->route('admin.kegiatan.materi-kegiatan.index', $kegiatan)
            ->with('success', 'Materi berhasil dihapus.');
    }

    public function kaderIndex(): View|RedirectResponse
    {
        $anggota = auth()->user()->anggota;
        if (! $anggota) {
            return $this->missingMemberRedirect();
        }

        $materis = $this->accessibleQuery($anggota)
            ->withExists(['disimpanOleh as tersimpan' => fn ($query) => $query->whereKey($anggota->id)])
            ->latest()
            ->paginate(6);

        return view('kader.materi.index', compact('materis'));
    }

    public function savedIndex(): View|RedirectResponse
    {
        $anggota = auth()->user()->anggota;
        if (! $anggota) {
            return $this->missingMemberRedirect();
        }

        $materis = $this->accessibleQuery($anggota)
            ->whereHas('disimpanOleh', fn ($query) => $query->whereKey($anggota->id))
            ->latest('materi_kegiatan.created_at')
            ->paginate(6);

        return view('kader.materi.tersimpan', compact('materis'));
    }

    public function save(MateriKegiatan $materiKegiatan): RedirectResponse
    {
        $anggota = $this->authorizedMember($materiKegiatan);
        $timestamp = now();

        DB::table('materi_tersimpan')->upsert([
            [
                'anggota_id' => $anggota->id,
                'materi_kegiatan_id' => $materiKegiatan->id,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ], ['anggota_id', 'materi_kegiatan_id'], ['updated_at']);

        return back()->with('success', 'Materi berhasil disimpan.');
    }

    public function download(MateriKegiatan $materiKegiatan): StreamedResponse
    {
        $this->authorizedMember($materiKegiatan);
        $path = $materiKegiatan->file_materi;

        if (! is_string($path) || $path === '' || ! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $name = Str::slug($materiKegiatan->judul) ?: "materi-{$materiKegiatan->id}";

        return Storage::disk('local')->download($path, "{$name}.{$extension}", [
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function accessibleQuery(Anggota $anggota): Builder
    {
        return MateriKegiatan::query()
            ->with('kegiatan')
            ->whereHas('kegiatan.presensi', fn ($query) => $query
                ->where('anggota_id', $anggota->id)
                ->where('status_kehadiran', 'hadir'));
    }

    private function authorizedMember(MateriKegiatan $materiKegiatan): Anggota
    {
        $anggota = auth()->user()->anggota;

        if (! $anggota || ! Presensi::query()
            ->where('anggota_id', $anggota->id)
            ->where('kegiatan_id', $materiKegiatan->kegiatan_id)
            ->where('status_kehadiran', 'hadir')
            ->exists()) {
            abort(403, 'Anda tidak memiliki akses ke materi ini.');
        }

        return $anggota;
    }

    private function storeFile(?UploadedFile $file): string
    {
        $path = $file?->store('materi_kegiatan', 'local');

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'file_materi' => 'File materi gagal disimpan. Silakan coba lagi.',
            ]);
        }

        return $path;
    }

    private function deleteFile(?string $path, string $context): void
    {
        if (! $path) {
            return;
        }

        try {
            $disk = Storage::disk('local');

            if ($disk->exists($path) && ! $disk->delete($path)) {
                report(new RuntimeException("Gagal menghapus file {$context}: {$path}"));
            }
        } catch (Throwable $exception) {
            report(new RuntimeException("Gagal menghapus file {$context}: {$path}", previous: $exception));
        }
    }

    private function missingMemberRedirect(): RedirectResponse
    {
        return redirect()->route('kader.dashboard')
            ->with('error', 'Profil anggota Anda belum dibuat. Silakan hubungi admin.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\AnggotaRequest;
use App\Models\Anggota;
use App\Models\User;
use App\Services\NiaGenerator;
use App\Services\ProfilePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Support\SortParams;
use RuntimeException;
use Throwable;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', Rule::enum(RoleEnum::class)],
        ]);

        $search = trim($filters['search'] ?? '');
        $selectedRole = $filters['role'] ?? null;
        $options = ['nama' => 'Nama', 'nia' => 'NIA', 'role' => 'Role', 'status' => 'Status', 'created' => 'Waktu Ditambahkan'];
        $sort = SortParams::resolve($request, array_keys($options), 'created');
        $query = Anggota::with('user');

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nia', 'like', "%{$search}%");
            });
        }

        if ($selectedRole !== null) {
            $query->whereHas('user', fn ($userQuery) => $userQuery->where('role', $selectedRole));
        }

        $query->when($sort['key'] === 'role', fn ($query) => $query->orderBy(
            User::select('role')->whereColumn('users.id', 'anggota.user_id'), $sort['direction']
        ))
            ->when($sort['key'] !== 'role', fn ($query) => $query->orderBy([
                'nama' => 'nama_lengkap', 'nia' => 'nia', 'status' => 'status_aktif', 'created' => 'created_at',
            ][$sort['key']], $sort['direction']))
            ->orderByDesc('anggota.id');
        $anggotas = $query->paginate(12)->withQueryString();

        return view('admin.anggota.index', compact('anggotas', 'search', 'selectedRole', 'options', 'sort'));
    }

    public function create()
    {
        return view('admin.anggota.create');
    }

    public function store(AnggotaRequest $request, ProfilePhoto $profilePhoto)
    {
        $validated = $request->validated();
        $newPhotoPath = null;

        if ($request->hasFile('foto_profil')) {
            $newPhotoPath = $profilePhoto->store($request->file('foto_profil'));
            $validated['foto_profil'] = $newPhotoPath;
        }

        try {
            DB::transaction(function () use ($validated) {
                $user = User::create([
                    'name' => $validated['nama_lengkap'],
                    'email' => $validated['email'],
                    'password' => $validated['password'],
                    'role' => $validated['role'],
                ]);

                Anggota::create([
                    'user_id' => $user->id,
                    'nia' => $validated['nia'] ?? null,
                    'nama_lengkap' => $validated['nama_lengkap'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'alamat' => $validated['alamat'],
                    'no_telp' => $validated['no_telp'],
                    'foto_profil' => $validated['foto_profil'] ?? null,
                    'status_aktif' => $validated['status_aktif'] ?? true,
                ]);
            });
        } catch (Throwable $exception) {
            $this->deletePhotoAfterFailure($newPhotoPath, $exception);

            throw $exception;
        }

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

    public function update(AnggotaRequest $request, Anggota $anggota, ProfilePhoto $profilePhoto)
    {
        $validated = $request->validated();
        $newPhotoPath = null;

        if ($anggota->user_id === auth()->id() && isset($validated['role']) && $validated['role'] !== 'admin') {
            abort(403, 'Anda tidak bisa menurunkan role akun Anda sendiri.');
        }

        if ($request->hasFile('foto_profil')) {
            $newPhotoPath = $profilePhoto->store($request->file('foto_profil'));
            $validated['foto_profil'] = $newPhotoPath;
        }

        try {
            $oldPhotoPath = DB::transaction(function () use ($anggota, $validated) {
                $lockedAnggota = Anggota::query()
                    ->lockForUpdate()
                    ->findOrFail($anggota->getKey());
                $oldPhotoPath = $lockedAnggota->foto_profil;

                $lockedAnggota->update($validated);

                if (isset($validated['role']) && $lockedAnggota->user) {
                    $lockedAnggota->user->update(['role' => $validated['role']]);
                }

                return $oldPhotoPath;
            });
        } catch (Throwable $exception) {
            $this->deletePhotoAfterFailure($newPhotoPath, $exception);

            throw $exception;
        }

        $this->deleteReplacedPhoto($oldPhotoPath, $newPhotoPath, $anggota->id);

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

    private function deleteReplacedPhoto(?string $oldPath, ?string $newPath, int $anggotaId): void
    {
        if ($oldPath === null || $newPath === null || $oldPath === $newPath) {
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

    /**
     * Generate NIA untuk satu anggota (hanya jika NIA masih kosong).
     */
    public function generateNia(Anggota $anggota, NiaGenerator $generator)
    {
        try {
            $generator->generateForAnggota($anggota);

            return redirect()->route('admin.anggota.edit', $anggota)
                ->with('success', 'NIA berhasil dibuat: '.$anggota->fresh()->nia);
        } catch (RuntimeException $e) {
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
            } catch (Throwable $e) {
                $jumlahGagal++;
                Log::warning('Gagal generate NIA untuk anggota ID: '.$anggota->id, [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($jumlahDiproses > 0) {
            $pesan = "Berhasil membuat NIA untuk {$jumlahDiproses} anggota.";
            if ($jumlahGagal > 0) {
                $pesan .= " Namun, {$jumlahGagal} anggota gagal diproses.";
            }

            return redirect()->route('admin.anggota.index')->with('success', $pesan);
        }

        if ($jumlahGagal > 0) {
            return redirect()->route('admin.anggota.index')
                ->with('warning', "Gagal membuat NIA untuk {$jumlahGagal} anggota. Silakan periksa log sistem.");
        }

        return redirect()->route('admin.anggota.index')
            ->with('success', 'Tidak ada anggota yang perlu dibuatkan NIA.');
    }
}

<?php

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\LaporanKegiatan;
use App\Models\MateriKegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\User;
use App\Services\CertificateEligibility;
use Database\Seeders\DemoSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    Storage::fake('local');
});

test('demo seeder creates complete login and activity scenarios', function () {
    $this->seed(DemoSeeder::class);

    $admin = User::where('email', 'admin@admin.com')->firstOrFail();
    $instructor = User::where('email', 'instruktur@example.com')->firstOrFail();
    $kader = User::where('email', 'kader@example.com')->firstOrFail();
    $dad = Kegiatan::where('nama_kegiatan', 'Darul Arqam Dasar 2026')->firstOrFail();

    expect($admin->role)->toBe('admin')
        ->and($instructor->role)->toBe('instruktur')
        ->and($kader->role)->toBe('kader')
        ->and(Hash::check('password', $kader->password))->toBeTrue()
        ->and(Anggota::count())->toBeGreaterThanOrEqual(12)
        ->and(Kegiatan::where('tanggal_waktu', '>', now())->count())->toBeGreaterThanOrEqual(3)
        ->and($dad->sesiKegiatans()->count())->toBe(4)
        ->and(Pendaftaran::where('status_validasi', 'pending')->count())->toBe(3)
        ->and(MateriKegiatan::count())->toBeGreaterThanOrEqual(5)
        ->and(LaporanKegiatan::count())->toBeGreaterThanOrEqual(3)
        ->and(Arsip::count())->toBeGreaterThanOrEqual(6)
        ->and(Sertifikat::count())->toBeGreaterThanOrEqual(6);
});

test('seeded certificates satisfy the application eligibility rules', function () {
    $this->seed(DemoSeeder::class);
    $eligibility = app(CertificateEligibility::class);

    Sertifikat::with(['kegiatan', 'anggota.user'])->get()->each(
        fn (Sertifikat $certificate) => expect($eligibility->eligible($certificate->kegiatan, $certificate->anggota))->toBeTrue(),
    );

    Presensi::with('sesiKegiatan')->get()->each(
        fn (Presensi $attendance) => expect($attendance->kegiatan_id)->toBe($attendance->sesiKegiatan->kegiatan_id),
    );
});

test('demo seeder is idempotent', function () {
    $this->seed(DemoSeeder::class);
    $counts = [
        User::count(), Anggota::count(), Kegiatan::count(), Presensi::count(),
        MateriKegiatan::count(), LaporanKegiatan::count(), Arsip::count(), Sertifikat::count(),
    ];

    $this->seed(DemoSeeder::class);

    expect([
        User::count(), Anggota::count(), Kegiatan::count(), Presensi::count(),
        MateriKegiatan::count(), LaporanKegiatan::count(), Arsip::count(), Sertifikat::count(),
    ])->toBe($counts);
});

test('demo files exist on the correct disks and command is safe to rerun', function () {
    $this->seed(DemoSeeder::class);
    $this->artisan('demo:seed-files')->assertSuccessful();

    Anggota::whereNotNull('foto_profil')->get()->each(
        fn (Anggota $member) => Storage::disk('public')->assertExists($member->foto_profil),
    );
    Sertifikat::all()->each(
        fn (Sertifikat $certificate) => Storage::disk('public')->assertExists($certificate->file_sertifikat),
    );
    MateriKegiatan::all()->each(function (MateriKegiatan $material) {
        Storage::disk('local')->assertExists($material->file_materi);
        Storage::disk('public')->assertMissing($material->file_materi);
    });
    Arsip::all()->each(
        fn (Arsip $archive) => Storage::disk('local')->assertExists($archive->file_arsip),
    );
});

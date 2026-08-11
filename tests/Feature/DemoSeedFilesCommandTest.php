<?php

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // We isolate storage disks before running any tests
    Storage::fake('public');
    Storage::fake('local');

    // We also provide the background config required for generic certificates
    Storage::disk('local')->put('sertifikat_settings.json', json_encode(['use_background' => false]));
});

test('command fails gracefully if database is empty', function () {
    // No database records
    $this->artisan('demo:seed-files')
        ->expectsOutput('Baseline demo records are missing.')
        ->assertFailed();
});

test('command provisions files and updates database paths correctly', function () {
    // Seed baseline database
    $this->seed(DatabaseSeeder::class);

    // Initial state before command
    // The standard seeder DOES create arsip records (via database/seeders/DatabaseSeeder.php)
    // but the actual physical files aren't tested yet.
    $arsipCountBefore = Arsip::count();
    $sertifikatsCountBefore = Sertifikat::count();

    // Ensure dummy pendaftaran and presensi exist
    $pendaftaran = Pendaftaran::where('status_validasi', 'pending')->first();
    if (!$pendaftaran) {
        Pendaftaran::factory()->create(['status_validasi' => 'pending']);
    }

    $pendingClaim = Presensi::where('status_klaim', 'pending')->first();
    if (!$pendingClaim) {
        $kegiatan = Kegiatan::first();
        $anggota = Anggota::first();
        Presensi::updateOrCreate([
            'kegiatan_id' => $kegiatan->id,
            'anggota_id' => $anggota->id,
        ], [
            'status_kehadiran' => 'hadir',
            'status_klaim' => 'pending',
            'bukti_kehadiran' => null
        ]);
    }

    $approvedClaim = Presensi::where('status_klaim', 'disetujui')->first();
    if (!$approvedClaim) {
        $kegiatan = Kegiatan::first();
        // Use a different anggota for the second claim to avoid unique constraint violations
        $anggota2 = Anggota::skip(1)->first();
        Presensi::updateOrCreate([
            'kegiatan_id' => $kegiatan->id,
            'anggota_id' => $anggota2->id,
        ], [
            'status_kehadiran' => 'hadir',
            'status_klaim' => 'disetujui',
            'bukti_kehadiran' => null
        ]);
    }

    $this->artisan('demo:seed-files')
        ->expectsOutput('Starting demo files provisioning...')
        ->expectsOutput('Demo files successfully provisioned.')
        ->assertSuccessful();

    // Verify Public artifacts
    // 1. Profile photos
    $anggotaWithFoto = Anggota::whereNotNull('foto_profil')->first();
    expect($anggotaWithFoto)->not->toBeNull();
    Storage::disk('public')->assertExists($anggotaWithFoto->foto_profil);

    // Verify it is actually an image (or our dummy payload at minimum)
    $fotoBytes = Storage::disk('public')->get($anggotaWithFoto->foto_profil);
    expect(strlen($fotoBytes))->toBeGreaterThan(0);

    // 2. Thumbnails
    $kegiatan = Kegiatan::whereNotNull('thumbnail')->first();
    expect($kegiatan)->not->toBeNull();
    Storage::disk('public')->assertExists($kegiatan->thumbnail);

    // 3. Pendaftaran PDF
    $pendaftaran = Pendaftaran::whereNotNull('file_persyaratan')->first();
    if ($pendaftaran) {
        Storage::disk('local')->assertExists($pendaftaran->file_persyaratan);
        Storage::disk('public')->assertMissing($pendaftaran->file_persyaratan);
        expect($pendaftaran->jenis_dokumen_identitas)->toBe('ktp');
        $pdfBytes = Storage::disk('local')->get($pendaftaran->file_persyaratan);
        expect(str_starts_with($pdfBytes, '%PDF'))->toBeTrue();
    }

    // 4. Bukti Kehadiran
    $pendingClaim = Presensi::where('status_klaim', 'pending')->whereNotNull('bukti_kehadiran')->first();
    expect($pendingClaim)->not->toBeNull();
    Storage::disk('public')->assertExists($pendingClaim->bukti_kehadiran);

    // 5. Sertifikat — command calls generateCertificateFile which uses updateOrCreate
    // so only the certificates for approved claims will have real paths on disk.
    $sertifikat = Sertifikat::where('file_sertifikat', 'not like', '%dummy%')->first();
    expect($sertifikat)->not->toBeNull('Command should have created/updated at least one real certificate');
    Storage::disk('public')->assertExists($sertifikat->file_sertifikat);

    // Verify Private artifacts
    $arsip = Arsip::where('judul_dokumen', 'like', 'Dokumen Arsip Demo%')->first();
    expect($arsip)->not->toBeNull();
    Storage::disk('local')->assertExists($arsip->file_arsip);
    Storage::disk('public')->assertMissing($arsip->file_arsip); // Must not be public
});

test('command is idempotent and handles cleanup', function () {
    $this->seed(DatabaseSeeder::class);

    // Ensure claims exist so the seeder actually creates files that we can later check
    $pendingClaim = Presensi::where('status_klaim', 'pending')->first();
    if (!$pendingClaim) {
        Presensi::updateOrCreate([
            'kegiatan_id' => Kegiatan::first()->id,
            'anggota_id' => Anggota::first()->id,
        ], [
            'status_kehadiran' => 'hadir',
            'status_klaim' => 'pending',
        ]);
    }

    $approvedClaim = Presensi::where('status_klaim', 'disetujui')->first();
    if (!$approvedClaim) {
        $kegiatan = Kegiatan::first();
        // Use a different anggota for the second claim to avoid unique constraint violations
        $anggota2 = Anggota::skip(1)->first();
        Presensi::updateOrCreate([
            'kegiatan_id' => $kegiatan->id,
            'anggota_id' => $anggota2->id,
        ], [
            'status_kehadiran' => 'hadir',
            'status_klaim' => 'disetujui',
        ]);
    }

    $this->artisan('demo:seed-files')->assertSuccessful();

    // Add a sentinel non-demo file to ensure we don't nuke the entire feature directory
    Storage::disk('public')->put('foto_profil/user-uploaded.png', 'content');
    Storage::disk('local')->put('arsip/user-uploaded.pdf', 'content');
    Storage::disk('local')->put('pendaftaran/user-uploaded.pdf', 'content');

    // Run again
    $this->artisan('demo:seed-files')->assertSuccessful();

    // Sentinel files must survive
    Storage::disk('public')->assertExists('foto_profil/user-uploaded.png');
    Storage::disk('local')->assertExists('arsip/user-uploaded.pdf');
    Storage::disk('local')->assertExists('pendaftaran/user-uploaded.pdf');

    // Check we didn't duplicate archives
    // It creates 2 archives because the command takes 2 Anggota owners.
    // If it ran twice, it should STILL be 2 archives, not 4, because of updateOrCreate.
    $arsipCountBefore = Arsip::where('judul_dokumen', 'like', 'Dokumen Arsip Demo%')->count();
    expect($arsipCountBefore)->toBe(2);
});

test('private archives enforce authorization correctly', function () {
    $this->seed(DatabaseSeeder::class);
    $this->artisan('demo:seed-files');

    // Get the seeded demo archive, not the factory default 'arsip/dummy.pdf'
    $arsip = Arsip::where('judul_dokumen', 'like', 'Dokumen Arsip Demo%')->first();
    $owner = Anggota::find($arsip->anggota_id)->user;

    // A different kader who DOES have an anggota record
    $otherKader = User::where('role', 'kader')->where('id', '!=', $owner->id)->whereHas('anggota')->first();
    // If we only seeded one kader during tests, let's create another
    if (!$otherKader) {
        $otherKader = User::factory()->kader()->create();
        Anggota::factory()->create(['user_id' => $otherKader->id]);
    }
    $admin = User::where('role', 'admin')->first();

    // Owner can download
    $this->actingAs($owner)
        ->get(route('kader.arsip.download', $arsip))
        ->assertSuccessful();

    // Other kader gets 403 Forbidden
    $this->actingAs($otherKader)
        ->get(route('kader.arsip.download', $arsip))
        ->assertForbidden();

    // Admin can download via admin route
    $this->actingAs($admin)
        ->get(route('admin.arsip.download', $arsip))
        ->assertSuccessful();
});

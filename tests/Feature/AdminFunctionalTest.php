<?php

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

test('admin can approve pendaftaran and create kader account', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $password = 'Pendaftaran-Password-2026';
    $pendaftaran = Pendaftaran::factory()->create([
        'password' => Hash::make($password),
    ]);

    $response = $this->actingAs($admin)
        ->post(route('admin.pendaftaran.validate', $pendaftaran->id), [
            'status' => 'disetujui',
            'role' => 'kader',
        ]);

    $response->assertRedirect(route('admin.pendaftaran.index'));

    $pendaftaran->refresh();
    expect($pendaftaran->status_validasi)->toBe('disetujui');

    $this->assertDatabaseHas('users', [
        'email' => $pendaftaran->email,
        'role' => 'kader',
    ]);

    $newUser = User::where('email', $pendaftaran->email)->first();
    expect(Hash::check($password, $pendaftaran->password))->toBeTrue()
        ->and(Hash::check($password, $newUser->password))->toBeTrue();
    $this->assertDatabaseHas('anggota', [
        'user_id' => $newUser->id,
        'nama_lengkap' => $pendaftaran->nama_lengkap,
    ]);

    Mail::assertNothingQueued();
});

test('admin can approve a legacy pendaftaran with a temporary password', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $pendaftaran = Pendaftaran::factory()->legacyWithoutPassword()->create();

    $this->actingAs($admin)
        ->post(route('admin.pendaftaran.validate', $pendaftaran), [
            'status' => 'disetujui',
            'role' => 'kader',
        ])->assertRedirect(route('admin.pendaftaran.index'));

    $user = User::where('email', $pendaftaran->email)->firstOrFail();

    expect($user->password)->not->toBeNull();
    Mail::assertNothingQueued();
});

test('admin pendaftaran detail page posts explicit status for approval action', function () {
    $admin = User::factory()->admin()->create();
    $pendaftaran = Pendaftaran::factory()->instruktur()->create();

    $response = $this->actingAs($admin)
        ->get(route('admin.pendaftaran.show', $pendaftaran));

    $response->assertOk()
        ->assertSee('name="status" value="disetujui"', false)
        ->assertSee('name="role"', false)
        ->assertSee('w-100 w-sm-auto', false)
        ->assertSee('btn-ui-sm', false)
        ->assertSeeText('Daftar Sebagai')
        ->assertSeeText('Role Akun')
        ->assertSeeText('Instruktur')
        ->assertSeeText('Setujui & Buat Akun')
        ->assertSeeText('Tolak Pendaftaran')
        ->assertSeeText('Kembali ke Daftar');
});

test('admin pendaftaran index shows selected role', function () {
    $admin = User::factory()->admin()->create();
    Pendaftaran::factory()->instruktur()->create([
        'nama_lengkap' => 'Fatimah Instruktur',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.pendaftaran.index'));

    $response->assertOk();
    $response->assertSeeText('Fatimah Instruktur');
    $response->assertSeeText('Daftar sebagai: Instruktur');
});

test('admin can approve pendaftaran and create instruktur account when applicant chooses instruktur role', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $pendaftaran = Pendaftaran::factory()->instruktur()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.pendaftaran.validate', $pendaftaran->id), [
            'status' => 'disetujui',
            'role' => 'instruktur',
        ]);

    $response->assertRedirect(route('admin.pendaftaran.index'));

    $this->assertDatabaseHas('users', [
        'email' => $pendaftaran->email,
        'role' => 'instruktur',
    ]);
});

test('admin can override selected pendaftaran role during approval', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $pendaftaran = Pendaftaran::factory()->instruktur()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.pendaftaran.validate', $pendaftaran->id), [
            'status' => 'disetujui',
            'role' => 'kader',
        ]);

    $response->assertRedirect(route('admin.pendaftaran.index'));

    $this->assertDatabaseHas('users', [
        'email' => $pendaftaran->email,
        'role' => 'kader',
    ]);

    $this->assertDatabaseHas('pendaftaran', [
        'email' => $pendaftaran->email,
        'role' => 'kader',
        'status_validasi' => 'disetujui',
    ]);
});

test('admin cannot approve pendaftaran with admin role', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $pendaftaran = Pendaftaran::factory()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.pendaftaran.validate', $pendaftaran->id), [
            'status' => 'disetujui',
            'role' => 'admin',
        ]);

    $response->assertSessionHasErrors('role');

    $pendaftaran->refresh();
    expect($pendaftaran->status_validasi)->toBe('pending')
        ->and($pendaftaran->user_id)->toBeNull();

    Mail::assertNothingQueued();
});

test('admin cannot approve pendaftaran when email already belongs to a user', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $existingUser = User::factory()->kader()->create();
    $pendaftaran = Pendaftaran::factory()->create([
        'email' => $existingUser->email,
    ]);

    $response = $this->actingAs($admin)
        ->post(route('admin.pendaftaran.validate', $pendaftaran->id), [
            'status' => 'disetujui',
            'role' => 'kader',
        ]);

    $response->assertSessionHasErrors('email');

    $pendaftaran->refresh();
    expect($pendaftaran->status_validasi)->toBe('pending')
        ->and($pendaftaran->user_id)->toBeNull();

    $this->assertDatabaseCount('users', 2);
    Mail::assertNothingQueued();
});

test('admin cannot re-approve already processed pendaftaran', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $pendaftaran = Pendaftaran::factory()->create([
        'status_validasi' => 'disetujui',
        'user_id' => User::factory()->kader()->create()->id,
    ]);

    $response = $this->actingAs($admin)
        ->from(route('admin.pendaftaran.show', $pendaftaran->id))
        ->post(route('admin.pendaftaran.validate', $pendaftaran->id), [
            'status' => 'disetujui',
            'role' => 'kader',
        ]);

    $response->assertSessionHasErrors('status');
    $this->assertDatabaseCount('users', 2);
    Mail::assertNothingQueued();
});

test('admin can reject pendaftaran', function () {
    $admin = User::factory()->admin()->create();
    $pendaftaran = Pendaftaran::factory()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.pendaftaran.validate', $pendaftaran->id), [
            'status' => 'ditolak',
            'catatan_admin' => 'Data tidak lengkap.',
        ]);

    $response->assertRedirect(route('admin.pendaftaran.index'));

    $pendaftaran->refresh();
    expect($pendaftaran->status_validasi)->toBe('ditolak');
    expect($pendaftaran->catatan_admin)->toBe('Data tidak lengkap.');
    expect($pendaftaran->password)->toBeNull();
});

test('admin must provide catatan admin when rejecting pendaftaran', function () {
    $admin = User::factory()->admin()->create();
    $pendaftaran = Pendaftaran::factory()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.pendaftaran.validate', $pendaftaran->id), [
            'status' => 'ditolak',
        ]);

    $response->assertSessionHasErrors('catatan_admin');

    $pendaftaran->refresh();
    expect($pendaftaran->status_validasi)->toBe('pending');
});

test('admin can store presensi data', function () {
    $admin = User::factory()->admin()->create();
    $kegiatan = Kegiatan::factory()->create();
    $anggota1 = Anggota::factory()->create();
    $anggota2 = Anggota::factory()->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.presensi.store', $kegiatan->id), [
            'presensi' => [
                $anggota1->id => 'hadir',
                $anggota2->id => 'izin',
            ],
        ]);

    $response->assertRedirect(route('admin.kegiatan.index'));

    $this->assertDatabaseHas('presensi', [
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota1->id,
        'status_kehadiran' => 'hadir',
    ]);

    $this->assertDatabaseHas('presensi', [
        'kegiatan_id' => $kegiatan->id,
        'anggota_id' => $anggota2->id,
        'status_kehadiran' => 'izin',
    ]);
});

test('admin cannot access arsip upload page (upload only by kader)', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/admin/arsip/create');

    // Route arsip create tidak terdaftar; request ke /admin/arsip/create
    // akan match pola {arsip} untuk method yang tidak didukung (405 Method Not Allowed)
    $response->assertStatus(405);
});

test('admin cannot upload arsip via POST (upload only by kader)', function () {
    Storage::fake('local');

    $admin = User::factory()->admin()->create();
    $anggota = Anggota::factory()->create();

    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $response = $this->actingAs($admin)
        ->post('/admin/arsip', [
            'anggota_id' => $anggota->id,
            'judul_dokumen' => 'Surat Keterangan',
            'kategori_arsip' => 'surat_masuk',
            'file_arsip' => $file,
        ]);

    $response->assertStatus(405);

    $this->assertDatabaseMissing('arsip', [
        'judul_dokumen' => 'Surat Keterangan',
    ]);
});

test('admin can search and filter arsip', function () {
    $admin = User::factory()->admin()->create();

    Arsip::factory()->create([
        'judul_dokumen' => 'Proposal Rapat Kerja',
        'nomor_dokumen' => 'PROP-002',
        'kategori_arsip' => 'proposal',
    ]);

    Arsip::factory()->create([
        'judul_dokumen' => 'Surat Keluar Cabang',
        'nomor_dokumen' => 'SK-002',
        'kategori_arsip' => 'surat_keluar',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.arsip.index', [
        'q' => 'PROP',
        'kategori' => 'proposal',
    ]));

    $response->assertSuccessful();
    $response->assertSee('Proposal Rapat Kerja');
    $response->assertDontSee('Surat Keluar Cabang');
    $response->assertSee('Atur ulang filter');
});

test('admin can download arsip', function () {
    $admin = User::factory()->admin()->create();

    Storage::fake('local');
    Storage::disk('local')->put('arsip/test.pdf', 'dummy content');

    $arsip = Arsip::factory()->create([
        'file_arsip' => 'arsip/test.pdf',
        'judul_dokumen' => 'Test Document',
    ]);

    $response = $this->actingAs($admin)
        ->get(route('admin.arsip.download', $arsip->id));

    $response->assertSuccessful();
});

test('admin can delete arsip', function () {
    $admin = User::factory()->admin()->create();

    Storage::fake('local');
    Storage::disk('local')->put('arsip/test.pdf', 'dummy content');

    $arsip = Arsip::factory()->create([
        'file_arsip' => 'arsip/test.pdf',
    ]);

    $response = $this->actingAs($admin)
        ->delete(route('admin.arsip.destroy', $arsip->id));

    $response->assertRedirect();

    $this->assertDatabaseMissing('arsip', ['id' => $arsip->id]);
    Storage::disk('local')->assertMissing('arsip/test.pdf');
});

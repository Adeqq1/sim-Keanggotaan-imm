<?php

use App\Models\Anggota;
use App\Models\Kegiatan;
use App\Models\Presensi;
use App\Models\Sertifikat;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

test('kader can view profile edit page', function () {
    $user = User::factory()->kader()->create();
    Anggota::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('profile.edit'));

    $response->assertSuccessful();
});

test('kader can update profile', function () {
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Updated Name',
            'email' => $user->email,
            'nama_lengkap' => 'Updated Nama Lengkap',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1999-05-15',
            'no_telp' => '089876543210',
            'alamat' => 'Jl. Baru No. 10',
        ]);

    $response->assertRedirect(route('profile.edit'));

    $user->refresh();
    expect($user->name)->toBe('Updated Name');

    $anggota->refresh();
    expect($anggota->nama_lengkap)->toBe('Updated Nama Lengkap');
    expect($anggota->tempat_lahir)->toBe('Bandung');
});

test('kader can view ekta preview', function () {
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create([
        'user_id' => $user->id,
        'nama_lengkap' => 'Aisyah Kader Login',
        'nia' => '24000001',
        'created_at' => '2024-01-15 00:00:00',
    ]);

    $response = $this->actingAs($user)->get(route('kader.ekta'));

    $response->assertSuccessful()
        ->assertSeeText('KARTU TANDA KADER')
        ->assertSeeText('AISYAH KADER LOGIN')
        ->assertSeeText('24000001')
        ->assertSeeText('2024')
        ->assertSee('images/logo.png', false)
        ->assertSee('ekta-card__logo-badge', false)
        ->assertSee('ekta-card__swoop', false)
        ->assertSee('ekta-card__photo-frame', false)
        ->assertSee('ekta-card__top-note', false)
        ->assertSee(route('kader.ekta.download'), false);

    expect($anggota->fresh()->nama_lengkap)->toBe('Aisyah Kader Login');
});

test('ekta preview displays a stored profile photo', function () {
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $user->id, 'nama_lengkap' => 'Foto Anggota']);
    Storage::fake('public');

    $photoPath = 'foto_profil/profile.png';
    Storage::disk('public')->put($photoPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='));
    $anggota->update(['foto_profil' => $photoPath]);

    $response = $this->actingAs($user)->get(route('kader.ekta'));

    $response->assertSuccessful()
        ->assertSee('data:image/png;base64,', false)
        ->assertSee('images/logo.png', false)
        ->assertDontSee('data-testid="ekta-photo-fallback"', false);
});

test('kader can download ekta pdf', function () {
    $user = User::factory()->kader()->create();
    Anggota::factory()->create([
        'user_id' => $user->id,
        'nia' => '24000001',
        'nama_lengkap' => 'Aisyah Kader Login',
    ]);

    $response = $this->actingAs($user)->get(route('kader.ekta.download'));

    $response->assertSuccessful();
    expect($response->headers->get('Content-Type'))->toContain('pdf');
    expect($response->headers->get('Content-Disposition'))
        ->toContain('attachment')
        ->toContain('E-KTA_24000001.pdf');
    expect($response->getContent())->toStartWith('%PDF');
});

test('ekta PDF keeps short and long cards on one page', function (string $name, bool $withPhoto) {
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create([
        'user_id' => $user->id,
        'nama_lengkap' => $name,
        'nia' => '24000001',
    ]);

    $photoSrc = null;
    if ($withPhoto) {
        Storage::fake('public');
        $photoPath = 'foto_profil/test.png';
        Storage::disk('public')->put($photoPath, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='));
        $anggota->update(['foto_profil' => $photoPath]);
        $photoSrc = Storage::disk('public')->path($photoPath);
    }

    $pdf = Pdf::loadView('pdf.ekta', [
        'anggota' => $anggota->fresh(),
        'roleLabel' => 'Kader',
        'photoSrc' => $photoSrc,
        'logoSrc' => public_path('images/logo.png'),
    ])->setPaper([0, 0, 240, 152.25]);
    $pdf->render();

    expect($pdf->getDomPDF()->getCanvas()->get_page_count())->toBe(1);
})->with([
    'short fallback' => ['Aisyah Kader', false],
    'long fallback' => ['Aisyah Kader Dengan Nama Sangat Panjang Untuk Kartu', false],
    'short photo' => ['Aisyah Kader', true],
    'long photo' => ['Aisyah Kader Dengan Nama Sangat Panjang Untuk Kartu', true],
]);

test('ekta print styles are limited to the ekta page', function () {
    $user = User::factory()->kader()->create();
    Anggota::factory()->create(['user_id' => $user->id]);

    $ektaResponse = $this->actingAs($user)->get(route('kader.ekta'));
    $profileResponse = $this->actingAs($user)->get(route('profile.edit'));

    $ektaResponse->assertSee('data-testid="ekta-print-styles"', false)
        ->assertSee('@media print', false);
    $profileResponse->assertDontSee('data-testid="ekta-print-styles"', false)
        ->assertDontSee('@media print', false);
});

test('kader only sees their own ekta data', function () {
    $owner = User::factory()->kader()->create();
    $otherUser = User::factory()->kader()->create();

    Anggota::factory()->create([
        'user_id' => $owner->id,
        'nama_lengkap' => 'PEMILIK KTA',
        'nia' => '24000002',
    ]);
    Anggota::factory()->create([
        'user_id' => $otherUser->id,
        'nama_lengkap' => 'DATA ANGGOTA LAIN',
        'nia' => '24000003',
    ]);

    $response = $this->actingAs($owner)->get(route('kader.ekta'));

    $response->assertSuccessful()
        ->assertSeeText('PEMILIK KTA')
        ->assertDontSeeText('DATA ANGGOTA LAIN')
        ->assertDontSeeText('24000003');
});

test('kader without anggota is redirected from each ekta route', function (string $routeName) {
    $user = User::factory()->kader()->create();

    $response = $this->actingAs($user)->get(route($routeName));

    $response->assertRedirect(route('kader.dashboard'))
        ->assertSessionHas('error', 'Data anggota tidak ditemukan.');
})->with([
    'preview' => 'kader.ekta',
    'download' => 'kader.ekta.download',
]);

test('guest is redirected to login from each ekta route', function (string $routeName) {
    $this->get(route($routeName))->assertRedirect(route('login'));
})->with([
    'preview' => 'kader.ekta',
    'download' => 'kader.ekta.download',
]);

test('non-kader cannot access kader ekta routes', function (string $role, string $routeName) {
    $user = User::factory()->create(['role' => $role]);

    $this->actingAs($user)->get(route($routeName))->assertForbidden();
})->with([
    'admin preview' => ['admin', 'kader.ekta'],
    'admin download' => ['admin', 'kader.ekta.download'],
    'instruktur preview' => ['instruktur', 'kader.ekta'],
    'instruktur download' => ['instruktur', 'kader.ekta.download'],
]);

test('ekta card renders clear fallbacks for incomplete data', function () {
    $anggota = new Anggota;
    $anggota->nama_lengkap = '';
    $anggota->nia = null;
    $anggota->created_at = null;
    $anggota->foto_profil = null;

    $html = view('components.ekta-card', [
        'anggota' => $anggota,
        'roleLabel' => 'Kader',
    ])->render();

    expect($html)
        ->toContain('KARTU TANDA KADER')
        ->toContain('NAMA BELUM TERSEDIA')
        ->toContain('BELUM TERSEDIA')
        ->toContain('data-testid="ekta-photo-fallback"')
        ->and(strip_tags($html))->toContain('?');

    $visibleText = preg_replace('/<style\b[^>]*>.*?<\/style>/is', '', $html);

    expect(strip_tags((string) $visibleText))->not->toContain('value');
});

test('ekta uses fallback when stored photo is missing', function () {
    $user = User::factory()->kader()->create();

    Anggota::factory()->create([
        'user_id' => $user->id,
        'nama_lengkap' => 'Foto Hilang',
        'foto_profil' => 'foto_profil/missing.jpg',
    ]);
    Storage::fake('public');

    $response = $this->actingAs($user)->get(route('kader.ekta'));

    $response->assertSuccessful()
        ->assertSee('data-testid="ekta-photo-fallback"', false)
        ->assertSeeText('F');
});

test('ekta download falls back to anggota id when nia is empty', function () {
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->tanpaNia()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('kader.ekta.download'));

    $response->assertSuccessful();
    expect($response->headers->get('Content-Disposition'))
        ->toContain('E-KTA_'.$anggota->id.'.pdf');
});

test('kader can view sertifikat list', function () {
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $user->id]);
    $kegiatan = Kegiatan::factory()->create();

    Sertifikat::factory()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
    ]);

    $response = $this->actingAs($user)->get(route('kader.sertifikat.index'));

    $response->assertSuccessful();
});

test('kader can download sertifikat pdf', function () {
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $user->id]);

    Storage::fake('public');
    Storage::disk('public')->put('sertifikat/test.pdf', 'dummy content');

    $sertifikat = Sertifikat::factory()->create([
        'anggota_id' => $anggota->id,
        'file_sertifikat' => 'sertifikat/test.pdf',
    ]);

    $response = $this->actingAs($user)->get(route('kader.sertifikat.download', $sertifikat->id));

    $response->assertSuccessful();
});

test('kader can view riwayat keaktifan', function () {
    $user = User::factory()->kader()->create();
    $anggota = Anggota::factory()->create(['user_id' => $user->id]);
    $kegiatan = Kegiatan::factory()->create();

    Presensi::factory()->hadir()->create([
        'anggota_id' => $anggota->id,
        'kegiatan_id' => $kegiatan->id,
    ]);

    $response = $this->actingAs($user)->get(route('kader.riwayat.index'));

    $response->assertSuccessful();
});

test('kader can view arsip list', function () {
    $user = User::factory()->kader()->create();
    Anggota::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('kader.arsip.index'));

    $response->assertSuccessful();
});

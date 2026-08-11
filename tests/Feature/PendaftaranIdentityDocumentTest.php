<?php

use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

function identityDocumentPayload(array $overrides = []): array
{
    return array_merge([
        'nama_lengkap' => 'Calon Anggota',
        'email' => 'calon.anggota@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'kader',
        'jenis_dokumen_identitas' => 'ktp',
        'tempat_lahir' => 'Yogyakarta',
        'tanggal_lahir' => '2000-01-01',
        'no_telp' => '08123456789',
        'alamat' => 'Jl. Contoh No. 1',
        'file_persyaratan' => validIdentityPdf(),
    ], $overrides);
}

function validIdentityPdf(string $name = 'identitas.pdf'): UploadedFile
{
    return UploadedFile::fake()->createWithContent($name, "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF");
}

test('public registration form exposes required identity document fields and roles', function () {
    $response = $this->get(route('pendaftaran'));

    $response->assertOk()
        ->assertSee('name="jenis_dokumen_identitas"', false)
        ->assertSee('value="ktp"', false)
        ->assertSee('value="ktm"', false)
        ->assertSee('name="file_persyaratan"', false)
        ->assertSee('name="file_persyaratan"', false)
        ->assertSee('accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"', false)
        ->assertSee('Maksimum 2 MB')
        ->assertSee('value="kader"', false)
        ->assertSee('value="instruktur"', false);

    expect($response->getContent())
        ->toContain('Wajib diisi')
        ->toContain('class="text-danger" aria-hidden="true">*</span>')
        ->toContain('id="jenis_dokumen_identitas"')
        ->toContain('id="file_persyaratan"')
        ->toContain('name="jenis_dokumen_identitas"')
        ->toContain('required');
});

test('public registration requires identity type and file', function () {
    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), identityDocumentPayload([
            'email' => 'missing.identity@example.com',
            'jenis_dokumen_identitas' => '',
            'file_persyaratan' => null,
        ]));

    $response->assertRedirect(route('pendaftaran'))
        ->assertSessionHasErrors(['jenis_dokumen_identitas', 'file_persyaratan']);

    $this->assertDatabaseMissing('pendaftaran', ['email' => 'missing.identity@example.com']);
});

test('public registration is limited to five requests per minute per IP', function () {
    $ip = '203.0.113.10';

    foreach (range(1, 5) as $attempt) {
        $this->withServerVariables(['REMOTE_ADDR' => $ip])
            ->post(route('pendaftaran.store'), identityDocumentPayload([
                'email' => "throttle-{$attempt}@example.com",
            ]))
            ->assertRedirect(route('pendaftaran.success'));
    }

    $blocked = $this->withServerVariables(['REMOTE_ADDR' => $ip])
        ->post(route('pendaftaran.store'), identityDocumentPayload([
            'email' => 'throttle-blocked@example.com',
        ]));

    $blocked->assertTooManyRequests()
        ->assertHeader('X-RateLimit-Limit', '5')
        ->assertHeader('X-RateLimit-Remaining', '0')
        ->assertHeader('Retry-After');

    $this->assertDatabaseMissing('pendaftaran', [
        'email' => 'throttle-blocked@example.com',
    ]);
    expect(Storage::disk('local')->allFiles('pendaftaran'))->toHaveCount(5);

    $this->withServerVariables(['REMOTE_ADDR' => $ip])
        ->get(route('pendaftaran'))
        ->assertOk();

    $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10'])
        ->post(route('pendaftaran.store'), identityDocumentPayload([
            'email' => 'throttle-other-ip@example.com',
        ]))
        ->assertRedirect(route('pendaftaran.success'));
});

test('public registration rejects an unknown identity type', function () {
    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), identityDocumentPayload([
            'email' => 'invalid.identity.type@example.com',
            'jenis_dokumen_identitas' => 'sim',
        ]));

    $response->assertRedirect(route('pendaftaran'))
        ->assertSessionHasErrors('jenis_dokumen_identitas');
});

test('public registration accepts every supported identity document format', function (UploadedFile $file) {
    $response = $this->post(route('pendaftaran.store'), identityDocumentPayload([
        'email' => fake()->unique()->safeEmail(),
        'file_persyaratan' => $file,
    ]));

    $response->assertRedirect(route('pendaftaran.success'));
})->with([
    'pdf' => fn () => validIdentityPdf(),
    'jpg' => fn () => UploadedFile::fake()->image('identitas.jpg'),
    'jpeg' => fn () => UploadedFile::fake()->image('identitas.jpeg'),
    'png' => fn () => UploadedFile::fake()->image('identitas.png'),
]);

test('public registration rejects unsupported identity files', function (UploadedFile $file) {
    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), identityDocumentPayload([
            'email' => fake()->unique()->safeEmail(),
            'file_persyaratan' => $file,
        ]));

    $response->assertRedirect(route('pendaftaran'))
        ->assertSessionHasErrors('file_persyaratan');
})->with([
    'text file' => fn () => UploadedFile::fake()->create('identitas.txt', 10, 'text/plain'),
    'mismatched mime' => fn () => UploadedFile::fake()->create('identitas.pdf', 10, 'text/plain'),
]);

test('public registration enforces the identity file size limit', function (int $kilobytes, bool $valid) {
    $email = fake()->unique()->safeEmail();
    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), identityDocumentPayload([
            'email' => $email,
            'file_persyaratan' => UploadedFile::fake()->create('identitas.pdf', $kilobytes, 'application/pdf'),
        ]));

    if ($valid) {
        $response->assertRedirect(route('pendaftaran.success'));
        $this->assertDatabaseHas('pendaftaran', ['email' => $email]);
    } else {
        $response->assertRedirect(route('pendaftaran'))
            ->assertSessionHasErrors('file_persyaratan');
        $this->assertDatabaseMissing('pendaftaran', ['email' => $email]);
    }
})->with([
    '2048 KiB' => [2048, true],
    '2049 KiB' => [2049, false],
]);

test('kader and instruktur registrations both require and store identity documents', function (string $role) {
    $email = fake()->unique()->safeEmail();

    $response = $this->post(route('pendaftaran.store'), identityDocumentPayload([
        'email' => $email,
        'role' => $role,
        'jenis_dokumen_identitas' => 'ktm',
    ]));

    $response->assertRedirect(route('pendaftaran.success'));
    $this->assertDatabaseHas('pendaftaran', [
        'email' => $email,
        'role' => $role,
        'jenis_dokumen_identitas' => 'ktm',
    ]);
})->with(['kader', 'instruktur']);

test('public registration stores identity documents privately with a generated path', function () {
    $response = $this->post(route('pendaftaran.store'), identityDocumentPayload([
        'email' => 'private.identity@example.com',
        'file_persyaratan' => validIdentityPdf('original-name.pdf'),
    ]));

    $response->assertRedirect(route('pendaftaran.success'));

    $pendaftaran = Pendaftaran::where('email', 'private.identity@example.com')->firstOrFail();

    expect($pendaftaran->file_persyaratan)
        ->toStartWith('pendaftaran/')
        ->not->toContain('original-name');

    Storage::disk('local')->assertExists($pendaftaran->file_persyaratan);
    Storage::disk('public')->assertMissing($pendaftaran->file_persyaratan);
});

test('uploaded identity document is cleaned up when database creation fails', function () {
    Pendaftaran::creating(function () {
        throw new RuntimeException('Simulated database failure.');
    });

    expect(fn () => $this->withoutExceptionHandling()->post(
        route('pendaftaran.store'),
        identityDocumentPayload(['email' => 'cleanup.identity@example.com'])
    ))->toThrow(RuntimeException::class, 'Simulated database failure.');

    Pendaftaran::flushEventListeners();

    expect(Storage::disk('local')->allFiles('pendaftaran'))->toBeEmpty();
    $this->assertDatabaseMissing('pendaftaran', ['email' => 'cleanup.identity@example.com']);
});

test('admin can see and download an identity document through the private route', function () {
    $admin = User::factory()->admin()->create();
    $path = 'pendaftaran/private-identity.pdf';
    Storage::disk('local')->put($path, '%PDF-1.4 private document');
    $pendaftaran = Pendaftaran::factory()->create([
        'file_persyaratan' => $path,
        'jenis_dokumen_identitas' => 'ktp',
    ]);

    $detail = $this->actingAs($admin)->get(route('admin.pendaftaran.show', $pendaftaran));
    $detail->assertOk()
        ->assertSeeText('Jenis Dokumen Identitas')
        ->assertSeeText('KTP')
        ->assertSee(route('admin.pendaftaran.document.download', $pendaftaran), false)
        ->assertDontSee('/storage/pendaftaran/', false);

    $download = $this->actingAs($admin)
        ->get(route('admin.pendaftaran.document.download', $pendaftaran));

    $download->assertSuccessful()
        ->assertHeader('Cache-Control', 'max-age=0, no-store, private')
        ->assertHeader('Pragma', 'no-cache')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Content-Disposition', 'attachment; filename=ktp-pendaftaran-'.$pendaftaran->id.'.pdf');
});

test('admin download returns not found for missing legacy documents', function () {
    $admin = User::factory()->admin()->create();
    $legacy = Pendaftaran::factory()->create(['file_persyaratan' => null]);
    $missing = Pendaftaran::factory()->create(['file_persyaratan' => 'pendaftaran/missing.pdf']);

    $this->actingAs($admin)
        ->get(route('admin.pendaftaran.document.download', $legacy))
        ->assertNotFound();

    $this->actingAs($admin)
        ->get(route('admin.pendaftaran.document.download', $missing))
        ->assertNotFound();
});

test('only admins can download registration identity documents', function () {
    $path = 'pendaftaran/access-test.pdf';
    Storage::disk('local')->put($path, '%PDF-1.4 access test');
    $pendaftaran = Pendaftaran::factory()->create(['file_persyaratan' => $path]);
    $kader = User::factory()->kader()->create();
    $instruktur = User::factory()->instruktur()->create();

    $this->get(route('admin.pendaftaran.document.download', $pendaftaran))
        ->assertRedirect(route('login'));

    $this->actingAs($kader)
        ->get(route('admin.pendaftaran.document.download', $pendaftaran))
        ->assertForbidden();

    $this->actingAs($instruktur)
        ->get(route('admin.pendaftaran.document.download', $pendaftaran))
        ->assertForbidden();
});

test('legacy registration detail identifies missing document metadata without a download link', function () {
    $admin = User::factory()->admin()->create();
    $pendaftaran = Pendaftaran::factory()->create(['file_persyaratan' => null]);

    $this->actingAs($admin)
        ->get(route('admin.pendaftaran.show', $pendaftaran))
        ->assertOk()
        ->assertSeeText('Tidak tercatat (data lama)')
        ->assertSeeText('Dokumen tidak tersedia pada data lama')
        ->assertDontSee('admin.pendaftaran.document.download');
});

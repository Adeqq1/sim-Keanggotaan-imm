<?php

use App\Models\Pendaftaran;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
});

function validPendaftaranPayload(array $overrides = []): array
{
    $defaults = [
        'nama_lengkap' => 'Ahmad Instruktur',
        'email' => 'ahmad.instruktur@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'instruktur',
        'jenis_dokumen_identitas' => 'ktp',
        'tempat_lahir' => 'Yogyakarta',
        'tanggal_lahir' => '2000-01-01',
        'no_telp' => '08123456789',
        'alamat' => 'Jl. Contoh No. 1',
        'tahun_daftar' => 2024,
        'file_persyaratan' => UploadedFile::fake()->create('identitas.pdf', 10, 'application/pdf'),
    ];

    if (($overrides['role'] ?? $defaults['role']) === 'kader'
        && ! array_key_exists('komisariat_id', $overrides)) {
        $defaults['komisariat_id'] = array_key_first(Pendaftaran::KOMISARIAT);
    }

    return array_merge($defaults, $overrides);
}

test('public pendaftaran form shows role choices', function () {
    $response = $this->get(route('pendaftaran'));

    $response->assertOk();
    $response->assertSee('Daftar Sebagai');
    $response->assertSee('value="kader"', false);
    $response->assertSee('value="instruktur"', false);
    $response->assertSee('type="password" name="password"', false);
    $response->assertSee('type="password" name="password_confirmation"', false);
});

test('public pendaftaran stores selected role', function () {
    $password = 'password';
    $response = $this->post(route('pendaftaran.store'), validPendaftaranPayload(['password' => $password, 'password_confirmation' => $password]));

    $response->assertRedirect(route('pendaftaran.success'));

    $this->assertDatabaseHas('pendaftaran', [
        'email' => 'ahmad.instruktur@example.com',
        'role' => 'instruktur',
        'tahun_daftar' => 2024,
        'komisariat_id' => null,
        'status_validasi' => 'pending',
    ]);

    $pendaftaran = Pendaftaran::where('email', 'ahmad.instruktur@example.com')->firstOrFail();
    expect($pendaftaran->password)->not->toBe($password)
        ->and(Hash::check($password, $pendaftaran->password))->toBeTrue();
});

test('public pendaftaran requires a confirmed password', function () {
    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'email' => 'password.invalid@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('password');
    $this->assertDatabaseMissing('pendaftaran', ['email' => 'password.invalid@example.com']);
});

test('public pendaftaran requires a password', function () {
    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'email' => 'password.required@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('password');
    $this->assertDatabaseMissing('pendaftaran', ['email' => 'password.required@example.com']);
});

test('public pendaftaran enforces the default password policy', function () {
    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'email' => 'password.weak@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('password');
    $this->assertDatabaseMissing('pendaftaran', ['email' => 'password.weak@example.com']);
});

test('public pendaftaran rejects admin role', function () {
    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'nama_lengkap' => 'Calon Admin',
            'email' => 'calon.admin@example.com',
            'role' => 'admin',
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('role');

    $this->assertDatabaseMissing('pendaftaran', [
        'email' => 'calon.admin@example.com',
    ]);
});

test('public pendaftaran rejects email that already belongs to a user', function () {
    $user = User::factory()->kader()->create();

    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'nama_lengkap' => 'Email Terdaftar',
            'email' => $user->email,
            'role' => 'kader',
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('email');
});

test('public pendaftaran rejects duplicate pending registration email', function () {
    $pendaftaran = Pendaftaran::factory()->create();

    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'nama_lengkap' => 'Pendaftar Duplikat',
            'email' => $pendaftaran->email,
            'role' => 'kader',
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('email');
});

test('public pendaftaran form renders komisariat and tahun daftar fields', function () {
    $response = $this->get(route('pendaftaran'));

    $response->assertOk()
        ->assertSee('name="komisariat_id"', false)
        ->assertSee('name="tahun_daftar"', false)
        ->assertSeeText('Komisariat')
        ->assertSeeText('Tahun Daftar')
        ->assertSee('Pilih komisariat')
        ->assertSee('min="2016"', false)
        ->assertSee('max="'.now()->year.'"', false);

    foreach (Pendaftaran::KOMISARIAT as $value => $label) {
        $response->assertSee('value="'.$value.'"', false);
        $response->assertSeeText($label);
    }

    expect($response->getContent())
        ->toContain('x-data="{ role:')
        ->toContain('x-show="role === \'kader\'"')
        ->toContain('x-cloak')
        ->toContain('id="komisariat_id"')
        ->toContain('id="tahun_daftar"');
});

test('kader registrasi valid menyimpan komisariat dan tahun daftar', function () {
    $komisariatId = array_key_first(Pendaftaran::KOMISARIAT);
    $email = fake()->unique()->safeEmail();

    $response = $this->post(route('pendaftaran.store'), validPendaftaranPayload([
        'email' => $email,
        'role' => 'kader',
        'komisariat_id' => $komisariatId,
        'tahun_daftar' => 2020,
    ]));

    $response->assertRedirect(route('pendaftaran.success'));

    $this->assertDatabaseHas('pendaftaran', [
        'email' => $email,
        'role' => 'kader',
        'komisariat_id' => $komisariatId,
        'tahun_daftar' => 2020,
    ]);
});

test('batas tahun daftar 2016 dan tahun berjalan diterima', function (int $tahun) {
    $email = fake()->unique()->safeEmail();

    $response = $this->post(route('pendaftaran.store'), validPendaftaranPayload([
        'email' => $email,
        'role' => 'instruktur',
        'tahun_daftar' => $tahun,
    ]));

    $response->assertRedirect(route('pendaftaran.success'));
    $this->assertDatabaseHas('pendaftaran', [
        'email' => $email,
        'tahun_daftar' => $tahun,
    ]);
})->with([
    '2016' => 2016,
    'tahun berjalan' => now()->year,
]);

test('kader tanpa komisariat ditolak', function () {
    $email = fake()->unique()->safeEmail();

    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'email' => $email,
            'role' => 'kader',
            'komisariat_id' => '',
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('komisariat_id');
    $this->assertDatabaseMissing('pendaftaran', ['email' => $email]);
});

test('kader dengan komisariat tidak dikenal ditolak', function () {
    $email = fake()->unique()->safeEmail();

    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'email' => $email,
            'role' => 'kader',
            'komisariat_id' => 'komisariat-tidak-ada',
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('komisariat_id');
    $this->assertDatabaseMissing('pendaftaran', ['email' => $email]);
});

test('tahun daftar invalid ditolak', function (array $overrides) {
    $email = fake()->unique()->safeEmail();

    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload(array_merge([
            'email' => $email,
            'role' => 'kader',
        ], $overrides)));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('tahun_daftar');
    $this->assertDatabaseMissing('pendaftaran', ['email' => $email]);
})->with([
    'tidak dikirim' => fn () => ['tahun_daftar' => null],
    'di bawah batas' => fn () => ['tahun_daftar' => 2015],
    'masa depan' => fn () => ['tahun_daftar' => now()->year + 1],
    'bukan angka' => fn () => ['tahun_daftar' => 'dua-ribu'],
    'bukan empat digit' => fn () => ['tahun_daftar' => 24],
]);

test('instruktur tanpa komisariat valid dan menyimpan tahun daftar', function () {
    $email = fake()->unique()->safeEmail();

    $response = $this->post(route('pendaftaran.store'), validPendaftaranPayload([
        'email' => $email,
        'role' => 'instruktur',
        'tahun_daftar' => 2023,
    ]));

    $response->assertRedirect(route('pendaftaran.success'));

    $this->assertDatabaseHas('pendaftaran', [
        'email' => $email,
        'role' => 'instruktur',
        'tahun_daftar' => 2023,
        'komisariat_id' => null,
    ]);
});

test('instruktur tanpa tahun daftar ditolak', function () {
    $email = fake()->unique()->safeEmail();

    $response = $this->from(route('pendaftaran'))
        ->post(route('pendaftaran.store'), validPendaftaranPayload([
            'email' => $email,
            'role' => 'instruktur',
            'tahun_daftar' => null,
        ]));

    $response->assertRedirect(route('pendaftaran'));
    $response->assertSessionHasErrors('tahun_daftar');
    $this->assertDatabaseMissing('pendaftaran', ['email' => $email]);
});

test('komisariat yang dikirim paksa oleh instruktur tidak tersimpan', function () {
    $komisariatId = array_key_first(Pendaftaran::KOMISARIAT);
    $email = fake()->unique()->safeEmail();

    $response = $this->post(route('pendaftaran.store'), validPendaftaranPayload([
        'email' => $email,
        'role' => 'instruktur',
        'komisariat_id' => $komisariatId,
        'tahun_daftar' => 2022,
    ]));

    $response->assertRedirect(route('pendaftaran.success'));

    $this->assertDatabaseHas('pendaftaran', [
        'email' => $email,
        'role' => 'instruktur',
        'tahun_daftar' => 2022,
        'komisariat_id' => null,
    ]);
});

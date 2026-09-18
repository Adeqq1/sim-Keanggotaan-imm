<?php

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
use App\Models\Pendaftaran;
use App\Models\User;
use App\Support\SortParams;
use Illuminate\Http\Request;

test('sort params only accepts allowlisted values and safe directions', function () {
    $request = Request::create('/admin/anggota', 'GET', [
        'sort' => 'nama_lengkap,desc',
        'direction' => 'drop table',
    ]);

    expect(SortParams::resolve($request, ['nama', 'created'], 'created'))->toBe([
        'key' => 'created',
        'direction' => 'desc',
    ]);
});

test('admin anggota sorting preserves filters and pagination query strings', function () {
    $admin = User::factory()->admin()->create();
    Anggota::factory()->create(['nama_lengkap' => 'Zeta']);
    Anggota::factory()->create(['nama_lengkap' => 'Alpha']);

    $response = $this->actingAs($admin)->get(route('admin.anggota.index', [
        'search' => 'a', 'role' => 'kader', 'sort' => 'nama', 'direction' => 'asc',
    ]));

    $response->assertSuccessful()->assertSeeInOrder(['Alpha', 'Zeta']);
    $response->assertSee('name="search" value="a"', false)
        ->assertSee('name="role"', false)->assertSee('value="kader"', false)
        ->assertSee('name="sort"', false)->assertSee('value="nama"', false)
        ->assertSee('name="direction"', false)->assertSee('value="asc"', false);
});

test('admin anggota dapat mengurutkan berdasarkan komisariat', function () {
    $admin = User::factory()->admin()->create();
    Anggota::factory()->create(['nama_lengkap' => 'Tanpa Komisariat', 'komisariat_id' => null]);
    Anggota::factory()->create(['nama_lengkap' => 'Ahmad Komisariat', 'komisariat_id' => 'ahmad-dahlan']);
    Anggota::factory()->create(['nama_lengkap' => 'Buya Komisariat', 'komisariat_id' => 'buya-hamka']);

    $this->actingAs($admin)
        ->get(route('admin.anggota.index', ['sort' => 'komisariat', 'direction' => 'asc']))
        ->assertSuccessful()
        ->assertSee('<option value="komisariat" selected>Komisariat</option>', false)
        ->assertSeeInOrder(['Tanpa Komisariat', 'Ahmad Komisariat', 'Buya Komisariat']);

    $this->actingAs($admin)
        ->get(route('admin.anggota.index', ['sort' => 'komisariat', 'direction' => 'desc']))
        ->assertSuccessful()
        ->assertSeeInOrder(['Buya Komisariat', 'Ahmad Komisariat', 'Tanpa Komisariat']);
});

test('member and archive filters preserve the selected sorting', function () {
    $admin = User::factory()->admin()->create();
    Anggota::factory()->create(['nama_lengkap' => 'Zeta Filter']);
    Anggota::factory()->create(['nama_lengkap' => 'Alpha Filter']);

    $this->actingAs($admin)->get(route('admin.anggota.index', [
        'sort' => 'nama', 'direction' => 'asc',
    ]))->assertSee('name="sort" value="nama"', false)
        ->assertSee('name="direction" value="asc"', false);

    $member = Anggota::factory()->create();
    Arsip::factory()->create(['anggota_id' => $member->id, 'judul_dokumen' => 'Zeta Archive']);
    Arsip::factory()->create(['anggota_id' => $member->id, 'judul_dokumen' => 'Alpha Archive']);

    $this->actingAs($admin)->get(route('admin.arsip.index', [
        'q' => 'Archive', 'sort' => 'judul', 'direction' => 'asc',
    ]))->assertSeeInOrder(['Alpha Archive', 'Zeta Archive'])
        ->assertSee('name="sort" value="judul"', false)
        ->assertSee('name="direction" value="asc"', false);

    $this->actingAs($member->user)->get(route('kader.arsip.index', [
        'q' => 'Archive', 'sort' => 'judul', 'direction' => 'asc',
    ]))->assertSee('name="sort" value="judul"', false)
        ->assertSee('name="direction" value="asc"', false);
});

test('list sorting controls auto-submit without a manual button', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.anggota.index'))
        ->assertSuccessful()
        ->assertSee('data-auto-submit-sort', false)
        ->assertDontSee('>Terapkan<', false);

    $script = file_get_contents(resource_path('js/app.js'));

    expect($script)->toContain("[data-auto-submit-sort]")
        ->toContain('select[name="sort"]')
        ->toContain('select[name="direction"]')
        ->toContain('form.requestSubmit()');
});

test('kader archive sorting remains owned by the authenticated member', function () {
    $member = Anggota::factory()->create();
    $other = Anggota::factory()->create();
    Arsip::factory()->create(['anggota_id' => $member->id, 'judul_dokumen' => 'A Own']);
    Arsip::factory()->create(['anggota_id' => $other->id, 'judul_dokumen' => 'Z Other']);

    $response = $this->actingAs($member->user)->get(route('kader.arsip.index', [
        'sort' => 'judul', 'direction' => 'asc',
    ]));

    $response->assertSuccessful()->assertSee('A Own')->assertDontSee('Z Other');
});

test('report exports accept sorting fields', function () {
    $admin = User::factory()->admin()->create();
    Kegiatan::factory()->create(['nama_kegiatan' => 'Zeta Export']);
    Kegiatan::factory()->create(['nama_kegiatan' => 'Alpha Export']);

    $this->actingAs($admin)->post(route('admin.laporan.exportExcel'), [
        'jenis_laporan' => 'kegiatan',
        'tanggal_mulai' => now()->subMonth()->toDateString(),
        'tanggal_selesai' => now()->addMonth()->toDateString(),
        'sort' => 'nama',
        'direction' => 'asc',
    ])->assertSuccessful();
});

test('all report types use the generic export sorting keys', function (string $jenis, string $first, string $last) {
    $admin = User::factory()->admin()->create();
    $controller = app(\App\Http\Controllers\LaporanController::class);
    $method = new ReflectionMethod($controller, 'getData');
    $method->setAccessible(true);
    $mulai = now()->subDay()->toDateString();
    $selesai = now()->addDay()->toDateString();

    match ($jenis) {
        'kegiatan' => [
            Kegiatan::factory()->create(['nama_kegiatan' => 'Zeta Report', 'tanggal_waktu' => now()]),
            Kegiatan::factory()->create(['nama_kegiatan' => 'Alpha Report', 'tanggal_waktu' => now()]),
        ],
        'anggota' => [
            Anggota::factory()->create(['nama_lengkap' => 'Zeta Report']),
            Anggota::factory()->create(['nama_lengkap' => 'Alpha Report']),
        ],
        'pendaftaran' => [
            Pendaftaran::factory()->create(['nama_lengkap' => 'Zeta Report']),
            Pendaftaran::factory()->create(['nama_lengkap' => 'Alpha Report']),
        ],
        'arsip' => [
            Arsip::factory()->create(['judul_dokumen' => 'Zeta Report']),
            Arsip::factory()->create(['judul_dokumen' => 'Alpha Report']),
        ],
    };

    $ascending = $method->invoke($controller, $jenis, $mulai, $selesai, ['key' => 'nama', 'direction' => 'asc']);
    $descending = $method->invoke($controller, $jenis, $mulai, $selesai, ['key' => 'nama', 'direction' => 'desc']);

    expect($ascending->first()->{match ($jenis) {
        'arsip' => 'judul_dokumen',
        default => 'nama_'.($jenis === 'kegiatan' ? 'kegiatan' : 'lengkap'),
    }})->toBe($first)
        ->and($descending->first()->{match ($jenis) {
            'arsip' => 'judul_dokumen',
            default => 'nama_'.($jenis === 'kegiatan' ? 'kegiatan' : 'lengkap'),
        }})->toBe($last);
})->with([
    ['kegiatan', 'Alpha Report', 'Zeta Report'],
    ['anggota', 'Alpha Report', 'Zeta Report'],
    ['pendaftaran', 'Alpha Report', 'Zeta Report'],
    ['arsip', 'Alpha Report', 'Zeta Report'],
]);

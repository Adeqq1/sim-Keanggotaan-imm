<?php

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Kegiatan;
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

test('list sorting controls auto-submit without a manual button', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.anggota.index'))
        ->assertSuccessful()
        ->assertSee('data-auto-submit-sort', false)
        ->assertDontSee('>Terapkan<', false);
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

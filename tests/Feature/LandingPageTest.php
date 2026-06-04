<?php

use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

test('landing page returns 200', function () {
    $this->get('/')->assertOk();
});

test('landing page contains IMM branding', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Ikatan Mahasiswa Muhammadiyah')
        ->assertSee('Daftar Anggota')
        ->assertSee('Visi');
});

test('landing page has correct route name', function () {
    expect(route('landing'))->toBe(url('/'));
});

test('landing page displays 3 latest activities', function () {
    // Seed 5 activities
    $activities = Kegiatan::factory()->count(5)->create();
    $latest = Kegiatan::latest()->take(3)->get();

    $response = $this->get('/');

    $response->assertOk();
    foreach ($latest as $activity) {
        $response->assertSee($activity->nama_kegiatan);
    }

    // Verify older activities (not in top 3 latest) are not shown on landing page
    $older = Kegiatan::whereNotIn('id', $latest->pluck('id'))->get();
    foreach ($older as $activity) {
        $response->assertDontSee($activity->nama_kegiatan);
    }
});

test('activity detail page displays correctly with recommendations', function () {
    // Seed 5 activities
    $activities = Kegiatan::factory()->count(5)->create();
    $targetActivity = $activities->first();

    $response = $this->get(route('kegiatan.show', $targetActivity));

    $response->assertOk()
        ->assertSee($targetActivity->nama_kegiatan)
        ->assertSee($targetActivity->lokasi);

    // Verify recommendations are present and do not include the target activity itself in recommendations list
    $recommendations = Kegiatan::where('id', '!=', $targetActivity->id)
        ->latest()
        ->take(3)
        ->get();

    foreach ($recommendations as $rec) {
        $response->assertSee($rec->nama_kegiatan);
    }
});

test('activity detail page returns 404 for non existent activity', function () {
    $this->get('/kegiatan/999999')->assertNotFound();
});

test('landing page activities are cached', function () {
    // Clear cache
    Cache::forget('kegiatan.terbaru');

    // Seed activities
    Kegiatan::factory()->count(3)->create();

    expect(Cache::has('kegiatan.terbaru'))->toBeFalse();

    $this->get('/');

    expect(Cache::has('kegiatan.terbaru'))->toBeTrue();
    $cached = Cache::get('kegiatan.terbaru');
    expect($cached)->toHaveCount(3);
});

test('activities cache is cleared when activities are added, updated, or deleted', function () {
    $instruktur = User::factory()->instruktur()->create();

    // Cache is active
    Cache::remember('kegiatan.terbaru', 3600, function () {
        return Kegiatan::latest()->take(3)->get()->toArray();
    });
    expect(Cache::has('kegiatan.terbaru'))->toBeTrue();

    // 1. Create activity clears cache
    $this->actingAs($instruktur)->post(route('admin.kegiatan.store'), [
        'nama_kegiatan' => 'Latihan Kader Baru',
        'deskripsi' => 'Deskripsi baru',
        'tanggal_waktu' => '2026-06-15 10:00:00',
        'lokasi' => 'Aula IMM',
    ]);
    expect(Cache::has('kegiatan.terbaru'))->toBeFalse();

    // Re-cache
    Cache::remember('kegiatan.terbaru', 3600, function () {
        return Kegiatan::latest()->take(3)->get()->toArray();
    });
    expect(Cache::has('kegiatan.terbaru'))->toBeTrue();

    $kegiatan = Kegiatan::where('nama_kegiatan', 'Latihan Kader Baru')->first();

    // 2. Update activity clears cache
    $this->actingAs($instruktur)->put(route('admin.kegiatan.update', $kegiatan), [
        'nama_kegiatan' => 'Latihan Kader Updated',
        'tanggal_waktu' => '2026-06-15 10:00:00',
        'lokasi' => 'Aula IMM Baru',
    ]);
    expect(Cache::has('kegiatan.terbaru'))->toBeFalse();

    // Re-cache
    Cache::remember('kegiatan.terbaru', 3600, function () {
        return Kegiatan::latest()->take(3)->get()->toArray();
    });
    expect(Cache::has('kegiatan.terbaru'))->toBeTrue();

    // 3. Destroy activity clears cache
    $this->actingAs($instruktur)->delete(route('admin.kegiatan.destroy', $kegiatan));
    expect(Cache::has('kegiatan.terbaru'))->toBeFalse();
});

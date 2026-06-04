<?php

use App\Models\Kegiatan;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

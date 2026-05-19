<?php

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

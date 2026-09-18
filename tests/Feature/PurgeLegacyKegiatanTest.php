<?php

use App\Models\Kegiatan;

test('purge legacy kegiatan preserves activities with target years', function () {
    $legacy = Kegiatan::factory()->create();
    $legacy->tahunAngkatans()->delete();
    $current = Kegiatan::factory()->create();

    $this->artisan('kegiatan:purge-legacy', ['--yes' => true])
        ->assertSuccessful();

    expect(Kegiatan::find($legacy->id))->toBeNull()
        ->and(Kegiatan::find($current->id))->not->toBeNull();
});

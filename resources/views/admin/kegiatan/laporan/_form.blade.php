<div class="alert alert-light border small">Nama, tanggal, lokasi, dan jumlah peserta dibaca langsung dari data kegiatan dan presensi.</div>

@foreach([
    'tujuan' => ['Tujuan', true],
    'ringkasan' => ['Ringkasan Pelaksanaan', true],
    'agenda' => ['Agenda', true],
    'narasumber' => ['Narasumber/Instruktur', false],
    'hasil' => ['Hasil', true],
    'kendala' => ['Kendala', false],
    'tindak_lanjut' => ['Tindak Lanjut', false],
] as $field => [$label, $required])
    <div class="mb-3">
        <label for="{{ $field }}" class="form-label fw-bold">{{ $label }}{{ $required ? ' *' : '' }}</label>
        <textarea id="{{ $field }}" name="{{ $field }}" rows="4" class="form-control @error($field) is-invalid @enderror" {{ $required ? 'required' : '' }}>{{ old($field, $laporanKegiatan?->{$field}) }}</textarea>
        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
@endforeach

<div class="mb-4">
    <label for="file_lampiran" class="form-label fw-bold">Lampiran</label>
    <input id="file_lampiran" name="file_lampiran" type="file" class="form-control @error('file_lampiran') is-invalid @enderror" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png">
    <div class="form-text">Opsional. PDF, dokumen Office, JPG, atau PNG; maksimal 2 MiB.</div>
    @if($laporanKegiatan?->file_lampiran)<div class="form-text">Lampiran lama tetap digunakan bila tidak memilih file baru.</div>@endif
    @error('file_lampiran')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="d-flex flex-wrap gap-2">
    <button type="submit" class="btn btn-primary btn-ui"><i class="bi bi-save me-1"></i>Simpan Laporan</button>
    <a href="{{ route('admin.laporan-kegiatan.index') }}" class="btn btn-outline-secondary btn-ui">Batal</a>
</div>

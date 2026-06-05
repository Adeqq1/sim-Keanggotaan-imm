<x-app-layout>
    <x-slot name="header">
        Generate Sertifikat
    </x-slot>

    <div class="card p-4">
        <h6 class="fw-bold mb-4 border-bottom pb-2">Pengaturan Sertifikat</h6>
        <form action="{{ route('admin.sertifikat.generate') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Pilih Kegiatan</label>
                <select name="kegiatan_id" class="form-select @error('kegiatan_id') is-invalid @enderror" required>
                    <option value="" disabled selected>Pilih kegiatan...</option>
                    @foreach($kegiatans as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kegiatan }} ({{ $k->tanggal_waktu->format('d/m/Y') }})</option>
                    @endforeach
                </select>
                @error('kegiatan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold d-block mb-3">Pilih Anggota (Multiple)</label>
                <div class="list-group shadow-sm border-0" style="max-height: 300px; overflow-y: auto; border-radius: 10px;">
                    @foreach($anggotas as $anggota)
                        <label class="list-group-item d-flex align-items-center py-3">
                            <input class="form-check-input me-3" type="checkbox" name="anggota_ids[]" value="{{ $anggota->id }}">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary me-2 small fw-bold" style="width: 30px; height: 30px;">
                                    {{ substr($anggota->nama_lengkap, 0, 1) }}
                                </div>
                                <span class="small fw-bold">{{ $anggota->nama_lengkap }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('anggota_ids') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary py-3 fw-bold">
                    <i class="bi bi-gear-wide-connected me-2"></i> Generate Sekarang
                </button>
                <a href="{{ route('admin.sertifikat.index') }}" class="btn btn-link text-muted small mt-2">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>

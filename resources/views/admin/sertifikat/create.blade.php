<x-app-layout>
    <x-slot name="header">
        Buat Sertifikat
    </x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.sertifikat.index') }}" class="btn btn-outline-secondary btn-ui btn-ui-sm">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <div class="mb-4">
                <h2 class="h6 fw-bold mb-1">Detail Sertifikat</h2>
                <p class="small text-muted mb-0">Pilih kegiatan dan anggota yang akan menerima sertifikat.</p>
            </div>

            <form action="{{ route('admin.sertifikat.create') }}" method="GET">
                <div class="mb-4">
                    <label for="kegiatan_id" class="form-label fw-bold">
                        Kegiatan <span class="text-danger" aria-hidden="true">*</span>
                    </label>
                    <select id="kegiatan_id" class="form-select @error('kegiatan_id') is-invalid @enderror" onchange="this.form.submit()" required>
                        <option value="">Pilih kegiatan</option>
                        @foreach($kegiatans as $kegiatan)
                            <option value="{{ $kegiatan->id }}" @selected((string) $selectedKegiatanId === (string) $kegiatan->id)>
                                {{ $kegiatan->nama_kegiatan }} ({{ $kegiatan->tanggal_waktu->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('kegiatan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

            </form>

            @if($selectedKegiatan)
                <form action="{{ route('admin.sertifikat.generate') }}" method="POST">
                    @csrf
                    <input type="hidden" name="kegiatan_id" value="{{ $selectedKegiatanId }}">

                @php
                    $selectedAnggotaIds = array_map('intval', (array) old('anggota_ids', []));
                    $anggotaError = $errors->first('anggota_ids') ?: $errors->first('anggota_ids.*');
                @endphp

                <fieldset class="border-0 p-0 m-0 mb-4" aria-describedby="anggota-help{{ $anggotaError ? ' anggota-error' : '' }}">
                    <legend class="form-label fw-bold mb-1">
                        Anggota <span class="text-danger" aria-hidden="true">*</span>
                    </legend>
                    <p id="anggota-help" class="small text-muted mb-3">
                        {{ $anggotas->count() }} anggota memenuhi syarat dan belum memiliki sertifikat untuk kegiatan ini.
                    </p>

                    <div class="list-group certificate-member-list {{ $anggotaError ? 'border border-danger' : '' }}">
                        @forelse($anggotas as $anggota)
                            <label for="anggota-{{ $anggota->id }}" class="list-group-item d-flex align-items-start gap-3 py-3 certificate-member-option">
                                <input id="anggota-{{ $anggota->id }}" class="form-check-input flex-shrink-0 mt-1" type="checkbox" name="anggota_ids[]" value="{{ $anggota->id }}" @checked(in_array($anggota->id, $selectedAnggotaIds, true))>
                                <span class="min-w-0">
                                    <span class="d-block fw-semibold text-break">{{ $anggota->nama_lengkap }}</span>
                                    @if($anggota->nia)
                                        <span class="d-block small text-muted">NIA {{ $anggota->nia }}</span>
                                    @endif
                                </span>
                            </label>
                        @empty
                            <p class="text-muted text-center py-4 mb-0">Tidak ada anggota yang memenuhi syarat untuk kegiatan ini.</p>
                        @endforelse
                    </div>

                    @if($anggotaError)
                        <div id="anggota-error" class="invalid-feedback d-block">{{ $anggotaError }}</div>
                    @endif
                </fieldset>

                <div class="d-grid d-sm-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-ui" @disabled($kegiatans->isEmpty() || $anggotas->isEmpty())>
                        <i class="bi bi-file-earmark-check" aria-hidden="true"></i> Buat Sertifikat
                    </button>
                    <a href="{{ route('admin.sertifikat.index') }}" class="btn btn-outline-secondary btn-ui">Batal</a>
                </div>
            </form>
            @else
                <div class="border rounded p-4 text-center text-muted small">
                    <i class="bi bi-arrow-up-circle d-block fs-4 mb-2" aria-hidden="true"></i>
                    Pilih kegiatan untuk menampilkan anggota yang memenuhi syarat.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        Kelola Presensi
    </x-slot>

    <div class="card p-3 mb-4 border-start border-primary border-4">
        <h6 class="fw-bold mb-1 text-primary">{{ $kegiatan->nama_kegiatan }}</h6>
        <p class="text-muted small mb-0"><i class="bi bi-calendar-event me-1"></i> {{ $kegiatan->tanggal_waktu->format('d F Y, H:i') }}</p>
    </div>

    <form action="{{ route('admin.presensi.update', $kegiatan) }}" method="POST">
        @csrf
        @method('PUT')

        @foreach($anggotas as $anggota)
            @php
                $presensi = $presensis->where('anggota_id', $anggota->id)->first();
                $status = $presensi ? $presensi->status_kehadiran : 'alfa';
            @endphp
            <div class="card mb-2 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary small fw-bold" style="width: 35px; height: 35px;">
                                {{ substr($anggota->nama_lengkap, 0, 1) }}
                            </div>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">{{ $anggota->nama_lengkap }}</h6>
                            <small class="text-muted" style="font-size: 0.75rem;">NIA: {{ $anggota->nia ?? '-' }}</small>
                        </div>
                    </div>
                    
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="presensi[{{ $anggota->id }}]" id="hadir{{ $anggota->id }}" value="hadir" {{ $status === 'hadir' ? 'checked' : '' }}>
                        <label class="btn btn-outline-success px-2" for="hadir{{ $anggota->id }}">H</label>

                        <input type="radio" class="btn-check" name="presensi[{{ $anggota->id }}]" id="izin{{ $anggota->id }}" value="izin" {{ $status === 'izin' ? 'checked' : '' }}>
                        <label class="btn btn-outline-warning px-2" for="izin{{ $anggota->id }}">I</label>

                        <input type="radio" class="btn-check" name="presensi[{{ $anggota->id }}]" id="alfa{{ $anggota->id }}" value="alfa" {{ $status === 'alfa' ? 'checked' : '' }}>
                        <label class="btn btn-outline-danger px-2" for="alfa{{ $anggota->id }}">A</label>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-grid gap-2 mt-4 mb-5">
            <button type="submit" class="btn btn-primary py-3 fw-bold shadow-sm">
                <i class="bi bi-save me-2"></i> Simpan Presensi
            </button>
            <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-link text-muted text-decoration-none">Kembali</a>
        </div>
    </form>
</x-app-layout>

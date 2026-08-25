<x-app-layout>
    <x-slot name="header">
        {{ $canManagePresensi ? 'Kelola Presensi' : 'Lihat Presensi' }}
    </x-slot>

    @if(auth()->user()->role === 'admin')
        <x-kegiatan-submenu />
    @endif

    <div class="card p-3 mb-4 border-start border-primary border-4">
        <h6 class="fw-bold mb-1 text-primary">{{ $kegiatan->nama_kegiatan }}</h6>
        <p class="text-muted small mb-0"><i class="bi bi-calendar-event me-1"></i> Sesi {{ $sesiKegiatan->urutan }}: {{ $sesiKegiatan->nama_sesi }} · {{ $sesiKegiatan->mulai_pada->translatedFormat('d F Y, H:i') }}</p>
        <p class="text-muted small mb-0"><i class="bi bi-people me-1"></i> Target angkatan: {{ $kegiatan->tahunAngkatans->pluck('tahun_daftar')->implode(', ') ?: 'Belum ditentukan' }}</p>
    </div>
    <x-sort-control :action="route('admin.presensi.sesi.show', [$kegiatan, $sesiKegiatan])" :options="$options" :selected-sort="$sort['key']" :selected-direction="$sort['direction']" />

    @if($canManagePresensi)
        <form action="{{ route('admin.presensi.store', [$kegiatan, $sesiKegiatan]) }}" method="POST">
            @csrf
    @endif

    @foreach($anggotas as $anggota)
        @php
            $presensi = $presensis->firstWhere('anggota_id', $anggota->id);
            $status = $presensi?->status_kehadiran;
            $formStatus = $status ?? 'alfa';
        @endphp
        <div class="card mb-2 p-3">
            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
                <div class="d-flex align-items-center min-w-0">
                    <div class="me-3">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-primary small fw-bold" style="width: 35px; height: 35px;">
                            {{ substr($anggota->nama_lengkap, 0, 1) }}
                        </div>
                    </div>
                    <div class="min-w-0">
                        <h6 class="fw-bold mb-0 text-break" style="font-size: 0.9rem;">{{ $anggota->nama_lengkap }}</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">NIA: {{ $anggota->nia ?? '-' }}</small>
                    </div>
                </div>

                @if($canManagePresensi)
                    <input type="hidden" name="presensi[{{ $anggota->id }}][anggota_id]" value="{{ $anggota->id }}">
                    <div class="btn-group btn-group-sm presensi-control align-self-end align-self-sm-auto" role="group" aria-label="Status kehadiran {{ $anggota->nama_lengkap }}">
                        <input type="radio" class="btn-check" name="presensi[{{ $anggota->id }}][status_kehadiran]" id="hadir{{ $anggota->id }}" value="hadir" {{ $formStatus === 'hadir' ? 'checked' : '' }}>
                        <label class="btn btn-outline-success px-2" for="hadir{{ $anggota->id }}" title="Hadir"><span aria-hidden="true">H</span><span class="visually-hidden">Hadir</span></label>

                        <input type="radio" class="btn-check" name="presensi[{{ $anggota->id }}][status_kehadiran]" id="izin{{ $anggota->id }}" value="izin" {{ $formStatus === 'izin' ? 'checked' : '' }}>
                        <label class="btn btn-outline-warning px-2" for="izin{{ $anggota->id }}" title="Izin"><span aria-hidden="true">I</span><span class="visually-hidden">Izin</span></label>

                        <input type="radio" class="btn-check" name="presensi[{{ $anggota->id }}][status_kehadiran]" id="alfa{{ $anggota->id }}" value="alfa" {{ $formStatus === 'alfa' ? 'checked' : '' }}>
                        <label class="btn btn-outline-danger px-2" for="alfa{{ $anggota->id }}" title="Alfa"><span aria-hidden="true">A</span><span class="visually-hidden">Alfa</span></label>
                    </div>
                @else
                    @if($status === null)
                        <span class="badge bg-secondary rounded-pill px-3">Belum dicatat</span>
                    @else
                        <span class="badge {{ $status === 'hadir' ? 'bg-success' : ($status === 'izin' ? 'bg-warning text-dark' : 'bg-danger') }} rounded-pill px-3">
                            {{ ucfirst($status) }}{{ $presensi->status_verifikasi !== 'pending' ? ' · '.ucfirst($presensi->status_verifikasi) : '' }}
                        </span>
                    @endif
                @endif
            </div>
        </div>
    @endforeach

    <div class="d-grid gap-2 mt-4 mb-5">
        @if($canManagePresensi)
            <button type="submit" class="btn btn-primary btn-ui py-3 shadow-sm">
                <i class="bi bi-save me-2"></i> Simpan Presensi
            </button>
        @endif
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary btn-ui">Kembali</a>
    </div>

    @if($canManagePresensi)
        </form>
        <h6 class="fw-bold mt-4">Verifikasi Kehadiran</h6>
        @foreach($presensis->where('status_kehadiran', 'hadir') as $presensi)
            <div class="card p-3 mb-2">
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <span>{{ $presensi->anggota->nama_lengkap }}: {{ ucfirst($presensi->status_verifikasi) }}</span>
                    <form method="POST" action="{{ route('admin.presensi.verifikasi.update', [$kegiatan, $sesiKegiatan, $presensi]) }}" class="d-flex gap-2">
                        @csrf @method('PATCH')
                        <select name="status_verifikasi" class="form-select form-select-sm" aria-label="Keputusan verifikasi {{ $presensi->anggota->nama_lengkap }}">
                            @foreach(['pending', 'terverifikasi', 'ditolak'] as $verification)<option value="{{ $verification }}" @selected($presensi->status_verifikasi === $verification)>{{ ucfirst($verification) }}</option>@endforeach
                        </select>
                        <button class="btn btn-sm btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        @endforeach
    @endif
</x-app-layout>

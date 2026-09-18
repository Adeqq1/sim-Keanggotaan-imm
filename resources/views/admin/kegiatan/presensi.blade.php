<x-app-layout>
    <x-slot name="header">
        {{ $canManagePresensi ? 'Kelola Presensi' : 'Lihat Presensi' }}
    </x-slot>

    @if(auth()->user()->role === 'admin')
        <x-kegiatan-submenu />
    @endif

    <section class="attendance-hero">
        <div>
            <span class="attendance-eyebrow">Presensi kegiatan</span>
            <h2>{{ $kegiatan->nama_kegiatan }}</h2>
            <p><i class="bi bi-calendar-event me-1"></i>{{ $sesiKegiatan->nama_sesi }} · {{ $sesiKegiatan->mulai_pada->translatedFormat('d F Y, H:i') }} WIB</p>
            <p><i class="bi bi-people me-1"></i>Target angkatan: {{ $kegiatan->tahunAngkatans->pluck('tahun_daftar')->implode(', ') ?: 'Belum ditentukan' }}</p>
        </div>
        <div class="attendance-summary-grid">
            @foreach([['total', 'Kader', 'primary'], ['hadir', 'Hadir', 'success'], ['pending', 'Menunggu', 'warning'], ['terverifikasi', 'Terverifikasi', 'info']] as [$key, $label, $color])
                <div class="attendance-summary-item"><strong class="text-{{ $color }}">{{ $presensiStats[$key] }}</strong><span>{{ $label }}</span></div>
            @endforeach
        </div>
    </section>

    <form method="GET" action="{{ route('admin.presensi.sesi.show', [$kegiatan, $sesiKegiatan]) }}" class="attendance-toolbar" data-auto-submit-sort>
        <div class="attendance-sort-field">
            <label for="attendance-sort">Urutkan berdasarkan</label>
            <select id="attendance-sort" name="sort" class="form-select">
                @foreach($options as $key => $label)
                    <option value="{{ $key }}" @selected($sort['key'] === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="attendance-sort-field">
            <label for="attendance-direction">Arah</label>
            <select id="attendance-direction" name="direction" class="form-select">
                <option value="asc" @selected($sort['direction'] === 'asc')>Terlama</option>
                <option value="desc" @selected($sort['direction'] === 'desc')>Terbaru</option>
            </select>
        </div>
    </form>

    @if($canManagePresensi)
        <form action="{{ route('admin.presensi.store', [$kegiatan, $sesiKegiatan]) }}" method="POST">
            @csrf
    @endif

    <section class="attendance-section">
        <div class="attendance-section-heading"><div><span class="attendance-section-kicker">Pencatatan</span><h3>Daftar Peserta</h3></div><span class="badge bg-light text-dark">{{ $presensiStats['total'] }} kader</span></div>
        @forelse($anggotas as $anggota)
            @php($presensi = $presensiByAnggota->get($anggota->id))
            @php($status = $presensi?->status_kehadiran)
            @php($formStatus = $status ?? 'alfa')
            <div class="attendance-member-row">
                <div class="attendance-member-identity"><span class="attendance-member-avatar">{{ mb_substr($anggota->nama_lengkap, 0, 1) }}</span><div><strong>{{ $anggota->nama_lengkap }}</strong><small>NIA: {{ $anggota->nia ?? '-' }} · Angkatan {{ $anggota->tahun_daftar ?? '-' }}</small></div></div>
                @if($canManagePresensi)
                    <input type="hidden" name="presensi[{{ $anggota->id }}][anggota_id]" value="{{ $anggota->id }}">
                    <div class="attendance-status-control" role="group" aria-label="Status kehadiran {{ $anggota->nama_lengkap }}">
                        @foreach(['hadir' => 'Hadir', 'izin' => 'Izin', 'alfa' => 'Alfa'] as $value => $label)
                            <input type="radio" class="btn-check" name="presensi[{{ $anggota->id }}][status_kehadiran]" id="{{ $value }}{{ $anggota->id }}" value="{{ $value }}" @checked($formStatus === $value)>
                            <label class="attendance-status attendance-status--{{ $value }}" for="{{ $value }}{{ $anggota->id }}">{{ $label }}</label>
                        @endforeach
                    </div>
                @else
                    <span class="attendance-status-badge attendance-status-badge--{{ $status ?? 'empty' }}">{{ $status ? ucfirst($status) : 'Belum dicatat' }}</span>
                @endif
            </div>
        @empty
            <div class="attendance-empty">Tidak ada kader aktif dari target angkatan kegiatan.</div>
        @endforelse
    </section>

    <div class="attendance-actions">
        @if($canManagePresensi)<button type="submit" class="btn btn-primary btn-ui btn-ui-sm"><i class="bi bi-save"></i>Simpan Presensi</button>@endif
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary btn-ui btn-ui-sm">Kembali</a>
    </div>

    @if($canManagePresensi)
        </form>
        <section class="attendance-verification-section">
            <div class="attendance-section-heading"><div><span class="attendance-section-kicker">Tindak lanjut</span><h3>Verifikasi Kehadiran</h3></div><span class="badge bg-warning text-dark">{{ $presensiStats['pending'] }} menunggu</span></div>

            @foreach([['pendingPresensis', 'Perlu Diverifikasi', 'warning', 'Belum ada kehadiran yang menunggu verifikasi.'], ['processedPresensis', 'Sudah Diproses', 'success', 'Belum ada kehadiran yang sudah diproses.'], ['rejectedPresensis', 'Ditolak', 'danger', 'Tidak ada kehadiran yang ditolak.']] as [$collection, $title, $color, $empty])
                <div class="verification-group verification-group--{{ $color }}">
                    <div class="verification-group-heading"><h4>{{ $title }}</h4><span>{{ ${$collection}->count() }}</span></div>
                    @forelse(${$collection} as $presensi)
                        <div class="verification-row"><div><strong>{{ $presensi->anggota->nama_lengkap }}</strong><small>{{ $presensi->status_verifikasi === 'legacy' ? 'Terverifikasi (data lama)' : ucfirst($presensi->status_verifikasi) }}@if($presensi->pemeriksa) · oleh {{ $presensi->pemeriksa->name }}@endif</small></div>
                            @if($collection === 'pendingPresensis')
                                <div class="verification-actions"><form method="POST" action="{{ route('admin.presensi.verifikasi.update', [$kegiatan, $sesiKegiatan, $presensi]) }}">@csrf @method('PATCH')<input type="hidden" name="status_verifikasi" value="terverifikasi"><button class="btn btn-sm btn-success btn-ui" type="submit">Verifikasi</button></form><form method="POST" action="{{ route('admin.presensi.verifikasi.update', [$kegiatan, $sesiKegiatan, $presensi]) }}">@csrf @method('PATCH')<input type="hidden" name="status_verifikasi" value="ditolak"><button class="btn btn-sm btn-outline-danger btn-ui" type="submit">Tolak</button></form></div>
                            @endif
                        </div>
                    @empty
                        <p class="verification-empty">{{ $empty }}</p>
                    @endforelse
                </div>
            @endforeach
        </section>
    @endif
</x-app-layout>

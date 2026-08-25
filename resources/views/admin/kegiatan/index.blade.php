<x-app-layout>
    <x-slot name="header">
        Manajemen Kegiatan
    </x-slot>

    @if(auth()->user()->role === 'admin')
        <x-kegiatan-submenu />
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h6 class="fw-bold mb-1 desktop-section-heading"><i class="bi bi-list-ul"></i> Daftar Kegiatan</h6>
            <p class="text-muted small mb-0">Kegiatan dipisahkan berdasarkan waktu pelaksanaan.</p>
        </div>
        <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-primary btn-ui btn-ui-sm">
            <i class="bi bi-plus-lg"></i> Tambah
        </a>
    </div>
    <x-sort-control :action="route('admin.kegiatan.index')" :options="$options" :selected-sort="$sort['key']" :selected-direction="$sort['direction']" />

    @php
        $renderKegiatanCard = function ($kegiatan, string $statusLabel, string $statusClass) {
    @endphp
        <div class="col-12 col-sm-6">
        <div class="card h-100 p-3 index-card d-flex flex-column desktop-kegiatan-index-card activity-status-card activity-status-card--{{ $statusClass }}">
            <div class="d-flex align-items-center">
                <div class="bg-light rounded p-2 me-3 text-center" style="min-width: 55px;">
                    <span class="d-block fw-bold text-primary fs-5">{{ $kegiatan->tanggal_waktu->format('d') }}</span>
                    <span class="small text-muted text-uppercase" style="font-size: 0.65rem;">{{ $kegiatan->tanggal_waktu->translatedFormat('M Y') }}</span>
                </div>
                <img src="{{ $kegiatan->thumbnail_url }}" alt="Gambar mini {{ $kegiatan->nama_kegiatan }}" class="rounded me-3 flex-shrink-0" style="width: 55px; height: 55px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/placeholder-kegiatan.png') }}';">
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap"><h6 class="fw-bold mb-0 text-break">{{ $kegiatan->nama_kegiatan }}</h6><span class="activity-status-badge activity-status-badge--{{ $statusClass }}">{{ $statusLabel }}</span></div>
                    <small class="text-muted d-block text-break"><i class="bi bi-geo-alt me-1"></i> {{ $kegiatan->lokasi }}</small>
                    <small class="text-muted d-block text-break"><i class="bi bi-people me-1"></i> Angkatan: {{ $kegiatan->tahunAngkatans->pluck('tahun_daftar')->implode(', ') ?: 'Belum ditentukan' }}</small>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-label="Aksi untuk {{ $kegiatan->nama_kegiatan }}"><i class="bi bi-three-dots-vertical fs-5"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item py-2" href="{{ route('admin.kegiatan.show', $kegiatan) }}"><i class="bi bi-eye me-2 text-primary"></i> Lihat Detail</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.presensi.show', $kegiatan) }}"><i class="bi bi-check2-square me-2 text-success"></i> {{ auth()->user()->role === 'instruktur' ? 'Kelola Presensi' : 'Lihat Presensi' }}</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.kegiatan.sesi.index', $kegiatan) }}"><i class="bi bi-calendar2-week me-2 text-primary"></i> Kelola Sesi</a></li>
                        @if($kegiatan->jenis_pelaksanaan === \App\Models\Kegiatan::MULTI_SESI)<li><a class="dropdown-item py-2" href="{{ route('admin.kegiatan.penilaian.index', $kegiatan) }}"><i class="bi bi-star me-2 text-warning"></i> {{ auth()->user()->role === 'instruktur' ? 'Kelola Penilaian' : 'Lihat Penilaian' }}</a></li>@endif
                        @if(auth()->user()->role === 'instruktur')<li><a class="dropdown-item py-2" href="{{ route('admin.kegiatan.materi-kegiatan.index', $kegiatan) }}"><i class="bi bi-journal-text me-2 text-primary"></i> Kelola Materi</a></li>@endif
                        <li><a class="dropdown-item py-2" href="{{ route('admin.kegiatan.edit', $kegiatan) }}"><i class="bi bi-pencil me-2 text-info"></i> Ubah Kegiatan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button type="button" class="dropdown-item py-2 text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $kegiatan->id }}"><i class="bi bi-trash me-2"></i> Hapus</button></li>
                    </ul>
                </div>
            </div>
        </div>
        </div>
    @php
        };
    @endphp

    @foreach([['upcomingKegiatans', 'Akan Datang', 'calendar-event', 'upcoming', 'Belum ada kegiatan yang akan datang.'], ['pastKegiatans', 'Telah Lalu', 'clock-history', 'past', 'Belum ada riwayat kegiatan.']] as [$collection, $title, $icon, $statusClass, $emptyMessage])
        <section class="activity-list-section activity-list-section--{{ $statusClass }}">
            <div class="activity-list-heading"><div><span class="activity-list-kicker"><i class="bi bi-{{ $icon }} me-1"></i>{{ $statusClass === 'upcoming' ? 'Agenda' : 'Arsip Kegiatan' }}</span><h2>{{ $title }}</h2></div><span class="badge bg-light text-dark">{{ $$collection->total() }} kegiatan</span></div>
            <div class="row g-3 index-card-grid">
            @forelse($$collection as $kegiatan)
                {{ $renderKegiatanCard($kegiatan, $statusClass === 'upcoming' ? 'Akan Datang' : 'Selesai', $statusClass) }}
            @empty
                <div class="col-12"><div class="activity-empty-state"><i class="bi bi-{{ $icon }}"></i><span>{{ $emptyMessage }}</span></div></div>
            @endforelse
            </div>
            {{ $$collection->links('components.pagination') }}
        </section>
    @endforeach

    @foreach($allKegiatans as $kegiatan)
        <x-_modal-delete
            id="deleteModal{{ $kegiatan->id }}"
            :action="route('admin.kegiatan.destroy', $kegiatan)"
            message="Menghapus kegiatan ini akan menghapus semua data presensi, sertifikat, materi, laporan, dan lampiran yang terkait."
        />
    @endforeach

</x-app-layout>

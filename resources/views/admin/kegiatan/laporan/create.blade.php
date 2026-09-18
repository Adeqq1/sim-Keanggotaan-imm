<x-app-layout>
    <x-slot name="header">Buat Laporan Kegiatan</x-slot>
    <x-kegiatan-submenu />

    <div class="card border-0 shadow-sm p-4 mb-5">
        <h5 class="fw-bold mb-1">{{ $kegiatan->nama_kegiatan }}</h5>
        <p class="text-muted small">{{ $kegiatan->tanggal_waktu->translatedFormat('d F Y, H:i') }} · {{ $kegiatan->lokasi }} · {{ $kegiatan->presensi_count }} peserta</p>
        <form method="POST" action="{{ route('admin.kegiatan.laporan-kegiatan.store', $kegiatan) }}" enctype="multipart/form-data">
            @csrf
            @include('admin.kegiatan.laporan._form', ['laporanKegiatan' => null])
        </form>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">Detail Laporan Kegiatan</x-slot>
    <x-kegiatan-submenu />

    @php($kegiatan = $laporanKegiatan->kegiatan)
    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <img src="{{ $kegiatan->thumbnail_url }}" alt="Thumbnail {{ $kegiatan->nama_kegiatan }}" class="w-100" style="max-height: 280px; object-fit: cover;">
        <div class="card-body p-4">
            <h2 class="h4 fw-bold">{{ $kegiatan->nama_kegiatan }}</h2>
            <p class="text-muted mb-0">{{ $kegiatan->tanggal_waktu->translatedFormat('d F Y, H:i') }} · {{ $kegiatan->lokasi }}</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([['Jumlah Peserta', $kegiatan->presensi_count], ['Hadir', $kegiatan->hadir_count], ['Izin', $kegiatan->izin_count], ['Alfa', $kegiatan->alfa_count]] as [$label, $count])
            <div class="col-6 col-lg-3"><div class="card border-0 shadow-sm p-3 h-100"><small class="text-muted">{{ $label }}</small><strong class="fs-3">{{ $count }}</strong></div></div>
        @endforeach
    </div>
    @if($kegiatan->presensi_count === 0)<div class="alert alert-secondary">Presensi belum dicatat untuk kegiatan ini.</div>@endif

    <div class="card border-0 shadow-sm p-4 mb-4">
        @foreach([
            'Tujuan' => $laporanKegiatan->tujuan,
            'Ringkasan Pelaksanaan' => $laporanKegiatan->ringkasan,
            'Agenda' => $laporanKegiatan->agenda,
            'Narasumber/Instruktur' => $laporanKegiatan->narasumber,
            'Hasil' => $laporanKegiatan->hasil,
            'Kendala' => $laporanKegiatan->kendala,
            'Tindak Lanjut' => $laporanKegiatan->tindak_lanjut,
        ] as $label => $value)
            <section class="mb-4">
                <h6 class="fw-bold">{{ $label }}</h6>
                <div class="text-muted lh-lg">{!! nl2br(e($value ?: '-')) !!}</div>
            </section>
        @endforeach

        <div class="d-flex flex-wrap gap-2">
            @if($laporanKegiatan->file_lampiran)
                <a href="{{ route('admin.laporan-kegiatan.lampiran.download', $laporanKegiatan) }}" class="btn btn-outline-primary btn-ui"><i class="bi bi-download me-1"></i>Unduh Lampiran</a>
            @endif
            <a href="{{ route('admin.laporan-kegiatan.edit', $laporanKegiatan) }}" class="btn btn-primary btn-ui">Ubah Laporan</a>
            <button type="button" class="btn btn-outline-danger btn-ui" data-bs-toggle="modal" data-bs-target="#deleteLaporanModal">Hapus Laporan</button>
        </div>
    </div>

    <x-_modal-delete id="deleteLaporanModal" :action="route('admin.laporan-kegiatan.destroy', $laporanKegiatan)" message="Laporan dan lampirannya akan dihapus. Data kegiatan tetap tersedia." />
</x-app-layout>

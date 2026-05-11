<x-app-layout>
    <x-slot name="header">
        Halo, {{ auth()->user()->name }}
    </x-slot>

    <!-- Stat Card Profile -->
    <div class="card p-4 mb-4 border-0 shadow-lg" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; border-radius: 20px;">
        <div class="d-flex align-items-center mb-4">
            <div class="me-3 position-relative">
                @if(auth()->user()->anggota && auth()->user()->anggota->foto_profil)
                    <img src="{{ Storage::url(auth()->user()->anggota->foto_profil) }}" class="rounded-circle border border-3 border-white shadow" width="75" height="75" style="object-fit: cover;">
                @else
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center text-white fs-1 fw-bold border border-3 border-white shadow" style="width: 75px; height: 75px;">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
                <span class="position-absolute bottom-0 end-0 bg-success border border-white border-2 rounded-circle p-2 shadow-sm"></span>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-white">{{ auth()->user()->name }}</h5>
                <small class="opacity-75 text-white">NIA: {{ auth()->user()->anggota->nia ?? '-' }}</small>
            </div>
        </div>
        <div class="row text-center g-2 border-top border-white border-opacity-10 pt-3">
            <div class="col-4 border-end border-white border-opacity-10">
                <small class="d-block opacity-75 text-white" style="font-size: 0.65rem; letter-spacing: 0.5px;">PRESENSI</small>
                <span class="fw-bold text-white fs-5">{{ $stats['total_kehadiran'] }}</span>
            </div>
            <div class="col-4 border-end border-white border-opacity-10">
                <small class="d-block opacity-75 text-white" style="font-size: 0.65rem; letter-spacing: 0.5px;">SERTIFIKAT</small>
                <span class="fw-bold text-white fs-5">{{ $stats['total_sertifikat'] }}</span>
            </div>
            <div class="col-4">
                <small class="d-block opacity-75 text-white" style="font-size: 0.65rem; letter-spacing: 0.5px;">STATUS</small>
                <span class="badge bg-white text-primary mt-1" style="font-size: 0.6rem; font-weight: 800;">AKTIF</span>
            </div>
        </div>
    </div>

    <!-- Quick Action -->
    <div class="row g-3 mb-4 text-center">
        <div class="col-6">
            <a href="{{ route('kader.ekta') }}" class="card p-3 text-decoration-none h-100 shadow-sm border-0" style="border-radius: 15px;">
                <i class="bi bi-person-vcard text-primary display-6 mb-2"></i>
                <span class="small fw-bold text-dark d-block">E-KTA Digital</span>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('kader.sertifikat.index') }}" class="card p-3 text-decoration-none h-100 shadow-sm border-0" style="border-radius: 15px;">
                <i class="bi bi-award text-success display-6 mb-2"></i>
                <span class="small fw-bold text-dark d-block">E-Sertifikat</span>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('kader.riwayat.index') }}" class="card p-3 text-decoration-none h-100 shadow-sm border-0" style="border-radius: 15px;">
                <i class="bi bi-clock-history text-info display-6 mb-2"></i>
                <span class="small fw-bold text-dark d-block">Riwayat</span>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('profile.edit') }}" class="card p-3 text-decoration-none h-100 shadow-sm border-0" style="border-radius: 15px;">
                <i class="bi bi-gear text-secondary display-6 mb-2"></i>
                <span class="small fw-bold text-dark d-block">Pengaturan</span>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Kegiatan Mendatang</h6>
        <i class="bi bi-calendar3 text-primary"></i>
    </div>
    
    @forelse($kegiatan_terdekat as $kegiatan)
        <div class="card mb-3 p-3 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="d-flex align-items-center">
                <div class="bg-light rounded p-2 me-3 text-center" style="min-width: 55px; border-radius: 12px !important;">
                    <span class="d-block fw-bold text-primary fs-5">{{ $kegiatan->tanggal_waktu->format('d') }}</span>
                    <span class="small text-muted text-uppercase fw-bold" style="font-size: 0.6rem;">{{ $kegiatan->tanggal_waktu->format('M Y') }}</span>
                </div>
                <div class="overflow-hidden">
                    <h6 class="mb-0 fw-bold text-truncate">{{ $kegiatan->nama_kegiatan }}</h6>
                    <small class="text-muted d-block text-truncate"><i class="bi bi-geo-alt me-1"></i> {{ $kegiatan->lokasi }}</small>
                    <small class="text-primary fw-bold" style="font-size: 0.65rem;"><i class="bi bi-clock me-1"></i> {{ $kegiatan->tanggal_waktu->format('H:i') }} WIB</small>
                </div>
            </div>
        </div>
    @empty
        <div class="card p-4 text-center border-0 shadow-sm bg-light" style="border-radius: 15px; border: 2px dashed #dee2e6 !important;">
            <p class="text-muted small mb-0">Tidak ada kegiatan dalam waktu dekat.</p>
        </div>
    @endforelse

    <div class="pb-3"></div>
</x-app-layout>

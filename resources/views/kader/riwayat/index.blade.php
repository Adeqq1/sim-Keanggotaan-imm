<x-app-layout>
    <x-slot name="header">
        Riwayat Keaktifan
    </x-slot>

    <!-- Stat Cards -->
    <div class="row g-2 mb-4">
        <div class="col-4">
            <div class="card p-2 text-center border-0 shadow-sm bg-success bg-opacity-10" style="border-radius: 12px;">
                <h4 class="fw-bold mb-0 text-success">{{ $stats['hadir'] }}</h4>
                <small class="text-success fw-bold" style="font-size: 0.6rem;">HADIR</small>
            </div>
        </div>
        <div class="col-4">
            <div class="card p-2 text-center border-0 shadow-sm bg-warning bg-opacity-10" style="border-radius: 12px;">
                <h4 class="fw-bold mb-0 text-warning">{{ $stats['izin'] }}</h4>
                <small class="text-warning fw-bold" style="font-size: 0.6rem;">IZIN</small>
            </div>
        </div>
        <div class="col-4">
            <div class="card p-2 text-center border-0 shadow-sm bg-danger bg-opacity-10" style="border-radius: 12px;">
                <h4 class="fw-bold mb-0 text-danger">{{ $stats['alfa'] }}</h4>
                <small class="text-danger fw-bold" style="font-size: 0.6rem;">ALFA</small>
            </div>
        </div>
    </div>

    <h6 class="fw-bold mb-3">Daftar Kehadiran</h6>
    @forelse($presensis as $p)
        <div class="card mb-2 p-3 border-0 shadow-sm" style="border-radius: 15px;">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center overflow-hidden">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; min-width: 40px;">
                        <i class="bi bi-calendar-check text-primary"></i>
                    </div>
                    <div class="overflow-hidden">
                        <h6 class="mb-0 fw-bold text-truncate" style="font-size: 0.85rem;">{{ $p->kegiatan->nama_kegiatan }}</h6>
                        <small class="text-muted d-block" style="font-size: 0.7rem;">{{ $p->kegiatan->tanggal_waktu->format('d M Y') }}</small>
                    </div>
                </div>
                <span class="badge {{ $p->status_kehadiran === 'hadir' ? 'bg-success' : ($p->status_kehadiran === 'izin' ? 'bg-warning' : 'bg-danger') }} rounded-pill px-3" style="font-size: 0.65rem;">
                    {{ ucfirst($p->status_kehadiran) }}
                </span>
            </div>

            @php
                $sertifikat = $sertifikats->get($p->kegiatan_id);
            @endphp
            <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                <div class="small text-muted" style="font-size: 0.75rem;">
                    Sertifikat: 
                    @if($p->status_klaim === 'pending')
                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Menunggu Verifikasi</span>
                    @elseif($p->status_klaim === 'disetujui' && $sertifikat)
                        <span class="badge bg-success"><i class="bi bi-patch-check"></i> Disetujui</span>
                    @elseif($p->status_klaim === 'ditolak')
                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Ditolak</span>
                    @else
                        <span class="badge bg-secondary">Belum Diklaim</span>
                    @endif
                </div>
                <div>
                    @if($p->status_klaim === 'disetujui' && $sertifikat)
                        <a href="{{ route('kader.sertifikat.download', $sertifikat) }}" class="btn btn-sm btn-outline-success px-3 py-1" style="font-size: 0.7rem; border-radius: 8px;">
                            <i class="bi bi-download"></i> Unduh
                        </a>
                    @elseif($p->status_klaim === null || $p->status_klaim === 'ditolak')
                        <button type="button" class="btn btn-sm btn-primary px-3 py-1" data-bs-toggle="modal" data-bs-target="#claimModal{{ $p->id }}" style="font-size: 0.7rem; border-radius: 8px;">
                            <i class="bi bi-upload"></i> Klaim
                        </button>
                    @endif
                </div>
            </div>
        </div>

        @if($p->status_klaim === null || $p->status_klaim === 'ditolak')
            <!-- Modal Klaim -->
            <div class="modal fade" id="claimModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mx-3">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                        <form action="{{ route('kader.sertifikat.klaim', $p) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header border-0 pb-0">
                                <h5 class="fw-bold mb-0">Klaim Sertifikat</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body py-3">
                                <p class="text-muted small mb-3">Silakan unggah foto bukti kehadiran Anda pada kegiatan <strong>{{ $p->kegiatan->nama_kegiatan }}</strong>.</p>
                                
                                <div class="mb-3 text-start">
                                    <label for="bukti_kehadiran_{{ $p->id }}" class="form-label fw-semibold small">Foto Bukti Kehadiran (Format: JPG, PNG, max 2MB)</label>
                                    <input type="file" class="form-control" id="bukti_kehadiran_{{ $p->id }}" name="bukti_kehadiran" accept="image/png, image/jpeg, image/jpg" required style="border-radius: 8px;">
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-light btn-sm fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                                <button type="submit" class="btn btn-primary btn-sm fw-semibold" style="border-radius: 8px;">Kirim Klaim</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @empty
        <div class="text-center py-5">
            <i class="bi bi-clock-history display-4 text-muted opacity-25"></i>
            <p class="text-muted mt-2 small">Belum ada riwayat kegiatan.</p>
        </div>
    @endforelse

    <div class="pb-3"></div>

    @vite(['resources/js/image-compressor.js'])
</x-app-layout>

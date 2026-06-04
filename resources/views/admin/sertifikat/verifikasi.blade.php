<x-app-layout>
    <x-slot name="header">
        Verifikasi Sertifikat
    </x-slot>

    <div class="mb-4">
        <h6 class="fw-bold mb-1">Daftar Klaim Pending</h6>
        <p class="text-muted small">Tinjau bukti kehadiran yang diunggah oleh Kader untuk menyetujui klaim sertifikat.</p>
    </div>

    @forelse($pendingClaims as $claim)
        <div class="card mb-3 p-3 border-0 shadow-sm" style="border-radius: 12px;">
            <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                    @if($claim->bukti_kehadiran)
                        <img src="{{ asset('storage/' . $claim->bukti_kehadiran) }}" alt="Bukti" class="rounded cursor-pointer border shadow-sm" style="width: 60px; height: 60px; object-fit: cover;" data-bs-toggle="modal" data-bs-target="#proofModal{{ $claim->id }}">
                    @else
                        <div class="rounded bg-light d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px;">
                            <i class="bi bi-image fs-3"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="fw-bold mb-1 text-truncate">{{ $claim->anggota->nama_lengkap }}</h6>
                    <small class="text-muted d-block text-truncate"><i class="bi bi-calendar-event me-1"></i> {{ $claim->kegiatan->nama_kegiatan }}</small>
                    <small class="text-muted d-block" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i> Diklaim pada: {{ $claim->updated_at->format('d M Y H:i') }}</small>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2 border-top pt-2">
                <form action="{{ route('admin.sertifikat.verifikasi.tolak', $claim) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak klaim sertifikat ini?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger px-3" style="border-radius: 8px; font-size: 0.75rem;">
                        <i class="bi bi-x-circle me-1"></i> Tolak
                    </button>
                </form>
                
                <form action="{{ route('admin.sertifikat.verifikasi.setuju', $claim) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui klaim ini?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success px-3" style="border-radius: 8px; font-size: 0.75rem;">
                        <i class="bi bi-check-circle me-1"></i> Setujui
                    </button>
                </form>
            </div>
        </div>

        @if($claim->bukti_kehadiran)
            <!-- Modal Detail Bukti -->
            <div class="modal fade" id="proofModal{{ $claim->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg mx-3">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="fw-bold mb-0">Bukti Kehadiran - {{ $claim->anggota->nama_lengkap }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center p-3">
                            <img src="{{ asset('storage/' . $claim->bukti_kehadiran) }}" alt="Bukti Kehadiran Lengkap" class="img-fluid rounded border shadow-sm" style="max-height: 70vh; object-fit: contain;">
                            <div class="mt-2 text-muted small">Kegiatan: {{ $claim->kegiatan->nama_kegiatan }}</div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-light btn-sm fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px;">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @empty
        <div class="card p-5 text-center border-0 shadow-sm bg-light" style="border-radius: 20px;">
            <i class="bi bi-check-circle display-1 text-success opacity-25 mb-3 text-opacity-50"></i>
            <h6 class="fw-bold text-muted">Semua Bersih!</h6>
            <p class="text-muted small mb-0 px-4">Tidak ada klaim sertifikat yang menunggu verifikasi saat ini.</p>
        </div>
    @endforelse

    {{ $pendingClaims->links('components.pagination') }}
</x-app-layout>

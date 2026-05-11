<x-app-layout>
    <x-slot name="header">
        E-KTA Digital
    </x-slot>

    <div class="mb-4 text-center">
        <p class="text-muted small">Kartu Tanda Anggota Digital Anda</p>
    </div>

    <!-- KTA Card View -->
    <div class="card p-0 mb-4 shadow-lg overflow-hidden" style="border-radius: 18px; border: none; aspect-ratio: 1.58/1; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); position: relative;">
        <!-- Card Background Pattern -->
        <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
        <div style="position: absolute; bottom: -20px; left: -20px; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>

        <div class="p-3 h-100 d-flex flex-column justify-content-between position-relative">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="text-white fw-bold mb-0" style="letter-spacing: 1px;">KADER IMM</h5>
                    <small class="text-white opacity-50" style="font-size: 0.6rem;">IKATAN MAHASISWA MUHAMMADIYAH</small>
                </div>
                <i class="bi bi-shield-check text-white fs-3"></i>
            </div>

            <div class="d-flex align-items-center mt-3">
                <div class="me-3">
                    @if($anggota->foto_profil)
                        <img src="{{ Storage::url($anggota->foto_profil) }}" class="rounded-3 border border-2 border-white shadow-sm" width="70" height="85" style="object-fit: cover;">
                    @else
                        <div class="rounded-3 bg-white bg-opacity-25 d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 70px; height: 85px; font-size: 2rem;">
                            {{ substr($anggota->nama_lengkap, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="text-white">
                    <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">{{ strtoupper($anggota->nama_lengkap) }}</h6>
                    <p class="mb-1 fw-bold text-warning" style="font-size: 0.8rem; letter-spacing: 1px;">NIA: {{ $anggota->nia }}</p>
                    <small class="d-block opacity-75" style="font-size: 0.6rem;">AKTIF SEJAK: {{ $anggota->created_at->format('Y') }}</small>
                </div>
            </div>

            <div class="text-end">
                <small class="text-white opacity-25" style="font-size: 0.5rem; letter-spacing: 2px;">OFFICIAL MEMBERSHIP CARD</small>
            </div>
        </div>
    </div>

    <div class="d-grid gap-3">
        <a href="{{ route('kader.ekta.download') }}" class="btn btn-primary py-3 fw-bold">
            <i class="bi bi-download me-2"></i> Download KTA (PDF)
        </a>
        <button onclick="window.print()" class="btn btn-outline-secondary py-3 fw-bold">
            <i class="bi bi-printer me-2"></i> Cetak Kartu
        </button>
    </div>

    <div class="mt-4 p-3 bg-light rounded-3">
        <h6 class="fw-bold small mb-2"><i class="bi bi-info-circle me-1"></i> Informasi Kartu</h6>
        <p class="text-muted" style="font-size: 0.75rem; line-height: 1.4;">E-KTA ini adalah identitas resmi anggota Ikatan Mahasiswa Muhammadiyah. Gunakan kartu ini untuk keperluan verifikasi pada kegiatan resmi organisasi.</p>
    </div>
</x-app-layout>

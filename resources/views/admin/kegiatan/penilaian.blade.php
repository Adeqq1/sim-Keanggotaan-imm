<x-app-layout>
    <x-slot name="header">
        {{ $canManagePenilaian ? 'Kelola Penilaian' : 'Lihat Penilaian' }}
    </x-slot>

    @if(auth()->user()->role === 'admin')
        <x-kegiatan-submenu />
    @endif

    <div class="card p-3 mb-4 border-start border-primary border-4">
        <h6 class="fw-bold mb-1 text-primary">{{ $kegiatan->nama_kegiatan }}</h6>
        <p class="text-muted small mb-2">Multi-sesi · Minimum {{ $kegiatan->minimum_sesi_terverifikasi }} sesi terverifikasi</p>
        <div class="small text-muted">
            @foreach($kegiatan->sesiKegiatans as $sesi)
                <span class="me-3"><i class="bi bi-calendar2-week me-1"></i>{{ $sesi->nama_sesi }} · {{ $sesi->mulai_pada->translatedFormat('d F Y, H:i') }}</span>
            @endforeach
        </div>
    </div>

    @forelse($anggotas as $anggota)
        @php
            $penilaian = $anggota->penilaianKegiatans->first();
            $errorBag = $errors->getBag('penilaian-'.$anggota->id);
            $describedBy = $errorBag->any() ? 'penilaian-error-'.$anggota->id : null;
            $selectedNilai = $errorBag->any() ? old('nilai') : $penilaian?->nilai;
        @endphp
        <div class="card mb-3 p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                <div>
                    <h6 class="fw-bold mb-1">{{ $anggota->nama_lengkap }}</h6>
                    <small class="text-muted">NIA: {{ $anggota->nia ?? '-' }}</small>
                </div>

                @if($canManagePenilaian)
                    <form method="POST" action="{{ route('admin.kegiatan.penilaian.update', [$kegiatan, $anggota]) }}" class="flex-grow-1">
                        @csrf
                        @method('PUT')
                        <fieldset aria-describedby="{{ $describedBy }}" class="border-0 p-0 m-0">
                            <legend class="visually-hidden">Penilaian untuk {{ $anggota->nama_lengkap }}</legend>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($nilaiLabels as $kode => $label)
                                    @php($radioId = "nilai-{$anggota->id}-{$kode}")
                                    <input type="radio" class="btn-check" name="nilai" id="{{ $radioId }}" value="{{ $kode }}" @checked($selectedNilai === $kode)>
                                    <label class="btn btn-outline-primary" for="{{ $radioId }}">{{ $kode }} - {{ $label }}</label>
                                @endforeach
                            </div>
                            @if($errorBag->any())
                                <div id="penilaian-error-{{ $anggota->id }}" class="text-danger small mt-2">{{ $errorBag->first('nilai') }}</div>
                            @endif
                            <button type="submit" class="btn btn-primary btn-ui btn-ui-sm mt-3"><i class="bi bi-save me-1"></i> Simpan</button>
                        </fieldset>
                    </form>
                @else
                    <div class="text-md-end">
                        <span class="badge bg-primary fs-6">{{ $penilaian?->nilai ?? 'Belum dinilai' }}</span>
                        @if($penilaian)
                            <div class="small text-muted mt-1">{{ $penilaian->nilai }} - {{ $nilaiLabels[$penilaian->nilai] }}</div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="card p-4 text-center text-muted">Belum ada kader aktif yang memenuhi syarat penilaian.</div>
    @endforelse

    <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-outline-secondary btn-ui">Kembali</a>
</x-app-layout>

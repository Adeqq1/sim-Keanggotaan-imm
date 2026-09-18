<x-app-layout>
    <x-slot name="header">Sesi Kegiatan</x-slot>
    <div class="card p-3 mb-4">
        <h5 class="mb-1">{{ $kegiatan->nama_kegiatan }}</h5>
        <p class="text-muted mb-0">Kebijakan: {{ str_replace('_', ' ', $kegiatan->jenis_pelaksanaan) }}. Minimum: {{ $kegiatan->minimum_sesi_terverifikasi ?? '-' }} sesi terverifikasi.</p>
    </div>
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(auth()->user()->role === 'instruktur' || auth()->user()->role === 'admin')
        <form method="POST" action="{{ route('admin.kegiatan.sesi.store', $kegiatan) }}" class="card card-body mb-4">
            @csrf
            <div class="row g-2 align-items-end">
                <div class="col-md-2"><label class="form-label">Urutan</label><input type="number" name="urutan" min="1" class="form-control" required></div>
                <div class="col-md-4"><label class="form-label">Nama sesi</label><input type="text" name="nama_sesi" class="form-control" required></div>
                <div class="col-md-4"><label class="form-label">Mulai pada</label><input type="datetime-local" name="mulai_pada" class="form-control" required></div>
                <div class="col-md-2"><button class="btn btn-primary w-100">Tambah</button></div>
            </div>
        </form>
    @endif
    <div class="list-group">
        @forelse($sesies as $sesi)
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div><strong>Sesi {{ $sesi->urutan }}: {{ $sesi->nama_sesi }}</strong><br><small class="text-muted">{{ $sesi->mulai_pada->translatedFormat('d F Y, H:i') }} · {{ $sesi->presensis()->count() }} catatan presensi</small></div>
                <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.presensi.sesi.show', [$kegiatan, $sesi]) }}">Presensi</a>
            </div>
        @empty
            <div class="alert alert-warning">Belum ada sesi.</div>
        @endforelse
    </div>
</x-app-layout>

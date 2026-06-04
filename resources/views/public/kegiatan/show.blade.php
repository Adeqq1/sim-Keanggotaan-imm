@extends('layouts.public')

@section('content')
<main class="py-5 bg-light" style="min-height: 80vh;">
    <div class="container my-4">
        <!-- Breadcrumb & Back button -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('landing') }}" class="text-decoration-none text-muted">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('landing') }}#kegiatan" class="text-decoration-none text-muted">Kegiatan</a></li>
                <li class="breadcrumb-item active text-primary" aria-current="page">Detail</li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Main Content Column -->
            <article class="col-lg-8">
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 16px;">
                    <!-- Image -->
                    <img
                        src="{{ $kegiatan->thumbnail_url }}"
                        alt="{{ $kegiatan->nama_kegiatan }}"
                        class="img-fluid w-100"
                        style="max-height: 450px; object-fit: cover;"
                    >

                    <!-- Card Body -->
                    <div class="card-body p-4 p-md-5">
                        <!-- Date & Location Badges -->
                        <div class="d-flex flex-wrap gap-3 mb-4 text-muted small">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-check-fill text-primary me-2 fs-5"></i>
                                <div>
                                    <span class="d-block fw-semibold text-dark">Tanggal Pelaksanaan</span>
                                    <span>{{ $kegiatan->tanggal_waktu->format('d F Y, H:i') }} WIB</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt-fill text-danger me-2 fs-5"></i>
                                <div>
                                    <span class="d-block fw-semibold text-dark">Tempat / Lokasi</span>
                                    <span>{{ $kegiatan->lokasi }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Title -->
                        <h1 class="h2 fw-bold text-dark mb-4" style="line-height: 1.3;">
                            {{ $kegiatan->nama_kegiatan }}
                        </h1>

                        <!-- Content Description -->
                        <div class="text-muted" style="font-size: 1.05rem; line-height: 1.8; text-align: justify;">
                            {!! nl2br(e($kegiatan->deskripsi)) !!}
                        </div>

                        <hr class="my-5 opacity-10">

                        <!-- Footer Share or Action -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <a href="{{ route('landing') }}#kegiatan" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                                <i class="bi bi-arrow-left me-2"></i>Kembali ke Beranda
                            </a>
                            <div class="d-flex align-items-center gap-2">
                                <span class="small text-muted me-1">Bagikan:</span>
                                <a href="https://api.whatsapp.com/send?text={{ rawurlencode($kegiatan->nama_kegiatan . ' - ' . route('kegiatan.show', $kegiatan->id)) }}" target="_blank" rel="noopener" class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Share WhatsApp">
                                    <i class="bi bi-whatsapp text-success fs-5"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{ rawurlencode($kegiatan->nama_kegiatan) }}&url={{ rawurlencode(route('kegiatan.show', $kegiatan->id)) }}" target="_blank" rel="noopener" class="btn btn-light btn-sm rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="Share Twitter">
                                    <i class="bi bi-twitter-x text-dark fs-5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Sticky Sidebar Column -->
            <aside class="col-lg-4">
                <div class="position-sticky" style="top: 6rem;">
                    <!-- Sidebar Title -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="bg-primary rounded" style="width: 4px; height: 24px;"></span>
                        <h2 class="h5 fw-bold text-dark mb-0">Kegiatan Lainnya</h2>
                    </div>

                    <!-- Recommendations List -->
                    <div class="d-flex flex-column gap-3">
                        @forelse($rekomendasi as $rek)
                            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 12px; transition: transform 0.2s;">
                                <div class="row g-0">
                                    <div class="col-4">
                                        <img
                                            src="{{ $rek->thumbnail_url }}"
                                            alt="{{ $rek->nama_kegiatan }}"
                                            class="w-100 h-100"
                                            style="object-fit: cover; min-height: 100px;"
                                        >
                                    </div>
                                    <div class="col-8">
                                        <div class="card-body p-3 d-flex flex-column h-100 justify-content-between">
                                            <div>
                                                <small class="text-primary d-block mb-1" style="font-size: 0.75rem;">
                                                    <i class="bi bi-calendar-event me-1"></i>{{ $rek->tanggal_waktu->format('d M Y') }}
                                                </small>
                                                <h3 class="h6 fw-bold mb-0 text-dark" style="line-height: 1.4;">
                                                    <a href="{{ route('kegiatan.show', $rek->id) }}" class="text-decoration-none text-dark stretched-link">
                                                        {{ Str::limit($rek->nama_kegiatan, 45) }}
                                                    </a>
                                                </h3>
                                            </div>
                                            <small class="text-muted text-truncate d-block mt-2">
                                                <i class="bi bi-geo-alt me-1"></i>{{ $rek->lokasi }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="card border-0 p-4 text-center text-muted" style="border-radius: 12px;">
                                Belum ada rekomendasi kegiatan.
                            </div>
                        @endforelse
                    </div>

                    <!-- Call To Action Widget (Premium Aesthetic Addition) -->
                    <div class="card border-0 text-white mt-4 overflow-hidden text-center shadow-sm" style="border-radius: 16px; background: linear-gradient(135deg, var(--imm-primary, #800000) 0%, #a00000 100%);">
                        <div class="card-body p-4">
                            <i class="bi bi-people-fill display-5 mb-3 d-block"></i>
                            <h3 class="h5 fw-bold mb-2">Ingin Bergabung Bersama Kami?</h3>
                            <p class="small opacity-75 mb-3">Jadilah bagian dari Ikatan Mahasiswa Muhammadiyah untuk berkolaborasi dan berdampak nyata.</p>
                            <a href="{{ route('pendaftaran') }}" class="btn btn-light text-primary btn-sm fw-bold px-4 py-2 rounded-pill shadow-sm" style="color: var(--imm-primary, #800000) !important;">
                                Daftar Anggota Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</main>
@endsection

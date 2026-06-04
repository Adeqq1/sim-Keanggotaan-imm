<section class="section-padding bg-light" id="kegiatan">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Agenda & Dokumentasi</span>
            <h2 class="section-title display-6 mt-2">Kegiatan Terbaru</h2>
            <p class="text-muted mt-3 mx-auto" style="max-width:520px;">
                Ikuti terus agenda terbaru dan dokumentasi kegiatan Ikatan Mahasiswa Muhammadiyah.
            </p>
        </div>

        <div class="row g-4">
            @forelse($kegiatan as $item)
            <div class="col-md-4">
                <div class="card program-card shadow-sm h-100 border-0">
                    <img
                        src="{{ $item->thumbnail ? asset('storage/' . $item->thumbnail) : asset('images/landing/hero.jpg') }}"
                        alt="{{ $item->nama_kegiatan }}"
                        class="card-img-top"
                        loading="lazy"
                        style="height: 200px; object-fit: cover;"
                    >
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-center mb-2 text-muted small">
                            <i class="bi bi-calendar-event me-2 text-primary"></i>
                            <span>{{ $item->tanggal_waktu->format('d M Y') }}</span>
                            <span class="mx-2">•</span>
                            <i class="bi bi-geo-alt me-1 text-danger"></i>
                            <span class="text-truncate" style="max-width: 150px;">{{ $item->lokasi }}</span>
                        </div>
                        <h3 class="h5 fw-bold mb-2">{{ $item->nama_kegiatan }}</h3>
                        <p class="text-muted small mb-4" style="line-height:1.7;">
                            {{ Str::limit(strip_tags($item->deskripsi), 120) }}
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('kegiatan.show', $item->id) }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <p class="text-muted">Belum ada kegiatan yang dipublikasikan.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

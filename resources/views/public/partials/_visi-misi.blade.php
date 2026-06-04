<section class="visi-misi-section section-padding" id="visi-misi">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Arah &amp; Tujuan</span>
            <h2 class="section-title display-6 mt-2">Visi &amp; Misi IMM</h2>
        </div>

        <div class="row g-4">
            {{-- Visi --}}
            <div class="col-lg-5">
                <div class="card visi-card shadow-sm h-100 p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-3 p-3" style="background:var(--imm-primary);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </div>
                        <h3 class="fw-bold mb-0 fs-4">Visi</h3>
                    </div>

                    {{-- TODO: Sesuaikan dengan redaksi resmi AD/ART IMM --}}
                    <p class="text-muted" style="line-height:1.8;font-size:1.05rem;">
                        Terbentuknya akademisi Islam yang berakhlak mulia dalam rangka mencapai tujuan Muhammadiyah.
                    </p>

                    <div class="visi-quote">
                        <p class="mb-0 small text-primary fw-semibold fst-italic">
                            "Mengutamakan iman, ilmu, dan amal sebagai landasan gerak organisasi."
                        </p>
                    </div>
                </div>
            </div>

            {{-- Misi --}}
            <div class="col-lg-7">
                <div class="card misi-card shadow-sm h-100 p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-3 p-3" style="background:var(--imm-yellow);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                            </svg>
                        </div>
                        <h3 class="fw-bold mb-0 fs-4">Misi</h3>
                    </div>

                    <div>
                        @foreach(config('landing.misi') as $item)
                        <div class="misi-item d-flex align-items-start gap-3">
                            <i class="bi {{ $item['icon'] }} text-primary mt-1 flex-shrink-0" aria-hidden="true"></i>
                            <p class="mb-0 text-muted small" style="line-height:1.7;">{{ $item['text'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-padding bg-light" id="program">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-label">Apa yang Kami Lakukan</span>
            <h2 class="section-title display-6 mt-2">Program Unggulan IMM</h2>
            <p class="text-muted mt-3 mx-auto" style="max-width:520px;">
                Berbagai program dirancang untuk membentuk kader yang kompeten, berkarakter, dan siap berkontribusi nyata.
            </p>
        </div>

        <div class="row g-4">
            @foreach(config('landing.programs') as $program)
            <div class="col-md-4">
                <div class="card program-card shadow-sm h-100">
                    {{-- TODO: Ganti dengan foto resmi kegiatan IMM --}}
                    <img
                        src="{{ $program['image'] }}"
                        alt="{{ $program['alt'] }}"
                        class="card-img-top"
                        loading="lazy"
                        width="600"
                        height="200"
                    >
                    <div class="card-body p-4">
                        <div class="program-icon mb-3" style="background:{{ $program['icon_bg'] }};">
                            <i class="bi {{ $program['icon'] }} {{ $program['icon_color'] }} fs-5"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2">{{ $program['title'] }}</h3>
                        <p class="text-muted small mb-0" style="line-height:1.7;">{{ $program['description'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

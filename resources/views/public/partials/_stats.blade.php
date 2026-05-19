<section class="stats-section py-5" aria-label="Statistik organisasi">
    <div class="container">
        <div class="row g-4 text-white text-center">
            @foreach(config('landing.stats') as $stat)
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    {{-- TODO: Verifikasi angka statistik dengan PM sebelum go-live --}}
                    <div class="display-5 fw-bold text-warning">{{ $stat['value'] }}</div>
                    <div class="mt-1 fw-semibold" style="opacity:.8;">{{ $stat['label'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

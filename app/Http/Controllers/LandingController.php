<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Display the landing page with the 3 latest activities.
     */
    public function index(): View
    {
        $kegiatanRaw = Cache::remember('kegiatan.terbaru', 3600, function () {
            return Kegiatan::latest()->take(3)->get()->toArray();
        });

        if (! is_array($kegiatanRaw)) {
            Cache::forget('kegiatan.terbaru');
            $kegiatanRaw = Kegiatan::latest()->take(3)->get()->toArray();
            Cache::put('kegiatan.terbaru', $kegiatanRaw, 3600);
        }

        $kegiatan = Kegiatan::hydrate($kegiatanRaw);

        return view('public.landing', compact('kegiatan'));
    }

    /**
     * Display the specified activity detail with 3 recommendations.
     */
    public function show(Kegiatan $kegiatan): View
    {
        $rekomendasi = Kegiatan::where('id', '!=', $kegiatan->id)
            ->latest()
            ->take(3)
            ->get();

        return view('public.kegiatan.show', compact('kegiatan', 'rekomendasi'));
    }
}

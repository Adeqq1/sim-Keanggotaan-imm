<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Display the landing page with the 3 latest activities.
     */
    public function index(): View
    {
        $kegiatan = Kegiatan::latest()->take(3)->get();

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

<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Pengaturan;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        $pengaturan = Pengaturan::aktif();

        $beritaTerbaru = Berita::published()
            ->with('pembuat')
            ->latest('published_at')
            ->limit(6)
            ->get();

        return view('public.home', compact(
            'pengaturan',
            'beritaTerbaru'
        ));
    }

    public function newsIndex(): View
    {
        $pengaturan = Pengaturan::aktif();

        $berita = Berita::published()
            ->with('pembuat')
            ->latest('published_at')
            ->paginate(9);

        return view('public.news.index', compact(
            'pengaturan',
            'berita'
        ));
    }
}
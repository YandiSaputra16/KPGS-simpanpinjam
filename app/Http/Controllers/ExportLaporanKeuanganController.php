<?php

namespace App\Http\Controllers;

use App\Exports\LaporanKeuanganExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExportLaporanKeuanganController extends Controller
{
    /**
     * Mengunduh laporan keuangan dalam format Excel.
     * Khusus untuk Bendahara.
     */
    public function __invoke(Request $request)
    {
        abort_unless(
            Auth::check() && Auth::user()->role === 'bendahara',
            403,
            'Hanya bendahara yang dapat mengekspor laporan keuangan.'
        );

        $validated = $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => [
                'nullable',
                'date',
                'after_or_equal:dari',
            ],
        ]);

        $dari = $validated['dari'] ?? null;
        $sampai = $validated['sampai'] ?? null;

        $namaFile = 'laporan-keuangan';

        if ($dari && $sampai) {
            $namaFile .= "-{$dari}-sampai-{$sampai}";
        } elseif ($dari) {
            $namaFile .= "-mulai-{$dari}";
        } elseif ($sampai) {
            $namaFile .= "-sampai-{$sampai}";
        }

        return Excel::download(
            new LaporanKeuanganExport($dari, $sampai),
            $namaFile . '.xlsx'
        );
    }
}

<?php

namespace App\Http\Controllers;

use App\Exports\LaporanAnggotaExport;
use App\Exports\LaporanPinjamanExport;
use App\Models\Anggota;
use App\Models\Pinjaman;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportLaporanController extends Controller
{
    /**
     * Ekspor laporan anggota ke Excel.
     * Digunakan oleh CS.
     */
    public function anggota(Request $request): BinaryFileResponse
    {
        $query = Anggota::query()
            ->with('user');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function (Builder $q) use ($search) {
                $q->where('kode_anggota', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                        $userQuery
                            ->where('nama', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'status_keanggotaan',
                $request->input('status')
            );
        }

        if ($request->filled('tahun')) {
            $query->whereYear(
                'tanggal_daftar',
                $request->input('tahun')
            );
        }

        $anggota = $query
            ->orderByDesc('tanggal_daftar')
            ->orderByDesc('id')
            ->get();

        return Excel::download(
            new LaporanAnggotaExport($anggota),
            'laporan-anggota-' . now()->format('Ymd-His') . '.xlsx'
        );
    }

    /**
     * Ekspor laporan pinjaman ke Excel.
     * Digunakan oleh CS, AO, Kabag, dan Bendahara
     * sesuai hak akses halaman laporan pinjaman.
     */
    public function pinjaman(Request $request): BinaryFileResponse
    {
        $query = Pinjaman::query()
            ->with([
                'anggota.user',
                'masterPinjaman',
            ]);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(function (Builder $q) use ($search) {
                $q->where('no_pinjaman', 'like', "%{$search}%")
                    ->orWhere('tujuan_pinjaman', 'like', "%{$search}%")
                    ->orWhereHas('anggota', function (Builder $anggotaQuery) use ($search) {
                        $anggotaQuery
                            ->where('kode_anggota', 'like', "%{$search}%")
                            ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                                $userQuery->where(
                                    'nama',
                                    'like',
                                    "%{$search}%"
                                );
                            });
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->input('status')
            );
        }

        if ($request->filled('dari')) {
            $query->whereDate(
                'tanggal_pengajuan',
                '>=',
                $request->input('dari')
            );
        }

        if ($request->filled('sampai')) {
            $query->whereDate(
                'tanggal_pengajuan',
                '<=',
                $request->input('sampai')
            );
        }

        $pinjaman = $query
            ->orderByDesc('tanggal_pengajuan')
            ->orderByDesc('id')
            ->get();

        return Excel::download(
            new LaporanPinjamanExport($pinjaman),
            'laporan-pinjaman-' . now()->format('Ymd-His') . '.xlsx'
        );
    }
}


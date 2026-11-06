<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LaporanKeuanganController extends Controller
{
    /**
     * Laporan Keuangan terpadu untuk Kabag dan Bendahara.
     */
    public function index(Request $request)
    {
        abort_unless(
            in_array(auth()->user()->role, ['kabag', 'bendahara'], true),
            403
        );

        $validated = $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ]);

        $dari = $validated['dari'] ?? null;
        $sampai = $validated['sampai'] ?? null;

        $kasTerakhir = DB::table('kas')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->first();

        $saldoKas = (float) ($kasTerakhir->saldo_sesudah ?? 0);

        /*
         * Arus kas mengikuti rentang tanggal yang dipilih.
         */
        $kasPeriode = DB::table('kas');

        if ($dari) {
            $kasPeriode->whereDate('tanggal', '>=', $dari);
        }

        if ($sampai) {
            $kasPeriode->whereDate('tanggal', '<=', $sampai);
        }

        $kasMasuk = (clone $kasPeriode)
            ->where('jenis', 'masuk')
            ->sum('jumlah');

        $kasKeluar = (clone $kasPeriode)
            ->where('jenis', 'keluar')
            ->sum('jumlah');

        $transaksiKas = (clone $kasPeriode)
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        /*
         * Ringkasan pinjaman.
         */
        $pinjamanQuery = DB::table('pinjaman');

        if ($dari) {
            $pinjamanQuery->whereDate('tanggal_pengajuan', '>=', $dari);
        }

        if ($sampai) {
            $pinjamanQuery->whereDate('tanggal_pengajuan', '<=', $sampai);
        }

        $totalPengajuan = (clone $pinjamanQuery)->count();

        $pinjamanAktif = (clone $pinjamanQuery)
            ->where('status', 'dicairkan')
            ->count();

        $nilaiPinjamanAktif = (float) (clone $pinjamanQuery)
            ->where('status', 'dicairkan')
            ->sum(DB::raw('COALESCE(jumlah_disetujui, jumlah_pinjaman)'));
        /*
|--------------------------------------------------------------------------
| RINCIAN PINJAMAN
|--------------------------------------------------------------------------
| Menampilkan pengajuan pinjaman sesuai periode laporan.
*/

$pinjamanTerbaru = DB::table('pinjaman')
    ->leftJoin(
        'anggota',
        'anggota.id',
        '=',
        'pinjaman.anggota_id'
    )
    ->leftJoin(
        'users',
        'users.id',
        '=',
        'anggota.user_id'
    )
    ->leftJoin(
        'master_pinjaman',
        'master_pinjaman.id',
        '=',
        'pinjaman.master_pinjaman_id'
    )
    ->select([
        'pinjaman.id',
        'pinjaman.no_pinjaman',
        'pinjaman.tanggal_pengajuan',
        'pinjaman.tanggal_cair',
        'pinjaman.jumlah_pinjaman',
        'pinjaman.jumlah_disetujui',
        'pinjaman.tenor',
        'pinjaman.status',
        'users.nama as nama_anggota',
        'master_pinjaman.nama_paket as nama_jenis_pinjaman',
    ])
    ->when($dari, function ($query) use ($dari) {
        $query->whereDate(
            'pinjaman.tanggal_pengajuan',
            '>=',
            $dari
        );
    })
    ->when($sampai, function ($query) use ($sampai) {
        $query->whereDate(
            'pinjaman.tanggal_pengajuan',
            '<=',
            $sampai
        );
    })
    ->orderByDesc('pinjaman.tanggal_pengajuan')
    ->orderByDesc('pinjaman.id')
    ->limit(10)
    ->get();
        /*
         * Ringkasan angsuran.
         */
        $angsuranQuery = DB::table('angsuran');

        if ($dari) {
            $angsuranQuery->whereDate('tanggal_jatuh_tempo', '>=', $dari);
        }

        if ($sampai) {
            $angsuranQuery->whereDate('tanggal_jatuh_tempo', '<=', $sampai);
        }

        $totalAngsuran = (clone $angsuranQuery)->count();

        $angsuranLunas = (clone $angsuranQuery)
            ->where('status_pembayaran', 'lunas')
            ->count();

        $menungguVerifikasi = (clone $angsuranQuery)
            ->where('status_pembayaran', 'menunggu_verifikasi')
            ->count();

        $totalTagihan = (float) (clone $angsuranQuery)
            ->sum('total_angsuran');

        $totalDibayar = (float) (clone $angsuranQuery)
            ->where('status_pembayaran', 'lunas')
            ->sum('jumlah_bayar');

        $angsuranTerbaru = DB::table('angsuran')
            ->join('pinjaman', 'pinjaman.id', '=', 'angsuran.pinjaman_id')
            ->leftJoin('anggota', 'anggota.id', '=', 'pinjaman.anggota_id')
            ->leftJoin('users', 'users.id', '=', 'anggota.user_id')
            ->select([
                'angsuran.id',
                'angsuran.angsuran_ke',
                'angsuran.tanggal_jatuh_tempo',
                'angsuran.total_angsuran',
                'angsuran.jumlah_bayar',
                'angsuran.status_pembayaran',
                'pinjaman.no_pinjaman',
                'users.nama as nama_anggota',
            ])
            ->when($dari, fn ($q) => $q->whereDate(
                'angsuran.tanggal_jatuh_tempo', '>=', $dari
            ))
            ->when($sampai, fn ($q) => $q->whereDate(
                'angsuran.tanggal_jatuh_tempo', '<=', $sampai
            ))
            ->orderByDesc('angsuran.tanggal_jatuh_tempo')
            ->orderByDesc('angsuran.id')
            ->limit(10)
            ->get();

    
        $simpananMasuk = (clone $kasPeriode)
            ->where('jenis', 'masuk')
            ->where('kategori', 'simpanan')
            ->sum('jumlah');

        $simpananKeluar = (clone $kasPeriode)
            ->where('jenis', 'keluar')
            ->where('kategori', 'simpanan')
            ->sum('jumlah');

       return view('laporan.keuangan', compact(
        'dari',
        'sampai',
        'saldoKas',
        'kasMasuk',
        'kasKeluar',
        'transaksiKas',
        'totalPengajuan',
        'pinjamanAktif',
        'nilaiPinjamanAktif',
        'pinjamanTerbaru',
        'totalAngsuran',
        'angsuranLunas',
        'menungguVerifikasi',
        'totalTagihan',
        'totalDibayar',
        'angsuranTerbaru',
        'simpananMasuk',
        'simpananKeluar'
    ));
    }
}

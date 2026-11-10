<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

            'search_kas' => ['nullable', 'string', 'max:100'],
            'search_simpanan' => ['nullable', 'string', 'max:100'],
            'search_pinjaman' => ['nullable', 'string', 'max:100'],
            'search_angsuran' => ['nullable', 'string', 'max:100'],
        ]);

        $dari = $validated['dari'] ?? null;
        $sampai = $validated['sampai'] ?? null;

        $searchKas = trim($validated['search_kas'] ?? '');
        $searchSimpanan = trim($validated['search_simpanan'] ?? '');
        $searchPinjaman = trim($validated['search_pinjaman'] ?? '');
        $searchAngsuran = trim($validated['search_angsuran'] ?? '');

        /*
         * Parameter query yang tidak boleh ikut menjadi parameter halaman.
         * Parameter pencarian dan periode tetap dipertahankan.
         */
        $queryString = $request->except([
            'kas_page',
            'simpanan_page',
            'pinjaman_page',
            'angsuran_page',
        ]);

        /*
         * SALDO KAS TERKINI
         */
        $kasTerakhir = DB::table('kas')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->first();

        $saldoKas = (float) ($kasTerakhir->saldo_sesudah ?? 0);

        /*
         * QUERY DASAR KAS BERDASARKAN PERIODE
         */
        $kasPeriode = DB::table('kas')
            ->when($dari, fn ($query) =>
                $query->whereDate('tanggal', '>=', $dari)
            )
            ->when($sampai, fn ($query) =>
                $query->whereDate('tanggal', '<=', $sampai)
            );

        /*
         * RINGKASAN KAS
         */
        $kasMasuk = (clone $kasPeriode)
            ->where('jenis', 'masuk')
            ->sum('jumlah');

        $kasKeluar = (clone $kasPeriode)
            ->where('jenis', 'keluar')
            ->sum('jumlah');

        /*
         * DETAIL TRANSAKSI KAS
         */
        $kasDetailQuery = (clone $kasPeriode);

        if ($searchKas !== '') {
            $kasDetailQuery->where(function ($query) use ($searchKas) {
                $query->where('no_transaksi', 'like', "%{$searchKas}%")
                    ->orWhere('jenis', 'like', "%{$searchKas}%")
                    ->orWhere('kategori', 'like', "%{$searchKas}%")
                    ->orWhere('referensi_tipe', 'like', "%{$searchKas}%")
                    ->orWhere('referensi_id', 'like', "%{$searchKas}%")
                    ->orWhere('jumlah', 'like', "%{$searchKas}%");
            });
        }

        $transaksiKas = $kasDetailQuery
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'kas_page')
            ->appends($queryString);

        /*
         * RINGKASAN PINJAMAN
         */
        $pinjamanQuery = DB::table('pinjaman')
            ->when($dari, fn ($query) =>
                $query->whereDate('tanggal_pengajuan', '>=', $dari)
            )
            ->when($sampai, fn ($query) =>
                $query->whereDate('tanggal_pengajuan', '<=', $sampai)
            );

        $totalPengajuan = (clone $pinjamanQuery)->count();

        $pinjamanAktif = (clone $pinjamanQuery)
            ->where('status', 'dicairkan')
            ->count();

        $nilaiPinjamanAktif = (float) (clone $pinjamanQuery)
            ->where('status', 'dicairkan')
            ->sum(DB::raw('COALESCE(jumlah_disetujui, jumlah_pinjaman)'));

        /*
         * DETAIL PINJAMAN
         */
        $pinjamanDetailQuery = DB::table('pinjaman')
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
            ->when($dari, fn ($query) =>
                $query->whereDate('pinjaman.tanggal_pengajuan', '>=', $dari)
            )
            ->when($sampai, fn ($query) =>
                $query->whereDate('pinjaman.tanggal_pengajuan', '<=', $sampai)
            );

        if ($searchPinjaman !== '') {
            $pinjamanDetailQuery->where(function ($query) use ($searchPinjaman) {
                $query->where('pinjaman.no_pinjaman', 'like', "%{$searchPinjaman}%")
                    ->orWhere('users.nama', 'like', "%{$searchPinjaman}%")
                    ->orWhere('master_pinjaman.nama_paket', 'like', "%{$searchPinjaman}%")
                    ->orWhere('pinjaman.status', 'like', "%{$searchPinjaman}%");
            });
        }

        $pinjamanTerbaru = $pinjamanDetailQuery
            ->orderByDesc('pinjaman.tanggal_pengajuan')
            ->orderByDesc('pinjaman.id')
            ->paginate(10, ['*'], 'pinjaman_page')
            ->appends($queryString);

        /*
         * RINGKASAN ANGSURAN
         */
        $angsuranQuery = DB::table('angsuran')
            ->when($dari, fn ($query) =>
                $query->whereDate('tanggal_jatuh_tempo', '>=', $dari)
            )
            ->when($sampai, fn ($query) =>
                $query->whereDate('tanggal_jatuh_tempo', '<=', $sampai)
            );

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

        /*
         * DETAIL ANGSURAN
         */
        $angsuranDetailQuery = DB::table('angsuran')
            ->join(
                'pinjaman',
                'pinjaman.id',
                '=',
                'angsuran.pinjaman_id'
            )
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
            ->when($dari, fn ($query) =>
                $query->whereDate('angsuran.tanggal_jatuh_tempo', '>=', $dari)
            )
            ->when($sampai, fn ($query) =>
                $query->whereDate('angsuran.tanggal_jatuh_tempo', '<=', $sampai)
            );

        if ($searchAngsuran !== '') {
            $angsuranDetailQuery->where(function ($query) use ($searchAngsuran) {
                $query->where('users.nama', 'like', "%{$searchAngsuran}%")
                    ->orWhere('pinjaman.no_pinjaman', 'like', "%{$searchAngsuran}%")
                    ->orWhere('angsuran.status_pembayaran', 'like', "%{$searchAngsuran}%")
                    ->orWhere('angsuran.angsuran_ke', 'like', "%{$searchAngsuran}%");
            });
        }

        $angsuranTerbaru = $angsuranDetailQuery
            ->orderByDesc('angsuran.tanggal_jatuh_tempo')
            ->orderByDesc('angsuran.id')
            ->paginate(10, ['*'], 'angsuran_page')
            ->appends($queryString);

        /*
         * RINGKASAN SIMPANAN
         */
        $simpananMasuk = (clone $kasPeriode)
            ->where('jenis', 'masuk')
            ->where('kategori', 'simpanan')
            ->sum('jumlah');

        $simpananKeluar = (clone $kasPeriode)
            ->where('jenis', 'keluar')
            ->where('kategori', 'simpanan')
            ->sum('jumlah');

        /*
         * DETAIL TRANSAKSI SIMPANAN
         * Query dibuat terpisah dari transaksi kas agar pagination independen.
         */
        $simpananDetailQuery = (clone $kasPeriode)
            ->where('kategori', 'simpanan');

        if ($searchSimpanan !== '') {
            $simpananDetailQuery->where(function ($query) use ($searchSimpanan) {
                $query->where('no_transaksi', 'like', "%{$searchSimpanan}%")
                    ->orWhere('jenis', 'like', "%{$searchSimpanan}%")
                    ->orWhere('referensi_tipe', 'like', "%{$searchSimpanan}%")
                    ->orWhere('referensi_id', 'like', "%{$searchSimpanan}%")
                    ->orWhere('jumlah', 'like', "%{$searchSimpanan}%");
            });
        }

        $transaksiSimpanan = $simpananDetailQuery
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'simpanan_page')
            ->appends($queryString);

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
            'simpananKeluar',
            'transaksiSimpanan',
            'searchKas',
            'searchSimpanan',
            'searchPinjaman',
            'searchAngsuran'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\Kas;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\JenisSimpanan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Models\PencairanSimpanan;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\LaporanSimpananExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LAPORAN ANGGOTA
    |--------------------------------------------------------------------------
    */

    public function anggota(Request $request)
    {
        $query = Anggota::query()
            ->with('user');

        /*
         * Filter pencarian.
         */
        if ($request->filled('search')) {
            $search = trim($request->search);

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

        /*
         * Filter status keanggotaan.
         */
        if ($request->filled('status')) {
            $query->where(
                'status_keanggotaan',
                $request->status
            );
        }

        /*
         * Filter tahun pendaftaran.
         */
        if ($request->filled('tahun')) {
            $query->whereYear(
                'tanggal_daftar',
                $request->tahun
            );
        }

        $anggota = $query
            ->orderByDesc('tanggal_daftar')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        /*
         * Ringkasan anggota.
         */
        $totalAnggota = Anggota::count();

        $anggotaAktif = Anggota::where(
            'status_keanggotaan',
            'aktif'
        )->count();

        $anggotaNonaktif = Anggota::where(
            'status_keanggotaan',
            'nonaktif'
        )->count();

        $tahunTersedia = Anggota::query()
            ->whereNotNull('tanggal_daftar')
            ->selectRaw('YEAR(tanggal_daftar) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view(
            'laporan.anggota',
            compact(
                'anggota',
                'totalAnggota',
                'anggotaAktif',
                'anggotaNonaktif',
                'tahunTersedia'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LAPORAN SIMPANAN
    |--------------------------------------------------------------------------
    */

    
public function simpanan(Request $request)
{
    $jenisSimpanan = JenisSimpanan::query()
        ->where('is_active', true)
        ->orderBy('nama_jenis')
        ->get();

    $setoran = Simpanan::query()
        ->with(['anggota.user', 'jenisSimpanan', 'pencatat'])
        ->where('status', 'disetujui')
        ->get()
        ->map(function ($item) {
            $namaJenis = $item->jenisSimpanan?->nama_jenis ?? '-';

            return [
                'id' => 'S-' . $item->id,
                'referensi_id' => $item->id,
                'anggota_id' => (int) $item->anggota_id,
                'kode_anggota' => $item->anggota?->kode_anggota ?? '-',
                'nama_anggota' => $item->anggota?->user?->nama ?? '-',
                'no_transaksi' => $item->no_simpanan,
                'jenis' => $namaJenis,
                'jenis_key' => $this->normalisasiJenisSimpanan($namaJenis),
                'tanggal' => $item->tanggal_transaksi,
                'jumlah' => (float) $item->jumlah,
                'arah' => 'masuk',
                'status' => 'disetujui',
                'keterangan' => $item->keterangan ?? 'Setoran simpanan',
            ];
        });

    $pencairan = PencairanSimpanan::query()
        ->with(['anggota.user'])
        ->where('status', 'selesai')
        ->get()
        ->map(function ($item) {
            $namaJenis = match ($item->jenis_simpanan) {
                'pokok' => 'Simpanan Pokok',
                'wajib' => 'Simpanan Wajib',
                'sukarela' => 'Simpanan Sukarela',
                default => ucfirst((string) $item->jenis_simpanan),
            };

            return [
                'id' => 'P-' . $item->id,
                'referensi_id' => $item->id,
                'anggota_id' => (int) $item->anggota_id,
                'kode_anggota' => $item->anggota?->kode_anggota ?? '-',
                'nama_anggota' => $item->anggota?->user?->nama ?? '-',
                'no_transaksi' => $item->no_pengajuan,
                'jenis' => $namaJenis,
                'jenis_key' => $this->normalisasiJenisSimpanan($namaJenis),
                'tanggal' => $item->tanggal_pencairan,
                'jumlah' => (float) $item->jumlah,
                'arah' => 'keluar',
                'status' => 'selesai',
                'keterangan' => $item->alasan ?? 'Pencairan simpanan',
            ];
        });

    /*
     * Gabungkan setoran dan pencairan menjadi satu riwayat.
     */
    $semuaTransaksi = $setoran
        ->concat($pencairan)
        ->sortBy(function ($item) {
            return sprintf(
                '%s-%010d',
                $item['tanggal'] ?? '0000-00-00',
                $item['referensi_id']
            );
        })
        ->values();

    /*
     * Hitung saldo berjalan per anggota dan jenis simpanan.
     */
    $saldoBerjalan = [];
    $semuaTransaksi = $semuaTransaksi->map(function ($item) use (&$saldoBerjalan) {
        $kunci = $item['anggota_id'] . ':' . $item['jenis_key'];

        $saldoBerjalan[$kunci] ??= 0;

        $saldoBerjalan[$kunci] += $item['arah'] === 'masuk'
            ? $item['jumlah']
            : -$item['jumlah'];

        $item['saldo_berjalan'] = $saldoBerjalan[$kunci];

        return $item;
    });

    /*
     * Terapkan filter pada riwayat yang ditampilkan.
     */
    $transaksiTampil = $semuaTransaksi;

    if ($request->filled('search')) {
        $search = mb_strtolower(trim($request->search));

        $transaksiTampil = $transaksiTampil->filter(function ($item) use ($search) {
            return str_contains(mb_strtolower($item['no_transaksi']), $search)
                || str_contains(mb_strtolower($item['kode_anggota']), $search)
                || str_contains(mb_strtolower($item['nama_anggota']), $search);
        });
    }

    if ($request->filled('jenis_simpanan_id')) {
        $jenisTerpilih = JenisSimpanan::find($request->jenis_simpanan_id);

        if ($jenisTerpilih) {
            $jenisKey = $this->normalisasiJenisSimpanan($jenisTerpilih->nama_jenis);

            $transaksiTampil = $transaksiTampil->filter(
                fn ($item) => $item['jenis_key'] === $jenisKey
            );
        }
    }

    if ($request->filled('dari')) {
        $transaksiTampil = $transaksiTampil->filter(
            fn ($item) => $item['tanggal'] >= $request->dari
        );
    }

    if ($request->filled('sampai')) {
        $transaksiTampil = $transaksiTampil->filter(
            fn ($item) => $item['tanggal'] <= $request->sampai
        );
    }

    $transaksiTampil = $transaksiTampil
        ->sortByDesc(fn ($item) => $item['tanggal'] . '-' . $item['referensi_id'])
        ->values();

    /*
     * Ringkasan mengikuti filter yang sedang aktif.
     */
    $totalSetoran = (float) $transaksiTampil
        ->where('arah', 'masuk')
        ->sum('jumlah');

    $totalPencairan = (float) $transaksiTampil
        ->where('arah', 'keluar')
        ->sum('jumlah');

    $totalSimpanan = $totalSetoran - $totalPencairan;
    $jumlahTransaksi = $transaksiTampil->count();

    $perHalaman = 20;
    $halaman = LengthAwarePaginator::resolveCurrentPage();
    $itemsHalaman = $transaksiTampil
        ->slice(($halaman - 1) * $perHalaman, $perHalaman)
        ->values();

    $simpanan = new LengthAwarePaginator(
        $itemsHalaman,
        $transaksiTampil->count(),
        $perHalaman,
        $halaman,
        [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]
    );

    return view('laporan.simpanan', compact(
        'simpanan',
        'totalSimpanan',
        'totalSetoran',
        'totalPencairan',
        'jumlahTransaksi',
        'jenisSimpanan'
    ));
}

private function normalisasiJenisSimpanan(string $nama): string
{
    $nama = mb_strtolower($nama);

    foreach (['pokok', 'wajib', 'sukarela'] as $jenis) {
        if (str_contains($nama, $jenis)) {
            return $jenis;
        }
    }

    return $nama;
}

/**
 * Ekspor laporan simpanan ke Excel.
 */
public function exportSimpanan(Request $request)
{
    $filters = $request->validate([
        'search' => ['nullable', 'string', 'max:100'],
        'jenis_simpanan_id' => ['nullable', 'integer', 'min:1'],
        'status' => [
        'nullable',
        'in:pending,disetujui,ditolak,selesai' ],
        'dari' => ['nullable', 'date_format:Y-m-d'],
        'sampai' => [
            'nullable',
            'date_format:Y-m-d',
            'after_or_equal:dari',
        ],
    ]);

    $namaFile = 'laporan-simpanan-' . now()->format('Y-m-d-His') . '.xlsx';

    return Excel::download(
        new LaporanSimpananExport($filters),
        $namaFile
    );
}


    /*
    |--------------------------------------------------------------------------
    | LAPORAN PINJAMAN
    |--------------------------------------------------------------------------
    */

    public function pinjaman(Request $request)
    {
        $query = Pinjaman::query()
            ->with([
                'anggota.user',
                'masterPinjaman',
            ]);

        /*
         * Filter pencarian.
         */
        if ($request->filled('search')) {
            $search = trim($request->search);

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

        /*
         * Filter status pinjaman.
         */
        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        /*
         * Filter tanggal pengajuan.
         */
        if ($request->filled('dari')) {
            $query->whereDate(
                'tanggal_pengajuan',
                '>=',
                $request->dari
            );
        }

        if ($request->filled('sampai')) {
            $query->whereDate(
                'tanggal_pengajuan',
                '<=',
                $request->sampai
            );
        }

        $pinjaman = $query
            ->orderByDesc('tanggal_pengajuan')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        /*
         * Ringkasan berdasarkan kondisi pinjaman.
         */
        $totalPengajuan = Pinjaman::count();

        $totalDisetujui = Pinjaman::where(
            'status',
            'disetujui'
        )->count();

        $totalDicairkan = Pinjaman::where(
            'status',
            'dicairkan'
        )->count();

        $totalLunas = Pinjaman::where(
            'status',
            'lunas'
        )->count();

        $totalDitolak = Pinjaman::where(
            'status',
            'ditolak'
        )->count();

        /*
         * Nilai pinjaman yang sudah dicairkan.
         */
        $nilaiDicairkan = (float) Pinjaman::where(
            'status',
            'dicairkan'
        )->sum('jumlah_disetujui');

        /*
         * Nilai pinjaman yang masih aktif.
         *
         * Disetujui dan dicairkan masih dianggap belum selesai.
         */
        $nilaiAktif = (float) Pinjaman::whereIn(
            'status',
            [
                'disetujui',
                'dicairkan',
            ]
        )->sum('jumlah_disetujui');

        return view(
            'laporan.pinjaman',
            compact(
                'pinjaman',
                'totalPengajuan',
                'totalDisetujui',
                'totalDicairkan',
                'totalLunas',
                'totalDitolak',
                'nilaiDicairkan',
                'nilaiAktif'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LAPORAN ANGSURAN
    |--------------------------------------------------------------------------
    */

    public function angsuran(Request $request)
    {
        $query = Angsuran::query()
            ->with([
                'pinjaman.anggota.user',
                'verifikator',
            ]);

        /*
         * Filter pencarian.
         */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function (Builder $q) use ($search) {
                $q->whereHas('pinjaman', function (Builder $pinjamanQuery) use ($search) {
                    $pinjamanQuery
                        ->where(
                            'no_pinjaman',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhereHas('anggota', function (Builder $anggotaQuery) use ($search) {
                            $anggotaQuery
                                ->where(
                                    'kode_anggota',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                                    $userQuery->where(
                                        'nama',
                                        'like',
                                        "%{$search}%"
                                    );
                                });
                        });
                });
            });
        }

        /*
         * Filter status pembayaran.
         */
        if ($request->filled('status')) {
            $query->where(
                'status_pembayaran',
                $request->status
            );
        }

        /*
         * Filter tanggal jatuh tempo.
         */
        if ($request->filled('dari')) {
            $query->whereDate(
                'tanggal_jatuh_tempo',
                '>=',
                $request->dari
            );
        }

        if ($request->filled('sampai')) {
            $query->whereDate(
                'tanggal_jatuh_tempo',
                '<=',
                $request->sampai
            );
        }

        $angsuran = $query
            ->orderByDesc('tanggal_jatuh_tempo')
            ->orderByDesc('angsuran_ke')
            ->paginate(20)
            ->withQueryString();

        /*
         * Ringkasan angsuran.
         */
        $totalAngsuran = Angsuran::count();

        $angsuranLunas = Angsuran::where(
            'status_pembayaran',
            'lunas'
        )->count();

        $angsuranBelumBayar = Angsuran::where(
            'status_pembayaran',
            'belum_bayar'
        )->count();

        $menungguVerifikasi = Angsuran::where(
            'status_pembayaran',
            'menunggu_verifikasi'
        )->count();

        $totalTagihan = (float) Angsuran::sum(
            'total_angsuran'
        );

        $totalDibayar = (float) Angsuran::where(
            'status_pembayaran',
            'lunas'
        )->sum('jumlah_bayar');

        return view(
            'laporan.angsuran',
            compact(
                'angsuran',
                'totalAngsuran',
                'angsuranLunas',
                'angsuranBelumBayar',
                'menungguVerifikasi',
                'totalTagihan',
                'totalDibayar'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LAPORAN KAS
    |--------------------------------------------------------------------------
    */

    public function kas(Request $request)
    {
        $query = Kas::query()
            ->with('pencatat');

        /*
         * Filter pencarian.
         */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function (Builder $q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhere('referensi_tipe', 'like', "%{$search}%");
            });
        }

        /*
         * Filter jenis transaksi.
         */
        if ($request->filled('jenis')) {
            $query->where(
                'jenis',
                $request->jenis
            );
        }

        /*
         * Filter kategori.
         */
        if ($request->filled('kategori')) {
            $query->where(
                'kategori',
                $request->kategori
            );
        }

        /*
         * Filter tanggal.
         */
        if ($request->filled('dari')) {
            $query->whereDate(
                'tanggal',
                '>=',
                $request->dari
            );
        }

        if ($request->filled('sampai')) {
            $query->whereDate(
                'tanggal',
                '<=',
                $request->sampai
            );
        }

        $kas = $query
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        /*
         * Ringkasan kas mengikuti filter tanggal dan kategori.
         */
        $ringkasanQuery = Kas::query();

        if ($request->filled('kategori')) {
            $ringkasanQuery->where(
                'kategori',
                $request->kategori
            );
        }

        if ($request->filled('dari')) {
            $ringkasanQuery->whereDate(
                'tanggal',
                '>=',
                $request->dari
            );
        }

        if ($request->filled('sampai')) {
            $ringkasanQuery->whereDate(
                'tanggal',
                '<=',
                $request->sampai
            );
        }

        $totalMasuk = (float) (clone $ringkasanQuery)
            ->where('jenis', 'masuk')
            ->sum('jumlah');

        $totalKeluar = (float) (clone $ringkasanQuery)
            ->where('jenis', 'keluar')
            ->sum('jumlah');

        $saldoPeriode = $totalMasuk - $totalKeluar;

        /*
         * Saldo kas terkini tetap mengambil saldo terakhir
         * dari buku kas, bukan hasil saldo periode.
         */
        $saldoTerkini = Kas::getSaldoTerkini();

        $kategoriTersedia = [
            'simpanan'   => 'Simpanan',
            'pinjaman'   => 'Pinjaman',
            'angsuran'   => 'Angsuran',
            'operasional' => 'Operasional',
            'shu'        => 'SHU',
            'modal'      => 'Modal',
            'lainnya'    => 'Lainnya',
        ];

        return view(
            'laporan.kas',
            compact(
                'kas',
                'totalMasuk',
                'totalKeluar',
                'saldoPeriode',
                'saldoTerkini',
                'kategoriTersedia'
            )
        );
    }
}
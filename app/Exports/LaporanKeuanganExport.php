<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanKeuanganExport implements WithMultipleSheets
{
    protected ?string $dari;
    protected ?string $sampai;

    public function __construct(?string $dari = null, ?string $sampai = null)
    {
        $this->dari = $dari;
        $this->sampai = $sampai;
    }

    public function sheets(): array
    {
        return [
            new RingkasanKeuanganSheet($this->dari, $this->sampai),
            new TransaksiKasSheet($this->dari, $this->sampai),
            new RiwayatAngsuranSheet($this->dari, $this->sampai),
        ];
    }
}

/**
 * Sheet pertama: Ringkasan Keuangan.
 */
class RingkasanKeuanganSheet implements FromArray, WithTitle
{
    protected ?string $dari;
    protected ?string $sampai;

    public function __construct(?string $dari, ?string $sampai)
    {
        $this->dari = $dari;
        $this->sampai = $sampai;
    }

    public function title(): string
    {
        return 'Ringkasan Keuangan';
    }

    public function array(): array
    {
        $kasTerakhir = DB::table('kas')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->first();

        $saldoKas = (float) ($kasTerakhir->saldo_sesudah ?? 0);

        $kas = DB::table('kas')
            ->when($this->dari, fn ($q) =>
                $q->whereDate('tanggal', '>=', $this->dari)
            )
            ->when($this->sampai, fn ($q) =>
                $q->whereDate('tanggal', '<=', $this->sampai)
            );

        $kasMasuk = (clone $kas)
            ->where('jenis', 'masuk')
            ->sum('jumlah');

        $kasKeluar = (clone $kas)
            ->where('jenis', 'keluar')
            ->sum('jumlah');

        $simpananMasuk = (clone $kas)
            ->where('jenis', 'masuk')
            ->where('kategori', 'simpanan')
            ->sum('jumlah');

        $simpananKeluar = (clone $kas)
            ->where('jenis', 'keluar')
            ->where('kategori', 'simpanan')
            ->sum('jumlah');

        $pinjaman = DB::table('pinjaman')
            ->when($this->dari, fn ($q) =>
                $q->whereDate('tanggal_pengajuan', '>=', $this->dari)
            )
            ->when($this->sampai, fn ($q) =>
                $q->whereDate('tanggal_pengajuan', '<=', $this->sampai)
            );

        $totalPengajuan = (clone $pinjaman)->count();

        $pinjamanAktif = (clone $pinjaman)
            ->where('status', 'dicairkan')
            ->count();

        $nilaiPinjamanAktif = (clone $pinjaman)
            ->where('status', 'dicairkan')
            ->sum(DB::raw('COALESCE(jumlah_disetujui, jumlah_pinjaman)'));

        $angsuran = DB::table('angsuran')
            ->when($this->dari, fn ($q) =>
                $q->whereDate('tanggal_jatuh_tempo', '>=', $this->dari)
            )
            ->when($this->sampai, fn ($q) =>
                $q->whereDate('tanggal_jatuh_tempo', '<=', $this->sampai)
            );

        $totalAngsuran = (clone $angsuran)->count();

        $angsuranLunas = (clone $angsuran)
            ->where('status_pembayaran', 'lunas')
            ->count();

        $menungguVerifikasi = (clone $angsuran)
            ->where('status_pembayaran', 'menunggu_verifikasi')
            ->count();

        $totalTagihan = (clone $angsuran)->sum('total_angsuran');

        $totalDibayar = (clone $angsuran)
            ->where('status_pembayaran', 'lunas')
            ->sum('jumlah_bayar');

        return [
            ['LAPORAN KEUANGAN KPGS'],
            ['Periode Mulai', $this->dari ?? 'Semua tanggal'],
            ['Periode Sampai', $this->sampai ?? 'Semua tanggal'],
            [],
            ['RINGKASAN KAS'],
            ['Keterangan', 'Nilai (Rp)'],
            ['Saldo Kas Terkini', $saldoKas],
            ['Total Kas Masuk', (float) $kasMasuk],
            ['Total Kas Keluar', (float) $kasKeluar],
            [],
            ['RINGKASAN SIMPANAN'],
            ['Simpanan Masuk', (float) $simpananMasuk],
            ['Simpanan Keluar', (float) $simpananKeluar],
            [],
            ['RINGKASAN PINJAMAN'],
            ['Jumlah Pengajuan', (int) $totalPengajuan],
            ['Jumlah Pinjaman Dicairkan', (int) $pinjamanAktif],
            ['Nilai Pinjaman Dicairkan (Rp)', (float) $nilaiPinjamanAktif],
            [],
            ['RINGKASAN ANGSURAN'],
            ['Jumlah Data Angsuran', (int) $totalAngsuran],
            ['Angsuran Lunas', (int) $angsuranLunas],
            ['Menunggu Verifikasi', (int) $menungguVerifikasi],
            ['Total Tagihan (Rp)', (float) $totalTagihan],
            ['Total Pembayaran Terverifikasi (Rp)', (float) $totalDibayar],
        ];
    }
}

/**
 * Sheet kedua: seluruh transaksi kas dalam periode.
 */
class TransaksiKasSheet implements FromArray, WithTitle
{
    protected ?string $dari;
    protected ?string $sampai;

    public function __construct(?string $dari, ?string $sampai)
    {
        $this->dari = $dari;
        $this->sampai = $sampai;
    }

    public function title(): string
    {
        return 'Transaksi Kas';
    }

    public function array(): array
    {
        $transaksi = DB::table('kas')
            ->when($this->dari, fn ($q) =>
                $q->whereDate('tanggal', '>=', $this->dari)
            )
            ->when($this->sampai, fn ($q) =>
                $q->whereDate('tanggal', '<=', $this->sampai)
            )
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get([
                'no_transaksi',
                'tanggal',
                'jenis',
                'kategori',
                'jumlah',
                'saldo_sebelum',
                'saldo_sesudah',
                'referensi_tipe',
                'referensi_id',
            ]);

        $rows = [[
            'No. Transaksi',
            'Tanggal',
            'Jenis',
            'Kategori',
            'Jumlah (Rp)',
            'Saldo Sebelum (Rp)',
            'Saldo Sesudah (Rp)',
            'Tipe Referensi',
            'ID Referensi',
        ]];

        foreach ($transaksi as $item) {
            $rows[] = [
                $item->no_transaksi,
                $item->tanggal,
                $item->jenis,
                $item->kategori,
                (float) $item->jumlah,
                (float) ($item->saldo_sebelum ?? 0),
                (float) ($item->saldo_sesudah ?? 0),
                $item->referensi_tipe,
                $item->referensi_id,
            ];
        }

        return $rows;
    }
}

/**
 * Sheet ketiga: seluruh riwayat angsuran dalam periode.
 */
class RiwayatAngsuranSheet implements FromArray, WithTitle
{
    protected ?string $dari;
    protected ?string $sampai;

    public function __construct(?string $dari, ?string $sampai)
    {
        $this->dari = $dari;
        $this->sampai = $sampai;
    }

    public function title(): string
    {
        return 'Riwayat Angsuran';
    }

    public function array(): array
    {
        $angsuran = DB::table('angsuran')
            ->join('pinjaman', 'pinjaman.id', '=', 'angsuran.pinjaman_id')
            ->leftJoin('anggota', 'anggota.id', '=', 'pinjaman.anggota_id')
            ->leftJoin('users', 'users.id', '=', 'anggota.user_id')
            ->when($this->dari, fn ($q) =>
                $q->whereDate(
                    'angsuran.tanggal_jatuh_tempo',
                    '>=',
                    $this->dari
                )
            )
            ->when($this->sampai, fn ($q) =>
                $q->whereDate(
                    'angsuran.tanggal_jatuh_tempo',
                    '<=',
                    $this->sampai
                )
            )
            ->orderBy('angsuran.tanggal_jatuh_tempo')
            ->orderBy('angsuran.id')
            ->get([
                'pinjaman.no_pinjaman',
                'users.nama as nama_anggota',
                'angsuran.angsuran_ke',
                'angsuran.tanggal_jatuh_tempo',
                'angsuran.total_angsuran',
                'angsuran.jumlah_bayar',
                'angsuran.status_pembayaran',
            ]);

        $rows = [[
            'No. Pinjaman',
            'Nama Anggota',
            'Angsuran Ke',
            'Tanggal Jatuh Tempo',
            'Total Angsuran (Rp)',
            'Jumlah Dibayar (Rp)',
            'Status Pembayaran',
        ]];

        foreach ($angsuran as $item) {
            $rows[] = [
                $item->no_pinjaman,
                $item->nama_anggota ?? '-',
                $item->angsuran_ke,
                $item->tanggal_jatuh_tempo,
                (float) $item->total_angsuran,
                (float) ($item->jumlah_bayar ?? 0),
                $item->status_pembayaran,
            ];
        }

        return $rows;
    }
}

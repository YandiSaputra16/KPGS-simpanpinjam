<?php

namespace App\Exports;

use App\Models\Angsuran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanAngsuranExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $query = Angsuran::query()
            ->with([
                'pinjaman.anggota.user',
                'verifikator',
            ]);

        // Filter pencarian.
        if (!empty($this->filters['search'])) {
            $search = trim($this->filters['search']);

            $query->where(function ($q) use ($search) {
                $q->whereHas('pinjaman', function ($pinjamanQuery) use ($search) {
                    $pinjamanQuery
                        ->where('no_pinjaman', 'like', "%{$search}%")
                        ->orWhereHas('anggota', function ($anggotaQuery) use ($search) {
                            $anggotaQuery
                                ->where('kode_anggota', 'like', "%{$search}%")
                                ->orWhereHas('user', function ($userQuery) use ($search) {
                                    $userQuery->where('nama', 'like', "%{$search}%");
                                });
                        });
                });
            });
        }

        // Filter status pembayaran.
        if (!empty($this->filters['status'])) {
            $query->where(
                'status_pembayaran',
                $this->filters['status']
            );
        }

        // Filter tanggal jatuh tempo.
        if (!empty($this->filters['dari'])) {
            $query->whereDate(
                'tanggal_jatuh_tempo',
                '>=',
                $this->filters['dari']
            );
        }

        if (!empty($this->filters['sampai'])) {
            $query->whereDate(
                'tanggal_jatuh_tempo',
                '<=',
                $this->filters['sampai']
            );
        }

        return $query
            ->orderByDesc('tanggal_jatuh_tempo')
            ->orderByDesc('angsuran_ke')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Pinjaman',
            'Kode Anggota',
            'Nama Anggota',
            'Angsuran Ke',
            'Tanggal Jatuh Tempo',
            'Pokok',
            'Bunga',
            'Denda',
            'Total Tagihan',
            'Jumlah Dibayar',
            'Tanggal Pembayaran',
            'Status Pembayaran',
            'Verifikator',
            'Tanggal Verifikasi',
        ];
    }

    public function map($angsuran): array
    {
        $status = match ($angsuran->status_pembayaran) {
            'belum_bayar' => 'Belum Bayar',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'lunas' => 'Lunas',
            'ditolak' => 'Ditolak',
            default => ucfirst(str_replace(
                '_',
                ' ',
                (string) $angsuran->status_pembayaran
            )),
        };

        return [
            $angsuran->id,
            $angsuran->pinjaman?->no_pinjaman ?? '-',
            $angsuran->pinjaman?->anggota?->kode_anggota ?? '-',
            $angsuran->pinjaman?->anggota?->user?->nama ?? '-',
            $angsuran->angsuran_ke,
            $angsuran->tanggal_jatuh_tempo
                ? $angsuran->tanggal_jatuh_tempo->format('d/m/Y')
                : '-',
            (float) $angsuran->jumlah_pokok,
            (float) $angsuran->jumlah_bunga,
            (float) $angsuran->denda,
            (float) $angsuran->total_tagihan,
            $angsuran->jumlah_bayar !== null
                ? (float) $angsuran->jumlah_bayar
                : 0,
            $angsuran->tanggal_bayar
                ? $angsuran->tanggal_bayar->format('d/m/Y')
                : '-',
            $status,
            $angsuran->verifikator?->nama ?? '-',
            $angsuran->tanggal_verifikasi
                ? $angsuran->tanggal_verifikasi->format('d/m/Y H:i')
                : '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '0D6EFD'],
                ],
            ],
        ];
    }
}

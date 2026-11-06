<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPinjamanExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
{
    protected Collection $pinjaman;

    public function __construct(Collection $pinjaman)
    {
        $this->pinjaman = $pinjaman;
    }

    public function collection(): Collection
    {
        return $this->pinjaman;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Pinjaman',
            'Kode Anggota',
            'Nama Anggota',
            'Tanggal Pengajuan',
            'Jumlah Pengajuan',
            'Jumlah Disetujui',
            'Status',
        ];
    }

    public function map($pinjaman): array
    {
        $status = match ($pinjaman->status) {
            'menunggu_cs' => 'Menunggu CS',
            'menunggu_ao' => 'Menunggu AO',
            'menunggu_kabag' => 'Menunggu Kabag',
            'disetujui' => 'Disetujui',
            'dicairkan' => 'Dicairkan',
            'lunas' => 'Lunas',
            'ditolak' => 'Ditolak',
            default => ucfirst(str_replace(
                '_',
                ' ',
                (string) $pinjaman->status
            )),
        };

        return [
            $pinjaman->id,
            $pinjaman->no_pinjaman ?? '-',
            $pinjaman->anggota?->kode_anggota ?? '-',
            $pinjaman->anggota?->user?->nama ?? '-',
            $pinjaman->tanggal_pengajuan
                ? $pinjaman->tanggal_pengajuan->format('d/m/Y')
                : '-',
            $pinjaman->jumlah_pinjaman !== null
                ? (float) $pinjaman->jumlah_pinjaman
                : 0,
            $pinjaman->jumlah_disetujui !== null
                ? (float) $pinjaman->jumlah_disetujui
                : null,
            $status,
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

<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanAnggotaExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
{
    protected Collection $anggota;

    public function __construct(Collection $anggota)
    {
        $this->anggota = $anggota;
    }

    public function collection(): Collection
    {
        return $this->anggota;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Anggota',
            'Nama',
            'NIK',
            'Nomor HP',
            'Tanggal Daftar',
            'Status Keanggotaan',
        ];
    }

    public function map($anggota): array
    {
        return [
            $anggota->id,
            $anggota->kode_anggota ?? '-',
            $anggota->user?->nama ?? '-',
            $anggota->nik ?? '-',
            $anggota->no_hp ?? '-',
            $anggota->tanggal_daftar
                ? $anggota->tanggal_daftar->format('d/m/Y')
                : '-',
            $anggota->status_keanggotaan === 'aktif'
                ? 'Aktif'
                : 'Nonaktif',
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


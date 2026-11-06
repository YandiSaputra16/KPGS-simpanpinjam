<?php

namespace App\Exports;

use App\Models\JenisSimpanan;
use App\Models\PencairanSimpanan;
use App\Models\Simpanan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanSimpananExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection(): Collection
    {
        $transaksi = collect();

        /*
         * SETORAN SIMPANAN
         * Hanya setoran yang sudah disetujui.
         */
        $setoran = Simpanan::query()
            ->with(['anggota.user', 'jenisSimpanan'])
            ->where('status', 'disetujui')
            ->get()
            ->map(function ($item) {
                $namaJenis = $item->jenisSimpanan?->nama_jenis ?? '-';

                return [
                    'referensi_id' => (int) $item->id,
                    'no_transaksi' => $item->no_simpanan ?? '-',
                    'kode_anggota' => $item->anggota?->kode_anggota ?? '-',
                    'nama_anggota' => $item->anggota?->user?->nama ?? '-',
                    'jenis' => $namaJenis,
                    'jenis_key' => $this->normalisasiJenis($namaJenis),
                    'tanggal' => $item->tanggal_transaksi
                        ? $item->tanggal_transaksi->format('Y-m-d')
                        : null,
                    'arah' => 'Setoran',
                    'jumlah' => (float) $item->jumlah,
                    'status' => 'Disetujui',
                    'keterangan' => $item->keterangan ?? '-',
                ];
            });

        /*
         * PENARIKAN SIMPANAN
         * Hanya pencairan yang sudah selesai dibayarkan.
         */
        $pencairan = PencairanSimpanan::query()
            ->with(['anggota.user'])
            ->where('status', 'selesai')
            ->get()
            ->map(function ($item) {
                $namaJenis = $item->nama_jenis_simpanan;

                return [
                    'referensi_id' => (int) $item->id,
                    'no_transaksi' => $item->no_pengajuan ?? '-',
                    'kode_anggota' => $item->anggota?->kode_anggota ?? '-',
                    'nama_anggota' => $item->anggota?->user?->nama ?? '-',
                    'jenis' => $namaJenis,
                    'jenis_key' => $this->normalisasiJenis($namaJenis),
                    'tanggal' => $item->tanggal_pencairan
                        ? $item->tanggal_pencairan->format('Y-m-d')
                        : null,
                    'arah' => 'Penarikan',
                    'jumlah' => (float) $item->jumlah,
                    'status' => 'Selesai',
                    'keterangan' => $item->alasan ?? 'Pencairan simpanan',
                ];
            });

        $transaksi = $setoran
            ->concat($pencairan)
            ->filter(function (array $item) {
                /*
                 * Filter pencarian.
                 */
                if (!empty($this->filters['search'])) {
                    $search = mb_strtolower(
                        trim($this->filters['search'])
                    );

                    $teksPencarian = [
                        $item['no_transaksi'],
                        $item['kode_anggota'],
                        $item['nama_anggota'],
                    ];

                    $cocok = false;

                    foreach ($teksPencarian as $teks) {
                        if (
                            str_contains(
                                mb_strtolower((string) $teks),
                                $search
                            )
                        ) {
                            $cocok = true;
                            break;
                        }
                    }

                    if (!$cocok) {
                        return false;
                    }
                }

                /*
                 * Filter jenis simpanan.
                 */
                if (!empty($this->filters['jenis_simpanan_id'])) {
                    $jenisTerpilih = JenisSimpanan::find(
                        $this->filters['jenis_simpanan_id']
                    );

                    if (!$jenisTerpilih) {
                        return false;
                    }

                    $jenisKey = $this->normalisasiJenis(
                        $jenisTerpilih->nama_jenis
                    );

                    if ($item['jenis_key'] !== $jenisKey) {
                        return false;
                    }
                }

                /*
                 * Filter tanggal transaksi.
                 */
                if (
                    !empty($this->filters['dari'])
                    && (
                        !$item['tanggal']
                        || $item['tanggal'] < $this->filters['dari']
                    )
                ) {
                    return false;
                }

                if (
                    !empty($this->filters['sampai'])
                    && (
                        !$item['tanggal']
                        || $item['tanggal'] > $this->filters['sampai']
                    )
                ) {
                    return false;
                }

                /*
                 * Filter status bila dipilih.
                 */
                if (!empty($this->filters['status'])) {
                    $statusFilter = $this->filters['status'];

                    $statusTransaksi = match ($item['status']) {
                        'Disetujui' => 'disetujui',
                        'Selesai' => 'selesai',
                        default => mb_strtolower($item['status']),
                    };

                    if ($statusTransaksi !== $statusFilter) {
                        return false;
                    }
                }

                return true;
            })
            ->sort(function (array $a, array $b) {
                $tanggalA = $a['tanggal'] ?? '0000-00-00';
                $tanggalB = $b['tanggal'] ?? '0000-00-00';

                $hasilTanggal = strcmp($tanggalB, $tanggalA);

                if ($hasilTanggal !== 0) {
                    return $hasilTanggal;
                }

                return $b['referensi_id'] <=> $a['referensi_id'];
            })
            ->values();

        /*
         * Bentuk baris Excel dan nomor urut.
         */
        return $transaksi
            ->values()
            ->map(function (array $item, int $index) {
                return [
                    $index + 1,
                    $item['no_transaksi'],
                    $item['kode_anggota'],
                    $item['nama_anggota'],
                    $item['arah'],
                    $item['jenis'],
                    $item['tanggal']
                        ? \Illuminate\Support\Carbon::parse(
                            $item['tanggal']
                        )->format('d/m/Y')
                        : '-',
                    $item['jumlah'],
                    $item['status'],
                    $item['keterangan'],
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No.',
            'Nomor Transaksi',
            'Kode Anggota',
            'Nama Anggota',
            'Jenis Transaksi',
            'Jenis Simpanan',
            'Tanggal Transaksi',
            'Jumlah (Rp)',
            'Status',
            'Keterangan',
        ];
    }

    private function normalisasiJenis(string $nama): string
    {
        $nama = mb_strtolower($nama);

        foreach (['pokok', 'wajib', 'sukarela'] as $jenis) {
            if (str_contains($nama, $jenis)) {
                return $jenis;
            }
        }

        return $nama;
    }
}

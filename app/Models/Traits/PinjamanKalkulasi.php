<?php

namespace App\Models\Traits;

use App\Models\Angsuran;

trait PinjamanKalkulasi
{
    public static function hitungAngsuran(
        float $jumlah,
        int $tenor,
        float $bungaPersen
    ): array {
        if ($jumlah <= 0) {
            throw new \InvalidArgumentException(
                'Jumlah pinjaman harus lebih besar dari 0.'
            );
        }

        if ($tenor <= 0) {
            throw new \InvalidArgumentException(
                'Tenor pinjaman harus lebih besar dari 0.'
            );
        }

        if ($bungaPersen < 0) {
            throw new \InvalidArgumentException(
                'Persentase bunga tidak boleh negatif.'
            );
        }

        $bungaPerBulan = $jumlah * ($bungaPersen / 100);

        $pokokPerBulan = $jumlah / $tenor;

        $angsuranPerBulan = $pokokPerBulan + $bungaPerBulan;

        $totalBunga = $bungaPerBulan * $tenor;

        $totalAngsuran = $jumlah + $totalBunga;

        return [
            'angsuran_per_bulan' => round($angsuranPerBulan, 2),
            'pokok_per_bulan'    => round($pokokPerBulan, 2),
            'bunga_per_bulan'    => round($bungaPerBulan, 2),
            'total_bunga'        => round($totalBunga, 2),
            'total_angsuran'     => round($totalAngsuran, 2),
        ];
    }

    /**Membuat jadwal angsuran setelah pinjaman dicairkan.*/
    public function generateJadwalAngsuran(): void
    {
        if ($this->angsuran()->exists()) {
            return;
        }

        if ($this->jumlah_disetujui === null) {
            throw new \Exception(
                'Jadwal angsuran tidak dapat dibuat karena nominal pinjaman belum disetujui Kabag.'
            );
        }

        if ((float) $this->jumlah_disetujui <= 0) {
            throw new \Exception(
                'Nominal pinjaman yang disetujui harus lebih besar dari 0.'
            );
        }

        if (!$this->tanggal_cair) {
            throw new \Exception(
                'Jadwal angsuran tidak dapat dibuat karena tanggal pencairan belum tersedia.'
            );
        }

        if ((int) $this->tenor <= 0) {
            throw new \Exception(
                'Tenor pinjaman tidak valid.'
            );
        }

        $jumlah = (float) $this->jumlah_disetujui;
        $tenor = (int) $this->tenor;
        $bungaPersen = (float) $this->bunga_persen;

        $hitung = self::hitungAngsuran(
            $jumlah,
            $tenor,
            $bungaPersen
        );

        $totalBunga = $hitung['total_bunga'];

        $totalKewajiban = $hitung['total_angsuran'];
       
        $this->angsuran_per_bulan = $hitung['angsuran_per_bulan'];

        $this->total_bunga = $totalBunga;

        $this->total_angsuran = $totalKewajiban;

        $this->sisa_pinjaman = $totalKewajiban;

        $this->save();

        $totalTerjadwal = 0;

        for ($ke = 1; $ke <= $tenor; $ke++) {
            $jumlahPokok = $hitung['pokok_per_bulan'];
            $jumlahBunga = $hitung['bunga_per_bulan'];
            $totalAngsuran = $hitung['angsuran_per_bulan'];

            if ($ke === $tenor) {
                $totalAngsuran = round(
                    $totalKewajiban - $totalTerjadwal,
                    2
                );

                $jumlahPokok = round(
                    $totalAngsuran - $jumlahBunga,
                    2
                );
            }

            $totalTerjadwal = round(
                $totalTerjadwal + $totalAngsuran,
                2
            );

            $sisaSetelahAngsuran = max(
                0,
                round(
                    $totalKewajiban - $totalTerjadwal,
                    2
                )
            );

            Angsuran::create([
                'pinjaman_id' => $this->id,
                'angsuran_ke' => $ke,

                'tanggal_jatuh_tempo' =>
                    $this->tanggal_cair
                        ->copy()
                        ->addMonths($ke),

                'jumlah_pokok' =>
                    round($jumlahPokok, 2),

                'jumlah_bunga' =>
                    round($jumlahBunga, 2),

                'total_angsuran' =>
                    round($totalAngsuran, 2),

                'denda' => 0,

                'jumlah_bayar' => 0,

                'sisa_pinjaman' =>
                    $sisaSetelahAngsuran,

                'hari_terlambat' => 0,

                'status_pembayaran' => 'belum_bayar',
            ]);
        }
    }

    public static function generateNomor(): string
    {
        $tahun = now()->year;

        $last = static::withTrashed()
            ->whereYear('created_at', $tahun)
            ->orderBy('id', 'desc')
            ->first();

        $urutan = $last
            ? ((int) substr($last->no_pinjaman, -4)) + 1
            : 1;

        return 'PIN-' . $tahun . '-'
            . str_pad($urutan, 4, '0', STR_PAD_LEFT);
    }
}
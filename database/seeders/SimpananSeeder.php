<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Simpanan;
use App\Models\User;
use Illuminate\Database\Seeder;

class SimpananSeeder extends Seeder
{
    public function run(): void
    {
        $bendahara = User::query()
            ->where('role', 'bendahara')
            ->where('status', 'aktif')
            ->first();

        if (!$bendahara) {
            $this->command?->warn(
                'SimpananSeeder dilewati: user Bendahara aktif tidak ditemukan.'
            );

            return;
        }

        $pokok = JenisSimpanan::query()
            ->where('kode', 'POKOK')
            ->where('is_active', true)
            ->first();

        $wajib = JenisSimpanan::query()
            ->where('kode', 'WAJIB')
            ->where('is_active', true)
            ->first();

        if (!$pokok || !$wajib) {
            $this->command?->warn(
                'SimpananSeeder dilewati: jenis simpanan POKOK/WAJIB belum tersedia.'
            );

            return;
        }

        foreach (Anggota::aktif()->get() as $anggota) {
            $this->buatJikaBelumAda(
                $anggota,
                $pokok,
                100000,
                'Simpanan pokok awal anggota',
                $bendahara->id
            );

            $this->buatJikaBelumAda(
                $anggota,
                $wajib,
                50000,
                'Simpanan wajib pertama',
                $bendahara->id
            );
        }
    }

    private function buatJikaBelumAda(
        Anggota $anggota,
        JenisSimpanan $jenis,
        float $jumlah,
        string $keterangan,
        int $dicatatOleh
    ): void {
        $sudahAda = Simpanan::query()
            ->where('anggota_id', $anggota->id)
            ->where('jenis_simpanan_id', $jenis->id)
            ->where('keterangan', $keterangan)
            ->exists();

        if ($sudahAda) {
            return;
        }

        Simpanan::catatTransaksi([
            'anggota_id' => $anggota->id,
            'jenis_simpanan_id' => $jenis->id,
            'jumlah' => $jumlah,
            'tanggal_transaksi' => now()->toDateString(),
            'metode_pembayaran' => 'tunai',
            'keterangan' => $keterangan,
            'dicatat_oleh' => $dicatatOleh,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\MasterPinjaman;
use App\Models\Pinjaman;
use App\Models\DataPendukungPinjaman;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PinjamanSeeder extends Seeder
{
    public function run(): void
    {
        $anggota = Anggota::query()
            ->orderBy('id')
            ->first();

        $master = MasterPinjaman::query()
            ->where('kode_pinjaman', 'PMK')
            ->where('is_active', true)
            ->first();

        if (!$anggota || !$master) {
            $this->command?->warn(
                'PinjamanSeeder dilewati: anggota atau master pinjaman tidak tersedia.'
            );

            return;
        }

        // Identitas khusus agar data contoh tidak dibuat berulang.
        $nomorPinjaman = 'PIN-SEED-' . now()->year . '-0001';

        if (Pinjaman::withTrashed()
            ->where('no_pinjaman', $nomorPinjaman)
            ->exists()) {
            $this->command?->info(
                'Data pinjaman contoh sudah tersedia.'
            );

            return;
        }

        $jumlah = 2000000;
        $tenor = 6;

        if (
            $jumlah < $master->plafon_min ||
            $jumlah > $master->plafon_max ||
            $tenor < $master->tenor_min ||
            $tenor > $master->tenor_max
        ) {
            $this->command?->warn(
                'PinjamanSeeder dilewati: nominal atau tenor tidak sesuai paket.'
            );

            return;
        }

        $hitung = Pinjaman::hitungAngsuran(
            $jumlah,
            $tenor,
            (float) $master->bunga_persen
        );

        DB::transaction(function () use (
            $anggota,
            $master,
            $nomorPinjaman,
            $jumlah,
            $tenor,
            $hitung
        ) {
            $pinjaman = Pinjaman::create([
                'no_pinjaman' => $nomorPinjaman,
                'anggota_id' => $anggota->id,
                'master_pinjaman_id' => $master->id,

                'jumlah_pinjaman' => $jumlah,
                'jumlah_rekomendasi' => null,
                'jumlah_disetujui' => null,

                'tenor' => $tenor,
                'bunga_persen' => $master->bunga_persen,
                'denda_persen' => $master->denda_persen,

                'angsuran_per_bulan' => $hitung['angsuran_per_bulan'],
                'total_bunga' => $hitung['total_bunga'],
                'total_angsuran' => $hitung['total_angsuran'],
                'sisa_pinjaman' => $jumlah,

                'tanggal_pengajuan' => today(),
                'tanggal_cair' => null,
                'tanggal_jatuh_tempo' => null,

                'tujuan_pinjaman' => 'Modal usaha ternak',

                'disetujui_syarat' => true,
                'waktu_setujui_syarat' => now(),

                'status' => 'menunggu_cs',
            ]);

            DataPendukungPinjaman::create([
                'pinjaman_id' => $pinjaman->id,
                'produksi_susu_bulanan' => 1000,
                'jumlah_sapi' => 5,
                'kondisi_usaha' => 'baik',
                'pendapatan_bulanan' => 5000000,
                'kewajiban_angsuran_lain' => 0,
                'keterangan_tambahan' => 'Data contoh untuk pengujian.',
            ]);
        });

        $this->command?->info(
            'Data pinjaman contoh berhasil dibuat.'
        );
    }
}

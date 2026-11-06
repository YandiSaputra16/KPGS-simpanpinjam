<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterPinjaman;

class MasterPinjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [

            [
                'kode_pinjaman' => 'PMK',
                'nama_paket'    => 'Pinjaman Mikro',
                'plafon_min'    => 500000,
                'plafon_max'    => 3000000,
                'tenor_min'     => 1,
                'tenor_max'     => 6,
                'bunga_persen'  => 2.5,
                'syarat'        => 'Fotokopi KTP dan telah menjadi anggota minimal 3 bulan',
                'is_active'     => true,
            ],

            [
                'kode_pinjaman' => 'PRG',
                'nama_paket'    => 'Pinjaman Reguler',
                'plafon_min'    => 3000000,
                'plafon_max'    => 10000000,
                'tenor_min'     => 3,
                'tenor_max'     => 12,
                'bunga_persen'  => 2.0,
                'syarat'        => 'Fotokopi KTP, KK, dan slip usaha/pendapatan',
                'is_active'     => true,
            ],

            [
                'kode_pinjaman' => 'PUS',
                'nama_paket'    => 'Pinjaman Usaha',
                'plafon_min'    => 10000000,
                'plafon_max'    => 50000000,
                'tenor_min'     => 6,
                'tenor_max'     => 24,
                'bunga_persen'  => 1.5,
                'syarat'        => 'Proposal usaha dan jaminan koperasi',
                'is_active'     => true,
            ],

        ];

        foreach ($data as $item) {
            MasterPinjaman::firstOrCreate(
                ['kode_pinjaman' => $item['kode_pinjaman']],
                $item
            );
        }

        $this->command?->info(
            'Master paket pinjaman berhasil diperiksa.'
        );
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSimpanan;

class JenisSimpananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_jenis' => 'Simpanan Pokok',
                'kode' => 'POKOK',
                'keterangan' => 'Simpanan awal saat menjadi anggota koperasi',
                'is_active' => true,
            ],
            [
                'nama_jenis' => 'Simpanan Wajib',
                'kode' => 'WAJIB',
                'keterangan' => 'Simpanan rutin anggota setiap bulan',
                'is_active' => true,
            ],
            [
                'nama_jenis' => 'Simpanan Sukarela',
                'kode' => 'SUKARELA',
                'keterangan' => 'Simpanan tambahan yang bersifat sukarela',
                'is_active' => true,
            ],
        ];

        foreach ($data as $item) {
            JenisSimpanan::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }
    }
}
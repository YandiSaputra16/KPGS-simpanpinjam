<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengaturan;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pengaturan::firstOrCreate(
            ['id' => 1],
            [
                'nama_koperasi'       => 'Koperasi Peternak Garut Selatan (KPGS)',
                'alamat'              => 'Garut Selatan, Jawa Barat',
                'telepon'             => '-',
                'email'               => 'kpgs@example.com',
                'tentang'             => 'Sistem informasi koperasi simpan pinjam berbasis web.',
                'visi'                => 'Menjadi koperasi modern, transparan, dan terpercaya.',
                'misi'                => 'Meningkatkan pelayanan koperasi berbasis digital.',
                'instagram'           => null,
                'facebook'            => null,
                'whatsapp'            => null,
                'modal_awal'          => 0,
                'total_inventaris'    => 0,
                'shu_ditahan'         => 0,
                'cadangan_umum'       => 0,
                'google_maps_api_key' => null,
                'fonnte_token'        => null,
            ]
        );

        $this->command?->info(
            'Pengaturan koperasi berhasil diperiksa.'
        );
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Menjalankan seeder utama aplikasi KPGS.
     *
     * Seeder transaksi dan anggota dijalankan secara terpisah.
     */
    public function run(): void
    {
        $this->call([
            PengaturanSeeder::class,
            UserSeeder::class,
            JenisSimpananSeeder::class,
            MasterPinjamanSeeder::class,
        ]);
    }
}

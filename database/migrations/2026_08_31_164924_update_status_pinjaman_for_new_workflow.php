<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE pinjaman
            MODIFY status ENUM(
                'menunggu_cs',
                'menunggu_ao',
                'menunggu_kabag',
                'disetujui',
                'ditolak',
                'dicairkan',
                'lunas'
            ) NOT NULL DEFAULT 'menunggu_cs'
        ");
    }

    /**
     * Kembalikan perubahan.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE pinjaman
            MODIFY status ENUM(
                'menunggu_bendahara',
                'menunggu_pimpinan',
                'disetujui',
                'ditolak',
                'dicairkan',
                'lunas'
            ) NOT NULL DEFAULT 'menunggu_bendahara'
        ");
    }
};
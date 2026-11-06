<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE approval_pinjaman
            MODIFY level ENUM('ao', 'kabag') NOT NULL
        ");

        DB::statement("
            ALTER TABLE approval_pinjaman
            MODIFY status ENUM(
                'pending',
                'direkomendasikan',
                'disetujui',
                'ditolak'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Kembalikan data baru ke nilai yang masih didukung
        // sebelum mengembalikan struktur enum.
        DB::statement("
            UPDATE approval_pinjaman
            SET status = 'disetujui'
            WHERE status = 'direkomendasikan'
        ");

        DB::statement("
            DELETE FROM approval_pinjaman
            WHERE level = 'ao'
        ");

        DB::statement("
            ALTER TABLE approval_pinjaman
            MODIFY level ENUM('kabag') NOT NULL
        ");

        DB::statement("
            ALTER TABLE approval_pinjaman
            MODIFY status ENUM(
                'pending',
                'disetujui',
                'ditolak'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};
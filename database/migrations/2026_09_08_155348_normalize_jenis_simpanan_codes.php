<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('jenis_simpanan')
            ->where('kode', 'SP')
            ->update([
                'kode' => 'POKOK',
            ]);

        DB::table('jenis_simpanan')
            ->where('kode', 'SW')
            ->update([
                'kode' => 'WAJIB',
            ]);

        DB::table('jenis_simpanan')
            ->where('kode', 'SS')
            ->update([
                'kode' => 'SUKARELA',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('jenis_simpanan')
            ->where('kode', 'POKOK')
            ->update([
                'kode' => 'SP',
            ]);

        DB::table('jenis_simpanan')
            ->where('kode', 'WAJIB')
            ->update([
                'kode' => 'SW',
            ]);

        DB::table('jenis_simpanan')
            ->where('kode', 'SUKARELA')
            ->update([
                'kode' => 'SS',
            ]);
    }
};
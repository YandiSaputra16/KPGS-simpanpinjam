<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom di tabel pinjaman
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->boolean('disetujui_syarat')
                  ->default(false)
                  ->after('tujuan_pinjaman');

            $table->timestamp('waktu_setujui_syarat')
                  ->nullable()
                  ->after('disetujui_syarat');

            $table->string('ip_setujui_syarat', 45)
                  ->nullable()
                  ->after('waktu_setujui_syarat');
        });

        // Tambah kolom di tabel pengaturan
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->longText('teks_perjanjian_pinjaman')
                  ->nullable()
                  ->after('maps_embed');
        });
    }

    public function down(): void
    {
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->dropColumn([
                'disetujui_syarat',
                'waktu_setujui_syarat',
                'ip_setujui_syarat',
            ]);
        });

        Schema::table('pengaturan', function (Blueprint $table) {
            $table->dropColumn('teks_perjanjian_pinjaman');
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->text('sejarah')
                ->nullable()
                ->after('tentang');

            $table->text('layanan')
                ->nullable()
                ->after('misi');

            $table->text('syarat_anggota')
                ->nullable()
                ->after('layanan');

            $table->text('prosedur_pinjaman')
                ->nullable()
                ->after('syarat_anggota');
        });
    }

    public function down(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->dropColumn([
                'sejarah',
                'layanan',
                'syarat_anggota',
                'prosedur_pinjaman',
            ]);
        });
    }
};
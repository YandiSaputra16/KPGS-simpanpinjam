<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->string('nama_bank', 100)->nullable()->after('pekerjaan');
            $table->string('nomor_rekening', 50)->nullable()->after('nama_bank');
            $table->string('atas_nama_rekening', 150)->nullable()->after('nomor_rekening');
        });
    }

    public function down(): void
    {
        Schema::table('anggota', function (Blueprint $table) {
            $table->dropColumn([
                'nama_bank',
                'nomor_rekening',
                'atas_nama_rekening',
            ]);
        });
    }
};
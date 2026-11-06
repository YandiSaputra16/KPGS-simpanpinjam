<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_pendukung_pinjaman', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pinjaman_id')
                ->unique()
                ->constrained('pinjaman')
                ->cascadeOnDelete();

            $table->decimal('produksi_susu_bulanan', 12, 2);

            $table->unsignedInteger('jumlah_sapi');

            $table->enum('kondisi_usaha', [
                'baik',
                'cukup',
                'kurang',
            ]);

            $table->decimal('pendapatan_bulanan', 15, 2);

            $table->decimal('kewajiban_angsuran_lain', 15, 2)
                ->default(0);

            $table->text('keterangan_tambahan')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_pendukung_pinjaman');
    }
};
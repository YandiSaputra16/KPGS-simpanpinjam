<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('angsuran', function (Blueprint $table) {

            $table->id();

            // Relasi ke pinjaman
            $table->foreignId('pinjaman_id')
                ->constrained('pinjaman')
                ->onDelete('cascade');

            // urutan cicilan
            $table->unsignedTinyInteger('angsuran_ke');

            // jadwal pembayaran
            $table->date('tanggal_jatuh_tempo');

            // komponen cicilan
            $table->decimal('jumlah_pokok', 15, 2);
            $table->decimal('jumlah_bunga', 15, 2);
            $table->decimal('total_angsuran', 15, 2);

            // denda jika telat
            $table->decimal('denda', 15, 2)->default(0);

            // pembayaran aktual
            $table->decimal('jumlah_bayar', 15, 2)->default(0);
            $table->date('tanggal_bayar')->nullable();
            $table->string('bukti_pembayaran')->nullable();

            // hitung keterlambatan (penting untuk denda)
            $table->unsignedInteger('hari_terlambat')->default(0);

            // status pembayaran
            $table->enum('status_pembayaran', [
                'belum_bayar',
                'menunggu_verifikasi',
                'lunas',
                'ditolak'
            ])->default('belum_bayar');

            // verifikasi bendahara
            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('tanggal_verifikasi')->nullable();

            $table->timestamps();

            // satu pinjaman tidak boleh punya cicilan dobel
            $table->unique(['pinjaman_id', 'angsuran_ke']);

            // index untuk performa laporan
            $table->index(['pinjaman_id', 'status_pembayaran']);
            $table->index('tanggal_jatuh_tempo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('angsuran');
    }
};
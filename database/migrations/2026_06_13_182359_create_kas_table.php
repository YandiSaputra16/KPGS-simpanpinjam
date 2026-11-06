<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas', function (Blueprint $table) {

            $table->id();

            // nomor transaksi kas
            $table->string('no_transaksi', 30)->unique();

            // tanggal transaksi
            $table->date('tanggal');

            // jenis aliran kas
            $table->enum('jenis', [
                'masuk',
                'keluar'
            ]);

            // kategori transaksi koperasi
            $table->enum('kategori', [
                'simpanan',
                'pinjaman',
                'angsuran',
                'operasional',
                'shu',
                'modal',
                'lainnya'
            ]);

            // nominal transaksi
            $table->decimal('jumlah', 15, 2);

            // ledger (untuk audit & neraca)
            $table->decimal('saldo_sebelum', 15, 2);
            $table->decimal('saldo_sesudah', 15, 2);

            // keterangan tambahan
            $table->text('keterangan')->nullable();

            // relasi ringan ke transaksi lain
            $table->string('referensi_tipe', 30)->nullable();
            $table->unsignedBigInteger('referensi_id')->nullable();

            // user pencatat transaksi
            $table->foreignId('dicatat_oleh')
                ->constrained('users')
                ->onDelete('restrict');

            $table->timestamps();

            // index untuk laporan & audit
            $table->index(['tanggal', 'jenis']);
            $table->index(['kategori', 'tanggal']);
            $table->index(['referensi_tipe', 'referensi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas');
    }
};
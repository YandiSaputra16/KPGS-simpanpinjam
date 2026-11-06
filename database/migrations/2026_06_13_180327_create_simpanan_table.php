<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simpanan', function (Blueprint $table) {

            $table->id();

            $table->string(
                'no_simpanan',
                30
            )->unique();

            $table->foreignId('anggota_id')
                ->constrained('anggota')
                ->onDelete('restrict');

            $table->foreignId('jenis_simpanan_id')
                ->constrained('jenis_simpanan')
                ->onDelete('restrict');

            $table->decimal(
                'jumlah',
                15,
                2
            );

            $table->date(
                'tanggal_transaksi'
            );

            $table->enum(
                'metode_pembayaran',
                ['tunai', 'transfer']
            )->default('tunai');

            $table->enum(
                'status',
                [
                    'pending',
                    'disetujui',
                    'ditolak'
                ]
            )->default('disetujui');

            $table->text('keterangan')
                ->nullable();

            $table->string('bukti')
                ->nullable();

            $table->foreignId(
                'dicatat_oleh'
            )
                ->constrained('users')
                ->onDelete('restrict');

            $table->timestamps();

            $table->index([
                'anggota_id',
                'tanggal_transaksi'
            ]);

            $table->index([
                'jenis_simpanan_id',
                'tanggal_transaksi'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simpanan');
    }
};
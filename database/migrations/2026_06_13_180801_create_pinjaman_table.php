<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjaman', function (Blueprint $table) {

            $table->id();

            $table->string(
                'no_pinjaman',
                30
            )->unique();

            $table->foreignId('anggota_id')
                ->constrained('anggota')
                ->onDelete('restrict');

            $table->foreignId(
                'master_pinjaman_id'
            )
                ->constrained('master_pinjaman')
                ->onDelete('restrict');

            // Snapshot finansial
            $table->decimal(
                'jumlah_pinjaman',
                15,
                2
            );

            $table->decimal(
                'jumlah_disetujui',
                15,
                2
            )->nullable();

            $table->unsignedTinyInteger(
                'tenor'
            );

            $table->decimal(
                'bunga_persen',
                5,
                2
            );

            $table->decimal(
                'denda_persen',
                5,
                2
            )->default(0);

            $table->decimal(
                'angsuran_per_bulan',
                15,
                2
            );

            $table->decimal(
                'total_bunga',
                15,
                2
            );

            $table->decimal(
                'total_angsuran',
                15,
                2
            );

            $table->decimal(
                'sisa_pinjaman',
                15,
                2
            )->default(0);

            // Timeline
            $table->date(
                'tanggal_pengajuan'
            );

            $table->date(
                'tanggal_cair'
            )->nullable();

            $table->date(
                'tanggal_jatuh_tempo'
            )->nullable();

            $table->foreignId('disetujui_bendahara_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('tanggal_disetujui_bendahara')
                ->nullable();

            $table->foreignId('disetujui_pimpinan_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('tanggal_disetujui_pimpinan')
                ->nullable();

            // Tujuan
            $table->text(
                'tujuan_pinjaman'
            )->nullable();

            // Status workflow
            $table->enum('status', [
                'menunggu_bendahara',
                'menunggu_pimpinan',
                'disetujui',
                'dicairkan',
                'ditolak',
                'lunas',
            ])->default(
                'menunggu_bendahara'
            );

            $table->timestamps();

            $table->softDeletes();

            $table->index([
                'anggota_id',
                'status'
            ]);

            $table->index([
                'status',
                'tanggal_pengajuan'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjaman');
    }
};
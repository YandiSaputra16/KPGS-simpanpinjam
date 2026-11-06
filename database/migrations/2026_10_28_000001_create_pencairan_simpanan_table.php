<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pencairan_simpanan', function (Blueprint $table) {
            $table->id();
            $table->string('no_pengajuan', 30)->unique();
            $table->foreignId('anggota_id')->constrained('anggota')->restrictOnDelete();
            $table->decimal('jumlah', 15, 2);
            $table->text('alasan');
            $table->string('bukti')->nullable();
            $table->enum('status', ['menunggu_cs', 'menunggu_kabag', 'menunggu_pencairan', 'ditolak_cs', 'ditolak_kabag', 'selesai'])->default('menunggu_cs');
            $table->text('catatan_cs')->nullable();
            $table->foreignId('diperiksa_cs_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diperiksa_cs_pada')->nullable();
            $table->text('catatan_kabag')->nullable();
            $table->foreignId('disetujui_kabag_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_kabag_pada')->nullable();
            $table->date('tanggal_pencairan')->nullable();
            $table->string('bukti_pencairan')->nullable();
            $table->foreignId('dicairkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['anggota_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('pencairan_simpanan'); }
};

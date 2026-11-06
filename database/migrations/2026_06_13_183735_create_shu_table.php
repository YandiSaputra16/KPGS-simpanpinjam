<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =========================
        // TABEL SHU (INDUK TAHUNAN)
        // =========================
        Schema::create('shu', function (Blueprint $table) {

            $table->id();

            // Tahun buku SHU
            $table->year('tahun')->unique();

            // Total SHU koperasi
            $table->decimal('total_shu', 15, 2);

            // Persentase pembagian
            $table->decimal('persen_anggota', 5, 2)->default(40);
            $table->decimal('persen_cadangan', 5, 2)->default(25);
            $table->decimal('persen_pengurus', 5, 2)->default(20);
            $table->decimal('persen_sosial', 5, 2)->default(15);

            // Total bagian anggota
            $table->decimal('total_untuk_anggota', 15, 2);

            // Status workflow SHU
            $table->enum('status', ['draft', 'final'])
                ->default('draft');

            // Audit user
            $table->foreignId('dibuat_oleh')
                ->constrained('users')
                ->onDelete('restrict');

            $table->foreignId('difinalisasi_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('difinalisasi_at')->nullable();

            $table->timestamps();
        });

        // =========================
        // TABEL DETAIL SHU ANGGOTA
        // =========================
        Schema::create('shu_anggota', function (Blueprint $table) {

            $table->id();

            $table->foreignId('shu_id')
                ->constrained('shu')
                ->onDelete('cascade');

            $table->foreignId('anggota_id')
                ->constrained('anggota')
                ->onDelete('restrict');

            // dasar perhitungan SHU
            $table->decimal('total_simpanan_periode', 15, 2)->default(0);
            $table->decimal('total_pinjaman_periode', 15, 2)->default(0);

            // hasil perhitungan
            $table->decimal('jasa_simpanan', 15, 2)->default(0);
            $table->decimal('jasa_pinjaman', 15, 2)->default(0);

            $table->decimal('total_shu', 15, 2);

            // status distribusi (manual tracking, bukan API)
            $table->enum('status_distribusi', [
                'pending',
                'sudah',
                'gagal'
            ])->default('pending');

            // waktu distribusi (manual / saat cetak PDF)
            $table->timestamp('dikirim_at')->nullable();

            $table->timestamps();

            // satu anggota hanya boleh 1 SHU per tahun
            $table->unique(['shu_id', 'anggota_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shu_anggota');
        Schema::dropIfExists('shu');
    }
};
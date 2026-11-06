<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_pinjaman', function (Blueprint $table) {

            $table->id();

            $table->foreignId('pinjaman_id')
                ->constrained('pinjaman')
                ->onDelete('cascade');

            // siapa yg approve
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('level', [
                'bendahara',
                'pimpinan'
            ]);

            $table->unsignedTinyInteger(
                'urutan'
            )->default(1);

            $table->enum('status', [
                'pending',
                'disetujui',
                'ditolak'
            ])->default('pending');

            $table->text('catatan')
                ->nullable();

            $table->timestamp(
                'tanggal_approval'
            )->nullable();

            $table->timestamps();

            $table->unique([
                'pinjaman_id',
                'level'
            ]);

            $table->index([
                'pinjaman_id',
                'level',
                'status'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'approval_pinjaman'
        );
    }
};
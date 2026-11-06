<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {

            $table->id();

            // penerima notifikasi
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // isi notifikasi
            $table->string('judul', 100);
            $table->text('pesan');

            // kategori notifikasi untuk UI
            $table->enum('tipe', [
                'simpanan',
                'pinjaman',
                'angsuran',
                'shu',
                'sistem'
            ])->default('sistem');

            // status baca
            $table->boolean('is_read')->default(false);

            // waktu dibaca (opsional tapi penting)
            $table->timestamp('read_at')->nullable();

            // link redirect (opsional)
            $table->string('link')->nullable();

            $table->timestamps();

            // index untuk bell notification
            $table->index(['user_id', 'is_read', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
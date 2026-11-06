<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {

            $table->id();

            // user yang melakukan aksi (nullable jika akun dihapus)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // jenis aksi sistem
            $table->string('action', 60);

            // target model
            $table->string('model', 50)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();

            // deskripsi untuk UI
            $table->text('deskripsi');

            // metadata teknis
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // snapshot data sebelum & sesudah
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();

            // waktu log dibuat (immutable)
            $table->timestamp('created_at')->useCurrent();

            // index untuk dashboard log
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['model', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
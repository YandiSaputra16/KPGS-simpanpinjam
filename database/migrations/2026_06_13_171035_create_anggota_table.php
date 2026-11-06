<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('kode_anggota', 20)
                ->unique();

            $table->string('nik', 16)
                ->unique();

            $table->string('no_hp', 15);

            $table->text('alamat')
                ->nullable();

            $table->string('pekerjaan', 100)
                ->nullable();

            $table->date('tanggal_daftar')
                ->default(now());

            $table->enum('status_keanggotaan', [
                'aktif',
                'nonaktif'
            ])->default('aktif');

            // Running balance simpanan
            $table->decimal(
                'saldo_simpanan_pokok',
                15,
                2
            )->default(0);

            $table->decimal(
                'saldo_simpanan_wajib',
                15,
                2
            )->default(0);

            $table->decimal(
                'saldo_simpanan_sukarela',
                15,
                2
            )->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
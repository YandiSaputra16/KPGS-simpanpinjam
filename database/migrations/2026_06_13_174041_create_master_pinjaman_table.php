<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_pinjaman', function (Blueprint $table) {

            $table->id();

            $table->string('kode_pinjaman', 30)
                ->unique();

            $table->string('nama_paket', 100)
                ->unique();

            $table->decimal(
                'plafon_min',
                15,
                2
            );

            $table->decimal(
                'plafon_max',
                15,
                2
            );

            $table->unsignedTinyInteger(
                'tenor_min'
            );

            $table->unsignedTinyInteger(
                'tenor_max'
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

            $table->text('deskripsi')
                ->nullable();

            $table->text('syarat')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_pinjaman');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_simpanan', function (Blueprint $table) {

            $table->id();

            $table->string('nama_jenis', 100)
                ->unique();

            $table->string('kode', 20)
                ->unique();

            $table->text('keterangan')
                ->nullable();

            $table->decimal(
                'nominal_default',
                15,
                2
            )->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_simpanan');
    }
};
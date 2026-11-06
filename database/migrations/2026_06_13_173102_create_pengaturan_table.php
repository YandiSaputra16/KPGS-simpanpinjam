<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {

            $table->id();

            // Identitas koperasi
            $table->string('nama_koperasi')
                ->default('Koperasi Peternak Garut Selatan');

            $table->string('logo')
                ->nullable();

            $table->string('favicon')
                ->nullable();

            $table->text('alamat')
                ->nullable();

            $table->string('telepon', 20)
                ->nullable();

            $table->string('email', 100)
                ->nullable()
                ->unique();

            $table->text('tentang')
                ->nullable();

            $table->text('visi')
                ->nullable();

            $table->text('misi')
                ->nullable();

            // Media sosial
            $table->string('instagram')
                ->nullable();

            $table->string('facebook')
                ->nullable();

            $table->string('whatsapp', 20)
                ->nullable();

            // Lokasi
            $table->text('maps_embed')
                ->nullable();

            // Keuangan awal
            $table->decimal(
                'modal_awal',
                15,
                2
            )->default(0);

            $table->decimal(
                'total_inventaris',
                15,
                2
            )->default(0);

            $table->decimal(
                'shu_ditahan',
                15,
                2
            )->default(0);

            $table->decimal(
                'cadangan_umum',
                15,
                2
            )->default(0);

            // Integrasi API
            $table->string('google_maps_api_key')
                ->nullable();

            $table->string('fonnte_token')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
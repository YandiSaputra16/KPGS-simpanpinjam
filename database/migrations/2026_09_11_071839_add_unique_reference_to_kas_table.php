<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kas', function (Blueprint $table) {
            $table->unique(
                ['referensi_tipe', 'referensi_id'],
                'kas_referensi_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('kas', function (Blueprint $table) {
            $table->dropUnique(
                'kas_referensi_unique'
            );
        });
    }
};
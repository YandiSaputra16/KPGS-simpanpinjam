<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('pencairan_simpanan') && !Schema::hasColumn('pencairan_simpanan', 'jenis_simpanan')) {
            Schema::table('pencairan_simpanan', function (Blueprint $table) {
                $table->enum('jenis_simpanan', ['pokok', 'wajib', 'sukarela'])->default('sukarela')->after('anggota_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pencairan_simpanan') && Schema::hasColumn('pencairan_simpanan', 'jenis_simpanan')) {
            Schema::table('pencairan_simpanan', function (Blueprint $table) {
                $table->dropColumn('jenis_simpanan');
            });
        }
    }
};

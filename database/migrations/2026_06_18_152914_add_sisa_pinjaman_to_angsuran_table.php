<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('angsuran', function (Blueprint $table) {

        $table->decimal('sisa_pinjaman', 15, 2)
            ->default(0)
            ->after('hari_terlambat');

    });
}

public function down(): void
{
    Schema::table('angsuran', function (Blueprint $table) {

        $table->dropColumn('sisa_pinjaman');

    });
}
};

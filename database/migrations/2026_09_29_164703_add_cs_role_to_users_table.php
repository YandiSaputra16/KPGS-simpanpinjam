<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'admin',
                'cs',
                'ao',
                'kabag',
                'bendahara',
                'pimpinan',
                'anggota',
            ])->default('anggota')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'admin',
                'bendahara',
                'pimpinan',
                'anggota',
            ])->default('anggota')->change();
        });
    }
};
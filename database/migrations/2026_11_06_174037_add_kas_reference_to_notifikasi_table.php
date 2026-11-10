
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->foreignId('kas_id')
                ->nullable()
                ->after('user_id')
                ->constrained('kas')
                ->nullOnDelete();

            $table->unique(
                ['user_id', 'kas_id'],
                'notifikasi_user_kas_unique'
            );
        });

        // Tambahkan tipe kas pada enum yang sudah ada.
        DB::statement("
            ALTER TABLE notifikasi
            MODIFY tipe ENUM(
                'simpanan',
                'pinjaman',
                'angsuran',
                'shu',
                'sistem',
                'kas'
            ) NOT NULL DEFAULT 'sistem'
        ");
    }

    public function down(): void
    {
        // Ubah notifikasi kas menjadi sistem sebelum menghapus tipe kas.
        DB::table('notifikasi')
            ->where('tipe', 'kas')
            ->update(['tipe' => 'sistem']);

        DB::statement("
            ALTER TABLE notifikasi
            MODIFY tipe ENUM(
                'simpanan',
                'pinjaman',
                'angsuran',
                'shu',
                'sistem'
            ) NOT NULL DEFAULT 'sistem'
        ");

        Schema::table('notifikasi', function (Blueprint $table) {
            $table->dropUnique('notifikasi_user_kas_unique');
            $table->dropConstrainedForeignId('kas_id');
        });
    }
};

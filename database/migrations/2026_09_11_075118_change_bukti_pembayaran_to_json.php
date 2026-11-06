<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Ubah data lama terlebih dahulu.
         *
         * Data lama berupa string path file akan
         * dikonversi menjadi JSON array.
         */
        DB::table('angsuran')
            ->whereNotNull('bukti_pembayaran')
            ->where('bukti_pembayaran', '!=', '')
            ->orderBy('id')
            ->eachById(function ($angsuran) {
                $nilaiLama = $angsuran->bukti_pembayaran;

                /*
                 * Jika ternyata data sudah berupa JSON valid,
                 * pertahankan isinya.
                 */
                $decoded = json_decode(
                    $nilaiLama,
                    true
                );

                if (json_last_error() === JSON_ERROR_NONE) {
                    if (!is_array($decoded)) {
                        $decoded = [$nilaiLama];
                    }
                } else {
                    /*
                     * Data lama berupa path string biasa.
                     */
                    $decoded = [$nilaiLama];
                }

                DB::table('angsuran')
                    ->where('id', $angsuran->id)
                    ->update([
                        'bukti_pembayaran' =>
                            json_encode(
                                $decoded,
                                JSON_UNESCAPED_SLASHES
                            ),
                    ]);
            });

        /*
         * Ubah tipe kolom menjadi JSON.
         */
        Schema::table('angsuran', function (Blueprint $table) {
            $table->json('bukti_pembayaran')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        /*
         * Kembalikan JSON array menjadi string.
         *
         * Jika terdapat beberapa bukti, semuanya
         * digabung menggunakan tanda koma.
         */
        DB::table('angsuran')
            ->whereNotNull('bukti_pembayaran')
            ->orderBy('id')
            ->eachById(function ($angsuran) {
                $decoded = json_decode(
                    $angsuran->bukti_pembayaran,
                    true
                );

                if (is_array($decoded)) {
                    $nilaiLama = implode(
                        ',',
                        $decoded
                    );
                } else {
                    $nilaiLama =
                        $angsuran->bukti_pembayaran;
                }

                DB::table('angsuran')
                    ->where('id', $angsuran->id)
                    ->update([
                        'bukti_pembayaran' =>
                            $nilaiLama,
                    ]);
            });

        /*
         * Kembalikan tipe kolom menjadi string.
         */
        Schema::table('angsuran', function (Blueprint $table) {
            $table->string('bukti_pembayaran')
                ->nullable()
                ->change();
        });
    }
};
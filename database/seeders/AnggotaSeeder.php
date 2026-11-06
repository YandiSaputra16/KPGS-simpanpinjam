<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Anggota;
use Illuminate\Database\Seeder;

class AnggotaSeeder extends Seeder
{
    /**
     * Membuat profil anggota untuk akun dengan role anggota.
     * Data anggota yang sudah tersedia tidak akan ditimpa.
     */
    public function run(): void
    {
        $usersAnggota = User::query()
            ->where('role', 'anggota')
            ->where('status', 'aktif')
            ->orderBy('id')
            ->get();

        foreach ($usersAnggota as $user) {

            Anggota::firstOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'kode_anggota' => Anggota::generateKode(),

                    // Identitas dummy untuk kebutuhan pengujian.
                    // Bukan NIK asli.
                    'nik' => '3201' . str_pad(
                        (string) $user->id,
                        12,
                        '0',
                        STR_PAD_LEFT
                    ),

                    'no_hp' => '08' . str_pad(
                        (string) $user->id,
                        10,
                        '0',
                        STR_PAD_LEFT
                    ),

                    'alamat' => 'Garut Selatan',
                    'pekerjaan' => 'Peternak',
                    'tanggal_daftar' => now()->toDateString(),
                    'status_keanggotaan' => 'aktif',

                    'saldo_simpanan_pokok' => 0,
                    'saldo_simpanan_wajib' => 0,
                    'saldo_simpanan_sukarela' => 0,
                ]
            );
        }

        $this->command?->info(
            'Seeder anggota selesai diperiksa.'
        );
    }
}

<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Anggota>
 */
class AnggotaFactory extends Factory
{
    protected $model = Anggota::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'kode_anggota' => 'KSP-' . fake()->unique()->numerify('####'),
            'nik' => fake()->unique()->numerify('################'),
            'no_hp' => fake()->numerify('08############'),
            'alamat' => fake()->address(),
            'pekerjaan' => fake()->jobTitle(),
            'tanggal_daftar' => now()->toDateString(),
            'status_keanggotaan' => 'aktif',
            'saldo_simpanan_pokok' => 0,
            'saldo_simpanan_wajib' => 0,
            'saldo_simpanan_sukarela' => 0,
        ];
    }
}

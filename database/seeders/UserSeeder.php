<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Jalankan database seeder.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'admin@kpgs.com'],
            [
                'nama'     => 'Administrator KPGS',
                'password' => 'password',
                'role'     => 'admin',
                'status'   => 'aktif',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | BENDAHARA
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'bendahara@kpgs.com'],
            [
                'nama'     => 'Bendahara KPGS',
                'password' => 'password',
                'role'     => 'bendahara',
                'status'   => 'aktif',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | PIMPINAN
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'pimpinan@kpgs.com'],
            [
                'nama'     => 'Pimpinan KPGS',
                'password' => 'password',
                'role'     => 'pimpinan',
                'status'   => 'aktif',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | ANGGOTA
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'anggota@kpgs.com'],
            [
                'nama'     => 'Anggota Demo',
                'password' => 'password',
                'role'     => 'anggota',
                'status'   => 'aktif',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | CUSTOMER SERVICE
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'cs@kpgs.com'],
            [
                'nama'     => 'Customer Service KPGS',
                'password' => 'password',
                'role'     => 'cs',
                'status'   => 'aktif',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | ACCOUNT OFFICER
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'ao@kpgs.com'],
            [
                'nama'     => 'Account Officer KPGS',
                'password' => 'password',
                'role'     => 'ao',
                'status'   => 'aktif',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | KEPALA BAGIAN
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'kabag@kpgs.com'],
            [
                'nama'     => 'Kepala Bagian KPGS',
                'password' => 'password',
                'role'     => 'kabag',
                'status'   => 'aktif',
            ]
        );
    }
}
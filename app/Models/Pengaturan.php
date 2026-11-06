<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = [
        // Identitas koperasi
        'nama_koperasi',
        'logo',
        'favicon',
        'alamat',
        'telepon',
        'email',

        // Company Profile
        'tentang',
        'sejarah',
        'visi',
        'misi',
        'layanan',
        'syarat_anggota',
        'prosedur_pinjaman',

        // Media sosial
        'instagram',
        'facebook',
        'whatsapp',

        // Lokasi
        'maps_embed',

        // Data keuangan dasar
        'modal_awal',
        'total_inventaris',
        'shu_ditahan',
        'cadangan_umum',

        // Integrasi
        'google_maps_api_key',
        'fonnte_token',
        'midtrans_server_key',
        'midtrans_client_key',
    ];

    /*
    |--------------------------------------------------------------------------
    | Pengaturan aktif
    |--------------------------------------------------------------------------
    */

    public static function aktif(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'nama_koperasi' => 'Koperasi Peternak Garut Selatan',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | URL Logo
    |--------------------------------------------------------------------------
    */

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        return Storage::disk('public')->url($this->logo);
    }

    /*
    |--------------------------------------------------------------------------
    | URL Favicon
    |--------------------------------------------------------------------------
    */

    public function getFaviconUrlAttribute(): ?string
    {
        if (!$this->favicon) {
            return null;
        }

        return Storage::disk('public')->url($this->favicon);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class JenisSimpanan extends Model
{
    protected $table = 'jenis_simpanan';

    protected $fillable = [
        'nama_jenis',
        'kode',
        'keterangan',
        'nominal_default', // ← kolom di database yang belum masuk
        'is_active',
    ];

    protected $casts = [
        'nominal_default' => 'decimal:2', // ← cast desimal
        'is_active'       => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function simpanan(): HasMany
    {
        return $this->hasMany(Simpanan::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */

    // Tambah type hint yang proper
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    public function isAktif(): bool
    {
        return $this->is_active === true;
    }

    // Cek apakah jenis ini punya nominal default yang sudah ditentukan
    // Berguna saat auto-fill form input simpanan
    public function hasNominalDefault(): bool
    {
        return $this->nominal_default > 0;
    }
}
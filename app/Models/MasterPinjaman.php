<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class MasterPinjaman extends Model
{
    protected $table = 'master_pinjaman';

    protected $fillable = [
        'kode_pinjaman',  
        'nama_paket',
        'plafon_min',
        'plafon_max',
        'tenor_min',
        'tenor_max',
        'bunga_persen',
        'denda_persen',   
        'deskripsi',      
        'syarat',
        'is_active',
    ];

    protected $casts = [
        'plafon_min'   => 'decimal:2',
        'plafon_max'   => 'decimal:2',
        'bunga_persen' => 'decimal:2',
        'denda_persen' => 'decimal:2', 
        'tenor_min'    => 'integer',   
        'tenor_max'    => 'integer',   
        'is_active'    => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function pinjaman(): HasMany
    {
        return $this->hasMany(Pinjaman::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */

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

    public function isValidPlafon(float $jumlah): bool
    {
        return $jumlah >= $this->plafon_min && $jumlah <= $this->plafon_max;
    }

    public function isValidTenor(int $tenor): bool
    {
        return $tenor >= $this->tenor_min && $tenor <= $this->tenor_max;
    }

    public function isValidPengajuan(float $jumlah, int $tenor): bool
    {
        return $this->isValidPlafon($jumlah) && $this->isValidTenor($tenor);
    }

    // Kembalikan range plafon dalam format rupiah
    // Dipakai di view form pengajuan pinjaman
    public function getRangePlafonLabel(): string
    {
        return 'Rp ' . number_format($this->plafon_min, 0, ',', '.')
             . ' – Rp ' . number_format($this->plafon_max, 0, ',', '.');
    }

    // Kembalikan range tenor dalam format label
    public function getRangeTenorLabel(): string
    {
        return $this->tenor_min . ' – ' . $this->tenor_max . ' bulan';
    }
}
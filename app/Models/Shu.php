<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Shu extends Model
{
    protected $table = 'shu';

    protected $fillable = [
        'tahun',
        'total_shu',
        'persen_anggota',
        'persen_cadangan',
        'persen_pengurus',
        'persen_sosial',
        'total_untuk_anggota',
        'status',
        'dibuat_oleh',
        'difinalisasi_oleh',
        'difinalisasi_at',
    ];

    protected $casts = [
        'tahun'               => 'integer',  // ← tambahan
        'total_shu'           => 'decimal:2', // ← tambahan
        'persen_anggota'      => 'decimal:2', // ← tambahan
        'persen_cadangan'     => 'decimal:2', // ← tambahan
        'persen_pengurus'     => 'decimal:2', // ← tambahan
        'persen_sosial'       => 'decimal:2', // ← tambahan
        'total_untuk_anggota' => 'decimal:2', // ← tambahan
        'difinalisasi_at'     => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function shuAnggota(): HasMany
    {
        return $this->hasMany(ShuAnggota::class);
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function pengFinalisasi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'difinalisasi_oleh');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeFinal(Builder $query): Builder
    {
        return $query->where('status', 'final');
    }

    public function scopeTahun(Builder $query, int $tahun): Builder
    {
        return $query->where('tahun', $tahun);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isFinal(): bool
    {
        return $this->status === 'final';
    }

    // Validasi total persentase harus = 100
    // Panggil sebelum menyimpan data SHU baru
    public function isTotalPersenValid(): bool
    {
        $total = (float) $this->persen_anggota
               + (float) $this->persen_cadangan
               + (float) $this->persen_pengurus
               + (float) $this->persen_sosial;

        return round($total, 2) === 100.00;
    }

    /*
    |--------------------------------------------------------------------------
    | KALKULASI
    |--------------------------------------------------------------------------
    */

    // Hitung total yang dialokasikan ke anggota
    // Dipanggil sebelum generate ShuAnggota
    public function hitungTotalUntukAnggota(): float
    {
        return round(
            (float) $this->total_shu * ((float) $this->persen_anggota / 100),
            2
        );
    }

    // Hitung alokasi per komponen — untuk ditampilkan di ringkasan SHU
    public function getAlokasiLengkap(): array
    {
        return [
            'untuk_anggota'  => round((float) $this->total_shu * ($this->persen_anggota / 100), 2),
            'untuk_cadangan' => round((float) $this->total_shu * ($this->persen_cadangan / 100), 2),
            'untuk_pengurus' => round((float) $this->total_shu * ($this->persen_pengurus / 100), 2),
            'untuk_sosial'   => round((float) $this->total_shu * ($this->persen_sosial / 100), 2),
        ];
    }
}
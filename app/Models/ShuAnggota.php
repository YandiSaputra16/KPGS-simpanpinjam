<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ShuAnggota extends Model
{
    protected $table = 'shu_anggota';

    protected $fillable = [
        'shu_id',
        'anggota_id',
        'total_simpanan_periode',
        'total_pinjaman_periode',
        'jasa_simpanan',
        'jasa_pinjaman',
        'total_shu',
        'status_distribusi', // ← sesuai database Anda (bukan 2 boolean)
        'dikirim_at',
    ];

    protected $casts = [
        'total_simpanan_periode' => 'decimal:2', // ← tambahan
        'total_pinjaman_periode' => 'decimal:2', // ← tambahan
        'jasa_simpanan'          => 'decimal:2', // ← tambahan
        'jasa_pinjaman'          => 'decimal:2', // ← tambahan
        'total_shu'              => 'decimal:2', // ← tambahan
        'dikirim_at'             => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function shu(): BelongsTo
    {
        return $this->belongsTo(Shu::class);
    }

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */

    // Filter yang belum dikirim — dipakai saat kirim massal
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status_distribusi', 'pending');
    }

    // Filter yang sudah berhasil dikirim
    public function scopeSudahDikirim(Builder $query): Builder
    {
        return $query->where('status_distribusi', 'sudah');
    }

    // Filter yang gagal dikirim — untuk retry
    public function scopeGagal(Builder $query): Builder
    {
        return $query->where('status_distribusi', 'gagal');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status_distribusi === 'pending';
    }

    public function isSudahDikirim(): bool
    {
        return $this->status_distribusi === 'sudah';
    }

    public function isGagal(): bool
    {
        return $this->status_distribusi === 'gagal';
    }

    // Tandai berhasil dikirim sekaligus catat waktu pengiriman
    public function tandaiSudahDikirim(): void
    {
        $this->update([
            'status_distribusi' => 'sudah',
            'dikirim_at'        => now(),
        ]);
    }

    // Tandai gagal — untuk dicoba ulang (retry)
    public function tandaiGagal(): void
    {
        $this->update([
            'status_distribusi' => 'gagal',
        ]);
    }
}
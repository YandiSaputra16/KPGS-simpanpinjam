<?php

namespace App\Models;

use App\Models\Traits\AngsuranVerifikasi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Angsuran extends Model
{
    use AngsuranVerifikasi;

    protected $table = 'angsuran';

    protected $fillable = [
        'pinjaman_id',
        'angsuran_ke',
        'tanggal_jatuh_tempo',
        'jumlah_pokok',
        'jumlah_bunga',
        'total_angsuran',
        'denda',
        'jumlah_bayar',
        'tanggal_bayar',
        'bukti_pembayaran',
        'payment_batch_id',
        'hari_terlambat',
        'sisa_pinjaman',
        'status_pembayaran',
        'verified_by',
        'tanggal_verifikasi',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_bayar' => 'date',
        'tanggal_verifikasi' => 'datetime',
        'jumlah_pokok' => 'decimal:2',
        'jumlah_bunga' => 'decimal:2',
        'total_angsuran' => 'decimal:2',
        'denda' => 'decimal:2',
        'jumlah_bayar' => 'decimal:2',
        'hari_terlambat' => 'integer',
        'sisa_pinjaman' => 'decimal:2',
        'bukti_pembayaran' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjaman::class);
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */

    public function scopeBelumBayar(Builder $query): Builder
    {
        return $query->where(
            'status_pembayaran',
            'belum_bayar'
        );
    }

    public function scopeLunas(Builder $query): Builder
    {
        return $query->where(
            'status_pembayaran',
            'lunas'
        );
    }

    public function scopeMenungguVerifikasi(Builder $query): Builder
    {
        return $query->where(
            'status_pembayaran',
            'menunggu_verifikasi'
        );
    }

    public function scopeJatuhTempo(Builder $query): Builder
    {
        return $query
            ->where(
                'status_pembayaran',
                'belum_bayar'
            )
            ->where(
                'tanggal_jatuh_tempo',
                '<=',
                today()
            );
    }

    public function scopeAkanJatuhTempo(
        Builder $query,
        int $hari = 7
    ): Builder {
        return $query
            ->where(
                'status_pembayaran',
                'belum_bayar'
            )
            ->whereBetween(
                'tanggal_jatuh_tempo',
                [
                    today(),
                    today()->addDays($hari),
                ]
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR
    |--------------------------------------------------------------------------
    */

    public function getTotalTagihanAttribute(): float
    {
        return (float) $this->total_angsuran
            + (float) $this->denda;
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    public function isBelumBayar(): bool
    {
        return $this->status_pembayaran === 'belum_bayar';
    }

    public function isMenungguVerifikasi(): bool
    {
        return $this->status_pembayaran === 'menunggu_verifikasi';
    }

    public function isLunas(): bool
    {
        return $this->status_pembayaran === 'lunas';
    }

    public function isDitolak(): bool
    {
        return $this->status_pembayaran === 'ditolak';
    }

    public function isTerlambat(): bool
    {
        return $this->status_pembayaran === 'belum_bayar'
            && $this->tanggal_jatuh_tempo->lt(today());
    }

    public function batchPembayaran()
    {
        return self::where(
            'payment_batch_id',
            $this->payment_batch_id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | KALKULASI DENDA
    |--------------------------------------------------------------------------
    */

    public function hitungDanUpdateDenda(): void
    {
        if (!$this->isTerlambat()) {
            return;
        }

        $hariTerlambat = $this->tanggal_jatuh_tempo
            ->diffInDays(today());

        $dendaPersen = $this->pinjaman->denda_persen ?? 0;

        $denda = (float) $this->jumlah_pokok
            * ((float) $dendaPersen / 100)
            * $hariTerlambat;

        $this->update([
            'hari_terlambat' => $hariTerlambat,
            'denda' => round($denda, 2),
        ]);
    }
}
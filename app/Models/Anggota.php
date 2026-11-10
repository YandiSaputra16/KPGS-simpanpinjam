<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggota';

    protected $fillable = [
        'user_id',
        'kode_anggota',
        'nik',
        'no_hp',
        'alamat',
        'pekerjaan',

        // Informasi rekening anggota
        'nama_bank',
        'nomor_rekening',
        'atas_nama_rekening',

        'tanggal_daftar',
        'status_keanggotaan',
        'saldo_simpanan_pokok',
        'saldo_simpanan_wajib',
        'saldo_simpanan_sukarela',
    ];

    protected $casts = [
        'tanggal_daftar'          => 'date',
        'saldo_simpanan_pokok'    => 'decimal:2',
        'saldo_simpanan_wajib'    => 'decimal:2',
        'saldo_simpanan_sukarela' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function simpanan(): HasMany
    {
        return $this->hasMany(Simpanan::class);
    }

    public function pinjaman(): HasMany
    {
        return $this->hasMany(Pinjaman::class);
    }

    public function shuAnggota(): HasMany
    {
        return $this->hasMany(ShuAnggota::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_keanggotaan', 'aktif');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR
    |--------------------------------------------------------------------------
    */

    public function getTotalSimpananAttribute(): float
    {
        return (float) $this->saldo_simpanan_pokok
            + (float) $this->saldo_simpanan_wajib
            + (float) $this->saldo_simpanan_sukarela;
    }

    public function getPinjamanAktifAttribute(): ?Pinjaman
    {
        return $this->pinjaman()
            ->whereIn('status', [
                'menunggu_cs',
                'menunggu_ao',
                'menunggu_kabag',
                'disetujui',
                'dicairkan',
            ])
            ->latest()
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    public static function generateKode(): string
    {
        $last = self::orderBy('id', 'desc')->first();

        $urutan = $last
            ? ((int) substr($last->kode_anggota, -4)) + 1
            : 1;

        return 'KSP-' . str_pad($urutan, 4, '0', STR_PAD_LEFT);
    }

    public function updateSaldo(
        string $jenis,
        float $jumlah,
        string $operasi = 'tambah'
    ): void {
        $kolom = match ($jenis) {
            'pokok'    => 'saldo_simpanan_pokok',
            'wajib'    => 'saldo_simpanan_wajib',
            'sukarela' => 'saldo_simpanan_sukarela',
            default    => throw new \InvalidArgumentException(
                "Jenis simpanan tidak valid: {$jenis}"
            ),
        };

        if ($operasi === 'tambah') {
            $this->increment($kolom, $jumlah);
        } else {
            $this->decrement($kolom, $jumlah);
        }
    }

    public function isAktif(): bool
    {
        return $this->status_keanggotaan === 'aktif';
    }

    /**
     * Memeriksa apakah informasi rekening sudah lengkap.
     */
    public function memilikiRekeningLengkap(): bool
    {
        return filled($this->nama_bank)
            && filled($this->nomor_rekening)
            && filled($this->atas_nama_rekening);
    }
}

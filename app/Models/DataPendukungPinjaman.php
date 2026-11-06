<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataPendukungPinjaman extends Model
{
    protected $table = 'data_pendukung_pinjaman';

    protected $fillable = [
        'pinjaman_id',
        'produksi_susu_bulanan',
        'jumlah_sapi',
        'kondisi_usaha',
        'pendapatan_bulanan',
        'kewajiban_angsuran_lain',
        'keterangan_tambahan',
    ];

    protected $casts = [
        'produksi_susu_bulanan'     => 'decimal:2',
        'jumlah_sapi'               => 'integer',
        'pendapatan_bulanan'        => 'decimal:2',
        'kewajiban_angsuran_lain'   => 'decimal:2',
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
}
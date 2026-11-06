<?php

namespace App\Models;

use App\Models\Traits\PinjamanApproval;
use App\Models\Traits\PinjamanKalkulasi;
use App\Models\Traits\PinjamanValidasi;
use App\Models\Traits\PinjamanPembayaran;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pinjaman extends Model
{
    use HasFactory, SoftDeletes;

    use PinjamanKalkulasi;
    use PinjamanValidasi;
    use PinjamanApproval;
    use PinjamanPembayaran;

    protected $table = 'pinjaman';

    protected $fillable = [
        'no_pinjaman',
        'anggota_id',
        'master_pinjaman_id',

        'jumlah_pinjaman',
        'jumlah_rekomendasi',
        'jumlah_disetujui',

        'tenor',
        'bunga_persen',
        'denda_persen',

        'angsuran_per_bulan',
        'total_bunga',
        'total_angsuran',
        'sisa_pinjaman',

        'tanggal_pengajuan',
        'tanggal_cair',
        'tanggal_jatuh_tempo',

        'tujuan_pinjaman',

        'status',

        'disetujui_syarat',
        'waktu_setujui_syarat',
        'ip_setujui_syarat',
    ];

    protected $casts = [
        'tanggal_pengajuan'   => 'date',
        'tanggal_cair'        => 'date',
        'tanggal_jatuh_tempo' => 'date',

        'jumlah_pinjaman'    => 'decimal:2',
        'jumlah_rekomendasi' => 'decimal:2',
        'jumlah_disetujui'   => 'decimal:2',

        'bunga_persen' => 'decimal:2',
        'denda_persen' => 'decimal:2',

        'angsuran_per_bulan' => 'decimal:2',
        'total_bunga'        => 'decimal:2',
        'total_angsuran'     => 'decimal:2',
        'sisa_pinjaman'      => 'decimal:2',

        'disetujui_syarat'      => 'boolean',
        'waktu_setujui_syarat' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    /**
     * Anggota yang mengajukan pinjaman.
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    /**
     * Paket/master pinjaman yang digunakan.
     */
    public function masterPinjaman(): BelongsTo
    {
        return $this->belongsTo(MasterPinjaman::class);
    }

    /**
     * Riwayat keputusan pinjaman.
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(ApprovalPinjaman::class);
    }

    /**
     * Approval final oleh Kabag.
     */
    public function approvalKabag(): HasOne
    {
        return $this->hasOne(ApprovalPinjaman::class)
            ->where('level', 'kabag');
    }

    /**
     * Jadwal pembayaran angsuran.
     */
    public function angsuran(): HasMany
    {
        return $this->hasMany(Angsuran::class);
    }

    /**
     * Data pendukung pengajuan pinjaman.
     */
    public function dataPendukung(): HasOne
    {
        return $this->hasOne(DataPendukungPinjaman::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE STATUS
    |--------------------------------------------------------------------------
    */

    public function scopeMenungguCS(Builder $query): Builder
    {
        return $query->where('status', 'menunggu_cs');
    }

    public function scopeMenungguAO(Builder $query): Builder
    {
        return $query->where('status', 'menunggu_ao');
    }

    public function scopeMenungguKabag(Builder $query): Builder
    {
        return $query->where('status', 'menunggu_kabag');
    }

    public function scopeDisetujui(Builder $query): Builder
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeDicairkan(Builder $query): Builder
    {
        return $query->where('status', 'dicairkan');
    }

    public function scopeLunas(Builder $query): Builder
    {
        return $query->where('status', 'lunas');
    }

    public function scopeDitolak(Builder $query): Builder
    {
        return $query->where('status', 'ditolak');
    }

    /**
     * Pinjaman yang sudah disetujui dan masih memiliki kewajiban.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereIn('status', [
            'disetujui',
            'dicairkan',
        ]);
    }

    /**
     * Pengajuan atau pinjaman yang belum selesai.
     */
    public function scopeBelumLunas(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            'lunas',
            'ditolak',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER STATUS
    |--------------------------------------------------------------------------
    */

    public function isMenungguCS(): bool
    {
        return $this->status === 'menunggu_cs';
    }

    public function isMenungguAO(): bool
    {
        return $this->status === 'menunggu_ao';
    }

    public function isMenungguKabag(): bool
    {
        return $this->status === 'menunggu_kabag';
    }

    public function isDisetujui(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isDicairkan(): bool
    {
        return $this->status === 'dicairkan';
    }

    public function isLunas(): bool
    {
        return $this->status === 'lunas';
    }

    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }

    public function isSyaratDisetujui(): bool
    {
        return $this->disetujui_syarat === true;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE SISA PINJAMAN
    |--------------------------------------------------------------------------
    */

    /**
     * Menghitung ulang sisa kewajiban pinjaman
     * berdasarkan seluruh angsuran yang sudah lunas.
     */
    public function updateSisaPinjaman(): void
    {
        $totalTerbayar = $this->angsuran()
            ->where('status_pembayaran', 'lunas')
            ->sum('jumlah_bayar');

        $totalKewajiban = (float) $this->total_angsuran;

        $sisaPinjaman = max(
            0,
            $totalKewajiban - (float) $totalTerbayar
        );

        $this->sisa_pinjaman = round($sisaPinjaman, 2);

        if (
            $sisaPinjaman <= 0 &&
            $this->angsuran()->exists() &&
            !$this->angsuran()
                ->where('status_pembayaran', '!=', 'lunas')
                ->exists()
        ) {
            $this->status = 'lunas';
        }

        $this->save();
    }
}
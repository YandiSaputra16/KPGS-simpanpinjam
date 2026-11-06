<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PencairanSimpanan extends Model
{
    protected $table = 'pencairan_simpanan';

    protected $fillable = [
        'no_pengajuan', 'anggota_id', 'jenis_simpanan', 'jumlah', 'alasan', 'bukti', 'status',
        'catatan_cs', 'diperiksa_cs_oleh', 'diperiksa_cs_pada', 'catatan_kabag',
        'disetujui_kabag_oleh', 'disetujui_kabag_pada', 'tanggal_pencairan',
        'bukti_pencairan', 'dicairkan_oleh',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'diperiksa_cs_pada' => 'datetime',
        'disetujui_kabag_pada' => 'datetime',
        'tanggal_pencairan' => 'date',
    ];

    public const JENIS = [
        'pokok' => 'Simpanan Pokok',
        'wajib' => 'Simpanan Wajib',
        'sukarela' => 'Simpanan Sukarela',
    ];

    public function anggota(): BelongsTo { return $this->belongsTo(Anggota::class); }
    public function pemeriksaCs(): BelongsTo { return $this->belongsTo(User::class, 'diperiksa_cs_oleh'); }
    public function penyetujuKabag(): BelongsTo { return $this->belongsTo(User::class, 'disetujui_kabag_oleh'); }
    public function bendahara(): BelongsTo { return $this->belongsTo(User::class, 'dicairkan_oleh'); }

    public function getNamaJenisSimpananAttribute(): string
    {
        return self::JENIS[$this->jenis_simpanan] ?? ucfirst((string) $this->jenis_simpanan);
    }

    public static function generateNomor(): string
    {
        $prefix = 'PCS-' . now()->format('Y') . '-';
        $last = static::where('no_pengajuan', 'like', $prefix . '%')->orderByDesc('id')->value('no_pengajuan');
        $next = $last ? ((int) substr($last, -5)) + 1 : 1;
        return $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}

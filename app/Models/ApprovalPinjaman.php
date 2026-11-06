<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class ApprovalPinjaman extends Model
{
    protected $table = 'approval_pinjaman';

    protected $fillable = [
        'pinjaman_id',
        'user_id',
        'level',
        'urutan',
        'status',
        'catatan',
        'tanggal_approval',
    ];

    protected $casts = [
        'urutan'           => 'integer',
        'tanggal_approval' => 'datetime',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */

    public function scopeLevelKabag(Builder $query): Builder
    {
        return $query->where('level', 'kabag');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeDisetujui(Builder $query): Builder
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeDitolak(Builder $query): Builder
    {
        return $query->where('status', 'ditolak');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDisetujui(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }

    public function isKabag(): bool
    {
        return $this->level === 'kabag';
    }
}
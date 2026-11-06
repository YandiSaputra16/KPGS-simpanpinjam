<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'deskripsi',
        'ip_address',
        'user_agent',
        'data_lama',
        'data_baru',
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
        'created_at' => 'datetime',
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

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */

    public function scopeAksi(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    public function scopeMilikUser(
        Builder $query,
        int $userId
    ): Builder {
        return $query->where('user_id', $userId);
    }

    public function scopeTerkait(
        Builder $query,
        string $model,
        int $modelId
    ): Builder {
        return $query->where('model', $model)
            ->where('model_id', $modelId);
    }

    /*
    |--------------------------------------------------------------------------
    | PENCATATAN AKTIVITAS
    |--------------------------------------------------------------------------
    */

    public static function catat(
        string $action,
        string $deskripsi,
        ?string $model = null,
        ?int $modelId = null,
        ?array $dataLama = null,
        ?array $dataBaru = null
    ): self {
        return self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'deskripsi' => $deskripsi,
            'ip_address' => request()?->ip() ?? 'system',
            'user_agent' => request()?->userAgent() ?? 'system',
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
        ]);
    }
}

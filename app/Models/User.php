<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden Attributes
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    /**
     * User memiliki satu data anggota.
     *
     * users 1 --- 1 anggota
     */
    public function anggota(): HasOne
    {
        return $this->hasOne(Anggota::class);
    }

    /**
     * User memiliki banyak notifikasi.
     *
     * users 1 --- M notifikasi
     */
    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    /**
     * User memiliki banyak activity log.
     *
     * users 1 --- M activity_logs
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * User memiliki banyak approval pinjaman.
     *
     * users 1 --- M approval_pinjaman
     *
     * Pada workflow terbaru, approval dilakukan oleh Kabag.
     */
    public function approvalPinjaman(): HasMany
    {
        return $this->hasMany(ApprovalPinjaman::class);
    }

    /**
     * User memiliki banyak transaksi kas
     * sebagai pencatat transaksi.
     *
     * users 1 --- M kas
     */
    public function kas(): HasMany
    {
        return $this->hasMany(Kas::class, 'dicatat_oleh');
    }

    /**
     * User memiliki banyak data SHU
     * sebagai pembuat.
     *
     * Fitur SHU sementara diparkir,
     * tetapi relasi dipertahankan karena tabelnya
     * masih menjadi bagian dari project.
     *
     * users 1 --- M shu
     */
    public function shu(): HasMany
    {
        return $this->hasMany(Shu::class, 'dibuat_oleh');
    }

    /**
     * User memiliki banyak angsuran yang diverifikasi.
     *
     * users 1 --- M angsuran
     */
    public function angsuranDiverifikasi(): HasMany
    {
        return $this->hasMany(Angsuran::class, 'verified_by');
    }

    /**
     * User memiliki banyak transaksi simpanan
     * sebagai pencatat.
     *
     * users 1 --- M simpanan
     */
    public function simpananDicatat(): HasMany
    {
        return $this->hasMany(Simpanan::class, 'dicatat_oleh');
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE HELPER
    |--------------------------------------------------------------------------
    */

    /**
     * Mengecek apakah user adalah Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Mengecek apakah user adalah Bendahara.
     */
    public function isBendahara(): bool
    {
        return $this->role === 'bendahara';
    }

    /**
     * Mengecek apakah user adalah Pimpinan.
     */
    public function isPimpinan(): bool
    {
        return $this->role === 'pimpinan';
    }

    /**
     * Mengecek apakah user adalah Anggota.
     */
    public function isAnggota(): bool
    {
        return $this->role === 'anggota';
    }

    /**
     * Mengecek apakah user adalah CS.
     */
    public function isCS(): bool
    {
        return $this->role === 'cs';
    }

    /**
     * Mengecek apakah user adalah AO.
     */
    public function isAO(): bool
    {
        return $this->role === 'ao';
    }

    /**
     * Mengecek apakah user adalah Kabag.
     */
    public function isKabag(): bool
    {
        return $this->role === 'kabag';
    }

    /**
     * Mengecek apakah akun user aktif.
     */
    public function isAktif(): bool
    {
        return $this->status === 'aktif';
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    /**
     * Mendapatkan route dashboard user.
     *
     * Untuk sementara seluruh role diarahkan ke
     * dashboard utama yang memang sudah tersedia.
     *
     * Dashboard khusus setiap role akan dipisahkan
     * pada Fase Dashboard & Monitoring.
     */
    public function getDashboardRoute(): string
    {
        return match ($this->role) {
            'admin',
            'bendahara',
            'pimpinan',
            'anggota',
            'cs',
            'ao',
            'kabag' => 'dashboard',

            default => 'login',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | NOTIFIKASI
    |--------------------------------------------------------------------------
    */

    /**
     * Mengambil jumlah notifikasi yang belum dibaca.
     */
    public function jumlahNotifikasiBelumDibaca(): int
    {
        return $this->notifikasi()
            ->where('is_read', false)
            ->count();
    }
}
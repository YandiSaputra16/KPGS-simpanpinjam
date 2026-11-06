<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Simpanan extends Model
{
    protected $table = 'simpanan';

    protected $fillable = [
        'no_simpanan',
        'anggota_id',
        'jenis_simpanan_id',
        'jumlah',
        'tanggal_transaksi',
        'metode_pembayaran',
        'status',
        'keterangan',
        'bukti',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'jumlah' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    public function jenisSimpanan(): BelongsTo
    {
        return $this->belongsTo(JenisSimpanan::class);
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */

    public function scopeDisetujui(Builder $query): Builder
    {
        return $query->where('status', 'disetujui');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeDitolak(Builder $query): Builder
    {
        return $query->where('status', 'ditolak');
    }

    public function scopeJenisPokok(Builder $query): Builder
    {
        return $query->whereHas(
            'jenisSimpanan',
            fn (Builder $q) => $q->whereIn(
                'kode',
                ['SP', 'POKOK']
            )
        );
    }

    public function scopeJenisWajib(Builder $query): Builder
    {
        return $query->whereHas(
            'jenisSimpanan',
            fn (Builder $q) => $q->whereIn(
                'kode',
                ['SW', 'WAJIB']
            )
        );
    }

    public function scopeJenisSukarela(Builder $query): Builder
    {
        return $query->whereHas(
            'jenisSimpanan',
            fn (Builder $q) => $q->whereIn(
                'kode',
                ['SS', 'SUKARELA']
            )
        );
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

    public function isTunai(): bool
    {
        return $this->metode_pembayaran === 'tunai';
    }

    public function isTransfer(): bool
    {
        return $this->metode_pembayaran === 'transfer';
    }

    /*
    |--------------------------------------------------------------------------
    | PENGAJUAN PEMBAYARAN
    |--------------------------------------------------------------------------
    |
    */

    public static function ajukanPembayaran(array $data): self
    {
        return DB::transaction(function () use ($data) {

            $anggota = Anggota::query()
                ->whereKey($data['anggota_id'] ?? null)
                ->first();

            if (!$anggota) {
                throw ValidationException::withMessages([
                    'anggota_id' => 'Anggota tidak ditemukan.',
                ]);
            }

            if (!$anggota->isAktif()) {
                throw ValidationException::withMessages([
                    'anggota_id' => 'Anggota tidak berstatus aktif.',
                ]);
            }

            $jenis = JenisSimpanan::query()
                ->whereKey($data['jenis_simpanan_id'] ?? null)
                ->where('is_active', true)
                ->first();

            if (!$jenis) {
                throw ValidationException::withMessages([
                    'jenis_simpanan_id' =>
                        'Jenis simpanan tidak ditemukan atau tidak aktif.',
                ]);
            }

            $jumlah = (float) ($data['jumlah'] ?? 0);

            if ($jumlah <= 0) {
                throw ValidationException::withMessages([
                    'jumlah' =>
                        'Jumlah pembayaran harus lebih besar dari 0.',
                ]);
            }

            $metode = $data['metode_pembayaran'] ?? null;

            if (!in_array(
                $metode,
                ['tunai', 'transfer'],
                true
            )) {
                throw ValidationException::withMessages([
                    'metode_pembayaran' =>
                        'Metode pembayaran tidak valid.',
                ]);
            }

            if (
                $metode === 'transfer'
                && empty($data['bukti'])
            ) {
                throw ValidationException::withMessages([
                    'bukti' =>
                        'Bukti pembayaran wajib diunggah untuk pembayaran transfer.',
                ]);
            }

            return self::create([
                'no_simpanan' =>
                    $data['no_simpanan'] ?? self::generateNomor(),

                'anggota_id' =>
                    $anggota->id,

                'jenis_simpanan_id' =>
                    $jenis->id,

                'jumlah' =>
                    $jumlah,

                'tanggal_transaksi' =>
                    $data['tanggal_transaksi']
                    ?? now()->toDateString(),

                'metode_pembayaran' =>
                    $metode,

                'status' =>
                    'pending',

                'keterangan' =>
                    $data['keterangan'] ?? null,

                'bukti' =>
                    $data['bukti'] ?? null,

                'dicatat_oleh' =>
                    $data['dicatat_oleh'] ?? $anggota->user_id,
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | VERIFIKASI PEMBAYARAN
    |--------------------------------------------------------------------------
    |
    | Validasi pembayaran anggota dilakukan oleh CS/Teller.
    |
    */

    public static function verifikasiPembayaran(
        self $simpanan,
        bool $disetujui,
        User $csTeller,
        ?string $catatan = null
    ): self {
        return DB::transaction(function () use (
            $simpanan,
            $disetujui,
            $csTeller,
            $catatan
        ) {

            if (
                $csTeller->role !== 'cs'
                || $csTeller->status !== 'aktif'
            ) {
                throw ValidationException::withMessages([
                    'cs_teller' =>
                        'Hanya CS/Teller aktif yang dapat memverifikasi pembayaran simpanan.',
                ]);
            }

            $simpanan = self::query()
                ->lockForUpdate()
                ->with([
                    'anggota',
                    'jenisSimpanan',
                ])
                ->find($simpanan->id);

            if (!$simpanan) {
                throw ValidationException::withMessages([
                    'simpanan' =>
                        'Data pembayaran tidak ditemukan.',
                ]);
            }

            if (!$simpanan->isPending()) {
                throw ValidationException::withMessages([
                    'simpanan' =>
                        'Pembayaran ini sudah tidak berada dalam status pending.',
                ]);
            }

            if (!$simpanan->anggota) {
                throw ValidationException::withMessages([
                    'simpanan' =>
                        'Data anggota pada pembayaran tidak ditemukan.',
                ]);
            }

            if (!$simpanan->jenisSimpanan) {
                throw ValidationException::withMessages([
                    'simpanan' =>
                        'Jenis simpanan pada pembayaran tidak ditemukan.',
                ]);
            }

            if (!$disetujui) {

                $keteranganLama = trim(
                    (string) $simpanan->keterangan
                );

                $keteranganBaru = trim(
                    (string) $catatan
                );

                if ($keteranganBaru !== '') {
                    $simpanan->keterangan =
                        $keteranganLama !== ''
                        ? $keteranganLama
                            . "\nCatatan CS/Teller: "
                            . $keteranganBaru
                        : 'Catatan CS/Teller: '
                            . $keteranganBaru;
                }

                $simpanan->status = 'ditolak';

                $simpanan->save();

                return $simpanan->fresh([
                    'anggota',
                    'jenisSimpanan',
                    'pencatat',
                ]);
            }

            $kodeJenis = strtoupper(
                trim((string) $simpanan->jenisSimpanan->kode)
            );

            $saldoJenis = match ($kodeJenis) {
                'SP', 'POKOK' => 'pokok',
                'SW', 'WAJIB' => 'wajib',
                'SS', 'SUKARELA' => 'sukarela',
                default => null,
            };

            if ($saldoJenis === null) {
                throw ValidationException::withMessages([
                    'simpanan' =>
                        "Kode jenis simpanan '{$simpanan->jenisSimpanan->kode}' belum memiliki pemetaan saldo anggota.",
                ]);
            }

            if (!$simpanan->anggota->isAktif()) {
                throw ValidationException::withMessages([
                    'simpanan' =>
                        'Anggota tidak lagi berstatus aktif.',
                ]);
            }

            $simpanan->status = 'disetujui';

            if (
                $catatan !== null
                && trim($catatan) !== ''
            ) {
                $keteranganLama = trim(
                    (string) $simpanan->keterangan
                );

                $simpanan->keterangan =
                    $keteranganLama !== ''
                    ? $keteranganLama
                        . "\nCatatan CS/Teller: "
                        . trim($catatan)
                    : 'Catatan CS/Teller: '
                        . trim($catatan);
            }

            $simpanan->save();

            $simpanan->anggota->updateSaldo(
                $saldoJenis,
                (float) $simpanan->jumlah
            );

            Kas::catat([
                'tanggal' =>
                    $simpanan->tanggal_transaksi,

                'jenis' =>
                    'masuk',

                'kategori' =>
                    'simpanan',

                'jumlah' =>
                    (float) $simpanan->jumlah,

                'keterangan' =>
                    $simpanan->keterangan
                    ?: "Pembayaran {$simpanan->jenisSimpanan->nama_jenis} - {$simpanan->anggota->kode_anggota}",

                'referensi_tipe' =>
                    'simpanan',

                'referensi_id' =>
                    $simpanan->id,

                'dicatat_oleh' =>
                    $csTeller->id,
            ]);

            return $simpanan->fresh([
                'anggota',
                'jenisSimpanan',
                'pencatat',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | TRANSAKSI SIMPANAN LANGSUNG
    |--------------------------------------------------------------------------
    |
    */

    public static function catatTransaksi(array $data): self
    {
        return DB::transaction(function () use ($data) {

            $anggota = Anggota::query()
                ->lockForUpdate()
                ->find($data['anggota_id'] ?? null);

            if (!$anggota) {
                throw ValidationException::withMessages([
                    'anggota_id' => 'Anggota tidak ditemukan.',
                ]);
            }

            if (!$anggota->isAktif()) {
                throw ValidationException::withMessages([
                    'anggota_id' => 'Anggota tidak berstatus aktif.',
                ]);
            }

            $jenis = JenisSimpanan::query()
                ->whereKey($data['jenis_simpanan_id'] ?? null)
                ->where('is_active', true)
                ->first();

            if (!$jenis) {
                throw ValidationException::withMessages([
                    'jenis_simpanan_id' =>
                        'Jenis simpanan tidak ditemukan atau tidak aktif.',
                ]);
            }

            $jumlah = (float) ($data['jumlah'] ?? 0);

            if ($jumlah <= 0) {
                throw ValidationException::withMessages([
                    'jumlah' =>
                        'Jumlah simpanan harus lebih besar dari 0.',
                ]);
            }

            $kodeJenis = strtoupper(
                trim((string) $jenis->kode)
            );

            $saldoJenis = match ($kodeJenis) {
                'SP', 'POKOK' => 'pokok',
                'SW', 'WAJIB' => 'wajib',
                'SS', 'SUKARELA' => 'sukarela',
                default => null,
            };

            if ($saldoJenis === null) {
                throw ValidationException::withMessages([
                    'jenis_simpanan_id' =>
                        "Kode jenis simpanan '{$jenis->kode}' belum memiliki pemetaan saldo anggota.",
                ]);
            }

            $simpanan = self::create([
                'no_simpanan' =>
                    $data['no_simpanan'] ?? self::generateNomor(),

                'anggota_id' =>
                    $anggota->id,

                'jenis_simpanan_id' =>
                    $jenis->id,

                'jumlah' =>
                    $jumlah,

                'tanggal_transaksi' =>
                    $data['tanggal_transaksi']
                    ?? now()->toDateString(),

                'metode_pembayaran' =>
                    $data['metode_pembayaran'] ?? 'tunai',

                'status' =>
                    'disetujui',

                'keterangan' =>
                    $data['keterangan'] ?? null,

                'bukti' =>
                    $data['bukti'] ?? null,

                'dicatat_oleh' =>
                    $data['dicatat_oleh'],
            ]);

            $anggota->updateSaldo(
                $saldoJenis,
                $jumlah
            );

            /*
             * Catat kas masuk USP.
             */
            Kas::catat([
                'tanggal' =>
                    $simpanan->tanggal_transaksi,

                'jenis' =>
                    'masuk',

                'kategori' =>
                    'simpanan',

                'jumlah' =>
                    $jumlah,

                'keterangan' =>
                    $simpanan->keterangan
                    ?: "Simpanan {$jenis->nama_jenis} - {$anggota->kode_anggota}",

                'referensi_tipe' =>
                    'simpanan',

                'referensi_id' =>
                    $simpanan->id,

                'dicatat_oleh' =>
                    $simpanan->dicatat_oleh,
            ]);

            return $simpanan->fresh([
                'anggota',
                'jenisSimpanan',
                'pencatat',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE NOMOR
    |--------------------------------------------------------------------------
    */

    public static function generateNomor(): string
    {
        $tahun = now()->year;

        $last = self::whereYear(
            'created_at',
            $tahun
        )
            ->orderBy('id', 'desc')
            ->first();

        $urutan = $last
            ? ((int) substr(
                $last->no_simpanan,
                -4
            )) + 1
            : 1;

        return 'SMP-'
            . $tahun
            . '-'
            . str_pad(
                $urutan,
                4,
                '0',
                STR_PAD_LEFT
            );
    }
}
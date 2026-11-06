<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Kas extends Model
{
    protected $table = 'kas';

    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'jenis',
        'kategori',
        'jumlah',
        'saldo_sebelum',
        'saldo_sesudah',
        'keterangan',
        'referensi_tipe',
        'referensi_id',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */

    public function scopeMasuk(Builder $query): Builder
    {
        return $query->where('jenis', 'masuk');
    }

    public function scopeKeluar(Builder $query): Builder
    {
        return $query->where('jenis', 'keluar');
    }

    public function scopeKategori(
        Builder $query,
        string $kategori
    ): Builder {
        return $query->where('kategori', $kategori);
    }

    public function scopePeriodeAntara(
        Builder $query,
        string $dari,
        string $sampai
    ): Builder {
        return $query->whereBetween('tanggal', [$dari, $sampai]);
    }

    public function scopeTahunIni(Builder $query): Builder
    {
        return $query->whereYear('tanggal', now()->year);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    public function isMasuk(): bool
    {
        return $this->jenis === 'masuk';
    }

    public function isKeluar(): bool
    {
        return $this->jenis === 'keluar';
    }

    /*
    |--------------------------------------------------------------------------
    | SALDO & RINGKASAN
    |--------------------------------------------------------------------------
    */

    public static function getSaldoTerkini(): float
    {
        $last = self::orderBy('id', 'desc')->first();

        return $last
            ? (float) $last->saldo_sesudah
            : 0;
    }

    public static function getTotalMasuk(
        ?int $tahun = null,
        ?int $bulan = null
    ): float {
        $query = self::where('jenis', 'masuk');

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }

        return (float) $query->sum('jumlah');
    }

    public static function getTotalKeluar(
        ?int $tahun = null,
        ?int $bulan = null
    ): float {
        $query = self::where('jenis', 'keluar');

        if ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        if ($bulan) {
            $query->whereMonth('tanggal', $bulan);
        }

        return (float) $query->sum('jumlah');
    }

    public static function getRingkasan(
        ?int $tahun = null,
        ?int $bulan = null
    ): array {
        $masuk = self::getTotalMasuk($tahun, $bulan);
        $keluar = self::getTotalKeluar($tahun, $bulan);

        return [
            'total_masuk' => $masuk,
            'total_keluar' => $keluar,
            'saldo' => $masuk - $keluar,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE NOMOR
    |--------------------------------------------------------------------------
    */

    public static function generateNomor(): string
    {
        $tahun = now()->year;

        $last = self::whereYear('created_at', $tahun)
            ->orderBy('id', 'desc')
            ->first();

        $urutan = $last
            ? ((int) substr($last->no_transaksi, -5)) + 1
            : 1;

        return 'TRX-' . $tahun . '-' . str_pad(
            $urutan,
            5,
            '0',
            STR_PAD_LEFT
        );
    }

    /*
    |--------------------------------------------------------------------------
    | NOTIFIKASI BENDAHARA
    |--------------------------------------------------------------------------
    */

    /**
     * Mengirim notifikasi kepada seluruh Bendahara aktif.
     *
     * Satu transaksi kas hanya menghasilkan satu notifikasi
     * untuk setiap pengguna Bendahara.
     */
    private static function kirimNotifikasiBendahara(self $kas): void
    {
        $bendahara = User::query()
            ->where('role', 'bendahara')
            ->where('status', 'aktif')
            ->get(['id']);

        if ($bendahara->isEmpty()) {
            return;
        }

        $jenis = $kas->jenis === 'masuk'
            ? 'Kas Masuk'
            : 'Kas Keluar';

        $kategori = ucfirst(
            str_replace('_', ' ', $kas->kategori ?? 'lainnya')
        );

        $jumlah = 'Rp ' . number_format(
            (float) $kas->jumlah,
            0,
            ',',
            '.'
        );

        $judul = 'Transaksi Kas Baru';

        $pesan = sprintf(
            '%s kategori %s sebesar %s dengan nomor transaksi %s telah berhasil dicatat dalam buku kas.',
            $jenis,
            $kategori,
            $jumlah,
            $kas->no_transaksi
        );

        $link = route('laporan.keuangan', [], false);

        foreach ($bendahara as $user) {
            Notifikasi::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'kas_id' => $kas->id,
                ],
                [
                    'judul' => $judul,
                    'pesan' => $pesan,
                    'tipe' => Notifikasi::TIPE_KAS,
                    'is_read' => false,
                    'read_at' => null,
                    'link' => $link,
                ]
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CATAT TRANSAKSI
    |--------------------------------------------------------------------------
    */

    public static function catat(array $data): self
    {
        $jumlah = (float) ($data['jumlah'] ?? 0);

        if ($jumlah <= 0) {
            throw ValidationException::withMessages([
                'jumlah' =>
                    'Jumlah transaksi kas harus lebih besar dari 0.',
            ]);
        }

        if (!in_array(
            $data['jenis'] ?? null,
            ['masuk', 'keluar'],
            true
        )) {
            throw ValidationException::withMessages([
                'jenis' =>
                    'Jenis transaksi kas tidak valid.',
            ]);
        }

        /*
         * Cegah transaksi sumber yang sama dicatat dua kali.
         */
        if (
            !empty($data['referensi_tipe'])
            && isset($data['referensi_id'])
        ) {
            $duplikat = self::query()
                ->where('referensi_tipe', $data['referensi_tipe'])
                ->where('referensi_id', $data['referensi_id'])
                ->exists();

            if ($duplikat) {
                throw ValidationException::withMessages([
                    'referensi_id' =>
                        'Transaksi kas untuk sumber transaksi tersebut sudah tercatat.',
                ]);
            }
        }

        /*
         * Ambil saldo terakhir.
         */
        $saldoSebelum = self::query()
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('saldo_sesudah');

        $saldoSebelum = (float) ($saldoSebelum ?? 0);

        if (
            ($data['jenis'] ?? null) === 'keluar'
            && $jumlah > $saldoSebelum
        ) {
            throw ValidationException::withMessages([
                'jumlah' =>
                    'Saldo kas tidak mencukupi. Saldo saat ini: Rp '
                    . number_format($saldoSebelum, 0, ',', '.'),
            ]);
        }

        $saldoSesudah = $data['jenis'] === 'masuk'
            ? $saldoSebelum + $jumlah
            : $saldoSebelum - $jumlah;

        /*
         * Simpan transaksi kas.
         */
        $kas = self::create(
            array_merge(
                $data,
                [
                    'no_transaksi' => self::generateNomor(),
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $saldoSesudah,
                ]
            )
        );

        /*
         * Kirim notifikasi setelah transaksi database commit.
         *
         * Jika tidak ada transaksi database aktif,
         * notifikasi langsung dibuat setelah penyimpanan kas.
         */
        $kirimNotifikasi = static function () use ($kas): void {
            self::kirimNotifikasiBendahara($kas);
        };

        if (DB::transactionLevel() > 0) {
            DB::afterCommit($kirimNotifikasi);
        } else {
            $kirimNotifikasi();
        }

        return $kas;
    }
}

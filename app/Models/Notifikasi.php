<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;



class Notifikasi extends Model  // ← ganti nama class
{
     /*
    |--------------------------------------------------------------------------
    | TIPE NOTIFIKASI
    |--------------------------------------------------------------------------
    */

    public const TIPE_PINJAMAN = 'pinjaman';
    public const TIPE_ANGSURAN = 'angsuran';
    public const TIPE_SIMPANAN = 'simpanan';
    public const TIPE_APPROVAL = 'approval';
    public const TIPE_KAS = 'kas';
    public const TIPE_SHU = 'shu';
    public const TIPE_SISTEM = 'sistem';

    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'kas_id',
        'judul',
        'pesan',
        'tipe',
        'is_read',
        'read_at',  // ← kolom di database yang belum masuk
        'link',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime', // ← tambahan
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

    public function kas(): BelongsTo
    {
        return $this->belongsTo(Kas::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */

    // Filter belum dibaca — dipakai di bell notification topbar
    public function scopeBelumDibaca(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }
    public function scopeSudahDibaca(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }
    // Filter per tipe — dipakai di halaman notifikasi
    public function scopeTipe(Builder $query, string $tipe): Builder
    {
        return $query->where('tipe', $tipe);
    }

    // Notifikasi milik user tertentu
    public function scopeMilikUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */

    public function isBelumDibaca(): bool
    {
        return $this->is_read === false;
    }

    public function isSudahDibaca(): bool
    {
        return $this->is_read === true;
    }

        public function memilikiLink(): bool
    {
        return !empty($this->link);
    }

    // Tandai satu notifikasi sebagai sudah dibaca
    public function tandaiDibaca(): void
    {
        if ($this->isBelumDibaca()) {
            $this->update([
                'is_read' => true,
                'read_at' => now(), // ← update read_at sekaligus
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | STATIC
    |--------------------------------------------------------------------------
    */

    // Kirim notifikasi in-app ke user tertentu
    public static function kirim(
        int $userId,
        string $judul,
        string $pesan,
        string $tipe = 'sistem',
        ?string $link = null
    ): self {
        // Beberapa pemanggil lama belum mengirimkan tipe secara eksplisit.
        if ($tipe === 'sistem') {
            $teks = mb_strtolower($judul . ' ' . $pesan);

            if (str_contains($teks, 'angsuran') || str_contains($teks, 'pembayaran angsuran')) {
                $tipe = self::TIPE_ANGSURAN;
            } elseif (str_contains($teks, 'simpanan')) {
                $tipe = self::TIPE_SIMPANAN;
            } elseif (str_contains($teks, 'pinjaman') || str_contains($teks, 'pengajuan pinjaman')) {
                $tipe = self::TIPE_PINJAMAN;
            }
        }

        // Bila pemanggil tidak memberikan link, coba buat link ke transaksi yang disebutkan.
        if ($link === null) {
            $link = self::buatLinkTransaksi($userId, $tipe, $judul, $pesan);
        }

        return self::create([
            'user_id' => $userId,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'tipe'    => $tipe,
            'is_read' => false,
            'read_at' => null,
            'link'    => $link,
        ]);
    }

    /**
     * Membuat URL detail sesuai role penerima dan transaksi yang disebutkan.
     * Link disimpan sebagai path relatif agar tetap berlaku di localhost maupun server.
     */
    private static function buatLinkTransaksi(int $userId, string $tipe, string $judul, string $pesan): ?string
    {
        if (! app()->bound('router')) {
            return null;
        }

        $user = User::query()->find($userId);
        if (! $user) {
            return null;
        }

        $teks = $judul . ' ' . $pesan;
        // Notifikasi transaksi kas diarahkan ke laporan keuangan.
        if ($tipe === self::TIPE_KAS) {
            return Route::has('laporan.keuangan')
                ? route('laporan.keuangan', [], false)
                : null;
        }
        $routeName = null;
        $routeParameters = [];

        // Notifikasi proses pinjaman/angsuran umumnya menyebut nomor PIN-YYYY-NNNN.
        if (preg_match('/\b(PIN-\d{4}-\d+)\b/i', $teks, $match)) {
            $pinjaman = Pinjaman::query()->where('no_pinjaman', $match[1])->first();
            if ($pinjaman) {
                if ($tipe === self::TIPE_ANGSURAN || stripos($teks, 'angsuran') !== false || stripos($teks, 'pembayaran') !== false) {
                    if ($user->role === 'anggota') {
                        // Jika nomor angsuran disebutkan, buka transaksi angsuran spesifik.
                        if (preg_match('/angsuran\s+ke[- ]?(\d+)/i', $teks, $nomorAngsuran)) {
                            $angsuran = Angsuran::query()
                                ->where('pinjaman_id', $pinjaman->id)
                                ->where('angsuran_ke', (int) $nomorAngsuran[1])
                                ->first();
                            if ($angsuran && Route::has('angsuran.saya.transaksi')) {
                                return route('angsuran.saya.transaksi', $angsuran->id, false);
                            }
                        }
                        $routeName = 'angsuran.saya.index';
                        $routeParameters = [];
                    } elseif ($user->role === 'cs') {
                        $query = Angsuran::query()->where('pinjaman_id', $pinjaman->id);
                        if (stripos($teks, 'verifikasi') !== false || stripos($teks, 'pembayaran') !== false) {
                            $query->where('status_pembayaran', 'menunggu_verifikasi');
                        }
                        $angsuran = $query->orderBy('angsuran_ke')->first();
                        if ($angsuran) {
                            $routeName = 'angsuran.show';
                            $routeParameters = [$angsuran->id];
                        } else {
                            $routeName = 'angsuran.index';
                        }
                    }
                } else {
                    $routeName = match ($user->role) {
                        'cs' => 'pinjaman.cs.show',
                        'ao' => 'pinjaman.ao.show',
                        'kabag' => 'pinjaman.kabag.show',
                        'bendahara' => 'pinjaman.bendahara.show',
                        // Anggota diarahkan ke daftar pinjaman miliknya.
                        'anggota' => 'pinjaman.anggota.index',
                        default => null,
                    };
                    $routeParameters = $user->role === 'anggota'
                        ? []
                        : [$pinjaman->id];
                }
            }
        }

        // Simpanan: jika pesan menyebut ID transaksi secara eksplisit, buka detail sesuai role.
        if ($routeName === null && $tipe === self::TIPE_SIMPANAN && preg_match('/(?:simpanan|transaksi)\s*(?:#|nomor\s*)?(\d+)/i', $teks, $matchId)) {
            $simpananId = (int) $matchId[1];
            $routeName = match ($user->role) {
                'cs' => 'simpanan.show',
                'anggota' => 'simpanan.saya.show',
                default => null,
            };
            $routeParameters = [$simpananId];
        }

        if ($routeName && Route::has($routeName)) {
            try {
                return route($routeName, $routeParameters, false);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return null;
    }

    // Tandai semua notifikasi milik user sebagai sudah dibaca
    public static function bacaSemua(int $userId): void
    {
        self::where('user_id', $userId)
            ->where('is_read', false)       // ← hanya yang belum dibaca
            ->update([
                'is_read' => true,
                'read_at' => now(),         // ← update read_at sekaligus
            ]);
    }

    // Hitung notifikasi belum dibaca milik user
    public static function jumlahBelumDibaca(int $userId): int
    {
        return self::where('user_id', $userId)
                   ->where('is_read', false)
                   ->count();
    }
}

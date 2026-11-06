<?php

namespace App\Models\Traits;

use App\Models\Anggota;
use App\Models\DataPendukungPinjaman;
use App\Models\MasterPinjaman;
use App\Models\User;
use App\Models\Notifikasi;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

trait PinjamanValidasi
{
    /*
    |--------------------------------------------------------------------------
    | VALIDASI PENGAJUAN
    |--------------------------------------------------------------------------
    */

    public static function validasiPengajuan(
        Anggota $anggota,
        MasterPinjaman $paket,
        float $jumlah,
        int $tenor,
        bool $setujuSyarat
    ): void {
        // 1. Anggota harus aktif
        if ($anggota->status_keanggotaan !== 'aktif') {
            throw new \Exception(
                'Keanggotaan tidak aktif.'
            );
        }

        // 2. Minimal 3 bulan menjadi anggota
        $lamaGabung = $anggota->tanggal_daftar->diffInMonths(now());

        if ($lamaGabung < 3) {
            throw new \Exception(
                'Minimal telah menjadi anggota selama 3 bulan.'
            );
        }

        // 3. Tidak boleh memiliki pinjaman yang masih aktif
        $punyaPinjamanAktif = static::where(
            'anggota_id',
            $anggota->id
        )
            ->whereIn('status', [
                'menunggu_cs',
                'menunggu_ao',
                'menunggu_kabag',
                'disetujui',
                'dicairkan',
            ])
            ->exists();

        if ($punyaPinjamanAktif) {
            throw new \Exception(
                'Masih memiliki pinjaman atau pengajuan yang sedang diproses.'
            );
        }

        // 4. Waktu tunggu 14 hari setelah ditolak
        $pinjamanDitolakTerakhir = static::where(
            'anggota_id',
            $anggota->id
        )
            ->where('status', 'ditolak')
            ->latest('updated_at')
            ->first();

        if ($pinjamanDitolakTerakhir) {
            $hariSejakDitolak = (int) $pinjamanDitolakTerakhir
                ->updated_at
                ->diffInDays(now());

            if ($hariSejakDitolak < 14) {
                $sisaHari = 14 - $hariSejakDitolak;

                throw new \Exception(
                    "Anda baru dapat mengajukan pinjaman lagi dalam {$sisaHari} hari."
                );
            }
        }

        // 5. Paket pinjaman harus aktif
        if (!$paket->is_active) {
            throw new \Exception(
                'Paket pinjaman tidak tersedia.'
            );
        }

        // 6. Nominal sesuai plafon
        if (
            $jumlah < $paket->plafon_min ||
            $jumlah > $paket->plafon_max
        ) {
            throw new \Exception(
                'Jumlah pinjaman tidak sesuai ketentuan paket.'
            );
        }

        // 7. Tenor sesuai paket
        if (
            $tenor < $paket->tenor_min ||
            $tenor > $paket->tenor_max
        ) {
            throw new \Exception(
                'Tenor tidak sesuai ketentuan paket.'
            );
        }

        // 8. Wajib menyetujui syarat
        if (!$setujuSyarat) {
            throw new \Exception(
                'Anda harus menyetujui syarat pinjaman.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDASI DATA PENDUKUNG
    |--------------------------------------------------------------------------
    */

    protected static function validasiDataPendukung(
        array $dataPendukung
    ): void {
        $fieldWajib = [
            'produksi_susu_bulanan',
            'jumlah_sapi',
            'kondisi_usaha',
            'pendapatan_bulanan',
            'kewajiban_angsuran_lain',
        ];

        foreach ($fieldWajib as $field) {
            if (
                !array_key_exists($field, $dataPendukung) ||
                $dataPendukung[$field] === null ||
                $dataPendukung[$field] === ''
            ) {
                throw new \Exception(
                    "Data pendukung {$field} wajib diisi."
                );
            }
        }

        // Produksi susu tidak boleh negatif
        if ((float) $dataPendukung['produksi_susu_bulanan'] < 0) {
            throw new \Exception(
                'Produksi susu bulanan tidak boleh bernilai negatif.'
            );
        }

        // Jumlah sapi minimal 0
        if ((int) $dataPendukung['jumlah_sapi'] < 0) {
            throw new \Exception(
                'Jumlah sapi tidak boleh bernilai negatif.'
            );
        }

        // Pendapatan tidak boleh negatif
        if ((float) $dataPendukung['pendapatan_bulanan'] < 0) {
            throw new \Exception(
                'Pendapatan bulanan tidak boleh bernilai negatif.'
            );
        }

        // Kewajiban angsuran lain tidak boleh negatif
        if ((float) $dataPendukung['kewajiban_angsuran_lain'] < 0) {
            throw new \Exception(
                'Total angsuran lain tidak boleh bernilai negatif.'
            );
        }

        // Sesuai enum database
        if (
            !in_array(
                $dataPendukung['kondisi_usaha'],
                ['baik', 'cukup', 'kurang'],
                true
            )
        ) {
            throw new \Exception(
                'Kondisi usaha tidak valid.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | AJUKAN PINJAMAN
    |--------------------------------------------------------------------------
    */

    public static function ajukanPinjaman(
        Anggota $anggota,
        MasterPinjaman $paket,
        float $jumlah,
        int $tenor,
        string $tujuan,
        bool $setujuSyarat,
        array $dataPendukung
    ): static {
        // Validasi utama pengajuan
        static::validasiPengajuan(
            $anggota,
            $paket,
            $jumlah,
            $tenor,
            $setujuSyarat
        );

        // Validasi data pendukung
        static::validasiDataPendukung(
            $dataPendukung
        );

        // Hitung simulasi angsuran
        $hitung = static::hitungAngsuran(
            $jumlah,
            $tenor,
            $paket->bunga_persen
        );

        /*
        |--------------------------------------------------------------------------
        | SIMPAN DALAM SATU TRANSAKSI
        |--------------------------------------------------------------------------
        */

        return DB::transaction(function () use (
            $anggota,
            $paket,
            $jumlah,
            $tenor,
            $tujuan,
            $setujuSyarat,
            $dataPendukung,
            $hitung
        ) {

            // Buat data pinjaman
           $pinjaman = static::create([
            'no_pinjaman'          => static::generateNomor(),

            'anggota_id'           => $anggota->id,
            'master_pinjaman_id'   => $paket->id,

            'jumlah_pinjaman'      => $jumlah,
            'jumlah_rekomendasi'   => null,
            'jumlah_disetujui'     => null,

            'tenor'                => $tenor,

            'bunga_persen'         => $paket->bunga_persen,
            'denda_persen'         => $paket->denda_persen,

            'angsuran_per_bulan'   => $hitung['angsuran_per_bulan'],
            'total_bunga'          => $hitung['total_bunga'],
            'total_angsuran'       => $hitung['total_angsuran'],

            'sisa_pinjaman'        => $jumlah,

            'tanggal_pengajuan'    => today(),

            'tujuan_pinjaman'      => $tujuan,

            'disetujui_syarat'     => $setujuSyarat,
            'waktu_setujui_syarat' => now(),
            'ip_setujui_syarat'    => request()->ip(),

            'status'               => 'menunggu_cs',
        ]);

            // Buat data pendukung
            DataPendukungPinjaman::create([
                'pinjaman_id'             => $pinjaman->id,

                'produksi_susu_bulanan'   =>
                    $dataPendukung['produksi_susu_bulanan'],

                'jumlah_sapi'             =>
                    $dataPendukung['jumlah_sapi'],

                'kondisi_usaha'           =>
                    $dataPendukung['kondisi_usaha'],

                'pendapatan_bulanan'      =>
                    $dataPendukung['pendapatan_bulanan'],

                'kewajiban_angsuran_lain' =>
                    $dataPendukung['kewajiban_angsuran_lain'],

                'keterangan_tambahan'     =>
                    $dataPendukung['keterangan_tambahan'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | NOTIFIKASI CS
            |--------------------------------------------------------------------------
            */

            User::where('role', 'cs')
                ->where('status', 'aktif')
                ->get()
                ->each(
                    fn ($user) => Notifikasi::kirim(
                        $user->id,
                        'Pengajuan Pinjaman Baru',
                        'Ada pengajuan pinjaman '
                        . $pinjaman->no_pinjaman
                        . ' yang perlu diperiksa.',
                        'pinjaman'
                    )
                );

            /*
            |--------------------------------------------------------------------------
            | ACTIVITY LOG
            |--------------------------------------------------------------------------
            */

            ActivityLog::catat(
                'create',
                'Anggota mengajukan pinjaman '
                . $pinjaman->no_pinjaman,
                static::class,
                $pinjaman->id,
                null,
                $pinjaman->fresh()->toArray()
            );

            return $pinjaman;
        });
    }
}
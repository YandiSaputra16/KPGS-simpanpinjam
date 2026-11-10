<?php

namespace App\Models\Traits;

use App\Models\ActivityLog;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;

trait PinjamanPembayaran
{
    /*
    |--------------------------------------------------------------------------
    | VALIDASI BERURUTAN
    |--------------------------------------------------------------------------
    */

    public function validasiBerurutan(array $angsuranIds): void
    {
        $angsuranBelumLunas = $this->angsuran()
            ->whereNotIn('status_pembayaran', ['lunas'])
            ->orderBy('angsuran_ke')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $angsuranDipilihBerurutan = $this->angsuran()
            ->whereIn('id', $angsuranIds)
            ->orderBy('angsuran_ke')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();

        $angsuranDiizinkan = array_slice(
            $angsuranBelumLunas,
            0,
            count($angsuranIds)
        );

        if (
            count($angsuranDipilihBerurutan) !== count($angsuranIds)
            || $angsuranDipilihBerurutan !== $angsuranDiizinkan
        ) {
            throw new \Exception(
                'Pembayaran harus dimulai dari angsuran pertama yang belum dibayar dan harus berurutan.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | BAYAR ANGSURAN
    |--------------------------------------------------------------------------
    */

    public function bayarAngsuran(
        array $angsuranIds,
        string $buktiPath
    ): void {
        if (!$this->isDicairkan()) {
            throw new \Exception(
                'Pinjaman belum dicairkan.'
            );
        }

        if (empty($angsuranIds)) {
            throw new \Exception(
                'Pilih minimal satu angsuran yang akan dibayar.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan semua angsuran milik pinjaman
        |--------------------------------------------------------------------------
        */

        $angsuranMilikPinjaman = $this->angsuran()
            ->whereIn('id', $angsuranIds)
            ->count();

        if ($angsuranMilikPinjaman !== count($angsuranIds)) {
            throw new \Exception(
                'Terdapat angsuran yang tidak valid.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan belum lunas dan tidak sedang diverifikasi
        |--------------------------------------------------------------------------
        */

        $statusTidakValid = $this->angsuran()
            ->whereIn('id', $angsuranIds)
            ->whereIn(
                'status_pembayaran',
                [
                    'lunas',
                    'menunggu_verifikasi',
                ]
            )
            ->exists();

        if ($statusTidakValid) {
            throw new \Exception(
                'Terdapat angsuran yang sudah lunas atau sedang menunggu verifikasi.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi urutan pembayaran
        |--------------------------------------------------------------------------
        */

        $this->validasiBerurutan($angsuranIds);

        DB::transaction(function () use (
            $angsuranIds,
            $buktiPath
        ) {

            $batchId = 'PAY-' . now()->format('YmdHis');

            $angsuranList = $this->angsuran()
                ->whereIn('id', $angsuranIds)
                ->orderBy('angsuran_ke')
                ->lockForUpdate()
                ->get();

            foreach ($angsuranList as $angsuran) {

                /*
                |--------------------------------------------------------------------------
                | Hitung denda apabila terlambat
                |--------------------------------------------------------------------------
                */

                $angsuran->hitungDanUpdateDenda();
                $angsuran->refresh();

                /*
                |--------------------------------------------------------------------------
                | Simpan riwayat bukti pembayaran
                |--------------------------------------------------------------------------
                */

                $buktiLama = $angsuran->bukti_pembayaran ?? [];

                if (!is_array($buktiLama)) {
                    $buktiLama = [];
                }

                $buktiUpdate = array_merge(
                    $buktiLama,
                    [$buktiPath]
                );

                /*
                |--------------------------------------------------------------------------
                | Simpan pengajuan pembayaran
                |--------------------------------------------------------------------------
                */

                $angsuran->update([
                    'status_pembayaran' => 'menunggu_verifikasi',
                    'jumlah_bayar'      => $angsuran->total_tagihan,
                    'tanggal_bayar'     => today(),
                    'bukti_pembayaran'  => $buktiUpdate,
                    'payment_batch_id'  => $batchId,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Notifikasi kepada anggota
            |--------------------------------------------------------------------------
            */

            Notifikasi::kirim(
                $this->anggota->user_id,
                'Pembayaran Angsuran Dikirim',
                'Pembayaran '
                . count($angsuranIds)
                . ' angsuran pada pinjaman '
                . $this->no_pinjaman
                . ' telah diterima sistem dan sedang menunggu verifikasi CS/Teller.'
            );

            /*
            |--------------------------------------------------------------------------
            | Notifikasi kepada CS/Teller
            |--------------------------------------------------------------------------
            */

            \App\Models\User::where('role', 'cs')
                ->where('status', 'aktif')
                ->get()
                ->each(
                    fn ($user) => Notifikasi::kirim(
                        $user->id,
                        'Pembayaran Angsuran Baru',
                        'Ada pembayaran '
                        . count($angsuranIds)
                        . ' angsuran dari pinjaman '
                        . $this->no_pinjaman
                        . ' yang perlu diverifikasi.'
                    )
                );

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */

            ActivityLog::catat(
                'update',
                'Anggota mengajukan pembayaran '
                . count($angsuranIds)
                . ' angsuran pinjaman '
                . $this->no_pinjaman,
                'Pinjaman',
                $this->id
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPLOAD ULANG BUKTI
    |--------------------------------------------------------------------------
    */

    public function uploadUlangBukti(
        array $angsuranIds,
        string $buktiBaruPath
    ): void {
        $statusTidakValid = $this->angsuran()
            ->whereIn('id', $angsuranIds)
            ->whereNotIn(
                'status_pembayaran',
                ['belum_bayar', 'ditolak']
            )
            ->exists();

        if ($statusTidakValid) {
            throw new \Exception(
                'Hanya angsuran yang belum dibayar atau ditolak yang dapat diajukan kembali.'
            );
        }

        $this->validasiBerurutan($angsuranIds);

        DB::transaction(function () use (
            $angsuranIds,
            $buktiBaruPath
        ) {

            $batchId = 'PAY-' . now()->format('YmdHis');

            $angsuranList = $this->angsuran()
                ->whereIn('id', $angsuranIds)
                ->orderBy('angsuran_ke')
                ->lockForUpdate()
                ->get();

            foreach ($angsuranList as $angsuran) {

                $buktiLama = $angsuran->bukti_pembayaran ?? [];

                if (!is_array($buktiLama)) {
                    $buktiLama = [];
                }

                $buktiUpdate = $buktiLama;

                if (!in_array(
                    $buktiBaruPath,
                    $buktiUpdate,
                    true
                )) {
                    $buktiUpdate[] = $buktiBaruPath;
                }

                $angsuran->update([
                    'status_pembayaran' => 'menunggu_verifikasi',
                    'jumlah_bayar'      => $angsuran->total_tagihan,
                    'tanggal_bayar'     => today(),
                    'bukti_pembayaran'  => $buktiUpdate,
                    'payment_batch_id'  => $batchId,
                    'verified_by'       => null,
                    'tanggal_verifikasi'=> null,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Notifikasi anggota
            |--------------------------------------------------------------------------
            */

            Notifikasi::kirim(
                $this->anggota->user_id,
                'Bukti Pembayaran Diperbarui',
                'Bukti pembayaran angsuran pada pinjaman '
                . $this->no_pinjaman
                . ' telah diperbarui dan sedang menunggu verifikasi CS/Teller.'
            );

            /*
            |--------------------------------------------------------------------------
            | Notifikasi CS/Teller
            |--------------------------------------------------------------------------
            */

            \App\Models\User::where('role', 'cs')
                ->where('status', 'aktif')
                ->get()
                ->each(
                    fn ($user) => Notifikasi::kirim(
                        $user->id,
                        'Bukti Pembayaran Diperbarui',
                        'Anggota telah mengupload ulang bukti pembayaran angsuran pinjaman '
                        . $this->no_pinjaman
                        . '. Silakan verifikasi kembali.'
                    )
                );

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */

            ActivityLog::catat(
                'update',
                'Anggota mengupload ulang bukti pembayaran angsuran pinjaman '
                . $this->no_pinjaman,
                'Pinjaman',
                $this->id
            );
        });
    }
}
<?php

namespace App\Models\Traits;

use App\Models\ActivityLog;
use App\Models\Angsuran;
use App\Models\Kas;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\DB;

trait AngsuranVerifikasi
{
    /*
    |--------------------------------------------------------------------------
    | VERIFIKASI PEMBAYARAN
    |--------------------------------------------------------------------------
    */
    
    public function verifikasi(User $csTeller): void
    {
        if (!$this->isMenungguVerifikasi()) {
            throw new \Exception(
                'Angsuran tidak dalam status menunggu verifikasi.'
            );
        }

        if ($csTeller->role !== 'cs') {
            throw new \Exception(
                'Hanya CS/Teller yang dapat memverifikasi pembayaran angsuran.'
            );
        }

        DB::transaction(function () use ($csTeller) {

            /*
            |--------------------------------------------------------------------------
            | Ambil seluruh angsuran dalam satu batch pembayaran
            |--------------------------------------------------------------------------
            |
            */

            $angsuranSekelompok = $this->pinjaman
                ->angsuran()
                ->where('status_pembayaran', 'menunggu_verifikasi')
                ->where('payment_batch_id', $this->payment_batch_id)
                ->orderBy('angsuran_ke')
                ->lockForUpdate()
                ->get();

            if ($angsuranSekelompok->isEmpty()) {
                throw new \Exception(
                    'Data pembayaran angsuran tidak ditemukan.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi ulang seluruh angsuran
            |--------------------------------------------------------------------------
            */

            foreach ($angsuranSekelompok as $angsuran) {

                if ((float) $angsuran->jumlah_bayar <= 0) {
                    throw new \Exception(
                        'Jumlah pembayaran angsuran tidak valid.'
                    );
                }

                $angsuran->update([
                    'status_pembayaran'  => 'lunas',
                    'verified_by'        => $csTeller->id,
                    'tanggal_verifikasi' => now(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | Catat uang masuk ke Kas USP
                |--------------------------------------------------------------------------
                */

                Kas::catat([
                    'tanggal'        => now(),
                    'jenis'          => 'masuk',
                    'kategori'       => 'angsuran',
                    'jumlah'         => $angsuran->jumlah_bayar,
                    'keterangan'     => 'Pembayaran angsuran ke-'
                        . $angsuran->angsuran_ke
                        . ' - '
                        . $this->pinjaman->no_pinjaman,
                    'referensi_tipe' => 'angsuran',
                    'referensi_id'   => $angsuran->id,
                    'dicatat_oleh'   => $csTeller->id,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Update sisa pinjaman
            |--------------------------------------------------------------------------
            */

            $this->pinjaman->updateSisaPinjaman();

            /*
            |--------------------------------------------------------------------------
            | Cek apakah seluruh angsuran sudah lunas
            |--------------------------------------------------------------------------
            */

            $masihAdaYangBelumLunas = $this->pinjaman
                ->angsuran()
                ->where('status_pembayaran', '!=', 'lunas')
                ->exists();

            if (!$masihAdaYangBelumLunas) {
                $this->pinjaman->update([
                    'status' => 'lunas',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Notifikasi kepada anggota
            |--------------------------------------------------------------------------
            */

            Notifikasi::kirim(
                $this->pinjaman->anggota->user_id,
                'Pembayaran Angsuran Diverifikasi',
                'Pembayaran angsuran ke-'
                . $this->angsuran_ke
                . ' pada pinjaman '
                . $this->pinjaman->no_pinjaman
                . ' telah diverifikasi dan dinyatakan lunas.'
            );

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */

            ActivityLog::catat(
                'update',
                'CS/Teller memverifikasi pembayaran angsuran ke-'
                . $this->angsuran_ke
                . ' - '
                . $this->pinjaman->no_pinjaman,
                'Angsuran',
                $this->id
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | TOLAK PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    public function tolak(User $csTeller, string $alasan): void
    {
        if (!$this->isMenungguVerifikasi()) {
            throw new \Exception(
                'Angsuran tidak dalam status menunggu verifikasi.'
            );
        }

        if ($csTeller->role !== 'cs') {
            throw new \Exception(
                'Hanya CS/Teller yang dapat menolak pembayaran angsuran.'
            );
        }

        DB::transaction(function () use ($csTeller, $alasan) {

            /*
            |--------------------------------------------------------------------------
            | Ambil seluruh angsuran dalam batch yang sama
            |--------------------------------------------------------------------------
            */

            $angsuranSekelompok = $this->pinjaman
                ->angsuran()
                ->where('status_pembayaran', 'menunggu_verifikasi')
                ->where('payment_batch_id', $this->payment_batch_id)
                ->orderBy('angsuran_ke')
                ->lockForUpdate()
                ->get();

            if ($angsuranSekelompok->isEmpty()) {
                throw new \Exception(
                    'Data pembayaran angsuran tidak ditemukan.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Kembalikan menjadi belum bayar
            |--------------------------------------------------------------------------
            |
            */

            foreach ($angsuranSekelompok as $angsuran) {
                $angsuran->update([
                    'status_pembayaran'  => 'belum_bayar',
                    'verified_by'        => null,
                    'tanggal_verifikasi' => null,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Notifikasi kepada anggota
            |--------------------------------------------------------------------------
            */

            Notifikasi::kirim(
                $this->pinjaman->anggota->user_id,
                'Pembayaran Angsuran Perlu Diperbaiki',
                'Pembayaran angsuran ke-'
                . $this->angsuran_ke
                . ' pada pinjaman '
                . $this->pinjaman->no_pinjaman
                . ' belum dapat diverifikasi.'
                . ' Alasan: '
                . $alasan
                . '. Silakan periksa dan ajukan kembali pembayaran.'
            );

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */

            ActivityLog::catat(
                'update',
                'CS/Teller menolak pembayaran angsuran ke-'
                . $this->angsuran_ke
                . ' - '
                . $this->pinjaman->no_pinjaman
                . '. Alasan: '
                . $alasan,
                'Angsuran',
                $this->id
            );
        });
    }
}
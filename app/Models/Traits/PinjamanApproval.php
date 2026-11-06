<?php

namespace App\Models\Traits;

use App\Models\ApprovalPinjaman;
use App\Models\Kas;
use App\Models\User;
use App\Models\Notifikasi;
use App\Models\ActivityLog;

trait PinjamanApproval
{
    /*
    |--------------------------------------------------------------------------
    | CS - PROSES PENGAJUAN
    |--------------------------------------------------------------------------
    */

    public function prosesOlehCS(User $cs): void
    {
        if ($cs->role !== 'cs') {
            throw new \Exception(
                'Hanya CS yang dapat memproses pengajuan pinjaman.'
            );
        }

        if (!$this->isMenungguCS()) {
            throw new \Exception(
                'Pinjaman tidak sedang menunggu pemeriksaan CS.'
            );
        }

        \DB::transaction(function () use ($cs) {
            $this->update([
                'status' => 'menunggu_ao',
            ]);

            ActivityLog::catat(
                'update',
                'CS memproses pengajuan pinjaman '
                    . $this->no_pinjaman,
                static::class,
                $this->id,
                null,
                $this->fresh()->toArray()
            );

            User::where('role', 'ao')
                ->where('status', 'aktif')
                ->get()
                ->each(fn ($user) => Notifikasi::kirim(
                    $user->id,
                    'Pengajuan Pinjaman Baru',
                    'Pengajuan pinjaman '
                        . $this->no_pinjaman
                        . ' telah diproses CS dan menunggu analisis AO.',
                    'pinjaman'
                ));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CS - MENOLAK PENGAJUAN
    |--------------------------------------------------------------------------
    */

    public function tolakOlehCS(
        User $cs,
        string $alasan
    ): void {
        if ($cs->role !== 'cs') {
            throw new \Exception(
                'Hanya CS yang dapat memproses pengajuan.'
            );
        }

        if (!$this->isMenungguCS()) {
            throw new \Exception(
                'Pinjaman tidak sedang menunggu pemeriksaan CS.'
            );
        }

        $alasan = trim($alasan);

        if ($alasan === '') {
            throw new \Exception(
                'Alasan penolakan wajib diisi.'
            );
        }

        \DB::transaction(function () use ($cs, $alasan) {
            $this->update([
                'status' => 'ditolak',
            ]);

            ActivityLog::catat(
                'update',
                'CS menolak pengajuan pinjaman '
                    . $this->no_pinjaman
                    . '. Alasan: ' . $alasan,
                static::class,
                $this->id,
                null,
                $this->fresh()->toArray()
            );

            Notifikasi::kirim(
                $this->anggota->user_id,
                'Pengajuan Pinjaman Ditolak',
                'Pengajuan pinjaman '
                    . $this->no_pinjaman
                    . ' ditolak pada tahap pemeriksaan CS. '
                    . 'Alasan: ' . $alasan,
                'pinjaman'
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | AO - ANALISIS DAN REKOMENDASI
    |--------------------------------------------------------------------------
    */

    
public function prosesOlehAO(
    User $ao,
    float $jumlahRekomendasi,
    ?string $catatan = null
): void {
    if ($ao->role !== 'ao') {
        throw new \Exception(
            'Hanya AO yang dapat melakukan analisis pinjaman.'
        );
    }

    if (!is_finite($jumlahRekomendasi) || $jumlahRekomendasi <= 0) {
        throw new \Exception(
            'Nominal rekomendasi harus lebih besar dari 0 dan valid.'
        );
    }

    \DB::transaction(function () use (
        $ao,
        $jumlahRekomendasi,
        $catatan
    ) {
        // Kunci baris agar proses AO yang bersamaan tidak
        // memproses pinjaman yang sama secara bersamaan.
        $pinjaman = static::query()
            ->whereKey($this->getKey())
            ->lockForUpdate()
            ->firstOrFail();

        // Periksa status terkini setelah memperoleh kunci.
        if (!$pinjaman->isMenungguAO()) {
            throw new \Exception(
                'Pinjaman tidak sedang menunggu analisis AO.'
            );
        }

        // Validasi nominal terhadap data terbaru di database.
        if (
            $jumlahRekomendasi >
            (float) $pinjaman->jumlah_pinjaman
        ) {
            throw new \Exception(
                'Nominal rekomendasi tidak boleh melebihi '
                . 'jumlah yang diajukan anggota.'
            );
        }

        $catatan = trim((string) $catatan);

        $pinjaman->update([
            'jumlah_rekomendasi' => $jumlahRekomendasi,
            'status' => 'menunggu_kabag',
        ]);

        ApprovalPinjaman::updateOrCreate(
            [
                'pinjaman_id' => $pinjaman->id,
                'level' => 'ao',
            ],
            [
                'user_id' => $ao->id,
                'urutan' => 1,
                'status' => 'direkomendasikan',
                'tanggal_approval' => now(),
                'catatan' => $catatan !== ''
                    ? $catatan
                    : null,
            ]
        );

        ActivityLog::catat(
            'update',
            'AO menyelesaikan analisis dan memberikan rekomendasi '
                . 'pinjaman ' . $pinjaman->no_pinjaman
                . ' sebesar Rp '
                . number_format(
                    $jumlahRekomendasi,
                    0,
                    ',',
                    '.'
                ),
            static::class,
            $pinjaman->id,
            null,
            $pinjaman->fresh()->toArray()
        );

        User::where('role', 'kabag')
            ->where('status', 'aktif')
            ->get()
            ->each(fn ($user) => Notifikasi::kirim(
                $user->id,
                'Pengajuan Menunggu Persetujuan',
                'Pengajuan pinjaman '
                    . $pinjaman->no_pinjaman
                    . ' telah dianalisis AO dengan rekomendasi sebesar Rp '
                    . number_format(
                        $jumlahRekomendasi,
                        0,
                        ',',
                        '.'
                    )
                    . ' dan menunggu persetujuan Kabag.',
                'pinjaman'
            ));
    });
}


    /*
    |--------------------------------------------------------------------------
    | AO - MENOLAK PENGAJUAN
    |--------------------------------------------------------------------------
    */

   
public function tolakOlehAO(
    User $ao,
    string $alasan
): void {
    if ($ao->role !== 'ao') {
        throw new \Exception(
            'Hanya AO yang dapat memproses analisis pinjaman.'
        );
    }

    $alasan = trim($alasan);

    if ($alasan === '') {
        throw new \Exception(
            'Alasan penolakan wajib diisi.'
        );
    }

    \DB::transaction(function () use ($ao, $alasan) {
        // Kunci baris untuk mencegah keputusan bersamaan.
        $pinjaman = static::query()
            ->whereKey($this->getKey())
            ->lockForUpdate()
            ->firstOrFail();

        // Status harus diperiksa kembali di dalam transaksi.
        if (!$pinjaman->isMenungguAO()) {
            throw new \Exception(
                'Pinjaman tidak sedang menunggu analisis AO.'
            );
        }

        $pinjaman->update([
            'status' => 'ditolak',
        ]);

        ApprovalPinjaman::updateOrCreate(
            [
                'pinjaman_id' => $pinjaman->id,
                'level' => 'ao',
            ],
            [
                'user_id' => $ao->id,
                'urutan' => 1,
                'status' => 'ditolak',
                'tanggal_approval' => now(),
                'catatan' => $alasan,
            ]
        );

        ActivityLog::catat(
            'update',
            'AO menolak pengajuan pinjaman '
                . $pinjaman->no_pinjaman
                . '. Alasan: ' . $alasan,
            static::class,
            $pinjaman->id,
            null,
            $pinjaman->fresh()->toArray()
        );

        Notifikasi::kirim(
            $pinjaman->anggota->user_id,
            'Pengajuan Pinjaman Ditolak',
            'Pengajuan pinjaman '
                . $pinjaman->no_pinjaman
                . ' ditolak pada tahap analisis AO. '
                . 'Alasan: ' . $alasan,
            'pinjaman'
        );
    });
}


    /*
    |--------------------------------------------------------------------------
    | KABAG - PERSETUJUAN FINAL
    |--------------------------------------------------------------------------
    */

    public function setujuiOlehKabag(
        User $kabag,
        ?string $catatan = null
    ): void {
        if ($kabag->role !== 'kabag') {
            throw new \Exception(
                'Hanya Kabag yang dapat memberikan persetujuan akhir.'
            );
        }

        if (!$this->isMenungguKabag()) {
            throw new \Exception(
                'Pinjaman tidak sedang menunggu persetujuan Kabag.'
            );
        }

        if (
            $this->jumlah_rekomendasi === null ||
            (float) $this->jumlah_rekomendasi <= 0
        ) {
            throw new \Exception(
                'Jumlah rekomendasi AO belum tersedia.'
            );
        }

        \DB::transaction(function () use ($kabag, $catatan) {
            $catatan = trim((string) $catatan);

            // Nominal persetujuan mengikuti rekomendasi AO.
            $jumlahDisetujui = (float) $this->jumlah_rekomendasi;

            // Hitung ulang kewajiban berdasarkan nominal persetujuan.
            $hitung = static::hitungAngsuran(
                $jumlahDisetujui,
                $this->tenor,
                (float) $this->bunga_persen
            );

            $this->update([
                'status'             => 'disetujui',
                'jumlah_disetujui'   => $jumlahDisetujui,
                'angsuran_per_bulan' => $hitung['angsuran_per_bulan'],
                'total_bunga'        => $hitung['total_bunga'],
                'total_angsuran'     => $hitung['total_angsuran'],
                'sisa_pinjaman'      => $hitung['total_angsuran'],
            ]);

            // Simpan keputusan dan catatan Kabag.
            ApprovalPinjaman::updateOrCreate(
                [
                    'pinjaman_id' => $this->id,
                    'level'       => 'kabag',
                ],
                [
                    'user_id'          => $kabag->id,
                    'urutan'           => 1,
                    'status'           => 'disetujui',
                    'tanggal_approval' => now(),
                    'catatan'          => $catatan !== ''
                        ? $catatan
                        : 'Disetujui berdasarkan rekomendasi AO.',
                ]
            );

            // Notifikasi anggota.
            Notifikasi::kirim(
                $this->anggota->user_id,
                'Pinjaman Disetujui',
                'Pengajuan pinjaman '
                    . $this->no_pinjaman
                    . ' sebesar Rp '
                    . number_format($jumlahDisetujui, 0, ',', '.')
                    . ' telah disetujui oleh Kabag dan menunggu pencairan.',
                'pinjaman'
            );

            // Notifikasi Bendahara.
            User::where('role', 'bendahara')
                ->where('status', 'aktif')
                ->get()
                ->each(fn ($user) => Notifikasi::kirim(
                    $user->id,
                    'Pinjaman Siap Dicairkan',
                    'Pinjaman '
                        . $this->no_pinjaman
                        . ' sebesar Rp '
                        . number_format($jumlahDisetujui, 0, ',', '.')
                        . ' telah disetujui Kabag dan siap diproses untuk pencairan.',
                    'pinjaman'
                ));

            // Catat aktivitas persetujuan.
            ActivityLog::catat(
                'update',
                'Kabag menyetujui pinjaman '
                    . $this->no_pinjaman
                    . ' sebesar Rp '
                    . number_format($jumlahDisetujui, 0, ',', '.')
                    . (
                        $catatan !== ''
                            ? '. Catatan: ' . $catatan
                            : ''
                    ),
                static::class,
                $this->id,
                null,
                $this->fresh()->toArray()
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | KABAG - MENOLAK PENGAJUAN
    |--------------------------------------------------------------------------
    */

    public function tolakOlehKabag(
        User $kabag,
        string $alasan
    ): void {
        if ($kabag->role !== 'kabag') {
            throw new \Exception(
                'Hanya Kabag yang dapat memberikan keputusan akhir.'
            );
        }

        if (!$this->isMenungguKabag()) {
            throw new \Exception(
                'Pinjaman tidak sedang menunggu persetujuan Kabag.'
            );
        }

        $alasan = trim($alasan);

        if ($alasan === '') {
            throw new \Exception(
                'Alasan penolakan wajib diisi.'
            );
        }

        \DB::transaction(function () use ($kabag, $alasan) {
            $this->update([
                'status' => 'ditolak',
            ]);

            ApprovalPinjaman::updateOrCreate(
                [
                    'pinjaman_id' => $this->id,
                    'level'       => 'kabag',
                ],
                [
                    'user_id'          => $kabag->id,
                    'urutan'           => 1,
                    'status'           => 'ditolak',
                    'tanggal_approval' => now(),
                    'catatan'          => $alasan,
                ]
            );

            Notifikasi::kirim(
                $this->anggota->user_id,
                'Pinjaman Ditolak',
                'Pengajuan pinjaman '
                    . $this->no_pinjaman
                    . ' ditolak oleh Kabag. '
                    . 'Alasan: ' . $alasan,
                'pinjaman'
            );

            ActivityLog::catat(
                'update',
                'Kabag menolak pinjaman '
                    . $this->no_pinjaman
                    . '. Alasan: ' . $alasan,
                static::class,
                $this->id,
                null,
                $this->fresh()->toArray()
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | BENDAHARA - PENCAIRAN
    |--------------------------------------------------------------------------
    */

    public function cairkanOlehBendahara(User $bendahara): void
    {
        if ($bendahara->role !== 'bendahara') {
            throw new \Exception(
                'Hanya bendahara yang dapat mencairkan pinjaman.'
            );
        }

        if (!$this->isDisetujui()) {
            throw new \Exception(
                'Pinjaman belum disetujui Kabag.'
            );
        }

        if ($this->jumlah_disetujui === null) {
            throw new \Exception(
                'Pinjaman belum memiliki nominal yang disetujui Kabag.'
            );
        }

        if ((float) $this->jumlah_disetujui <= 0) {
            throw new \Exception(
                'Nominal pencairan tidak valid.'
            );
        }

        \DB::transaction(function () use ($bendahara) {
            $this->update([
                'status'              => 'dicairkan',
                'tanggal_cair'        => today(),
                'tanggal_jatuh_tempo' => today()->addMonths($this->tenor),
            ]);

            // Generate jadwal angsuran.
            $this->fresh()->generateJadwalAngsuran();

            // Catat kas keluar.
            Kas::catat([
                'tanggal'        => now(),
                'jenis'          => 'keluar',
                'kategori'       => 'pinjaman',
                'jumlah'         => $this->jumlah_disetujui,
                'keterangan'     => 'Pencairan pinjaman '
                    . $this->no_pinjaman,
                'referensi_tipe' => 'pinjaman',
                'referensi_id'   => $this->id,
                'dicatat_oleh'   => $bendahara->id,
            ]);

            // Notifikasi anggota.
            Notifikasi::kirim(
                $this->anggota->user_id,
                'Dana Pinjaman Dicairkan',
                'Dana pinjaman '
                    . $this->no_pinjaman
                    . ' sebesar Rp '
                    . number_format(
                        $this->jumlah_disetujui,
                        0,
                        ',',
                        '.'
                    )
                    . ' telah dicairkan.',
                'pinjaman'
            );

            // Catat aktivitas pencairan.
            ActivityLog::catat(
                'update',
                'Bendahara mencairkan pinjaman '
                    . $this->no_pinjaman
                    . ' sebesar Rp '
                    . number_format(
                        $this->jumlah_disetujui,
                        0,
                        ',',
                        '.'
                    ),
                static::class,
                $this->id,
                null,
                $this->fresh()->toArray()
            );
        });
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Angsuran;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AngsuranController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ANGGOTA
    |--------------------------------------------------------------------------
    */

    /**
     * Menampilkan seluruh angsuran milik anggota yang sedang login.
     */
    public function milikSaya()
    {
        $user = Auth::user();

        if ($user->role !== 'anggota') {
            abort(403);
        }

        $anggota = $user->anggota;

        if (!$anggota) {
            abort(403, 'Data anggota belum tersedia.');
        }

        $pinjaman = $anggota->pinjaman()
            ->with([
                'masterPinjaman',
                'angsuran' => function ($query) {
                    $query->orderBy('angsuran_ke');
                },
            ])
            ->latest()
            ->get();

        return view(
            'anggota.angsuran',
            compact('anggota', 'pinjaman')
        );
    }

    /**
     * Menampilkan detail angsuran berdasarkan pinjaman.
     */
    public function detailMilikSaya(Pinjaman $pinjaman)
    {
        $user = Auth::user();

        if ($user->role !== 'anggota') {
            abort(403);
        }

        $anggota = $user->anggota;

        if (!$anggota) {
            abort(403, 'Data anggota belum tersedia.');
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan pinjaman benar-benar milik anggota yang sedang login
        |--------------------------------------------------------------------------
        */

        if ((int) $pinjaman->anggota_id !== (int) $anggota->id) {
            abort(403);
        }

        $pinjaman->load([
            'masterPinjaman',
            'angsuran' => function ($query) {
                $query->orderBy('angsuran_ke');
            },
        ]);

        return view(
            'anggota.angsuran-detail',
            compact('pinjaman')
        );
    }

    public function detailTransaksiSaya(Angsuran $angsuran)
    {
        $user = Auth::user();

        if ($user->role !== 'anggota') {
            abort(403);
        }

        $anggota = $user->anggota;

        if (!$anggota) {
            abort(403, 'Data anggota belum tersedia.');
        }

        $angsuran->load([
            'pinjaman.anggota',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Pastikan transaksi milik anggota yang sedang login
        |--------------------------------------------------------------------------
        */

        if (
            !$angsuran->pinjaman ||
            (int) $angsuran->pinjaman->anggota_id !== (int) $anggota->id
        ) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Normalisasi data untuk detail-transaksi.blade.php
        |--------------------------------------------------------------------------
        |
        | View detail-transaksi digunakan bersama oleh Simpanan dan Angsuran.
        | Karena itu controller menyiapkan data umum yang sama.
        |
        */

       $bukti = null;

        $buktiPembayaran = $angsuran->bukti_pembayaran;

        if (is_array($buktiPembayaran)) {
            $bukti = !empty($buktiPembayaran)
                ? $buktiPembayaran[array_key_last($buktiPembayaran)]
                : null;
        } elseif (is_string($buktiPembayaran) && $buktiPembayaran !== '') {
            $bukti = $buktiPembayaran;
        }
        $pembayaran = (object) [
            'id' => $angsuran->id,

            'nomor' => 'Angsuran ke-' . $angsuran->angsuran_ke,

            'jumlah' => $angsuran->jumlah_bayar > 0
                ? $angsuran->jumlah_bayar
                : $angsuran->total_tagihan,

            'tanggal' => $angsuran->tanggal_bayar,

            'metode_pembayaran' => $bukti
                ? 'Transfer'
                : 'Belum dibayar',

            'bukti' => $bukti,

            'status' => $angsuran->status_pembayaran,

            'keterangan' =>
                'Pembayaran angsuran ke-'
                . $angsuran->angsuran_ke
                . ' untuk pinjaman '
                . ($angsuran->pinjaman->no_pinjaman ?? '-'),

            'created_at' => $angsuran->created_at,

            'updated_at' => $angsuran->updated_at,

            /*
            |--------------------------------------------------------------------------
            | Data khusus Angsuran
            |--------------------------------------------------------------------------
            */

            'jenis_simpanan' => null,

            'no_pinjaman' =>
                $angsuran->pinjaman->no_pinjaman ?? null,

            'angsuran_ke' =>
                $angsuran->angsuran_ke,

            'tanggal_jatuh_tempo' =>
                $angsuran->tanggal_jatuh_tempo,

            'jumlah_pokok' =>
                $angsuran->jumlah_pokok,

            'jumlah_bunga' =>
                $angsuran->jumlah_bunga,

            'total_angsuran' =>
                $angsuran->total_angsuran,

            'denda' =>
                $angsuran->denda,

            'sisa_pinjaman' =>
                $angsuran->pinjaman->sisa_pinjaman ?? null,

            'back_route' =>
                'angsuran.saya.index',
        ];

        return view(
            'detail-transaksi',
            compact('pembayaran')
        );
    }

   
    public function bayar(Request $request, Pinjaman $pinjaman)
    {
        $user = Auth::user();

        if ($user->role !== 'anggota') {
            abort(403);
        }

        $anggota = $user->anggota;

        if (!$anggota) {
            abort(403, 'Data anggota belum tersedia.');
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan pinjaman milik anggota
        |--------------------------------------------------------------------------
        */

        if ((int) $pinjaman->anggota_id !== (int) $anggota->id) {
            abort(403);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'angsuran_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'angsuran_ids.*' => [
                'required',
                'integer',
                'distinct',
            ],

            'bukti_pembayaran' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Upload bukti
        |--------------------------------------------------------------------------
        */

        $buktiPath = $request
            ->file('bukti_pembayaran')
            ->store(
                'bukti-pembayaran/angsuran',
                'public'
            );

        try {
            $pinjaman->bayarAngsuran(
                array_map(
                    'intval',
                    $validated['angsuran_ids']
                ),
                $buktiPath
            );

        } catch (\Throwable $e) {

            Storage::disk('public')->delete($buktiPath);

            return back()
                ->withInput()
                ->withErrors([
                    'pembayaran' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route(
                'angsuran.saya.show',
                $pinjaman
            )
            ->with(
                'success',
                'Pembayaran angsuran berhasil dikirim dan sedang menunggu verifikasi CS/Teller.'
            );
    }

    public function uploadUlang(
        Request $request,
        Pinjaman $pinjaman
    ) {
        $user = Auth::user();

        if ($user->role !== 'anggota') {
            abort(403);
        }

        $anggota = $user->anggota;

        if (!$anggota) {
            abort(403, 'Data anggota belum tersedia.');
        }

        if ((int) $pinjaman->anggota_id !== (int) $anggota->id) {
            abort(403);
        }

        $validated = $request->validate([
            'angsuran_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'angsuran_ids.*' => [
                'required',
                'integer',
                'distinct',
            ],

            'bukti_pembayaran' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
        ]);

        $buktiPath = $request
            ->file('bukti_pembayaran')
            ->store(
                'bukti-pembayaran/angsuran',
                'public'
            );

        try {
            $pinjaman->uploadUlangBukti(
                array_map(
                    'intval',
                    $validated['angsuran_ids']
                ),
                $buktiPath
            );

        } catch (\Throwable $e) {

            Storage::disk('public')->delete($buktiPath);

            return back()
                ->withInput()
                ->withErrors([
                    'pembayaran' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route(
                'angsuran.saya.show',
                $pinjaman
            )
            ->with(
                'success',
                'Bukti pembayaran berhasil dikirim ulang dan sedang menunggu verifikasi CS/Teller.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | CS / TELLER
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'cs') {
            abort(403);
        }

        $angsuran = Angsuran::query()
            ->with([
                'pinjaman.anggota.user',
                'pinjaman',
            ])
            ->menungguVerifikasi()
            ->orderByDesc('tanggal_bayar')
            ->orderBy('angsuran_ke')
            ->get();

        return view(
            'angsuran.index',
            compact('angsuran')
        );
    }

    public function show(Angsuran $angsuran)
    {
        $user = Auth::user();

        if ($user->role !== 'cs') {
            abort(403);
        }

        $angsuran->load([
            'pinjaman.anggota.user',
            'pinjaman.masterPinjaman',
        ]);

        $buktiPembayaran = $angsuran->bukti_pembayaran;

        if (is_array($buktiPembayaran)) {
            $bukti = !empty($buktiPembayaran)
                ? $buktiPembayaran[array_key_last($buktiPembayaran)]
                : null;
        } elseif (is_string($buktiPembayaran) && $buktiPembayaran !== '') {
            $bukti = $buktiPembayaran;
        } else {
            $bukti = null;
        }

        return view(
            'angsuran.show',
            compact('angsuran')
        );
    }

    /**
     * Menyetujui pembayaran angsuran.
     */
    public function verifikasi(Request $request, Angsuran $angsuran)
    {
        $user = Auth::user();

        if ($user->role !== 'cs') {
            abort(403);
        }

        try {
            $angsuran->load([
                'pinjaman.anggota.user',
            ]);

            $angsuran->verifikasi($user);

        } catch (\Throwable $e) {

            return back()
                ->withErrors([
                    'verifikasi' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route('angsuran.index')
            ->with(
                'success',
                'Pembayaran angsuran berhasil diverifikasi.'
            );
    }

    /**
     * Menolak pembayaran angsuran.
     */
    public function tolak(Request $request, Angsuran $angsuran)
    {
        $user = Auth::user();

        if ($user->role !== 'cs') {
            abort(403);
        }

        $validated = $request->validate([
            'alasan' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        try {
            $angsuran->load([
                'pinjaman.anggota.user',
            ]);

            $angsuran->tolak(
                $user,
                $validated['alasan']
            );

        } catch (\Throwable $e) {

            return back()
                ->withErrors([
                    'penolakan' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route('angsuran.index')
            ->with(
                'success',
                'Pembayaran angsuran dikembalikan untuk diperbaiki.'
            );
    }
}
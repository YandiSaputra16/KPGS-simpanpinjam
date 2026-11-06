<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Simpanan;
use App\Models\PencairanSimpanan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SimpananController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CS / TELLER - DAFTAR SIMPANAN
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $query = Simpanan::query()
            ->with([
                'anggota.user',
                'jenisSimpanan',
                'pencatat',
            ]);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where(
                    'no_simpanan',
                    'like',
                    "%{$search}%"
                )
                    ->orWhereHas(
                        'anggota',
                        function ($anggotaQuery) use ($search) {
                            $anggotaQuery
                                ->where(
                                    'kode_anggota',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'user',
                                    function ($userQuery) use ($search) {
                                        $userQuery->where(
                                            'nama',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                );
                        }
                    );
            });
        }

        if ($request->filled('jenis_simpanan_id')) {
            $query->where(
                'jenis_simpanan_id',
                $request->jenis_simpanan_id
            );
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(
                'tanggal_transaksi',
                '>=',
                $request->tanggal_mulai
            );
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate(
                'tanggal_transaksi',
                '<=',
                $request->tanggal_akhir
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        $simpanan = $query
            ->latest('tanggal_transaksi')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $jenisSimpanan = JenisSimpanan::query()
            ->where('is_active', true)
            ->orderBy('nama_jenis')
            ->get();

        $jumlahPending = Simpanan::query()
            ->pending()
            ->count();

        // Riwayat pengajuan pencairan untuk CS/Teller.
        // Gunakan nama parameter halaman terpisah agar pagination simpanan tidak bertabrakan.
        $daftarPencairan = PencairanSimpanan::query()
            ->with('anggota.user')
            ->latest()
            ->paginate(10, ['*'], 'halaman_pencairan');

        return view('simpanan.index', compact(
            'simpanan',
            'jenisSimpanan',
            'jumlahPending',
            'daftarPencairan'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CS / TELLER - FORM INPUT PEMBAYARAN LANGSUNG
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        $anggota = Anggota::query()
            ->with('user')
            ->where('status_keanggotaan', 'aktif')
            ->orderBy('kode_anggota')
            ->get();

        $jenisSimpanan = JenisSimpanan::query()
            ->where('is_active', true)
            ->orderBy('nama_jenis')
            ->get();

        return view('simpanan.create', compact(
            'anggota',
            'jenisSimpanan'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CS / TELLER - SIMPAN PEMBAYARAN LANGSUNG
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): RedirectResponse
    {
        $csTeller = $request->user();

        abort_unless(
            $csTeller instanceof User && $csTeller->isCS(),
            403
        );

        $validated = $request->validate([
            'anggota_id' => [
                'required',
                'integer',
                'exists:anggota,id',
            ],
            'jenis_simpanan_id' => [
                'required',
                'integer',
                'exists:jenis_simpanan,id',
            ],
            'jumlah' => [
                'required',
                'numeric',
                'min:1',
            ],
            'tanggal_transaksi' => [
                'required',
                'date',
            ],
            'metode_pembayaran' => [
                'required',
                'in:tunai,transfer',
            ],
            'bukti' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $pathBukti = null;

        if ($request->hasFile('bukti')) {
            $pathBukti = $request->file('bukti')->store(
                'simpanan/bukti',
                'public'
            );
        }

        try {
            $simpanan = Simpanan::catatTransaksi([
                'anggota_id' => $validated['anggota_id'],
                'jenis_simpanan_id' => $validated['jenis_simpanan_id'],
                'jumlah' => $validated['jumlah'],
                'tanggal_transaksi' => $validated['tanggal_transaksi'],
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'keterangan' => $validated['keterangan'] ?? null,
                'bukti' => $pathBukti,
                'dicatat_oleh' => $csTeller->id,
            ]);
        } catch (\Throwable $e) {
            if ($pathBukti) {
                Storage::disk('public')->delete($pathBukti);
            }

            throw $e;
        }

        return redirect()
            ->route('simpanan.show', $simpanan)
            ->with(
                'success',
                'Pembayaran simpanan berhasil dicatat. Saldo anggota dan kas masuk telah diperbarui.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | CS / TELLER - DETAIL OPERASIONAL SIMPANAN
    |--------------------------------------------------------------------------
    |
    */

    public function show(Simpanan $simpanan): View
    {
        $simpanan->load([
            'anggota.user',
            'jenisSimpanan',
            'pencatat',
        ]);

        return view('simpanan.show', compact(
            'simpanan'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | CS / TELLER - VERIFIKASI PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    public function verifikasi(
        Request $request,
        Simpanan $simpanan
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:disetujui,ditolak',
            ],

            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        $disetujui = $validated['status'] === 'disetujui';

        $csTeller = $request->user();

        abort_unless(
            $csTeller instanceof User
            && $csTeller->isCS(),
            403
        );

        Simpanan::verifikasiPembayaran(
            $simpanan,
            $disetujui,
            $csTeller,
            $validated['catatan'] ?? null
        );

        return redirect()
            ->route('simpanan.show', $simpanan)
            ->with(
                'success',
                $disetujui
                    ? 'Pembayaran simpanan berhasil diverifikasi dan disetujui.'
                    : 'Pembayaran simpanan ditandai perlu diperbaiki.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ANGGOTA - DAFTAR SIMPANAN SAYA
    |--------------------------------------------------------------------------
    */

    public function milikSaya(Request $request): View
    {
        $user = $request->user();

        $anggota = $user->anggota;

        abort_unless($anggota, 404);

        $simpanan = Simpanan::query()
            ->with([
                'jenisSimpanan',
                'pencatat',
            ])
            ->where(
                'anggota_id',
                $anggota->id
            )
            ->latest('tanggal_transaksi')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        // Anggota hanya menerima pengajuan pencairan miliknya sendiri.
        $daftarPencairan = PencairanSimpanan::query()
            ->where('anggota_id', $anggota->id)
            ->latest()
            ->paginate(10, ['*'], 'halaman_pencairan');

        return view('anggota.simpanan', compact(
            'anggota',
            'simpanan',
            'daftarPencairan'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | ANGGOTA - DETAIL TRANSAKSI
    |--------------------------------------------------------------------------
    |
    */

    public function detailMilikSaya(
        Request $request,
        Simpanan $simpanan
    ): View {
        $anggota = $request->user()->anggota;

        abort_unless($anggota, 404);

        abort_unless(
            $simpanan->anggota_id === $anggota->id,
            404
        );

        $simpanan->load([
            'jenisSimpanan',
        ]);

        $pembayaran = [
            'tipe' => 'simpanan',

            'nomor' => $simpanan->no_simpanan,

            'judul' => 'Pembayaran Simpanan',

            'jumlah' => $simpanan->jumlah,

            'tanggal' => $simpanan->tanggal_transaksi,

            'metode_pembayaran' =>
                $simpanan->metode_pembayaran,

            'status' =>
                $simpanan->status,

            'keterangan' =>
                $simpanan->keterangan,

            'bukti' =>
                $simpanan->bukti,

            'created_at' =>
                $simpanan->created_at,

            'updated_at' =>
                $simpanan->updated_at,

            /*
             * Data khusus Simpanan.
             */
            'jenis_simpanan' =>
                $simpanan->jenisSimpanan?->nama_jenis,

            'nomor_pinjaman' => null,

            'angsuran_ke' => null,

            'tanggal_jatuh_tempo' => null,

            'jumlah_pokok' => null,

            'jumlah_bunga' => null,

            'total_angsuran' => null,

            'sisa_pinjaman' => null,

            'back_route' =>
                'simpanan.saya.index',
        ];

        return view(
            'detail-transaksi',
            compact('pembayaran')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ANGGOTA - FORM PENGAJUAN PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    public function buatPengajuan(): View
    {
        $user = Auth::user();

        $anggota = $user->anggota;

        abort_unless($anggota, 404);

        $jenisSimpanan = JenisSimpanan::query()
            ->where('is_active', true)
            ->orderBy('nama_jenis')
            ->get();

        return view('anggota.pembayaran', compact(
            'anggota',
            'jenisSimpanan'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | ANGGOTA - KIRIM PENGAJUAN PEMBAYARAN
    |--------------------------------------------------------------------------
    */

    public function ajukanPembayaran(
        Request $request
    ): RedirectResponse {
        $anggota = $request->user()->anggota;

        abort_unless($anggota, 404);

        $validated = $request->validate([
            'jenis_simpanan_id' => [
                'required',
                'integer',
                'exists:jenis_simpanan,id',
            ],

            'jumlah' => [
                'required',
                'numeric',
                'min:1',
            ],

            'tanggal_transaksi' => [
                'required',
                'date',
            ],

            'metode_pembayaran' => [
                'required',
                'in:tunai,transfer',
            ],

            'bukti' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        /*
         * Bukti pembayaran wajib untuk transfer.
         */
        if (
            $validated['metode_pembayaran'] === 'transfer'
            && !$request->hasFile('bukti')
        ) {
            return back()
                ->withErrors([
                    'bukti' =>
                        'Bukti pembayaran wajib diunggah untuk pembayaran transfer.',
                ])
                ->withInput();
        }

        /*
         * Simpan bukti pembayaran jika ada.
         */
        $pathBukti = null;

        if ($request->hasFile('bukti')) {
            $pathBukti = $request
                ->file('bukti')
                ->store(
                    'simpanan/bukti',
                    'public'
                );
        }

        try {
            $simpanan = Simpanan::ajukanPembayaran([
                'anggota_id' =>
                    $anggota->id,

                'jenis_simpanan_id' =>
                    $validated['jenis_simpanan_id'],

                'jumlah' =>
                    $validated['jumlah'],

                'tanggal_transaksi' =>
                    $validated['tanggal_transaksi'],

                'metode_pembayaran' =>
                    $validated['metode_pembayaran'],

                'keterangan' =>
                    $validated['keterangan'] ?? null,

                'bukti' =>
                    $pathBukti,

                'dicatat_oleh' => null,
            ]);
        } catch (\Throwable $e) {

            if ($pathBukti) {
                Storage::disk('public')
                    ->delete($pathBukti);
            }

            throw $e;
        }

        return redirect()
            ->route('simpanan.saya.index')
            ->with(
                'success',
                "Pengajuan pembayaran {$simpanan->no_simpanan} berhasil dikirim dan sedang menunggu verifikasi."
            );
    }
}
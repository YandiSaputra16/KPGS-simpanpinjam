<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;

class DataAnggotaController extends Controller
{
    /**
     * Daftar anggota untuk kebutuhan pemantauan dan analisis AO.
     * Seluruh halaman pada controller ini bersifat read-only.
     */
    public function index(Request $request)
    {
        abort_unless(auth()->user()?->role === 'ao', 403);

        $search = trim((string) $request->query('search', ''));
        $status = $request->query('status', '');

        $query = Anggota::query()
            ->with('user')
            ->withCount([
                'pinjaman',
                'simpanan as jumlah_transaksi_simpanan' => function ($q) {
                    $q->where('status', 'disetujui');
                },
            ])
            ->withSum([
                'simpanan as total_simpanan_disetujui' => function ($q) {
                    $q->where('status', 'disetujui');
                },
            ], 'jumlah');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('kode_anggota', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('nama', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if (in_array($status, ['aktif', 'nonaktif'], true)) {
            $query->where('status_keanggotaan', $status);
        }

        $anggota = $query
            ->withCount([
                'pinjaman as pinjaman_aktif_count' => function ($q) {
                    $q->whereIn('status', ['disetujui', 'dicairkan']);
                },
            ])
            ->orderBy('kode_anggota')
            ->paginate(15)
            ->withQueryString();

        $ringkasan = [
            'total' => Anggota::count(),
            'aktif' => Anggota::where('status_keanggotaan', 'aktif')->count(),
            'nonaktif' => Anggota::where('status_keanggotaan', 'nonaktif')->count(),
        ];

        return view('pinjaman.data-anggota-index', compact(
            'anggota',
            'search',
            'status',
            'ringkasan'
        ));
    }

    /**
     * Profil ringkas anggota beserta riwayat simpanan, pinjaman,
     * jadwal/pembayaran angsuran, dan data usaha yang pernah diinput.
     */
    public function show(Anggota $anggota)
    {
        abort_unless(auth()->user()?->role === 'ao', 403);

        $anggota->load([
            'user',
            'simpanan' => function ($q) {
                $q->with('jenisSimpanan')
                    ->orderByDesc('tanggal_transaksi')
                    ->orderByDesc('id');
            },
            'pinjaman' => function ($q) {
                $q->with([
                    'masterPinjaman',
                    'dataPendukung',
                    'angsuran' => function ($angsuran) {
                        $angsuran->orderBy('angsuran_ke');
                    },
                ])->orderByDesc('tanggal_pengajuan')->orderByDesc('id');
            },
        ]);

        $simpananDisetujui = $anggota->simpanan
            ->where('status', 'disetujui');

        $ringkasan = [
            'total_simpanan' => (float) $anggota->saldo_simpanan_pokok
                + (float) $anggota->saldo_simpanan_wajib
                + (float) $anggota->saldo_simpanan_sukarela,
            'transaksi_simpanan' => $simpananDisetujui->count(),
            'total_pengajuan_pinjaman' => $anggota->pinjaman->count(),
            'pinjaman_aktif' => $anggota->pinjaman
                ->whereIn('status', ['disetujui', 'dicairkan'])->count(),
            'total_jadwal_angsuran' => $anggota->pinjaman
                ->sum(fn ($pinjaman) => $pinjaman->angsuran->count()),
            'angsuran_lunas' => $anggota->pinjaman
                ->sum(fn ($pinjaman) => $pinjaman->angsuran
                    ->where('status_pembayaran', 'lunas')->count()),
            'angsuran_terlambat' => $anggota->pinjaman
                ->sum(fn ($pinjaman) => $pinjaman->angsuran
                    ->filter(fn ($angsuran) =>
                        $angsuran->status_pembayaran === 'belum_bayar'
                        && $angsuran->tanggal_jatuh_tempo
                        && $angsuran->tanggal_jatuh_tempo->lt(today())
                    )->count()),
        ];

        return view('pinjaman.data-anggota-show', compact(
            'anggota',
            'ringkasan'
        ));
    }
}

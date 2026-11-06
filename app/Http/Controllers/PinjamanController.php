<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\MasterPinjaman;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PinjamanController extends Controller
{
    /**
     * ============================================================
     * ANGGOTA - FORM PENGAJUAN PINJAMAN
     * ============================================================
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->role !== 'anggota') {
            abort(403, 'Hanya anggota yang dapat mengajukan pinjaman.');
        }

        $anggota = $user->anggota;

        if (!$anggota) {
            abort(403, 'Data anggota belum tersedia.');
        }

        $paketPinjaman = MasterPinjaman::where('is_active', true)
            ->orderBy('plafon_min')
            ->get();

        return view(
            'anggota.pinjaman.create',
            compact('anggota', 'paketPinjaman')
        );
    }

    /**
     * ============================================================
     * ANGGOTA - MENYIMPAN PENGAJUAN PINJAMAN
     * ============================================================
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'anggota') {
            abort(403, 'Hanya anggota yang dapat mengajukan pinjaman.');
        }

        $anggota = $user->anggota;

        if (!$anggota) {
            abort(403, 'Data anggota belum tersedia.');
        }

        $validated = $request->validate([
            'master_pinjaman_id' => [
                'required',
                'integer',
                'exists:master_pinjaman,id',
            ],

            'jumlah_pinjaman' => [
                'required',
                'numeric',
                'min:1',
            ],

            'tenor' => [
                'required',
                'integer',
                'min:1',
            ],

            'tujuan_pinjaman' => [
                'required',
                'string',
                'max:1000',
            ],

            'setuju_syarat' => [
                'required',
                'accepted',
            ],

            'produksi_susu_bulanan' => [
                'required',
                'numeric',
                'min:0',
            ],

            'jumlah_sapi' => [
                'required',
                'integer',
                'min:0',
            ],

            'kondisi_usaha' => [
                'required',
                Rule::in([
                    'baik',
                    'cukup',
                    'kurang',
                ]),
            ],

            'pendapatan_bulanan' => [
                'required',
                'numeric',
                'min:0',
            ],

            'kewajiban_angsuran_lain' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $paket = MasterPinjaman::findOrFail(
            $validated['master_pinjaman_id']
        );

        try {
            $pinjaman = Pinjaman::ajukanPinjaman(
                anggota: $anggota,
                paket: $paket,
                jumlah: (float) $validated['jumlah_pinjaman'],
                tenor: (int) $validated['tenor'],
                tujuan: $validated['tujuan_pinjaman'],
                setujuSyarat: true,
                dataPendukung: [
                    'produksi_susu_bulanan' =>
                        $validated['produksi_susu_bulanan'],

                    'jumlah_sapi' =>
                        $validated['jumlah_sapi'],

                    'kondisi_usaha' =>
                        $validated['kondisi_usaha'],

                    'pendapatan_bulanan' =>
                        $validated['pendapatan_bulanan'],

                    'kewajiban_angsuran_lain' =>
                        $validated['kewajiban_angsuran_lain'],
                ]
            );
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'pinjaman' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route('pinjaman.anggota.index')
            ->with(
                'success',
                'Pengajuan pinjaman berhasil dikirim dan sedang diproses.'
            );
    }

    /**
     * ============================================================
     * ANGGOTA - DAFTAR PINJAMAN MILIK SENDIRI
     * ============================================================
     */
    public function anggotaIndex()
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
                'dataPendukung',
                'angsuran',
            ])
            ->latest()
            ->get();

        return view(
            'anggota.pinjaman.index',
            compact('anggota', 'pinjaman')
        );
    }

    /**
     * ============================================================
     * CS - DAFTAR PINJAMAN
     * ============================================================
     */
    public function csIndex()
    {
        $user = Auth::user();

        if ($user->role !== 'cs') {
            abort(403);
        }

        $pinjaman = Pinjaman::with([
            'anggota.user',
            'masterPinjaman',
            'dataPendukung',
        ])
            ->latest('tanggal_pengajuan')
            ->get();

        return view(
            'pinjaman.index',
            compact('pinjaman')
        );
    }

    /**
     * ============================================================
     * CS - DETAIL PINJAMAN
     * ============================================================
     */
    public function csShow(Pinjaman $pinjaman)
    {
        $user = Auth::user();

        if ($user->role !== 'cs') {
            abort(403);
        }

        $pinjaman->load([
            'anggota.user',
            'masterPinjaman',
            'dataPendukung',
            'approvals.user',
            'angsuran',
        ]);

        return view(
            'pinjaman.show',
            compact('pinjaman')
        );
    }

    /**
     * ============================================================
     * CS - MENERUSKAN PENGAJUAN KE AO
     * ============================================================
     */
    public function csProses(Pinjaman $pinjaman)
    {
        $user = Auth::user();

        if ($user->role !== 'cs') {
            abort(403);
        }

        try {
            $pinjaman->prosesOlehCS($user);
        } catch (\Throwable $e) {
            return back()
                ->withErrors([
                    'pinjaman' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route('pinjaman.cs.show', $pinjaman)
            ->with(
                'success',
                'Pengajuan pinjaman berhasil diteruskan ke AO untuk dianalisis.'
            );
    }

    /**
     * ============================================================
     * CS - MENOLAK PENGAJUAN
     * ============================================================
     */
    public function csTolak(Request $request, Pinjaman $pinjaman)
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
            $pinjaman->tolakOlehCS(
                $user,
                $validated['alasan']
            );
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'pinjaman' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route('pinjaman.cs.index')
            ->with(
                'success',
                'Pengajuan pinjaman berhasil ditolak.'
            );
    }

    /**
 * =========================================================
 * AO - DAFTAR ANALISIS DAN RIWAYAT ANALISIS PINJAMAN
 * =========================================================
 */
public function aoIndex()
{
    $user = Auth::user();

    abort_unless($user && $user->role === 'ao', 403);

    $filter = request()->query('filter', 'aktif');

    if (! in_array($filter, ['aktif', 'riwayat'], true)) {
        $filter = 'aktif';
    }

    $query = Pinjaman::with([
        'anggota.user',
        'masterPinjaman',
        'dataPendukung',
        'approvals.user',
    ]);

    if ($filter === 'riwayat') {
        $query->whereHas('approvals', function ($approval) use ($user) {
            $approval->where('level', 'ao')
                ->where('user_id', $user->id);
        });
    } else {
        $query->where('status', 'menunggu_ao');
    }

    $pinjaman = $query
        ->latest('tanggal_pengajuan')
        ->get();

    return view('pinjaman.index', compact('pinjaman', 'filter'));
}

    /**
     * ============================================================
     * AO - DETAIL PINJAMAN
     * ============================================================
     */
    public function aoShow(Pinjaman $pinjaman)
    {
        $user = Auth::user();

        if ($user->role !== 'ao') {
            abort(403);
        }

        $pinjaman->load([
            'anggota.user',
            'anggota.simpanan.jenisSimpanan',
            'anggota.pinjaman.angsuran',
            'masterPinjaman',
            'dataPendukung',
            'approvals.user',
            'angsuran',
        ]);

        return view(
            'pinjaman.show',
            compact('pinjaman')
        );
    }

    /**
     * ============================================================
     * AO - MEMBERIKAN REKOMENDASI
     * ============================================================
     */
    public function aoProses(
        Request $request,
        Pinjaman $pinjaman
    ) {
        $user = Auth::user();

        if ($user->role !== 'ao') {
            abort(403);
        }

        $validated = $request->validate([
            'jumlah_rekomendasi' => [
                'required',
                'numeric',
                'min:1',
            ],

            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        try {
            $pinjaman->prosesOlehAO(
                $user,
                (float) $validated['jumlah_rekomendasi'],
                $validated['catatan'] ?? null
            );
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'pinjaman' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route('pinjaman.ao.show', $pinjaman)
            ->with(
                'success',
                'Analisis pinjaman berhasil disimpan dan diteruskan ke Kabag.'
            );
    }

    /**
     * ============================================================
     * AO - MENOLAK PENGAJUAN
     * ============================================================
     */
    public function aoTolak(
        Request $request,
        Pinjaman $pinjaman
    ) {
        $user = Auth::user();

        if ($user->role !== 'ao') {
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
            $pinjaman->tolakOlehAO(
                $user,
                $validated['alasan']
            );
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'pinjaman' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route('pinjaman.ao.index')
            ->with(
                'success',
                'Pengajuan pinjaman berhasil ditolak.'
            );
    }

   
/**
 * ============================================================
 * KABAG - PERSETUJUAN DAN RIWAYAT KEPUTUSAN
 * ============================================================
 */
public function kabagIndex(Request $request)
{
    $user = Auth::user();

    abort_unless($user && $user->role === 'kabag', 403);

    $filter = $request->query('filter', 'aktif');

    if (! in_array($filter, ['aktif', 'riwayat'], true)) {
        $filter = 'aktif';
    }

    $query = Pinjaman::with([
        'anggota.user',
        'masterPinjaman',
        'dataPendukung',
        'approvals.user',
    ]);

    if ($filter === 'riwayat') {
        // Hanya tampilkan keputusan Kabag yang sudah tercatat.
        $query->whereHas('approvals', function ($approval) use ($user) {
            $approval
                ->where('level', 'kabag')
                ->where('user_id', $user->id)
                ->whereIn('status', ['disetujui', 'ditolak']);
        });
    } else {
        // Antrean aktif hanya untuk pengajuan yang menunggu Kabag.
        $query->where('status', 'menunggu_kabag');
    }

    $pinjaman = $query
        ->orderByDesc('tanggal_pengajuan')
        ->orderByDesc('id')
        ->get();

    $jumlahMenunggu = Pinjaman::where(
        'status',
        'menunggu_kabag'
    )->count();

    $jumlahRiwayat = \App\Models\ApprovalPinjaman::query()
        ->where('level', 'kabag')
        ->where('user_id', $user->id)
        ->whereIn('status', ['disetujui', 'ditolak'])
        ->count();

    return view('pinjaman.index', compact(
        'pinjaman',
        'filter',
        'jumlahMenunggu',
        'jumlahRiwayat'
    ));
}

    /**
     * ============================================================
     * KABAG - DETAIL PINJAMAN
     * ============================================================
     */
    public function kabagShow(Pinjaman $pinjaman)
    {
        $user = Auth::user();

        if ($user->role !== 'kabag') {
            abort(403);
        }

        $pinjaman->load([
            'anggota.user',
            'masterPinjaman',
            'dataPendukung',
            'approvals.user',
            'angsuran',
        ]);

        return view(
            'pinjaman.show',
            compact('pinjaman')
        );
    }

    /**
     * ============================================================
     * KABAG - MENYETUJUI PINJAMAN
     * ============================================================
     */
    public function kabagSetujui(
        Request $request,
        Pinjaman $pinjaman
    ) {
        $user = Auth::user();

        if ($user->role !== 'kabag') {
            abort(403);
        }

        $validated = $request->validate([
            'catatan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        try {
            $pinjaman->setujuiOlehKabag(
                $user,
                $validated['catatan'] ?? null
            );
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'pinjaman' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route('pinjaman.kabag.show', $pinjaman)
            ->with(
                'success',
                'Pinjaman berhasil disetujui dan menunggu proses pencairan oleh bendahara.'
            );
    }

    /**
     * ============================================================
     * KABAG - MENOLAK PINJAMAN
     * ============================================================
     */
    public function kabagTolak(
        Request $request,
        Pinjaman $pinjaman
    ) {
        $user = Auth::user();

        if ($user->role !== 'kabag') {
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
            $pinjaman->tolakOlehKabag(
                $user,
                $validated['alasan']
            );
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'pinjaman' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route('pinjaman.kabag.index')
            ->with(
                'success',
                'Pengajuan pinjaman berhasil ditolak.'
            );
    }

    /**
     * ============================================================
     * BENDAHARA - DAFTAR PINJAMAN
     * ============================================================
     */
    public function bendaharaIndex()
    {
        $user = Auth::user();

        if ($user->role !== 'bendahara') {
            abort(403);
        }

        // Bendahara dapat memantau pengajuan dari awal hingga lunas.
        // Hak pencairan tetap ditentukan oleh status pinjaman.
        $pinjaman = Pinjaman::with([
            'anggota.user',
            'masterPinjaman',
            'dataPendukung',
            'approvals.user',
        ])
            ->latest('tanggal_pengajuan')
            ->get();

        return view(
            'pinjaman.index',
            compact('pinjaman')
        );
    }

    /**
     * ============================================================
     * BENDAHARA - DETAIL PINJAMAN
     * ============================================================
     */
    public function bendaharaShow(Pinjaman $pinjaman)
    {
        $user = Auth::user();

        if ($user->role !== 'bendahara') {
            abort(403);
        }

        $pinjaman->load([
            'anggota.user',
            'masterPinjaman',
            'dataPendukung',
            'approvals.user',
            'angsuran',
        ]);

        return view(
            'pinjaman.show',
            compact('pinjaman')
        );
    }

    /**
     * ============================================================
     * BENDAHARA - MENCAIRKAN PINJAMAN
     * ============================================================
     */
    public function bendaharaCairkan(Pinjaman $pinjaman)
    {
        $user = Auth::user();

        if ($user->role !== 'bendahara') {
            abort(403);
        }

        try {
            $pinjaman->cairkanOlehBendahara($user);
        } catch (\Throwable $e) {
            return back()
                ->withErrors([
                    'pinjaman' => $e->getMessage(),
                ]);
        }

        return redirect()
            ->route('pinjaman.bendahara.show', $pinjaman)
            ->with(
                'success',
                'Pinjaman berhasil dicairkan dan transaksi kas telah diproses.'
            );
    }
}

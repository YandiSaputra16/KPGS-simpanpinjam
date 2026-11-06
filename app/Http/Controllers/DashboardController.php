<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\Kas;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Status pinjaman yang masih dianggap aktif
     * untuk kebutuhan dashboard dan monitoring.
     */
    private const STATUS_PINJAMAN_AKTIF = [
        'menunggu_cs',
        'menunggu_ao',
        'menunggu_kabag',
        'disetujui',
        'dicairkan',
    ];

    /**
     * Dashboard utama berdasarkan role pengguna.
     */
    public function index(): View
    {
        $role = auth()->user()->role;

        $data = match ($role) {
            'admin' => $this->adminDashboard(),

            'cs' => $this->csDashboard(),

            'ao' => $this->aoDashboard(),

            'kabag' => $this->kabagDashboard(),

            'bendahara' => $this->bendaharaDashboard(),

            'pimpinan' => $this->pimpinanDashboard(),

            'anggota' => $this->anggotaDashboard(),

            default => [],
        };

        return view('dashboard.index', [
            'role' => $role,
            'data' => $data,
        ]);
    }

    /**
     * Dashboard Administrator.
     *
     * Admin berfokus pada pengelolaan aplikasi,
     * bukan operasional simpan pinjam.
     */
    private function adminDashboard(): array
    {
        return [
            'pengguna_internal' => User::query()
                ->whereIn('role', [
                    'cs',
                    'ao',
                    'kabag',
                    'bendahara',
                    'pimpinan',
                ])
                ->count(),

            'anggota' => Anggota::query()
                ->count(),

            'akun_aktif' => User::query()
                ->where('status', 'aktif')
                ->count(),

            'akun_nonaktif' => User::query()
                ->where('status', 'nonaktif')
                ->count(),

            'aktivitas_terbaru' => ActivityLog::query()
                ->with('user')
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }

    /**
     * Dashboard Customer Service.
     */
    private function csDashboard(): array
    {
        return [
            'totalAnggota' => Anggota::query()
                ->where('status_keanggotaan', 'aktif')
                ->count(),

            'menungguCS' => Pinjaman::query()
                ->where('status', 'menunggu_cs')
                ->count(),

            'pinjamanAktif' => Pinjaman::query()
                ->whereIn('status', self::STATUS_PINJAMAN_AKTIF)
                ->count(),

            'pengajuanCS' => Pinjaman::query()
                ->with(['anggota.user'])
                ->where('status', 'menunggu_cs')
                ->latest()
                ->get(),
        ];
    }

    /**
 * Dashboard Account Officer.
 */
private function aoDashboard(): array
{
    return [
        // Ringkasan jumlah anggota aktif.
        'totalAnggota' => Anggota::query()
            ->where('status_keanggotaan', 'aktif')
            ->count(),

        // Ringkasan pengajuan yang menunggu analisis AO.
        'menungguAO' => Pinjaman::query()
            ->where('status', 'menunggu_ao')
            ->count(),

        // Ringkasan pinjaman berdasarkan status aktif yang ditetapkan.
        'pinjamanAktif' => Pinjaman::query()
            ->whereIn('status', self::STATUS_PINJAMAN_AKTIF)
            ->count(),

        // Detail pengajuan yang menunggu analisis AO.
        'pengajuanAO' => Pinjaman::query()
            ->with(['anggota.user'])
            ->where('status', 'menunggu_ao')
            ->latest()
            ->get(),

        // Detail seluruh pinjaman dengan status aktif.
        'daftarPinjamanAktif' => Pinjaman::query()
            ->with(['anggota.user'])
            ->whereIn('status', self::STATUS_PINJAMAN_AKTIF)
            ->latest()
            ->get(),

        // Detail anggota dengan status keanggotaan aktif.
        'daftarAnggotaAktif' => Anggota::query()
            ->with('user')
            ->where('status_keanggotaan', 'aktif')
            ->latest()
            ->get(),
    ];
}


/**
 * Dashboard Kepala Bagian.
 */
private function kabagDashboard(): array
{
    return [
        'menungguKabag' => Pinjaman::query()
            ->where('status', 'menunggu_kabag')
            ->count(),

        'pinjamanDisetujui' => Pinjaman::query()
            ->where('status', 'disetujui')
            ->count(),

        'pinjamanDicairkan' => Pinjaman::query()
            ->where('status', 'dicairkan')
            ->count(),

        // Pengajuan yang membutuhkan keputusan Kabag.
        'pengajuanKabag' => Pinjaman::query()
            ->with(['anggota.user', 'masterPinjaman'])
            ->where('status', 'menunggu_kabag')
            ->latest()
            ->limit(10)
            ->get(),

        // Pinjaman yang sudah disetujui dan menunggu pencairan.
        'daftarPinjamanDisetujui' => Pinjaman::query()
            ->with(['anggota.user', 'masterPinjaman'])
            ->where('status', 'disetujui')
            ->latest()
            ->limit(5)
            ->get(),

        // Pinjaman yang sudah dicairkan.
        'daftarPinjamanDicairkan' => Pinjaman::query()
            ->with(['anggota.user', 'masterPinjaman'])
            ->where('status', 'dicairkan')
            ->latest()
            ->limit(5)
            ->get(),
    ];
}


    /**
     * Dashboard Bendahara.
     */
    private function bendaharaDashboard(): array
    {
        return [
            'totalSimpanan' => Simpanan::query()
                ->where('status', 'disetujui')
                ->sum('jumlah'),

            'saldoKas' => Kas::query()
                ->orderByDesc('id')
                ->value('saldo_sesudah') ?? 0,

            'menungguCS' => Pinjaman::query()->where('status', 'menunggu_cs')->count(),
            'menungguAO' => Pinjaman::query()->where('status', 'menunggu_ao')->count(),
            'menungguKabag' => Pinjaman::query()->where('status', 'menunggu_kabag')->count(),
            'pinjamanDisetujui' => Pinjaman::query()->where('status', 'disetujui')->count(),
            'pinjamanDicairkan' => Pinjaman::query()->where('status', 'dicairkan')->count(),
            'pinjamanLunas' => Pinjaman::query()->where('status', 'lunas')->count(),
            'pinjamanDitolak' => Pinjaman::query()->where('status', 'ditolak')->count(),

            'pinjamanTerbaru' => Pinjaman::query()
                ->with(['anggota.user'])
                ->latest('tanggal_pengajuan')
                ->limit(8)
                ->get(),

            'pinjamanSiapDicairkan' => Pinjaman::query()
                ->with(['anggota.user'])
                ->where('status', 'disetujui')
                ->latest('tanggal_pengajuan')
                ->get(),
        ];
    }

    /**
     * Dashboard Pimpinan.
     */
    private function pimpinanDashboard(): array
    {
        return [
            'totalAnggota' => Anggota::query()
                ->where('status_keanggotaan', 'aktif')
                ->count(),

            'pinjamanAktif' => Pinjaman::query()
                ->whereIn('status', self::STATUS_PINJAMAN_AKTIF)
                ->count(),

            'totalSimpanan' => Simpanan::query()
                ->where('status', 'disetujui')
                ->sum('jumlah'),

            'saldoKas' => Kas::query()
                ->orderByDesc('id')
                ->value('saldo_sesudah') ?? 0,

            'menungguCS' => Pinjaman::query()
                ->where('status', 'menunggu_cs')
                ->count(),

            'menungguAO' => Pinjaman::query()
                ->where('status', 'menunggu_ao')
                ->count(),

            'menungguKabag' => Pinjaman::query()
                ->where('status', 'menunggu_kabag')
                ->count(),

            'pinjamanDisetujui' => Pinjaman::query()
                ->where('status', 'disetujui')
                ->count(),

            'pinjamanDicairkan' => Pinjaman::query()
                ->where('status', 'dicairkan')
                ->count(),
        ];
    }

    /**
     * Dashboard Anggota.
     */
    private function anggotaDashboard(): array
    {
        $anggota = auth()->user()->anggota;

        if (!$anggota) {
            return [
                'simpananPokok' => 0,
                'simpananWajib' => 0,
                'simpananSukarela' => 0,
                'pinjamanAktif' => 0,
                'pinjaman' => null,
            ];
        }

        $pinjaman = $anggota->pinjaman()
            ->whereIn('status', self::STATUS_PINJAMAN_AKTIF)
            ->latest()
            ->first();

        return [
            'simpananPokok' => $anggota->saldo_simpanan_pokok ?? 0,

            'simpananWajib' => $anggota->saldo_simpanan_wajib ?? 0,

            'simpananSukarela' => $anggota->saldo_simpanan_sukarela ?? 0,

            'pinjamanAktif' => $anggota->pinjaman()
                ->whereIn('status', self::STATUS_PINJAMAN_AKTIF)
                ->count(),

            'pinjaman' => $pinjaman,
        ];
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\Kas;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Models\User;
use App\Models\PencairanSimpanan;
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
        // Jumlah anggota aktif.
        'totalAnggota' => Anggota::query()
            ->where('status_keanggotaan', 'aktif')
            ->count(),

        // Jumlah pengajuan pinjaman yang menunggu pemeriksaan CS.
        'menungguCS' => Pinjaman::query()
            ->where('status', 'menunggu_cs')
            ->count(),

        // Jumlah pinjaman aktif.
        'pinjamanAktif' => Pinjaman::query()
            ->whereIn('status', self::STATUS_PINJAMAN_AKTIF)
            ->count(),

        // Jumlah angsuran yang menunggu verifikasi CS/Teller.
        'menungguVerifikasiAngsuran' => Angsuran::query()
            ->menungguVerifikasi()
            ->count(),

        // Jumlah pengajuan pencairan simpanan yang menunggu pemeriksaan CS.
        'menungguPencairanSimpanan' => PencairanSimpanan::query()
            ->where('status', 'menunggu_cs')
            ->count(),

        // Daftar pengajuan pinjaman yang menunggu pemeriksaan CS.
        'pengajuanCS' => Pinjaman::query()
            ->with(['anggota.user'])
            ->where('status', 'menunggu_cs')
            ->latest()
            ->get(),

        // Lima pembayaran angsuran terbaru yang menunggu verifikasi.
        'pengajuanAngsuran' => Angsuran::query()
            ->with([
                'pinjaman.anggota.user',
            ])
            ->menungguVerifikasi()
            ->orderByDesc('tanggal_bayar')
            ->orderByDesc('id')
            ->limit(5)
            ->get(),

        // Lima pengajuan pencairan simpanan terbaru yang menunggu pemeriksaan CS.
        'pengajuanPencairan' => PencairanSimpanan::query()
            ->with(['anggota.user'])
            ->where('status', 'menunggu_cs')
            ->latest()
            ->limit(5)
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
        // Ringkasan pinjaman.
        'menungguKabag' => Pinjaman::query()
            ->where('status', 'menunggu_kabag')
            ->count(),

        'pinjamanDisetujui' => Pinjaman::query()
            ->where('status', 'disetujui')
            ->count(),

        'pinjamanDicairkan' => Pinjaman::query()
            ->where('status', 'dicairkan')
            ->count(),

        // Pengajuan pinjaman yang membutuhkan keputusan Kabag.
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

        // Ringkasan pencairan simpanan yang menunggu keputusan Kabag.
        'menungguPencairanSimpanan' => PencairanSimpanan::query()
            ->where('status', 'menunggu_kabag')
            ->count(),

        // Total seluruh pengajuan pencairan simpanan.
        'totalPencairanSimpanan' => PencairanSimpanan::query()
            ->count(),

        // Lima pengajuan pencairan simpanan terbaru yang menunggu keputusan Kabag.
        'pengajuanPencairanSimpanan' => PencairanSimpanan::query()
            ->with(['anggota.user', 'pemeriksaCs'])
            ->where('status', 'menunggu_kabag')
            ->latest()
            ->limit(5)
            ->get(),
    ];
}

/**
 * Dashboard Bendahara.
 *
 * Berfokus pada pekerjaan operasional pencairan
 * simpanan dan pinjaman.
 */
private function bendaharaDashboard(): array
{
    return [
        /*
        |--------------------------------------------------------------------------
        | RINGKASAN PENCAIRAN SIMPANAN
        |--------------------------------------------------------------------------
        */

        'menungguPencairanSimpanan' => PencairanSimpanan::query()
            ->where('status', 'menunggu_pencairan')
            ->count(),

        'totalPencairanSimpananSelesai' => PencairanSimpanan::query()
            ->where('status', 'selesai')
            ->count(),

        /*
        |--------------------------------------------------------------------------
        | DAFTAR PENCAIRAN SIMPANAN SIAP DIPROSES
        |--------------------------------------------------------------------------
        */

        'daftarPencairanSimpanan' => PencairanSimpanan::query()
            ->with([
                'anggota.user',
                'penyetujuKabag',
            ])
            ->where('status', 'menunggu_pencairan')
            ->latest()
            ->limit(5)
            ->get(),

        /*
        |--------------------------------------------------------------------------
        | RINGKASAN PENCAIRAN PINJAMAN
        |--------------------------------------------------------------------------
        */

        'pinjamanDisetujui' => Pinjaman::query()
            ->where('status', 'disetujui')
            ->count(),

        'pinjamanDicairkan' => Pinjaman::query()
            ->where('status', 'dicairkan')
            ->count(),

        /*
        |--------------------------------------------------------------------------
        | DAFTAR PINJAMAN SIAP DICAIRKAN
        |--------------------------------------------------------------------------
        */

        'pinjamanSiapDicairkan' => Pinjaman::query()
            ->with([
                'anggota.user',
                'masterPinjaman',
            ])
            ->where('status', 'disetujui')
            ->latest('tanggal_pengajuan')
            ->limit(5)
            ->get(),

        /*
        |--------------------------------------------------------------------------
        | RIWAYAT PENCAIRAN SIMPANAN
        |--------------------------------------------------------------------------
        */

        'riwayatPencairanSimpanan' => PencairanSimpanan::query()
            ->with([
                'anggota.user',
                'bendahara',
            ])
            ->where('status', 'selesai')
            ->latest('tanggal_pencairan')
            ->limit(5)
            ->get(),

        /*
        |--------------------------------------------------------------------------
        | RIWAYAT PENCAIRAN PINJAMAN
        |--------------------------------------------------------------------------
        */

        'riwayatPencairanPinjaman' => Pinjaman::query()
            ->with([
                'anggota.user',
                'masterPinjaman',
            ])
            ->whereIn('status', ['dicairkan', 'lunas'])
            ->latest('tanggal_pengajuan')
            ->limit(5)
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
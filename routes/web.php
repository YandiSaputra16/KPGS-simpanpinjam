<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\AngsuranController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\SimpananController;
use App\Http\Controllers\PencairanSimpananController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\ExportLaporanKeuanganController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ExportLaporanController;


/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicController::class, 'home'])
    ->name('public.home');

Route::get('/berita', [PublicController::class, 'newsIndex'])
    ->name('public.news.index');


/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRATOR
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            /*
            |--------------------------------------------------------------------------
            | Company Profile
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/company-profile',
                [AdminController::class, 'companyProfile']
            )->name('company-profile');

            Route::put(
                '/company-profile',
                [AdminController::class, 'updateCompanyProfile']
            )->name('company-profile.update');


            /*
            |--------------------------------------------------------------------------
            | Pengaturan Aplikasi
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/settings',
                [AdminController::class, 'settings']
            )->name('settings');

            Route::put(
                '/settings',
                [AdminController::class, 'updateSettings']
            )->name('settings.update');

            /*
            |--------------------------------------------------------------------------
            | Berita KPGS
            |--------------------------------------------------------------------------
            */

            Route::resource('berita', BeritaController::class)
                ->except(['show'])
                ->parameters([
                    'berita' => 'berita',
                ]);

            Route::patch(
                '/berita/{berita}/publish',
                [BeritaController::class, 'publish']
            )->name('berita.publish');

            Route::patch(
                '/berita/{berita}/unpublish',
                [BeritaController::class, 'unpublish']
            )->name('berita.unpublish');
        });


    /*
    |--------------------------------------------------------------------------
    | MANAJEMEN PENGGUNA
    |--------------------------------------------------------------------------
    |
    | Hanya Admin.
    |
    */

    Route::resource('users', UserController::class)
        ->except(['show'])
        ->middleware('role:admin');


    /*
    |--------------------------------------------------------------------------
    | MANAJEMEN ANGGOTA
    |--------------------------------------------------------------------------
    |
    | CS membuat dan mengelola akun anggota.
    |
    */

    Route::resource('anggota', AnggotaController::class)
        ->except(['show'])
        ->parameters([
            'anggota' => 'anggota',
        ])
        ->middleware('role:cs');


    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | PINJAMAN ANGGOTA
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/pinjaman/ajukan',
        [PinjamanController::class, 'create']
    )->name('pinjaman.create');

    Route::post(
        '/pinjaman',
        [PinjamanController::class, 'store']
    )->name('pinjaman.store');

    Route::get(
        '/pinjaman/saya',
        [PinjamanController::class, 'anggotaIndex']
    )->name('pinjaman.anggota.index');


    /*
    |--------------------------------------------------------------------------
    | SIMPANAN - CS / TELLER
    |--------------------------------------------------------------------------
    |
    | CS/Teller bertugas memeriksa dan memverifikasi pembayaran
    | yang diajukan anggota melalui sistem.
    |
    */

    Route::middleware('role:cs')->group(function () {

        Route::get(
            '/simpanan',
            [SimpananController::class, 'index']
        )->name('simpanan.index');

        Route::get(
            '/simpanan/create',
            [SimpananController::class, 'create']
        )->name('simpanan.create');

        Route::post(
            '/simpanan',
            [SimpananController::class, 'store']
        )->name('simpanan.store');

        Route::get(
            '/simpanan/{simpanan}',
            [SimpananController::class, 'show']
        )->name('simpanan.show');

        Route::post(
            '/simpanan/{simpanan}/verifikasi',
            [SimpananController::class, 'verifikasi']
        )->name('simpanan.verifikasi');
    });


    /*
    |--------------------------------------------------------------------------
    | SIMPANAN - ANGGOTA
    |--------------------------------------------------------------------------
    |
    | Anggota hanya dapat melihat transaksi miliknya sendiri
    | dan mengajukan pembayaran.
    |
    */

    Route::middleware('role:anggota')->group(function () {

        Route::get(
            '/simpanan-saya',
            [SimpananController::class, 'milikSaya']
        )->name('simpanan.saya.index');

        Route::get(
            '/simpanan-saya/pembayaran',
            [SimpananController::class, 'buatPengajuan']
        )->name('simpanan.pengajuan.create');

        Route::post(
            '/simpanan-saya/pembayaran',
            [SimpananController::class, 'ajukanPembayaran']
        )->name('simpanan.pengajuan.store');

        Route::get(
            '/simpanan-saya/{simpanan}',
            [SimpananController::class, 'detailMilikSaya']
        )->name('simpanan.saya.show');
    });


    /*
    |--------------------------------------------------------------------------
    | ANGSURAN - CS / TELLER
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:cs')->group(function () {

        Route::get(
            '/angsuran',
            [AngsuranController::class, 'index']
        )->name('angsuran.index');

        Route::get(
            '/angsuran/{angsuran}',
            [AngsuranController::class, 'show']
        )->name('angsuran.show');

        Route::post(
            '/angsuran/{angsuran}/verifikasi',
            [AngsuranController::class, 'verifikasi']
        )->name('angsuran.verifikasi');

        Route::post(
            '/angsuran/{angsuran}/tolak',
            [AngsuranController::class, 'tolak']
        )->name('angsuran.tolak');
    });


    /*
    |--------------------------------------------------------------------------
    | ANGSURAN - ANGGOTA
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:anggota')->group(function () {

        Route::get(
            '/angsuran-saya',
            [AngsuranController::class, 'milikSaya']
        )->name('angsuran.saya.index');

        Route::get(
            '/angsuran-saya/transaksi/{angsuran}',
            [AngsuranController::class, 'detailTransaksiSaya']
        )->name('angsuran.saya.transaksi');

        Route::get(
            '/angsuran-saya/{pinjaman}',
            [AngsuranController::class, 'detailMilikSaya']
        )->name('angsuran.saya.show');

        Route::post(
            '/angsuran-saya/{pinjaman}/bayar',
            [AngsuranController::class, 'bayar']
        )->name('angsuran.saya.bayar');

        Route::post(
            '/angsuran-saya/{pinjaman}/upload-ulang',
            [AngsuranController::class, 'uploadUlang']
        )->name('angsuran.saya.upload-ulang');
    });


    /*
    |--------------------------------------------------------------------------
    | PINJAMAN - CS
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:cs')->group(function () {

        Route::get(
            '/pinjaman/cs',
            [PinjamanController::class, 'csIndex']
        )->name('pinjaman.cs.index');

        Route::get(
            '/pinjaman/cs/{pinjaman}',
            [PinjamanController::class, 'csShow']
        )->name('pinjaman.cs.show');

        Route::post(
            '/pinjaman/cs/{pinjaman}/proses',
            [PinjamanController::class, 'csProses']
        )->name('pinjaman.cs.proses');

        Route::post(
            '/pinjaman/cs/{pinjaman}/tolak',
            [PinjamanController::class, 'csTolak']
        )->name('pinjaman.cs.tolak');
    });


    /*
    |--------------------------------------------------------------------------
    | PINJAMAN - AO
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:ao')->group(function () {

        Route::get(
            '/pinjaman/ao',
            [PinjamanController::class, 'aoIndex']
        )->name('pinjaman.ao.index');

        Route::get(
            '/pinjaman/ao/{pinjaman}',
            [PinjamanController::class, 'aoShow']
        )->name('pinjaman.ao.show');

        Route::post(
            '/pinjaman/ao/{pinjaman}/proses',
            [PinjamanController::class, 'aoProses']
        )->name('pinjaman.ao.proses');

        Route::post(
            '/pinjaman/ao/{pinjaman}/tolak',
            [PinjamanController::class, 'aoTolak']
        )->name('pinjaman.ao.tolak');
    });


    /*
    |--------------------------------------------------------------------------
    | PINJAMAN - KABAG
    |--------------------------------------------------------------------------
    |
    | Kabag memeriksa hasil analisis AO dan memberikan keputusan akhir.
    |
    */

    Route::middleware('role:kabag')->group(function () {

        Route::get(
            '/pinjaman/kabag',
            [PinjamanController::class, 'kabagIndex']
        )->name('pinjaman.kabag.index');

        Route::get(
            '/pinjaman/kabag/{pinjaman}',
            [PinjamanController::class, 'kabagShow']
        )->name('pinjaman.kabag.show');

        Route::post(
            '/pinjaman/kabag/{pinjaman}/setujui',
            [PinjamanController::class, 'kabagSetujui']
        )->name('pinjaman.kabag.setujui');

        Route::post(
            '/pinjaman/kabag/{pinjaman}/tolak',
            [PinjamanController::class, 'kabagTolak']
        )->name('pinjaman.kabag.tolak');
    });


    /*
|--------------------------------------------------------------------------
| AKTIVITAS SISTEM - KABAG
|--------------------------------------------------------------------------
*/

Route::middleware('role:kabag')->group(function () {
    Route::get(
        '/kabag/aktivitas-sistem',
        [AdminController::class, 'activities']
    )->name('kabag.activities');
});

    /*
    |--------------------------------------------------------------------------
    | PINJAMAN - BENDAHARA
    |--------------------------------------------------------------------------
    |
    | Bendahara melakukan pencairan setelah pinjaman disetujui Kabag.
    |
    */

    Route::middleware('role:bendahara')->group(function () {

        Route::get(
            '/pinjaman/bendahara',
            [PinjamanController::class, 'bendaharaIndex']
        )->name('pinjaman.bendahara.index');

        Route::get(
            '/pinjaman/bendahara/{pinjaman}',
            [PinjamanController::class, 'bendaharaShow']
        )->name('pinjaman.bendahara.show');

        Route::post(
            '/pinjaman/bendahara/{pinjaman}/cairkan',
            [PinjamanController::class, 'bendaharaCairkan']
        )->name('pinjaman.bendahara.cairkan');
    });


    /*
    |--------------------------------------------------------------------------
    | Laporan Anggota
    |--------------------------------------------------------------------------
    |
    */

    Route::get(
        '/laporan/anggota',
        [LaporanController::class, 'anggota']
    )
        ->middleware('role:cs')
        ->name('laporan.anggota');


    /*
    |--------------------------------------------------------------------------
    | Laporan Simpanan
    |--------------------------------------------------------------------------
    |
    */

   Route::middleware('role:cs,bendahara')->group(function () {
    Route::get(
        '/laporan/simpanan',
        [LaporanController::class, 'simpanan']
    )->name('laporan.simpanan');

    Route::get(
        '/laporan/simpanan/export',
        [LaporanController::class, 'exportSimpanan']
    )->name('laporan.simpanan.export');
});


    /*
    |--------------------------------------------------------------------------
    | Laporan Pinjaman
    |--------------------------------------------------------------------------
    |
    */

    Route::get(
        '/laporan/pinjaman',
        [LaporanController::class, 'pinjaman']
    )
        ->middleware('role:cs,ao,kabag,bendahara')
        ->name('laporan.pinjaman');


    /*
    |--------------------------------------------------------------------------
    | Laporan Angsuran
    |--------------------------------------------------------------------------
    |
    */

    Route::get(
        '/laporan/angsuran',
        [LaporanController::class, 'angsuran']
    )
        ->middleware('role:kabag,bendahara')
        ->name('laporan.angsuran');


    /*
    |--------------------------------------------------------------------------
    | Laporan Kas
    |--------------------------------------------------------------------------
    |
    */

    Route::get(
        '/laporan/kas',
        [LaporanController::class, 'kas']
    )
        ->middleware('role:bendahara')
        ->name('laporan.kas');

        Route::get(
    '/laporan/keuangan',
    [LaporanKeuanganController::class, 'index']
)
    ->middleware('role:kabag,bendahara')
    ->name('laporan.keuangan');

    /*
    |--------------------------------------------------------------------------
    | PENCAIRAN SIMPANAN
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:anggota')->group(function () {
        Route::get('/pencairan-simpanan-saya', [PencairanSimpananController::class, 'anggotaIndex'])
            ->name('pencairan-simpanan.saya.index');
        Route::get('/pencairan-simpanan-saya/ajukan', [PencairanSimpananController::class, 'create'])
            ->name('pencairan-simpanan.saya.create');
        Route::post('/pencairan-simpanan-saya', [PencairanSimpananController::class, 'store'])
            ->name('pencairan-simpanan.saya.store');
    });

    Route::middleware('role:cs')->prefix('pencairan-simpanan/cs')->name('pencairan-simpanan.cs.')->group(function () {
        Route::get('/', [PencairanSimpananController::class, 'csIndex'])->name('index');
        Route::post('/{pencairanSimpanan}/periksa', [PencairanSimpananController::class, 'periksaCs'])->name('periksa');
    });

    Route::middleware('role:kabag')->prefix('pencairan-simpanan/kabag')->name('pencairan-simpanan.kabag.')->group(function () {
        Route::get('/', [PencairanSimpananController::class, 'kabagIndex'])->name('index');
        Route::post('/{pencairanSimpanan}/keputusan', [PencairanSimpananController::class, 'keputusanKabag'])->name('keputusan');
    });

    Route::middleware('role:bendahara')->prefix('pencairan-simpanan/bendahara')->name('pencairan-simpanan.bendahara.')->group(function () {
        Route::get('/', [PencairanSimpananController::class, 'bendaharaIndex'])->name('index');
        Route::post('/{pencairanSimpanan}/cairkan', [PencairanSimpananController::class, 'cairkan'])->name('cairkan');
    });

    });

Route::middleware(['auth', 'role:bendahara'])->group(function () {
    Route::get(
        '/laporan/keuangan/export',
        ExportLaporanKeuanganController::class
    )->name('laporan.keuangan.export');

    Route::get(
    '/laporan/anggota/export',
    [ExportLaporanController::class, 'anggota']
)
    ->middleware('role:cs')
    ->name('laporan.anggota.export');

    Route::get(
    '/laporan/pinjaman/export',
    [ExportLaporanController::class, 'pinjaman']
)
    ->middleware('role:cs,ao,kabag,bendahara')
    ->name('laporan.pinjaman.export');
});

/*
|--------------------------------------------------------------------------
| NOTIFIKASI
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('notifikasi')->name('notifikasi.')->group(function () {
    // Menampilkan daftar notifikasi milik pengguna yang login.
    Route::get('/', [NotifikasiController::class, 'index'])
        ->name('index');

    // Menandai satu notifikasi sebagai telah dibaca.
    Route::post('/{notifikasi}/baca', [NotifikasiController::class, 'baca'])
        ->name('baca');

    // Menandai seluruh notifikasi pengguna sebagai telah dibaca.
    Route::post('/baca-semua', [NotifikasiController::class, 'bacaSemua'])
        ->name('baca-semua');
});
/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
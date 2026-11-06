<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    /**
     * Menampilkan notifikasi milik pengguna yang sedang login.
     */
    public function index(Request $request): View
    {
        $userId = (int) $request->user()->id;

        $query = Notifikasi::milikUser($userId);

        $filter = $request->query('filter', 'semua');

        if (!in_array(
            $filter,
            ['semua', 'belum_dibaca', 'sudah_dibaca'],
            true
        )) {
            $filter = 'semua';
        }

        if ($filter === 'belum_dibaca') {
            $query->belumDibaca();
        } elseif ($filter === 'sudah_dibaca') {
            $query->sudahDibaca();
        }

        $notifikasi = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $jumlahBelumDibaca = Notifikasi::milikUser($userId)
            ->belumDibaca()
            ->count();

        return view('notifikasi.index', compact(
            'notifikasi',
            'filter',
            'jumlahBelumDibaca'
        ));
    }

    /**
     * Menandai satu notifikasi sebagai telah dibaca,
     * kemudian mengarahkan pengguna ke halaman tujuan.
     */
    public function baca(
        Request $request,
        Notifikasi $notifikasi
    ): RedirectResponse {
        $user = $request->user();

        // Pengguna hanya boleh membaca notifikasi miliknya sendiri.
        abort_unless(
            (int) $notifikasi->user_id === (int) $user->id,
            403,
            'Anda tidak memiliki akses ke notifikasi ini.'
        );

        $notifikasi->tandaiDibaca();

        /*
         * Jika notifikasi memiliki tautan lokal yang valid,
         * gunakan tautan tersebut terlebih dahulu.
         */
        if ($notifikasi->memilikiLink()) {
            $link = trim((string) $notifikasi->link);

            if (
                str_starts_with($link, '/')
                && !str_starts_with($link, '//')
                && !str_contains($link, '\\')
            ) {
                return redirect($link);
            }

            // URL eksternal hanya diterima jika host-nya sama
            // dengan host aplikasi yang dikonfigurasi.
            if (filter_var($link, FILTER_VALIDATE_URL)) {
                $appHost = parse_url(
                    config('app.url'),
                    PHP_URL_HOST
                );

                $linkHost = parse_url($link, PHP_URL_HOST);

                if (
                    $appHost
                    && $linkHost
                    && strcasecmp($appHost, $linkHost) === 0
                ) {
                    return redirect($link);
                }
            }
        }

        /*
         * Fallback untuk notifikasi yang tidak memiliki tautan
         * atau tautannya tidak dapat digunakan.
         */
        $judul = strtolower((string) $notifikasi->judul);
        $pesan = strtolower((string) $notifikasi->pesan);

        $berkaitanDenganPencairan =
            str_contains($judul, 'pencairan')
            || str_contains($judul, 'penarikan')
            || str_contains($pesan, 'pencairan')
            || str_contains($pesan, 'penarikan');

        $fallbackRoute = null;

        if (
            $notifikasi->tipe === 'simpanan'
            && $berkaitanDenganPencairan
        ) {
            $fallbackRoute = match ($user->role) {
                'anggota' => 'pencairan-simpanan.saya.index',
                'cs' => 'pencairan-simpanan.cs.index',
                'kabag' => 'pencairan-simpanan.kabag.index',
                'bendahara' => 'pencairan-simpanan.bendahara.index',
                default => null,
            };
        }

        /*
         * Fallback untuk notifikasi modul lainnya.
         */
        if (!$fallbackRoute) {
            $fallbackRoute = match ($notifikasi->tipe) {
                'pinjaman' => match ($user->role) {
                    'cs' => 'pinjaman.cs.index',
                    'ao' => 'pinjaman.ao.index',
                    'kabag' => 'pinjaman.kabag.index',
                    'bendahara' => 'pinjaman.bendahara.index',
                    'anggota' => 'pinjaman.anggota.index',
                    default => null,
                },

                'angsuran' => match ($user->role) {
                    'cs' => 'angsuran.index',
                    'anggota' => 'angsuran.saya.index',
                    default => null,
                },

                'simpanan' => match ($user->role) {
                    'cs' => 'simpanan.index',
                    'anggota' => 'simpanan.saya.index',
                    default => null,
                },

                default => null,
            };
        }

        if ($fallbackRoute && Route::has($fallbackRoute)) {
            return redirect()
                ->route($fallbackRoute)
                ->with(
                    'success',
                    'Notifikasi telah ditandai dibaca.'
                );
        }

        return redirect()
            ->route('notifikasi.index')
            ->with(
                'success',
                'Notifikasi telah ditandai dibaca.'
            );
    }

    /**
     * Menandai seluruh notifikasi pengguna sebagai telah dibaca.
     */
    public function bacaSemua(Request $request): RedirectResponse
    {
        Notifikasi::bacaSemua((int) $request->user()->id);

        return redirect()
            ->route('notifikasi.index', ['filter' => 'belum_dibaca'])
            ->with(
                'success',
                'Semua notifikasi telah ditandai dibaca.'
            );
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Kas;
use App\Models\Notifikasi;
use App\Models\PencairanSimpanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PencairanSimpananController extends Controller
{
    private function kolomSaldo(string $jenis): string
    {
        return match ($jenis) {
            'pokok' => 'saldo_simpanan_pokok',
            'wajib' => 'saldo_simpanan_wajib',
            'sukarela' => 'saldo_simpanan_sukarela',
            default => throw ValidationException::withMessages(['jenis_simpanan' => 'Jenis simpanan tidak valid.']),
        };
    }

    public function anggotaIndex(Request $request): View
    {
        $anggota = $request->user()->anggota;
        abort_unless($anggota, 404);
        $items = PencairanSimpanan::where('anggota_id', $anggota->id)->latest()->paginate(10);
        return view('pencairan-simpanan.anggota-index', compact('items', 'anggota'));
    }

    public function create(Request $request): View
    {
        $anggota = $request->user()->anggota;
        abort_unless($anggota, 404);
        return view('pencairan-simpanan.create', ['anggota' => $anggota, 'jenisSimpanan' => PencairanSimpanan::JENIS]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $anggotaAwal = $user->anggota;
        abort_unless($anggotaAwal && $anggotaAwal->isAktif(), 403, 'Akun anggota tidak aktif.');
        $data = $request->validate([
            'jenis_simpanan' => ['required', 'in:pokok,wajib,sukarela'],
            'jumlah' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'alasan' => ['required', 'string', 'max:2000'],
            'bukti' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        $path = $request->hasFile('bukti') ? $request->file('bukti')->store('pencairan-simpanan/pengajuan', 'public') : null;
        try {
            DB::transaction(function () use ($data, $anggotaAwal, $path) {
                $anggota = Anggota::whereKey($anggotaAwal->id)->lockForUpdate()->firstOrFail();
                $kolom = $this->kolomSaldo($data['jenis_simpanan']);
                $saldo = (float) $anggota->{$kolom};
                $jumlah = (float) $data['jumlah'];
                if ($jumlah > $saldo) {
                    throw ValidationException::withMessages(['jumlah' => 'Nominal pencairan melebihi saldo simpanan yang dipilih.']);
                }
                $pending = (float) PencairanSimpanan::where('anggota_id', $anggota->id)
                    ->where('jenis_simpanan', $data['jenis_simpanan'])
                    ->whereIn('status', ['menunggu_cs', 'menunggu_kabag', 'menunggu_pencairan'])
                    ->sum('jumlah');
                if ($jumlah + $pending > $saldo) {
                    throw ValidationException::withMessages(['jumlah' => 'Saldo tidak mencukupi setelah memperhitungkan pengajuan lain yang masih diproses untuk jenis simpanan ini.']);
                }
                $pengajuan = PencairanSimpanan::create([
                    'no_pengajuan' => PencairanSimpanan::generateNomor(),
                    'anggota_id' => $anggota->id,
                    'jenis_simpanan' => $data['jenis_simpanan'],
                    'jumlah' => $jumlah,
                    'alasan' => $data['alasan'],
                    'bukti' => $path,
                    'status' => 'menunggu_cs',
                ]);

                // Beri notifikasi kepada seluruh akun CS aktif setelah pengajuan dibuat.
                $namaJenis = PencairanSimpanan::JENIS[$data['jenis_simpanan']];
                $tautan = route('pencairan-simpanan.cs.index');

                User::query()
                    ->where('role', 'cs')
                    ->where('status', 'aktif')
                    ->pluck('id')
                    ->each(function ($csId) use ($pengajuan, $anggota, $jumlah, $namaJenis, $tautan) {
                        Notifikasi::kirim(
                            (int) $csId,
                            'Pengajuan pencairan simpanan baru',
                            'Anggota ' . $anggota->kode_anggota . ' mengajukan pencairan ' . $namaJenis
                                . ' sebesar Rp' . number_format($jumlah, 0, ',', '.')
                                . '. Nomor pengajuan: ' . $pengajuan->no_pengajuan . '.',
                            'simpanan',
                            $tautan
                        );
                    });
            });
        } catch (Throwable $e) {
            if ($path) Storage::disk('public')->delete($path);
            throw $e;
        }
        return redirect()->route('pencairan-simpanan.saya.index')->with('success', 'Pengajuan pencairan berhasil dikirim dan menunggu pemeriksaan CS/Teller.');
    }

    public function csIndex(): View
    {
        $items = PencairanSimpanan::with('anggota.user')->where('status', 'menunggu_cs')->latest()->paginate(15);
        return view('pencairan-simpanan.operasional-index', ['items' => $items, 'tahap' => 'cs']);
    }
public function csShow(PencairanSimpanan $pencairanSimpanan): View
{
    abort_unless(
        auth()->user()->role === 'cs'
        && auth()->user()->status === 'aktif',
        403
    );

    $pencairanSimpanan->load([
        'anggota.user',
        'pemeriksaCs',
        'penyetujuKabag',
        'bendahara',
    ]);

    $anggota = $pencairanSimpanan->anggota;

    $kolom = $this->kolomSaldo(
        $pencairanSimpanan->jenis_simpanan
    );

    $saldoSimpanan = (float) $anggota->{$kolom};

    return view('pencairan-simpanan.show', [
        'item' => $pencairanSimpanan,
        'anggota' => $anggota,
        'saldoSimpanan' => $saldoSimpanan,
    ]);
}


    public function kabagIndex(): View
    {
        $items = PencairanSimpanan::with('anggota.user', 'pemeriksaCs')
            ->where('status', 'menunggu_kabag')
            ->latest()
            ->paginate(15);

        $riwayatPencairan = PencairanSimpanan::with('anggota.user')
            ->whereIn('status', ['menunggu_kabag', 'menunggu_pencairan', 'ditolak_kabag', 'selesai'])
            ->latest()
            ->paginate(10, ['*'], 'riwayat');

        return view('pencairan-simpanan.operasional-index', [
            'items' => $items,
            'tahap' => 'kabag',
            'riwayatPencairan' => $riwayatPencairan,
        ]);
    }

    public function bendaharaIndex(): View
    {
        $items = PencairanSimpanan::with('anggota.user', 'penyetujuKabag')
            ->where('status', 'menunggu_pencairan')
            ->latest()
            ->paginate(15);

        $riwayatPencairan = PencairanSimpanan::with('anggota.user')
            ->whereIn('status', ['menunggu_pencairan', 'selesai'])
            ->latest()
            ->paginate(10, ['*'], 'riwayat');

        return view('pencairan-simpanan.operasional-index', [
            'items' => $items,
            'tahap' => 'bendahara',
            'riwayatPencairan' => $riwayatPencairan,
        ]);
    }

    public function periksaCs(Request $request, PencairanSimpanan $pencairanSimpanan): RedirectResponse
    {
        abort_unless($request->user()->role === 'cs' && $request->user()->status === 'aktif', 403);
        $data = $request->validate(['keputusan' => ['required', 'in:lanjut,tolak'], 'catatan' => ['nullable', 'string', 'max:2000']]);
        DB::transaction(function () use ($pencairanSimpanan, $data, $request) {
            $item = PencairanSimpanan::whereKey($pencairanSimpanan->id)->lockForUpdate()->firstOrFail();
            if ($item->status !== 'menunggu_cs') throw ValidationException::withMessages(['status' => 'Pengajuan ini sudah diproses pada tahap CS/Teller.']);
            $item->update(['status' => $data['keputusan'] === 'lanjut' ? 'menunggu_kabag' : 'ditolak_cs', 'catatan_cs' => $data['catatan'] ?? null, 'diperiksa_cs_oleh' => $request->user()->id, 'diperiksa_cs_pada' => now()]);
        });
        return back()->with('success', 'Hasil pemeriksaan CS/Teller berhasil disimpan.');
    }

    public function keputusanKabag(Request $request, PencairanSimpanan $pencairanSimpanan): RedirectResponse
    {
        abort_unless($request->user()->role === 'kabag' && $request->user()->status === 'aktif', 403);
        $data = $request->validate(['keputusan' => ['required', 'in:setujui,tolak'], 'catatan' => ['nullable', 'string', 'max:2000']]);
        DB::transaction(function () use ($pencairanSimpanan, $data, $request) {
            $item = PencairanSimpanan::whereKey($pencairanSimpanan->id)->lockForUpdate()->firstOrFail();
            if ($item->status !== 'menunggu_kabag') throw ValidationException::withMessages(['status' => 'Pengajuan ini tidak sedang menunggu persetujuan Kabag.']);
            $item->update(['status' => $data['keputusan'] === 'setujui' ? 'menunggu_pencairan' : 'ditolak_kabag', 'catatan_kabag' => $data['catatan'] ?? null, 'disetujui_kabag_oleh' => $request->user()->id, 'disetujui_kabag_pada' => now()]);
        });
        return back()->with('success', $data['keputusan'] === 'setujui' ? 'Pengajuan disetujui. Menunggu pembayaran Bendahara.' : 'Pengajuan pencairan ditolak.');
    }

    public function cairkan(Request $request, PencairanSimpanan $pencairanSimpanan): RedirectResponse
    {
        $bendahara = $request->user();
        abort_unless($bendahara->role === 'bendahara' && $bendahara->status === 'aktif', 403);
        $data = $request->validate(['tanggal_pencairan' => ['required', 'date', 'before_or_equal:today'], 'bukti_pencairan' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']]);
        $path = $request->hasFile('bukti_pencairan') ? $request->file('bukti_pencairan')->store('pencairan-simpanan/pembayaran', 'public') : null;
        try {
            DB::transaction(function () use ($pencairanSimpanan, $bendahara, $data, $path) {
                $item = PencairanSimpanan::whereKey($pencairanSimpanan->id)->lockForUpdate()->firstOrFail();
                if ($item->status !== 'menunggu_pencairan') throw ValidationException::withMessages(['status' => 'Pengajuan belum disetujui Kabag atau sudah dicairkan.']);
                $anggota = Anggota::whereKey($item->anggota_id)->lockForUpdate()->firstOrFail();
                $kolom = $this->kolomSaldo($item->jenis_simpanan);
                $jumlah = (float) $item->jumlah;
                if ((float) $anggota->{$kolom} < $jumlah) throw ValidationException::withMessages(['jumlah' => 'Saldo jenis simpanan ini tidak lagi mencukupi.']);
                if (Kas::where('referensi_tipe', 'pencairan_simpanan')->where('referensi_id', $item->id)->exists()) throw ValidationException::withMessages(['status' => 'Transaksi kas untuk pengajuan ini sudah tercatat.']);
                $anggota->decrement($kolom, $jumlah);
                Kas::catat([
                    'tanggal' => $data['tanggal_pencairan'], 'jenis' => 'keluar', 'kategori' => 'simpanan', 'jumlah' => $jumlah,
                    'keterangan' => 'Pencairan ' . PencairanSimpanan::JENIS[$item->jenis_simpanan] . ' ' . $item->no_pengajuan . ' - ' . $anggota->kode_anggota,
                    'referensi_tipe' => 'pencairan_simpanan', 'referensi_id' => $item->id, 'dicatat_oleh' => $bendahara->id,
                ]);
                $item->update(['status' => 'selesai', 'tanggal_pencairan' => $data['tanggal_pencairan'], 'bukti_pencairan' => $path, 'dicairkan_oleh' => $bendahara->id]);
            });
        } catch (Throwable $e) {
            if ($path) Storage::disk('public')->delete($path);
            throw $e;
        }
        return back()->with('success', 'Pencairan berhasil dicatat. Saldo jenis simpanan terpilih dan kas keluar telah diperbarui.');
    }
}

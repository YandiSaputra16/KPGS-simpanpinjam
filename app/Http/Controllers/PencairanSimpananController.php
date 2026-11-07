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
            default => throw ValidationException::withMessages([
                'jenis_simpanan' => 'Jenis simpanan tidak valid.',
            ]),
        };
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'menunggu_cs' => 'Menunggu pemeriksaan CS/Teller',
            'menunggu_kabag' => 'Menunggu persetujuan Kabag',
            'menunggu_pencairan' => 'Menunggu pencairan Bendahara',
            'ditolak_cs' => 'Ditolak CS/Teller',
            'ditolak_kabag' => 'Ditolak Kabag',
            'selesai' => 'Selesai dicairkan',
            default => ucfirst(str_replace('_', ' ', $status)),
        };
    }

    private function kirimKeRole(string $role, string $judul, string $pesan, string $link): void
    {
        User::query()
            ->where('role', $role)
            ->where('status', 'aktif')
            ->pluck('id')
            ->each(fn ($id) => Notifikasi::kirim(
                (int) $id,
                $judul,
                $pesan,
                'simpanan',
                $link
            ));
    }

    private function kirimKeAnggota(PencairanSimpanan $item, string $judul, string $pesan): void
    {
        $userId = (int) $item->anggota()->value('user_id');
        if ($userId > 0) {
            Notifikasi::kirim(
                $userId,
                $judul,
                $pesan . ' Nomor pengajuan: ' . $item->no_pengajuan . '.',
                'simpanan',
                route('pencairan-simpanan.saya.show', $item, false)
            );
        }
    }

    public function anggotaIndex(Request $request): View
    {
        $anggota = $request->user()->anggota;
        abort_unless($anggota, 404);
        $items = PencairanSimpanan::where('anggota_id', $anggota->id)
            ->latest()->paginate(10);

        return view('pencairan-simpanan.anggota-index', compact('items', 'anggota'));
    }

    public function anggotaShow(Request $request, PencairanSimpanan $pencairanSimpanan): View
    {
        $anggota = $request->user()->anggota;
        abort_unless($anggota && (int) $pencairanSimpanan->anggota_id === (int) $anggota->id, 404);

        return $this->tampilkanDetail($pencairanSimpanan);
    }

    public function create(Request $request): View
    {
        $anggota = $request->user()->anggota;
        abort_unless($anggota, 404);

        return view('pencairan-simpanan.create', [
            'anggota' => $anggota,
            'jenisSimpanan' => PencairanSimpanan::JENIS,
        ]);
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

        $path = $request->hasFile('bukti')
            ? $request->file('bukti')->store('pencairan-simpanan/pengajuan', 'public')
            : null;

        try {
            DB::transaction(function () use ($data, $anggotaAwal, $path) {
                $anggota = Anggota::whereKey($anggotaAwal->id)->lockForUpdate()->firstOrFail();
                $kolom = $this->kolomSaldo($data['jenis_simpanan']);
                $saldo = (float) $anggota->{$kolom};
                $jumlah = (float) $data['jumlah'];

                if ($jumlah > $saldo) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Nominal pencairan melebihi saldo simpanan yang dipilih.',
                    ]);
                }

                $pending = (float) PencairanSimpanan::where('anggota_id', $anggota->id)
                    ->where('jenis_simpanan', $data['jenis_simpanan'])
                    ->whereIn('status', ['menunggu_cs', 'menunggu_kabag', 'menunggu_pencairan'])
                    ->sum('jumlah');

                if ($jumlah + $pending > $saldo) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Saldo tidak mencukupi setelah memperhitungkan pengajuan lain yang masih diproses.',
                    ]);
                }

                $item = PencairanSimpanan::create([
                    'no_pengajuan' => PencairanSimpanan::generateNomor(),
                    'anggota_id' => $anggota->id,
                    'jenis_simpanan' => $data['jenis_simpanan'],
                    'jumlah' => $jumlah,
                    'alasan' => $data['alasan'],
                    'bukti' => $path,
                    'status' => 'menunggu_cs',
                ]);

                $jenis = PencairanSimpanan::JENIS[$data['jenis_simpanan']];
                $pesan = 'Anggota ' . $anggota->kode_anggota . ' mengajukan ' . $jenis
                    . ' sebesar Rp' . number_format($jumlah, 0, ',', '.') . '. Nomor pengajuan: ' . $item->no_pengajuan . '.';

                $this->kirimKeRole(
                    'cs',
                    'Pengajuan pencairan simpanan baru',
                    $pesan,
                    route('pencairan-simpanan.cs.show', $item, false)
                );
            });
        } catch (Throwable $e) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        }

        return redirect()->route('pencairan-simpanan.saya.index')
            ->with('success', 'Pengajuan pencairan berhasil dikirim dan menunggu pemeriksaan CS/Teller.');
    }

    public function csIndex(): RedirectResponse
{
    return redirect()->route('simpanan.index');
}

    public function csShow(PencairanSimpanan $pencairanSimpanan): View
    {
        abort_unless(auth()->user()->role === 'cs' && auth()->user()->status === 'aktif', 403);
        return $this->tampilkanDetail($pencairanSimpanan);
    }

    public function kabagIndex(): View
    {
        $items = PencairanSimpanan::with('anggota.user', 'pemeriksaCs')
            ->where('status', 'menunggu_kabag')->latest()->paginate(15);

        $riwayatPencairan = PencairanSimpanan::with('anggota.user')
            ->whereIn('status', ['menunggu_kabag', 'menunggu_pencairan', 'ditolak_kabag', 'selesai'])
            ->latest()->paginate(10, ['*'], 'riwayat');
            $totalPengajuanKabag = PencairanSimpanan::query()->count();

$menungguKeputusanKabag = PencairanSimpanan::query()
    ->where('status', 'menunggu_kabag')
    ->count();

        return view('pencairan-simpanan.operasional-index', [
            'items' => $items, 'tahap' => 'kabag', 'riwayatPencairan' => $riwayatPencairan, 'totalPengajuanKabag' => $totalPengajuanKabag, 'menungguKeputusanKabag' => $menungguKeputusanKabag,
            'totalPengajuanKabag' => $totalPengajuanKabag,
            'menungguKeputusanKabag' => $menungguKeputusanKabag,
        ]);
    }

    public function kabagShow(PencairanSimpanan $pencairanSimpanan): View
    {
        abort_unless(auth()->user()->role === 'kabag' && auth()->user()->status === 'aktif', 403);
        return $this->tampilkanDetail($pencairanSimpanan);
    }

   
public function bendaharaIndex(): View
{
    // Pengajuan yang sudah disetujui Kabag dan menunggu pembayaran.
    $items = PencairanSimpanan::with('anggota.user', 'penyetujuKabag')
        ->where('status', 'menunggu_pencairan')
        ->latest()
        ->paginate(15);

    // Riwayat hanya berisi transaksi yang sudah selesai dicairkan.
    $riwayatPencairan = PencairanSimpanan::with('anggota.user')
        ->where('status', 'selesai')
        ->latest()
        ->paginate(10, ['*'], 'riwayat');

    // Total nominal pengajuan yang masih menunggu pembayaran.
    $totalMenunggu = (float) PencairanSimpanan::where('status', 'menunggu_pencairan')
        ->sum('jumlah');

    return view('pencairan-simpanan.bendahara-index', [
        'items' => $items,
        'riwayatPencairan' => $riwayatPencairan,
        'totalMenunggu' => $totalMenunggu,
    ]);
}

    public function bendaharaShow(PencairanSimpanan $pencairanSimpanan): View
    {
        abort_unless(auth()->user()->role === 'bendahara' && auth()->user()->status === 'aktif', 403);
        return $this->tampilkanDetail($pencairanSimpanan);
    }

    private function tampilkanDetail(PencairanSimpanan $item): View
    {
        $item->load(['anggota.user', 'pemeriksaCs', 'penyetujuKabag', 'bendahara']);
        $anggota = $item->anggota;
        $saldoSimpanan = (float) $anggota->{$this->kolomSaldo($item->jenis_simpanan)};

        return view('pencairan-simpanan.show', [
            'item' => $item,
            'anggota' => $anggota,
            'saldoSimpanan' => $saldoSimpanan,
            'statusLabel' => $this->statusLabel($item->status),
        ]);
    }

    public function periksaCs(Request $request, PencairanSimpanan $pencairanSimpanan): RedirectResponse
    {
        abort_unless($request->user()->role === 'cs' && $request->user()->status === 'aktif', 403);

        $data = $request->validate([
            'keputusan' => ['required', 'in:lanjut,tolak'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($pencairanSimpanan, $data, $request) {
            $item = PencairanSimpanan::whereKey($pencairanSimpanan->id)->lockForUpdate()->firstOrFail();
            if ($item->status !== 'menunggu_cs') {
                throw ValidationException::withMessages(['status' => 'Pengajuan ini sudah diproses pada tahap CS/Teller.']);
            }

            $lanjut = $data['keputusan'] === 'lanjut';
            $item->update([
                'status' => $lanjut ? 'menunggu_kabag' : 'ditolak_cs',
                'catatan_cs' => $data['catatan'] ?? null,
                'diperiksa_cs_oleh' => $request->user()->id,
                'diperiksa_cs_pada' => now(),
            ]);

            $item->refresh();
            $nominal = 'Rp' . number_format((float) $item->jumlah, 0, ',', '.');
            if ($lanjut) {
                $this->kirimKeRole(
                    'kabag',
                    'Pengajuan pencairan menunggu persetujuan',
                    $item->no_pengajuan . ' telah diperiksa CS/Teller dan menunggu keputusan Kabag. Nominal: ' . $nominal . '.',
                    route('pencairan-simpanan.kabag.show', $item, false)
                );
                $this->kirimKeAnggota($item, 'Pengajuan diteruskan ke Kabag', 'Pengajuan Anda telah lolos pemeriksaan CS/Teller dan diteruskan kepada Kabag.');
            } else {
                $this->kirimKeAnggota($item, 'Pengajuan pencairan ditolak CS/Teller', 'Pengajuan Anda ditolak pada tahap pemeriksaan CS/Teller. Catatan: ' . ($data['catatan'] ?? 'Tidak ada catatan.'));
            }
        });

        return redirect()->route('pencairan-simpanan.cs.index')
            ->with('success', 'Hasil pemeriksaan CS/Teller berhasil disimpan.');
    }

    public function keputusanKabag(Request $request, PencairanSimpanan $pencairanSimpanan): RedirectResponse
    {
        abort_unless($request->user()->role === 'kabag' && $request->user()->status === 'aktif', 403);

        $data = $request->validate([
            'keputusan' => ['required', 'in:setujui,tolak'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($pencairanSimpanan, $data, $request) {
            $item = PencairanSimpanan::whereKey($pencairanSimpanan->id)->lockForUpdate()->firstOrFail();
            if ($item->status !== 'menunggu_kabag') {
                throw ValidationException::withMessages(['status' => 'Pengajuan ini tidak sedang menunggu persetujuan Kabag.']);
            }

            $setujui = $data['keputusan'] === 'setujui';
            $item->update([
                'status' => $setujui ? 'menunggu_pencairan' : 'ditolak_kabag',
                'catatan_kabag' => $data['catatan'] ?? null,
                'disetujui_kabag_oleh' => $request->user()->id,
                'disetujui_kabag_pada' => now(),
            ]);

            $item->refresh();
            $nominal = 'Rp' . number_format((float) $item->jumlah, 0, ',', '.');

            if ($setujui) {
                $this->kirimKeRole(
                    'bendahara',
                    'Pencairan simpanan siap dibayarkan',
                    'Pengajuan ' . $item->no_pengajuan . ' telah disetujui Kabag dan menunggu pembayaran. Nominal: ' . $nominal . '.',
                    route('pencairan-simpanan.bendahara.show', $item, false)
                );
                $this->kirimKeAnggota($item, 'Pengajuan pencairan disetujui', 'Pengajuan Anda telah disetujui Kabag dan sedang menunggu proses pembayaran Bendahara.');
            } else {
                $this->kirimKeAnggota($item, 'Pengajuan pencairan ditolak Kabag', 'Pengajuan Anda ditolak oleh Kabag. Catatan: ' . ($data['catatan'] ?? 'Tidak ada catatan.'));
            }
        });

        return redirect()->route('pencairan-simpanan.kabag.index')
            ->with('success', $data['keputusan'] === 'setujui'
                ? 'Pengajuan disetujui dan Bendahara telah diberi notifikasi.'
                : 'Pengajuan pencairan ditolak.');
    }

    public function cairkan(Request $request, PencairanSimpanan $pencairanSimpanan): RedirectResponse
    {
        $bendahara = $request->user();
        abort_unless($bendahara->role === 'bendahara' && $bendahara->status === 'aktif', 403);

        $data = $request->validate([
            'tanggal_pencairan' => ['required', 'date', 'before_or_equal:today'],
            'bukti_pencairan' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $path = $request->hasFile('bukti_pencairan')
            ? $request->file('bukti_pencairan')->store('pencairan-simpanan/pembayaran', 'public')
            : null;

        try {
            DB::transaction(function () use ($pencairanSimpanan, $bendahara, $data, $path) {
                $item = PencairanSimpanan::whereKey($pencairanSimpanan->id)->lockForUpdate()->firstOrFail();
                if ($item->status !== 'menunggu_pencairan') {
                    throw ValidationException::withMessages(['status' => 'Pengajuan belum disetujui Kabag atau sudah dicairkan.']);
                }

                $anggota = Anggota::whereKey($item->anggota_id)->lockForUpdate()->firstOrFail();
                $kolom = $this->kolomSaldo($item->jenis_simpanan);
                $jumlah = (float) $item->jumlah;

                if ((float) $anggota->{$kolom} < $jumlah) {
                    throw ValidationException::withMessages(['jumlah' => 'Saldo jenis simpanan ini tidak lagi mencukupi.']);
                }

                if (Kas::where('referensi_tipe', 'pencairan_simpanan')
                    ->where('referensi_id', $item->id)->exists()) {
                    throw ValidationException::withMessages(['status' => 'Transaksi kas untuk pengajuan ini sudah tercatat.']);
                }

                // Perubahan saldo dan pencatatan kas tetap berada dalam satu transaksi database.
                $anggota->decrement($kolom, $jumlah);

                Kas::catat([
                    'tanggal' => $data['tanggal_pencairan'],
                    'jenis' => 'keluar',
                    'kategori' => 'simpanan',
                    'jumlah' => $jumlah,
                    'keterangan' => 'Pencairan ' . PencairanSimpanan::JENIS[$item->jenis_simpanan]
                        . ' ' . $item->no_pengajuan . ' - ' . $anggota->kode_anggota,
                    'referensi_tipe' => 'pencairan_simpanan',
                    'referensi_id' => $item->id,
                    'dicatat_oleh' => $bendahara->id,
                ]);

                $item->update([
                    'status' => 'selesai',
                    'tanggal_pencairan' => $data['tanggal_pencairan'],
                    'bukti_pencairan' => $path,
                    'dicairkan_oleh' => $bendahara->id,
                ]);

                $this->kirimKeAnggota(
                    $item,
                    'Pencairan simpanan selesai',
                    'Dana pencairan sebesar Rp' . number_format($jumlah, 0, ',', '.')
                        . ' telah dicatat sebagai pembayaran pada ' . $data['tanggal_pencairan'] . '.'
                );
            });
        } catch (Throwable $e) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
            throw $e;
        }

        return redirect()->route('pencairan-simpanan.bendahara.index')
            ->with('success', 'Pencairan berhasil dicatat. Saldo jenis simpanan terpilih dan kas keluar telah diperbarui.');
    }
}

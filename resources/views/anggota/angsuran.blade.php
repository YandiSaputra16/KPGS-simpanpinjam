<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 fw-bold mb-1">Angsuran Saya</h2>
            <p class="text-muted mb-0">
                Informasi jadwal dan status pembayaran angsuran pinjaman Anda.
            </p>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="container-xxl">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle mt-1"></i>
                        <div>
                            <div class="fw-semibold mb-1">Terdapat kesalahan:</div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @forelse ($pinjaman as $item)
                @php
                    $totalPinjaman = (float) (
                        $item->jumlah_disetujui
                        ?? $item->jumlah_pinjaman
                        ?? 0
                    );

                    $totalAngsuran = (float) (
                        $item->total_angsuran
                        ?? 0
                    );

                    $sisaPinjaman = (float) (
                        $item->sisa_pinjaman
                        ?? $totalAngsuran
                    );

                    $angsuranLunas = $item->angsuran
                        ->where('status_pembayaran', 'lunas')
                        ->count();

                    $totalAngsuranData = $item->angsuran->count();

                    $angsuranBerjalan = $item->angsuran
                        ->whereIn('status_pembayaran', [
                            'belum_bayar',
                            'menunggu_verifikasi',
                            'ditolak',
                        ])
                        ->count();

                    $angsuranDapatDibayar = $item->angsuran
                        ->whereIn('status_pembayaran', [
                            'belum_bayar',
                            'ditolak',
                        ]);

                    $adaMenungguVerifikasi = $item->angsuran
                        ->contains('status_pembayaran', 'menunggu_verifikasi');

                    $formPembayaranId = 'formPembayaran-' . $item->id;
                @endphp

                <div class="card border-0 shadow-sm mb-4" data-kpgs-installment-page>

                    <div class="card-header bg-white py-4 px-4">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <h3 class="h5 fw-bold mb-0">
                                        {{ $item->no_pinjaman }}
                                    </h3>

                                    @php
                                        $statusPinjaman = $item->status;

                                        $statusLabel = match ($statusPinjaman) {
                                            'disetujui' => 'Disetujui',
                                            'dicairkan' => 'Dicairkan',
                                            'lunas' => 'Lunas',
                                            'ditolak' => 'Ditolak',
                                            'menunggu_cs' => 'Menunggu CS',
                                            'menunggu_ao' => 'Menunggu AO',
                                            'menunggu_kabag' => 'Menunggu Kabag',
                                            default => ucwords(
                                                str_replace('_', ' ', $statusPinjaman)
                                            ),
                                        };

                                        $statusClass = match ($statusPinjaman) {
                                            'lunas' => 'bg-success',
                                            'dicairkan',
                                            'disetujui' => 'bg-primary',
                                            'ditolak' => 'bg-danger',
                                            default => 'bg-warning text-dark',
                                        };
                                    @endphp

                                    <span class="badge {{ $statusClass }}">
                                        @if ($statusPinjaman === 'lunas')
                                            <i class="bi bi-check-circle me-1"></i>
                                        @elseif ($statusPinjaman === 'ditolak')
                                            <i class="bi bi-x-circle me-1"></i>
                                        @elseif (in_array($statusPinjaman, ['dicairkan', 'disetujui']))
                                            <i class="bi bi-wallet2 me-1"></i>
                                        @else
                                            <i class="bi bi-hourglass-split me-1"></i>
                                        @endif
                                        {{ $statusLabel }}
                                    </span>
                                </div>

                                @if ($item->masterPinjaman)
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-box-seam me-1"></i>
                                        {{ $item->masterPinjaman->nama_paket }}
                                    </p>
                                @endif
                            </div>

                        </div>
                    </div>

                    <div class="card-body bg-light p-4">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="text-muted small mb-1">
                                            <i class="bi bi-cash-stack me-1"></i>
                                            Jumlah Pinjaman
                                        </div>
                                        <div class="fw-bold">
                                            Rp {{ number_format($totalPinjaman, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="text-muted small mb-1">
                                            <i class="bi bi-wallet2 me-1"></i>
                                            Total Kewajiban
                                        </div>
                                        <div class="fw-bold">
                                            Rp {{ number_format($totalAngsuran, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="text-muted small mb-1">
                                            <i class="bi bi-arrow-down-circle me-1"></i>
                                            Sisa Kewajiban
                                        </div>
                                        <div class="fw-bold">
                                            Rp {{ number_format($sisaPinjaman, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="text-muted small mb-1">
                                            <i class="bi bi-list-check me-1"></i>
                                            Progress Angsuran
                                        </div>
                                        <div class="fw-bold">
                                            {{ $angsuranLunas }} / {{ $totalAngsuranData }}
                                        </div>
                                        <div class="text-muted small mt-1">
                                            {{ $angsuranBerjalan }} angsuran belum selesai
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-2 mb-4">
                            <i class="bi bi-calendar3 text-primary fs-5"></i>
                            <div>
                                <h4 class="h5 fw-bold mb-1">
                                    Jadwal Angsuran
                                </h4>
                                <p class="text-muted small mb-0">
                                    Daftar pembayaran angsuran pinjaman.
                                </p>
                            </div>
                        </div>

                        @if ($item->angsuran->isNotEmpty())

                            <div class="table-responsive d-none d-md-block">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="py-3 text-center">Pilih</th>
                                            <th class="py-3">Ke-</th>
                                            <th class="py-3">Jatuh Tempo</th>
                                            <th class="py-3 text-end">Pokok</th>
                                            <th class="py-3 text-end">Bunga</th>
                                            <th class="py-3 text-end">Denda</th>
                                            <th class="py-3 text-end">Total Tagihan</th>
                                            <th class="py-3">Status</th>
                                            <th class="py-3 text-end">Aksi</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($item->angsuran as $angsuran)
                                            @php
                                                $status = $angsuran->status_pembayaran;

                                                $statusAngsuranLabel = match ($status) {
                                                    'belum_bayar' => 'Belum Dibayar',
                                                    'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                                    'lunas' => 'Lunas',
                                                    'ditolak' => 'Perlu Diperbaiki',
                                                    default => ucwords(
                                                        str_replace('_', ' ', $status)
                                                    ),
                                                };

                                                $statusAngsuranClass = match ($status) {
                                                    'lunas' => 'bg-success',
                                                    'menunggu_verifikasi' => 'bg-warning text-dark',
                                                    'ditolak' => 'bg-danger',
                                                    default => 'bg-secondary',
                                                };

                                                $tanggalJatuhTempo = $angsuran->tanggal_jatuh_tempo
                                                    ? \Carbon\Carbon::parse($angsuran->tanggal_jatuh_tempo)
                                                    : null;

                                                $totalTagihan = (float) $angsuran->total_angsuran
                                                    + (float) ($angsuran->denda ?? 0);

                                                $dapatDipilih = in_array($status, ['belum_bayar', 'ditolak'], true)
                                                    && !$adaMenungguVerifikasi;
                                            @endphp

                                            <tr>
                                                <td class="text-center">
                                                    @if ($dapatDipilih)
                                                        <input
                                                            type="checkbox"
                                                            name="display_angsuran_ids[]"
                                                            value="{{ $angsuran->id }}"
                                                            class="form-check-input angsuran-checkbox"
                                                            data-angsuran-ke="{{ $angsuran->angsuran_ke }}"
                                                            data-total="{{ $totalTagihan }}"
                                                            aria-label="Pilih angsuran ke-{{ $angsuran->angsuran_ke }}"
                                                        >
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <span class="fw-semibold">
                                                        {{ $angsuran->angsuran_ke }}
                                                    </span>
                                                </td>

                                                <td>
                                                    @if ($tanggalJatuhTempo)
                                                        {{ $tanggalJatuhTempo->translatedFormat('d M Y') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>

                                                <td class="text-end">
                                                    Rp {{ number_format((float) $angsuran->jumlah_pokok, 0, ',', '.') }}
                                                </td>

                                                <td class="text-end">
                                                    Rp {{ number_format((float) $angsuran->jumlah_bunga, 0, ',', '.') }}
                                                </td>

                                                <td class="text-end">
                                                    <span class="{{ (float) ($angsuran->denda ?? 0) > 0 ? 'text-danger fw-semibold' : '' }}">
                                                        Rp {{ number_format((float) ($angsuran->denda ?? 0), 0, ',', '.') }}
                                                    </span>
                                                </td>

                                                <td class="text-end fw-semibold">
                                                    Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                                                </td>

                                                <td>
                                                    <span class="badge {{ $statusAngsuranClass }}">
                                                        @if ($status === 'lunas')
                                                            <i class="bi bi-check-circle me-1"></i>
                                                        @elseif ($status === 'menunggu_verifikasi')
                                                            <i class="bi bi-hourglass-split me-1"></i>
                                                        @elseif ($status === 'ditolak')
                                                            <i class="bi bi-exclamation-circle me-1"></i>
                                                        @else
                                                            <i class="bi bi-circle me-1"></i>
                                                        @endif
                                                        {{ $statusAngsuranLabel }}
                                                    </span>
                                                </td>

                                                <td class="text-end">
                                                    <a
                                                        href="{{ route('angsuran.saya.transaksi', $angsuran) }}"
                                                        class="btn btn-sm btn-outline-secondary"
                                                    >
                                                        <i class="bi bi-eye me-1"></i>
                                                        Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-md-none">
                                @foreach ($item->angsuran as $angsuran)
                                    @php
                                        $status = $angsuran->status_pembayaran;

                                        $statusAngsuranLabel = match ($status) {
                                            'belum_bayar' => 'Belum Dibayar',
                                            'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                            'lunas' => 'Lunas',
                                            'ditolak' => 'Perlu Diperbaiki',
                                            default => ucwords(
                                                str_replace('_', ' ', $status)
                                            ),
                                        };

                                        $statusAngsuranClass = match ($status) {
                                            'lunas' => 'bg-success',
                                            'menunggu_verifikasi' => 'bg-warning text-dark',
                                            'ditolak' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };

                                        $tanggalJatuhTempo = $angsuran->tanggal_jatuh_tempo
                                            ? \Carbon\Carbon::parse($angsuran->tanggal_jatuh_tempo)
                                            : null;

                                        $totalTagihan = (float) $angsuran->total_angsuran
                                            + (float) ($angsuran->denda ?? 0);

                                        $dapatDipilih = in_array($status, ['belum_bayar', 'ditolak'], true)
                                            && !$adaMenungguVerifikasi;
                                    @endphp

                                    <div class="border rounded p-3 mb-3">
                                        <div class="d-flex align-items-start justify-content-between gap-3">
                                            <div>
                                                <div class="fw-semibold">
                                                    Angsuran ke-{{ $angsuran->angsuran_ke }}
                                                </div>

                                                <div class="text-muted small mt-1">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    Jatuh tempo:
                                                    @if ($tanggalJatuhTempo)
                                                        {{ $tanggalJatuhTempo->translatedFormat('d M Y') }}
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            </div>

                                            <span class="badge {{ $statusAngsuranClass }}">
                                                @if ($status === 'lunas')
                                                    <i class="bi bi-check-circle me-1"></i>
                                                @elseif ($status === 'menunggu_verifikasi')
                                                    <i class="bi bi-hourglass-split me-1"></i>
                                                @elseif ($status === 'ditolak')
                                                    <i class="bi bi-exclamation-circle me-1"></i>
                                                @else
                                                    <i class="bi bi-circle me-1"></i>
                                                @endif
                                                {{ $statusAngsuranLabel }}
                                            </span>
                                        </div>

                                        <div class="row g-3 mt-2">
                                            <div class="col-6">
                                                <div class="text-muted small">Pokok</div>
                                                <div class="fw-semibold">
                                                    Rp {{ number_format((float) $angsuran->jumlah_pokok, 0, ',', '.') }}
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="text-muted small">Bunga</div>
                                                <div class="fw-semibold">
                                                    Rp {{ number_format((float) $angsuran->jumlah_bunga, 0, ',', '.') }}
                                                </div>
                                            </div>

                                            <div class="col-6">
                                                <div class="text-muted small">Total</div>
                                                <div class="fw-bold">
                                                    Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                                                </div>
                                            </div>

                                            @if ((float) $angsuran->denda > 0)
                                                <div class="col-6">
                                                    <div class="text-muted small">Denda</div>
                                                    <div class="text-danger fw-semibold">
                                                        Rp {{ number_format((float) $angsuran->denda, 0, ',', '.') }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="border-top mt-3 pt-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                            @if ($dapatDipilih)
                                                <div class="form-check">
                                                    <input
                                                        type="checkbox"
                                                        name="display_angsuran_ids[]"
                                                        value="{{ $angsuran->id }}"
                                                        class="form-check-input angsuran-checkbox"
                                                        data-angsuran-ke="{{ $angsuran->angsuran_ke }}"
                                                        data-total="{{ $totalTagihan }}"
                                                        id="angsuran-mobile-{{ $item->id }}-{{ $angsuran->id }}"
                                                    >
                                                    <label class="form-check-label small" for="angsuran-mobile-{{ $item->id }}-{{ $angsuran->id }}">
                                                        Pilih untuk dibayar
                                                    </label>
                                                </div>
                                            @else
                                                <span></span>
                                            @endif

                                            <a
                                                href="{{ route('angsuran.saya.transaksi', $angsuran) }}"
                                                class="btn btn-sm btn-outline-secondary"
                                            >
                                                <i class="bi bi-eye me-1"></i>
                                                Detail Pembayaran
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($angsuranDapatDibayar->isNotEmpty() && !$adaMenungguVerifikasi)
                                <div class="border-top mt-4 pt-4">
                                    <div class="d-flex align-items-start gap-2 mb-3">
                                        <i class="bi bi-credit-card text-primary fs-5"></i>
                                        <div>
                                            <h4 class="h5 fw-bold mb-1">Pengajuan Pembayaran</h4>
                                            <p class="text-muted small mb-0">
                                                Pilih angsuran sesuai urutan pembayaran, kemudian unggah bukti pembayaran.
                                            </p>
                                        </div>
                                    </div>

                                    <form
                                        id="{{ $formPembayaranId }}"
                                        data-form-pembayaran
                                        method="POST"
                                        action="{{ route('angsuran.saya.bayar', $item) }}"
                                        enctype="multipart/form-data"
                                    >
                                        @csrf

                                        <div class="mb-3">
                                            <label for="bukti-pembayaran-{{ $item->id }}" class="form-label fw-semibold">
                                                Bukti Pembayaran
                                            </label>
                                            <input
                                                id="bukti-pembayaran-{{ $item->id }}"
                                                name="bukti_pembayaran"
                                                type="file"
                                                accept=".jpg,.jpeg,.png,.pdf"
                                                required
                                                class="form-control"
                                            >
                                            <div class="form-text">
                                                Format JPG, JPEG, PNG, atau PDF. Maksimal 5 MB.
                                            </div>
                                        </div>

                                        <div class="card bg-light border mb-3">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between gap-3">
                                                    <span class="text-muted">Total pembayaran dipilih</span>
                                                    <span data-total-pembayaran class="fs-5 fw-bold">Rp 0</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <button type="submit" data-btn-ajukan-pembayaran disabled class="btn btn-primary">
                                                <i class="bi bi-send me-1"></i>
                                                Ajukan Pembayaran
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @elseif ($adaMenungguVerifikasi)
                                <div class="alert alert-warning mt-4 mb-0" role="alert">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-hourglass-split mt-1"></i>
                                        <div>
                                            <div class="fw-semibold">Pembayaran sedang menunggu verifikasi petugas.</div>
                                            <div class="small">
                                                Anda dapat mengajukan pembayaran berikutnya setelah pembayaran sebelumnya selesai diverifikasi.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-success mt-4 mb-0" role="alert">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Seluruh angsuran telah selesai dibayar.
                                </div>
                            @endif

                        @else
                            <div class="alert alert-secondary mb-0" role="alert">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-calendar-x mt-1"></i>
                                    <div>
                                        <div class="fw-semibold">
                                            Belum terdapat data angsuran.
                                        </div>
                                        <div class="small">
                                            Jadwal angsuran akan tampil setelah pinjaman dicairkan.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            @empty
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5 px-4">
                        <div class="mb-3">
                            <i class="bi bi-credit-card-2-front text-muted fs-1"></i>
                        </div>

                        <h3 class="h5 fw-bold">
                            Belum Ada Pinjaman
                        </h3>

                        <p class="text-muted small mt-2 mb-0">
                            Anda belum memiliki pinjaman yang memiliki jadwal angsuran.
                        </p>

                        @if (Route::has('pinjaman.ajukan'))
                            <div class="mt-4">
                                <a
                                    href="{{ route('pinjaman.ajukan') }}"
                                    class="btn btn-primary"
                                >
                                    <i class="bi bi-plus-lg me-1"></i>
                                    Ajukan Pinjaman
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
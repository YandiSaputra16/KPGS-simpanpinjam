<x-app-layout>
    <div data-kpgs-installment-page>
    <x-slot name="header">
        <div>
            <h2 class="h4 fw-bold mb-1">Detail Angsuran</h2>
            <p class="text-muted mb-0">
                Detail pinjaman dan jadwal pembayaran angsuran Anda.
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

            @php
                $jumlahPinjaman = (float) (
                    $pinjaman->jumlah_disetujui
                    ?? $pinjaman->jumlah_pinjaman
                    ?? 0
                );

                $totalKewajiban = (float) (
                    $pinjaman->total_angsuran
                    ?? 0
                );

                $sisaKewajiban = (float) (
                    $pinjaman->sisa_pinjaman
                    ?? $totalKewajiban
                );

                $jumlahLunas = $pinjaman->angsuran
                    ->where('status_pembayaran', 'lunas')
                    ->count();

                $jumlahAngsuran = $pinjaman->angsuran->count();

                $angsuranDapatDibayar = $pinjaman->angsuran
                    ->whereIn('status_pembayaran', [
                        'belum_bayar',
                        'ditolak',
                    ]);

                $adaMenungguVerifikasi = $pinjaman->angsuran
                    ->where('status_pembayaran', 'menunggu_verifikasi')
                    ->isNotEmpty();
            @endphp

            <div class="mb-4">
                <a
                    href="{{ route('angsuran.saya.index') }}"
                    class="btn btn-outline-secondary"
                >
                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali ke Angsuran Saya
                </a>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-4 px-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <div class="text-muted small mb-1">Nomor Pinjaman</div>

                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <h3 class="h4 fw-bold mb-0">
                                    {{ $pinjaman->no_pinjaman }}
                                </h3>

                                @php
                                    $statusPinjamanLabel = match ($pinjaman->status) {
                                        'disetujui' => 'Disetujui',
                                        'dicairkan' => 'Dicairkan',
                                        'lunas' => 'Lunas',
                                        'ditolak' => 'Ditolak',
                                        'menunggu_cs' => 'Menunggu CS',
                                        'menunggu_ao' => 'Menunggu AO',
                                        'menunggu_kabag' => 'Menunggu Kabag',
                                        default => ucwords(
                                            str_replace('_', ' ', $pinjaman->status)
                                        ),
                                    };

                                    $statusPinjamanClass = match ($pinjaman->status) {
                                        'lunas' => 'bg-success',
                                        'dicairkan',
                                        'disetujui' => 'bg-primary',
                                        'ditolak' => 'bg-danger',
                                        default => 'bg-warning text-dark',
                                    };
                                @endphp

                                <span class="badge {{ $statusPinjamanClass }}">
                                    @if ($pinjaman->status === 'lunas')
                                        <i class="bi bi-check-circle me-1"></i>
                                    @elseif ($pinjaman->status === 'ditolak')
                                        <i class="bi bi-x-circle me-1"></i>
                                    @elseif (in_array($pinjaman->status, ['dicairkan', 'disetujui']))
                                        <i class="bi bi-wallet2 me-1"></i>
                                    @else
                                        <i class="bi bi-hourglass-split me-1"></i>
                                    @endif
                                    {{ $statusPinjamanLabel }}
                                </span>
                            </div>

                            @if ($pinjaman->masterPinjaman)
                                <p class="text-muted small mt-2 mb-0">
                                    <i class="bi bi-box-seam me-1"></i>
                                    {{ $pinjaman->masterPinjaman->nama_paket }}
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
                                        Rp {{ number_format($jumlahPinjaman, 0, ',', '.') }}
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
                                        Rp {{ number_format($totalKewajiban, 0, ',', '.') }}
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
                                        Rp {{ number_format($sisaKewajiban, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body">
                                    <div class="text-muted small mb-1">
                                        <i class="bi bi-list-check me-1"></i>
                                        Progress Pembayaran
                                    </div>
                                    <div class="fw-bold">
                                        {{ $jumlahLunas }} / {{ $jumlahAngsuran }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-4 px-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-calendar3 text-primary fs-5"></i>
                        <div>
                            <h3 class="h5 fw-bold mb-1">Jadwal Angsuran</h3>
                            <p class="text-muted small mb-0">
                                Pilih angsuran yang ingin Anda bayarkan sesuai urutan pembayaran.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if ($pinjaman->angsuran->isNotEmpty())

                        @if ($adaMenungguVerifikasi)
                            <div class="alert alert-warning mb-4" role="alert">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-hourglass-split mt-1"></i>
                                    <div>
                                        <div class="fw-semibold">
                                            Terdapat pembayaran yang sedang diverifikasi.
                                        </div>
                                        <div class="small">
                                            Tunggu sampai pembayaran tersebut selesai diperiksa
                                            sebelum mengajukan pembayaran berikutnya.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-3 text-center">Pilih</th>
                                        <th class="py-3">Angsuran</th>
                                        <th class="py-3">Jatuh Tempo</th>
                                        <th class="py-3 text-end">Pokok</th>
                                        <th class="py-3 text-end">Bunga</th>
                                        <th class="py-3 text-end">Denda</th>
                                        <th class="py-3 text-end">Total</th>
                                        <th class="py-3">Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($pinjaman->angsuran as $index => $angsuran)
                                        @php
                                            $status = $angsuran->status_pembayaran;

                                            $statusLabel = match ($status) {
                                                'belum_bayar' => 'Belum Dibayar',
                                                'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                                'lunas' => 'Lunas',
                                                'ditolak' => 'Perlu Diperbaiki',
                                                default => ucwords(
                                                    str_replace('_', ' ', $status)
                                                ),
                                            };

                                            $statusClass = match ($status) {
                                                'lunas' => 'bg-success',
                                                'menunggu_verifikasi' => 'bg-warning text-dark',
                                                'ditolak' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };

                                            $dapatDipilih = in_array(
                                                $status,
                                                [
                                                    'belum_bayar',
                                                    'ditolak',
                                                ],
                                                true
                                            );

                                            $tanggalJatuhTempo = $angsuran->tanggal_jatuh_tempo
                                                ? \Carbon\Carbon::parse($angsuran->tanggal_jatuh_tempo)
                                                : null;

                                            $totalTagihan =
                                                (float) $angsuran->total_angsuran
                                                + (float) ($angsuran->denda ?? 0);
                                        @endphp

                                        <tr class="{{ $index >= 5 ? 'angsuran-extra d-none' : '' }}">
                                            <td class="text-center">
                                                @if ($dapatDipilih && !$adaMenungguVerifikasi)
                                                    <input
                                                        type="checkbox"
                                                        name="display_angsuran_ids[]"
                                                        value="{{ $angsuran->id }}"
                                                        form="formPembayaran"
                                                        class="form-check-input angsuran-checkbox"
                                                        data-total="{{ $totalTagihan }}"
                                                    >
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="fw-semibold">
                                                    Ke-{{ $angsuran->angsuran_ke }}
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
                                                <span class="{{ (float) $angsuran->denda > 0 ? 'text-danger fw-semibold' : '' }}">
                                                    Rp {{ number_format((float) ($angsuran->denda ?? 0), 0, ',', '.') }}
                                                </span>
                                            </td>

                                            <td class="text-end fw-semibold">
                                                Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                                            </td>

                                            <td>
                                                <span class="badge {{ $statusClass }}">
                                                    @if ($status === 'lunas')
                                                        <i class="bi bi-check-circle me-1"></i>
                                                    @elseif ($status === 'menunggu_verifikasi')
                                                        <i class="bi bi-hourglass-split me-1"></i>
                                                    @elseif ($status === 'ditolak')
                                                        <i class="bi bi-exclamation-circle me-1"></i>
                                                    @else
                                                        <i class="bi bi-circle me-1"></i>
                                                    @endif
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if ($pinjaman->angsuran->count() > 5)
                                        <tr>
                                            <td colspan="8" class="text-center py-3">
                                                <button
                                                    type="button"
                                                    id="toggleAngsuran"
                                                    class="btn btn-outline-secondary btn-sm"
                                                >
                                                    <i class="bi bi-chevron-down me-1"></i>
                                                    Lihat Lainnya
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="d-md-none">
                            @foreach ($pinjaman->angsuran as $index => $angsuran)
                                @php
                                    $status = $angsuran->status_pembayaran;

                                    $statusLabel = match ($status) {
                                        'belum_bayar' => 'Belum Dibayar',
                                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                        'lunas' => 'Lunas',
                                        'ditolak' => 'Perlu Diperbaiki',
                                        default => ucwords(
                                            str_replace('_', ' ', $status)
                                        ),
                                    };

                                    $statusClass = match ($status) {
                                        'lunas' => 'bg-success',
                                        'menunggu_verifikasi' => 'bg-warning text-dark',
                                        'ditolak' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };

                                    $dapatDipilih = in_array(
                                        $status,
                                        [
                                            'belum_bayar',
                                            'ditolak',
                                        ],
                                        true
                                    );

                                    $tanggalJatuhTempo = $angsuran->tanggal_jatuh_tempo
                                        ? \Carbon\Carbon::parse($angsuran->tanggal_jatuh_tempo)
                                        : null;

                                    $totalTagihan =
                                        (float) $angsuran->total_angsuran
                                        + (float) ($angsuran->denda ?? 0);
                                @endphp

                                <div class="angsuran-mobile-item border rounded p-3 mb-3 {{ $index >= 5 ? 'd-none' : '' }}">
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

                                        <span class="badge {{ $statusClass }}">
                                            @if ($status === 'lunas')
                                                <i class="bi bi-check-circle me-1"></i>
                                            @elseif ($status === 'menunggu_verifikasi')
                                                <i class="bi bi-hourglass-split me-1"></i>
                                            @elseif ($status === 'ditolak')
                                                <i class="bi bi-exclamation-circle me-1"></i>
                                            @else
                                                <i class="bi bi-circle me-1"></i>
                                            @endif
                                            {{ $statusLabel }}
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
                                            <div class="text-muted small">Denda</div>
                                            <div class="{{ (float) ($angsuran->denda ?? 0) > 0 ? 'text-danger fw-semibold' : 'fw-semibold' }}">
                                                Rp {{ number_format((float) ($angsuran->denda ?? 0), 0, ',', '.') }}
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="text-muted small">Total Tagihan</div>
                                            <div class="fw-bold">
                                                Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>

                                    @if ($dapatDipilih && !$adaMenungguVerifikasi)
                                        <div class="border-top mt-3 pt-3">
                                            <div class="form-check">
                                                <input
                                                    type="checkbox"
                                                    name="display_angsuran_ids[]"
                                                    value="{{ $angsuran->id }}"
                                                    form="formPembayaran"
                                                    class="form-check-input angsuran-checkbox"
                                                    data-total="{{ $totalTagihan }}"
                                                    id="angsuran_mobile_{{ $angsuran->id }}"
                                                >

                                                <label
                                                    for="angsuran_mobile_{{ $angsuran->id }}"
                                                    class="form-check-label"
                                                >
                                                    Pilih untuk dibayar
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            @if ($pinjaman->angsuran->count() > 5)
                                <div class="text-center pt-1">
                                    <button
                                        type="button"
                                        id="toggleAngsuranMobile"
                                        class="btn btn-outline-secondary btn-sm"
                                    >
                                        <i class="bi bi-chevron-down me-1"></i>
                                        Lihat Lainnya
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if ($angsuranDapatDibayar->isNotEmpty() && !$adaMenungguVerifikasi)
                            <div class="border-top mt-4 pt-4">
                                <div class="d-flex align-items-start gap-2 mb-4">
                                    <i class="bi bi-credit-card text-primary fs-5"></i>
                                    <div>
                                        <h4 class="h5 fw-bold mb-1">
                                            Pengajuan Pembayaran
                                        </h4>
                                        <p class="text-muted small mb-0">
                                            Pilih satu atau beberapa angsuran sesuai urutan pembayaran,
                                            kemudian unggah bukti pembayaran.
                                        </p>
                                    </div>
                                </div>

                                <form
                                    id="formPembayaran"
                                    method="POST"
                                    action="{{ route('angsuran.saya.bayar', $pinjaman) }}"
                                    enctype="multipart/form-data"
                                >
                                    @csrf

                                    <div class="mb-4">
                                        <label
                                            for="bukti_pembayaran"
                                            class="form-label fw-semibold"
                                        >
                                            Bukti Pembayaran
                                        </label>

                                        <input
                                            id="bukti_pembayaran"
                                            name="bukti_pembayaran"
                                            type="file"
                                            accept=".jpg,.jpeg,.png,.pdf"
                                            required
                                            class="form-control"
                                        >

                                        <div class="form-text">
                                            Format: JPG, JPEG, PNG, atau PDF. Maksimal 5 MB.
                                        </div>
                                    </div>

                                    <div class="card bg-light border mb-4">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between gap-3">
                                                <span class="text-muted">
                                                    Total pembayaran dipilih
                                                </span>

                                                <span
                                                    id="totalPembayaran"
                                                    class="fs-5 fw-bold"
                                                >
                                                    Rp 0
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button
                                            type="submit"
                                            id="btnAjukanPembayaran"
                                            disabled
                                            class="btn btn-primary"
                                        >
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
                                        <div class="fw-semibold">
                                            Pembayaran sedang menunggu verifikasi petugas.
                                        </div>
                                        <div class="small">
                                            Anda dapat melakukan pembayaran berikutnya setelah
                                            pembayaran sebelumnya selesai diverifikasi.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-success mt-4 mb-0" role="alert">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-check-circle mt-1"></i>
                                    <div>
                                        <div class="fw-semibold">
                                            Seluruh angsuran telah selesai dibayar.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @else
                        <div class="alert alert-secondary mb-0" role="alert">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-calendar-x mt-1"></i>
                                <div>
                                    <div class="fw-semibold">
                                        Belum terdapat jadwal angsuran.
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>


</x-app-layout>
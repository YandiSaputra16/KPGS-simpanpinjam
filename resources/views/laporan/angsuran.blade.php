<x-app-layout>
<div class="container-fluid py-4 kpgs-report-page">

    <div class="mb-4">
        <h4 class="fw-bold mb-1">Laporan Angsuran</h4>
        <p class="text-muted mb-0">
            Rekap pembayaran angsuran pinjaman anggota KPGS.
        </p>
    </div>
        <div class="kpgs-report-actions d-flex flex-wrap gap-2 mb-4" data-no-print><button type="button" class="btn btn-primary" data-report-print><i class="bi bi-printer me-1"></i> Cetak Laporan</button> <a href="{{ url()->current() }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter</a></div>

    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">
                        Total Angsuran
                    </div>

                    <div class="fs-4 fw-bold">
                        {{ number_format($totalAngsuran, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">
                        Sudah Lunas
                    </div>

                    <div class="fs-4 fw-bold text-success">
                        {{ number_format($angsuranLunas, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">
                        Belum Bayar
                    </div>

                    <div class="fs-4 fw-bold text-warning">
                        {{ number_format($angsuranBelumBayar, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">
                        Menunggu Verifikasi
                    </div>

                    <div class="fs-4 fw-bold text-primary">
                        {{ number_format($menungguVerifikasi, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">
                        Total Tagihan
                    </div>

                    <div class="fs-4 fw-bold">
                        Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Total pembayaran --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="text-muted small mb-1">
                Total Pembayaran Terverifikasi
            </div>

            <div class="fs-3 fw-bold text-success">
                Rp {{ number_format($totalDibayar, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">
                Filter Laporan
            </h6>
        </div>

        <div class="card-body">

            <form method="GET" action="{{ route('laporan.angsuran') }}">

                <div class="row g-3">

                    <div class="col-12 col-md-4">
                        <label for="search" class="form-label">
                            Pencarian
                        </label>

                        <input
                            type="text"
                            id="search"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="No. pinjaman, nama anggota, atau kode"
                        >
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="status" class="form-label">
                            Status Pembayaran
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="form-select"
                        >
                            <option value="">
                                Semua Status
                            </option>

                            <option
                                value="belum_bayar"
                                @selected(request('status') === 'belum_bayar')
                            >
                                Belum Bayar
                            </option>

                            <option
                                value="menunggu_verifikasi"
                                @selected(request('status') === 'menunggu_verifikasi')
                            >
                                Menunggu Verifikasi
                            </option>

                            <option
                                value="lunas"
                                @selected(request('status') === 'lunas')
                            >
                                Lunas
                            </option>

                            <option
                                value="ditolak"
                                @selected(request('status') === 'ditolak')
                            >
                                Ditolak
                            </option>
                        </select>
                    </div>

                    <div class="col-12 col-md-2">
                        <label for="dari" class="form-label">
                            Jatuh Tempo Dari
                        </label>

                        <input
                            type="date"
                            id="dari"
                            name="dari"
                            class="form-control"
                            value="{{ request('dari') }}"
                        >
                    </div>

                    <div class="col-12 col-md-2">
                        <label for="sampai" class="form-label">
                            Jatuh Tempo Sampai
                        </label>

                        <input
                            type="date"
                            id="sampai"
                            name="sampai"
                            class="form-control"
                            value="{{ request('sampai') }}"
                        >
                    </div>

                    <div class="col-12 col-md-1 d-flex align-items-end">
                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Cari
                        </button>
                    </div>

                </div>

                <div class="mt-3">
                    <a
                        href="{{ route('laporan.angsuran') }}"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        Reset Filter
                    </a>
                </div>

            </form>

        </div>

    </div>

    {{-- Tabel --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <h6 class="fw-bold mb-0">
                    Data Angsuran
                </h6>

                <span class="text-muted small">
                    {{ $angsuran->total() }} data
                </span>

            </div>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th class="px-3">No</th>
                            <th>No. Pinjaman</th>
                            <th>Anggota</th>
                            <th>Angsuran Ke</th>
                            <th>Jatuh Tempo</th>
                            <th>Pokok</th>
                            <th>Bunga</th>
                            <th>Denda</th>
                            <th>Total Tagihan</th>
                            <th>Dibayar</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($angsuran as $item)

                            @php
                                $statusLabel = match ($item->status_pembayaran) {
                                    'belum_bayar' => 'Belum Bayar',
                                    'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                    'lunas' => 'Lunas',
                                    'ditolak' => 'Ditolak',
                                    default => ucfirst(
                                        str_replace(
                                            '_',
                                            ' ',
                                            $item->status_pembayaran ?? '-'
                                        )
                                    ),
                                };

                                $statusClass = match ($item->status_pembayaran) {
                                    'lunas' => 'bg-success',
                                    'menunggu_verifikasi' => 'bg-primary',
                                    'ditolak' => 'bg-danger',
                                    'belum_bayar' => 'bg-warning text-dark',
                                    default => 'bg-secondary',
                                };
                            @endphp

                            <tr>

                                <td class="px-3">
                                    {{ $loop->iteration + ($angsuran->currentPage() - 1) * $angsuran->perPage() }}
                                </td>

                                <td>
                                    {{ $item->pinjaman?->no_pinjaman ?? '-' }}
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $item->pinjaman?->anggota?->user?->nama ?? '-' }}
                                    </div>

                                    <div class="small text-muted">
                                        {{ $item->pinjaman?->anggota?->kode_anggota ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    {{ $item->angsuran_ke }}
                                </td>

                                <td>
                                    {{ $item->tanggal_jatuh_tempo
                                        ? $item->tanggal_jatuh_tempo->format('d/m/Y')
                                        : '-'
                                    }}
                                </td>

                                <td>
                                    Rp {{ number_format($item->jumlah_pokok, 0, ',', '.') }}
                                </td>

                                <td>
                                    Rp {{ number_format($item->jumlah_bunga, 0, ',', '.') }}
                                </td>

                                <td>
                                    @if ((float) $item->denda > 0)
                                        <span class="text-danger">
                                            Rp {{ number_format($item->denda, 0, ',', '.') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="fw-semibold">
                                    Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                                </td>

                                <td>
                                    @if ($item->jumlah_bayar !== null)
                                        Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <span class="badge {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="11"
                                    class="text-center py-5 text-muted"
                                >
                                    Tidak ada data angsuran yang ditemukan.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if ($angsuran->hasPages())
            <div class="card-footer bg-white">
                {{ $angsuran->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </div>

</div>
</x-app-layout>
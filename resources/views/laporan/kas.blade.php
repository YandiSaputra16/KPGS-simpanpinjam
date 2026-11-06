<x-app-layout>
<div class="container-fluid py-4 kpgs-report-page">

    <div class="mb-4">
        <h4 class="fw-bold mb-1">Laporan Kas</h4>
        <p class="text-muted mb-0">
            Rekap arus kas masuk dan kas keluar KPGS.
        </p>
    </div>
        <div class="kpgs-report-actions d-flex flex-wrap gap-2 mb-4" data-no-print><button type="button" class="btn btn-primary" data-report-print><i class="bi bi-printer me-1"></i> Cetak Laporan</button> <a href="{{ url()->current() }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter</a></div>

    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="text-muted small mb-1">
                        Total Kas Masuk
                    </div>

                    <div class="fs-3 fw-bold text-success">
                        Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="text-muted small mb-1">
                        Total Kas Keluar
                    </div>

                    <div class="fs-3 fw-bold text-danger">
                        Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="text-muted small mb-1">
                        Saldo Kas Terkini
                    </div>

                    <div class="fs-3 fw-bold">
                        Rp {{ number_format($saldoTerkini, 0, ',', '.') }}
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- Saldo periode --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">

                <div>
                    <div class="text-muted small mb-1">
                        Selisih Kas pada Periode Filter
                    </div>

                    <div class="fs-4 fw-bold">
                        Rp {{ number_format($saldoPeriode, 0, ',', '.') }}
                    </div>
                </div>

                <div class="text-muted small align-self-md-center">
                    Selisih dihitung dari total kas masuk dikurangi total kas keluar
                    berdasarkan filter laporan.
                </div>

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

            <form method="GET" action="{{ route('laporan.kas') }}">

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
                            placeholder="No. transaksi atau keterangan"
                        >

                    </div>

                    <div class="col-12 col-md-2">

                        <label for="jenis" class="form-label">
                            Jenis
                        </label>

                        <select
                            id="jenis"
                            name="jenis"
                            class="form-select"
                        >
                            <option value="">
                                Semua
                            </option>

                            <option
                                value="masuk"
                                @selected(request('jenis') === 'masuk')
                            >
                                Kas Masuk
                            </option>

                            <option
                                value="keluar"
                                @selected(request('jenis') === 'keluar')
                            >
                                Kas Keluar
                            </option>
                        </select>

                    </div>

                    <div class="col-12 col-md-2">

                        <label for="kategori" class="form-label">
                            Kategori
                        </label>

                        <select
                            id="kategori"
                            name="kategori"
                            class="form-select"
                        >

                            <option value="">
                                Semua Kategori
                            </option>

                            @foreach ($kategoriTersedia as $value => $label)

                                <option
                                    value="{{ $value }}"
                                    @selected(request('kategori') === $value)
                                >
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-12 col-md-2">

                        <label for="dari" class="form-label">
                            Dari
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
                            Sampai
                        </label>

                        <input
                            type="date"
                            id="sampai"
                            name="sampai"
                            class="form-control"
                            value="{{ request('sampai') }}"
                        >

                    </div>

                </div>

                <div class="mt-3 d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Terapkan Filter
                    </button>

                    <a
                        href="{{ route('laporan.kas') }}"
                        class="btn btn-outline-secondary"
                    >
                        Reset
                    </a>

                </div>

            </form>

        </div>

    </div>

    {{-- Buku Kas --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <h6 class="fw-bold mb-0">
                    Buku Kas
                </h6>

                <span class="text-muted small">
                    {{ $kas->total() }} transaksi
                </span>

            </div>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th class="px-3">No</th>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Kas Masuk</th>
                            <th>Kas Keluar</th>
                            <th>Saldo</th>
                            <th>Pencatat</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($kas as $item)

                            @php
                                $kategoriLabel = $kategoriTersedia[$item->kategori]
                                    ?? ucfirst($item->kategori ?? '-');
                            @endphp

                            <tr>

                                <td class="px-3">
                                    {{ $loop->iteration + ($kas->currentPage() - 1) * $kas->perPage() }}
                                </td>

                                <td>
                                    {{ $item->no_transaksi }}
                                </td>

                                <td>
                                    {{ $item->tanggal
                                        ? $item->tanggal->format('d/m/Y')
                                        : '-'
                                    }}
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $kategoriLabel }}
                                    </span>
                                </td>

                                <td>
                                    {{ $item->keterangan ?? '-' }}
                                </td>

                                <td>
                                    @if ($item->jenis === 'masuk')
                                        <span class="text-success fw-semibold">
                                            Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if ($item->jenis === 'keluar')
                                        <span class="text-danger fw-semibold">
                                            Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="fw-semibold">
                                    Rp {{ number_format($item->saldo_sesudah, 0, ',', '.') }}
                                </td>

                                <td>
                                    {{ $item->pencatat?->nama ?? '-' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="9"
                                    class="text-center py-5 text-muted"
                                >
                                    Belum ada transaksi kas yang ditemukan.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if ($kas->hasPages())
            <div class="card-footer bg-white">
                {{ $kas->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </div>

</div>
</x-app-layout>
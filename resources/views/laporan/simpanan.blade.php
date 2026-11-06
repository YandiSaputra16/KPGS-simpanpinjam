
<x-app-layout>
    <div class="container-fluid py-4 kpgs-report-page">

        {{-- Header --}}
        <div class="mb-4 p-4 bg-white border-start border-primary rounded shadow-sm">
            <h4 class="fw-bold mb-1">Laporan Simpanan</h4>
            <p class="text-muted mb-0">
                Riwayat setoran, pencairan, dan saldo simpanan anggota KPGS.
            </p>
        </div>

        {{-- Tombol --}}
        <div class="d-flex flex-wrap gap-2 mb-4" data-no-print>
            <button type="button" class="btn btn-primary" data-report-print>
                <i class="bi bi-printer me-1"></i> Cetak Laporan
            </button>

            <a
                href="{{ route('laporan.simpanan.export', request()->query()) }}"
                class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>
                Export Excel
            </a>

            <a href="{{ route('laporan.simpanan') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
            </a>
        </div>

        {{-- Ringkasan --}}
        <div class="row g-3 mb-4">

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-muted mb-2">Total Setoran</div>
                        <div class="fs-4 fw-bold text-success">
                            Rp {{ number_format($totalSetoran, 0, ',', '.') }}
                        </div>
                        <div class="small text-muted mt-2">
                            Setoran yang disetujui
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-muted mb-2">Total Pencairan</div>
                        <div class="fs-4 fw-bold text-danger">
                            Rp {{ number_format($totalPencairan, 0, ',', '.') }}
                        </div>
                        <div class="small text-muted mt-2">
                            Pencairan yang selesai
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-muted mb-2">Saldo Bersih Transaksi</div>
                        <div class="fs-4 fw-bold {{ $totalSimpanan < 0 ? 'text-danger' : 'text-primary' }}">
                            Rp {{ number_format($totalSimpanan, 0, ',', '.') }}
                        </div>
                        <div class="small text-muted mt-2">
                            Setoran dikurangi pencairan sesuai filter
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-muted mb-2">Jumlah Transaksi</div>
                        <div class="fs-4 fw-bold">
                            {{ number_format($jumlahTransaksi, 0, ',', '.') }}
                        </div>
                        <div class="small text-muted mt-2">
                            Setoran dan pencairan
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Filter --}}
        <div class="card border-0 shadow-sm mb-4" data-no-print>
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0">Filter Laporan</h6>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('laporan.simpanan') }}">
                    <div class="row g-3">

                        <div class="col-12 col-md-4">
                            <label for="search" class="form-label">Pencarian</label>
                            <input
                                type="text"
                                id="search"
                                name="search"
                                class="form-control"
                                value="{{ request('search') }}"
                                placeholder="Nomor transaksi, kode anggota, atau nama"
                            >
                        </div>

                        <div class="col-12 col-md-3">
                            <label for="jenis_simpanan_id" class="form-label">
                                Jenis Simpanan
                            </label>

                            <select
                                id="jenis_simpanan_id"
                                name="jenis_simpanan_id"
                                class="form-select"
                            >
                                <option value="">Semua Jenis</option>

                                @foreach ($jenisSimpanan as $jenis)
                                    <option
                                        value="{{ $jenis->id }}"
                                        @selected((string) request('jenis_simpanan_id') === (string) $jenis->id)
                                    >
                                        {{ $jenis->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-2">
                            <label for="dari" class="form-label">Dari</label>
                            <input
                                type="date"
                                id="dari"
                                name="dari"
                                class="form-control"
                                value="{{ request('dari') }}"
                            >
                        </div>

                        <div class="col-12 col-md-2">
                            <label for="sampai" class="form-label">Sampai</label>
                            <input
                                type="date"
                                id="sampai"
                                name="sampai"
                                class="form-control"
                                value="{{ request('sampai') }}"
                            >
                        </div>

                        <div class="col-12 col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                Cari
                            </button>
                        </div>

                    </div>

                    <div class="mt-3">
                        <a href="{{ route('laporan.simpanan') }}"
                           class="btn btn-sm btn-outline-secondary">
                            Reset Filter
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel transaksi --}}
        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h6 class="fw-bold mb-1">Riwayat Transaksi Simpanan</h6>
                        <div class="small text-muted">
                            Setiap pencairan ditampilkan sebagai transaksi keluar.
                        </div>
                    </div>

                    <span class="text-muted small">
                        {{ number_format($simpanan->total(), 0, ',', '.') }} data
                    </span>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="px-3">No.</th>
                                <th>No. Transaksi</th>
                                <th>Anggota</th>
                                <th>Jenis Simpanan</th>
                                <th>Arah</th>
                                <th>Tanggal</th>
                                <th class="text-end">Nominal</th>
                                <th class="text-end">Saldo Berjalan</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($simpanan as $item)
                                <tr>
                                    <td class="px-3">
                                        {{ $loop->iteration + (($simpanan->currentPage() - 1) * $simpanan->perPage()) }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $item['no_transaksi'] }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $item['arah'] === 'masuk' ? 'Transaksi setoran' : 'Transaksi pencairan' }}
                                        </div>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $item['nama_anggota'] }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $item['kode_anggota'] }}
                                        </div>
                                    </td>

                                    <td>{{ $item['jenis'] }}</td>

                                    <td>
                                        @if ($item['arah'] === 'masuk')
                                            <span class="badge bg-success">
                                                <i class="bi bi-arrow-down-left me-1"></i> Masuk
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-arrow-up-right me-1"></i> Keluar
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $item['tanggal']
                                            ? \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y')
                                            : '-' }}
                                    </td>

                                    <td class="text-end fw-semibold {{ $item['arah'] === 'masuk' ? 'text-success' : 'text-danger' }}">
                                        {{ $item['arah'] === 'masuk' ? '+' : '-' }}Rp
                                        {{ number_format($item['jumlah'], 0, ',', '.') }}
                                    </td>

                                    <td class="text-end fw-semibold">
                                        Rp {{ number_format($item['saldo_berjalan'], 0, ',', '.') }}
                                    </td>

                                    <td>
                                        @if ($item['status'] === 'disetujui')
                                            <span class="badge bg-success">Disetujui</span>
                                        @elseif ($item['status'] === 'selesai')
                                            <span class="badge bg-primary">Selesai</span>
                                        @else
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($item['status']) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        Tidak ada transaksi simpanan yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>

            @if ($simpanan->hasPages())
                <div class="card-footer bg-white">
                    {{ $simpanan->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

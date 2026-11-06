<x-app-layout>
<div class="container-fluid py-4 kpgs-report-page">

    <div class="mb-4 p-4 bg-white border-start border-primary" >
            <h4 class="fw-bold mb-1">Laporan Anggota</h4>
            <p class="text-muted mb-0">
                Riwayat anggota KPGS.
            </p>
        </div>

        {{-- Tombol --}}
        <div class="d-flex flex-wrap gap-2 mb-4" data-no-print>
            <button type="button" class="btn btn-primary" data-report-print>
                <i class="bi bi-printer me-1"></i> Cetak Laporan
            </button>

            <a
                href="{{ route('laporan.anggota.export', request()->query()) }}"
                class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i>
                Export Excel
            </a>

            <a href="{{ route('laporan.anggota') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
            </a>
        </div>

    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Total Anggota</div>
                    <div class="fs-3 fw-bold">
                        {{ number_format($totalAnggota, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Anggota Aktif</div>
                    <div class="fs-3 fw-bold text-success">
                        {{ number_format($anggotaAktif, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Anggota Nonaktif</div>
                    <div class="fs-3 fw-bold text-secondary">
                        {{ number_format($anggotaNonaktif, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0">Filter Laporan</h6>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('laporan.anggota') }}">
                <div class="row g-3">

                    <div class="col-12 col-md-5">
                        <label for="search" class="form-label">
                            Pencarian
                        </label>
                        <input
                            type="text"
                            id="search"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Nama, kode anggota, NIK, atau nomor HP"
                        >
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="status" class="form-label">
                            Status
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="form-select"
                        >
                            <option value="">Semua Status</option>
                            <option
                                value="aktif"
                                @selected(request('status') === 'aktif')
                            >
                                Aktif
                            </option>
                            <option
                                value="nonaktif"
                                @selected(request('status') === 'nonaktif')
                            >
                                Nonaktif
                            </option>
                        </select>
                    </div>

                    <div class="col-12 col-md-2">
                        <label for="tahun" class="form-label">
                            Tahun Daftar
                        </label>
                        <select
                            id="tahun"
                            name="tahun"
                            class="form-select"
                        >
                            <option value="">Semua Tahun</option>

                            @foreach ($tahunTersedia as $tahun)
                                <option
                                    value="{{ $tahun }}"
                                    @selected((string) request('tahun') === (string) $tahun)
                                >
                                    {{ $tahun }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ route('laporan.anggota') }}"
                            class="btn btn-outline-secondary"
                        >
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Data Anggota</h6>

                <span class="text-muted small">
                    {{ $anggota->total() }} data
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">No</th>
                            <th>Kode Anggota</th>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>No. HP</th>
                            <th>Tanggal Daftar</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($anggota as $item)
                            <tr>
                                <td class="px-3">
                                    {{ $loop->iteration + ($anggota->currentPage() - 1) * $anggota->perPage() }}
                                </td>

                                <td>
                                    {{ $item->kode_anggota }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $item->user?->nama ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->nik ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->no_hp ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->tanggal_daftar
                                        ? \Carbon\Carbon::parse($item->tanggal_daftar)->format('d/m/Y')
                                        : '-'
                                    }}
                                </td>

                                <td>
                                    @if ($item->status_keanggotaan === 'aktif')
                                        <span class="badge bg-success">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="7"
                                    class="text-center py-5 text-muted"
                                >
                                    Tidak ada data anggota yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

                @if ($anggota->hasPages())
            <div class="card-footer bg-white">
                {{ $anggota->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
</x-app-layout>

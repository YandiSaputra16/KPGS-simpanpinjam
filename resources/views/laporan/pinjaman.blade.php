<x-app-layout>
<div class="container-fluid py-4 kpgs-report-page">

    <div class="mb-4 p-4 bg-white border-start border-primary" >
        <h4 class="fw-bold mb-1">Laporan Pinjaman</h4>
        <p class="text-muted mb-0">
            Rekap pengajuan dan realisasi pinjaman anggota KPGS.
        </p>
    </div>
        <div class="kpgs-report-actions d-flex flex-wrap gap-2 mb-4" data-no-print>
            <button type="button" class="btn btn-primary" data-report-print>
                <i class="bi bi-printer me-1">
                </i> Cetak Laporan</button> 
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter</a>
        <a
    href="{{ route('laporan.pinjaman.export', request()->query()) }}"
    class="btn btn-success">
    <i class="bi bi-file-earmark-excel me-1"></i>
    Export Excel
        </a>
    </div>

    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Total Pengajuan
                    </div>
                    <div class="fs-4 fw-bold">
                        {{ number_format($totalPengajuan, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Disetujui
                    </div>
                    <div class="fs-4 fw-bold text-success">
                        {{ number_format($totalDisetujui, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Dicairkan
                    </div>
                    <div class="fs-4 fw-bold text-primary">
                        {{ number_format($totalDicairkan, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Lunas
                    </div>
                    <div class="fs-4 fw-bold text-info">
                        {{ number_format($totalLunas, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">
                        Ditolak
                    </div>
                    <div class="fs-4 fw-bold text-danger">
                        {{ number_format($totalDitolak, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Nilai --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small mb-1">
                        Nilai Pinjaman Dicairkan
                    </div>

                    <div class="fs-4 fw-bold">
                        Rp {{ number_format($nilaiDicairkan, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small mb-1">
                        Nilai Pinjaman Aktif
                    </div>

                    <div class="fs-4 fw-bold">
                        Rp {{ number_format($nilaiAktif, 0, ',', '.') }}
                    </div>
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

            <form method="GET" action="{{ route('laporan.pinjaman') }}">

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
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="form-select"
                        >
                            <option value="">
                                Semua Status
                            </option>

                            <option value="menunggu_cs" @selected(request('status') === 'menunggu_cs')>
                                Menunggu CS
                            </option>

                            <option value="menunggu_ao" @selected(request('status') === 'menunggu_ao')>
                                Menunggu AO
                            </option>

                            <option value="menunggu_kabag" @selected(request('status') === 'menunggu_kabag')>
                                Menunggu Kabag
                            </option>

                            <option value="disetujui" @selected(request('status') === 'disetujui')>
                                Disetujui
                            </option>

                            <option value="dicairkan" @selected(request('status') === 'dicairkan')>
                                Dicairkan
                            </option>

                            <option value="lunas" @selected(request('status') === 'lunas')>
                                Lunas
                            </option>

                            <option value="ditolak" @selected(request('status') === 'ditolak')>
                                Ditolak
                            </option>
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
                        href="{{ route('laporan.pinjaman') }}"
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
                    Data Pinjaman
                </h6>

                <span class="text-muted small">
                    {{ $pinjaman->total() }} data
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
                            <th>Tanggal Pengajuan</th>
                            <th>Jumlah Pengajuan</th>
                            <th>Jumlah Disetujui</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($pinjaman as $item)

                            <tr>

                                <td class="px-3">
                                    {{ $loop->iteration + ($pinjaman->currentPage() - 1) * $pinjaman->perPage() }}
                                </td>

                                <td>
                                    {{ $item->no_pinjaman }}
                                </td>

                                <td>

                                    <div class="fw-semibold">
                                        {{ $item->anggota?->user?->nama ?? '-' }}
                                    </div>

                                    <div class="small text-muted">
                                        {{ $item->anggota?->kode_anggota ?? '-' }}
                                    </div>

                                </td>

                                <td>
                                    {{ $item->tanggal_pengajuan
                                        ? \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y')
                                        : '-'
                                    }}
                                </td>

                                <td>
                                    Rp {{ number_format($item->jumlah_pinjaman, 0, ',', '.') }}
                                </td>

                                <td>
                                    @if ($item->jumlah_disetujui !== null)
                                        Rp {{ number_format($item->jumlah_disetujui, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>

                                    @php
                                        $statusLabel = match ($item->status) {
                                            'menunggu_cs' => 'Menunggu CS',
                                            'menunggu_ao' => 'Menunggu AO',
                                            'menunggu_kabag' => 'Menunggu Kabag',
                                            'disetujui' => 'Disetujui',
                                            'dicairkan' => 'Dicairkan',
                                            'lunas' => 'Lunas',
                                            'ditolak' => 'Ditolak',
                                            default => ucfirst(str_replace('_', ' ', $item->status ?? '-')),
                                        };

                                        $statusClass = match ($item->status) {
                                            'disetujui',
                                            'lunas' => 'bg-success',

                                            'dicairkan' => 'bg-primary',

                                            'ditolak' => 'bg-danger',

                                            'menunggu_cs',
                                            'menunggu_ao',
                                            'menunggu_kabag' => 'bg-warning text-dark',

                                            default => 'bg-secondary',
                                        };
                                    @endphp

                                    <span class="badge {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="7"
                                    class="text-center py-5 text-muted"
                                >
                                    Tidak ada data pinjaman yang ditemukan.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if ($pinjaman->hasPages())
            <div class="card-footer bg-white">
                {{ $pinjaman->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </div>

</div>
</x-app-layout>
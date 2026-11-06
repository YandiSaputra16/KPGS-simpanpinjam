
<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 fw-bold mb-1">Laporan Keuangan</h2>
            <p class="text-muted mb-0">
                Ringkasan kondisi keuangan Koperasi Peternak Garut Selatan.
            </p>
        </div>
    </x-slot>

    @php
        $rolePengguna = auth()->user()->role ?? '';

        $formatRupiah = fn ($nilai) =>
            'Rp ' . number_format((float) $nilai, 0, ',', '.');

        $formatTanggal = fn ($tanggal) =>
            $tanggal
                ? \Illuminate\Support\Carbon::parse($tanggal)->format('d/m/Y')
                : '-';

        $statusAngsuran = [
            'lunas' => 'Lunas',
            'belum_bayar' => 'Belum Dibayar',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'ditolak' => 'Ditolak',
        ];

        $statusPinjaman = [
            'menunggu_cs' => 'Menunggu CS',
            'menunggu_ao' => 'Menunggu AO',
            'menunggu_kabag' => 'Menunggu Kabag',
            'disetujui' => 'Disetujui',
            'dicairkan' => 'Dicairkan',
            'lunas' => 'Lunas',
            'ditolak' => 'Ditolak',
        ];

        $statusPinjamanClass = [
            'menunggu_cs' => 'bg-warning text-dark',
            'menunggu_ao' => 'bg-warning text-dark',
            'menunggu_kabag' => 'bg-warning text-dark',
            'disetujui' => 'bg-primary',
            'dicairkan' => 'bg-success',
            'lunas' => 'bg-info text-dark',
            'ditolak' => 'bg-danger',
        ];

        $statusAngsuranClass = [
            'lunas' => 'bg-success',
            'belum_bayar' => 'bg-secondary',
            'menunggu_verifikasi' => 'bg-warning text-dark',
            'ditolak' => 'bg-danger',
        ];
    @endphp

    <div class="container-fluid py-4 kpgs-report-page">
        <div class="container-xxl">

            {{-- JUDUL HALAMAN --}}
            <div class="mb-4">
                <h4 class="fw-bold mb-1">Ringkasan Keuangan KPGS</h4>
                <p class="text-muted mb-0">
                    Pantau kondisi kas, simpanan, pinjaman, dan angsuran koperasi.
                </p>
            </div>

            {{-- FILTER PERIODE DAN TOMBOL LAPORAN --}}
            <div class="card border-0 shadow-sm mb-4 report-panel no-print">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-calendar-range text-primary me-2"></i>
                        Filter Periode Laporan
                    </h5>
                    <p class="text-muted small mb-0">
                        Pilih rentang tanggal untuk melihat laporan keuangan.
                    </p>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('laporan.keuangan') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md-4">
                                <label for="dari" class="form-label fw-semibold">
                                    Dari Tanggal
                                </label>
                                <input
                                    type="date"
                                    name="dari"
                                    id="dari"
                                    class="form-control"
                                    value="{{ $dari ?? '' }}"
                                >
                            </div>

                            <div class="col-12 col-md-4">
                                <label for="sampai" class="form-label fw-semibold">
                                    Sampai Tanggal
                                </label>
                                <input
                                    type="date"
                                    name="sampai"
                                    id="sampai"
                                    class="form-control"
                                    value="{{ $sampai ?? '' }}"
                                    min="{{ $dari ?? '' }}"
                                >
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-funnel me-1"></i>
                                        Terapkan Filter
                                    </button>

                                    <a
                                        href="{{ route('laporan.keuangan') }}"
                                        class="btn btn-outline-secondary"
                                    >
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                                        Reset
                                    </a>

                                    @if ($rolePengguna === 'bendahara')
                                        <a
                                            href="{{ route('laporan.keuangan.export', array_filter([
                                                'dari' => $dari ?? null,
                                                'sampai' => $sampai ?? null,
                                            ])) }}"
                                            class="btn btn-success"
                                        >
                                            <i class="bi bi-file-earmark-excel me-1"></i>
                                            Export Excel
                                        </a>

                                        <button
                                            type="button"
                                            class="btn btn-dark"
                                            id="tombolCetakLaporan"
                                        >
                                            <i class="bi bi-printer me-1"></i>
                                            Cetak Laporan
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ===================================================== --}}
            {{-- 1. RINGKASAN KAS --}}
            {{-- ===================================================== --}}

            <section class="card border-0 shadow-sm mb-4 report-panel">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="report-icon bg-primary-subtle text-primary">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Ringkasan Kas</h5>
                            <p class="text-muted small mb-0">
                                Saldo dan pergerakan kas koperasi.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="summary-card h-100">
                                <div class="summary-label">Saldo Kas Terkini</div>
                                <div class="summary-value text-primary">
                                    {{ $formatRupiah($saldoKas ?? 0) }}
                                </div>
                                <div class="summary-description">
                                    Saldo terakhir dalam buku kas.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="summary-card h-100">
                                <div class="summary-label">Total Kas Masuk</div>
                                <div class="summary-value text-success">
                                    {{ $formatRupiah($kasMasuk ?? 0) }}
                                </div>
                                <div class="summary-description">
                                    Pemasukan pada periode terpilih.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="summary-card h-100">
                                <div class="summary-label">Total Kas Keluar</div>
                                <div class="summary-value text-danger">
                                    {{ $formatRupiah($kasKeluar ?? 0) }}
                                </div>
                                <div class="summary-description">
                                    Pengeluaran pada periode terpilih.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="summary-card h-100">
                                <div class="summary-label">Selisih Kas Periode</div>
                                <div class="summary-value {{ (($kasMasuk ?? 0) - ($kasKeluar ?? 0)) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $formatRupiah(($kasMasuk ?? 0) - ($kasKeluar ?? 0)) }}
                                </div>
                                <div class="summary-description">
                                    Kas masuk dikurangi kas keluar.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DETAIL TRANSAKSI KAS --}}
                    <div class="accordion report-accordion mt-3" id="detailKas">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDetailKas">
                                <button
                                    class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapseDetailKas"
                                    aria-expanded="false"
                                    aria-controls="collapseDetailKas"
                                >
                                    <i class="bi bi-table me-2 text-primary"></i>
                                    Lihat Detail Transaksi Kas
                                    <span class="badge bg-primary-subtle text-primary ms-2">
                                        {{ $transaksiKas->total() ?? 0 }} Data
                                    </span>
                                </button>
                            </h2>

                            <div
                                id="collapseDetailKas"
                                class="accordion-collapse collapse"
                                aria-labelledby="headingDetailKas"
                                data-bs-parent="#detailKas"
                            >
                                <div class="accordion-body p-0">

                                    {{-- PENCARIAN KAS --}}
                                    <div class="p-3 border-bottom no-print">
                                        <form
                                            method="GET"
                                            action="{{ route('laporan.keuangan') }}"
                                            class="row g-2"
                                        >
                                            <input type="hidden" name="dari" value="{{ $dari }}">
                                            <input type="hidden" name="sampai" value="{{ $sampai }}">

                                            <div class="col-12 col-md-8">
                                                <input
                                                    type="search"
                                                    name="search_kas"
                                                    class="form-control"
                                                    value="{{ $searchKas ?? '' }}"
                                                    placeholder="Cari nomor transaksi, jenis, kategori, atau referensi..."
                                                >
                                            </div>

                                            <div class="col-12 col-md-4 d-flex gap-2">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="bi bi-search me-1"></i>
                                                    Cari
                                                </button>

                                                <a
                                                    href="{{ route('laporan.keuangan', array_filter([
                                                        'dari' => $dari,
                                                        'sampai' => $sampai,
                                                    ])) }}"
                                                    class="btn btn-outline-secondary"
                                                >
                                                    Reset
                                                </a>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="px-3">No.</th>
                                                    <th>Tanggal</th>
                                                    <th>Nomor Transaksi</th>
                                                    <th>Jenis</th>
                                                    <th>Kategori</th>
                                                    <th>Referensi</th>
                                                    <th class="text-end">Jumlah</th>
                                                    <th class="text-end pe-3">Saldo Setelah</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse ($transaksiKas as $kas)
                                                    <tr>
                                                        <td class="px-3">
                                                            {{ $transaksiKas->firstItem() + $loop->index }}
                                                        </td>

                                                        <td class="text-nowrap">
                                                            {{ $formatTanggal($kas->tanggal) }}
                                                        </td>

                                                        <td class="text-nowrap">
                                                            {{ $kas->no_transaksi ?? '-' }}
                                                        </td>

                                                        <td>
                                                            <span class="badge {{ ($kas->jenis ?? '') === 'masuk' ? 'bg-success' : 'bg-danger' }}">
                                                                {{ ucfirst($kas->jenis ?? '-') }}
                                                            </span>
                                                        </td>

                                                        <td>
                                                            {{ ucfirst($kas->kategori ?? '-') }}
                                                        </td>

                                                        <td>
                                                            {{ $kas->referensi_tipe ?? '-' }}
                                                        </td>

                                                        <td class="text-end text-nowrap">
                                                            {{ $formatRupiah($kas->jumlah ?? 0) }}
                                                        </td>

                                                        <td class="text-end text-nowrap pe-3">
                                                            {{ $formatRupiah($kas->saldo_sesudah ?? 0) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted py-4">
                                                            Tidak ada transaksi kas yang sesuai.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- PAGINATION KAS --}}
                                    @if ($transaksiKas->hasPages())
                                        <div class="pagination-wrapper no-print">
                                            <div class="small text-muted">
                                                Menampilkan {{ $transaksiKas->firstItem() }}
                                                sampai {{ $transaksiKas->lastItem() }}
                                                dari {{ $transaksiKas->total() }} transaksi kas.
                                            </div>

                                            {{ $transaksiKas->links('pagination::bootstrap-5') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ===================================================== --}}
            {{-- 2. RINGKASAN SIMPANAN --}}
            {{-- ===================================================== --}}

            <section class="card border-0 shadow-sm mb-4 report-panel">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="report-icon bg-success-subtle text-success">
                            <i class="bi bi-piggy-bank"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Ringkasan Simpanan</h5>
                            <p class="text-muted small mb-0">
                                Total transaksi simpanan yang masuk dan keluar.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="summary-card h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="summary-label">Simpanan Masuk</div>
                                    <i class="bi bi-arrow-down-left-circle fs-4 text-success"></i>
                                </div>

                                <div class="summary-value text-success">
                                    {{ $formatRupiah($simpananMasuk ?? 0) }}
                                </div>

                                <div class="summary-description">
                                    Total transaksi kas kategori simpanan yang masuk.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="summary-card h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="summary-label">Simpanan Keluar</div>
                                    <i class="bi bi-arrow-up-right-circle fs-4 text-danger"></i>
                                </div>

                                <div class="summary-value text-danger">
                                    {{ $formatRupiah($simpananKeluar ?? 0) }}
                                </div>

                                <div class="summary-description">
                                    Total transaksi kas kategori simpanan yang keluar.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DETAIL TRANSAKSI SIMPANAN --}}
                    <div class="accordion report-accordion mt-3" id="detailSimpanan">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDetailSimpanan">
                                <button
                                    class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapseDetailSimpanan"
                                    aria-expanded="false"
                                    aria-controls="collapseDetailSimpanan"
                                >
                                    <i class="bi bi-table me-2 text-success"></i>
                                    Lihat Detail Transaksi Simpanan
                                    <span class="badge bg-success-subtle text-success ms-2">
                                        {{ $transaksiSimpanan->total() ?? 0 }} Data
                                    </span>
                                </button>
                            </h2>

                            <div
                                id="collapseDetailSimpanan"
                                class="accordion-collapse collapse"
                                aria-labelledby="headingDetailSimpanan"
                                data-bs-parent="#detailSimpanan"
                            >
                                <div class="accordion-body p-0">

                                    {{-- PENCARIAN SIMPANAN --}}
                                    <div class="p-3 border-bottom no-print">
                                        <form
                                            method="GET"
                                            action="{{ route('laporan.keuangan') }}"
                                            class="row g-2"
                                        >
                                            <input type="hidden" name="dari" value="{{ $dari }}">
                                            <input type="hidden" name="sampai" value="{{ $sampai }}">

                                            <div class="col-12 col-md-8">
                                                <input
                                                    type="search"
                                                    name="search_simpanan"
                                                    class="form-control"
                                                    value="{{ $searchSimpanan ?? '' }}"
                                                    placeholder="Cari nomor transaksi, jenis, atau referensi simpanan..."
                                                >
                                            </div>

                                            <div class="col-12 col-md-4 d-flex gap-2">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-search me-1"></i>
                                                    Cari
                                                </button>

                                                <a
                                                    href="{{ route('laporan.keuangan', array_filter([
                                                        'dari' => $dari,
                                                        'sampai' => $sampai,
                                                    ])) }}"
                                                    class="btn btn-outline-secondary"
                                                >
                                                    Reset
                                                </a>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="px-3">No.</th>
                                                    <th>Tanggal</th>
                                                    <th>Nomor Transaksi</th>
                                                    <th>Jenis</th>
                                                    <th>Kategori</th>
                                                    <th>Referensi</th>
                                                    <th class="text-end">Jumlah</th>
                                                    <th class="text-end pe-3">Saldo Setelah</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse ($transaksiSimpanan as $kas)
                                                    <tr>
                                                        <td class="px-3">
                                                            {{ $transaksiSimpanan->firstItem() + $loop->index }}
                                                        </td>

                                                        <td class="text-nowrap">
                                                            {{ $formatTanggal($kas->tanggal) }}
                                                        </td>

                                                        <td class="text-nowrap">
                                                            {{ $kas->no_transaksi ?? '-' }}
                                                        </td>

                                                        <td>
                                                            <span class="badge {{ ($kas->jenis ?? '') === 'masuk' ? 'bg-success' : 'bg-danger' }}">
                                                                {{ ucfirst($kas->jenis ?? '-') }}
                                                            </span>
                                                        </td>

                                                        <td>
                                                            {{ ucfirst($kas->kategori ?? '-') }}
                                                        </td>

                                                        <td>
                                                            {{ $kas->referensi_tipe ?? '-' }}
                                                        </td>

                                                        <td class="text-end text-nowrap">
                                                            {{ $formatRupiah($kas->jumlah ?? 0) }}
                                                        </td>

                                                        <td class="text-end text-nowrap pe-3">
                                                            {{ $formatRupiah($kas->saldo_sesudah ?? 0) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted py-4">
                                                            Tidak ada transaksi simpanan yang sesuai.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- PAGINATION SIMPANAN --}}
                                    @if ($transaksiSimpanan->hasPages())
                                        <div class="pagination-wrapper no-print">
                                            <div class="small text-muted">
                                                Menampilkan {{ $transaksiSimpanan->firstItem() }}
                                                sampai {{ $transaksiSimpanan->lastItem() }}
                                                dari {{ $transaksiSimpanan->total() }} transaksi simpanan.
                                            </div>

                                            {{ $transaksiSimpanan->links('pagination::bootstrap-5') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ===================================================== --}}
            {{-- 3. RINGKASAN PINJAMAN --}}
            {{-- ===================================================== --}}

            <section class="card border-0 shadow-sm mb-4 report-panel">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="report-icon bg-info-subtle text-info">
                            <i class="bi bi-bank"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Ringkasan Pinjaman</h5>
                            <p class="text-muted small mb-0">
                                Ringkasan pengajuan dan pencairan pinjaman anggota.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <div class="summary-card h-100">
                                <div class="summary-label">Total Pengajuan Pinjaman</div>
                                <div class="summary-value">
                                    {{ number_format($totalPengajuan ?? 0, 0, ',', '.') }}
                                </div>
                                <div class="summary-description">
                                    Jumlah pengajuan sesuai periode terpilih.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="summary-card h-100">
                                <div class="summary-label">Pinjaman Dicairkan</div>
                                <div class="summary-value text-primary">
                                    {{ number_format($pinjamanAktif ?? 0, 0, ',', '.') }}
                                </div>
                                <div class="summary-description">
                                    Jumlah pinjaman berstatus dicairkan.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="summary-card h-100">
                                <div class="summary-label">Nilai Pinjaman Dicairkan</div>
                                <div class="summary-value">
                                    {{ $formatRupiah($nilaiPinjamanAktif ?? 0) }}
                                </div>
                                <div class="summary-description">
                                    Nilai pencairan berdasarkan periode.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DETAIL PINJAMAN --}}
                    <div class="accordion report-accordion mt-3" id="detailPinjaman">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDetailPinjaman">
                                <button
                                    class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapseDetailPinjaman"
                                    aria-expanded="false"
                                    aria-controls="collapseDetailPinjaman"
                                >
                                    <i class="bi bi-table me-2 text-info"></i>
                                    Lihat Detail Pinjaman
                                    <span class="badge bg-info-subtle text-info ms-2">
                                        {{ $pinjamanTerbaru->total() ?? 0 }} Data
                                    </span>
                                </button>
                            </h2>

                            <div
                                id="collapseDetailPinjaman"
                                class="accordion-collapse collapse"
                                aria-labelledby="headingDetailPinjaman"
                                data-bs-parent="#detailPinjaman"
                            >
                                <div class="accordion-body p-0">

                                    {{-- PENCARIAN PINJAMAN --}}
                                    <div class="p-3 border-bottom no-print">
                                        <form
                                            method="GET"
                                            action="{{ route('laporan.keuangan') }}"
                                            class="row g-2"
                                        >
                                            <input type="hidden" name="dari" value="{{ $dari }}">
                                            <input type="hidden" name="sampai" value="{{ $sampai }}">

                                            <div class="col-12 col-md-8">
                                                <input
                                                    type="search"
                                                    name="search_pinjaman"
                                                    class="form-control"
                                                    value="{{ $searchPinjaman ?? '' }}"
                                                    placeholder="Cari nomor pinjaman, nama anggota, jenis, atau status..."
                                                >
                                            </div>

                                            <div class="col-12 col-md-4 d-flex gap-2">
                                                <button type="submit" class="btn btn-info text-white">
                                                    <i class="bi bi-search me-1"></i>
                                                    Cari
                                                </button>

                                                <a
                                                    href="{{ route('laporan.keuangan', array_filter([
                                                        'dari' => $dari,
                                                        'sampai' => $sampai,
                                                    ])) }}"
                                                    class="btn btn-outline-secondary"
                                                >
                                                    Reset
                                                </a>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="px-3">No.</th>
                                                    <th>Nomor Pinjaman</th>
                                                    <th>Nama Anggota</th>
                                                    <th>Jenis Pinjaman</th>
                                                    <th>Tanggal Pengajuan</th>
                                                    <th class="text-end">Jumlah Pengajuan</th>
                                                    <th class="text-end">Disetujui</th>
                                                    <th>Tenor</th>
                                                    <th>Status</th>
                                                    <th>Tanggal Cair</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse ($pinjamanTerbaru as $pinjaman)
                                                    @php
                                                        $status = $pinjaman->status ?? '';
                                                    @endphp

                                                    <tr>
                                                        <td class="px-3">
                                                            {{ $pinjamanTerbaru->firstItem() + $loop->index }}
                                                        </td>

                                                        <td class="text-nowrap">
                                                            {{ $pinjaman->no_pinjaman ?? '-' }}
                                                        </td>

                                                        <td>
                                                            {{ $pinjaman->nama_anggota ?? '-' }}
                                                        </td>

                                                        <td>
                                                            {{ $pinjaman->nama_jenis_pinjaman ?? '-' }}
                                                        </td>

                                                        <td class="text-nowrap">
                                                            {{ $formatTanggal($pinjaman->tanggal_pengajuan) }}
                                                        </td>

                                                        <td class="text-end text-nowrap">
                                                            {{ $formatRupiah($pinjaman->jumlah_pinjaman ?? 0) }}
                                                        </td>

                                                        <td class="text-end text-nowrap">
                                                            {{ $formatRupiah($pinjaman->jumlah_disetujui ?? 0) }}
                                                        </td>

                                                        <td class="text-nowrap">
                                                            {{ $pinjaman->tenor ?? '-' }}
                                                            {{ $pinjaman->tenor ? 'bulan' : '' }}
                                                        </td>

                                                        <td>
                                                            <span class="badge {{ $statusPinjamanClass[$status] ?? 'bg-secondary' }}">
                                                                {{ $statusPinjaman[$status] ?? ucfirst(str_replace('_', ' ', $status ?: '-')) }}
                                                            </span>
                                                        </td>

                                                        <td class="text-nowrap">
                                                            {{ $formatTanggal($pinjaman->tanggal_cair) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="10" class="text-center text-muted py-4">
                                                            Tidak ada data pinjaman yang sesuai.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- PAGINATION PINJAMAN --}}
                                    @if ($pinjamanTerbaru->hasPages())
                                        <div class="pagination-wrapper no-print">
                                            <div class="small text-muted">
                                                Menampilkan {{ $pinjamanTerbaru->firstItem() }}
                                                sampai {{ $pinjamanTerbaru->lastItem() }}
                                                dari {{ $pinjamanTerbaru->total() }} data pinjaman.
                                            </div>

                                            {{ $pinjamanTerbaru->links('pagination::bootstrap-5') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ===================================================== --}}
            {{-- 4. RINGKASAN ANGSURAN --}}
            {{-- ===================================================== --}}

            <section class="card border-0 shadow-sm mb-4 report-panel">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="report-icon bg-warning-subtle text-warning-emphasis">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">Ringkasan Angsuran</h5>
                            <p class="text-muted small mb-0">
                                Ringkasan tagihan, pembayaran, dan status angsuran.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="summary-card h-100">
                                <div class="summary-label">Total Tagihan Angsuran</div>
                                <div class="summary-value">
                                    {{ $formatRupiah($totalTagihan ?? 0) }}
                                </div>
                                <div class="summary-description">
                                    Total tagihan sesuai periode.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="summary-card h-100">
                                <div class="summary-label">Total Angsuran Dibayar</div>
                                <div class="summary-value text-success">
                                    {{ $formatRupiah($totalDibayar ?? 0) }}
                                </div>
                                <div class="summary-description">
                                    Total pembayaran berstatus lunas.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="summary-card h-100">
                                <div class="summary-label">Angsuran Lunas</div>
                                <div class="summary-value text-success">
                                    {{ number_format($angsuranLunas ?? 0, 0, ',', '.') }}
                                </div>
                                <div class="summary-description">
                                    Jumlah angsuran berstatus lunas.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-3">
                            <div class="summary-card h-100">
                                <div class="summary-label">Menunggu Verifikasi</div>
                                <div class="summary-value text-warning-emphasis">
                                    {{ number_format($menungguVerifikasi ?? 0, 0, ',', '.') }}
                                </div>
                                <div class="summary-description">
                                    Pembayaran menunggu pemeriksaan.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DETAIL RIWAYAT ANGSURAN --}}
                    <div class="accordion report-accordion mt-3" id="detailAngsuran">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDetailAngsuran">
                                <button
                                    class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapseDetailAngsuran"
                                    aria-expanded="false"
                                    aria-controls="collapseDetailAngsuran"
                                >
                                    <i class="bi bi-table me-2 text-warning-emphasis"></i>
                                    Lihat Detail Riwayat Angsuran
                                    <span class="badge bg-warning-subtle text-warning-emphasis ms-2">
                                        {{ $angsuranTerbaru->total() ?? 0 }} Data
                                    </span>
                                </button>
                            </h2>

                            <div
                                id="collapseDetailAngsuran"
                                class="accordion-collapse collapse"
                                aria-labelledby="headingDetailAngsuran"
                                data-bs-parent="#detailAngsuran"
                            >
                                <div class="accordion-body p-0">

                                    {{-- PENCARIAN ANGSURAN --}}
                                    <div class="p-3 border-bottom no-print">
                                        <form
                                            method="GET"
                                            action="{{ route('laporan.keuangan') }}"
                                            class="row g-2"
                                        >
                                            <input type="hidden" name="dari" value="{{ $dari }}">
                                            <input type="hidden" name="sampai" value="{{ $sampai }}">

                                            <div class="col-12 col-md-8">
                                                <input
                                                    type="search"
                                                    name="search_angsuran"
                                                    class="form-control"
                                                    value="{{ $searchAngsuran ?? '' }}"
                                                    placeholder="Cari nama anggota, nomor pinjaman, nomor angsuran, atau status..."
                                                >
                                            </div>

                                            <div class="col-12 col-md-4 d-flex gap-2">
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="bi bi-search me-1"></i>
                                                    Cari
                                                </button>

                                                <a
                                                    href="{{ route('laporan.keuangan', array_filter([
                                                        'dari' => $dari,
                                                        'sampai' => $sampai,
                                                    ])) }}"
                                                    class="btn btn-outline-secondary"
                                                >
                                                    Reset
                                                </a>
                                            </div>
                                        </form>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="px-3">No.</th>
                                                    <th>Nama Anggota</th>
                                                    <th>Nomor Pinjaman</th>
                                                    <th>Angsuran Ke-</th>
                                                    <th>Jatuh Tempo</th>
                                                    <th class="text-end">Tagihan</th>
                                                    <th class="text-end">Dibayar</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse ($angsuranTerbaru as $angsuran)
                                                    @php
                                                        $status = $angsuran->status_pembayaran ?? '';
                                                    @endphp

                                                    <tr>
                                                        <td class="px-3">
                                                            {{ $angsuranTerbaru->firstItem() + $loop->index }}
                                                        </td>

                                                        <td>
                                                            {{ $angsuran->nama_anggota ?? '-' }}
                                                        </td>

                                                        <td class="text-nowrap">
                                                            {{ $angsuran->no_pinjaman ?? '-' }}
                                                        </td>

                                                        <td>
                                                            {{ $angsuran->angsuran_ke ?? '-' }}
                                                        </td>

                                                        <td class="text-nowrap">
                                                            {{ $formatTanggal($angsuran->tanggal_jatuh_tempo) }}
                                                        </td>

                                                        <td class="text-end text-nowrap">
                                                            {{ $formatRupiah($angsuran->total_angsuran ?? 0) }}
                                                        </td>

                                                        <td class="text-end text-nowrap">
                                                            {{ $formatRupiah($angsuran->jumlah_bayar ?? 0) }}
                                                        </td>

                                                        <td>
                                                            <span class="badge {{ $statusAngsuranClass[$status] ?? 'bg-secondary' }}">
                                                                {{ $statusAngsuran[$status] ?? ucfirst(str_replace('_', ' ', $status ?: '-')) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted py-4">
                                                            Tidak ada data angsuran yang sesuai.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- PAGINATION ANGSURAN --}}
                                    @if ($angsuranTerbaru->hasPages())
                                        <div class="pagination-wrapper no-print">
                                            <div class="small text-muted">
                                                Menampilkan {{ $angsuranTerbaru->firstItem() }}
                                                sampai {{ $angsuranTerbaru->lastItem() }}
                                                dari {{ $angsuranTerbaru->total() }} data angsuran.
                                            </div>

                                            {{ $angsuranTerbaru->links('pagination::bootstrap-5') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- INFORMASI PERIODE --}}
            <div class="text-muted small mb-2">
                Periode laporan:
                <strong>
                    {{ !empty($dari) ? $formatTanggal($dari) : 'Awal data' }}
                </strong>
                sampai
                <strong>
                    {{ !empty($sampai) ? $formatTanggal($sampai) : 'Tanggal terakhir' }}
                </strong>.
            </div>

        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- STYLE HALAMAN LAPORAN --}}
    {{-- ========================================================= --}}

    <style>
        .report-panel {
            border-radius: 12px;
            overflow: hidden;
        }

        .report-icon {
            width: 44px;
            height: 44px;
            flex: 0 0 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 1.3rem;
        }

        .summary-card {
            padding: 1.1rem;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            background: #fff;
            transition: border-color .2s ease, transform .2s ease;
        }

        .summary-card:hover {
            border-color: #ced4da;
            transform: translateY(-2px);
        }

        .summary-label {
            color: #6c757d;
            font-size: .875rem;
            font-weight: 600;
            margin-bottom: .65rem;
        }

        .summary-value {
            font-size: clamp(1.15rem, 2vw, 1.65rem);
            font-weight: 700;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .summary-description {
            color: #6c757d;
            font-size: .8rem;
            margin-top: .65rem;
        }

        .report-accordion .accordion-item {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }

        .report-accordion .accordion-button {
            font-weight: 600;
            background: #f8f9fa;
            box-shadow: none;
        }

        .report-accordion .accordion-button:not(.collapsed) {
            color: #212529;
            background: #f1f3f5;
        }

        .report-accordion .accordion-body {
            background: #fff;
        }

        .kpgs-report-page .table th,
        .kpgs-report-page .table td {
            vertical-align: middle;
        }

        .pagination-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding: 1rem;
            border-top: 1px solid #e9ecef;
        }

        .pagination-wrapper nav {
            margin: 0;
        }

        .pagination-wrapper .pagination {
            margin-bottom: 0;
            flex-wrap: wrap;
            justify-content: center;
        }

        @media (min-width: 768px) {
            .pagination-wrapper {
                flex-direction: row;
            }
        }

        @media (max-width: 767.98px) {
            .kpgs-report-page {
                padding-left: .75rem;
                padding-right: .75rem;
            }

            .report-icon {
                width: 38px;
                height: 38px;
                flex-basis: 38px;
                font-size: 1.1rem;
            }

            .summary-card {
                padding: .95rem;
            }

            .report-accordion .accordion-button {
                font-size: .9rem;
            }

            .pagination-wrapper {
                overflow-x: auto;
                align-items: flex-start;
            }
        }

        @media print {
            body {
                background: #fff !important;
            }

            nav,
            header,
            aside,
            .no-print,
            .pagination-wrapper,
            .accordion-button::after {
                display: none !important;
            }

            .kpgs-report-page {
                padding: 0 !important;
            }

            .kpgs-report-page .card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                break-inside: avoid;
            }

            .kpgs-report-page .accordion-collapse {
                display: block !important;
                height: auto !important;
                visibility: visible !important;
            }

            .kpgs-report-page .accordion-button {
                padding: .5rem 0;
                background: #fff !important;
                color: #212529 !important;
            }

            .kpgs-report-page .table-responsive {
                overflow: visible !important;
            }

            .kpgs-report-page .table {
                font-size: 10px;
            }

            .kpgs-report-page .container-xxl {
                max-width: 100% !important;
                width: 100% !important;
            }

            .summary-card {
                break-inside: avoid;
                transform: none !important;
            }

            a {
                text-decoration: none !important;
                color: inherit !important;
            }
        }
    </style>

    {{-- ========================================================= --}}
    {{-- JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tombolCetak = document.getElementById('tombolCetakLaporan');

            if (tombolCetak) {
                tombolCetak.addEventListener('click', function () {
                    window.print();
                });
            }
        });
    </script>
</x-app-layout>

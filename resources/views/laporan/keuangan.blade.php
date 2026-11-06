
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

        $formatRupiah = fn ($nilai) => 'Rp ' . number_format((float) $nilai, 0, ',', '.');

        $statusAngsuran = [
            'lunas' => 'Lunas',
            'belum_bayar' => 'Belum Dibayar',
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'ditolak' => 'Ditolak',
        ];

        $transaksiSimpanan = collect($transaksiKas ?? [])
            ->filter(fn ($kas) => strtolower((string) ($kas->kategori ?? '')) === 'simpanan');
    @endphp

    <div class="container-fluid py-4 kpgs-report-page">
        <div class="container-xxl">

            {{-- JUDUL --}}
            <div class="mb-4">
                <h4 class="fw-bold mb-1">Ringkasan Keuangan KPGS</h4>
                <p class="text-muted mb-0">
                    Pantau kondisi kas, simpanan, pinjaman, dan angsuran koperasi.
                </p>
            </div>

            {{-- FILTER DAN TOMBOL LAPORAN --}}
            <div class="card border-0 shadow-sm mb-4 report-panel">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-calendar-range text-primary me-2"></i>
                        Filter Periode Laporan
                    </h5>
                    <p class="text-muted small mb-0">
                        Pilih rentang tanggal untuk melihat ringkasan keuangan.
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

            {{-- 1. RINGKASAN KAS --}}
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
                                </button>
                            </h2>

                            <div
                                id="collapseDetailKas"
                                class="accordion-collapse collapse"
                                aria-labelledby="headingDetailKas"
                                data-bs-parent="#detailKas"
                            >
                                <div class="accordion-body p-0">
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
                                            <tbody data-search-table>
                                                @forelse ($transaksiKas ?? [] as $kas)
                                                    <tr>
                                                        <td class="px-3">{{ $loop->iteration }}</td>
                                                        <td class="text-nowrap">
                                                            {{ \Illuminate\Support\Carbon::parse($kas->tanggal)->format('d/m/Y') }}
                                                        </td>
                                                        <td>{{ $kas->no_transaksi ?? '-' }}</td>
                                                        <td>
                                                            <span class="badge {{ ($kas->jenis ?? '') === 'masuk' ? 'bg-success' : 'bg-danger' }}">
                                                                {{ ucfirst($kas->jenis ?? '-') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ ucfirst($kas->kategori ?? '-') }}</td>
                                                        <td>{{ $kas->referensi_tipe ?? '-' }}</td>
                                                        <td class="text-end text-nowrap">
                                                            {{ $formatRupiah($kas->jumlah ?? 0) }}
                                                        </td>
                                                        <td class="text-end text-nowrap pe-3">
                                                            {{ $formatRupiah($kas->saldo_sesudah ?? 0) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr data-empty-row>
                                                        <td colspan="8" class="text-center text-muted py-4">
                                                            Belum ada transaksi kas pada periode ini.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 2. RINGKASAN SIMPANAN --}}
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
                                </button>
                            </h2>

                            <div
                                id="collapseDetailSimpanan"
                                class="accordion-collapse collapse"
                                aria-labelledby="headingDetailSimpanan"
                                data-bs-parent="#detailSimpanan"
                            >
                                <div class="accordion-body p-0">
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
                                            <tbody data-search-table>
                                                @forelse ($transaksiSimpanan as $kas)
                                                    <tr>
                                                        <td class="px-3">{{ $loop->iteration }}</td>
                                                        <td class="text-nowrap">
                                                            {{ \Illuminate\Support\Carbon::parse($kas->tanggal)->format('d/m/Y') }}
                                                        </td>
                                                        <td>{{ $kas->no_transaksi ?? '-' }}</td>
                                                        <td>
                                                            <span class="badge {{ ($kas->jenis ?? '') === 'masuk' ? 'bg-success' : 'bg-danger' }}">
                                                                {{ ucfirst($kas->jenis ?? '-') }}
                                                            </span>
                                                        </td>
                                                        <td>{{ ucfirst($kas->kategori ?? '-') }}</td>
                                                        <td>{{ $kas->referensi_tipe ?? '-' }}</td>
                                                        <td class="text-end text-nowrap">
                                                            {{ $formatRupiah($kas->jumlah ?? 0) }}
                                                        </td>
                                                        <td class="text-end text-nowrap pe-3">
                                                            {{ $formatRupiah($kas->saldo_sesudah ?? 0) }}
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr data-empty-row>
                                                        <td colspan="8" class="text-center text-muted py-4">
                                                            Belum ada transaksi simpanan pada periode ini.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 3. RINGKASAN PINJAMAN --}}
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
                                </button>
                            </h2>

                            <div
                                id="collapseDetailPinjaman"
                                class="accordion-collapse collapse"
                                aria-labelledby="headingDetailPinjaman"
                                data-bs-parent="#detailPinjaman"
                            >
                                <div class="accordion-body">
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

            <tbody data-search-table>
                @forelse ($pinjamanTerbaru ?? [] as $pinjaman)
                    @php
                        $status = $pinjaman->status ?? '';

                        $statusClass = match ($status) {
                            'dicairkan' => 'bg-success',
                            'disetujui' => 'bg-primary',
                            'lunas' => 'bg-info text-dark',
                            'ditolak' => 'bg-danger',
                            'menunggu_cs',
                            'menunggu_ao',
                            'menunggu_kabag' => 'bg-warning text-dark',
                            default => 'bg-secondary',
                        };

                        $statusLabel = match ($status) {
                            'menunggu_cs' => 'Menunggu CS',
                            'menunggu_ao' => 'Menunggu AO',
                            'menunggu_kabag' => 'Menunggu Kabag',
                            'disetujui' => 'Disetujui',
                            'dicairkan' => 'Dicairkan',
                            'lunas' => 'Lunas',
                            'ditolak' => 'Ditolak',
                            default => ucfirst(str_replace('_', ' ', $status ?: '-')),
                        };
                    @endphp

                    <tr>
                        <td class="px-3">{{ $loop->iteration }}</td>

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
                            {{ $pinjaman->tanggal_pengajuan
                                ? \Illuminate\Support\Carbon::parse($pinjaman->tanggal_pengajuan)->format('d/m/Y')
                                : '-' }}
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
                            <span class="badge {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>

                        <td class="text-nowrap">
                            {{ $pinjaman->tanggal_cair
                                ? \Illuminate\Support\Carbon::parse($pinjaman->tanggal_cair)->format('d/m/Y')
                                : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr data-empty-row>
                        <td colspan="10" class="text-center text-muted py-4">
                            Belum ada data pinjaman pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- 4. RINGKASAN ANGSURAN --}}
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
                                </button>
                            </h2>

                            <div
                                id="collapseDetailAngsuran"
                                class="accordion-collapse collapse"
                                aria-labelledby="headingDetailAngsuran"
                                data-bs-parent="#detailAngsuran"
                            >
                                <div class="accordion-body p-0">
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
                                            <tbody data-search-table>
                                                @forelse ($angsuranTerbaru ?? [] as $angsuran)
                                                    @php
                                                        $status = $angsuran->status_pembayaran ?? '';

                                                        $statusClass = match ($status) {
                                                            'lunas' => 'bg-success',
                                                            'menunggu_verifikasi' => 'bg-warning text-dark',
                                                            'ditolak' => 'bg-danger',
                                                            default => 'bg-secondary',
                                                        };
                                                    @endphp

                                                    <tr>
                                                        <td class="px-3">{{ $loop->iteration }}</td>
                                                        <td>{{ $angsuran->nama_anggota ?? '-' }}</td>
                                                        <td>{{ $angsuran->no_pinjaman ?? '-' }}</td>
                                                        <td>{{ $angsuran->angsuran_ke ?? '-' }}</td>
                                                        <td class="text-nowrap">
                                                            {{ $angsuran->tanggal_jatuh_tempo
                                                                ? \Illuminate\Support\Carbon::parse($angsuran->tanggal_jatuh_tempo)->format('d/m/Y')
                                                                : '-' }}
                                                        </td>
                                                        <td class="text-end text-nowrap">
                                                            {{ $formatRupiah($angsuran->total_angsuran ?? 0) }}
                                                        </td>
                                                        <td class="text-end text-nowrap">
                                                            {{ $formatRupiah($angsuran->jumlah_bayar ?? 0) }}
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $statusClass }}">
                                                                {{ $statusAngsuran[$status] ?? ucfirst(str_replace('_', ' ', $status ?: '-')) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr data-empty-row>
                                                        <td colspan="8" class="text-center text-muted py-4">
                                                            Belum ada data angsuran pada periode ini.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- PENCARIAN DETAIL --}}
            <section class="card border-0 shadow-sm mb-4 report-panel no-print">
                <div class="card-body">
                    <label for="pencarianLaporan" class="form-label fw-semibold">
                        <i class="bi bi-search text-primary me-1"></i>
                        Pencarian Detail Laporan
                    </label>

                    <input
                        type="search"
                        id="pencarianLaporan"
                        class="form-control"
                        placeholder="Cari nomor transaksi, kategori, nomor pinjaman, nama anggota, atau status..."
                        autocomplete="off"
                    >

                    <div class="form-text">
                        Pencarian menyaring baris pada tabel detail yang tersedia.
                    </div>
                    <div id="hasilPencarian" class="small text-muted mt-2" aria-live="polite"></div>
                </div>
            </section>

            {{-- PERIODE --}}
            <div class="text-muted small mb-2">
                Periode laporan:
                <strong>
                    {{ !empty($dari) ? \Illuminate\Support\Carbon::parse($dari)->format('d/m/Y') : 'Awal data' }}
                </strong>
                sampai
                <strong>
                    {{ !empty($sampai) ? \Illuminate\Support\Carbon::parse($sampai)->format('d/m/Y') : 'Tanggal terakhir' }}
                </strong>.
            </div>

        </div>
    </div>

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
        }

        @media print {
            body {
                background: #fff !important;
            }

            nav,
            header,
            aside,
            .kpgs-report-page form,
            #pencarianLaporan,
            #hasilPencarian,
            .form-text,
            #tombolCetakLaporan,
            .no-print,
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputPencarian = document.getElementById('pencarianLaporan');
            const areaHasil = document.getElementById('hasilPencarian');
            const tabelBody = document.querySelectorAll('[data-search-table]');

            if (inputPencarian) {
                inputPencarian.addEventListener('input', function () {
                    const kataKunci = this.value.trim().toLocaleLowerCase('id-ID');

                    let jumlahCocok = 0;
                    let jumlahBarisData = 0;

                    tabelBody.forEach(function (tbody) {
                        const baris = tbody.querySelectorAll(
                            'tr:not([data-empty-row]):not([data-search-empty])'
                        );

                        baris.forEach(function (tr) {
                            jumlahBarisData++;

                            const teksBaris = tr.textContent.toLocaleLowerCase('id-ID');
                            const cocok = kataKunci === '' || teksBaris.includes(kataKunci);

                            tr.hidden = !cocok;

                            if (cocok) {
                                jumlahCocok++;
                            }
                        });

                        let barisKosong = tbody.querySelector('[data-search-empty]');

                        if (!barisKosong) {
                            barisKosong = document.createElement('tr');
                            barisKosong.setAttribute('data-search-empty', '');
                            barisKosong.hidden = true;

                            const kolom = document.createElement('td');
                            kolom.colSpan = 8;
                            kolom.className = 'text-center text-muted py-3';
                            kolom.textContent = 'Tidak ada data yang cocok dengan pencarian.';

                            barisKosong.appendChild(kolom);
                            tbody.appendChild(barisKosong);
                        }

                        const barisTerlihat = Array.from(baris).some(function (tr) {
                            return !tr.hidden;
                        });

                        barisKosong.hidden = kataKunci === '' || barisTerlihat;
                    });

                    if (areaHasil) {
                        areaHasil.textContent = kataKunci === ''
                            ? ''
                            : jumlahCocok + ' baris cocok dari ' +
                              jumlahBarisData + ' baris data yang ditampilkan.';
                    }
                });
            }

            const tombolCetak = document.getElementById('tombolCetakLaporan');

            if (tombolCetak) {
                tombolCetak.addEventListener('click', function () {
                    window.print();
                });
            }
        });
    </script>
</x-app-layout>

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <x-dashboard-stat-card title="Total Simpanan Disetujui" :value="'Rp ' . number_format($data['totalSimpanan'] ?? 0, 0, ',', '.')" />
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <x-dashboard-stat-card title="Saldo Kas Terakhir" :value="'Rp ' . number_format($data['saldoKas'] ?? 0, 0, ',', '.')" />
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <x-dashboard-stat-card title="Pinjaman Siap Dicairkan" :value="$data['pinjamanDisetujui'] ?? 0" />
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <x-dashboard-stat-card title="Pinjaman Sedang Berjalan" :value="$data['pinjamanDicairkan'] ?? 0" />
    </div>
</div>

{{-- Pemantauan tahap pengajuan --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
            <div>
                <h3 class="h5 fw-semibold mb-1">Ringkasan Tahapan Pinjaman</h3>
                <p class="small text-muted mb-0">Jumlah pengajuan pada setiap status saat ini.</p>
            </div>
            <a href="{{ route('pinjaman.bendahara.index') }}" class="btn btn-outline-primary btn-sm">Lihat Semua Pinjaman</a>
        </div>
        <div class="row g-3">
            @foreach ([
                ['label' => 'Menunggu CS', 'key' => 'menungguCS'],
                ['label' => 'Menunggu AO', 'key' => 'menungguAO'],
                ['label' => 'Menunggu Kabag', 'key' => 'menungguKabag'],
                ['label' => 'Disetujui', 'key' => 'pinjamanDisetujui'],
                ['label' => 'Sudah Dicairkan', 'key' => 'pinjamanDicairkan'],
                ['label' => 'Lunas', 'key' => 'pinjamanLunas'],
                ['label' => 'Ditolak', 'key' => 'pinjamanDitolak'],
            ] as $item)
                <div class="col-6 col-md-4 col-xl">
                    <div class="border rounded p-3 h-100">
                        <div class="small text-muted mb-2">{{ $item['label'] }}</div>
                        <div class="fs-4 fw-bold">{{ $data[$item['key']] ?? 0 }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Pengajuan terbaru --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="mb-3">
            <h3 class="h5 fw-semibold mb-1">Pengajuan Pinjaman Terbaru</h3>
            <p class="small text-muted mb-0">Daftar ini bersifat pemantauan. Tindakan proses tetap mengikuti kewenangan setiap petugas.</p>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Pinjaman</th>
                        <th>Anggota</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Jumlah Pengajuan</th>
                        <th>Status</th>
                        <th class="text-end">Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (($data['pinjamanTerbaru'] ?? collect()) as $pinjaman)
                        @php
                            $statusLabel = [
                                'menunggu_cs' => 'Menunggu CS',
                                'menunggu_ao' => 'Menunggu AO',
                                'menunggu_kabag' => 'Menunggu Kabag',
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                                'dicairkan' => 'Dicairkan',
                                'lunas' => 'Lunas',
                            ][$pinjaman->status] ?? ucfirst(str_replace('_', ' ', $pinjaman->status));
                            $statusClass = match ($pinjaman->status) {
                                'ditolak' => 'text-bg-danger',
                                'disetujui' => 'text-bg-success',
                                'dicairkan', 'lunas' => 'text-bg-primary',
                                default => 'text-bg-warning',
                            };
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $pinjaman->no_pinjaman }}</td>
                            <td>{{ $pinjaman->anggota?->user?->nama ?? '-' }}</td>
                            <td>{{ $pinjaman->tanggal_pengajuan?->format('d/m/Y') ?? '-' }}</td>
                            <td>Rp {{ number_format($pinjaman->jumlah_pinjaman ?? 0, 0, ',', '.') }}</td>
                            <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                            <td class="text-end">
                                <a href="{{ route('pinjaman.bendahara.show', $pinjaman) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pengajuan pinjaman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Akses laporan yang telah tersedia --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <h3 class="h5 fw-semibold mb-3">Pemantauan dan Laporan Keuangan</h3>
        <div class="row g-3">
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('laporan.simpanan') }}" class="d-flex align-items-center gap-3 border rounded p-3 h-100 text-decoration-none text-body">
                    <i class="bi bi-piggy-bank fs-4 text-primary"></i><span class="fw-medium">Laporan Simpanan</span>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('laporan.pinjaman') }}" class="d-flex align-items-center gap-3 border rounded p-3 h-100 text-decoration-none text-body">
                    <i class="bi bi-clipboard-data fs-4 text-primary"></i><span class="fw-medium">Laporan Pinjaman</span>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('laporan.angsuran') }}" class="d-flex align-items-center gap-3 border rounded p-3 h-100 text-decoration-none text-body">
                    <i class="bi bi-calendar-check fs-4 text-primary"></i><span class="fw-medium">Laporan Angsuran</span>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ route('laporan.kas') }}" class="d-flex align-items-center gap-3 border rounded p-3 h-100 text-decoration-none text-body">
                    <i class="bi bi-journal-text fs-4 text-primary"></i><span class="fw-medium">Buku Kas / Laporan Kas</span>
                </a>
            </div>
        </div>
    </div>
</div>

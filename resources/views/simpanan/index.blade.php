<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div>
                <h2 class="h4 fw-bold mb-1">Data Simpanan</h2>
                <p class="text-muted mb-0">Kelola, catat, dan periksa transaksi simpanan anggota.</p>
            </div>
            <a href="{{ route('simpanan.create') }}" class="btn btn-primary flex-shrink-0">
                <i class="bi bi-plus-circle me-1"></i>
                Input Pembayaran
            </a>
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

            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width: 48px; height: 48px;">
                                    <i class="bi bi-wallet2 fs-5"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Total Transaksi</div>
                                    <div class="fs-4 fw-bold">{{ $simpanan->total() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width: 48px; height: 48px;">
                                    <i class="bi bi-hourglass-split fs-5"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Menunggu Verifikasi</div>
                                    <div class="fs-4 fw-bold">
                                        {{ $simpanan->where('status', 'pending')->count() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width: 48px; height: 48px;">
                                    <i class="bi bi-check-circle fs-5"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">Disetujui</div>
                                    <div class="fs-4 fw-bold">
                                        {{ $simpanan->where('status', 'disetujui')->count() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div>
                            <h5 class="fw-bold mb-1">Filter Data</h5>
                            <p class="text-muted small mb-0">Gunakan filter untuk menemukan transaksi tertentu.</p>
                        </div>
                        <i class="bi bi-funnel text-muted fs-5"></i>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form method="GET" action="{{ route('simpanan.index') }}">
                        <div class="row g-3">
                            <div class="col-12 col-lg-4">
                                <label for="search" class="form-label fw-semibold">Pencarian</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input
                                        type="text"
                                        id="search"
                                        name="search"
                                        value="{{ request('search') }}"
                                        class="form-control"
                                        placeholder="Nomor transaksi atau nama anggota..."
                                    >
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-2">
                                <label for="jenis_simpanan_id" class="form-label fw-semibold">Jenis Simpanan</label>
                                <select id="jenis_simpanan_id" name="jenis_simpanan_id" class="form-select">
                                    <option value="">Semua Jenis</option>
                                    @foreach ($jenisSimpanan as $jenis)
                                        <option value="{{ $jenis->id }}"
                                            {{ request('jenis_simpanan_id') == $jenis->id ? 'selected' : '' }}>
                                            {{ $jenis->nama_jenis }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6 col-lg-2">
                                <label for="status" class="form-label fw-semibold">Status</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                        Menunggu Verifikasi
                                    </option>
                                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>
                                        Disetujui
                                    </option>
                                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>
                                        Ditolak
                                    </option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6 col-lg-2">
                                <label for="tanggal_mulai" class="form-label fw-semibold">Tanggal Mulai</label>
                                <input
                                    type="date"
                                    id="tanggal_mulai"
                                    name="tanggal_mulai"
                                    value="{{ request('tanggal_mulai') }}"
                                    class="form-control"
                                >
                            </div>

                            <div class="col-12 col-md-6 col-lg-2">
                                <label for="tanggal_akhir" class="form-label fw-semibold">Tanggal Akhir</label>
                                <input
                                    type="date"
                                    id="tanggal_akhir"
                                    name="tanggal_akhir"
                                    value="{{ request('tanggal_akhir') }}"
                                    class="form-control"
                                >
                            </div>

                            <div class="col-12">
                                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                                    <a href="{{ route('simpanan.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                                        Reset
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search me-1"></i>
                                        Terapkan Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 px-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <div>
                            <h5 class="fw-bold mb-1">Daftar Simpanan</h5>
                            <p class="text-muted small mb-0">
                                Menampilkan data transaksi simpanan anggota.
                            </p>
                        </div>
                        <span class="badge bg-primary-subtle text-primary">
                            {{ $simpanan->total() }} Data
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">No. Simpanan</th>
                                    <th class="py-3">Anggota</th>
                                    <th class="py-3">Jenis Simpanan</th>
                                    <th class="py-3">Tanggal</th>
                                    <th class="py-3 text-end">Jumlah</th>
                                    <th class="py-3">Metode</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($simpanan as $item)
                                    <tr>
                                        <td class="px-4">
                                            <div class="fw-semibold">{{ $item->no_simpanan }}</div>
                                        </td>

                                        <td>
                                            <div class="fw-semibold">
                                                {{ $item->anggota?->user?->nama ?? '-' }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ $item->anggota?->kode_anggota ?? '-' }}
                                            </div>
                                        </td>

                                        <td>
                                            {{ $item->jenisSimpanan?->nama_jenis ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $item->tanggal_transaksi?->translatedFormat('d F Y') ?? '-' }}
                                        </td>

                                        <td class="text-end fw-semibold">
                                            Rp {{ number_format((float) $item->jumlah, 0, ',', '.') }}
                                        </td>

                                        <td class="text-capitalize">
                                            {{ $item->metode_pembayaran ?? '-' }}
                                        </td>

                                        <td>
                                            @if ($item->status === 'pending')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-hourglass-split me-1"></i>
                                                    Menunggu
                                                </span>
                                            @elseif ($item->status === 'disetujui')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>
                                                    Disetujui
                                                </span>
                                            @elseif ($item->status === 'ditolak')
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle me-1"></i>
                                                    Ditolak
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($item->status ?? '-') }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <a
                                                href="{{ route('simpanan.show', $item) }}"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Lihat Detail"
                                            >
                                                <i class="bi bi-eye"></i>
                                                <span class="d-none d-lg-inline ms-1">Detail</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="bi bi-wallet2 fs-1 d-block mb-3"></i>
                                                <div class="fw-semibold mb-1">Belum ada data simpanan</div>
                                                <div class="small">
                                                    Tidak ada transaksi yang sesuai dengan filter.
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-md-none p-3">
                        @forelse ($simpanan as $item)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                    <div>
                                        <div class="text-muted small">No. Simpanan</div>
                                        <div class="fw-bold">{{ $item->no_simpanan }}</div>
                                    </div>

                                    @if ($item->status === 'pending')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-hourglass-split me-1"></i>
                                            Menunggu
                                        </span>
                                    @elseif ($item->status === 'disetujui')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Disetujui
                                        </span>
                                    @elseif ($item->status === 'ditolak')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ ucfirst($item->status ?? '-') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <div class="text-muted small">Anggota</div>
                                    <div class="fw-semibold">
                                        {{ $item->anggota?->user?->nama ?? '-' }}
                                    </div>
                                    <div class="text-muted small">
                                        {{ $item->anggota?->kode_anggota ?? '-' }}
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-6">
                                        <div class="text-muted small">Jenis Simpanan</div>
                                        <div class="fw-semibold">
                                            {{ $item->jenisSimpanan?->nama_jenis ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="text-muted small">Tanggal</div>
                                        <div class="fw-semibold">
                                            {{ $item->tanggal_transaksi?->translatedFormat('d F Y') ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="text-muted small">Jumlah</div>
                                        <div class="fw-bold">
                                            Rp {{ number_format((float) $item->jumlah, 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="text-muted small">Metode</div>
                                        <div class="fw-semibold text-capitalize">
                                            {{ $item->metode_pembayaran ?? '-' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <a
                                        href="{{ route('simpanan.show', $item) }}"
                                        class="btn btn-outline-primary"
                                    >
                                        <i class="bi bi-eye me-1"></i>
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-wallet2 fs-1 d-block mb-3"></i>
                                <div class="fw-semibold mb-1">Belum ada data simpanan</div>
                                <div class="small">
                                    Tidak ada transaksi yang sesuai dengan filter.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if ($simpanan->hasPages())
                    <div class="card-footer bg-white border-0 px-4 py-3">
                        {{ $simpanan->links('pagination::bootstrap-5') }}
                    </div>
                @endif


            {{-- DAFTAR PENCAIRAN SIMPANAN: hanya menampilkan data, tanpa tindakan --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white py-3 px-4">
                    <h5 class="fw-bold mb-1">Daftar Pencairan Simpanan</h5>
                    <p class="text-muted small mb-0">Menampilkan pengajuan pencairan simpanan anggota dari seluruh tahap proses.</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4">No. Pengajuan</th>
                                    <th>Anggota</th>
                                    <th>Jenis Simpanan</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th class="text-end">Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($daftarPencairan as $pencairan)
                                    @php
                                        $statusPencairan = match ($pencairan->status) {
                                            'menunggu_cs' => ['Menunggu CS/Teller', 'bg-warning text-dark'],
                                            'menunggu_kabag' => ['Menunggu Kabag', 'bg-info text-dark'],
                                            'menunggu_pencairan' => ['Menunggu Pencairan', 'bg-primary'],
                                            'selesai' => ['Selesai', 'bg-success'],
                                            'ditolak_cs' => ['Ditolak CS/Teller', 'bg-danger'],
                                            'ditolak_kabag' => ['Ditolak Kabag', 'bg-danger'],
                                            default => [ucfirst(str_replace('_', ' ', $pencairan->status)), 'bg-secondary'],
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-4 fw-semibold text-nowrap">{{ $pencairan->no_pengajuan }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $pencairan->anggota?->user?->nama ?? $pencairan->anggota?->user?->name ?? '-' }}</div>
                                            <div class="small text-muted">{{ $pencairan->anggota?->kode_anggota ?? '-' }}</div>
                                        </td>
                                        <td>{{ $pencairan->nama_jenis_simpanan }}</td>
                                        <td class="text-nowrap">{{ $pencairan->created_at?->format('d/m/Y') ?? '-' }}</td>
                                        <td class="text-end text-nowrap">Rp {{ number_format((float) $pencairan->jumlah, 0, ',', '.') }}</td>
                                        <td><span class="badge {{ $statusPencairan[1] }}">{{ $statusPencairan[0] }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada pengajuan pencairan simpanan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($daftarPencairan->hasPages())
                    <div class="card-footer bg-white border-0 px-4 py-3">
                        {{ $daftarPencairan->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            </div>
        </div>
    </div>
</x-app-layout>
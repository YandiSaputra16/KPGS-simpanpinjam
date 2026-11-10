<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 mb-1 fw-bold">Data Anggota</h2>
            <p class="text-muted small mb-0">
                Informasi anggota untuk mendukung pemantauan dan analisis AO.
            </p>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-muted mb-1">Total Anggota</div>
                        <div class="fs-3 fw-bold">{{ number_format($ringkasan['total']) }}</div>
                        <div class="small text-muted">Seluruh anggota terdaftar</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-muted mb-1">Anggota Aktif</div>
                        <div class="fs-3 fw-bold text-success">{{ number_format($ringkasan['aktif']) }}</div>
                        <div class="small text-muted">Status keanggotaan aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="small text-muted mb-1">Anggota Nonaktif</div>
                        <div class="fs-3 fw-bold text-secondary">{{ number_format($ringkasan['nonaktif']) }}</div>
                        <div class="small text-muted">Status keanggotaan nonaktif</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2">
                    <div>
                        <h5 class="fw-bold mb-1">Daftar Anggota</h5>
                        <div class="small text-muted">Pilih anggota untuk melihat riwayat simpanan, pinjaman, dan angsuran.</div>
                    </div>
                    <form method="GET" action="{{ route('data-anggota.ao.index') }}" class="d-flex flex-column flex-sm-row gap-2">
                        <input type="search" name="search" value="{{ $search }}"
                               class="form-control" placeholder="Cari nama, kode, NIK, atau HP">
                        <select name="status" class="form-select" style="min-width: 150px;">
                            <option value="">Semua Status</option>
                            <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                        </select>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                        <a href="{{ route('data-anggota.ao.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">No.</th>
                            <th>Anggota</th>
                            <th>Kode Anggota</th>
                            <th>Kontak</th>
                            <th class="text-center">Pinjaman</th>
                            <th class="text-end">Total Saldo Simpanan</th>
                            <th>Status</th>
                            <th class="text-center pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($anggota as $item)
                            <tr>
                                <td class="ps-3">{{ $anggota->firstItem() + $loop->index }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $item->user?->nama ?? '-' }}</div>
                                    <div class="small text-muted">{{ $item->user?->email ?? '-' }}</div>
                                </td>
                                <td>{{ $item->kode_anggota }}</td>
                                <td>
                                    <div>{{ $item->no_hp ?: '-' }}</div>
                                    <div class="small text-muted">{{ $item->pekerjaan ?: '-' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ $item->pinjaman_count }} pengajuan
                                    </span>
                                    <div class="small text-muted">{{ $item->pinjaman_aktif_count }} aktif</div>
                                </td>
                                <td class="text-end text-nowrap">
                                    Rp {{ number_format((float) $item->total_simpanan, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if ($item->status_keanggotaan === 'aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center pe-3">
                                    <a href="{{ route('data-anggota.ao.show', $item) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="bi bi-people fs-2"></i></div>
                                    <div class="fw-semibold">Data anggota tidak ditemukan</div>
                                    <div class="small text-muted">Ubah kata kunci atau filter pencarian.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($anggota->hasPages())
                <div class="card-footer bg-white">
                    {{ $anggota->links() }}
                </div>
            @endif
        </div>

        <div class="alert alert-info mt-3 mb-0 small">
            <i class="bi bi-info-circle me-1"></i>
            Halaman ini hanya menyediakan akses baca. AO tidak dapat mengubah data anggota atau memproses transaksi simpanan dan angsuran dari halaman ini.
        </div>
    </div>
</x-app-layout>

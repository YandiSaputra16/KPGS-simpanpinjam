
{{-- Sambutan --}}

{{-- Ringkasan Kabag --}}
<div class="row g-4 mb-4">

    <div class="col-12 col-sm-6 col-lg-4">
        <x-dashboard-stat-card
            title="Menunggu Persetujuan"
            :value="$data['menungguKabag'] ?? 0"
        />
    </div>

    <div class="col-12 col-sm-6 col-lg-4">
        <x-dashboard-stat-card
            title="Pinjaman Disetujui"
            :value="$data['pinjamanDisetujui'] ?? 0"
        />
    </div>

    <div class="col-12 col-sm-6 col-lg-4">
        <x-dashboard-stat-card
            title="Pinjaman Dicairkan"
            :value="$data['pinjamanDicairkan'] ?? 0"
        />
    </div>

</div>

{{-- =========================================================
     PENGAJUAN MENUNGGU KEPUTUSAN KABAG
========================================================= --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">

        <div class="d-flex flex-column flex-md-row
                    justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h3 class="h5 fw-semibold text-dark mb-1">
                    Pengajuan Menunggu Keputusan
                </h3>

                <p class="small text-muted mb-0">
                    Periksa data pengajuan dan rekomendasi AO sebelum mengambil keputusan.
                </p>
            </div>

            <a href="{{ route('pinjaman.kabag.index') }}"
               class="btn btn-outline-primary btn-sm">
                Lihat Semua Pengajuan
                <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="small fw-semibold text-muted">No. Pinjaman</th>
                        <th class="small fw-semibold text-muted">Anggota</th>
                        <th class="small fw-semibold text-muted">Jenis Pinjaman</th>
                        <th class="small fw-semibold text-muted">Jumlah Pengajuan</th>
                        <th class="small fw-semibold text-muted">Rekomendasi AO</th>
                        <th class="small fw-semibold text-muted">Tenor</th>
                        <th class="small fw-semibold text-muted">Tanggal Pengajuan</th>
                        <th class="small fw-semibold text-muted">Status</th>
                        <th class="small fw-semibold text-muted text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse (($data['pengajuanKabag'] ?? collect()) as $pinjaman)
                        <tr>
                            <td class="small fw-semibold text-dark text-nowrap">
                                {{ $pinjaman->no_pinjaman }}
                            </td>

                            <td class="small text-dark">
                                {{ $pinjaman->anggota?->user?->nama ?? '-' }}
                            </td>

                            <td class="small text-muted">
                                {{ $pinjaman->masterPinjaman?->nama_pinjaman
                                    ?? $pinjaman->masterPinjaman?->nama
                                    ?? '-' }}
                            </td>

                            <td class="small text-nowrap">
                                Rp {{ number_format((float) ($pinjaman->jumlah_pinjaman ?? 0), 0, ',', '.') }}
                            </td>

                            <td class="small text-nowrap">
                                @if ($pinjaman->jumlah_rekomendasi !== null)
                                    <span class="fw-semibold text-dark">
                                        Rp {{ number_format((float) $pinjaman->jumlah_rekomendasi, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-muted">Belum tersedia</span>
                                @endif
                            </td>

                            <td class="small text-nowrap">
                                {{ $pinjaman->tenor ?? 0 }} bulan
                            </td>

                            <td class="small text-nowrap">
                                {{ $pinjaman->tanggal_pengajuan
                                    ? \Illuminate\Support\Carbon::parse($pinjaman->tanggal_pengajuan)->format('d/m/Y')
                                    : ($pinjaman->created_at?->format('d/m/Y') ?? '-') }}
                            </td>

                            <td>
                                <span class="badge bg-warning-subtle text-warning-emphasis">
                                    Menunggu Kabag
                                </span>
                            </td>

                            <td class="text-center">
                                <a href="{{ route('pinjaman.kabag.show', $pinjaman) }}"
                                   class="btn btn-primary btn-sm text-nowrap">
                                    <i class="bi bi-eye me-1"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-clipboard-check fs-3 text-muted d-block mb-2"></i>

                                <div class="small fw-semibold text-dark mb-1">
                                    Tidak Ada Pengajuan Menunggu Keputusan
                                </div>

                                <div class="small text-muted">
                                    Pengajuan yang telah dianalisis AO akan muncul di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>

{{-- =========================================================
     PINJAMAN DISETUJUI
========================================================= --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">

        <div class="d-flex flex-column flex-md-row
                    justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h3 class="h5 fw-semibold text-dark mb-1">
                    Pinjaman Disetujui
                </h3>

                <p class="small text-muted mb-0">
                    Pengajuan yang telah mendapatkan keputusan persetujuan Kabag
                    dan belum dicairkan.
                </p>
            </div>

            <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">
                {{ number_format($data['pinjamanDisetujui'] ?? 0, 0, ',', '.') }} pinjaman
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="small fw-semibold text-muted">No. Pinjaman</th>
                        <th class="small fw-semibold text-muted">Anggota</th>
                        <th class="small fw-semibold text-muted">Jumlah Disetujui</th>
                        <th class="small fw-semibold text-muted">Tenor</th>
                        <th class="small fw-semibold text-muted">Tanggal Pengajuan</th>
                        <th class="small fw-semibold text-muted text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse (($data['daftarPinjamanDisetujui'] ?? collect()) as $pinjaman)
                        <tr>
                            <td class="small fw-semibold text-dark text-nowrap">
                                {{ $pinjaman->no_pinjaman }}
                            </td>

                            <td class="small text-dark">
                                {{ $pinjaman->anggota?->user?->nama ?? '-' }}
                            </td>

                            <td class="small fw-semibold text-nowrap">
                                Rp {{ number_format((float) ($pinjaman->jumlah_disetujui ?? 0), 0, ',', '.') }}
                            </td>

                            <td class="small text-nowrap">
                                {{ $pinjaman->tenor ?? 0 }} bulan
                            </td>

                            <td class="small text-nowrap">
                                {{ $pinjaman->tanggal_pengajuan
                                    ? \Illuminate\Support\Carbon::parse($pinjaman->tanggal_pengajuan)->format('d/m/Y')
                                    : ($pinjaman->created_at?->format('d/m/Y') ?? '-') }}
                            </td>

                            <td class="text-center">
                                <a href="{{ route('pinjaman.kabag.show', $pinjaman) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <span class="small text-muted">
                                    Belum ada pinjaman yang disetujui dan menunggu pencairan.
                                </span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>

{{-- =========================================================
     PINJAMAN DICAIRKAN
========================================================= --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">

        <div class="d-flex flex-column flex-md-row
                    justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h3 class="h5 fw-semibold text-dark mb-1">
                    Pinjaman Telah Dicairkan
                </h3>

                <p class="small text-muted mb-0">
                    Pemantauan pinjaman yang telah dilanjutkan ke tahap pencairan.
                </p>
            </div>

            <span class="badge bg-info-subtle text-info-emphasis px-3 py-2">
                {{ number_format($data['pinjamanDicairkan'] ?? 0, 0, ',', '.') }} pinjaman
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="small fw-semibold text-muted">No. Pinjaman</th>
                        <th class="small fw-semibold text-muted">Anggota</th>
                        <th class="small fw-semibold text-muted">Jumlah Dicairkan</th>
                        <th class="small fw-semibold text-muted">Tenor</th>
                        <th class="small fw-semibold text-muted">Tanggal Pencairan</th>
                        <th class="small fw-semibold text-muted text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse (($data['daftarPinjamanDicairkan'] ?? collect()) as $pinjaman)
                        <tr>
                            <td class="small fw-semibold text-dark text-nowrap">
                                {{ $pinjaman->no_pinjaman }}
                            </td>

                            <td class="small text-dark">
                                {{ $pinjaman->anggota?->user?->nama ?? '-' }}
                            </td>

                            <td class="small fw-semibold text-nowrap">
                                Rp {{ number_format((float) ($pinjaman->jumlah_disetujui ?? 0), 0, ',', '.') }}
                            </td>

                            <td class="small text-nowrap">
                                {{ $pinjaman->tenor ?? 0 }} bulan
                            </td>

                            <td class="small text-nowrap">
                                {{ $pinjaman->tanggal_cair
                                    ? \Illuminate\Support\Carbon::parse($pinjaman->tanggal_cair)->format('d/m/Y')
                                    : '-' }}
                            </td>

                            <td class="text-center">
                                <a href="{{ route('pinjaman.kabag.show', $pinjaman) }}"
                                   class="btn btn-outline-primary btn-sm">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <span class="small text-muted">
                                    Belum ada pinjaman yang dicairkan.
                                </span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>
</div>

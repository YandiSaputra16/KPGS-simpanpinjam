
<div class="container-fluid">

    {{-- =========================================================
         RINGKASAN OPERASIONAL
    ========================================================== --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-sm-6 col-xl-3">
            <x-dashboard-stat-card
                title="Simpanan Menunggu Pencairan"
                :value="$data['menungguPencairanSimpanan'] ?? 0"
            />
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <x-dashboard-stat-card
                title="Simpanan Selesai Dicairkan"
                :value="$data['totalPencairanSimpananSelesai'] ?? 0"
            />
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <x-dashboard-stat-card
                title="Pinjaman Siap Dicairkan"
                :value="$data['pinjamanDisetujui'] ?? 0"
            />
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <x-dashboard-stat-card
                title="Pinjaman Sudah Dicairkan"
                :value="$data['pinjamanDicairkan'] ?? 0"
            />
        </div>

    </div>


    {{-- =========================================================
         PENCAIRAN SIMPANAN
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="fw-bold mb-1">Pencairan Simpanan</h5>
                    <p class="text-muted small mb-0">
                        Pengajuan yang telah disetujui Kabag dan siap dibayarkan.
                    </p>
                </div>

                <a
                    href="{{ route('pencairan-simpanan.bendahara.index') }}"
                    class="btn btn-outline-primary btn-sm"
                >
                    Lihat Semua
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-3">No. Pengajuan</th>
                        <th>Anggota</th>
                        <th>Jenis Simpanan</th>
                        <th class="text-end">Nominal</th>
                        <th>Tanggal Pengajuan</th>
                        <th class="text-center">Tindakan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($data['daftarPencairanSimpanan'] ?? [] as $item)
                        <tr>
                            <td class="ps-3 fw-semibold text-nowrap">
                                {{ $item->no_pengajuan }}
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
                                {{ $item->nama_jenis_simpanan }}
                            </td>

                            <td class="text-end text-nowrap fw-semibold">
                                Rp {{ number_format((float) $item->jumlah, 0, ',', '.') }}
                            </td>

                            <td class="text-nowrap">
                                {{ $item->created_at?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td class="text-center">
                                <a
                                    href="{{ route('pencairan-simpanan.bendahara.show', $item) }}"
                                    class="btn btn-sm btn-primary"
                                >
                                    <i class="bi bi-eye me-1"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-piggy-bank fs-2 text-muted d-block mb-2"></i>
                                <div class="fw-semibold">
                                    Tidak Ada Pencairan Simpanan
                                </div>
                                <div class="small text-muted">
                                    Pengajuan yang disetujui Kabag akan muncul di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>


    {{-- =========================================================
         PENCAIRAN PINJAMAN
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="fw-bold mb-1">Pencairan Pinjaman</h5>
                    <p class="text-muted small mb-0">
                        Pinjaman yang telah mendapatkan persetujuan Kabag.
                    </p>
                </div>

                <a
                    href="{{ route('pinjaman.bendahara.index') }}"
                    class="btn btn-outline-primary btn-sm"
                >
                    Lihat Semua
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-3">No. Pinjaman</th>
                        <th>Anggota</th>
                        <th class="text-end">Jumlah Disetujui</th>
                        <th>Tenor</th>
                        <th>Tanggal Pengajuan</th>
                        <th class="text-center">Tindakan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($data['pinjamanSiapDicairkan'] ?? [] as $pinjaman)
                        <tr>
                            <td class="ps-3 fw-semibold text-nowrap">
                                {{ $pinjaman->no_pinjaman }}
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $pinjaman->anggota?->user?->nama ?? '-' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $pinjaman->anggota?->kode_anggota ?? '-' }}
                                </div>
                            </td>

                            <td class="text-end text-nowrap fw-semibold">
                                Rp {{ number_format((float) $pinjaman->jumlah_disetujui, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ $pinjaman->tenor }} bulan
                            </td>

                            <td class="text-nowrap">
                                {{ $pinjaman->tanggal_pengajuan?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td class="text-center">
                                <a
                                    href="{{ route('pinjaman.bendahara.show', $pinjaman) }}"
                                    class="btn btn-sm btn-primary"
                                >
                                    <i class="bi bi-eye me-1"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-cash-stack fs-2 text-muted d-block mb-2"></i>
                                <div class="fw-semibold">
                                    Tidak Ada Pinjaman Siap Dicairkan
                                </div>
                                <div class="small text-muted">
                                    Pinjaman yang disetujui Kabag akan muncul di sini.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>


    {{-- =========================================================
         RIWAYAT PENCAIRAN SIMPANAN
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-1">Riwayat Pencairan Simpanan</h5>
            <p class="text-muted small mb-0">
                Lima transaksi pencairan simpanan terakhir.
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-3">No. Pengajuan</th>
                        <th>Anggota</th>
                        <th>Jenis Simpanan</th>
                        <th class="text-end">Nominal</th>
                        <th>Tanggal Pencairan</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($data['riwayatPencairanSimpanan'] ?? [] as $item)
                        <tr>
                            <td class="ps-3 fw-semibold">
                                {{ $item->no_pengajuan }}
                            </td>

                            <td>
                                {{ $item->anggota?->user?->nama ?? '-' }}
                            </td>

                            <td>
                                {{ $item->nama_jenis_simpanan }}
                            </td>

                            <td class="text-end text-nowrap">
                                Rp {{ number_format((float) $item->jumlah, 0, ',', '.') }}
                            </td>

                            <td class="text-nowrap">
                                {{ $item->tanggal_pencairan?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td>
                                <span class="badge text-bg-success">
                                    Selesai
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada riwayat pencairan simpanan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>


    {{-- =========================================================
         RIWAYAT PENCAIRAN PINJAMAN
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-1">Riwayat Pencairan Pinjaman</h5>
            <p class="text-muted small mb-0">
                Lima pinjaman yang terakhir dicairkan atau telah lunas.
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="ps-3">No. Pinjaman</th>
                        <th>Anggota</th>
                        <th class="text-end">Jumlah Disetujui</th>
                        <th>Tenor</th>
                        <th>Status</th>
                        <th class="text-center">Detail</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($data['riwayatPencairanPinjaman'] ?? [] as $pinjaman)
                        <tr>
                            <td class="ps-3 fw-semibold">
                                {{ $pinjaman->no_pinjaman }}
                            </td>

                            <td>
                                {{ $pinjaman->anggota?->user?->nama ?? '-' }}
                            </td>

                            <td class="text-end text-nowrap">
                                Rp {{ number_format((float) $pinjaman->jumlah_disetujui, 0, ',', '.') }}
                            </td>

                            <td>
                                {{ $pinjaman->tenor }} bulan
                            </td>

                            <td>
                                <span class="badge {{ $pinjaman->status === 'lunas' ? 'text-bg-success' : 'text-bg-primary' }}">
                                    {{ $pinjaman->status === 'lunas' ? 'Lunas' : 'Dicairkan' }}
                                </span>
                            </td>

                            <td class="text-center">
                                <a
                                    href="{{ route('pinjaman.bendahara.show', $pinjaman) }}"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada riwayat pencairan pinjaman.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>

</div>

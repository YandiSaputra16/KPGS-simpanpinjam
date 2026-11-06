{{-- Sambutan --}}

{{-- Ringkasan CS --}}
<div class="row g-4 mb-3">

    <div class="col-12 col-sm-6 col-lg-4">
        <x-dashboard-stat-card
            title="Anggota Aktif"
            :value="$data['totalAnggota'] ?? 0"
        />
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <x-dashboard-stat-card
            title="Menunggu Pemeriksaan"
            :value="$data['menungguCS'] ?? 0"
        />
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <x-dashboard-stat-card
            title="Pinjaman Aktif"
            :value="$data['pinjamanAktif'] ?? 0"
        />
    </div>

</div>

{{-- Pengajuan Menunggu Pemeriksaan --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-body p-4">

        <div class="mb-4">

            <h3 class="h5 fw-semibold text-dark mb-1">
                Pengajuan Menunggu Pemeriksaan CS
            </h3>

            <p class="text-muted small mb-0">
                Daftar pengajuan pinjaman yang perlu diperiksa dan diproses oleh Customer Service.
            </p>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th class="small fw-semibold text-muted">
                            No. Pinjaman
                        </th>

                        <th class="small fw-semibold text-muted">
                            Anggota
                        </th>

                        <th class="small fw-semibold text-muted">
                            Jumlah Pengajuan
                        </th>

                        <th class="small fw-semibold text-muted">
                            Tanggal Pengajuan
                        </th>

                        <th class="small fw-semibold text-muted">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse (($data['pengajuanCS'] ?? []) as $pinjaman)

                        <tr>

                            <td class="small fw-semibold text-dark">
                                {{ $pinjaman->no_pinjaman }}
                            </td>

                            <td class="small text-muted">
                                {{ $pinjaman->anggota?->user?->nama ?? '-' }}
                            </td>

                            <td class="small text-muted">
                                Rp {{ number_format($pinjaman->jumlah_pinjaman ?? 0, 0, ',', '.') }}
                            </td>

                            <td class="small text-muted">
                                {{ $pinjaman->created_at?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td>
                                <span class="badge bg-warning-subtle text-warning-emphasis">
                                    Menunggu CS
                                </span>
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="text-center py-4">

                                <p class="small fw-medium text-muted mb-1">
                                    Tidak ada pengajuan yang menunggu pemeriksaan.
                                </p>

                                <p class="small text-muted mb-0">
                                    Pengajuan baru dari anggota akan muncul di sini.
                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

{{-- PENGAJUAN PENCAIRAN SIMPANAN --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-body p-4">

        <div class="d-flex flex-column flex-md-row
                    justify-content-between align-items-md-center
                    gap-3 mb-4">

            <div>
                <h3 class="h5 fw-semibold text-dark mb-1">
                    Pengajuan Pencairan Simpanan
                </h3>

                <p class="text-muted small mb-0">
                    Daftar pengajuan pencairan simpanan anggota
                    yang menunggu pemeriksaan CS/Teller.
                </p>
            </div>

            <a href="{{ route('pencairan-simpanan.cs.index') }}"
               class="btn btn-outline-primary btn-sm">
                Lihat Semua Pengajuan
                <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th>No. Pengajuan</th>
                        <th>Anggota</th>
                        <th>Jenis Simpanan</th>
                        <th class="text-end">Jumlah Pencairan</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse (($data['pengajuanPencairan'] ?? []) as $item)

                        <tr>
                            <td class="fw-semibold text-nowrap">
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

                            <td class="text-end fw-semibold text-nowrap">
                                Rp {{ number_format((float) $item->jumlah, 0, ',', '.') }}
                            </td>

                            <td class="text-nowrap">
                                {{ $item->created_at?->format('d/m/Y H:i') ?? '-' }}
                            </td>

                            <td>
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    Menunggu CS
                                </span>
                            </td>

                            <td class="text-center">
                                <a href="{{ route('pencairan-simpanan.cs.index') }}"
                                   class="btn btn-sm btn-outline-primary text-nowrap">
                                    <i class="bi bi-eye me-1"></i>
                                    Periksa
                                </a>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-wallet2 text-muted d-block mb-3"
                                   style="font-size: 2.5rem;"></i>

                                <div class="fw-semibold text-dark mb-1">
                                    Belum Ada Pengajuan Pencairan
                                </div>

                                <div class="small text-muted">
                                    Pengajuan pencairan dari anggota
                                    akan muncul di sini setelah dikirim.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>


{{-- PENGAJUAN PEMBAYARAN ANGSURAN --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-body p-4">

        <div class="d-flex flex-column flex-md-row
                    justify-content-between align-items-md-center
                    gap-3 mb-4">

            <div>
                <h3 class="h5 fw-semibold text-dark mb-1">
                    Pengajuan Pembayaran Angsuran
                </h3>

                <p class="text-muted small mb-0">
                    Pembayaran angsuran anggota yang menunggu
                    pemeriksaan dan verifikasi CS/Teller.
                </p>
            </div>

            <a
                href="{{ route('angsuran.index') }}"
                class="btn btn-outline-primary btn-sm"
            >
                Lihat Semua Pembayaran
                <i class="bi bi-arrow-right ms-1"></i>
            </a>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th class="small text-muted">No. Pinjaman</th>
                        <th class="small text-muted">Anggota</th>
                        <th class="small text-muted">Angsuran</th>
                        <th class="small text-muted">Tanggal Bayar</th>
                        <th class="small text-muted text-end">Jumlah Bayar</th>
                        <th class="small text-muted">Status</th>
                        <th class="small text-muted text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse (($data['pengajuanAngsuran'] ?? []) as $item)

                        @php
                            $pinjaman = $item->pinjaman;
                            $anggota = $pinjaman?->anggota;

                            $jumlahBayar = (float) (
                                $item->jumlah_bayar
                                ?? $item->total_tagihan
                                ?? $item->total_angsuran
                                ?? 0
                            );
                        @endphp

                        <tr>

                            <td class="fw-semibold text-nowrap">
                                {{ $pinjaman?->no_pinjaman ?? '-' }}
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $anggota?->user?->nama ?? '-' }}
                                </div>

                                <div class="small text-muted">
                                    {{ $anggota?->kode_anggota ?? '-' }}
                                </div>
                            </td>

                            <td class="text-nowrap">
                                Angsuran ke-{{ $item->angsuran_ke }}
                            </td>

                            <td class="text-nowrap">
                                {{ $item->tanggal_bayar?->translatedFormat('d F Y') ?? '-' }}
                            </td>

                            <td class="text-end fw-semibold text-nowrap">
                                Rp {{ number_format($jumlahBayar, 0, ',', '.') }}
                            </td>

                            <td>
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    Menunggu Verifikasi
                                </span>
                            </td>

                            <td class="text-center">
                                <a
                                    href="{{ route('angsuran.show', $item) }}"
                                    class="btn btn-sm btn-outline-primary text-nowrap"
                                >
                                    <i class="bi bi-eye me-1"></i>
                                    Periksa
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="text-center py-5">

                                <i
                                    class="bi bi-receipt text-muted d-block mb-3"
                                    style="font-size: 2.5rem;"
                                ></i>

                                <div class="fw-semibold text-dark mb-1">
                                    Belum Ada Pengajuan Pembayaran
                                </div>

                                <div class="small text-muted">
                                    Pembayaran angsuran dari anggota
                                    akan muncul di sini setelah diajukan.
                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>
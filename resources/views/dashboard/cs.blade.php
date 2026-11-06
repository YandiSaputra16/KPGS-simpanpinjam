{{-- Sambutan --}}

{{-- Ringkasan CS --}}
<div class="row g-4 mb-4">

    <div class="col-12 col-sm-6 col-lg-4">
        <x-dashboard-stat-card
            title="Anggota Aktif"
            :value="$data['totalAnggota'] ?? 0"
        />
    </div>

    <div class="col-12 col-sm-6 col-lg-4">
        <x-dashboard-stat-card
            title="Menunggu Pemeriksaan"
            :value="$data['menungguCS'] ?? 0"
        />
    </div>

    <div class="col-12 col-sm-6 col-lg-4">
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


{{-- Akses Cepat --}}
<div class="card border-0 shadow-sm mt-4">

    <div class="card-body p-4">

        <div class="mb-4">
            <h3 class="h5 fw-semibold text-dark mb-1">
                Akses Cepat
            </h3>

            <p class="text-muted small mb-0">
                Pilih menu untuk mengakses fitur operasional KPGS.
            </p>
        </div>

        <div class="row g-3">

            {{-- Manajemen Anggota --}}
            <div class="col-12 col-md-6">
                <a href="{{ url('/anggota') }}"
                   class="text-decoration-none text-dark d-block h-100">

                    <div class="border rounded-3 p-3 h-100
                                d-flex align-items-center gap-3
                                transition"
                         style="transition: background-color .2s, border-color .2s;">

                        <div class="rounded-3 bg-primary bg-opacity-10
                                    text-primary d-flex align-items-center
                                    justify-content-center flex-shrink-0"
                             style="width: 48px; height: 48px;">
                            <i class="bi bi-people fs-4"></i>
                        </div>

                        <div class="flex-grow-1">
                            <div class="fw-semibold mb-1">
                                Manajemen Anggota
                            </div>

                            <div class="text-muted small">
                                Lihat dan kelola data anggota koperasi.
                            </div>
                        </div>

                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                </a>
            </div>

            {{-- Verifikasi Simpanan --}}
            <div class="col-12 col-md-6">
                <a href="{{ url('/simpanan') }}"
                   class="text-decoration-none text-dark d-block h-100">

                    <div class="border rounded-3 p-3 h-100
                                d-flex align-items-center gap-3"
                         style="transition: background-color .2s, border-color .2s;">

                        <div class="rounded-3 bg-success bg-opacity-10
                                    text-success d-flex align-items-center
                                    justify-content-center flex-shrink-0"
                             style="width: 48px; height: 48px;">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>

                        <div class="flex-grow-1">
                            <div class="fw-semibold mb-1">
                                Verifikasi Simpanan
                            </div>

                            <div class="text-muted small">
                                Periksa transaksi simpanan anggota.
                            </div>
                        </div>

                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                </a>
            </div>

            {{-- Verifikasi Angsuran --}}
            <div class="col-12 col-md-6">
                <a href="{{ url('/angsuran') }}"
                   class="text-decoration-none text-dark d-block h-100">

                    <div class="border rounded-3 p-3 h-100
                                d-flex align-items-center gap-3"
                         style="transition: background-color .2s, border-color .2s;">

                        <div class="rounded-3 bg-warning bg-opacity-10
                                    text-warning d-flex align-items-center
                                    justify-content-center flex-shrink-0"
                             style="width: 48px; height: 48px;">
                            <i class="bi bi-receipt fs-4"></i>
                        </div>

                        <div class="flex-grow-1">
                            <div class="fw-semibold mb-1">
                                Verifikasi Angsuran
                            </div>

                            <div class="text-muted small">
                                Periksa pembayaran angsuran pinjaman.
                            </div>
                        </div>

                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                </a>
            </div>

            {{-- Pengajuan Pinjaman --}}
            <div class="col-12 col-md-6">
                <a href="{{ url('/pinjaman/cs') }}"
                   class="text-decoration-none text-dark d-block h-100">

                    <div class="border rounded-3 p-3 h-100
                                d-flex align-items-center gap-3"
                         style="transition: background-color .2s, border-color .2s;">

                        <div class="rounded-3 bg-info bg-opacity-10
                                    text-info d-flex align-items-center
                                    justify-content-center flex-shrink-0"
                             style="width: 48px; height: 48px;">
                            <i class="bi bi-file-earmark-text fs-4"></i>
                        </div>

                        <div class="flex-grow-1">
                            <div class="fw-semibold mb-1">
                                Pengajuan Pinjaman
                            </div>

                            <div class="text-muted small">
                                Periksa dan proses pengajuan pinjaman.
                            </div>
                        </div>

                        <i class="bi bi-chevron-right text-muted"></i>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>

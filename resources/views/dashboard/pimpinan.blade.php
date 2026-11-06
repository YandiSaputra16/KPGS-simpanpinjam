{{-- Sambutan --}}

{{-- Ringkasan Pimpinan --}}
<div class="row g-4 mb-4">

    <div class="col-12 col-sm-6 col-xl-3">
        <x-dashboard-stat-card
            title="Total Anggota"
            :value="$data['totalAnggota'] ?? 0"
        />
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <x-dashboard-stat-card
            title="Pinjaman Aktif"
            :value="$data['pinjamanAktif'] ?? 0"
        />
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <x-dashboard-stat-card
            title="Total Simpanan"
            :value="'Rp ' . number_format($data['totalSimpanan'] ?? 0, 0, ',', '.')"
        />
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <x-dashboard-stat-card
            title="Saldo Kas"
            :value="'Rp ' . number_format($data['saldoKas'] ?? 0, 0, ',', '.')"
        />
    </div>

</div>

{{-- Monitoring Pinjaman --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-body p-4">

        <div class="mb-4">

            <h3 class="h5 fw-semibold text-dark mb-1">
                Monitoring Pinjaman
            </h3>

            <p class="text-muted small mb-0">
                Ringkasan posisi pengajuan dan pinjaman yang sedang berjalan.
            </p>

        </div>

        <div class="row g-3">

            <div class="col-12 col-sm-6 col-lg-4 col-xl">
                <div class="border rounded bg-light p-3 h-100">
                    <p class="small text-muted mb-2">
                        Menunggu CS
                    </p>

                    <p class="fs-3 fw-bold text-dark mb-0">
                        {{ $data['menungguCS'] ?? 0 }}
                    </p>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4 col-xl">
                <div class="border rounded bg-light p-3 h-100">
                    <p class="small text-muted mb-2">
                        Menunggu AO
                    </p>

                    <p class="fs-3 fw-bold text-dark mb-0">
                        {{ $data['menungguAO'] ?? 0 }}
                    </p>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4 col-xl">
                <div class="border rounded bg-light p-3 h-100">
                    <p class="small text-muted mb-2">
                        Menunggu Kabag
                    </p>

                    <p class="fs-3 fw-bold text-dark mb-0">
                        {{ $data['menungguKabag'] ?? 0 }}
                    </p>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4 col-xl">
                <div class="border rounded bg-light p-3 h-100">
                    <p class="small text-muted mb-2">
                        Disetujui
                    </p>

                    <p class="fs-3 fw-bold text-dark mb-0">
                        {{ $data['pinjamanDisetujui'] ?? 0 }}
                    </p>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-4 col-xl">
                <div class="border rounded bg-light p-3 h-100">
                    <p class="small text-muted mb-2">
                        Dicairkan
                    </p>

                    <p class="fs-3 fw-bold text-dark mb-0">
                        {{ $data['pinjamanDicairkan'] ?? 0 }}
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>

{{-- Fungsi Monitoring --}}
<div class="card border-0 shadow-sm">

    <div class="card-body p-4">

        <h3 class="h5 fw-semibold text-dark mb-4">
            Fungsi Pimpinan
        </h3>

        <div class="d-flex flex-column gap-3">

            <div class="d-flex align-items-start gap-3">

                <i class="bi bi-check-circle-fill text-primary mt-1"></i>

                <p class="small text-muted lh-lg mb-0">
                    Memantau perkembangan pengajuan dan pinjaman dalam sistem.
                </p>

            </div>

            <div class="d-flex align-items-start gap-3">

                <i class="bi bi-check-circle-fill text-primary mt-1"></i>

                <p class="small text-muted lh-lg mb-0">
                    Melihat kondisi simpanan dan kas koperasi.
                </p>

            </div>

            <div class="d-flex align-items-start gap-3">

                <i class="bi bi-check-circle-fill text-primary mt-1"></i>

                <p class="small text-muted lh-lg mb-0">
                    Mengakses laporan sebagai bahan monitoring dan evaluasi.
                </p>

            </div>

        </div>

    </div>

</div>
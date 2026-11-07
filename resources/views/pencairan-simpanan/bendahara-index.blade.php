
<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 fw-bold mb-1">Pencairan Simpanan</h2>
            <p class="text-muted small mb-0">
                Kelola pembayaran pengajuan pencairan simpanan yang telah disetujui Kabag.
            </p>
        </div>
    </x-slot>

    <div class="container-fluid py-4">

        {{-- NOTIFIKASI --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="fw-semibold mb-2">Pencairan belum dapat diproses:</div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- RINGKASAN --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="small text-muted mb-2">Menunggu Pencairan</div>
                                <div class="fs-3 fw-bold">
                                    {{ number_format($items->total(), 0, ',', '.') }}
                                </div>
                                <div class="small text-muted mt-1">
                                    Pengajuan yang telah disetujui Kabag
                                </div>
                            </div>
                            <div class="rounded-3 bg-primary-subtle text-primary p-3">
                                <i class="bi bi-wallet2 fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="small text-muted mb-2">Selesai Dicairkan</div>
                                <div class="fs-3 fw-bold">
                                    {{ number_format($riwayatPencairan->total(), 0, ',', '.') }}
                                </div>
                                <div class="small text-muted mt-1">
                                    Transaksi pencairan yang telah selesai
                                </div>
                            </div>
                            <div class="rounded-3 bg-success-subtle text-success p-3">
                                <i class="bi bi-check-circle fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="small text-muted mb-2">Total Nominal Menunggu</div>
                                <div class="fs-3 fw-bold">
                                    Rp {{ number_format((float) $totalMenunggu, 0, ',', '.') }}
                                </div>
                                <div class="small text-muted mt-1">
                                    Akumulasi pengajuan pada daftar pencairan
                                </div>
                            </div>
                            <div class="rounded-3 bg-warning-subtle text-warning p-3">
                                <i class="bi bi-cash-stack fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DAFTAR PENGAJUAN MENUNGGU PENCAIRAN --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1">Daftar Pengajuan Siap Dicairkan</h5>
                        <p class="text-muted small mb-0">
                            Pengajuan yang telah melewati pemeriksaan CS/Teller dan persetujuan Kabag.
                        </p>
                    </div>
                    <span class="badge bg-primary">
                        {{ number_format($items->total(), 0, ',', '.') }} Pengajuan
                    </span>
                </div>
            </div>

            @if ($items->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-3">No. Pengajuan</th>
                                <th class="py-3">Anggota</th>
                                <th class="py-3">Jenis Simpanan</th>
                                <th class="py-3 text-end">Jumlah</th>
                                <th class="py-3">Tanggal Pengajuan</th>
                                <th class="py-3 text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                @php
                                    $saldoAnggota = (float) (
                                        $item->anggota->{'saldo_simpanan_'.$item->jenis_simpanan} ?? 0
                                    );
                                @endphp

                                <tr>
                                    <td class="px-3">
                                        <div class="fw-semibold text-nowrap">
                                            {{ $item->no_pengajuan }}
                                        </div>
                                        <div class="small text-muted">
                                            Disetujui Kabag:
                                            {{ $item->disetujui_kabag_pada?->format('d/m/Y H:i') ?? '-' }}
                                        </div>
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
                                        <div>{{ $item->nama_jenis_simpanan }}</div>
                                        <div class="small text-muted">
                                            Saldo saat ini:
                                            Rp {{ number_format($saldoAnggota, 0, ',', '.') }}
                                        </div>
                                    </td>

                                    <td class="text-end text-nowrap fw-semibold">
                                        Rp {{ number_format((float) $item->jumlah, 0, ',', '.') }}
                                    </td>

                                    <td class="text-nowrap">
                                        {{ $item->created_at?->format('d/m/Y') ?? '-' }}
                                    </td>

                                    <td class="text-center">
                                        <button
                                            class="btn btn-sm btn-outline-primary"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#detail-pencairan-{{ $item->id }}"
                                            aria-expanded="false"
                                            aria-controls="detail-pencairan-{{ $item->id }}"
                                        >
                                            <i class="bi bi-eye me-1"></i>
                                            Tinjau & Cairkan
                                        </button>
                                    </td>
                                </tr>

                                {{-- DETAIL DAN FORM PENCAIRAN --}}
                                <tr>
                                    <td colspan="6" class="p-0 border-0">
                                        <div
                                            class="collapse"
                                            id="detail-pencairan-{{ $item->id }}"
                                        >
                                            <div class="bg-light border-top p-3 p-md-4">

                                                <div class="row g-3 mb-4">
                                                    <div class="col-12 col-lg-6">
                                                        <div class="card border-0 h-100">
                                                            <div class="card-body">
                                                                <h6 class="fw-bold mb-3">
                                                                    <i class="bi bi-person-vcard me-2"></i>
                                                                    Informasi Pengajuan
                                                                </h6>

                                                                <div class="row g-2 mb-2">
                                                                    <div class="col-5 small text-muted">
                                                                        Nomor Pengajuan
                                                                    </div>
                                                                    <div class="col-7 fw-semibold">
                                                                        {{ $item->no_pengajuan }}
                                                                    </div>
                                                                </div>

                                                                <div class="row g-2 mb-2">
                                                                    <div class="col-5 small text-muted">
                                                                        Nama Anggota
                                                                    </div>
                                                                    <div class="col-7">
                                                                        {{ $item->anggota?->user?->nama ?? '-' }}
                                                                    </div>
                                                                </div>

                                                                <div class="row g-2 mb-2">
                                                                    <div class="col-5 small text-muted">
                                                                        Kode Anggota
                                                                    </div>
                                                                    <div class="col-7">
                                                                        {{ $item->anggota?->kode_anggota ?? '-' }}
                                                                    </div>
                                                                </div>

                                                                <div class="row g-2 mb-2">
                                                                    <div class="col-5 small text-muted">
                                                                        Jenis Simpanan
                                                                    </div>
                                                                    <div class="col-7">
                                                                        {{ $item->nama_jenis_simpanan }}
                                                                    </div>
                                                                </div>

                                                                <div class="row g-2 mb-2">
                                                                    <div class="col-5 small text-muted">
                                                                        Saldo Saat Ini
                                                                    </div>
                                                                    <div class="col-7 fw-semibold">
                                                                        Rp {{ number_format($saldoAnggota, 0, ',', '.') }}
                                                                    </div>
                                                                </div>

                                                                <div class="row g-2 mb-2">
                                                                    <div class="col-5 small text-muted">
                                                                        Jumlah Pencairan
                                                                    </div>
                                                                    <div class="col-7 fw-bold text-primary">
                                                                        Rp {{ number_format((float) $item->jumlah, 0, ',', '.') }}
                                                                    </div>
                                                                </div>

                                                                <div class="row g-2">
                                                                    <div class="col-5 small text-muted">
                                                                        Alasan
                                                                    </div>
                                                                    <div class="col-7">
                                                                        {{ $item->alasan ?: '-' }}
                                                                    </div>
                                                                </div>

                                                                @if ($item->bukti)
                                                                    <div class="mt-3">
                                                                        <a
                                                                            href="{{ asset('storage/'.$item->bukti) }}"
                                                                            target="_blank"
                                                                            rel="noopener"
                                                                            class="btn btn-sm btn-outline-secondary"
                                                                        >
                                                                            <i class="bi bi-file-earmark-image me-1"></i>
                                                                            Lihat Bukti Pengajuan
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 col-lg-6">
                                                        <div class="card border-0 h-100">
                                                            <div class="card-body">
                                                                <h6 class="fw-bold mb-3">
                                                                    <i class="bi bi-clipboard-check me-2"></i>
                                                                    Informasi Persetujuan
                                                                </h6>

                                                                <div class="small text-muted mb-1">
                                                                    Status Pengajuan
                                                                </div>
                                                                <div class="mb-3">
                                                                    <span class="badge bg-primary">
                                                                        Disetujui - Menunggu Pencairan
                                                                    </span>
                                                                </div>

                                                                <div class="small text-muted mb-1">
                                                                    Disetujui oleh Kabag
                                                                </div>
                                                                <div class="mb-3">
                                                                    {{ $item->penyetujuKabag?->nama ?? '-' }}
                                                                </div>

                                                                <div class="small text-muted mb-1">
                                                                    Waktu Persetujuan
                                                                </div>
                                                                <div class="mb-3">
                                                                    {{ $item->disetujui_kabag_pada?->format('d/m/Y H:i') ?? '-' }}
                                                                </div>

                                                                <div class="small text-muted mb-1">
                                                                    Catatan Kabag
                                                                </div>
                                                                <div>
                                                                    {{ $item->catatan_kabag ?: 'Tidak ada catatan.' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- FORM PENCAIRAN --}}
                                                <div class="card border-0">
                                                    <div class="card-body">
                                                        <h6 class="fw-bold mb-1">
                                                            <i class="bi bi-cash-coin me-2"></i>
                                                            Proses Pembayaran
                                                        </h6>
                                                        <p class="small text-muted mb-3">
                                                            Pastikan pembayaran benar-benar telah diberikan kepada anggota sebelum mencatat pencairan.
                                                        </p>

                                                        <form
                                                            method="POST"
                                                            action="{{ route('pencairan-simpanan.bendahara.cairkan', $item) }}"
                                                            enctype="multipart/form-data"
                                                        >
                                                            @csrf

                                                            <div class="row g-3">
                                                                <div class="col-12 col-md-5">
                                                                    <label class="form-label fw-semibold">
                                                                        Tanggal Pembayaran
                                                                    </label>
                                                                    <input
                                                                        class="form-control"
                                                                        type="date"
                                                                        name="tanggal_pencairan"
                                                                        value="{{ old('tanggal_pencairan', now()->toDateString()) }}"
                                                                        max="{{ now()->toDateString() }}"
                                                                        required
                                                                    >
                                                                </div>

                                                                <div class="col-12 col-md-7">
                                                                    <label class="form-label fw-semibold">
                                                                        Bukti Pembayaran
                                                                        <span class="text-muted fw-normal">(Opsional)</span>
                                                                    </label>
                                                                    <input
                                                                        class="form-control"
                                                                        type="file"
                                                                        name="bukti_pencairan"
                                                                        accept="image/jpeg,image/png,image/webp"
                                                                    >
                                                                    <div class="form-text">
                                                                        Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="border-top mt-4 pt-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
                                                                <div>
                                                                    <div class="small text-muted">
                                                                        Nominal yang akan dicairkan
                                                                    </div>
                                                                    <div class="fs-5 fw-bold">
                                                                        Rp {{ number_format((float) $item->jumlah, 0, ',', '.') }}
                                                                    </div>
                                                                </div>

                                                                <button
                                                                    class="btn btn-primary"
                                                                    type="submit"
                                                                    onclick="return confirm('Pastikan uang benar-benar sudah dibayarkan kepada anggota. Lanjutkan pencatatan pencairan?')"
                                                                >
                                                                    <i class="bi bi-check-circle me-1"></i>
                                                                    Bayar & Catat Kas Keluar
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($items->hasPages())
                    <div class="card-body">
                        {{ $items->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @else
                <div class="text-center py-5 px-3">
                    <div class="mb-3">
                        <i class="bi bi-inbox text-secondary" style="font-size: 2.75rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Tidak Ada Pengajuan Menunggu Pencairan</h5>
                    <p class="text-muted mb-0">
                        Pengajuan yang telah disetujui Kabag akan ditampilkan di halaman ini.
                    </p>
                </div>
            @endif
        </div>

        {{-- RIWAYAT PENCAIRAN --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1">Riwayat Pencairan Simpanan</h5>
                        <p class="small text-muted mb-0">
                            Daftar pengajuan yang telah berhasil dibayarkan dan dicatat sebagai kas keluar.
                        </p>
                    </div>
                    <span class="badge bg-success">
                        {{ number_format($riwayatPencairan->total(), 0, ',', '.') }} Transaksi
                    </span>
                </div>
            </div>

            @if ($riwayatPencairan->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-3">No. Pengajuan</th>
                                <th class="py-3">Anggota</th>
                                <th class="py-3">Jenis Simpanan</th>
                                <th class="py-3 text-end">Jumlah</th>
                                <th class="py-3">Tanggal Pencairan</th>
                                <th class="py-3 text-center">Status</th>
                                <th class="py-3">Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($riwayatPencairan as $riwayat)
                                <tr>
                                    <td class="px-3 fw-semibold text-nowrap">
                                        {{ $riwayat->no_pengajuan }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ $riwayat->anggota?->user?->nama ?? '-' }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $riwayat->anggota?->kode_anggota ?? '-' }}
                                        </div>
                                    </td>
                                    <td>{{ $riwayat->nama_jenis_simpanan }}</td>
                                    <td class="text-end text-nowrap fw-semibold">
                                        Rp {{ number_format((float) $riwayat->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="text-nowrap">
                                        {{ $riwayat->tanggal_pencairan?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">Selesai</span>
                                    </td>
                                    <td>
                                        @if ($riwayat->bukti_pencairan)
                                            <a
                                                href="{{ asset('storage/'.$riwayat->bukti_pencairan) }}"
                                                target="_blank"
                                                rel="noopener"
                                                class="btn btn-sm btn-outline-secondary"
                                            >
                                                <i class="bi bi-image me-1"></i>
                                                Lihat
                                            </a>
                                        @else
                                            <span class="text-muted small">Tidak ada</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($riwayatPencairan->hasPages())
                    <div class="card-body">
                        {{ $riwayatPencairan->links('pagination::bootstrap-5', ['pageName' => 'riwayat']) }}
                    </div>
                @endif
            @else
                <div class="text-center py-5 px-3">
                    <div class="mb-3">
                        <i class="bi bi-clock-history text-secondary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Belum Ada Riwayat Pencairan</h5>
                    <p class="text-muted mb-0">
                        Transaksi yang telah selesai dicairkan akan ditampilkan di sini.
                    </p>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>

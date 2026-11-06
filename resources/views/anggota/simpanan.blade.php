<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="h4 fw-bold mb-1">
                Simpanan Saya
            </h2>

            <p class="text-muted mb-0">
                Lihat saldo dan riwayat pembayaran simpanan Anda.
            </p>
        </div>
    </x-slot>


    <div class="container-fluid py-4">

        <div class="container-xxl">

            {{-- ==========================================================
                NOTIFIKASI
            =========================================================== --}}

            @if (session('success'))
                <div
                    class="alert alert-success alert-dismissible fade show shadow-sm mb-4"
                    role="alert"
                >
                    <div class="fw-semibold">
                        {{ session('success') }}
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Tutup"
                    ></button>
                </div>
            @endif


            @if (session('error'))
                <div
                    class="alert alert-danger alert-dismissible fade show shadow-sm mb-4"
                    role="alert"
                >
                    <div class="fw-semibold">
                        {{ session('error') }}
                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Tutup"
                    ></button>
                </div>
            @endif


            {{-- ==========================================================
                HEADER HALAMAN
            =========================================================== --}}

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">

                <div>

                    <h1 class="h4 fw-bold mb-1">
                        Simpanan Saya
                    </h1>

                    <p class="text-muted mb-0">

                        {{ $anggota->user?->nama ?? '-' }}

                        <span class="mx-1">•</span>

                        {{ $anggota->kode_anggota ?? '-' }}

                    </p>

                </div>


                <div class="d-grid d-sm-block">

                    <a
                        href="{{ route('simpanan.pengajuan.create') }}"
                        class="btn btn-primary"
                    >
                        Ajukan Pembayaran
                    </a>

                </div>

            </div>


            {{-- ==========================================================
                SALDO SIMPANAN
            =========================================================== --}}

            <div class="row g-3 mb-4">

                {{-- Simpanan Pokok --}}
                <div class="col-12 col-md-4">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body p-4">

                            <div class="small text-muted mb-2">
                                Simpanan Pokok
                            </div>

                            <div class="fs-4 fw-bold text-break">

                                Rp
                                {{ number_format(
                                    (float) $anggota->saldo_simpanan_pokok,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Simpanan Wajib --}}
                <div class="col-12 col-md-4">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body p-4">

                            <div class="small text-muted mb-2">
                                Simpanan Wajib
                            </div>

                            <div class="fs-4 fw-bold text-break">

                                Rp
                                {{ number_format(
                                    (float) $anggota->saldo_simpanan_wajib,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </div>

                        </div>

                    </div>

                </div>


                {{-- Simpanan Sukarela --}}
                <div class="col-12 col-md-4">

                    <div class="card border-0 shadow-sm h-100">

                        <div class="card-body p-4">

                            <div class="small text-muted mb-2">
                                Simpanan Sukarela
                            </div>

                            <div class="fs-4 fw-bold text-break">

                                Rp
                                {{ number_format(
                                    (float) $anggota->saldo_simpanan_sukarela,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ==========================================================
                RIWAYAT TRANSAKSI
            =========================================================== --}}

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-bottom py-3 px-4">

                    <h5 class="fw-bold mb-1">
                        Riwayat Simpanan
                    </h5>

                    <p class="text-muted small mb-0">
                        Riwayat pengajuan dan transaksi simpanan Anda.
                    </p>

                </div>

                @if ($simpanan->count())


                    {{-- ==================================================
                        DESKTOP / TABLET
                    =================================================== --}}

                    <div class="d-none d-md-block">

                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th class="px-4 py-3 text-nowrap">
                                            No. Pengajuan
                                        </th>

                                        <th class="py-3">
                                            Jenis
                                        </th>

                                        <th class="py-3 text-nowrap">
                                            Tanggal
                                        </th>

                                        <th class="py-3 text-end text-nowrap">
                                            Jumlah
                                        </th>

                                        <th class="py-3">
                                            Metode
                                        </th>

                                        <th class="py-3 text-center">
                                            Status
                                        </th>

                                        <th class="px-4 py-3 text-end">
                                            Aksi
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @foreach ($simpanan as $item)

                                        <tr>

                                            {{-- Nomor Pengajuan --}}
                                            <td class="px-4">

                                                <span class="fw-semibold text-nowrap">
                                                    {{ $item->no_simpanan }}
                                                </span>

                                            </td>


                                            {{-- Jenis --}}
                                            <td>

                                                {{ $item->jenisSimpanan?->nama_jenis ?? '-' }}

                                            </td>


                                            {{-- Tanggal --}}
                                            <td class="text-nowrap text-muted">

                                                {{ $item->tanggal_transaksi?->translatedFormat('d F Y') ?? '-' }}

                                            </td>


                                            {{-- Jumlah --}}
                                            <td class="text-end text-nowrap">

                                                <span class="fw-bold">

                                                    Rp
                                                    {{ number_format(
                                                        (float) $item->jumlah,
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) }}

                                                </span>

                                            </td>


                                            {{-- Metode --}}
                                            <td class="text-capitalize">

                                                {{ $item->metode_pembayaran ?? '-' }}

                                            </td>


                                            {{-- Status --}}
                                            <td class="text-center">

                                                @if ($item->status === 'pending')

                                                    <span class="badge bg-warning text-dark">
                                                        Sedang Diverifikasi
                                                    </span>

                                                @elseif ($item->status === 'disetujui')

                                                    <span class="badge bg-success">
                                                        Berhasil Diverifikasi
                                                    </span>

                                                @elseif ($item->status === 'ditolak')

                                                    <span class="badge bg-danger">
                                                        Perlu Diperbaiki
                                                    </span>

                                                @else

                                                    <span class="badge bg-secondary">
                                                        {{ ucfirst($item->status ?? '-') }}
                                                    </span>

                                                @endif

                                            </td>


                                            {{-- Aksi --}}
                                            <td class="px-4 text-end">

                                                <a
                                                    href="{{ route('simpanan.saya.show', $item) }}"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    Lihat Detail
                                                </a>

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>


                    {{-- ==================================================
                        MOBILE
                    =================================================== --}}

                    <div class="d-md-none">

                        @foreach ($simpanan as $item)

                            <div class="border-bottom">

                                <div class="p-4">

                                    {{-- Header Card --}}
                                    <div class="d-flex justify-content-between align-items-start gap-3">

                                        <div class="min-w-0">

                                            <div class="fw-bold text-break">
                                                {{ $item->no_simpanan }}
                                            </div>

                                            <div class="small text-muted mt-1">
                                                {{ $item->jenisSimpanan?->nama_jenis ?? '-' }}
                                            </div>

                                        </div>


                                        <div class="flex-shrink-0">

                                            @if ($item->status === 'pending')

                                                <span class="badge bg-warning text-dark">
                                                    Diverifikasi
                                                </span>

                                            @elseif ($item->status === 'disetujui')

                                                <span class="badge bg-success">
                                                    Berhasil
                                                </span>

                                            @elseif ($item->status === 'ditolak')

                                                <span class="badge bg-danger">
                                                    Diperbaiki
                                                </span>

                                            @else

                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($item->status ?? '-') }}
                                                </span>

                                            @endif

                                        </div>

                                    </div>


                                    {{-- Informasi --}}
                                    <div class="row g-3 mt-2">

                                        <div class="col-6">

                                            <div class="small text-muted mb-1">
                                                Tanggal
                                            </div>

                                            <div class="small fw-semibold">

                                                {{ $item->tanggal_transaksi?->translatedFormat('d F Y') ?? '-' }}

                                            </div>

                                        </div>


                                        <div class="col-6">

                                            <div class="small text-muted mb-1">
                                                Metode
                                            </div>

                                            <div class="small fw-semibold text-capitalize">

                                                {{ $item->metode_pembayaran ?? '-' }}

                                            </div>

                                        </div>


                                        <div class="col-12">

                                            <div class="small text-muted mb-1">
                                                Jumlah
                                            </div>

                                            <div class="fs-5 fw-bold">

                                                Rp
                                                {{ number_format(
                                                    (float) $item->jumlah,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}

                                            </div>

                                        </div>

                                    </div>


                                    {{-- Status Detail --}}
                                    @if ($item->status === 'pending')

                                        <div class="alert alert-warning small mt-3 mb-0">

                                            Pembayaran sedang dalam proses verifikasi.

                                        </div>

                                    @elseif ($item->status === 'disetujui')

                                        <div class="alert alert-success small mt-3 mb-0">

                                            Pembayaran telah berhasil diverifikasi
                                            dan tercatat sebagai pembayaran yang sah.

                                        </div>

                                    @elseif ($item->status === 'ditolak')

                                        <div class="alert alert-danger small mt-3 mb-0">

                                            Pembayaran belum dapat diverifikasi.
                                            Silakan periksa kembali bukti pembayaran
                                            dan lakukan pengajuan ulang.

                                        </div>

                                    @endif


                                    {{-- Detail --}}
                                    <div class="d-grid mt-3">

                                        <a
                                            href="{{ route('simpanan.saya.show', $item) }}"
                                            class="btn btn-outline-primary"
                                        >
                                            Lihat Detail
                                        </a>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>


                    {{-- ==================================================
                        PAGINATION
                    =================================================== --}}

                    @if ($simpanan->hasPages())

                        <div class="border-top px-4 py-3">

                            <div class="d-flex justify-content-center">

                                {{ $simpanan->links('pagination::bootstrap-5') }}

                            </div>

                        </div>

                    @endif


                @else


                    {{-- ==================================================
                        EMPTY STATE
                    =================================================== --}}

                    <div class="p-5 text-center">

                        <h3 class="h5 fw-bold mb-2">
                            Belum Ada Riwayat Simpanan
                        </h3>

                        <p class="text-muted small mb-4">
                            Anda belum memiliki transaksi atau pengajuan
                            pembayaran simpanan.
                        </p>

                        <a
                            href="{{ route('simpanan.pengajuan.create') }}"
                            class="btn btn-primary"
                        >
                            Ajukan Pembayaran
                        </a>

                    </div>


                @endif

            </div>


        {{-- DAFTAR PENCAIRAN SIMPANAN MILIK ANGGOTA --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white py-3 px-4">
                <h5 class="fw-bold mb-1">Riwayat Pencairan Simpanan</h5>
                <p class="text-muted small mb-0">Riwayat pengajuan pencairan simpanan Anda.</p>
            </div>
                        {{-- Pencairan Simpanan --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row
                                justify-content-between align-items-md-center gap-3">
                        <div>
                            <h3 class="h5 fw-semibold text-dark mb-2">
                                Pencairan Simpanan
                            </h3>

                            <p class="text-muted small mb-0">
                                Ajukan pencairan simpanan  dan pengajuan akan diproses oleh petugas KPGS secara bertahap. Status pengajuan akan diperbarui setelah proses pemeriksaan selesai.
                            </p>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a
                                href="{{ route('pencairan-simpanan.saya.create') }}"
                                class="btn btn-primary"
                            >
                                Ajukan Pencairan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4">No. Pengajuan</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Jenis Simpanan</th>
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
                                    <td class="text-nowrap">{{ $pencairan->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td>{{ $pencairan->nama_jenis_simpanan }}</td>
                                    <td class="text-end text-nowrap">Rp {{ number_format((float) $pencairan->jumlah, 0, ',', '.') }}</td>
                                    <td><span class="badge {{ $statusPencairan[1] }}">{{ $statusPencairan[0] }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Anda belum memiliki pengajuan pencairan simpanan.</td>
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

</x-app-layout>

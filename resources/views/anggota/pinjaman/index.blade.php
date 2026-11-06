<x-app-layout>

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-1 fw-bold">Pinjaman Saya</h2>
                <p class="text-muted mb-0 small">
                    Kelola dan pantau pengajuan pinjaman Anda.
                </p>
            </div>

    </x-slot>

    <div class="container-fluid py-4">

        {{-- ALERT SUCCESS --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Berhasil.</strong>
                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"></button>
            </div>
        @endif

        {{-- ALERT ERROR --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal.</strong>
                {{ session('error') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"></button>
            </div>
        @endif

        {{-- VALIDATION ERROR --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">

                <div class="fw-bold mb-2">
                    Terdapat kesalahan:
                </div>

                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"></button>

            </div>
        @endif


        {{-- INFORMASI PENGAJUAN --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <div class="row align-items-center">

                    <div class="col-lg-9">

                        <h5 class="fw-bold mb-2">
                            Informasi Pengajuan Pinjaman
                        </h5>

                        <p class="text-muted mb-0">
                            Setelah pengajuan dikirim, pinjaman akan diproses
                            oleh petugas KPGS secara bertahap. Status pengajuan
                            akan diperbarui setelah proses pemeriksaan selesai.
                        </p>

                    </div>

                    <div class="col-lg-3 text-lg-end mt-3 mt-lg-0">

                        <a href="{{ route('pinjaman.create') }}"
                           class="btn btn-primary">
                            Ajukan Pinjaman
                        </a>

                    </div>

                </div>

            </div>

        </div>


        {{-- DAFTAR PINJAMAN --}}
        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white border-bottom py-3">

                <h5 class="fw-bold mb-1">
                    Pinjaman Saya
                </h5>

                <p class="text-muted small mb-0">
                    Informasi pengajuan, pinjaman, dan jadwal angsuran Anda.
                </p>

            </div>


            @if ($pinjaman->count() > 0)

                @foreach ($pinjaman as $item)

                    @php

                        $status = $item->status;

                        $statusLabel = match ($status) {

                            'menunggu_cs'
                                => 'Menunggu Pemeriksaan CS',

                            'menunggu_ao'
                                => 'Menunggu Analisis AO',

                            'menunggu_kabag'
                                => 'Menunggu Persetujuan Kabag',

                            'disetujui'
                                => 'Disetujui',

                            'dicairkan'
                                => 'Telah Dicairkan',

                            'lunas'
                                => 'Lunas',

                            'ditolak'
                                => 'Ditolak',

                            default
                                => ucwords(str_replace('_', ' ', $status ?? '-')),

                        };


                        $statusClass = match ($status) {

                            'menunggu_cs',
                            'menunggu_ao',
                            'menunggu_kabag'
                                => 'bg-warning text-dark',

                            'disetujui'
                                => 'bg-success',

                            'dicairkan'
                                => 'bg-primary',

                            'lunas'
                                => 'bg-success',

                            'ditolak'
                                => 'bg-danger',

                            default
                                => 'bg-secondary',

                        };


                        $jumlahPinjaman =
                            $item->jumlah_disetujui
                            ?? $item->jumlah_pinjaman
                            ?? 0;


                        $totalKewajiban =
                            ($item->jumlah_disetujui ?? $item->jumlah_pinjaman ?? 0)
                            + ($item->total_bunga ?? 0);


                        $totalAngsuran =
                            $item->angsuran?->sum('total_angsuran')
                            ?? 0;


                        $sisaPinjaman =
                            max(0, $totalKewajiban - $totalAngsuran);

                    @endphp


                    {{-- PINJAMAN --}}
                    <div class="border-bottom">

                        {{-- HEADER PINJAMAN --}}
                        <div class="px-3 px-md-4 py-3">

                            <div class="row align-items-center">

                                <div class="col-md-8">

                                    <div class="small text-muted mb-1">
                                        No. Pinjaman
                                    </div>

                                    <div class="h5 fw-bold mb-1">
                                        {{ $item->no_pinjaman }}
                                    </div>

                                    <div class="text-muted small">
                                        {{ $item->masterPinjaman?->nama_paket ?? 'Paket Pinjaman' }}
                                    </div>

                                </div>

                                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                                    <span class="badge {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>

                                </div>

                            </div>

                        </div>


                        {{-- INFORMASI UTAMA --}}
                        <div class="px-3 px-md-4 pb-4">

                            <div class="row g-3">

                                {{-- JUMLAH --}}
                                <div class="col-12 col-md-6 col-xl-3">

                                    <div class="border rounded p-3 h-100">

                                        <div class="small text-muted mb-1">
                                            Jumlah Pinjaman
                                        </div>

                                        <div class="fw-bold">
                                            Rp {{ number_format($jumlahPinjaman, 0, ',', '.') }}
                                        </div>

                                    </div>

                                </div>


                                {{-- TENOR --}}
                                <div class="col-12 col-md-6 col-xl-3">

                                    <div class="border rounded p-3 h-100">

                                        <div class="small text-muted mb-1">
                                            Tenor
                                        </div>

                                        <div class="fw-bold">
                                            {{ $item->tenor ?? '-' }} bulan
                                        </div>

                                    </div>

                                </div>


                                {{-- ANGSURAN --}}
                                <div class="col-12 col-md-6 col-xl-3">

                                    <div class="border rounded p-3 h-100">

                                        <div class="small text-muted mb-1">
                                            Angsuran per Bulan
                                        </div>

                                        <div class="fw-bold">
                                            Rp {{ number_format($item->angsuran_per_bulan ?? 0, 0, ',', '.') }}
                                        </div>

                                    </div>

                                </div>


                                {{-- TANGGAL PENGAJUAN --}}
                                <div class="col-12 col-md-6 col-xl-3">

                                    <div class="border rounded p-3 h-100">

                                        <div class="small text-muted mb-1">
                                            Tanggal Pengajuan
                                        </div>

                                        <div class="fw-bold">

                                            @if ($item->tanggal_pengajuan)
                                                {{ optional($item->tanggal_pengajuan)->format('d/m/Y') }}
                                            @else
                                                -
                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </div>


                            {{-- INFORMASI KEUANGAN --}}
                            <div class="row g-4 mt-1">

                                {{-- TOTAL BUNGA --}}
                                <div class="col-12 col-md-4">

                                    <div class="small text-muted mb-1">
                                        Total Bunga
                                    </div>

                                    <div class="fw-semibold">
                                        Rp {{ number_format($item->total_bunga ?? 0, 0, ',', '.') }}
                                    </div>

                                </div>


                                {{-- TOTAL KEWAJIBAN --}}
                                <div class="col-12 col-md-4">

                                    <div class="small text-muted mb-1">
                                        Total Kewajiban
                                    </div>

                                    <div class="fw-semibold">
                                        Rp {{ number_format($totalKewajiban, 0, ',', '.') }}
                                    </div>

                                </div>


                                {{-- SISA PINJAMAN --}}
                                <div class="col-12 col-md-4">

                                    <div class="small text-muted mb-1">
                                        Sisa Pinjaman
                                    </div>

                                    <div class="fw-semibold">
                                        Rp {{ number_format($sisaPinjaman, 0, ',', '.') }}
                                    </div>

                                </div>

                            </div>


                            {{-- TANGGAL PENCAIRAN --}}
                            @if ($item->tanggal_pencairan)

                                <div class="row mt-3">

                                    <div class="col-md-4">

                                        <div class="small text-muted mb-1">
                                            Tanggal Pencairan
                                        </div>

                                        <div class="fw-semibold">
                                            {{ optional($item->tanggal_pencairan)->format('d/m/Y') }}
                                        </div>

                                    </div>

                                </div>

                            @endif


                            {{-- TUJUAN PINJAMAN --}}
                            @if ($item->tujuan_pinjaman)

                                <div class="border rounded p-3 mt-4">

                                    <div class="small text-muted mb-1">
                                        Tujuan Pinjaman
                                    </div>

                                    <div>
                                        {{ $item->tujuan_pinjaman }}
                                    </div>

                                </div>

                            @endif


                            {{-- CATATAN --}}
                            @if ($item->keterangan)

                                <div class="border rounded p-3 mt-3">

                                    <div class="small text-muted mb-1">
                                        Keterangan
                                    </div>

                                    <div>
                                        {{ $item->keterangan }}
                                    </div>

                                </div>

                            @endif


                            {{-- DATA PENDUKUNG --}}
                            @if ($item->dataPendukung && $item->dataPendukung->count() > 0)

                                <div class="mt-4">

                                    <h6 class="fw-bold mb-3">
                                        Data Pendukung
                                    </h6>

                                    <div class="row g-3">

                                        @foreach ($item->dataPendukung as $data)

                                            <div class="col-12 col-md-6">

                                                <div class="border rounded p-3">

                                                    <div class="small text-muted mb-1">
                                                        {{ $data->nama_dokumen ?? 'Dokumen' }}
                                                    </div>

                                                    @if ($data->file ?? null)

                                                        <a href="{{ asset('storage/' . $data->file) }}"
                                                           target="_blank"
                                                           class="btn btn-sm btn-outline-primary">
                                                            Lihat Dokumen
                                                        </a>

                                                    @else

                                                        <span class="text-muted small">
                                                            Dokumen tidak tersedia
                                                        </span>

                                                    @endif

                                                </div>

                                            </div>

                                        @endforeach

                                    </div>

                                </div>

                            @endif


                            {{-- JADWAL ANGSURAN --}}
                            @if (
                                in_array($status, [
                                    'disetujui',
                                    'dicairkan',
                                    'lunas'
                                ])
                                && $item->angsuran
                                && $item->angsuran->count() > 0
                            )

                                <div class="card bg-light border mt-4">

                                    <div class="card-header bg-transparent">

                                        <div class="d-flex justify-content-between align-items-center">

                                            <div>

                                                <h6 class="fw-bold mb-1">
                                                    Jadwal Angsuran
                                                </h6>

                                                <div class="small text-muted">
                                                    Jadwal dan status pembayaran angsuran pinjaman.
                                                </div>

                                            </div>

                                            <a href="{{ route('angsuran.saya.index') }}"
                                               class="btn btn-sm btn-outline-primary">
                                                Lihat Angsuran
                                            </a>

                                        </div>

                                    </div>


                                    <div class="table-responsive">

                                        <table class="table table-sm table-hover align-middle mb-0">

                                            <thead class="table-light">

                                                <tr>

                                                    <th class="px-3">
                                                        Angsuran
                                                    </th>

                                                    <th>
                                                        Jatuh Tempo
                                                    </th>

                                                    <th>
                                                        Pokok
                                                    </th>

                                                    <th>
                                                        Bunga
                                                    </th>

                                                    <th>
                                                        Total
                                                    </th>

                                                    <th>
                                                        Status
                                                    </th>

                                                </tr>

                                            </thead>


                                            <tbody>

                                                @foreach ($item->angsuran as $angsuran)

                                                    @php

                                                        $statusAngsuran =
                                                            $angsuran->status_pembayaran ?? 'belum_lunas';

                                                        $labelAngsuran = match ($statusAngsuran) {

                                                            'lunas'
                                                                => 'Lunas',

                                                            'menunggu_verifikasi'
                                                                => 'Menunggu Verifikasi',

                                                            'ditolak'
                                                                => 'Ditolak',

                                                            default
                                                                => 'Belum Lunas',

                                                        };


                                                        $classAngsuran = match ($statusAngsuran) {

                                                            'lunas'
                                                                => 'bg-success',

                                                            'menunggu_verifikasi'
                                                                => 'bg-warning text-dark',

                                                            'ditolak'
                                                                => 'bg-danger',

                                                            default
                                                                => 'bg-secondary',

                                                        };

                                                    @endphp


                                                    <tr>

                                                        <td class="px-3">
                                                            Angsuran ke-{{ $angsuran->angsuran_ke }}
                                                        </td>


                                                        <td>

                                                            @if ($angsuran->tanggal_jatuh_tempo)
                                                                {{ optional($angsuran->tanggal_jatuh_tempo)->format('d/m/Y') }}
                                                            @else
                                                                -
                                                            @endif

                                                        </td>


                                                        <td>
                                                            Rp {{ number_format($angsuran->jumlah_pokok ?? 0, 0, ',', '.') }}
                                                        </td>


                                                        <td>
                                                            Rp {{ number_format($angsuran->jumlah_bunga ?? 0, 0, ',', '.') }}
                                                        </td>


                                                        <td>
                                                            <span class="fw-semibold">
                                                                Rp {{ number_format($angsuran->total_angsuran ?? 0, 0, ',', '.') }}
                                                            </span>
                                                        </td>


                                                        <td>

                                                            <span class="badge {{ $classAngsuran }}">
                                                                {{ $labelAngsuran }}
                                                            </span>

                                                        </td>

                                                    </tr>

                                                @endforeach

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            @elseif (
                                in_array($status, [
                                    'disetujui',
                                    'dicairkan',
                                    'lunas'
                                ])
                            )

                                <div class="card bg-light border mt-4">

                                    <div class="card-body">

                                        <div class="d-flex justify-content-between align-items-center">

                                            <div>

                                                <h6 class="fw-bold mb-1">
                                                    Jadwal Angsuran
                                                </h6>

                                                <div class="small text-muted">
                                                    Jadwal angsuran belum tersedia.
                                                </div>

                                            </div>

                                            <a href="{{ route('angsuran.saya.index') }}"
                                               class="btn btn-sm btn-outline-primary">
                                                Lihat Angsuran
                                            </a>

                                        </div>

                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                @endforeach

            @else

                {{-- EMPTY STATE --}}
                <div class="text-center py-5 px-3">

                    <h5 class="fw-bold mb-2">
                        Belum Ada Pinjaman
                    </h5>

                    <p class="text-muted mb-4">
                        Anda belum memiliki pengajuan pinjaman.
                    </p>

                    <a href="{{ route('pinjaman.create') }}"
                       class="btn btn-primary">
                        Ajukan Pinjaman
                    </a>

                </div>

            @endif

        </div>

    </div>

</x-app-layout>
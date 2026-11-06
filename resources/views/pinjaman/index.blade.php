

<x-app-layout>

    <x-slot name="header">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                @if (auth()->user()->role === 'cs')

                    <h2 class="h4 mb-1 fw-bold">Pengajuan Pinjaman</h2>

                    <p class="text-muted mb-0 small">

                        Kelola dan periksa pengajuan pinjaman anggota.

                    </p>

                @elseif (auth()->user()->role === 'ao')

                    <h2 class="h4 mb-1 fw-bold">

                        {{ ($filter ?? request('filter', 'aktif')) === 'riwayat' ? 'Riwayat Penanganan Pinjaman' : 'Analisis Pinjaman' }}

                    </h2>

                    <p class="text-muted mb-0 small">

                        {{ ($filter ?? request('filter', 'aktif')) === 'riwayat'

                            ? 'Lihat pengajuan yang pernah Anda rekomendasikan atau tolak.'

                            : 'Tinjau pengajuan yang menunggu analisis dan rekomendasi Anda.' }}

                    </p>

                @elseif (auth()->user()->role === 'kabag')

                    <h2 class="h4 mb-1 fw-bold">
                        {{ ($filter ?? request('filter', 'aktif')) === 'riwayat' ? 'Riwayat Persetujuan Pinjaman' : 'Persetujuan Pinjaman' }}
                    </h2>

                    <p class="text-muted mb-0 small">
                        {{ ($filter ?? request('filter', 'aktif')) === 'riwayat'
                            ? 'Lihat keputusan persetujuan dan penolakan yang telah Anda lakukan.'
                            : 'Tinjau rekomendasi AO dan tentukan keputusan akhir pengajuan pinjaman.' }}
                    </p>

                @elseif (auth()->user()->role === 'bendahara')

                    <h2 class="h4 mb-1 fw-bold">Pemantauan Simpan Pinjam</h2>

                    <p class="text-muted mb-0 small">

                        Pantau status pengajuan pinjaman dan tindak lanjuti pencairan pinjaman yang telah disetujui.

                    </p>

                @else

                    <h2 class="h4 mb-1 fw-bold">Daftar Pinjaman</h2>

                    <p class="text-muted mb-0 small">

                        Daftar seluruh pengajuan pinjaman.

                    </p>

                @endif

            </div>

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

                <div class="fw-bold mb-2">Terdapat kesalahan:</div>

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

        {{-- INFORMASI ROLE --}}

        @if (auth()->user()->role === 'cs')

        @elseif (auth()->user()->role === 'ao')

        @elseif (auth()->user()->role === 'bendahara')
        @endif

        {{-- NAVIGASI KHUSUS AO: AKTIF DAN RIWAYAT --}}

        @if (auth()->user()->role === 'ao')

            @php

                $filterAO = in_array(($filter ?? request('filter', 'aktif')), ['aktif', 'riwayat'], true)

                    ? ($filter ?? request('filter', 'aktif'))

                    : 'aktif';

            @endphp

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body p-2">

                    <ul class="nav nav-pills nav-fill gap-2">

                        <li class="nav-item">

                            <a class="nav-link {{ $filterAO === 'aktif' ? 'active' : 'text-body' }}"

                               href="{{ request()->fullUrlWithQuery(['filter' => 'aktif']) }}"

                               @if ($filterAO === 'aktif') aria-current="page" @endif>

                                <i class="bi bi-clipboard-check me-1"></i>

                                Pengajuan Aktif

                            </a>

                        </li>

                        <li class="nav-item">

                            <a class="nav-link {{ $filterAO === 'riwayat' ? 'active' : 'text-body' }}"

                               href="{{ request()->fullUrlWithQuery(['filter' => 'riwayat']) }}"

                               @if ($filterAO === 'riwayat') aria-current="page" @endif>

                                <i class="bi bi-clock-history me-1"></i>

                                Riwayat Penanganan

                            </a>

                        </li>

                    </ul>

                </div>

            </div>

        @endif

        {{-- NAVIGASI KHUSUS KABAG: PERSETUJUAN DAN RIWAYAT --}}
        @if (auth()->user()->role === 'kabag')
            @php
                $filterKabag = in_array(($filter ?? request('filter', 'aktif')), ['aktif', 'riwayat'], true)
                    ? ($filter ?? request('filter', 'aktif'))
                    : 'aktif';
            @endphp

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-2">
                    <ul class="nav nav-pills nav-fill gap-2">
                        <li class="nav-item">
                            <a
                                class="nav-link {{ $filterKabag === 'aktif' ? 'active' : 'text-body' }}"
                                href="{{ route('pinjaman.kabag.index', ['filter' => 'aktif']) }}"
                                @if ($filterKabag === 'aktif') aria-current="page" @endif
                            >
                                <i class="bi bi-clipboard-check me-1"></i>
                                Persetujuan
                                <span class="badge {{ $filterKabag === 'aktif' ? 'bg-light text-primary' : 'bg-secondary' }} ms-1">
                                    {{ $jumlahMenunggu ?? 0 }}
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link {{ $filterKabag === 'riwayat' ? 'active' : 'text-body' }}"
                                href="{{ route('pinjaman.kabag.index', ['filter' => 'riwayat']) }}"
                                @if ($filterKabag === 'riwayat') aria-current="page" @endif
                            >
                                <i class="bi bi-clock-history me-1"></i>
                                Riwayat Persetujuan
                                <span class="badge {{ $filterKabag === 'riwayat' ? 'bg-light text-primary' : 'bg-secondary' }} ms-1">
                                    {{ $jumlahRiwayat ?? 0 }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        @endif

        {{-- RINGKASAN --}}

        @php

            $totalPinjaman = $pinjaman->count();

            if (auth()->user()->role === 'cs') {

                $menunggu = $pinjaman->where('status', 'menunggu_cs')->count();

            } elseif (auth()->user()->role === 'ao') {

                $menunggu = $pinjaman->where('status', 'menunggu_ao')->count();

            } elseif (auth()->user()->role === 'kabag') {

                $menunggu = $pinjaman->where('status', 'menunggu_kabag')->count();

            } elseif (auth()->user()->role === 'bendahara') {

                $menunggu = $pinjaman->where('status', 'disetujui')->count();

            } else {

                $menunggu = $pinjaman->whereIn('status', [

                    'menunggu_cs',

                    'menunggu_ao',

                    'menunggu_kabag'

                ])->count();

            }

            $disetujui = $pinjaman->whereIn('status', [

                'disetujui',

                'dicairkan',

                'lunas'

            ])->count();

            $ditolak = $pinjaman->where('status', 'ditolak')->count();

        @endphp

        <div class="row g-3 mb-4">

            {{-- TOTAL --}}

            <div class="col-12 col-md-6 col-xl-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="small text-muted mb-1">

                            {{ auth()->user()->role === 'kabag' && ($filter ?? 'aktif') === 'riwayat' ? 'Total Keputusan' : 'Total Pengajuan' }}

                        </div>

                        <div class="fs-3 fw-bold">

                            {{ $totalPinjaman }}

                        </div>

                        <div class="small text-muted">

                            Seluruh data pinjaman

                        </div>

                    </div>

                </div>

            </div>

            {{-- MENUNGGU --}}

            <div class="col-12 col-md-6 col-xl-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="small text-muted mb-1">

                            Menunggu Diproses

                        </div>

                        <div class="fs-3 fw-bold">

                            {{ $menunggu }}

                        </div>

                        <div class="small text-muted">

                            Pengajuan yang perlu ditindaklanjuti

                        </div>

                    </div>

                </div>

            </div>

            {{-- DISETUJUI --}}

            <div class="col-12 col-md-6 col-xl-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="small text-muted mb-1">

                            Disetujui

                        </div>

                        <div class="fs-3 fw-bold">

                            {{ $disetujui }}

                        </div>

                        <div class="small text-muted">

                            Pinjaman disetujui/dicairkan/lunas

                        </div>

                    </div>

                </div>

            </div>

            {{-- DITOLAK --}}

            <div class="col-12 col-md-6 col-xl-3">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="small text-muted mb-1">

                            Ditolak

                        </div>

                        <div class="fs-3 fw-bold">

                            {{ $ditolak }}

                        </div>

                        <div class="small text-muted">

                            Pengajuan ditolak

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- DAFTAR PINJAMAN --}}

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white border-bottom py-3">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <div>

                        <h5 class="mb-1 fw-bold">

                            @if (auth()->user()->role === 'cs')

                                Daftar Pengajuan Pinjaman

                            @elseif (auth()->user()->role === 'ao')

                                {{ ($filter ?? request('filter', 'aktif')) === 'riwayat' ? 'Riwayat Penanganan Pinjaman' : 'Daftar Pinjaman untuk Analisis' }}

                            @elseif (auth()->user()->role === 'kabag')

                                {{ ($filter ?? 'aktif') === 'riwayat' ? 'Riwayat Keputusan Persetujuan' : 'Daftar Pinjaman untuk Persetujuan' }}

                            @elseif (auth()->user()->role === 'bendahara')

                                Daftar Pinjaman untuk Pemantauan

                            @else

                                Daftar Pinjaman

                            @endif

                        </h5>

                        <div class="small text-muted">

                            {{ $totalPinjaman }} data ditemukan

                            @if (auth()->user()->role === 'ao' && ($filter ?? request('filter', 'aktif')) === 'riwayat')

                                · Pengajuan yang pernah ditangani oleh akun AO ini

                            @endif

                        </div>

                    </div>

                </div>

            </div>

            @if ($pinjaman->count() > 0)

                {{-- DESKTOP TABLE --}}

                <div class="table-responsive d-none d-md-block">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th class="px-3 py-3" style="width: 60px;">

                                    No

                                </th>

                                <th class="py-3">

                                    No. Pinjaman

                                </th>

                                <th class="py-3">

                                    Anggota

                                </th>

                                <th class="py-3">

                                    Paket Pinjaman

                                </th>

                                <th class="py-3">

                                    Jumlah

                                </th>

                                <th class="py-3">

                                    Tenor

                                </th>

                                <th class="py-3">

                                    Tanggal Pengajuan

                                </th>

                                <th class="py-3">

                                    Status

                                </th>

                                <th class="py-3 text-center">

                                    Aksi

                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($pinjaman as $item)

                                @php

                                    $status = $item->status;

                                    $statusLabel = match ($status) {

                                        'menunggu_cs' => 'Menunggu CS',

                                        'menunggu_ao' => 'Menunggu AO',

                                        'menunggu_kabag' => 'Menunggu Kabag',

                                        'disetujui' => 'Disetujui',

                                        'dicairkan' => 'Dicairkan',

                                        'lunas' => 'Lunas',

                                        'ditolak' => 'Ditolak',

                                        default => ucwords(str_replace('_', ' ', $status ?? '-')),

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

                                        ?? $item->jumlah_rekomendasi

                                        ?? $item->jumlah_pinjaman

                                        ?? 0;

                                @endphp

                                <tr>

                                    {{-- NO --}}

                                    <td class="px-3">

                                        {{ $loop->iteration }}

                                    </td>

                                    {{-- NO PINJAMAN --}}

                                    <td>

                                        <div class="fw-semibold">

                                            {{ $item->no_pinjaman }}

                                        </div>

                                    </td>

                                    {{-- ANGGOTA --}}

                                    <td>

                                        @if ($item->anggota)

                                            <div class="fw-semibold">

                                                {{ $item->anggota->user?->nama ?? '-' }}

                                            </div>

                                            @if ($item->anggota->kode_anggota)

                                                <div class="small text-muted">

                                                    {{ $item->anggota->kode_anggota }}

                                                </div>

                                            @endif

                                        @else

                                            <span class="text-muted">

                                                -

                                            </span>

                                        @endif

                                    </td>

                                    {{-- PAKET --}}

                                    <td>

                                        {{ $item->masterPinjaman?->nama_paket ?? '-' }}

                                    </td>

                                    {{-- JUMLAH --}}

                                    <td>

                                        <div class="fw-semibold">

                                            Rp {{ number_format($jumlahPinjaman, 0, ',', '.') }}

                                        </div>

                                    </td>

                                    {{-- TENOR --}}

                                    <td>

                                        {{ $item->tenor ?? '-' }} bulan

                                    </td>

                                    {{-- TANGGAL --}}

                                    <td>

                                        @if ($item->tanggal_pengajuan)

                                            {{ optional($item->tanggal_pengajuan)->format('d/m/Y') }}

                                        @else

                                            -

                                        @endif

                                    </td>

                                    {{-- STATUS --}}

                                    <td>

                                        <span class="badge {{ $statusClass }}">

                                            {{ $statusLabel }}

                                        </span>

                                    </td>

                                    {{-- AKSI --}}

                                    <td class="text-center">

                                        @if (auth()->user()->role === 'cs')

                                            <a href="{{ route('pinjaman.cs.show', $item) }}"

                                               class="btn btn-sm btn-primary">

                                                Periksa

                                            </a>

                                        @elseif (auth()->user()->role === 'ao')

                                            <a href="{{ route('pinjaman.ao.show', $item) }}"

                                               class="btn btn-sm {{ ($filter ?? request('filter', 'aktif')) === 'riwayat' ? 'btn-outline-primary' : 'btn-primary' }}">

                                                {{ ($filter ?? request('filter', 'aktif')) === 'riwayat' ? 'Lihat Detail' : 'Analisis' }}

                                            </a>

                                        @elseif (auth()->user()->role === 'kabag')

                                            <a href="{{ route('pinjaman.kabag.show', $item) }}"

                                               class="btn btn-sm btn-outline-primary">

                                                {{ ($filter ?? 'aktif') === 'riwayat' ? 'Lihat Riwayat' : 'Tinjau Persetujuan' }}

                                            </a>

                                        @elseif (auth()->user()->role === 'bendahara')

                                            <a href="{{ route('pinjaman.bendahara.show', $item) }}"

                                               class="btn btn-sm btn-outline-primary">

                                                Detail

                                            </a>

                                        @else

                                            <a href="{{ route('pinjaman.show', $item) }}"

                                               class="btn btn-sm btn-outline-primary">

                                                Detail

                                            </a>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

                {{-- MOBILE CARD --}}

                <div class="d-md-none">

                    @foreach ($pinjaman as $item)

                        @php

                            $status = $item->status;

                            $statusLabel = match ($status) {

                                'menunggu_cs' => 'Menunggu CS',

                                'menunggu_ao' => 'Menunggu AO',

                                'menunggu_kabag' => 'Menunggu Kabag',

                                'disetujui' => 'Disetujui',

                                'dicairkan' => 'Dicairkan',

                                'lunas' => 'Lunas',

                                'ditolak' => 'Ditolak',

                                default => ucwords(str_replace('_', ' ', $status ?? '-')),

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

                                ?? $item->jumlah_rekomendasi

                                ?? $item->jumlah_pinjaman

                                ?? 0;

                        @endphp

                        <div class="border-bottom p-3">

                            {{-- HEADER CARD --}}

                            <div class="d-flex justify-content-between align-items-start mb-3">

                                <div>

                                    <div class="fw-bold">

                                        {{ $item->no_pinjaman }}

                                    </div>

                                    <div class="small text-muted">

                                        {{ optional($item->tanggal_pengajuan)->format('d/m/Y') ?? '-' }}

                                    </div>

                                </div>

                                <span class="badge {{ $statusClass }}">

                                    {{ $statusLabel }}

                                </span>

                            </div>

                            {{-- ANGGOTA --}}

                            <div class="mb-2">

                                <div class="small text-muted">

                                    Anggota

                                </div>

                                <div class="fw-semibold">

                                    {{ $item->anggota?->user?->nama ?? '-' }}

                                </div>

                                @if ($item->anggota?->kode_anggota)

                                    <div class="small text-muted">

                                        {{ $item->anggota->kode_anggota }}

                                    </div>

                                @endif

                            </div>

                            {{-- PAKET --}}

                            <div class="mb-2">

                                <div class="small text-muted">

                                    Paket Pinjaman

                                </div>

                                <div>

                                    {{ $item->masterPinjaman?->nama_paket ?? '-' }}

                                </div>

                            </div>

                            {{-- JUMLAH --}}

                            <div class="mb-2">

                                <div class="small text-muted">

                                    Jumlah Pinjaman

                                </div>

                                <div class="fw-semibold">

                                    Rp {{ number_format($jumlahPinjaman, 0, ',', '.') }}

                                </div>

                            </div>

                            {{-- TENOR --}}

                            <div class="mb-3">

                                <div class="small text-muted">

                                    Tenor

                                </div>

                                <div>

                                    {{ $item->tenor ?? '-' }} bulan

                                </div>

                            </div>

                            {{-- AKSI --}}

                            @if (auth()->user()->role === 'cs')

                                <a href="{{ route('pinjaman.cs.show', $item) }}"

                                   class="btn btn-primary btn-sm w-100">

                                    Periksa Pengajuan

                                </a>

                            @elseif (auth()->user()->role === 'ao')

                                <a href="{{ route('pinjaman.ao.show', $item) }}"

                                   class="btn {{ ($filter ?? request('filter', 'aktif')) === 'riwayat' ? 'btn-outline-primary' : 'btn-primary' }} btn-sm w-100">

                                    {{ ($filter ?? request('filter', 'aktif')) === 'riwayat' ? 'Lihat Detail Penanganan' : 'Analisis Pinjaman' }}

                                </a>

                            @elseif (auth()->user()->role === 'kabag')

                                <a href="{{ route('pinjaman.kabag.show', $item) }}"

                                   class="btn btn-outline-primary btn-sm w-100">

                                    {{ ($filter ?? 'aktif') === 'riwayat' ? 'Lihat Riwayat Keputusan' : 'Tinjau Persetujuan' }}

                                </a>

                            @elseif (auth()->user()->role === 'bendahara')

                                <a href="{{ route('pinjaman.bendahara.show', $item) }}"

                                   class="btn btn-outline-primary btn-sm w-100">

                                    Lihat Detail

                                </a>

                            @else

                                <a href="{{ route('pinjaman.show', $item) }}"

                                   class="btn btn-outline-primary btn-sm w-100">

                                    Lihat Detail

                                </a>

                            @endif

                        </div>

                    @endforeach

                </div>

            @else

                {{-- EMPTY STATE --}}

                <div class="text-center py-5 px-3">

                    <div class="mb-3">

                        <h5 class="fw-bold mb-2">

                            Belum Ada Pengajuan

                        </h5>

                        @if (auth()->user()->role === 'cs')

                            <p class="text-muted mb-0">

                                Belum terdapat pengajuan pinjaman anggota yang masuk.

                            </p>

                        @elseif (auth()->user()->role === 'ao')

                            <p class="text-muted mb-0">

                                {{ ($filter ?? request('filter', 'aktif')) === 'riwayat'

                                    ? 'Belum ada pengajuan pinjaman yang tercatat pernah Anda tangani.'

                                    : 'Belum terdapat pengajuan pinjaman yang perlu dianalisis.' }}

                            </p>

                        @elseif (auth()->user()->role === 'kabag')

                            <p class="text-muted mb-0">

                                {{ ($filter ?? 'aktif') === 'riwayat' ? 'Belum ada keputusan persetujuan atau penolakan yang tercatat pada akun Anda.' : 'Saat ini tidak ada pengajuan pinjaman yang menunggu persetujuan Kabag.' }}

                            </p>

                        @elseif (auth()->user()->role === 'bendahara')

                            <p class="text-muted mb-0">

                                Belum terdapat data pengajuan pinjaman untuk dipantau.

                            </p>

                        @else

                            <p class="text-muted mb-0">

                                Belum terdapat data pinjaman.

                            </p>

                        @endif

                    </div>

                </div>

            @endif

        </div>

    </div>

</x-app-layout>

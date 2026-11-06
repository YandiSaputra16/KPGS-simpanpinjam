<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="h4 mb-1 fw-bold">
                Detail Pengajuan Pinjaman
            </h2>

            <p class="text-muted small mb-0">
                Pemeriksaan data pengajuan sebelum diteruskan ke tahap berikutnya.
            </p>
        </div>
    </x-slot>


    <div class="container-fluid py-4">

        {{-- ==========================================================
            PESAN
        =========================================================== --}}

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"></button>
            </div>
        @endif


        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                {{ session('error') }}

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close"></button>
            </div>
        @endif


        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">

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


        {{-- ==========================================================
            NAVIGASI
        =========================================================== --}}

        <div class="mb-4">

            @if (auth()->user()->role === 'cs')
                <a href="{{ route('pinjaman.cs.index') }}"
                   class="text-decoration-none">
                    &larr; Kembali ke daftar pengajuan
                </a>

            @elseif (auth()->user()->role === 'ao')
                <a href="{{ route('pinjaman.ao.index') }}"
                   class="text-decoration-none">
                    &larr; Kembali ke daftar analisis
                </a>

            @else
                <a href="{{ url()->previous() }}"
                   class="text-decoration-none">
                    &larr; Kembali
                </a>
            @endif

        </div>


        @php

            $statusLabel = match ($pinjaman->status) {

                'menunggu_cs'
                    => 'Menunggu CS',

                'menunggu_ao'
                    => 'Menunggu AO',

                'menunggu_kabag'
                    => 'Menunggu Kabag',

                'disetujui'
                    => 'Disetujui',

                'dicairkan'
                    => 'Dicairkan',

                'ditolak'
                    => 'Ditolak',

                'lunas'
                    => 'Lunas',

                default
                    => ucwords(
                        str_replace(
                            '_',
                            ' ',
                            $pinjaman->status ?? '-'
                        )
                    ),

            };


            $statusClass = match ($pinjaman->status) {

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

        @endphp


        {{-- ==========================================================
            HEADER DETAIL PINJAMAN
        =========================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body p-4">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                    <div>

                        <div class="small text-muted mb-1">
                            Nomor Pinjaman
                        </div>

                        <h3 class="h4 fw-bold mb-1">
                            {{ $pinjaman->no_pinjaman }}
                        </h3>

                        <div class="text-muted small">
                            Diajukan oleh
                            <span class="fw-semibold">
                                {{ $pinjaman->anggota?->user?->nama ?? '-' }}
                            </span>
                        </div>

                    </div>


                    <div>

                        <span class="badge {{ $statusClass }} fs-6 px-3 py-2">
                            {{ $statusLabel }}
                        </span>

                    </div>

                </div>

            </div>


            {{-- RINGKASAN NOMINAL --}}
            <div class="border-top">

                <div class="row g-0">

                    {{-- JUMLAH PENGAJUAN --}}
                    <div class="col-12 col-md-4">

                        <div class="p-4 border-bottom border-md-bottom-0">

                            <div class="small text-muted mb-1">
                                Jumlah Pengajuan
                            </div>

                            <div class="fs-5 fw-bold">
                                Rp {{ number_format((float) $pinjaman->jumlah_pinjaman, 0, ',', '.') }}
                            </div>

                        </div>

                    </div>


                    {{-- REKOMENDASI AO --}}
                    <div class="col-12 col-md-4">

                        <div class="p-4 border-bottom border-md-bottom-0 border-md-start">

                            <div class="small text-muted mb-1">
                                Rekomendasi AO
                            </div>

                            <div class="fs-5 fw-bold">

                                @if ($pinjaman->jumlah_rekomendasi !== null)

                                    Rp {{ number_format((float) $pinjaman->jumlah_rekomendasi, 0, ',', '.') }}

                                @else

                                    -

                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- JUMLAH DISETUJUI --}}
                    <div class="col-12 col-md-4">

                        <div class="p-4">

                            <div class="small text-muted mb-1">
                                Jumlah Disetujui
                            </div>

                            <div class="fs-5 fw-bold">

                                @if ($pinjaman->jumlah_disetujui !== null)

                                    Rp {{ number_format((float) $pinjaman->jumlah_disetujui, 0, ',', '.') }}

                                @else

                                    -

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ==========================================================
            DATA ANGGOTA + INFORMASI PINJAMAN
        =========================================================== --}}

        <div class="row g-4 mb-4">

            {{-- DATA ANGGOTA --}}
            <div class="col-12 col-lg-6">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-header bg-white py-3">

                        <h5 class="fw-bold mb-0">
                            Data Anggota
                        </h5>

                    </div>


                    <div class="card-body">

                        <div class="mb-4">

                            <div class="small text-muted mb-1">
                                Nama
                            </div>

                            <div class="fw-semibold">
                                {{ $pinjaman->anggota?->user?->nama ?? '-' }}
                            </div>

                        </div>


                        <div class="row g-3 mb-4">

                            <div class="col-12 col-sm-6">

                                <div class="small text-muted mb-1">
                                    Kode Anggota
                                </div>

                                <div>
                                    {{ $pinjaman->anggota?->kode_anggota ?? '-' }}
                                </div>

                            </div>


                            <div class="col-12 col-sm-6">

                                <div class="small text-muted mb-1">
                                    NIK
                                </div>

                                <div>
                                    {{ $pinjaman->anggota?->nik ?? '-' }}
                                </div>

                            </div>

                        </div>


                        <div class="mb-4">

                            <div class="small text-muted mb-1">
                                Nomor HP
                            </div>

                            <div>
                                {{ $pinjaman->anggota?->no_hp ?? '-' }}
                            </div>

                        </div>


                        <div>

                            <div class="small text-muted mb-1">
                                Alamat
                            </div>

                            <div>
                                {{ $pinjaman->anggota?->alamat ?? '-' }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- INFORMASI PINJAMAN --}}
            <div class="col-12 col-lg-6">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-header bg-white py-3">

                        <h5 class="fw-bold mb-0">
                            Informasi Pinjaman
                        </h5>

                    </div>


                    <div class="card-body">

                        <div class="row g-4">

                            {{-- PAKET --}}
                            <div class="col-12 col-sm-6">

                                <div class="small text-muted mb-1">
                                    Paket Pinjaman
                                </div>

                                <div class="fw-semibold">
                                    {{ $pinjaman->masterPinjaman?->nama_paket ?? '-' }}
                                </div>

                            </div>


                            {{-- KODE PAKET --}}
                            <div class="col-12 col-sm-6">

                                <div class="small text-muted mb-1">
                                    Kode Paket
                                </div>

                                <div>
                                    {{ $pinjaman->masterPinjaman?->kode ?? '-' }}
                                </div>

                            </div>


                            {{-- TENOR --}}
                            <div class="col-12 col-sm-6">

                                <div class="small text-muted mb-1">
                                    Tenor
                                </div>

                                <div>
                                    {{ $pinjaman->tenor ?? '-' }} bulan
                                </div>

                            </div>


                            {{-- BUNGA --}}
                            <div class="col-12 col-sm-6">

                                <div class="small text-muted mb-1">
                                    Bunga
                                </div>

                                <div>
                                    {{ number_format((float) $pinjaman->bunga_persen, 2, ',', '.') }}%
                                </div>

                            </div>


                            {{-- ANGSURAN --}}
                            <div class="col-12 col-sm-6">

                                <div class="small text-muted mb-1">
                                    Angsuran per Bulan
                                </div>

                                <div>
                                    Rp {{ number_format((float) $pinjaman->angsuran_per_bulan, 0, ',', '.') }}
                                </div>

                            </div>


                            {{-- TOTAL ANGSURAN --}}
                            <div class="col-12 col-sm-6">

                                <div class="small text-muted mb-1">
                                    Total Angsuran
                                </div>

                                <div>
                                    Rp {{ number_format((float) $pinjaman->total_angsuran, 0, ',', '.') }}
                                </div>

                            </div>


                            {{-- TANGGAL PENGAJUAN --}}
                            <div class="col-12 col-sm-6">

                                <div class="small text-muted mb-1">
                                    Tanggal Pengajuan
                                </div>

                                <div>
                                    {{ $pinjaman->tanggal_pengajuan?->format('d/m/Y') ?? '-' }}
                                </div>

                            </div>


                            {{-- STATUS KEANGGOTAAN --}}
                            <div class="col-12 col-sm-6">

                                <div class="small text-muted mb-1">
                                    Status Keanggotaan
                                </div>

                                <div>
                                    {{ ucfirst($pinjaman->anggota?->status_keanggotaan ?? '-') }}
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ==========================================================
            DATA PENDUKUNG
        =========================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="fw-bold mb-1">
                    Data Pendukung Analisis
                </h5>

                <p class="text-muted small mb-0">
                    Data yang disampaikan anggota untuk membantu proses
                    pemeriksaan dan analisis pinjaman.
                </p>

            </div>


            <div class="card-body">

                @if ($pinjaman->dataPendukung)

                    <div class="row g-3">

                        {{-- PRODUKSI SUSU --}}
                        <div class="col-12 col-md-6 col-lg-4">

                            <div class="border rounded p-3 h-100">

                                <div class="small text-muted mb-1">
                                    Produksi Susu / Bulan
                                </div>

                                <div class="fw-semibold">
                                    {{ number_format((float) $pinjaman->dataPendukung->produksi_susu_bulanan, 2, ',', '.') }}
                                    liter
                                </div>

                            </div>

                        </div>


                        {{-- JUMLAH SAPI --}}
                        <div class="col-12 col-md-6 col-lg-4">

                            <div class="border rounded p-3 h-100">

                                <div class="small text-muted mb-1">
                                    Jumlah Sapi
                                </div>

                                <div class="fw-semibold">
                                    {{ $pinjaman->dataPendukung->jumlah_sapi }}
                                    ekor
                                </div>

                            </div>

                        </div>


                        {{-- KONDISI USAHA --}}
                        <div class="col-12 col-md-6 col-lg-4">

                            <div class="border rounded p-3 h-100">

                                <div class="small text-muted mb-1">
                                    Kondisi Usaha
                                </div>

                                <div class="fw-semibold">
                                    {{ ucfirst($pinjaman->dataPendukung->kondisi_usaha ?? '-') }}
                                </div>

                            </div>

                        </div>


                        {{-- PENDAPATAN --}}
                        <div class="col-12 col-md-6 col-lg-4">

                            <div class="border rounded p-3 h-100">

                                <div class="small text-muted mb-1">
                                    Pendapatan / Bulan
                                </div>

                                <div class="fw-semibold">
                                    Rp {{ number_format((float) $pinjaman->dataPendukung->pendapatan_bulanan, 0, ',', '.') }}
                                </div>

                            </div>

                        </div>


                        {{-- KEWAJIBAN LAIN --}}
                        <div class="col-12 col-md-6 col-lg-4">

                            <div class="border rounded p-3 h-100">

                                <div class="small text-muted mb-1">
                                    Kewajiban Angsuran Lain
                                </div>

                                <div class="fw-semibold">
                                    Rp {{ number_format((float) $pinjaman->dataPendukung->kewajiban_angsuran_lain, 0, ',', '.') }}
                                    / bulan
                                </div>

                            </div>

                        </div>


                        {{-- KETERANGAN TAMBAHAN --}}
                        <div class="col-12 col-md-6 col-lg-7">

                            <div class="border rounded p-3 h-100">

                                <div class="small text-muted mb-1">
                                    Keterangan Tambahan
                                </div>

                                <div>
                                    {{ $pinjaman->dataPendukung->keterangan_tambahan ?: '-' }}
                                </div>

                            </div>

                        </div>

                    </div>

                @else

                    <div class="alert alert-warning mb-0">
                        Data pendukung pengajuan belum tersedia.
                    </div>

                @endif

            </div>

        </div>


        {{-- ==========================================================
            TUJUAN PINJAMAN
        =========================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="fw-bold mb-0">
                    Tujuan Pinjaman
                </h5>

            </div>


            <div class="card-body">

                <p class="mb-0" style="white-space: pre-line;">
                    {{ $pinjaman->tujuan_pinjaman ?: '-' }}
                </p>

            </div>

        </div>


        {{-- ==========================================================
            INFORMASI TAMBAHAN ANALISIS AO
            Hanya ditampilkan untuk AO; bagian role lain tidak berubah.
        =========================================================== --}}

        @if (auth()->user()->role === 'ao' && $pinjaman->anggota)

            @php
                $anggotaUntukAO = $pinjaman->anggota;

                $riwayatSimpananAO = $anggotaUntukAO->simpanan
                    ->sortByDesc('tanggal_transaksi')
                    ->take(10);

                $riwayatPinjamanAO = $anggotaUntukAO->pinjaman
                    ->where('id', '!=', $pinjaman->id)
                    ->sortByDesc('tanggal_pengajuan');
            @endphp

            {{-- RINGKASAN SALDO SIMPANAN --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-1">Ringkasan Simpanan Anggota</h5>
                    <p class="text-muted small mb-0">Saldo simpanan berdasarkan data anggota yang tersimpan.</p>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="small text-muted mb-1">Simpanan Pokok</div>
                                <div class="fw-bold">Rp {{ number_format((float) ($anggotaUntukAO->saldo_simpanan_pokok ?? 0), 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="small text-muted mb-1">Simpanan Wajib</div>
                                <div class="fw-bold">Rp {{ number_format((float) ($anggotaUntukAO->saldo_simpanan_wajib ?? 0), 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="small text-muted mb-1">Simpanan Sukarela</div>
                                <div class="fw-bold">Rp {{ number_format((float) ($anggotaUntukAO->saldo_simpanan_sukarela ?? 0), 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-light border mb-0 d-flex flex-wrap justify-content-between gap-2">
                                <span class="fw-semibold">Total Saldo Simpanan</span>
                                <span class="fw-bold">Rp {{ number_format((float) $anggotaUntukAO->total_simpanan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIWAYAT SIMPANAN --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-1">Riwayat Transaksi Simpanan</h5>
                    <p class="text-muted small mb-0">Maksimal 10 transaksi simpanan terbaru anggota.</p>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Nomor Simpanan</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Nominal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($riwayatSimpananAO as $simpananAO)
                                @php
                                    $kelasStatusSimpananAO = match ($simpananAO->status) {
                                        'disetujui' => 'bg-success',
                                        'pending' => 'bg-warning text-dark',
                                        'ditolak' => 'bg-danger',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <tr>
                                    <td class="px-3">{{ $simpananAO->no_simpanan ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $simpananAO->tanggal_transaksi?->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ $simpananAO->jenisSimpanan?->nama_jenis ?? '-' }}</td>
                                    <td class="text-nowrap">Rp {{ number_format((float) $simpananAO->jumlah, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $kelasStatusSimpananAO }}">
                                            {{ ucfirst(str_replace('_', ' ', $simpananAO->status ?? '-')) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada transaksi simpanan anggota.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- RIWAYAT PINJAMAN DAN ANGSURAN --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-1">Riwayat Pinjaman Sebelumnya</h5>
                    <p class="text-muted small mb-0">Riwayat pengajuan terdahulu dan pembayaran angsuran anggota.</p>
                </div>

                <div class="card-body">
                    @forelse ($riwayatPinjamanAO as $pinjamanLamaAO)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                                <div>
                                    <div class="fw-bold">{{ $pinjamanLamaAO->no_pinjaman }}</div>
                                    <div class="small text-muted">Tanggal pengajuan: {{ $pinjamanLamaAO->tanggal_pengajuan?->format('d/m/Y') ?? '-' }}</div>
                                </div>
                                <span class="badge bg-secondary align-self-start">{{ ucfirst(str_replace('_', ' ', $pinjamanLamaAO->status ?? '-')) }}</span>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-4">
                                    <div class="small text-muted mb-1">Jumlah Pengajuan</div>
                                    <div class="fw-semibold">Rp {{ number_format((float) $pinjamanLamaAO->jumlah_pinjaman, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="small text-muted mb-1">Jumlah Disetujui</div>
                                    <div class="fw-semibold">{{ $pinjamanLamaAO->jumlah_disetujui !== null ? 'Rp ' . number_format((float) $pinjamanLamaAO->jumlah_disetujui, 0, ',', '.') : '-' }}</div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="small text-muted mb-1">Angsuran Lunas</div>
                                    <div class="fw-semibold">{{ $pinjamanLamaAO->angsuran->where('status_pembayaran', 'lunas')->count() }} dari {{ $pinjamanLamaAO->angsuran->count() }}</div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Ke-</th>
                                            <th>Jatuh Tempo</th>
                                            <th>Tagihan</th>
                                            <th>Dibayar</th>
                                            <th>Tanggal Bayar</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pinjamanLamaAO->angsuran->sortBy('angsuran_ke') as $angsuranLamaAO)
                                            @php
                                                $kelasStatusAngsuranAO = match ($angsuranLamaAO->status_pembayaran) {
                                                    'lunas' => 'bg-success',
                                                    'menunggu_verifikasi' => 'bg-warning text-dark',
                                                    'ditolak' => 'bg-danger',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <tr>
                                                <td>{{ $angsuranLamaAO->angsuran_ke }}</td>
                                                <td class="text-nowrap">{{ $angsuranLamaAO->tanggal_jatuh_tempo?->format('d/m/Y') ?? '-' }}</td>
                                                <td class="text-nowrap">Rp {{ number_format((float) $angsuranLamaAO->total_angsuran, 0, ',', '.') }}</td>
                                                <td class="text-nowrap">{{ $angsuranLamaAO->jumlah_bayar !== null ? 'Rp ' . number_format((float) $angsuranLamaAO->jumlah_bayar, 0, ',', '.') : '-' }}</td>
                                                <td class="text-nowrap">{{ $angsuranLamaAO->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td>
                                                <td><span class="badge {{ $kelasStatusAngsuranAO }}">{{ ucfirst(str_replace('_', ' ', $angsuranLamaAO->status_pembayaran ?? '-')) }}</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada data angsuran.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-light border mb-0">Anggota belum memiliki riwayat pinjaman sebelumnya.</div>
                    @endforelse
                </div>
            </div>

        @endif


        
        {{-- ==========================================================
            DETAIL ANALISIS AO UNTUK KABAG
            Menggunakan data pinjaman dan approval yang sudah tersedia.
            Tidak membutuhkan perubahan controller atau database.
        =========================================================== --}}

        @if (auth()->user()->role === 'kabag')
            @php
                $approvalAO = $pinjaman->approvals->firstWhere('level', 'ao');
                $selisihRekomendasiAO = (
                    $pinjaman->jumlah_rekomendasi !== null
                    && $pinjaman->jumlah_pinjaman !== null
                )
                    ? (float) $pinjaman->jumlah_pinjaman - (float) $pinjaman->jumlah_rekomendasi
                    : null;
            @endphp

            @if ($approvalAO)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                            <div>
                                <h5 class="fw-bold mb-1">Detail Analisis dan Rekomendasi AO</h5>
                                <p class="text-muted small mb-0">
                                    Ringkasan data pengajuan dan rekomendasi yang perlu ditinjau sebelum keputusan Kabag.
                                </p>
                            </div>
                            <span class="badge bg-success align-self-start">
                                {{ ucfirst(str_replace('_', ' ', $approvalAO->status ?? 'direkomendasikan')) }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="small text-muted mb-1">Jumlah Pengajuan Anggota</div>
                                    <div class="fs-5 fw-bold">
                                        Rp {{ number_format((float) $pinjaman->jumlah_pinjaman, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="border border-success rounded p-3 h-100">
                                    <div class="small text-muted mb-1">Rekomendasi Nominal AO</div>
                                    <div class="fs-5 fw-bold text-success">
                                        {{ $pinjaman->jumlah_rekomendasi !== null ? 'Rp ' . number_format((float) $pinjaman->jumlah_rekomendasi, 0, ',', '.') : '-' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="small text-muted mb-1">Selisih dari Pengajuan</div>
                                    <div class="fs-5 fw-bold">
                                        {{ $selisihRekomendasiAO !== null ? 'Rp ' . number_format($selisihRekomendasiAO, 0, ',', '.') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3">Rincian Pinjaman Tercatat</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-lg-3">
                                <div class="small text-muted mb-1">Tenor</div>
                                <div class="fw-semibold">{{ $pinjaman->tenor ?? '-' }} bulan</div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="small text-muted mb-1">Bunga</div>
                                <div class="fw-semibold">{{ number_format((float) $pinjaman->bunga_persen, 2, ',', '.') }}%</div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="small text-muted mb-1">Angsuran per Bulan Saat Ini</div>
                                <div class="fw-semibold">Rp {{ number_format((float) $pinjaman->angsuran_per_bulan, 0, ',', '.') }}</div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="small text-muted mb-1">Total Angsuran Saat Ini</div>
                                <div class="fw-semibold">Rp {{ number_format((float) $pinjaman->total_angsuran, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <div class="border rounded p-3 bg-light">
                            <div class="fw-semibold mb-2">Catatan Analisis AO</div>
                            <div style="white-space: pre-line;">{{ $approvalAO->catatan ?: 'Tidak ada catatan analisis yang dicatat.' }}</div>
                            <hr>
                            <div class="row g-2 small text-muted">
                                <div class="col-12 col-md-6">
                                    <span class="fw-semibold">Petugas AO:</span> {{ $approvalAO->user?->nama ?? '-' }}
                                </div>
                                <div class="col-12 col-md-6">
                                    <span class="fw-semibold">Waktu analisis:</span> {{ $approvalAO->tanggal_approval?->format('d/m/Y H:i') ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif


        {{-- ==========================================================
            TINDAKAN BERDASARKAN ROLE
        =========================================================== --}}

        @if (
            auth()->user()->role === 'cs'
            && $pinjaman->status === 'menunggu_cs'
        )

            <x-pinjaman-cs-action
                :pinjaman="$pinjaman"
            />

        @elseif (
            auth()->user()->role === 'ao'
            && $pinjaman->status === 'menunggu_ao'
        )

            <x-pinjaman-ao-action
                :pinjaman="$pinjaman"
            />

        @elseif (
            auth()->user()->role === 'kabag'
            && $pinjaman->status === 'menunggu_kabag'
        )

            <x-pinjaman-kabag-action
                :pinjaman="$pinjaman"
            />

        @elseif (
            auth()->user()->role === 'bendahara'
            && $pinjaman->status === 'disetujui'
        )

            <x-pinjaman-bendahara-action
                :pinjaman="$pinjaman"
            />

        @endif


        {{-- ==========================================================
            INFORMASI STATUS
        =========================================================== --}}

        @if (
            !(
                (auth()->user()->role === 'cs' && $pinjaman->status === 'menunggu_cs')
                ||
                (auth()->user()->role === 'ao' && $pinjaman->status === 'menunggu_ao')
                ||
                (auth()->user()->role === 'kabag' && $pinjaman->status === 'menunggu_kabag')
                ||
                (auth()->user()->role === 'bendahara' && $pinjaman->status === 'disetujui')
            )
        )

            <div class="alert alert-light border mb-4">

                <div class="small text-muted">

                    Pengajuan ini sudah tidak berada pada tahap tindakan
                    {{ strtoupper(auth()->user()->role) }}.

                    Status saat ini:

                    <span class="fw-semibold text-dark">
                        {{ $statusLabel }}
                    </span>.

                </div>

            </div>

        @endif

    </div>

</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 fw-bold mb-1">Detail Transaksi</h2>
            <p class="text-muted small mb-0">
                Informasi lengkap transaksi pembayaran Anda.
            </p>
        </div>
    </x-slot>

    @php
        $tipeTransaksi = isset($pembayaran->no_pinjaman)
            ? 'angsuran'
            : (isset($pembayaran->jenis_simpanan) ? 'simpanan' : 'lainnya');

        $statusTransaksi = $pembayaran->status ?? '-';

        $statusLabel = match ($statusTransaksi) {
            'pending', 'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'disetujui' => 'Berhasil Diverifikasi',
            'lunas' => 'Lunas',
            'ditolak' => 'Perlu Diperbaiki',
            'belum_bayar' => 'Belum Dibayar',
            default => ucwords(str_replace('_', ' ', $statusTransaksi)),
        };

        $statusClass = match ($statusTransaksi) {
            'pending', 'menunggu_verifikasi' => 'bg-warning text-dark',
            'disetujui', 'lunas' => 'bg-success',
            'ditolak' => 'bg-danger',
            'belum_bayar' => 'bg-secondary',
            default => 'bg-secondary',
        };

        $judulTransaksi = $tipeTransaksi === 'angsuran'
            ? 'Pembayaran Angsuran'
            : ($tipeTransaksi === 'simpanan' ? 'Pembayaran Simpanan' : 'Transaksi');

        $nomorTransaksi = $pembayaran->nomor ?? '-';
        $jumlahTransaksi = (float) ($pembayaran->jumlah ?? 0);
        $tanggalTransaksi = $pembayaran->tanggal ?? null;
        $metodePembayaran = $pembayaran->metode_pembayaran ?? '-';
        $buktiPembayaran = $pembayaran->bukti ?? null;
        $keteranganTransaksi = $pembayaran->keterangan ?? null;
    @endphp

    <div class="container-fluid py-4">
        <div class="container kpgs-container-1100">

            {{-- Flash Message --}}
            @if (session('success'))
                <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Header Transaksi --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                        <div>
                            <p class="text-muted small mb-1">Nomor Transaksi</p>
                            <h1 class="h3 fw-bold mb-1">{{ $nomorTransaksi }}</h1>
                            <div class="text-muted small">{{ $judulTransaksi }}</div>
                        </div>

                        <div>
                            <span class="badge {{ $statusClass }} px-3 py-2">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informasi Status --}}
            @if (in_array($statusTransaksi, ['pending', 'menunggu_verifikasi']))
                <div class="alert alert-warning mb-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-clock-history mt-1"></i>
                        <div>
                            <p class="fw-semibold mb-1">Pembayaran Sedang Diverifikasi</p>
                            <p class="small mb-0">
                                Pembayaran Anda sedang menunggu pemeriksaan petugas.
                                Silakan menunggu sampai proses verifikasi selesai.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif ($statusTransaksi === 'disetujui')
                <div class="alert alert-success mb-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-check-circle mt-1"></i>
                        <div>
                            <p class="fw-semibold mb-1">Pembayaran Berhasil Diverifikasi</p>
                            <p class="small mb-0">
                                Pembayaran telah diverifikasi dan tercatat dalam sistem.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif ($statusTransaksi === 'lunas')
                <div class="alert alert-success mb-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-check-circle mt-1"></i>
                        <div>
                            <p class="fw-semibold mb-1">Angsuran Telah Lunas</p>
                            <p class="small mb-0">
                                Data angsuran ini tercatat telah lunas.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif ($statusTransaksi === 'ditolak')
                <div class="alert alert-danger mb-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle mt-1"></i>
                        <div>
                            <p class="fw-semibold mb-1">Pembayaran Perlu Diperbaiki</p>
                            <p class="small mb-0">
                                Pembayaran belum dapat diverifikasi.
                                Periksa keterangan yang tersedia dan lakukan pengajuan ulang
                                apabila fitur tersebut tersedia untuk transaksi ini.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif ($statusTransaksi === 'belum_bayar')
                <div class="alert alert-secondary mb-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle mt-1"></i>
                        <div>
                            <p class="fw-semibold mb-1">Angsuran Belum Dibayar</p>
                            <p class="small mb-0">
                                Belum ada pembayaran yang tercatat untuk angsuran ini.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Informasi Pembayaran --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h3 class="h5 fw-bold mb-1">Informasi Pembayaran</h3>
                    <p class="text-muted small mb-0">Rincian transaksi pembayaran.</p>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">

                        <div class="col-12 col-sm-6">
                            <p class="text-muted small mb-1">Jenis Transaksi</p>
                            <p class="fw-semibold mb-0">{{ $judulTransaksi }}</p>
                        </div>

                        <div class="col-12 col-sm-6">
                            <p class="text-muted small mb-1">Nomor Transaksi</p>
                            <p class="fw-semibold mb-0">{{ $nomorTransaksi }}</p>
                        </div>

                        <div class="col-12 col-sm-6">
                            <p class="text-muted small mb-1">Jumlah Pembayaran</p>
                            <p class="h5 fw-bold mb-0">
                                Rp {{ number_format($jumlahTransaksi, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="col-12 col-sm-6">
                            <p class="text-muted small mb-1">Tanggal Pembayaran</p>
                            <p class="fw-semibold mb-0">
                                @if ($tanggalTransaksi)
                                    {{ \Carbon\Carbon::parse($tanggalTransaksi)->translatedFormat('d F Y') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>

                        <div class="col-12 col-sm-6">
                            <p class="text-muted small mb-1">Metode Pembayaran</p>
                            <p class="fw-semibold mb-0">
                                {{ $metodePembayaran !== '-' ? ucfirst(str_replace('_', ' ', $metodePembayaran)) : '-' }}
                            </p>
                        </div>

                        <div class="col-12 col-sm-6">
                            <p class="text-muted small mb-1">Status</p>
                            <p class="fw-semibold mb-0">{{ $statusLabel }}</p>
                        </div>

                        {{-- Informasi Simpanan --}}
                        @if ($tipeTransaksi === 'simpanan')
                            <div class="col-12 col-sm-6">
                                <p class="text-muted small mb-1">Jenis Simpanan</p>
                                <p class="fw-semibold mb-0">
                                    {{ $pembayaran->jenis_simpanan ?? '-' }}
                                </p>
                            </div>
                        @endif

                        {{-- Informasi Angsuran --}}
                        @if ($tipeTransaksi === 'angsuran')
                            <div class="col-12 col-sm-6">
                                <p class="text-muted small mb-1">Nomor Pinjaman</p>
                                <p class="fw-semibold mb-0">
                                    {{ $pembayaran->no_pinjaman ?? '-' }}
                                </p>
                            </div>

                            <div class="col-12 col-sm-6">
                                <p class="text-muted small mb-1">Angsuran Ke-</p>
                                <p class="fw-semibold mb-0">
                                    {{ $pembayaran->angsuran_ke ?? '-' }}
                                </p>
                            </div>

                            <div class="col-12 col-sm-6">
                                <p class="text-muted small mb-1">Tanggal Jatuh Tempo</p>
                                <p class="fw-semibold mb-0">
                                    @if (!empty($pembayaran->tanggal_jatuh_tempo))
                                        {{ \Carbon\Carbon::parse($pembayaran->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>

                            <div class="col-12 col-sm-6">
                                <p class="text-muted small mb-1">Pokok</p>
                                <p class="fw-semibold mb-0">
                                    Rp {{ number_format((float) ($pembayaran->jumlah_pokok ?? 0), 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="col-12 col-sm-6">
                                <p class="text-muted small mb-1">Bunga</p>
                                <p class="fw-semibold mb-0">
                                    Rp {{ number_format((float) ($pembayaran->jumlah_bunga ?? 0), 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="col-12 col-sm-6">
                                <p class="text-muted small mb-1">Total Angsuran</p>
                                <p class="h5 fw-bold mb-0">
                                    Rp {{ number_format((float) ($pembayaran->total_angsuran ?? $pembayaran->jumlah ?? 0), 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="col-12 col-sm-6">
                                <p class="text-muted small mb-1">Denda</p>
                                <p class="fw-semibold mb-0">
                                    Rp {{ number_format((float) ($pembayaran->denda ?? 0), 0, ',', '.') }}
                                </p>
                            </div>

                            <div class="col-12 col-sm-6">
                                <p class="text-muted small mb-1">Sisa Pinjaman</p>
                                <p class="fw-semibold mb-0">
                                    Rp {{ number_format((float) ($pembayaran->sisa_pinjaman ?? 0), 0, ',', '.') }}
                                </p>
                            </div>
                        @endif

                        @if (!empty($keteranganTransaksi))
                            <div class="col-12">
                                <p class="text-muted small mb-1">Keterangan</p>
                                <p class="mb-0 kpgs-pre-line">{{ $keteranganTransaksi }}</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            
{{-- Form Pengajuan Pembayaran Khusus Angsuran --}}
@if (
    $tipeTransaksi === 'angsuran'
    && in_array($statusTransaksi, ['belum_bayar', 'ditolak'], true)
    && !empty($pembayaran->no_pinjaman)
    && !empty($pembayaran->id)
)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h3 class="h5 fw-bold mb-1">
                {{ $statusTransaksi === 'ditolak'
                    ? 'Kirim Ulang Bukti Pembayaran'
                    : 'Ajukan Pembayaran' }}
            </h3>
            <p class="text-muted small mb-0">
                Unggah bukti pembayaran untuk angsuran ke-{{ $pembayaran->angsuran_ke }}.
            </p>
        </div>

        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route(
                    $statusTransaksi === 'ditolak'
                        ? 'angsuran.saya.upload-ulang'
                        : 'angsuran.saya.bayar',
                    $pembayaran->no_pinjaman
                ) }}"
                enctype="multipart/form-data"
            >
                @csrf

                <input
                    type="hidden"
                    name="angsuran_ids[]"
                    value="{{ $pembayaran->id }}"
                >

                <div class="mb-3">
                    <label for="bukti_pembayaran" class="form-label fw-semibold">
                        Bukti Pembayaran
                    </label>

                    <input
                        type="file"
                        name="bukti_pembayaran"
                        id="bukti_pembayaran"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.pdf"
                        required
                    >

                    <div class="form-text">
                        Format JPG, JPEG, PNG, atau PDF. Maksimal 5 MB.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i>
                    {{ $statusTransaksi === 'ditolak'
                        ? 'Kirim Ulang Bukti'
                        : 'Ajukan Pembayaran' }}
                </button>
            </form>
        </div>
    </div>
@endif


            {{-- Bukti Pembayaran --}}
            @if (!empty($buktiPembayaran))
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h3 class="h5 fw-bold mb-1">Bukti Pembayaran</h3>
                        <p class="text-muted small mb-0">
                            Bukti pembayaran yang dilampirkan pada transaksi.
                        </p>
                    </div>

                    <div class="card-body p-4">
                        <div class="border rounded bg-light p-3 text-center overflow-hidden">
                            <img
                                src="{{ asset('storage/' . $buktiPembayaran) }}"
                                alt="Bukti pembayaran"
                                class="img-fluid kpgs-image-contain-600"
                            >
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-secondary mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-image"></i>
                        <span class="small">
                            Tidak ada bukti pembayaran yang dilampirkan pada transaksi ini.
                        </span>
                    </div>
                </div>
            @endif

            {{-- Informasi Sistem --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h3 class="h5 fw-bold mb-0">Informasi Sistem</h3>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12 col-sm-6">
                            <p class="text-muted small mb-1">Diajukan</p>
                            <p class="fw-medium mb-0">
                                @if (!empty($pembayaran->created_at))
                                    {{ \Carbon\Carbon::parse($pembayaran->created_at)->translatedFormat('d F Y, H:i') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>

                        <div class="col-12 col-sm-6">
                            <p class="text-muted small mb-1">Pembaruan Terakhir</p>
                            <p class="fw-medium mb-0">
                                @if (!empty($pembayaran->updated_at))
                                    {{ \Carbon\Carbon::parse($pembayaran->updated_at)->translatedFormat('d F Y, H:i') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Navigasi Kembali --}}
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between gap-3">
                <a
                    href="{{ \Illuminate\Support\Facades\Route::has($pembayaran->back_route ?? '') ? route($pembayaran->back_route) : route('angsuran.saya.index') }}"
                    class="btn btn-outline-secondary"
                >
                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali
                </a>
            </div>

        </div>
    </div>
</x-app-layout>

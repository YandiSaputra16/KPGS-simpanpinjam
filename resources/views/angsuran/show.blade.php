
<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 fw-bold mb-1">Detail Angsuran</h2>
            <p class="text-muted mb-0">
                Pemeriksaan dan verifikasi pembayaran angsuran anggota.
            </p>
        </div>
    </x-slot>

    <div class="container-fluid py-4">
        <div class="container-xxl">

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle mt-1"></i>
                        <div>
                            <h6 class="alert-heading fw-bold">Terdapat kesalahan</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @php
                $status = $angsuran->status_pembayaran;

                $statusLabel = match ($status) {
                    'menunggu_verifikasi' => 'Menunggu Verifikasi',
                    'lunas' => 'Lunas',
                    'ditolak' => 'Ditolak',
                    'belum_bayar' => 'Belum Dibayar',
                    default => ucwords(str_replace('_', ' ', $status ?? '-')),
                };

                $statusClass = match ($status) {
                    'menunggu_verifikasi' => 'bg-warning text-dark',
                    'lunas' => 'bg-success',
                    'ditolak' => 'bg-danger',
                    default => 'bg-secondary',
                };

                $pinjaman = $angsuran->pinjaman;
$anggota = $pinjaman?->anggota;

$buktiPembayaran = $angsuran->bukti_pembayaran;

if (is_array($buktiPembayaran)) {
    $bukti = !empty($buktiPembayaran)
        ? $buktiPembayaran[array_key_last($buktiPembayaran)]
        : null;
} elseif (is_string($buktiPembayaran) && $buktiPembayaran !== '') {
    $bukti = $buktiPembayaran;
} else {
    $bukti = null;
}

$menungguVerifikasi = $status === 'menunggu_verifikasi';
            @endphp

            {{-- Informasi utama --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div>
                            <div class="text-muted small">Nomor Pinjaman</div>
                            <h1 class="h4 fw-bold mb-0 mt-1">
                                {{ $pinjaman?->no_pinjaman ?? '-' }}
                            </h1>
                            <div class="text-muted small mt-2">
                                Angsuran ke-{{ $angsuran->angsuran_ke ?? '-' }}
                            </div>
                        </div>

                        <div>
                            <span class="badge {{ $statusClass }} fs-6">
                                @if ($status === 'lunas')
                                    <i class="bi bi-check-circle me-1"></i>
                                @elseif ($status === 'ditolak')
                                    <i class="bi bi-x-circle me-1"></i>
                                @elseif ($menungguVerifikasi)
                                    <i class="bi bi-hourglass-split me-1"></i>
                                @else
                                    <i class="bi bi-circle me-1"></i>
                                @endif
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">
                        {{-- Informasi pembayaran --}}
                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-4 h-100">
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <i class="bi bi-wallet2 text-primary fs-5"></i>
                                    <h5 class="fw-bold mb-0">Informasi Pembayaran</h5>
                                </div>

                                <div class="mb-4">
                                    <div class="text-muted small mb-1">Nomor Pinjaman</div>
                                    <div class="fw-semibold">
                                        {{ $pinjaman?->no_pinjaman ?? '-' }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="text-muted small mb-1">Angsuran Ke</div>
                                    <div class="fw-semibold">
                                        Angsuran ke-{{ $angsuran->angsuran_ke ?? '-' }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="text-muted small mb-1">Jumlah Pembayaran</div>
                                    <div class="fs-4 fw-bold">
                                        Rp {{ number_format((float) ($angsuran->jumlah_bayar ?? $angsuran->total_angsuran ?? 0), 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="text-muted small mb-1">Tanggal Pembayaran</div>
                                    <div class="fw-semibold">
                                        {{ $angsuran->tanggal_bayar?->translatedFormat('d F Y') ?? '-' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-muted small mb-1">Jatuh Tempo</div>
                                    <div class="fw-semibold">
                                        {{ $angsuran->tanggal_jatuh_tempo?->translatedFormat('d F Y') ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Informasi anggota --}}
                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-4 h-100">
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <i class="bi bi-person-vcard text-primary fs-5"></i>
                                    <h5 class="fw-bold mb-0">Informasi Anggota</h5>
                                </div>

                                <div class="mb-4">
                                    <div class="text-muted small mb-1">Nama Anggota</div>
                                    <div class="fw-semibold">
                                        {{ $anggota?->user?->nama ?? '-' }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="text-muted small mb-1">Kode Anggota</div>
                                    <div class="fw-semibold">
                                        {{ $anggota?->kode_anggota ?? '-' }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="text-muted small mb-1">Nomor HP</div>
                                    <div class="fw-semibold">
                                        {{ $anggota?->no_hp ?? '-' }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="text-muted small mb-1">Jumlah Pinjaman</div>
                                    <div class="fw-semibold">
                                        Rp {{ number_format((float) ($pinjaman?->jumlah_pinjaman ?? 0), 0, ',', '.') }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-muted small mb-1">Paket Pinjaman</div>
                                    <div class="fw-semibold">
                                        {{ $pinjaman?->masterPinjaman?->nama_paket ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bukti pembayaran --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-receipt text-primary fs-5"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Bukti Pembayaran</h5>
                            <p class="text-muted small mb-0">
                                Bukti yang dikirim anggota pada saat pengajuan pembayaran.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if ($bukti)
                        @php
                            $ekstensiBukti = strtolower(pathinfo($bukti, PATHINFO_EXTENSION));
                            $urlBukti = asset('storage/' . $bukti);
                        @endphp

                        <div class="border rounded bg-light p-3 text-center">
                            @if (in_array($ekstensiBukti, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                                <img
                                    src="{{ $urlBukti }}"
                                    alt="Bukti pembayaran angsuran"
                                    class="img-fluid d-block mx-auto"
                                    style="max-height: 650px; object-fit: contain;"
                                >
                            @elseif ($ekstensiBukti === 'pdf')
                                <div class="py-4">
                                    <i class="bi bi-file-earmark-pdf text-danger display-4"></i>
                                    <p class="fw-semibold mt-3 mb-1">Dokumen bukti pembayaran PDF</p>
                                    <p class="text-muted small mb-0">
                                        Buka dokumen untuk memeriksa bukti pembayaran.
                                    </p>
                                </div>
                            @else
                                <div class="py-4">
                                    <i class="bi bi-file-earmark text-secondary display-4"></i>
                                    <p class="text-muted mb-0 mt-2">
                                        File bukti pembayaran tersedia.
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="mt-3">
                            <a
                                href="{{ $urlBukti }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="btn btn-outline-primary"
                            >
                                <i class="bi bi-box-arrow-up-right me-1"></i>
                                Buka Bukti di Tab Baru
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Tidak ada file bukti pembayaran yang dilampirkan pada data ini.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Panel verifikasi --}}
            @if ($menungguVerifikasi)
                <div class="card border-warning shadow-sm mb-4">
                    <div class="card-header bg-warning-subtle border-warning py-3 px-4">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-shield-check text-warning-emphasis fs-5 mt-1"></i>
                            <div>
                                <h5 class="fw-bold mb-1 text-warning-emphasis">
                                    Verifikasi Pembayaran
                                </h5>
                                <p class="small text-warning-emphasis mb-0">
                                    Periksa nominal, tanggal pembayaran, data pinjaman, dan bukti
                                    pembayaran sebelum menyetujui atau menolak transaksi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Verifikasi pembayaran akan memproses angsuran yang termasuk dalam
                            kelompok pembayaran terkait.
                        </div>

                        <form
                            method="POST"
                            action="{{ route('angsuran.tolak', $angsuran) }}"
                        >
                            @csrf

                            <div class="mb-4">
                                <label for="alasan" class="form-label fw-semibold">
                                    Alasan Penolakan
                                    <span class="text-danger">*</span>
                                </label>

                                <textarea
                                    id="alasan"
                                    name="alasan"
                                    rows="4"
                                    maxlength="1000"
                                    required
                                    class="form-control @error('alasan') is-invalid @enderror"
                                    placeholder="Wajib diisi jika pembayaran ditolak."
                                >{{ old('alasan') }}</textarea>

                                <div class="form-text">
                                    Isi alasan yang jelas agar anggota mengetahui hal yang perlu diperbaiki.
                                </div>

                                @error('alasan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-flex flex-column flex-sm-row justify-content-sm-end gap-2">
                                <button
                                    type="submit"
                                    class="btn btn-outline-danger"
                                    data-confirm="Tolak pembayaran angsuran ini dan kembalikan pengajuan kepada anggota?"
                                >
                                    <i class="bi bi-x-circle me-1"></i>
                                    Tolak Pembayaran
                                </button>
                            </div>
                        </form>

                        <hr class="my-4">

                        <form
                            method="POST"
                            action="{{ route('angsuran.verifikasi', $angsuran) }}"
                        >
                            @csrf

                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                                <div class="small text-muted">
                                    Pastikan bukti dan nominal pembayaran telah diperiksa.
                                </div>

                                <button
                                    type="submit"
                                    class="btn btn-success"
                                    data-confirm="Setujui pembayaran angsuran ini? Proses verifikasi akan memperbarui status pembayaran dan pencatatan kas sesuai backend."
                                >
                                    <i class="bi bi-check-circle me-1"></i>
                                    Setujui Pembayaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Waktu pengajuan --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-12 col-sm-6">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-calendar-plus text-muted mt-1"></i>
                                <div>
                                    <div class="text-muted small mb-1">Pengajuan dibuat</div>
                                    <div class="fw-semibold">
                                        {{ $angsuran->created_at?->translatedFormat('d F Y H:i') ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-clock-history text-muted mt-1"></i>
                                <div>
                                    <div class="text-muted small mb-1">Terakhir diperbarui</div>
                                    <div class="fw-semibold">
                                        {{ $angsuran->updated_at?->translatedFormat('d F Y H:i') ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <a href="{{ route('angsuran.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali ke Daftar Angsuran
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
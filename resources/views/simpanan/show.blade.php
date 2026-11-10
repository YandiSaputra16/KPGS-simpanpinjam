<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 fw-bold mb-1">Detail Simpanan</h2>
            <p class="text-muted mb-0">Pemeriksaan dan verifikasi pembayaran simpanan anggota.</p>
        </div>
    </x-slot>

    <div class="container-fluid w-100 py-3 py-md-4 px-2 px-sm-3 px-lg-4">
        <div class="w-100">
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

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-3 px-sm-4">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <div>
                            <div class="text-muted small">Nomor Simpanan</div>
                            <h1 class="h4 fw-bold mb-0 mt-1">{{ $simpanan->no_simpanan }}</h1>
                        </div>

                        <div>
                            @if ($simpanan->status === 'pending')
                                <span class="badge bg-warning text-dark fs-6">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    Menunggu Verifikasi
                                </span>
                            @elseif ($simpanan->status === 'disetujui')
                                <span class="badge bg-success fs-6">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Disetujui
                                </span>
                            @elseif ($simpanan->status === 'ditolak')
                                <span class="badge bg-danger fs-6">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Ditolak
                                </span>
                            @else
                                <span class="badge bg-secondary fs-6">
                                    {{ ucfirst($simpanan->status ?? '-') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body p-3 p-sm-4">
                    <div class="row g-3 g-md-4">
                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-4 h-100">
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <i class="bi bi-wallet2 text-primary fs-5"></i>
                                    <h5 class="fw-bold mb-0">Informasi Pembayaran</h5>
                                </div>

                                <div class="mb-4">
                                    <div class="text-muted small mb-1">Jenis Simpanan</div>
                                    <div class="fw-semibold">
                                        {{ $simpanan->jenisSimpanan?->nama_jenis ?? '-' }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="text-muted small mb-1">Jumlah Pembayaran</div>
                                    <div class="fs-4 fw-bold">
                                        Rp {{ number_format((float) $simpanan->jumlah, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="text-muted small mb-1">Tanggal Transaksi</div>
                                    <div class="fw-semibold">
                                        {{ $simpanan->tanggal_transaksi?->translatedFormat('d F Y') ?? '-' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-muted small mb-1">Metode Pembayaran</div>
                                    <div class="fw-semibold text-capitalize">
                                        {{ $simpanan->metode_pembayaran ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-4 h-100">
                                <div class="d-flex align-items-center gap-2 mb-4">
                                    <i class="bi bi-person-vcard text-primary fs-5"></i>
                                    <h5 class="fw-bold mb-0">Informasi Anggota</h5>
                                </div>

                                <div class="mb-4">
                                    <div class="text-muted small mb-1">Nama Anggota</div>
                                    <div class="fw-semibold">
                                        {{ $simpanan->anggota?->user?->nama ?? '-' }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="text-muted small mb-1">Kode Anggota</div>
                                    <div class="fw-semibold">
                                        {{ $simpanan->anggota?->kode_anggota ?? '-' }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="text-muted small mb-1">Nomor HP</div>
                                    <div class="fw-semibold">
                                        {{ $simpanan->anggota?->no_hp ?? '-' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-muted small mb-1">Diajukan Oleh</div>
                                    <div class="fw-semibold">
                                        {{ $simpanan->pencatat?->nama ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-3 px-sm-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-receipt text-primary fs-5"></i>
                        <div>
                            <h5 class="fw-bold mb-1">Bukti Pembayaran</h5>
                            <p class="text-muted small mb-0">
                                Bukti yang dikirim pada saat pengajuan.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3 p-sm-4">
                    @if ($simpanan->bukti)
                        <div class="border rounded bg-light p-3">
                            <img
                                src="{{ asset('storage/' . $simpanan->bukti) }}"
                                alt="Bukti pembayaran {{ $simpanan->no_simpanan }}"
                                class="img-fluid d-block mx-auto"
                            >
                        </div>

                        <div class="mt-3">
                            <a
                                href="{{ asset('storage/' . $simpanan->bukti) }}"
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
                            Tidak ada file bukti pembayaran yang dilampirkan.
                        </div>
                    @endif
                </div>
            </div>

            @if ($simpanan->keterangan)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 px-3 px-sm-4">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-chat-left-text text-primary fs-5"></i>
                            <h5 class="fw-bold mb-0">Keterangan</h5>
                        </div>
                    </div>

                    <div class="card-body p-3 p-sm-4">
                        <div class="text-muted">
                            {!! nl2br(e($simpanan->keterangan)) !!}
                        </div>
                    </div>
                </div>
            @endif

            @if ($simpanan->status === 'pending')
                <div class="card border-warning shadow-sm mb-4">
                    <div class="card-header bg-warning-subtle border-warning py-3 px-3 px-sm-4">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-shield-check text-warning-emphasis fs-5 mt-1"></i>
                            <div>
                                <h5 class="fw-bold mb-1 text-warning-emphasis">
                                    Verifikasi Pembayaran
                                </h5>
                                <p class="small text-warning-emphasis mb-0">
                                    Pastikan nominal, metode pembayaran, dan bukti pembayaran
                                    telah diperiksa sebelum menentukan status transaksi.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3 p-sm-4">
                        <form
                            method="POST"
                            action="{{ route('simpanan.verifikasi', $simpanan) }}"
                        >
                            @csrf

                            <div class="mb-4">
                                <label for="catatan" class="form-label fw-semibold">
                                    Catatan CS/Teller
                                </label>

                                <textarea
                                    id="catatan"
                                    name="catatan"
                                    rows="4"
                                    maxlength="1000"
                                    class="form-control @error('catatan') is-invalid @enderror"
                                    placeholder="Opsional. Isi catatan jika diperlukan."
                                >{{ old('catatan') }}</textarea>

                                @error('catatan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="d-flex flex-column flex-sm-row justify-content-sm-end gap-2">
                                <button
                                    type="submit"
                                    name="status"
                                    value="ditolak"
                                    class="btn btn-outline-danger"
                                    data-confirm="Tolak pembayaran {{ $simpanan->no_simpanan }}?"
                                >
                                    <i class="bi bi-x-circle me-1"></i>
                                    Tolak Pembayaran
                                </button>

                                <button
                                    type="submit"
                                    name="status"
                                    value="disetujui"
                                    class="btn btn-success"
                                    data-confirm="Setujui pembayaran {{ $simpanan->no_simpanan }}? Saldo anggota dan kas akan diperbarui."
                                >
                                    <i class="bi bi-check-circle me-1"></i>
                                    Setujui Pembayaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3 p-sm-4">
                    <div class="row g-3 g-md-4">
                        <div class="col-12 col-sm-6">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-calendar-plus text-muted mt-1"></i>
                                <div>
                                    <div class="text-muted small mb-1">Pengajuan dibuat</div>
                                    <div class="fw-semibold">
                                        {{ $simpanan->created_at?->translatedFormat('d F Y H:i') ?? '-' }}
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
                                        {{ $simpanan->updated_at?->translatedFormat('d F Y H:i') ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <a href="{{ route('simpanan.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali ke Daftar Simpanan
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h5 fw-bold mb-1">
                Ajukan Pembayaran
            </h2>
            <p class="text-muted small mb-0">
                Kirim pembayaran simpanan untuk diverifikasi oleh Bendahara KPGS.
            </p>
        </div>
    </x-slot>

    <div class="container-fluid py-3 py-md-4 px-2 px-sm-3 px-lg-4">
        <div class="w-100">>
            {{-- Tombol kembali --}}

            {{-- Informasi alur --}}
            <div class="alert alert-info border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <div>
                        <h6 class="fw-bold mb-2">
                            Informasi Pembayaran
                        </h6>
                        <p class="small mb-0">
                            Setelah pembayaran dikirim, transaksi akan berstatus
                            <strong>Menunggu Verifikasi</strong>. Saldo simpanan Anda
                            belum berubah sampai pembayaran diperiksa dan disetujui
                            oleh Bendahara.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Error validasi --}}
            @if ($errors->any())
                <div class="alert alert-danger shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div class="w-100">
                            <h6 class="fw-bold mb-2">
                                Terdapat kesalahan pada pengisian data.
                            </h6>

                            <ul class="small mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-4">
                    <h5 class="fw-bold mb-1">
                        Form Pembayaran Simpanan
                    </h5>
                    <p class="text-muted small mb-0">
                        Isi data pembayaran sesuai transaksi yang Anda lakukan.
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('simpanan.pengajuan.store') }}"
                    enctype="multipart/form-data"
                >
                    @csrf

                    <div class="card-body p-4">

                        {{-- Anggota --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Anggota
                            </label>

                            <div class="border rounded bg-light p-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="fs-4 text-primary">
                                        <i class="bi bi-person-circle"></i>
                                    </div>

                                    <div>
                                        <div class="fw-semibold">
                                            {{ $anggota->user?->nama ?? '-' }}
                                        </div>

                                        <div class="text-muted small">
                                            Kode Anggota:
                                            {{ $anggota->kode_anggota ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Jenis simpanan --}}
                        <div class="mb-4">
                            <label
                                for="jenis_simpanan_id"
                                class="form-label fw-semibold"
                            >
                                Jenis Simpanan
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                id="jenis_simpanan_id"
                                name="jenis_simpanan_id"
                                required
                                class="form-select @error('jenis_simpanan_id') is-invalid @enderror"
                            >
                                <option value="">
                                    -- Pilih Jenis Simpanan --
                                </option>

                                @foreach ($jenisSimpanan as $jenis)
                                    <option
                                        value="{{ $jenis->id }}"
                                        @selected(old('jenis_simpanan_id') == $jenis->id)
                                    >
                                        {{ $jenis->nama_jenis }}
                                    </option>
                                @endforeach
                            </select>

                            @error('jenis_simpanan_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Jumlah --}}
                        <div class="mb-4">
                            <label
                                for="jumlah"
                                class="form-label fw-semibold"
                            >
                                Jumlah Pembayaran
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <span class="input-group-text">
                                    Rp
                                </span>

                                <input
                                    type="number"
                                    id="jumlah"
                                    name="jumlah"
                                    value="{{ old('jumlah') }}"
                                    min="1"
                                    step="0.01"
                                    required
                                    class="form-control @error('jumlah') is-invalid @enderror"
                                    placeholder="Contoh: 100000"
                                >

                                @error('jumlah')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        {{-- Tanggal --}}
                        <div class="mb-4">
                            <label
                                for="tanggal_transaksi"
                                class="form-label fw-semibold"
                            >
                                Tanggal Pembayaran
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="date"
                                id="tanggal_transaksi"
                                name="tanggal_transaksi"
                                value="{{ old('tanggal_transaksi', now()->toDateString()) }}"
                                max="{{ now()->toDateString() }}"
                                required
                                class="form-control @error('tanggal_transaksi') is-invalid @enderror"
                            >

                            @error('tanggal_transaksi')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Metode pembayaran --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Metode Pembayaran
                                <span class="text-danger">*</span>
                            </label>

                            <div class="row g-3">

                                {{-- Transfer --}}
                               <div class="col-12">
    <div class="border rounded-3 p-4 h-100 bg-white">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3">
                <i class="bi bi-bank fs-3"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1">Transfer Bank</h6>
                <p class="text-muted small mb-0">
                    Lakukan pembayaran melalui rekening resmi KPGS.
                </p>
            </div>
        </div>

        <div class="bg-light border rounded-3 p-4">
            <div class="mb-3">
                <span class="d-block text-muted small mb-1">Bank</span>
                <span class="fw-semibold fs-5">BRI</span>
            </div>

            <div class="mb-3">
                <span class="d-block text-muted small mb-1">Nomor Rekening</span>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <strong class="text-primary fs-4">
                        0000-0000-0000-000
                    </strong>

                    <button
                        type="button"
                        class="btn btn-outline-primary"
                        onclick="navigator.clipboard.writeText('000000000000000')"
                    >
                        <i class="bi bi-copy me-1"></i>
                        Salin Nomor Rekening
                    </button>
                </div>
            </div>

            <div>
                <span class="d-block text-muted small mb-1">Atas Nama</span>
                <span class="fw-semibold">
                    Koperasi Peternak Garut Selatan
                </span>
            </div>
        </div>

        <div class="alert alert-info mt-3 mb-0 small">
            <i class="bi bi-info-circle me-1"></i>
            Silakan transfer sesuai nominal pembayaran, kemudian unggah bukti transfer pada formulir di bawah.
        </div>
    </div>
</div>
                            @error('metode_pembayaran')
                                <div class="text-danger small mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Bukti --}}
                        <div class="mb-4">
                            <label
                                for="bukti"
                                class="form-label fw-semibold"
                            >
                                Bukti Pembayaran
                            </label>

                            <input
                                type="file"
                                id="bukti"
                                name="bukti"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                class="form-control @error('bukti') is-invalid @enderror"
                            >

                            <div class="form-text">
                                <i class="bi bi-paperclip me-1"></i>
                                Untuk pembayaran transfer, bukti pembayaran wajib
                                diunggah. Format yang diterima JPG, JPEG, PNG,
                                atau WEBP dengan ukuran maksimal 2 MB.
                            </div>

                            @error('bukti')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <label
                                for="keterangan"
                                class="form-label fw-semibold"
                            >
                                Keterangan
                            </label>

                            <textarea
                                id="keterangan"
                                name="keterangan"
                                rows="4"
                                maxlength="1000"
                                class="form-control @error('keterangan') is-invalid @enderror"
                                placeholder="Tambahkan keterangan jika diperlukan."
                            >{{ old('keterangan') }}</textarea>

                            @error('keterangan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                    {{-- Footer form --}}
                    <div class="card-footer bg-light border-top p-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">

                            <a
                                href="{{ route('simpanan.saya.index') }}"
                                class="btn btn-outline-secondary"
                            >
                                <i class="bi bi-arrow-left me-1"></i>
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-send me-1"></i>
                                Ajukan Pembayaran
                            </button>

                        </div>
                    </div>
                </form>
            </div>

            {{-- Catatan --}}
            <div class="alert alert-warning border-0 shadow-sm mb-0" role="alert">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-exclamation-circle-fill fs-5"></i>

                    <div class="small">
                        <strong>Catatan:</strong>
                        pengajuan pembayaran tidak langsung menambah saldo simpanan.
                        Saldo baru diperbarui setelah Bendahara memeriksa dan
                        menyetujui pembayaran.
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
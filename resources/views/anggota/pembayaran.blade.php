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

    <div class="container-fluid py-4">
        <div class="container kpgs-container-900">

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
                                <div class="col-12 col-md-6">
                                    <label
                                        class="border rounded p-3 h-100 d-flex align-items-start gap-3 w-100"
                                        for="metode_transfer"
                                        class="kpgs-pointer"
                                    >
                                        <input
                                            type="radio"
                                            id="metode_transfer"
                                            name="metode_pembayaran"
                                            value="transfer"
                                            class="form-check-input mt-1 flex-shrink-0"
                                            @checked(old('metode_pembayaran') === 'transfer')
                                            required
                                        >

                                        <span>
                                            <span class="d-block fw-semibold">
                                                <i class="bi bi-bank me-1"></i>
                                                Transfer
                                            </span>

                                            <span class="d-block text-muted small mt-1">
                                                Upload bukti transfer setelah pembayaran.
                                            </span>
                                        </span>
                                    </label>
                                </div>

                                {{-- Tunai --}}
                                <div class="col-12 col-md-6">
                                    <label
                                        class="border rounded p-3 h-100 d-flex align-items-start gap-3 w-100"
                                        for="metode_tunai"
                                        class="kpgs-pointer"
                                    >
                                        <input
                                            type="radio"
                                            id="metode_tunai"
                                            name="metode_pembayaran"
                                            value="tunai"
                                            class="form-check-input mt-1 flex-shrink-0"
                                            @checked(old('metode_pembayaran') === 'tunai')
                                        >

                                        <span>
                                            <span class="d-block fw-semibold">
                                                <i class="bi bi-cash-stack me-1"></i>
                                                Tunai
                                            </span>

                                            <span class="d-block text-muted small mt-1">
                                                Pembayaran tunai akan diperiksa oleh Bendahara.
                                            </span>
                                        </span>
                                    </label>
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
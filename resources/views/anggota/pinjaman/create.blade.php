<x-app-layout>
    <div data-kpgs-loan-form>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h2 class="h5 fw-bold mb-1">
                    Ajukan Pinjaman
                </h2>
                <p class="text-muted small mb-0">
                    Lengkapi data pengajuan pinjaman Anda dengan benar.
                </p>
            </div>

            <a
                href="{{ route('pinjaman.anggota.index') }}"
                class="btn btn-outline-secondary btn-sm"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="container-fluid w-100 py-3 py-md-4 px-2 px-sm-3 px-lg-4">
        <div class="w-100">

            {{-- Pesan error umum --}}
            @if ($errors->has('pinjaman'))
                <div class="alert alert-danger shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>

                        <div>
                            <h6 class="fw-bold mb-1">
                                Pengajuan tidak dapat diproses
                            </h6>

                            <p class="small mb-0">
                                {{ $errors->first('pinjaman') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Error validasi --}}
            @if ($errors->any() && !$errors->has('pinjaman'))
                <div class="alert alert-danger shadow-sm mb-4" role="alert">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-exclamation-circle-fill fs-5"></i>

                        <div class="w-100">
                            <h6 class="fw-bold mb-2">
                                Terdapat data yang belum benar.
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

            {{-- Informasi anggota --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 px-3 px-sm-4">
                    <h5 class="fw-bold mb-1">
                        Data Anggota
                    </h5>

                    <p class="text-muted small mb-0">
                        Data berikut diambil dari akun anggota yang sedang login.
                    </p>
                </div>

                <div class="card-body p-3 p-sm-4">
                    <div class="row g-3 g-md-4">

                        <div class="col-12 col-md-6">
                            <div class="text-muted small mb-1">
                                Nama Anggota
                            </div>

                            <div class="fw-semibold">
                                {{ $anggota->user->nama ?? '-' }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="text-muted small mb-1">
                                Kode Anggota
                            </div>

                            <div class="fw-semibold">
                                {{ $anggota->kode_anggota ?? '-' }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="text-muted small mb-1">
                                Nomor HP
                            </div>

                            <div class="fw-semibold">
                                {{ $anggota->no_hp ?? '-' }}
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="text-muted small mb-1">
                                Status Keanggotaan
                            </div>

                            <div>
                                @if ($anggota->status_keanggotaan === 'aktif')
                                    <span class="badge bg-success-subtle text-success-emphasis">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger-emphasis">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('pinjaman.store') }}"
                id="formPengajuanPinjaman"
            >
                @csrf

                {{-- Paket dan nominal --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 px-3 px-sm-4">
                        <h5 class="fw-bold mb-1">
                            Data Pinjaman
                        </h5>

                        <p class="text-muted small mb-0">
                            Pilih paket pinjaman dan tentukan nominal serta tenor yang diinginkan.
                        </p>
                    </div>

                    <div class="card-body p-3 p-sm-4">

                        {{-- Paket --}}
                        <div class="mb-4">
                            <label
                                for="master_pinjaman_id"
                                class="form-label fw-semibold"
                            >
                                Paket Pinjaman
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                id="master_pinjaman_id"
                                name="master_pinjaman_id"
                                required
                                class="form-select @error('master_pinjaman_id') is-invalid @enderror"
                            >
                                <option value="">
                                    -- Pilih Paket Pinjaman --
                                </option>

                                @foreach ($paketPinjaman as $paket)
                                    <option
                                        value="{{ $paket->id }}"
                                        data-min="{{ $paket->plafon_min }}"
                                        data-max="{{ $paket->plafon_max }}"
                                        data-tenor-min="{{ $paket->tenor_min }}"
                                        data-tenor-max="{{ $paket->tenor_max }}"
                                        data-bunga="{{ $paket->bunga_persen }}"
                                        @selected(old('master_pinjaman_id') == $paket->id)
                                    >
                                        {{ $paket->nama_paket }}
                                        —
                                        Rp {{ number_format($paket->plafon_min, 0, ',', '.') }}
                                        s/d
                                        Rp {{ number_format($paket->plafon_max, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>

                            @error('master_pinjaman_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div
                                id="informasiPaket"
                                class="d-none mt-3 alert alert-info mb-0"
                            >
                                <div class="d-flex align-items-start gap-3">
                                    <i class="bi bi-info-circle-fill fs-5"></i>

                                    <div class="w-100">
                                        <h6 class="fw-bold mb-3">
                                            Informasi Paket
                                        </h6>

                                        <div class="row g-3 small">
                                            <div class="col-12 col-md-4">
                                                <div class="text-muted mb-1">
                                                    Plafon
                                                </div>

                                                <div
                                                    id="infoPlafon"
                                                    class="fw-semibold"
                                                >
                                                    -
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <div class="text-muted mb-1">
                                                    Tenor
                                                </div>

                                                <div
                                                    id="infoTenor"
                                                    class="fw-semibold"
                                                >
                                                    -
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-4">
                                                <div class="text-muted mb-1">
                                                    Bunga
                                                </div>

                                                <div
                                                    id="infoBunga"
                                                    class="fw-semibold"
                                                >
                                                    -
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Jumlah --}}
                        <div class="mb-4">
                            <label
                                for="jumlah_pinjaman"
                                class="form-label fw-semibold"
                            >
                                Jumlah Pinjaman
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <span class="input-group-text">
                                    Rp
                                </span>

                                <input
                                    type="number"
                                    id="jumlah_pinjaman"
                                    name="jumlah_pinjaman"
                                    value="{{ old('jumlah_pinjaman') }}"
                                    min="1"
                                    step="1000"
                                    required
                                    class="form-control @error('jumlah_pinjaman') is-invalid @enderror"
                                    placeholder="Contoh: 1500000"
                                >

                                @error('jumlah_pinjaman')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div
                                id="infoJumlah"
                                class="form-text"
                            >
                                Pilih paket terlebih dahulu untuk melihat batas nominal.
                            </div>
                        </div>

                        {{-- Tenor --}}
                        <div class="mb-4">
                            <label
                                for="tenor"
                                class="form-label fw-semibold"
                            >
                                Tenor
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <input
                                    type="number"
                                    id="tenor"
                                    name="tenor"
                                    value="{{ old('tenor') }}"
                                    min="1"
                                    required
                                    class="form-control @error('tenor') is-invalid @enderror"
                                    placeholder="Contoh: 6"
                                >

                                <span class="input-group-text">
                                    bulan
                                </span>

                                @error('tenor')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div
                                id="infoTenorInput"
                                class="form-text"
                            >
                                Pilih paket terlebih dahulu untuk melihat pilihan tenor.
                            </div>
                        </div>

                        {{-- Estimasi --}}
                        <div
                            id="estimasiPinjaman"
                            class="d-none border rounded bg-light p-4 mb-4"
                        >
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-calculator fs-5 text-primary"></i>

                                <h6 class="fw-bold mb-0">
                                    Estimasi Pinjaman
                                </h6>
                            </div>

                            <div class="row g-3">

                                <div class="col-12 col-md-4">
                                    <div class="border rounded bg-white p-3 h-100">
                                        <div class="text-muted small mb-1">
                                            Bunga per bulan
                                        </div>

                                        <div
                                            id="estimasiBunga"
                                            class="fw-bold"
                                        >
                                            Rp 0
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="border rounded bg-white p-3 h-100">
                                        <div class="text-muted small mb-1">
                                            Angsuran per bulan
                                        </div>

                                        <div
                                            id="estimasiAngsuran"
                                            class="fw-bold"
                                        >
                                            Rp 0
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="border rounded bg-white p-3 h-100">
                                        <div class="text-muted small mb-1">
                                            Total pembayaran
                                        </div>

                                        <div
                                            id="estimasiTotal"
                                            class="fw-bold"
                                        >
                                            Rp 0
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <p class="text-muted small mt-3 mb-0">
                                Estimasi ini digunakan sebagai gambaran awal.
                                Nilai final dapat berubah setelah proses analisis dan
                                persetujuan pinjaman.
                            </p>
                        </div>

                        {{-- Tujuan --}}
                        <div>
                            <label
                                for="tujuan_pinjaman"
                                class="form-label fw-semibold"
                            >
                                Tujuan Pinjaman
                                <span class="text-danger">*</span>
                            </label>

                            <textarea
                                id="tujuan_pinjaman"
                                name="tujuan_pinjaman"
                                rows="4"
                                required
                                maxlength="1000"
                                class="form-control @error('tujuan_pinjaman') is-invalid @enderror"
                                placeholder="Jelaskan tujuan penggunaan dana pinjaman..."
                            >{{ old('tujuan_pinjaman') }}</textarea>

                            @error('tujuan_pinjaman')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                Jelaskan penggunaan dana secara jelas agar dapat membantu proses analisis.
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Data pendukung --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 px-3 px-sm-4">
                        <h5 class="fw-bold mb-1">
                            Data Pendukung Analisis
                        </h5>

                        <p class="text-muted small mb-0">
                            Data ini digunakan sebagai bahan pemeriksaan dan analisis kelayakan pinjaman.
                        </p>
                    </div>

                    <div class="card-body p-3 p-sm-4">

                        <div class="row g-3 g-md-4">

                            {{-- Produksi susu --}}
                            <div class="col-12 col-md-6">
                                <label
                                    for="produksi_susu_bulanan"
                                    class="form-label fw-semibold"
                                >
                                    Produksi Susu Bulanan
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <input
                                        type="number"
                                        id="produksi_susu_bulanan"
                                        name="produksi_susu_bulanan"
                                        value="{{ old('produksi_susu_bulanan') }}"
                                        min="0"
                                        step="0.01"
                                        required
                                        class="form-control @error('produksi_susu_bulanan') is-invalid @enderror"
                                        placeholder="Contoh: 1500"
                                    >

                                    <span class="input-group-text">
                                        liter/bulan
                                    </span>

                                    @error('produksi_susu_bulanan')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Jumlah sapi --}}
                            <div class="col-12 col-md-6">
                                <label
                                    for="jumlah_sapi"
                                    class="form-label fw-semibold"
                                >
                                    Jumlah Sapi
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <input
                                        type="number"
                                        id="jumlah_sapi"
                                        name="jumlah_sapi"
                                        value="{{ old('jumlah_sapi') }}"
                                        min="0"
                                        required
                                        class="form-control @error('jumlah_sapi') is-invalid @enderror"
                                        placeholder="Contoh: 5"
                                    >

                                    <span class="input-group-text">
                                        ekor
                                    </span>

                                    @error('jumlah_sapi')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Kondisi usaha --}}
                            <div class="col-12 col-md-6">
                                <label
                                    for="kondisi_usaha"
                                    class="form-label fw-semibold"
                                >
                                    Kondisi Usaha
                                    <span class="text-danger">*</span>
                                </label>

                                <select
                                    id="kondisi_usaha"
                                    name="kondisi_usaha"
                                    required
                                    class="form-select @error('kondisi_usaha') is-invalid @enderror"
                                >
                                    <option value="">
                                        -- Pilih Kondisi Usaha --
                                    </option>

                                    <option
                                        value="baik"
                                        @selected(old('kondisi_usaha') === 'baik')
                                    >
                                        Baik
                                    </option>

                                    <option
                                        value="cukup"
                                        @selected(old('kondisi_usaha') === 'cukup')
                                    >
                                        Cukup
                                    </option>

                                    <option
                                        value="kurang"
                                        @selected(old('kondisi_usaha') === 'kurang')
                                    >
                                        Kurang
                                    </option>
                                </select>

                                @error('kondisi_usaha')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Pendapatan --}}
                            <div class="col-12 col-md-6">
                                <label
                                    for="pendapatan_bulanan"
                                    class="form-label fw-semibold"
                                >
                                    Pendapatan Bulanan
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">
                                        Rp
                                    </span>

                                    <input
                                        type="number"
                                        id="pendapatan_bulanan"
                                        name="pendapatan_bulanan"
                                        value="{{ old('pendapatan_bulanan') }}"
                                        min="0"
                                        step="1000"
                                        required
                                        class="form-control @error('pendapatan_bulanan') is-invalid @enderror"
                                        placeholder="Contoh: 3000000"
                                    >

                                    @error('pendapatan_bulanan')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Kewajiban lain --}}
                            <div class="col-12">
                                <label
                                    for="kewajiban_angsuran_lain"
                                    class="form-label fw-semibold"
                                >
                                    Kewajiban Angsuran Lain
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">
                                        Rp
                                    </span>

                                    <input
                                        type="number"
                                        id="kewajiban_angsuran_lain"
                                        name="kewajiban_angsuran_lain"
                                        value="{{ old('kewajiban_angsuran_lain', 0) }}"
                                        min="0"
                                        step="1000"
                                        required
                                        class="form-control @error('kewajiban_angsuran_lain') is-invalid @enderror"
                                        placeholder="0"
                                    >

                                    @error('kewajiban_angsuran_lain')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-text">
                                    Masukkan total kewajiban angsuran dari pinjaman atau pembiayaan lain per bulan.
                                </div>
                            </div>

                            {{-- Keterangan --}}
                            <div class="col-12">
                                <label
                                    for="keterangan_tambahan"
                                    class="form-label fw-semibold"
                                >
                                    Keterangan Tambahan
                                </label>

                                <textarea
                                    id="keterangan_tambahan"
                                    name="keterangan_tambahan"
                                    rows="4"
                                    maxlength="2000"
                                    class="form-control @error('keterangan_tambahan') is-invalid @enderror"
                                    placeholder="Tambahkan informasi lain jika diperlukan..."
                                >{{ old('keterangan_tambahan') }}</textarea>

                                @error('keterangan_tambahan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div>

                    </div>
                </div>

                {{-- Ketentuan --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 px-3 px-sm-4">
                        <h5 class="fw-bold mb-0">
                            Persetujuan Pengajuan
                        </h5>
                    </div>

                    <div class="card-body p-3 p-sm-4">

                        @php
                            $syaratUmum = $paketPinjaman
                                ->filter(function ($paket) {
                                    return !empty($paket->syarat);
                                })
                                ->pluck('syarat')
                                ->unique()
                                ->values();
                        @endphp

                        @if ($syaratUmum->count() > 0)
                            <div class="alert alert-light border mb-4">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="bi bi-journal-text fs-5 text-primary"></i>

                                    <div>
                                        <h6 class="fw-bold mb-2">
                                            Ketentuan Paket Pinjaman
                                        </h6>

                                        <ul class="small mb-0 ps-3">
                                            @foreach ($syaratUmum as $syarat)
                                                <li class="mb-1">
                                                    {{ $syarat }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-check">
                            <input
                                type="checkbox"
                                id="setuju_syarat"
                                name="setuju_syarat"
                                value="1"
                                required
                                class="form-check-input @error('setuju_syarat') is-invalid @enderror"
                                @checked(old('setuju_syarat'))
                            >

                            <label
                                for="setuju_syarat"
                                class="form-check-label small"
                            >
                                Saya menyatakan bahwa seluruh data yang saya berikan
                                adalah benar dan saya menyetujui ketentuan pengajuan
                                pinjaman yang berlaku.
                            </label>

                            @error('setuju_syarat')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Tombol --}}
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">

                    <p class="text-muted small mb-0">
                        Setelah dikirim, pengajuan akan diperiksa oleh CS sebelum masuk ke tahap analisis.
                    </p>

                    <button
                        type="submit"
                        id="btnSubmit"
                        class="btn btn-primary px-4"
                    >
                        <i class="bi bi-send me-1"></i>
                        Kirim Pengajuan
                    </button>

                </div>

            </form>

        </div>
    </div>
    </div>
</x-app-layout>
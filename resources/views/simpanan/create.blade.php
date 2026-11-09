<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 fw-bold mb-1">Catat Simpanan</h2>
            <p class="text-muted mb-0">Catat transaksi simpanan anggota KPGS.</p>
        </div>
    </x-slot>

    <div class="container-fluid w-100 py-3 py-md-4 px-2 px-sm-3 px-lg-4">
        <div class="w-100">
            @if ($errors->any())
                <div class="alert alert-danger shadow-sm mb-4" role="alert">
                    <div class="fw-semibold">Transaksi belum dapat disimpan.</div>
                    <ul class="mb-0 mt-2 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('simpanan.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- DATA TRANSAKSI --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom py-3 px-3 px-sm-4">
                        <h5 class="fw-bold mb-1">Data Transaksi</h5>
                        <p class="small text-muted mb-0">Isi data simpanan yang akan dicatat ke dalam sistem.</p>
                    </div>

                    <div class="card-body p-3 p-sm-4">
                        <div class="row g-3 g-md-4">
                            {{-- Anggota --}}
                            <div class="col-12">
                                <label for="anggota_id" class="form-label fw-semibold">
                                    Anggota <span class="text-danger">*</span>
                                </label>
                                <select id="anggota_id" name="anggota_id" required class="form-select @error('anggota_id') is-invalid @enderror">
                                    <option value="">Pilih anggota</option>
                                    @foreach ($anggota as $item)
                                        <option value="{{ $item->id }}" @selected(old('anggota_id') == $item->id)>
                                            {{ $item->kode_anggota }} - {{ $item->user?->nama ?? 'Tanpa Nama' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('anggota_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Jenis Simpanan --}}
                            <div class="col-12 col-md-6">
                                <label for="jenis_simpanan_id" class="form-label fw-semibold">
                                    Jenis Simpanan <span class="text-danger">*</span>
                                </label>
                                <select id="jenis_simpanan_id" name="jenis_simpanan_id" required class="form-select @error('jenis_simpanan_id') is-invalid @enderror">
                                    <option value="">Pilih jenis simpanan</option>
                                    @foreach ($jenisSimpanan as $jenis)
                                        <option value="{{ $jenis->id }}" @selected(old('jenis_simpanan_id') == $jenis->id)>
                                            {{ $jenis->nama_jenis }}@if ($jenis->kode) ({{ $jenis->kode }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis_simpanan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Jumlah --}}
                            <div class="col-12 col-md-6">
                                <label for="jumlah" class="form-label fw-semibold">
                                    Jumlah Simpanan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input
                                        type="number"
                                        id="jumlah"
                                        name="jumlah"
                                        value="{{ old('jumlah') }}"
                                        min="1"
                                        step="0.01"
                                        required
                                        placeholder="100000"
                                        class="form-control @error('jumlah') is-invalid @enderror"
                                    >
                                    @error('jumlah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Masukkan nominal simpanan dalam rupiah.</div>
                            </div>

                            {{-- Tanggal --}}
                            <div class="col-12 col-md-6">
                                <label for="tanggal_transaksi" class="form-label fw-semibold">
                                    Tanggal Transaksi <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="date"
                                    id="tanggal_transaksi"
                                    name="tanggal_transaksi"
                                    value="{{ old('tanggal_transaksi', now()->toDateString()) }}"
                                    required
                                    class="form-control @error('tanggal_transaksi') is-invalid @enderror"
                                >
                                @error('tanggal_transaksi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Metode Pembayaran --}}
                            <div class="col-12 col-md-6">
                                <label for="metode_pembayaran" class="form-label fw-semibold">
                                    Metode Pembayaran <span class="text-danger">*</span>
                                </label>
                                <select id="metode_pembayaran" name="metode_pembayaran" required class="form-select @error('metode_pembayaran') is-invalid @enderror">
                                    <option value="">Pilih metode pembayaran</option>
                                    <option value="tunai" @selected(old('metode_pembayaran') === 'tunai')>Tunai</option>
                                    <option value="transfer" @selected(old('metode_pembayaran') === 'transfer')>Transfer</option>
                                </select>
                                @error('metode_pembayaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Keterangan --}}
                            <div class="col-12">
                                <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
                                <textarea
                                    id="keterangan"
                                    name="keterangan"
                                    rows="4"
                                    maxlength="1000"
                                    placeholder="Keterangan tambahan transaksi..."
                                    class="form-control @error('keterangan') is-invalid @enderror"
                                >{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Bukti --}}
                            <div class="col-12">
                                <label for="bukti" class="form-label fw-semibold">Bukti Transaksi</label>
                                <input
                                    type="file"
                                    id="bukti"
                                    name="bukti"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    class="form-control @error('bukti') is-invalid @enderror"
                                >
                                <div class="form-text">Format: JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</div>
                                @error('bukti')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="d-flex flex-column-reverse flex-sm-row justify-content-sm-end gap-2">
                    <a href="{{ route('simpanan.index') }}" class="btn btn-outline-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
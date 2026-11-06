<x-app-layout>
    <x-slot name="title">
        Tambah Berita
    </x-slot>

    <div class="container-fluid">

        {{-- Header --}}
        <div class="mb-4">
            <h1 class="h4 fw-bold mb-1">
                Tambah Berita
            </h1>

            <p class="text-muted mb-0">
                Buat berita atau informasi baru untuk website KPGS.
            </p>
        </div>

        {{-- Error Validasi --}}
        @if ($errors->any())
            <div
                class="alert alert-danger mb-4"
                role="alert"
            >
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>

                    <div>
                        <p class="fw-semibold mb-1">
                            Terdapat kesalahan:
                        </p>

                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form
            action="{{ route('admin.berita.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            {{-- Informasi Utama --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h5 fw-bold mb-1">
                        Informasi Berita
                    </h2>

                    <p class="text-muted small mb-0">
                        Masukkan informasi utama berita yang akan ditampilkan.
                    </p>
                </div>

                <div class="card-body p-4">

                    {{-- Judul --}}
                    <div class="mb-4">
                        <label
                            for="judul"
                            class="form-label fw-semibold"
                        >
                            Judul Berita
                        </label>

                        <input
                            type="text"
                            id="judul"
                            name="judul"
                            value="{{ old('judul') }}"
                            required
                            placeholder="Masukkan judul berita"
                            class="form-control @error('judul') is-invalid @enderror"
                        >

                        @error('judul')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            Slug URL akan dibuat otomatis berdasarkan judul.
                        </div>
                    </div>

                    {{-- Ringkasan --}}
                    <div class="mb-4">
                        <label
                            for="ringkasan"
                            class="form-label fw-semibold"
                        >
                            Ringkasan
                        </label>

                        <textarea
                            id="ringkasan"
                            name="ringkasan"
                            rows="4"
                            maxlength="1000"
                            placeholder="Tuliskan ringkasan singkat berita..."
                            class="form-control @error('ringkasan') is-invalid @enderror"
                        >{{ old('ringkasan') }}</textarea>

                        @error('ringkasan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            Ringkasan digunakan sebagai gambaran singkat berita.
                        </div>
                    </div>

                    {{-- Isi --}}
                    <div>
                        <label
                            for="isi"
                            class="form-label fw-semibold"
                        >
                            Isi Berita
                        </label>

                        <textarea
                            id="isi"
                            name="isi"
                            rows="14"
                            required
                            placeholder="Tuliskan isi berita..."
                            class="form-control @error('isi') is-invalid @enderror"
                        >{{ old('isi') }}</textarea>

                        @error('isi')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            Tuliskan isi berita secara lengkap.
                        </div>
                    </div>

                </div>
            </div>

            {{-- Gambar --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h5 fw-bold mb-1">
                        Gambar Berita
                    </h2>

                    <p class="text-muted small mb-0">
                        Tambahkan gambar utama yang berkaitan dengan berita.
                    </p>
                </div>

                <div class="card-body p-4">

                    <label
                        for="gambar"
                        class="form-label fw-semibold"
                    >
                        Gambar
                    </label>

                    <input
                        type="file"
                        id="gambar"
                        name="gambar"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="form-control @error('gambar') is-invalid @enderror"
                    >

                    @error('gambar')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="form-text">
                        <i class="bi bi-image me-1"></i>
                        Format: JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                    </div>

                </div>
            </div>

            {{-- Status --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h5 fw-bold mb-0">
                        Status Publikasi
                    </h2>
                </div>

                <div class="card-body p-4">

                    <div class="alert alert-light border mb-0">
                        <div class="d-flex align-items-start gap-3">
                            <div class="text-primary fs-5">
                                <i class="bi bi-clock-history"></i>
                            </div>

                            <div>
                                <p class="fw-semibold mb-1">
                                    Berita akan disimpan sebagai Draft
                                </p>

                                <p class="text-muted small mb-0">
                                    Setelah tersimpan, Anda dapat mempublikasikannya
                                    dari halaman daftar berita.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Tombol --}}
            <div class="d-flex flex-column-reverse flex-sm-row justify-content-sm-end gap-2">

                <a
                    href="{{ route('admin.berita.index') }}"
                    class="btn btn-outline-secondary"
                >
                    <i class="bi bi-arrow-left me-1"></i>
                    Batal
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-save me-1"></i>
                    Simpan sebagai Draft
                </button>

            </div>

        </form>

    </div>
</x-app-layout>
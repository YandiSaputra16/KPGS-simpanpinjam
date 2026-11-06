<x-app-layout>
    <x-slot name="title">
        Edit Berita
    </x-slot>

    <div class="container-fluid">

        {{-- Header --}}
        <div class="mb-4">
            <h1 class="h4 fw-bold mb-1">
                Edit Berita
            </h1>

            <p class="text-muted mb-0">
                Perbarui informasi berita KPGS.
            </p>
        </div>

        {{-- Pesan sukses --}}
        @if (session('success'))
            <div
                class="alert alert-success d-flex align-items-center gap-2 mb-4"
                role="alert"
            >
                <i class="bi bi-check-circle-fill"></i>

                <div>
                    {{ session('success') }}
                </div>
            </div>
        @endif

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
            action="{{ route('admin.berita.update', $berita) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            {{-- Informasi Berita --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h5 fw-bold mb-1">
                        Informasi Berita
                    </h2>

                    <p class="text-muted small mb-0">
                        Perbarui informasi utama berita.
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
                            value="{{ old('judul', $berita->judul) }}"
                            required
                            class="form-control @error('judul') is-invalid @enderror"
                        >

                        @error('judul')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
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
                            class="form-control @error('ringkasan') is-invalid @enderror"
                        >{{ old('ringkasan', $berita->ringkasan) }}</textarea>

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
                            class="form-control @error('isi') is-invalid @enderror"
                        >{{ old('isi', $berita->isi) }}</textarea>

                        @error('isi')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
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
                        Ganti gambar jika diperlukan.
                    </p>
                </div>

                <div class="card-body p-4">

                    {{-- Gambar Saat Ini --}}
                    @if ($berita->gambar)
                        <div class="mb-4">
                            <p class="form-label fw-semibold mb-2">
                                Gambar Saat Ini
                            </p>

                            <div class="border rounded p-2 bg-light d-inline-block">
                                <img
                                    src="{{ asset('storage/' . $berita->gambar) }}"
                                    alt="{{ $berita->judul }}"
                                    class="img-fluid rounded kpgs-image-contain-256"
                                >
                            </div>
                        </div>
                    @endif

                    {{-- Upload --}}
                    <div>
                        <label
                            for="gambar"
                            class="form-label fw-semibold"
                        >
                            {{ $berita->gambar ? 'Ganti Gambar' : 'Upload Gambar' }}
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
            </div>

            {{-- Status --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h5 fw-bold mb-0">
                        Status Publikasi
                    </h2>
                </div>

                <div class="card-body p-4">

                    @if ($berita->status === 'published')

                        <div class="alert alert-success d-flex align-items-start gap-3 mb-0">
                            <div class="fs-5">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>

                            <div>
                                <p class="fw-semibold mb-1">
                                    Berita sudah dipublikasikan.
                                </p>

                                @if ($berita->published_at)
                                    <p class="small mb-0">
                                        Dipublikasikan pada
                                        {{ $berita->published_at->format('d M Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        </div>

                    @else

                        <div class="alert alert-light border d-flex align-items-start gap-3 mb-0">
                            <div class="fs-5 text-secondary">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>

                            <div>
                                <p class="fw-semibold mb-1">
                                    Berita masih dalam status Draft.
                                </p>

                                <p class="text-muted small mb-0">
                                    Publikasikan berita melalui halaman daftar berita
                                    setelah isinya siap.
                                </p>
                            </div>
                        </div>

                    @endif

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
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>
</x-app-layout>
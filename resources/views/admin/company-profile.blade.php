<x-app-layout>
    <x-slot name="title">
        Company Profile
    </x-slot>

    <div class="container-fluid">

        {{-- Header --}}
        <div class="mb-4">
            <h1 class="h4 fw-bold mb-1">
                Company Profile
            </h1>

            <p class="text-muted mb-0">
                Kelola informasi publik Koperasi Peternak Garut Selatan.
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

        {{-- Error validasi --}}
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

        <form
            action="{{ route('admin.company-profile.update') }}"
            method="POST"
        >
            @csrf
            @method('PUT')

            {{-- Identitas Koperasi --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h5 fw-bold mb-1">
                        Identitas Koperasi
                    </h2>

                    <p class="text-muted small mb-0">
                        Informasi dasar yang ditampilkan pada website KPGS.
                    </p>
                </div>

                <div class="card-body p-4">

                    <div class="row g-4">

                        <div class="col-12 col-md-6">
                            <label
                                for="nama_koperasi"
                                class="form-label fw-semibold"
                            >
                                Nama Koperasi
                            </label>

                            <input
                                type="text"
                                id="nama_koperasi"
                                name="nama_koperasi"
                                value="{{ old('nama_koperasi', $pengaturan->nama_koperasi) }}"
                                required
                                class="form-control @error('nama_koperasi') is-invalid @enderror"
                            >

                            @error('nama_koperasi')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label
                                for="telepon"
                                class="form-label fw-semibold"
                            >
                                Telepon
                            </label>

                            <input
                                type="text"
                                id="telepon"
                                name="telepon"
                                value="{{ old('telepon', $pengaturan->telepon) }}"
                                class="form-control @error('telepon') is-invalid @enderror"
                            >

                            @error('telepon')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label
                                for="email"
                                class="form-label fw-semibold"
                            >
                                Email
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email', $pengaturan->email) }}"
                                class="form-control @error('email') is-invalid @enderror"
                            >

                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label
                                for="whatsapp"
                                class="form-label fw-semibold"
                            >
                                WhatsApp
                            </label>

                            <input
                                type="text"
                                id="whatsapp"
                                name="whatsapp"
                                value="{{ old('whatsapp', $pengaturan->whatsapp) }}"
                                class="form-control @error('whatsapp') is-invalid @enderror"
                            >

                            @error('whatsapp')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label
                                for="alamat"
                                class="form-label fw-semibold"
                            >
                                Alamat
                            </label>

                            <textarea
                                id="alamat"
                                name="alamat"
                                rows="3"
                                class="form-control @error('alamat') is-invalid @enderror"
                            >{{ old('alamat', $pengaturan->alamat) }}</textarea>

                            @error('alamat')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                </div>
            </div>

            {{-- Tentang KPGS --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h5 fw-bold mb-1">
                        Tentang KPGS
                    </h2>

                    <p class="text-muted small mb-0">
                        Informasi mengenai profil, sejarah, visi, dan misi KPGS.
                    </p>
                </div>

                <div class="card-body p-4">

                    {{-- Tentang --}}
                    <div class="mb-4">
                        <label
                            for="tentang"
                            class="form-label fw-semibold"
                        >
                            Tentang KPGS
                        </label>

                        <textarea
                            id="tentang"
                            name="tentang"
                            rows="6"
                            placeholder="Tuliskan gambaran umum mengenai KPGS..."
                            class="form-control @error('tentang') is-invalid @enderror"
                        >{{ old('tentang', $pengaturan->tentang) }}</textarea>

                        @error('tentang')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Sejarah --}}
                    <div class="mb-4">
                        <label
                            for="sejarah"
                            class="form-label fw-semibold"
                        >
                            Sejarah KPGS
                        </label>

                        <textarea
                            id="sejarah"
                            name="sejarah"
                            rows="7"
                            placeholder="Tuliskan sejarah atau perjalanan berdirinya KPGS..."
                            class="form-control @error('sejarah') is-invalid @enderror"
                        >{{ old('sejarah', $pengaturan->sejarah) }}</textarea>

                        @error('sejarah')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Visi dan Misi --}}
                    <div class="row g-4">

                        <div class="col-12 col-md-6">
                            <label
                                for="visi"
                                class="form-label fw-semibold"
                            >
                                Visi
                            </label>

                            <textarea
                                id="visi"
                                name="visi"
                                rows="6"
                                placeholder="Tuliskan visi KPGS..."
                                class="form-control @error('visi') is-invalid @enderror"
                            >{{ old('visi', $pengaturan->visi) }}</textarea>

                            @error('visi')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label
                                for="misi"
                                class="form-label fw-semibold"
                            >
                                Misi
                            </label>

                            <textarea
                                id="misi"
                                name="misi"
                                rows="6"
                                placeholder="Tuliskan misi KPGS..."
                                class="form-control @error('misi') is-invalid @enderror"
                            >{{ old('misi', $pengaturan->misi) }}</textarea>

                            @error('misi')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                </div>
            </div>

            {{-- Informasi Layanan --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h5 fw-bold mb-1">
                        Layanan KPGS
                    </h2>

                    <p class="text-muted small mb-0">
                        Informasi layanan atau unit usaha yang ingin ditampilkan kepada masyarakat.
                    </p>
                </div>

                <div class="card-body p-4">

                    <label
                        for="layanan"
                        class="form-label fw-semibold"
                    >
                        Deskripsi Layanan
                    </label>

                    <textarea
                        id="layanan"
                        name="layanan"
                        rows="8"
                        placeholder="Tuliskan layanan atau unit usaha yang tersedia di KPGS..."
                        class="form-control @error('layanan') is-invalid @enderror"
                    >{{ old('layanan', $pengaturan->layanan) }}</textarea>

                    @error('layanan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>
            </div>

            {{-- Informasi Keanggotaan --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h5 fw-bold mb-1">
                        Informasi Keanggotaan
                    </h2>

                    <p class="text-muted small mb-0">
                        Informasi publik mengenai persyaratan untuk menjadi anggota KPGS.
                    </p>
                </div>

                <div class="card-body p-4">

                    <label
                        for="syarat_anggota"
                        class="form-label fw-semibold"
                    >
                        Syarat Menjadi Anggota
                    </label>

                    <textarea
                        id="syarat_anggota"
                        name="syarat_anggota"
                        rows="8"
                        placeholder="Tuliskan persyaratan menjadi anggota KPGS..."
                        class="form-control @error('syarat_anggota') is-invalid @enderror"
                    >{{ old('syarat_anggota', $pengaturan->syarat_anggota) }}</textarea>

                    @error('syarat_anggota')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>
            </div>

            {{-- Informasi Pinjaman --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h5 fw-bold mb-1">
                        Informasi Pinjaman
                    </h2>

                    <p class="text-muted small mb-0">
                        Informasi umum mengenai prosedur pengajuan pinjaman.
                    </p>
                </div>

                <div class="card-body p-4">

                    <label
                        for="prosedur_pinjaman"
                        class="form-label fw-semibold"
                    >
                        Prosedur Pinjaman
                    </label>

                    <textarea
                        id="prosedur_pinjaman"
                        name="prosedur_pinjaman"
                        rows="8"
                        placeholder="Tuliskan prosedur pengajuan pinjaman..."
                        class="form-control @error('prosedur_pinjaman') is-invalid @enderror"
                    >{{ old('prosedur_pinjaman', $pengaturan->prosedur_pinjaman) }}</textarea>

                    @error('prosedur_pinjaman')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>
            </div>

            {{-- Media Sosial --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h5 fw-bold mb-1">
                        Media Sosial & Lokasi
                    </h2>

                    <p class="text-muted small mb-0">
                        Informasi kontak tambahan yang dapat ditampilkan pada website.
                    </p>
                </div>

                <div class="card-body p-4">

                    <div class="row g-4">

                        <div class="col-12 col-md-6">
                            <label
                                for="instagram"
                                class="form-label fw-semibold"
                            >
                                Instagram
                            </label>

                            <input
                                type="text"
                                id="instagram"
                                name="instagram"
                                value="{{ old('instagram', $pengaturan->instagram) }}"
                                class="form-control @error('instagram') is-invalid @enderror"
                            >

                            @error('instagram')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label
                                for="facebook"
                                class="form-label fw-semibold"
                            >
                                Facebook
                            </label>

                            <input
                                type="text"
                                id="facebook"
                                name="facebook"
                                value="{{ old('facebook', $pengaturan->facebook) }}"
                                class="form-control @error('facebook') is-invalid @enderror"
                            >

                            @error('facebook')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label
                                for="maps_embed"
                                class="form-label fw-semibold"
                            >
                                Google Maps Embed
                            </label>

                            <textarea
                                id="maps_embed"
                                name="maps_embed"
                                rows="4"
                                placeholder="Masukkan kode embed Google Maps..."
                                class="form-control @error('maps_embed') is-invalid @enderror"
                            >{{ old('maps_embed', $pengaturan->maps_embed) }}</textarea>

                            @error('maps_embed')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                Masukkan kode embed Google Maps yang akan digunakan pada halaman publik.
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            {{-- Tombol --}}
            <div class="d-flex justify-content-end">
                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-save me-1"></i>
                    Simpan Company Profile
                </button>
            </div>

        </form>
    </div>
</x-app-layout>
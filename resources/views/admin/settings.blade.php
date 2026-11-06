<x-app-layout>
    <x-slot name="title">
        Pengaturan Aplikasi
    </x-slot>

    <div class="container-fluid">

        {{-- Header --}}
        <div class="mb-4">
            <h1 class="h4 fw-bold mb-1">
                Pengaturan Aplikasi
            </h1>

            <p class="text-muted mb-0">
                Kelola konfigurasi teknis dan tampilan aplikasi KPGS.
            </p>
        </div>

        {{-- Alert --}}
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

        @if ($errors->any())
            <div
                class="alert alert-danger mb-4"
                role="alert"
            >
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>

                    <div>
                        <p class="fw-semibold mb-1">
                            Terdapat kesalahan pada data yang dimasukkan.
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
            action="{{ route('admin.settings.update') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            {{-- Tampilan Aplikasi --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h5 fw-bold mb-1">
                        Tampilan Aplikasi
                    </h2>

                    <p class="text-muted small mb-0">
                        Kelola logo dan favicon yang digunakan aplikasi.
                    </p>
                </div>

                <div class="card-body p-4">

                    <div class="row g-4">

                        {{-- Logo --}}
                        <div class="col-12 col-md-6">
                            <label
                                for="logo"
                                class="form-label fw-semibold"
                            >
                                Logo
                            </label>

                            @if ($pengaturan->logo)
                                <div class="border rounded bg-light p-3 mb-3">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <img
                                            src="{{ $pengaturan->logo_url }}"
                                            alt="Logo KPGS"
                                            class="img-fluid kpgs-logo-settings"
                                        >
                                    </div>
                                </div>
                            @endif

                            <input
                                id="logo"
                                name="logo"
                                type="file"
                                accept=".jpg,.jpeg,.png,.webp"
                                class="form-control @error('logo') is-invalid @enderror"
                            >

                            @error('logo')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                <i class="bi bi-image me-1"></i>
                                Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.
                            </div>
                        </div>

                        {{-- Favicon --}}
                        <div class="col-12 col-md-6">
                            <label
                                for="favicon"
                                class="form-label fw-semibold"
                            >
                                Favicon
                            </label>

                            @if ($pengaturan->favicon)
                                <div class="border rounded bg-light p-3 mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img
                                            src="{{ $pengaturan->favicon_url }}"
                                            alt="Favicon KPGS"
                                            class="img-fluid kpgs-logo-48"
                                        >

                                        <span class="small text-muted">
                                            Favicon saat ini
                                        </span>
                                    </div>
                                </div>
                            @endif

                            <input
                                id="favicon"
                                name="favicon"
                                type="file"
                                accept=".ico,.jpg,.jpeg,.png,.webp"
                                class="form-control @error('favicon') is-invalid @enderror"
                            >

                            @error('favicon')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                <i class="bi bi-image me-1"></i>
                                Format ICO, JPG, JPEG, PNG, atau WEBP. Maksimal 1 MB.
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            {{-- Integrasi --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h5 fw-bold mb-1">
                        Integrasi Sistem
                    </h2>

                    <p class="text-muted small mb-0">
                        Pengaturan layanan eksternal yang digunakan aplikasi.
                    </p>
                </div>

                <div class="card-body p-4">

                    {{-- Google Maps --}}
                    <div class="mb-4">
                        <label
                            for="google_maps_api_key"
                            class="form-label fw-semibold"
                        >
                            Google Maps API Key
                        </label>

                        <input
                            id="google_maps_api_key"
                            name="google_maps_api_key"
                            type="text"
                            value="{{ old('google_maps_api_key', $pengaturan->google_maps_api_key) }}"
                            autocomplete="off"
                            class="form-control font-monospace @error('google_maps_api_key') is-invalid @enderror"
                        >

                        @error('google_maps_api_key')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            Digunakan jika aplikasi membutuhkan integrasi Google Maps API.
                        </div>
                    </div>

                    {{-- Fonnte --}}
                    <div>
                        <label
                            for="fonnte_token"
                            class="form-label fw-semibold"
                        >
                            Fonnte Token
                        </label>

                        <input
                            id="fonnte_token"
                            name="fonnte_token"
                            type="password"
                            value="{{ old('fonnte_token', $pengaturan->fonnte_token) }}"
                            autocomplete="new-password"
                            class="form-control font-monospace @error('fonnte_token') is-invalid @enderror"
                        >

                        @error('fonnte_token')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            Token digunakan untuk integrasi pengiriman WhatsApp melalui Fonnte.
                        </div>
                    </div>

                </div>
            </div>

            {{-- Informasi --}}
            <div class="alert alert-info mb-4" role="alert">
                <div class="d-flex align-items-start gap-3">
                    <div class="fs-5">
                        <i class="bi bi-info-circle-fill"></i>
                    </div>

                    <div>
                        <h3 class="h6 fw-bold mb-1">
                            Catatan keamanan
                        </h3>

                        <p class="small mb-0">
                            API key dan token integrasi merupakan data sensitif.
                            Jangan membagikan nilainya kepada pengguna lain atau
                            memasukkannya ke dalam source code secara langsung.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="d-flex align-items-center justify-content-end gap-2">
                <a
                    href="{{ route('dashboard') }}"
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
                    Simpan Pengaturan
                </button>
            </div>

        </form>
    </div>
</x-app-layout>
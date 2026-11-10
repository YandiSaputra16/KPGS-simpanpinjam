
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>{{ $title ?? ($pengaturan->nama_koperasi ?? 'KPGS') }}</title>

    @if (!empty($pengaturan?->favicon))
        <link
            rel="icon"
            type="image/png"
            href="{{ $pengaturan->favicon_url }}"
        >
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
        rel="stylesheet"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="bg-light text-dark">

    {{-- Navbar --}}
    <header class="sticky-top border-bottom bg-white shadow-sm">
        <nav class="navbar navbar-expand-lg bg-white">
            <div class="container-fluid px-4 px-xl-5 py-2">

                {{-- Logo dan identitas KPGS --}}
                <a
                    href="{{ route('public.home') }}"
                    class="d-flex align-items-center text-decoration-none flex-shrink-0"
                >
                    <img
                        src="{{ asset('image/kpgs logo 1.jpg') }}"
                        alt="Logo KPGS"
                        class="kpgs-navbar-logo me-3"
                    >

                    <div>
                        <div class="fw-bold text-dark">
                            Koperasi Peternak Garut Selatan (KPGS)
                        </div>

                        <div class="text-secondary small">
                            Koperasi Peternak Garut Selatan
                        </div>
                    </div>
                </a>

                {{-- Tombol menu mobile --}}
                <button
                    class="navbar-toggler ms-2"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#publicNavbar"
                    aria-controls="publicNavbar"
                    aria-expanded="false"
                    aria-label="Buka menu"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                {{-- Menu navigasi --}}
                <div
                    class="collapse navbar-collapse"
                    id="publicNavbar"
                >
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">

                        <li class="nav-item">
                            <a
                                href="{{ route('public.home') }}"
                                class="nav-link px-3"
                            >
                                Beranda
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                href="{{ route('public.home') }}#tentang"
                                class="nav-link px-3"
                            >
                                Tentang
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                href="{{ route('public.home') }}#layanan"
                                class="nav-link px-3"
                            >
                                Layanan
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                href="{{ route('public.home') }}#keanggotaan"
                                class="nav-link px-3"
                            >
                                Keanggotaan
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                href="{{ route('public.home') }}#pinjaman"
                                class="nav-link px-3"
                            >
                                Pinjaman
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                href="{{ route('public.news.index') }}"
                                class="nav-link px-3"
                            >
                                Berita
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                href="{{ route('public.home') }}#kontak"
                                class="nav-link px-3"
                            >
                                Kontak
                            </a>
                        </li>

                        <li class="nav-item mt-2 mt-lg-0 ms-lg-2">
                            <a
                                href="{{ route('login') }}"
                                class="btn btn-primary px-4"
                            >
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Login
                            </a>
                        </li>

                    </ul>
                </div>

            </div>
        </nav>
    </header>

    {{-- Konten halaman --}}
    <main>
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer
        id="kontak"
        class="bg-dark text-light"
    >
        <div class="container-fluid px-4 px-xl-5 py-5">
            <div class="row g-4">

                {{-- Identitas koperasi --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <h2 class="h5 fw-bold text-white mb-3">
                        {{ $pengaturan->nama_koperasi ?? 'KPGS' }}
                    </h2>

                    <p class="text-white-50 small mb-0">
                        Koperasi Peternak Garut Selatan.
                    </p>
                </div>

                {{-- Informasi kontak --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <h3 class="h6 fw-semibold text-white mb-3">
                        Kontak
                    </h3>

                    <div class="d-flex flex-column gap-2 small text-white-50">

                        @if ($pengaturan?->alamat)
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-geo-alt mt-1"></i>
                                <span>{{ $pengaturan->alamat }}</span>
                            </div>
                        @endif

                        @if ($pengaturan?->telepon)
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-telephone"></i>
                                <span>
                                    Telepon: {{ $pengaturan->telepon }}
                                </span>
                            </div>
                        @endif

                        @if ($pengaturan?->email)
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-envelope"></i>
                                <span>
                                    Email: {{ $pengaturan->email }}
                                </span>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Tautan --}}
                <div class="col-12 col-md-6 col-lg-4">
                    <h3 class="h6 fw-semibold text-white mb-3">
                        Tautan
                    </h3>

                    <div class="d-flex flex-column gap-2 small">

                        <a
                            href="{{ route('public.home') }}#tentang"
                            class="text-white-50 text-decoration-none"
                        >
                            Tentang KPGS
                        </a>

                        <a
                            href="{{ route('public.home') }}#layanan"
                            class="text-white-50 text-decoration-none"
                        >
                            Layanan
                        </a>

                        <a
                            href="{{ route('public.home') }}#keanggotaan"
                            class="text-white-50 text-decoration-none"
                        >
                            Informasi Anggota
                        </a>

                        <a
                            href="{{ route('public.news.index') }}"
                            class="text-white-50 text-decoration-none"
                        >
                            Berita KPGS
                        </a>

                        <a
                            href="{{ route('login') }}"
                            class="text-white-50 text-decoration-none"
                        >
                            Login Sistem
                        </a>

                    </div>
                </div>

            </div>
        </div>

        {{-- Copyright --}}
        <div class="border-top border-secondary">
            <div class="container-fluid px-4 px-xl-5 py-3">
                <div class="text-center small text-white-50">
                    © {{ date('Y') }}
                    {{ $pengaturan->nama_koperasi ?? 'KPGS' }}.
                    Seluruh hak cipta dilindungi.
                </div>
            </div>
        </div>
    </footer>

</body>
</html>

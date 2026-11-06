<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'KPGS Simpan Pinjam') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
        rel="stylesheet"
    >

   @vite([
    'resources/css/app.css',
    'resources/css/kpgs.css',
    'resources/css/kpgs.theme.css',
    'resources/js/app.js'
    ])
</head>

<body class="bg-light text-dark">
    <div class="min-vh-100 d-flex flex-column">

        {{-- Header aplikasi --}}
        @include('layouts.navigation')

        <div class="d-flex flex-grow-1">

           {{-- Sidebar desktop --}}
        <aside class="d-none d-lg-block kpgs-sidebar-desktop-app kpgs-app-sidebar">
            <div class="position-sticky top-0 overflow-auto kpgs-sidebar-scroll-app">
                <div class="p-3">
                    <x-navigation-menu />
                </div>
            </div>
        </aside>
            {{-- Sidebar mobile --}}
            <div
                class="offcanvas offcanvas-start kpgs-mobile-sidebar d-lg-none"
                tabindex="-1"
                id="mobileSidebar"
                aria-labelledby="mobileSidebarLabel"
            >
                <div class="offcanvas-header kpgs-mobile-sidebar-header">
                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="offcanvas"
                        aria-label="Tutup menu"
                    ></button>
                </div>

                <div class="offcanvas-body p-3">
                    <x-navigation-menu />
                </div>
            </div>

            {{-- Area utama aplikasi --}}
            <div class="flex-grow-1 d-flex flex-column min-vh-100 kpgs-app-main">

                {{-- Judul halaman --}}
                @if (isset($header))
                    <header class="bg-white border-bottom kpgs-app-page-header">
                        <div class="container-fluid px-4 py-3">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                {{-- Konten halaman --}}
                <main class="flex-grow-1 w-100">
                    {{ $slot }}
                </main>

                {{-- Footer aplikasi --}}
                <footer class="bg-white border-top mt-auto">
                    <div class="container-fluid px-4 py-3">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-1 text-muted small">
                            <div>
                                &copy; {{ date('Y') }}
                                Koperasi Peternak Garut Selatan
                            </div>

                            <div>
                                Sistem Simpan Pinjam KPGS
                            </div>
                        </div>
                    </div>
                </footer>

            </div>
        </div>
    </div>
</body>
</html>

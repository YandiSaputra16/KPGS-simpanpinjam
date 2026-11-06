<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'KPGS Simpan Pinjam') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light text-dark">
    <div class="min-vh-100 d-flex flex-column">
        @include('layouts.navigation')
        <div class="d-flex flex-grow-1">
            <aside class="d-none d-lg-block flex-shrink-0 bg-white border-end kpgs-sidebar-desktop">
                <div class="position-sticky top-0 overflow-auto kpgs-sidebar-scroll-dashboard">
                    <div class="p-3"><x-navigation-menu /></div>
                </div>
            </aside>
            <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
                <div class="offcanvas-header border-bottom">
                    <div>
                        <div class="fw-bold" id="mobileSidebarLabel">KPGS</div>
                        <div class="small text-muted">Simpan Pinjam</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup menu"></button>
                </div>
                <div class="offcanvas-body p-3"><x-navigation-menu /></div>
            </div>
            <div class="flex-grow-1  d-flex flex-column">
                @isset($header)
                    <header class="bg-white border-bottom"><div class="container-fluid py-3 px-3 px-md-4">{{ $header }}</div></header>
                @endisset
                <main class="flex-grow-1 w-100 py-4 px-3 px-md-4">{{ $slot }}</main>
                <footer class="bg-white border-top mt-auto"><div class="container-fluid py-3 px-3 px-md-4"><div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-1 small text-muted"><div>&copy; {{ date('Y') }} Koperasi Peternak Garut Selatan</div><div>Sistem Simpan Pinjam KPGS</div></div></div></footer>
            </div>
        </div>
    </div>
</body>
</html>
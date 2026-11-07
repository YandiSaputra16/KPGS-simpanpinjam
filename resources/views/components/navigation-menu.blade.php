@php
    $user = auth()->user();

    $roleLabels = [
        'admin' => 'Administrator',
        'cs' => 'Customer Service',
        'ao' => 'Account Officer',
        'kabag' => 'Kepala Bagian',
        'bendahara' => 'Bendahara',
        'pimpinan' => 'Pimpinan',
        'anggota' => 'Anggota',
    ];

    $role = $user?->role;
    $roleLabel = $roleLabels[$role] ?? 'Pengguna';

    
$laporanMenus = match ($role) {
    'cs' => [
        [
            'label' => 'Laporan Anggota',
            'route' => 'laporan.anggota',
            'active' => 'laporan.anggota',
        ],
        [
            'label' => 'Laporan Simpanan',
            'route' => 'laporan.simpanan',
            'active' => 'laporan.simpanan',
        ],
        [
            'label' => 'Laporan Pinjaman',
            'route' => 'laporan.pinjaman',
            'active' => 'laporan.pinjaman',
        ],
    ],

    'ao' => [
        [
            'label' => 'Laporan Pinjaman',
            'route' => 'laporan.pinjaman',
            'active' => 'laporan.pinjaman',
        ],
    ],

    default => [],
};


    $hasLaporan = count($laporanMenus) > 0;
    $isLaporanActive = request()->routeIs('laporan.*');

    $isAoIndex = request()->routeIs('pinjaman.ao.index');
    $isAoRiwayat = $isAoIndex && request('filter') === 'riwayat';

    $isKabagIndex = request()->routeIs('pinjaman.kabag.index');
    $isKabagRiwayat = $isKabagIndex && request('filter') === 'riwayat';
@endphp

<div class="w-100">

    {{-- IDENTITAS PENGGUNA --}}
    <div class="px-3 pb-3 mb-3 border-bottom">
        <div class="d-flex align-items-center gap-3">
            <div
                class="d-flex align-items-center justify-content-center
                       rounded-circle bg-light text-secondary
                       fw-semibold flex-shrink-0"
                style="width: 40px; height: 40px;"
            >
                {{ strtoupper(substr($user?->nama ?? 'U', 0, 1)) }}
            </div>

            <div class="min-w-0">
                <div class="fw-semibold small text-dark text-truncate">
                    {{ $user?->nama ?? 'Pengguna' }}
                </div>

                <div class="small text-muted text-truncate">
                    {{ $roleLabel }}
                </div>
            </div>
        </div>
    </div>

    {{-- MENU UTAMA --}}
    <div class="px-3 mb-2">
        <span class="small fw-semibold text-uppercase text-muted">
            Menu Utama
        </span>
    </div>

    <nav class="nav flex-column gap-1" aria-label="Menu utama">

        {{-- DASHBOARD --}}
        <a
            href="{{ route('dashboard') }}"
            class="nav-link rounded px-3 py-2 d-flex align-items-center gap-2
                {{ request()->routeIs('dashboard')
                    ? 'bg-light text-dark fw-semibold'
                    : 'text-secondary' }}"
        >
            <i class="bi bi-grid me-1"></i>
            <span>Dashboard</span>
        </a>

        {{-- ADMIN --}}
        @if ($role === 'admin')

            <a
                href="{{ route('users.index') }}"
                class="nav-link rounded px-3 py-2
                    {{ request()->routeIs('users.*')
                        ? 'bg-light text-dark fw-semibold'
                        : 'text-secondary' }}"
            >
                <i class="bi bi-people me-2"></i>
                Manajemen Pengguna
            </a>

            <a
                href="{{ route('admin.company-profile') }}"
                class="nav-link rounded px-3 py-2
                    {{ request()->routeIs('admin.company-profile*')
                        ? 'bg-light text-dark fw-semibold'
                        : 'text-secondary' }}"
            >
                <i class="bi bi-building me-2"></i>
                Company Profile
            </a>

            <a
                href="{{ route('admin.berita.index') }}"
                class="nav-link rounded px-3 py-2
                    {{ request()->routeIs('admin.berita.*')
                        ? 'bg-light text-dark fw-semibold'
                        : 'text-secondary' }}"
            >
                <i class="bi bi-newspaper me-2"></i>
                Berita KPGS
            </a>

        @endif

        {{-- CUSTOMER SERVICE --}}
        @if ($role === 'cs')

            <a
                href="{{ route('anggota.index') }}"
                class="nav-link rounded px-3 py-2
                    {{ request()->routeIs('anggota.*')
                        ? 'bg-light text-dark fw-semibold'
                        : 'text-secondary' }}"
            >
                <i class="bi bi-people me-2"></i>
                Manajemen Anggota
            </a>

            <a
                href="{{ route('simpanan.index') }}"
                class="nav-link rounded px-3 py-2
                    {{ request()->routeIs(
                        'simpanan.index',
                        'simpanan.show',
                        'simpanan.create'
                    ) ? 'bg-light text-dark fw-semibold' : 'text-secondary' }}"
            >
                <i class="bi bi-piggy-bank me-2"></i>
                Verifikasi Simpanan
            </a>

            <a
                href="{{ route('angsuran.index') }}"
                class="nav-link rounded px-3 py-2
                    {{ request()->routeIs(
                        'angsuran.index',
                        'angsuran.show'
                    ) ? 'bg-light text-dark fw-semibold' : 'text-secondary' }}"
            >
                <i class="bi bi-calendar-check me-2"></i>
                Verifikasi Angsuran
            </a>

            <a
                href="{{ route('pinjaman.cs.index') }}"
                class="nav-link rounded px-3 py-2
                    {{ request()->routeIs('pinjaman.cs.*')
                        ? 'bg-light text-dark fw-semibold'
                        : 'text-secondary' }}"
            >
                <i class="bi bi-file-earmark-text me-2"></i>
                Pengajuan Pinjaman
            </a>

        @endif

        {{-- ACCOUNT OFFICER --}}
        @if ($role === 'ao')

            <a
                href="{{ route('pinjaman.ao.index') }}"
                class="nav-link rounded px-3 py-2
                    {{ $isAoIndex && ! $isAoRiwayat
                        ? 'bg-light text-dark fw-semibold'
                        : 'text-secondary' }}"
            >
                <i class="bi bi-clipboard-check me-2"></i>
                Analisis Pinjaman
            </a>

            <a
                href="{{ route('pinjaman.ao.index', ['filter' => 'riwayat']) }}"
                class="nav-link rounded px-3 py-2
                    {{ $isAoRiwayat
                        ? 'bg-light text-dark fw-semibold'
                        : 'text-secondary' }}"
            >
                <i class="bi bi-clock-history me-2"></i>
                Riwayat Analisis
            </a>

        @endif

       
{{-- KEPALA BAGIAN --}}
@if ($role === 'kabag')

    {{-- SIMPANAN --}}
    <a
        href="{{ route('pencairan-simpanan.kabag.index') }}"
        class="nav-link rounded px-3 py-2
            {{ request()->routeIs('pencairan-simpanan.kabag.*')
                ? 'bg-light text-dark fw-semibold'
                : 'text-secondary' }}"
    >
        <i class="bi bi-piggy-bank me-2"></i>
        Simpanan
    </a>

    {{-- PINJAMAN --}}
    <a
        href="{{ route('pinjaman.kabag.index') }}"
        class="nav-link rounded px-3 py-2
            {{ request()->routeIs('pinjaman.kabag.*')
                ? 'bg-light text-dark fw-semibold'
                : 'text-secondary' }}"
    >
        <i class="bi bi-cash-stack me-2"></i>
        Pinjaman
    </a>

    {{-- AKTIVITAS SISTEM --}}
    @if (Route::has('kabag.activities'))
        <a
            href="{{ route('kabag.activities') }}"
            class="nav-link rounded px-3 py-2
                {{ request()->routeIs('kabag.activities')
                    ? 'bg-light text-dark fw-semibold'
                    : 'text-secondary' }}"
        >
            <i class="bi bi-activity me-2"></i>
            Aktivitas Sistem
        </a>
    @endif

    {{-- LAPORAN KEUANGAN --}}
    @if (Route::has('laporan.keuangan'))
        <a
            href="{{ route('laporan.keuangan') }}"
            class="nav-link rounded px-3 py-2
                {{ request()->routeIs('laporan.keuangan')
                    ? 'bg-light text-dark fw-semibold'
                    : 'text-secondary' }}"
        >
            <i class="bi bi-file-earmark-bar-graph me-2"></i>
            Laporan Keuangan
        </a>
    @endif

@endif


{{-- BENDAHARA --}}
@if ($role === 'bendahara')

    {{-- SIMPANAN DAN PENCAIRAN SIMPANAN --}}
    <a
        href="{{ route('pencairan-simpanan.bendahara.index') }}"
        class="nav-link rounded px-3 py-2
            {{ request()->routeIs('pencairan-simpanan.bendahara.*')
                ? 'bg-light text-dark fw-semibold'
                : 'text-secondary' }}"
    >
        <i class="bi bi-piggy-bank me-2"></i>
        Simpanan
    </a>

    {{-- PINJAMAN DAN PENCAIRAN PINJAMAN --}}
    <a
        href="{{ route('pinjaman.bendahara.index') }}"
        class="nav-link rounded px-3 py-2
            {{ request()->routeIs('pinjaman.bendahara.*')
                ? 'bg-light text-dark fw-semibold'
                : 'text-secondary' }}"
    >
        <i class="bi bi-cash-stack me-2"></i>
        Pinjaman
    </a>

    {{-- KAS --}}
    <a
        href="{{ route('laporan.kas') }}"
        class="nav-link rounded px-3 py-2
            {{ request()->routeIs('laporan.kas')
                ? 'bg-light text-dark fw-semibold'
                : 'text-secondary' }}"
    >
        <i class="bi bi-wallet2 me-2"></i>
        Kas
    </a>

    {{-- LAPORAN KEUANGAN --}}
    @if (Route::has('laporan.keuangan'))
        <a
            href="{{ route('laporan.keuangan') }}"
            class="nav-link rounded px-3 py-2
                {{ request()->routeIs('laporan.keuangan')
                    ? 'bg-light text-dark fw-semibold'
                    : 'text-secondary' }}"
        >
            <i class="bi bi-file-earmark-bar-graph me-2"></i>
            Laporan Keuangan
        </a>
    @endif

@endif
        
{{-- ANGGOTA --}}
@if ($role === 'anggota')

    {{-- PINJAMAN SAYA --}}
    <a
        href="{{ route('pinjaman.anggota.index') }}"
        class="nav-link rounded px-3 py-2 d-flex align-items-center gap-2
            {{ request()->routeIs('pinjaman.anggota.*')
                ? 'bg-light text-dark fw-semibold'
                : 'text-secondary' }}"
    >
        <i class="bi bi-cash-stack"></i>
        <span>Pinjaman Saya</span>
    </a>

    {{-- ANGSURAN SAYA --}}
    <a
        href="{{ route('angsuran.saya.index') }}"
        class="nav-link rounded px-3 py-2 d-flex align-items-center gap-2
            {{ request()->routeIs('angsuran.saya.*')
                ? 'bg-light text-dark fw-semibold'
                : 'text-secondary' }}"
    >
        <i class="bi bi-calendar-check"></i>
        <span>Angsuran Saya</span>
    </a>

    {{-- SIMPANAN SAYA --}}
    <a
        href="{{ route('simpanan.saya.index') }}"
        class="nav-link rounded px-3 py-2 d-flex align-items-center gap-2
            {{ request()->routeIs('simpanan.saya.*')
                ? 'bg-light text-dark fw-semibold'
                : 'text-secondary' }}"
    >
        <i class="bi bi-piggy-bank"></i>
        <span>Simpanan Saya</span>
    </a>

@endif


        {{-- PELAPORAN CS DAN AO --}}
        @if ($hasLaporan && in_array($role, ['cs', 'ao'], true))

            <div class="mt-3 pt-2 border-top">

                <div class="px-3 mb-1">
                    <span class="small fw-semibold text-uppercase text-muted">
                        Pelaporan
                    </span>
                </div>

                @foreach ($laporanMenus as $laporan)
                    @if (Route::has($laporan['route']))
                        <a
                            href="{{ route($laporan['route']) }}"
                            class="nav-link rounded px-3 py-2
                                {{ request()->routeIs($laporan['active'])
                                    ? 'bg-light text-dark fw-semibold'
                                    : 'text-secondary' }}"
                        >
                           @if ($laporan['route'] === 'laporan.anggota')
                                    <i class="bi bi-people me-2"></i>
                                @elseif ($laporan['route'] === 'laporan.simpanan')
                                    <i class="bi bi-piggy-bank me-2"></i>
                                @else
                                    <i class="bi bi-file-earmark-bar-graph me-2"></i>
                                @endif
                            {{ $laporan['label'] }}
                        </a>
                    @endif
                    
                @endforeach

            </div>

        @endif

    </nav>
</div>
@php
    $dashboardViews = [
        'admin' => 'dashboard.admin',
        'cs' => 'dashboard.cs',
        'ao' => 'dashboard.ao',
        'kabag' => 'dashboard.kabag',
        'bendahara' => 'dashboard.bendahara',
        'pimpinan' => 'dashboard.pimpinan',
        'anggota' => 'dashboard.anggota',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 fw-bold mb-1">
                    Dashboard
                </h2>
                <p class="text-muted mb-0">
                    Sistem Informasi Simpan Pinjam KPGS
                </p>
            </div>
        </div>
    </x-slot>

    <main class="container-fluid py-4">
        @if (isset($dashboardViews[$role]))
            @include($dashboardViews[$role], [
                'data' => $data ?? [],
            ])
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-exclamation-circle fs-1 text-warning mb-3"></i>

                    <h5 class="fw-bold mb-2">
                        Dashboard Tidak Tersedia
                    </h5>

                    <p class="text-muted mb-0">
                        Dashboard untuk role Anda belum tersedia.
                    </p>
                </div>
            </div>
        @endif
    </main>
</x-app-layout>
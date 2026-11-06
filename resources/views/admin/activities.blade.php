
<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="h4 fw-bold mb-1">
                Aktivitas Sistem
            </h2>

            <p class="text-muted mb-0">
                Pemantauan aktivitas sistem dan ringkasan arus keuangan KPGS.
            </p>
        </div>
    </x-slot>

    <div class="container-fluid">

        {{-- Ringkasan Arus Keuangan --}}
        <div class="mb-4">
            <div class="mb-3">
                <h5 class="fw-bold mb-1">Ringkasan Arus Keuangan</h5>

                <p class="text-muted small mb-0">
                    Ringkasan transaksi kas berdasarkan periode yang dipilih.
                </p>
            </div>

            <div class="row g-3">

                {{-- Kas Masuk --}}
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <p class="text-muted small mb-2">
                                        Total Kas Masuk
                                    </p>

                                    <h4 class="fw-bold text-success mb-0">
                                        Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
                                    </h4>
                                </div>

                                <div class="bg-light rounded p-2">
                                    <i class="bi bi-arrow-down-left-circle fs-4 text-success"></i>
                                </div>
                            </div>

                            <p class="small text-muted mt-3 mb-0">
                                Total transaksi kas masuk.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Kas Keluar --}}
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <p class="text-muted small mb-2">
                                        Total Kas Keluar
                                    </p>

                                    <h4 class="fw-bold text-danger mb-0">
                                        Rp {{ number_format($totalKasKeluar, 0, ',', '.') }}
                                    </h4>
                                </div>

                                <div class="bg-light rounded p-2">
                                    <i class="bi bi-arrow-up-right-circle fs-4 text-danger"></i>
                                </div>
                            </div>

                            <p class="small text-muted mt-3 mb-0">
                                Total transaksi kas keluar.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Selisih Arus Kas --}}
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between">
                                <div>
                                    <p class="text-muted small mb-2">
                                        Selisih Arus Kas
                                    </p>

                                    <h4 class="fw-bold {{ $selisihArusKas >= 0 ? 'text-primary' : 'text-danger' }} mb-0">
                                        Rp {{ number_format($selisihArusKas, 0, ',', '.') }}
                                    </h4>
                                </div>

                                <div class="bg-light rounded p-2">
                                    <i class="bi bi-wallet2 fs-4 text-primary"></i>
                                </div>
                            </div>

                            <p class="small text-muted mt-3 mb-0">
                                Kas masuk dikurangi kas keluar pada periode terpilih.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Filter Aktivitas --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-funnel text-primary"></i>

                    <h5 class="fw-bold mb-0">
                        Filter Aktivitas
                    </h5>
                </div>
            </div>

            <div class="card-body">
                <form
                    action="{{ route('kabag.activities') }}"
                    method="GET"
                >
                    <div class="row g-3">

                        <div class="col-12 col-md-6 col-xl-3">
                            <label for="dari" class="form-label small fw-semibold">
                                Tanggal Mulai
                            </label>

                            <input
                                type="date"
                                name="dari"
                                id="dari"
                                class="form-control"
                                value="{{ $validated['dari'] ?? '' }}"
                            >
                        </div>

                        <div class="col-12 col-md-6 col-xl-3">
                            <label for="sampai" class="form-label small fw-semibold">
                                Tanggal Selesai
                            </label>

                            <input
                                type="date"
                                name="sampai"
                                id="sampai"
                                class="form-control"
                                value="{{ $validated['sampai'] ?? '' }}"
                            >
                        </div>

                        <div class="col-12 col-md-6 col-xl-2">
                            <label for="action" class="form-label small fw-semibold">
                                Jenis Aktivitas
                            </label>

                            <select
                                name="action"
                                id="action"
                                class="form-select"
                            >
                                <option value="">Semua Aktivitas</option>

                                @foreach ($daftarAksi as $aksi)
                                    <option
                                        value="{{ $aksi }}"
                                        @selected(($validated['action'] ?? '') === $aksi)
                                    >
                                        {{ $aksi }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-6 col-xl-2">
                            <label for="model" class="form-label small fw-semibold">
                                Modul Data
                            </label>

                            <select
                                name="model"
                                id="model"
                                class="form-select"
                            >
                                <option value="">Semua Modul</option>

                                @foreach ($daftarModel as $namaModel)
                                    <option
                                        value="{{ $namaModel }}"
                                        @selected(($validated['model'] ?? '') === $namaModel)
                                    >
                                        {{ $namaModel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-xl-2">
                            <label for="user_id" class="form-label small fw-semibold">
                                Pengguna
                            </label>

                            <select
                                name="user_id"
                                id="user_id"
                                class="form-select"
                            >
                                <option value="">Semua Pengguna</option>

                                @foreach ($daftarPengguna as $pengguna)
                                    <option
                                        value="{{ $pengguna->id }}"
                                        @selected((string) ($validated['user_id'] ?? '') === (string) $pengguna->id)
                                    >
                                        {{ $pengguna->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel me-1"></i>
                                Terapkan Filter
                            </button>

                            <a
                                href="{{ route('kabag.activities') }}"
                                class="btn btn-outline-secondary"
                            >
                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                Reset
                            </a>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Riwayat Aktivitas --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex flex-column flex-sm-row
                            align-items-sm-center justify-content-between gap-2">

                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clipboard-check fs-5 text-primary"></i>

                        <div>
                            <h5 class="fw-bold mb-0">
                                Riwayat Aktivitas
                            </h5>

                            <p class="small text-muted mb-0">
                                Daftar tindakan yang tercatat di dalam sistem.
                            </p>
                        </div>
                    </div>

                    <span class="badge bg-light text-secondary border">
                        {{ number_format($activities->total(), 0, ',', '.') }} data
                    </span>

                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 text-nowrap">Waktu</th>
                                <th class="py-3">Pengguna</th>
                                <th class="py-3">Aktivitas</th>
                                <th class="py-3 pe-4">Keterangan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($activities as $activity)
                                @php
                                    $action = strtolower($activity->action ?? '');

                                    $actionClass = match ($action) {
                                        'create', 'created', 'tambah', 'store', 'register'
                                            => 'bg-success-subtle text-success',

                                        'update', 'updated', 'edit'
                                            => 'bg-warning-subtle text-warning-emphasis',

                                        'delete', 'deleted', 'hapus'
                                            => 'bg-danger-subtle text-danger',

                                        'login'
                                            => 'bg-primary-subtle text-primary',

                                        'logout'
                                            => 'bg-secondary-subtle text-secondary',

                                        default
                                            => 'bg-light text-dark',
                                    };
                                @endphp

                                <tr>
                                    <td class="px-4 py-3 text-nowrap">
                                        <div class="fw-semibold small">
                                            {{ $activity->created_at?->format('d/m/Y') ?? '-' }}
                                        </div>

                                        <div class="small text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $activity->created_at?->format('H:i:s') ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div
                                                class="d-flex align-items-center justify-content-center
                                                       rounded-circle bg-light flex-shrink-0"
                                                style="width: 36px; height: 36px;"
                                            >
                                                @if ($activity->user)
                                                    <span class="fw-semibold text-secondary">
                                                        {{ strtoupper(substr($activity->user->nama ?? 'U', 0, 1)) }}
                                                    </span>
                                                @else
                                                    <i class="bi bi-cpu text-secondary"></i>
                                                @endif
                                            </div>

                                            <div>
                                                <div class="fw-semibold small">
                                                    {{ $activity->user->nama ?? 'Sistem' }}
                                                </div>

                                                <div class="small text-muted">
                                                    {{ $activity->user ? ucfirst($activity->user->role ?? 'Pengguna') : 'Aktivitas otomatis' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="py-3">
                                        <span class="badge {{ $actionClass }}">
                                            {{ $activity->action ?? 'Aktivitas' }}
                                        </span>
                                    </td>

                                    <td class="py-3 pe-4">
                                        <div class="small text-dark">
                                            {{ $activity->deskripsi ?: 'Tidak ada keterangan.' }}
                                        </div>

                                        @if ($activity->model)
                                            <div class="small text-muted mt-1">
                                                <i class="bi bi-database me-1"></i>
                                                {{ $activity->model }}

                                                @if ($activity->model_id)
                                                    #{{ $activity->model_id }}
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="bi bi-search fs-1 text-muted"></i>

                                        <h6 class="fw-bold mt-3 mb-1">
                                            Aktivitas Tidak Ditemukan
                                        </h6>

                                        <p class="text-muted small mb-0">
                                            Tidak ada aktivitas yang sesuai dengan filter.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>

            @if ($activities->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    {{ $activities->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>

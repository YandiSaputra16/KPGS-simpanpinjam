<div>
    {{-- Welcome --}}
    <div class="card border-0 shadow-sm mb-4">
    </div>

    {{-- Status Aplikasi --}}
    <div class="mb-4">
        <div class="mb-3">
            <h3 class="h5 fw-semibold text-dark mb-1">
                Status Aplikasi
            </h3>

            <p class="text-muted small mb-0">
                Ringkasan data yang berkaitan dengan pengelolaan aplikasi.
            </p>
        </div>

        <div class="row g-3">
            {{-- Pengguna Internal --}}
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <p class="text-muted small mb-2">
                                    Pengguna Internal
                                </p>

                                <p class="h3 fw-bold text-dark mb-0">
                                    {{ $data['pengguna_internal'] ?? 0 }}
                                </p>
                            </div>

                            <div class="bg-light rounded p-2">
                                <i class="bi bi-people fs-5 text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Anggota --}}
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <p class="text-muted small mb-2">
                                    Anggota Terdaftar
                                </p>

                                <p class="h3 fw-bold text-dark mb-0">
                                    {{ $data['anggota'] ?? 0 }}
                                </p>
                            </div>

                            <div class="bg-light rounded p-2">
                                <i class="bi bi-person-badge fs-5 text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Akun Aktif --}}
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <p class="text-muted small mb-2">
                                    Akun Aktif
                                </p>

                                <p class="h3 fw-bold text-dark mb-0">
                                    {{ $data['akun_aktif'] ?? 0 }}
                                </p>
                            </div>

                            <div class="bg-light rounded p-2">
                                <i class="bi bi-check-circle fs-5 text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Akun Nonaktif --}}
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <p class="text-muted small mb-2">
                                    Akun Nonaktif
                                </p>

                                <p class="h3 fw-bold text-dark mb-0">
                                    {{ $data['akun_nonaktif'] ?? 0 }}
                                </p>
                            </div>

                            <div class="bg-light rounded p-2">
                                <i class="bi bi-dash-circle fs-5 text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Administrasi Sistem --}}
    <div class="mb-4">
        <div class="mb-3">
            <h3 class="h5 fw-semibold text-dark mb-1">
                Administrasi Sistem
            </h3>

            <p class="text-muted small mb-0">
                Fitur yang digunakan untuk mengelola aplikasi KPGS.
            </p>
        </div>

        <div class="row g-3">
            {{-- Manajemen Pengguna --}}
            <div class="col-12 col-md-6">
                <a
                    href="{{ route('users.index') }}"
                    class="card border-0 shadow-sm h-100 text-decoration-none"
                >
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-light rounded p-3 flex-shrink-0">
                                <i class="bi bi-people fs-5 text-secondary"></i>
                            </div>

                            <div>
                                <h4 class="h6 fw-semibold text-dark mb-1">
                                    Manajemen Pengguna
                                </h4>

                                <p class="text-muted small mb-0">
                                    Kelola akun pengguna internal dan status akses aplikasi.
                                </p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            
{{-- Company Profile --}}
<div class="col-12 col-md-6">
    <a
        href="{{ route('admin.company-profile') }}"
        class="text-decoration-none"
    >
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-light rounded p-3 flex-shrink-0">
                        <i class="bi bi-building fs-5 text-primary"></i>
                    </div>

                    <div class="flex-grow-1">
                        <h4 class="h6 fw-semibold text-dark mb-1">
                            Company Profile
                        </h4>

                        <p class="text-muted small mb-3">
                            Kelola informasi KPGS yang ditampilkan
                            pada halaman publik.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

        </div>
    </div>

</div>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-1 fw-bold">Tambah Pengguna</h2>
                <p class="text-muted mb-0">
                    Tambahkan akun pengguna internal KPGS.
                </p>
            </div>

            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="container-fluid py-4">

        <div class="card shadow-sm border-0">
            <div class="card-body">

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">

                        {{-- Nama --}}
                        <div class="col-md-6">
                            <label for="nama" class="form-label fw-semibold">
                                Nama Lengkap
                            </label>

                            <input
                                type="text"
                                id="nama"
                                name="nama"
                                value="{{ old('nama') }}"
                                class="form-control @error('nama') is-invalid @enderror"
                                placeholder="Masukkan nama lengkap"
                                required
                            >

                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">
                                Email
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="contoh@kpgs.coop"
                                required
                            >

                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Role --}}
                        <div class="col-md-6">
                            <label for="role" class="form-label fw-semibold">
                                Role
                            </label>

                            <select
                                id="role"
                                name="role"
                                class="form-select @error('role') is-invalid @enderror"
                                required
                            >
                                <option value="">-- Pilih Role --</option>

                                @foreach ($roles as $value => $label)
                                    <option
                                        value="{{ $value }}"
                                        @selected(old('role') === $value)
                                    >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            @error('role')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">
                                Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                class="form-select @error('status') is-invalid @enderror"
                                required
                            >
                                <option value="aktif" @selected(old('status', 'aktif') === 'aktif')>
                                    Aktif
                                </option>

                                <option value="nonaktif" @selected(old('status') === 'nonaktif')>
                                    Nonaktif
                                </option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">
                                Password
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Minimal 8 karakter"
                                required
                            >

                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                Konfirmasi Password
                            </label>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Ulangi password"
                                required
                            >
                        </div>

                    </div>

                    <div class="alert alert-info mt-4 mb-0">
                        <strong>Informasi:</strong>
                        Form ini digunakan Admin untuk membuat akun pengguna internal
                        KPGS. Akun anggota tidak dibuat melalui menu ini.
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('users.index') }}"
                           class="btn btn-outline-secondary">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Simpan Pengguna
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</x-app-layout>
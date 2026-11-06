<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-1 fw-bold">Edit Pengguna</h2>
                <p class="text-muted mb-0">
                    Perbarui informasi akun pengguna internal KPGS.
                </p>
            </div>

            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="container-fluid py-4">

        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-2">
                    Terdapat kesalahan pada data yang dimasukkan:
                </div>

                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body">

                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

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
                                value="{{ old('nama', $user->nama) }}"
                                class="form-control @error('nama') is-invalid @enderror"
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
                                value="{{ old('email', $user->email) }}"
                                class="form-control @error('email') is-invalid @enderror"
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
                                @foreach ($roles as $value => $label)
                                    <option
                                        value="{{ $value }}"
                                        @selected(old('role', $user->role) === $value)
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
                                <option
                                    value="aktif"
                                    @selected(old('status', $user->status) === 'aktif')
                                >
                                    Aktif
                                </option>

                                <option
                                    value="nonaktif"
                                    @selected(old('status', $user->status) === 'nonaktif')
                                >
                                    Nonaktif
                                </option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Password Baru --}}
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">
                                Password Baru
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Kosongkan jika tidak ingin mengubah"
                            >

                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                Kosongkan jika password pengguna tidak ingin diubah.
                            </div>
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label fw-semibold">
                                Konfirmasi Password Baru
                            </label>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Ulangi password baru"
                            >
                        </div>

                    </div>

                    <div class="alert alert-warning mt-4 mb-0">
                        <strong>Perhatian:</strong>
                        Perubahan role akan langsung memengaruhi hak akses pengguna
                        setelah akun digunakan kembali.
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('users.index') }}"
                           class="btn btn-outline-secondary">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</x-app-layout>
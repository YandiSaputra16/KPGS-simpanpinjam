<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-1 fw-bold">Tambah Anggota</h2>
                <p class="text-muted mb-0">
                    Mendaftarkan anggota dan membuat akun anggota.
                </p>
            </div>

            <a href="{{ route('anggota.index') }}" class="btn btn-outline-secondary">
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

                <form action="{{ route('anggota.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">

                        {{-- DATA AKUN --}}
                        <div class="col-12">
                            <h5 class="fw-bold mb-1">Data Akun</h5>
                            <p class="text-muted small mb-0">
                                Data ini digunakan anggota untuk masuk ke sistem.
                            </p>
                        </div>

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
                                placeholder="contoh@email.com"
                                required
                            >

                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">
                                Password Awal
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

                            <div class="form-text">
                                Password ini diberikan kepada anggota untuk login pertama kali.
                            </div>
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

                        {{-- DATA ANGGOTA --}}
                        <div class="col-12 mt-4">
                            <hr>
                            <h5 class="fw-bold mb-1">Data Anggota</h5>
                            <p class="text-muted small mb-0">
                                Informasi administrasi anggota KPGS.
                            </p>
                        </div>

                        {{-- NIK --}}
                        <div class="col-md-6">
                            <label for="nik" class="form-label fw-semibold">
                                NIK
                            </label>

                            <input
                                type="text"
                                id="nik"
                                name="nik"
                                value="{{ old('nik') }}"
                                class="form-control @error('nik') is-invalid @enderror"
                                placeholder="16 digit NIK"
                                maxlength="16"
                                inputmode="numeric"
                                required
                            >

                            @error('nik')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- No HP --}}
                        <div class="col-md-6">
                            <label for="no_hp" class="form-label fw-semibold">
                                Nomor HP
                            </label>

                            <input
                                type="text"
                                id="no_hp"
                                name="no_hp"
                                value="{{ old('no_hp') }}"
                                class="form-control @error('no_hp') is-invalid @enderror"
                                placeholder="08xxxxxxxxxx"
                                required
                            >

                            @error('no_hp')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Pekerjaan --}}
                        <div class="col-md-6">
                            <label for="pekerjaan" class="form-label fw-semibold">
                                Pekerjaan
                            </label>

                            <input
                                type="text"
                                id="pekerjaan"
                                name="pekerjaan"
                                value="{{ old('pekerjaan') }}"
                                class="form-control @error('pekerjaan') is-invalid @enderror"
                                placeholder="Masukkan pekerjaan"
                            >

                            @error('pekerjaan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Tanggal Daftar --}}
                        <div class="col-md-6">
                            <label for="tanggal_daftar" class="form-label fw-semibold">
                                Tanggal Daftar
                            </label>

                            <input
                                type="date"
                                id="tanggal_daftar"
                                name="tanggal_daftar"
                                value="{{ old('tanggal_daftar', now()->format('Y-m-d')) }}"
                                class="form-control @error('tanggal_daftar') is-invalid @enderror"
                                required
                            >

                            @error('tanggal_daftar')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Alamat --}}
                        <div class="col-12">
                            <label for="alamat" class="form-label fw-semibold">
                                Alamat
                            </label>

                            <textarea
                                id="alamat"
                                name="alamat"
                                rows="4"
                                class="form-control @error('alamat') is-invalid @enderror"
                                placeholder="Masukkan alamat lengkap"
                                required
                            >{{ old('alamat') }}</textarea>

                            @error('alamat')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                    <div class="col-12 mt-4">
                        <hr>
                        <h5 class="fw-bold mb-1">Informasi Rekening Anggota</h5>
                        <p class="text-muted small mb-0">Rekening ini digunakan sebagai tujuan pencairan pinjaman dan simpanan. Data dapat dilengkapi kemudian melalui profil anggota.</p>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-4">
                            <label for="nama_bank" class="form-label fw-semibold">Nama Bank</label>
                            <input type="text" id="nama_bank" name="nama_bank" value="{{ old('nama_bank') }}" class="form-control @error('nama_bank') is-invalid @enderror" maxlength="100">
                            @error('nama_bank')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="nomor_rekening" class="form-label fw-semibold">Nomor Rekening</label>
                            <input type="text" id="nomor_rekening" name="nomor_rekening" value="{{ old('nomor_rekening') }}" class="form-control @error('nomor_rekening') is-invalid @enderror" maxlength="50" inputmode="numeric">
                            @error('nomor_rekening')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="atas_nama_rekening" class="form-label fw-semibold">Atas Nama Rekening</label>
                            <input type="text" id="atas_nama_rekening" name="atas_nama_rekening" value="{{ old('atas_nama_rekening') }}" class="form-control @error('atas_nama_rekening') is-invalid @enderror" maxlength="150">
                            @error('atas_nama_rekening')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="alert alert-info mt-4 mb-0">
                        <strong>Informasi:</strong>
                        Akun yang dibuat melalui form ini otomatis memiliki role
                        <strong>Anggota</strong>. Kode anggota akan dibuat otomatis oleh sistem.
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('anggota.index') }}"
                           class="btn btn-outline-secondary">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary">
                            Simpan Anggota
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</x-app-layout>
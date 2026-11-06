<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-1 fw-bold">Edit Anggota</h2>
                <p class="text-muted mb-0">
                    Perbarui data anggota dan akun anggota KPGS.
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

               <form action="{{ route('anggota.update', ['anggota' => $anggota->getKey()]) }}"
                        method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">

                        {{-- INFORMASI ANGGOTA --}}
                        <div class="col-12">
                            <h5 class="fw-bold mb-1">Informasi Anggota</h5>
                            <p class="text-muted small mb-0">
                                Data identitas dan keanggotaan anggota.
                            </p>
                        </div>

                        {{-- Kode Anggota --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Kode Anggota
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                value="{{ $anggota->kode_anggota }}"
                                readonly
                            >

                            <div class="form-text">
                                Kode anggota dibuat otomatis oleh sistem dan tidak dapat diubah.
                            </div>
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
                                value="{{ old('nama', $anggota->user?->nama) }}"
                                class="form-control @error('nama') is-invalid @enderror"
                                required
                            >

                            @error('nama')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
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
                                value="{{ old('nik', $anggota->nik) }}"
                                class="form-control @error('nik') is-invalid @enderror"
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
                                value="{{ old('no_hp', $anggota->no_hp) }}"
                                class="form-control @error('no_hp') is-invalid @enderror"
                                required
                            >

                            @error('no_hp')
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
                                value="{{ old('email', $anggota->user?->email) }}"
                                class="form-control @error('email') is-invalid @enderror"
                                required
                            >

                            @error('email')
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
                                value="{{ old('pekerjaan', $anggota->pekerjaan) }}"
                                class="form-control @error('pekerjaan') is-invalid @enderror"
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
                                value="{{ old('tanggal_daftar', optional($anggota->tanggal_daftar)->format('Y-m-d')) }}"
                                class="form-control @error('tanggal_daftar') is-invalid @enderror"
                                required
                            >

                            @error('tanggal_daftar')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Status Keanggotaan --}}
                        <div class="col-md-6">
                            <label for="status_keanggotaan" class="form-label fw-semibold">
                                Status Keanggotaan
                            </label>

                            <select
                                id="status_keanggotaan"
                                name="status_keanggotaan"
                                class="form-select @error('status_keanggotaan') is-invalid @enderror"
                                required
                            >
                                <option
                                    value="aktif"
                                    @selected(old('status_keanggotaan', $anggota->status_keanggotaan) === 'aktif')
                                >
                                    Aktif
                                </option>

                                <option
                                    value="nonaktif"
                                    @selected(old('status_keanggotaan', $anggota->status_keanggotaan) === 'nonaktif')
                                >
                                    Nonaktif
                                </option>
                            </select>

                            @error('status_keanggotaan')
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
                                required
                            >{{ old('alamat', $anggota->alamat) }}</textarea>

                            @error('alamat')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- AKUN --}}
                        <div class="col-12 mt-4">
                            <hr>

                            <h5 class="fw-bold mb-1">Akun Anggota</h5>

                            <p class="text-muted small mb-0">
                                Password hanya perlu diisi jika ingin mengganti password anggota.
                            </p>
                        </div>

                        {{-- Password --}}
                        <div class="col-md-6">
                            <label for="password" class="form-label fw-semibold">
                                Password Baru
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Kosongkan jika tidak diubah"
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

                    <div class="alert alert-info mt-4 mb-0">
                        <strong>Catatan:</strong>
                        Perubahan status keanggotaan juga akan menyesuaikan status
                        akun login anggota.
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('anggota.index') }}"
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
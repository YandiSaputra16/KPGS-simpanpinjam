
<section>
    <div class="mb-4">
        <h2 class="h5 fw-bold text-dark mb-2">
            Informasi Profil
        </h2>

        <p class="text-muted small mb-0">
            Perbarui nama dan alamat email akun Anda.
        </p>
    </div>

    <form
        id="send-verification"
        method="post"
        action="{{ route('verification.send') }}"
    >
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-4">
            <label for="nama" class="form-label fw-semibold">
                Nama Lengkap
            </label>

            <input
                id="nama"
                name="nama"
                type="text"
                class="form-control @error('nama') is-invalid @enderror"
                value="{{ old('nama', $user->nama) }}"
                required
                autofocus
                autocomplete="name"
            >

            @error('nama')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="form-label fw-semibold">
                Email
            </label>

            <input
                id="email"
                name="email"
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="email"
            >

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

            @if (
                $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
                && ! $user->hasVerifiedEmail()
            )
                <div class="mt-3">
                    <div class="alert alert-warning mb-0">
                        <p class="mb-2 small">
                            Alamat email Anda belum diverifikasi.
                        </p>

                        <button
                            form="send-verification"
                            type="submit"
                            class="btn btn-link p-0 text-decoration-underline small"
                        >
                            Kirim ulang email verifikasi
                        </button>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success mt-3 mb-0">
                            <span class="small">
                                Email verifikasi baru telah dikirim.
                            </span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        @if ($user->role === 'anggota' && $anggota)
            <hr class="my-4">
            <div class="mb-3">
                <h3 class="h6 fw-bold mb-1">Data Keanggotaan</h3>
                <p class="text-muted small mb-0">Perbarui identitas dan informasi kontak anggota.</p>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="nik" class="form-label fw-semibold">NIK</label>
                    <input id="nik" name="nik" type="text" maxlength="16" inputmode="numeric" value="{{ old('nik', $anggota->nik) }}" class="form-control @error('nik') is-invalid @enderror" required>
                    @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="no_hp" class="form-label fw-semibold">Nomor HP</label>
                    <input id="no_hp" name="no_hp" type="text" maxlength="20" value="{{ old('no_hp', $anggota->no_hp) }}" class="form-control @error('no_hp') is-invalid @enderror" required>
                    @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="pekerjaan" class="form-label fw-semibold">Pekerjaan</label>
                    <input id="pekerjaan" name="pekerjaan" type="text" maxlength="255" value="{{ old('pekerjaan', $anggota->pekerjaan) }}" class="form-control @error('pekerjaan') is-invalid @enderror">
                    @error('pekerjaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="alamat" class="form-label fw-semibold">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $anggota->alamat) }}</textarea>
                    @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <hr class="my-4">
            <div class="mb-3">
                <h3 class="h6 fw-bold mb-1">Informasi Rekening Pencairan</h3>
                <p class="text-muted small mb-0">Rekening ini menjadi tujuan transfer pencairan pinjaman dan simpanan. Pastikan data sesuai dengan buku rekening.</p>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="nama_bank" class="form-label fw-semibold">Nama Bank</label>
                    <input id="nama_bank" name="nama_bank" type="text" maxlength="100" value="{{ old('nama_bank', $anggota->nama_bank) }}" class="form-control @error('nama_bank') is-invalid @enderror">
                    @error('nama_bank')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="nomor_rekening" class="form-label fw-semibold">Nomor Rekening</label>
                    <input id="nomor_rekening" name="nomor_rekening" type="text" maxlength="50" inputmode="numeric" value="{{ old('nomor_rekening', $anggota->nomor_rekening) }}" class="form-control @error('nomor_rekening') is-invalid @enderror">
                    @error('nomor_rekening')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="atas_nama_rekening" class="form-label fw-semibold">Atas Nama Rekening</label>
                    <input id="atas_nama_rekening" name="atas_nama_rekening" type="text" maxlength="150" value="{{ old('atas_nama_rekening', $anggota->atas_nama_rekening) }}" class="form-control @error('atas_nama_rekening') is-invalid @enderror">
                    @error('atas_nama_rekening')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        @endif

        <div class="d-flex flex-wrap align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i>
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <span
                    data-auto-hide="2000"
                    class="text-success small"
                >
                    <i class="bi bi-check-circle me-1"></i>
                    Perubahan berhasil disimpan.
                </span>
            @endif
        </div>
    </form>
</section>

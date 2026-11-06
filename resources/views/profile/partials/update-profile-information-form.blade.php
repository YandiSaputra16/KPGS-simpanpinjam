
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

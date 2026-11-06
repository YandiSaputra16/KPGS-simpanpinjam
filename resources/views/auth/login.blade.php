
<x-guest-layout>
    <div class="kpgs-login-card">
        {{-- Identitas KPGS --}}
        <div class="kpgs-login-brand">
            <div class="kpgs-login-logo">
                <img
                    src="{{ asset('image/kpgs logo 1.jpg') }}"
                    alt="Logo KPGS"
                >
            </div>

            <h1 class="kpgs-login-brand-title">KPGS</h1>
            <p class="kpgs-login-brand-subtitle">
                Sistem Simpan Pinjam
            </p>
        </div>

        {{-- Form Login --}}
        <div class="kpgs-login-form-card">
            <div class="mb-4">
                <h2 class="kpgs-login-title">Login</h2>
                <p class="kpgs-login-description">
                    Silakan masuk ke akun Anda.
                </p>
            </div>

            <x-auth-session-status
                class="mb-4"
                :status="session('status')"
            />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">
                        {{ __('Email') }}
                    </label>

                    <input
                        id="email"
                        name="email"
                        type="email"
                        class="form-control kpgs-login-input @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Masukkan email"
                    >

                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">
                        {{ __('Password') }}
                    </label>

                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="form-control kpgs-login-input @error('password') is-invalid @enderror"
                        required
                        autocomplete="current-password"
                        placeholder="Masukkan kata sandi"
                    >

                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="form-check mb-4">
                    <input
                        id="remember_me"
                        type="checkbox"
                        class="form-check-input"
                        name="remember"
                    >

                    <label for="remember_me" class="form-check-label text-muted small">
                        {{ __('Remember me') }}
                    </label>
                </div>

                {{-- Tombol Login --}}
                <button
                    type="submit"
                    class="btn btn-primary kpgs-login-button w-100"
                >
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    {{ __('Log in') }}
                </button>

                @if (Route::has('password.request'))
                    <div class="text-center mt-3">
                        <a
                            href="{{ route('password.request') }}"
                            class="kpgs-login-forgot"
                        >
                            {{ __('Forgot your password?') }}
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <p class="kpgs-login-footer">
            &copy; {{ date('Y') }} Koperasi Peternak Garut Selatan
        </p>
    </div>
</x-guest-layout>

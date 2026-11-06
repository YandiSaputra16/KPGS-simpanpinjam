<x-guest-layout>
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark mb-2">
            {{ __('Register') }}
        </h1>

        <p class="text-muted small mb-0">
            Buat akun untuk menggunakan Sistem Simpan Pinjam KPGS.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <div class="mb-4">
            <label for="name" class="form-label fw-semibold">
                {{ __('Name') }}
            </label>

            <input
                id="name"
                name="name"
                type="text"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
            >

            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label for="email" class="form-label fw-semibold">
                {{ __('Email') }}
            </label>

            <input
                id="email"
                name="email"
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                required
                autocomplete="username"
            >

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-4">
            <label for="password" class="form-label fw-semibold">
                {{ __('Password') }}
            </label>

            <input
                id="password"
                name="password"
                type="password"
                class="form-control @error('password') is-invalid @enderror"
                required
                autocomplete="new-password"
            >

            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-semibold">
                {{ __('Confirm Password') }}
            </label>

            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                required
                autocomplete="new-password"
            >

            @error('password_confirmation')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <a
                href="{{ route('login') }}"
                class="small text-decoration-none"
            >
                {{ __('Already registered?') }}
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-1"></i>
                {{ __('Register') }}
            </button>
        </div>
    </form>
</x-guest-layout>
<x-guest-layout>
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark mb-2">
            {{ __('Reset Password') }}
        </h1>

        <p class="text-muted small mb-0">
            Silakan masukkan password baru untuk akun Anda.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        {{-- Password Reset Token --}}
        <input
            type="hidden"
            name="token"
            value="{{ $request->route('token') }}"
        >

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
                value="{{ old('email', $request->email) }}"
                required
                autofocus
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
            <label
                for="password_confirmation"
                class="form-label fw-semibold"
            >
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

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-key me-1"></i>
                {{ __('Reset Password') }}
            </button>
        </div>
    </form>
</x-guest-layout>
<x-guest-layout>
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark mb-2">
            {{ __('Forgot Password') }}
        </h1>

        <p class="text-muted small mb-0">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </p>
    </div>

    <x-auth-session-status
        class="mb-4"
        :status="session('status')"
    />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

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
                autofocus
                autocomplete="email"
            >

            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-envelope me-1"></i>
                {{ __('Email Password Reset Link') }}
            </button>
        </div>
    </form>
</x-guest-layout>
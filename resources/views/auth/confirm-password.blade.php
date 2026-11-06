<x-guest-layout>
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark mb-2">
            {{ __('Confirm Password') }}
        </h1>

        <p class="text-muted small mb-0">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

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
                autofocus
                autocomplete="current-password"
            >

            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-shield-check me-1"></i>
                {{ __('Confirm') }}
            </button>
        </div>
    </form>
</x-guest-layout>
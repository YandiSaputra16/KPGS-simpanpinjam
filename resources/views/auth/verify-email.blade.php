<x-guest-layout>
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark mb-2">
            {{ __('Verify Your Email Address') }}
        </h1>

        <p class="text-muted small mb-0">
            {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-4" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-check-circle mt-1"></i>

                <div class="small">
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </div>
            </div>
        </div>
    @endif

    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-envelope me-1"></i>
                {{ __('Resend Verification Email') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="btn btn-link text-decoration-none text-muted p-0">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
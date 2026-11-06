<section>
    <div class="mb-4">
        <h2 class="h5 fw-bold text-danger mb-2">
            {{ __('Delete Account') }}
        </h2>

        <p class="text-muted small mb-0">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </div>

    <button
        type="button"
        class="btn btn-outline-danger"
        data-kpgs-modal-open="#confirm-user-deletion"
    >
        <i class="bi bi-trash me-1"></i>
        {{ __('Delete Account') }}
    </button>
    <div class="modal fade" id="confirm-user-deletion" tabindex="-1" aria-labelledby="delete-account-title" aria-hidden="true" data-kpgs-modal-auto-show="{{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }}">
        <div class="modal-dialog modal-dialog-centered kpgs-modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <div class="modal-header">
                        <div class="d-flex align-items-start gap-3">
                            <div class="text-danger fs-4"><i class="bi bi-exclamation-triangle"></i></div>
                            <div><h2 id="delete-account-title" class="h5 fw-bold text-dark mb-2">{{ __('Are you sure you want to delete your account?') }}</h2><p class="text-muted small mb-0">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p></div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">{{ __('Password') }}</label>
                            <input id="password" name="password" type="password" class="form-control @if ($errors->userDeletion->has('password')) is-invalid @endif" placeholder="{{ __('Password') }}" autocomplete="current-password" required>
                            @if ($errors->userDeletion->has('password'))<div class="invalid-feedback">{{ $errors->userDeletion->first('password') }}</div>@endif
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-danger" data-confirm="Apakah Anda yakin ingin menghapus akun ini secara permanen?"><i class="bi bi-trash me-1"></i>{{ __('Delete Account') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
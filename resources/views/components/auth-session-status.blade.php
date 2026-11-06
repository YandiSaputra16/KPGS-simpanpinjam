@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'alert alert-success']) }} role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle"></i>
            <span>{{ $status }}</span>
        </div>
    </div>
@endif
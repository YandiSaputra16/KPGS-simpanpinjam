@props([
    'title',
    'value',
    'description' => null,
    'icon' => null,
])

<div class="card border-0 shadow-sm h-100">
    <div class="card-body p-4">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <div class="min-w-0">
                <p class="small fw-medium text-secondary mb-0">
                    {{ $title }}
                </p>

                <p class="h3 fw-bold text-dark mt-2 mb-0">
                    {{ $value }}
                </p>

                @if ($description)
                    <p class="small text-secondary mt-1 mb-0">
                        {{ $description }}
                    </p>
                @endif
            </div>

            @if ($icon)
                <div class="flex-shrink-0 text-secondary">
                    {!! $icon !!}
                </div>
            @endif
        </div>
    </div>
</div>
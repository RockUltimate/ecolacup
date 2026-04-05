@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'status-note border-emerald-200 bg-emerald-50 text-emerald-800']) }}>
        {{ $status }}
    </div>
@endif

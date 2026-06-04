@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'form-hint text-green']) }}>
        {{ $status }}
    </div>
@endif

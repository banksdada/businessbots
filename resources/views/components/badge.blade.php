@props(['variant' => 'accent'])

@php
$variants = [
    'accent'  => 'bg-accent-muted text-accent-light',
    'success' => 'bg-success-muted text-success',
    'warning' => 'bg-warning-muted text-warning',
    'error'   => 'bg-error-muted text-error',
    'neutral' => 'bg-surface-secondary text-text-secondary',
];
@endphp

<span {{ $attributes->merge(['class' => 'inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap ' . $variants[$variant]]) }}>
    {{ $slot }}
</span>

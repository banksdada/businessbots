@props(['variant' => 'accent'])

@php
$variants = [
    'accent'  => 'bg-accent-muted text-accent-light',
    'success' => 'bg-success-muted text-success',
    'error'   => 'bg-error/10 text-error',
];
@endphp

<span {{ $attributes->merge(['class' => 'inline-block px-2 py-0.5 rounded-full text-[11px] font-medium ' . $variants[$variant]]) }}>
    {{ $slot }}
</span>

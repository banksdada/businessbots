@props(['name', 'bg' => 'bg-peach'])

{{-- A hand-drawn person in a soft coloured circle. Decorative, so alt is empty. --}}
<div {{ $attributes->merge(['class' => "relative overflow-hidden rounded-full $bg"]) }}>
    <img src="{{ asset('images/people/' . $name . '.svg') }}" alt="" loading="lazy"
        class="absolute inset-x-0 bottom-0 w-full translate-y-[6%]">
</div>

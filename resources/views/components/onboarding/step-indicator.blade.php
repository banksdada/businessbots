@props(['steps', 'current'])

@php
$currentIndex = array_search($current, $steps, true);
@endphp

<div class="mb-6">
    <div class="flex gap-1.5 mb-2">
        @foreach ($steps as $i => $step)
            <div class="flex-1 h-1 rounded-full {{ $i <= $currentIndex ? 'bg-accent' : 'bg-border' }}"></div>
        @endforeach
    </div>
    <div class="text-[11px] text-text-muted font-medium">
        Step {{ $currentIndex + 1 }} of {{ count($steps) }}
    </div>
</div>

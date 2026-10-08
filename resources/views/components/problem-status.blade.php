@props(['problem'])

@php
    $variant = match ($problem->status) {
        \App\Models\ProblemRequest::STATUS_APPROVED => 'success',
        \App\Models\ProblemRequest::STATUS_FAILED => 'warning',
        default => 'accent',
    };
@endphp

<x-badge :variant="$variant">{{ $problem->clientStatusLabel() }}</x-badge>

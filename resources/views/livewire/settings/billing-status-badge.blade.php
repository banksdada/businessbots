@if ($state)
    <a href="{{ route($linkRoute) }}"
        class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium
            {{ $state === 'past_due' ? 'bg-error/10 text-error' : 'bg-warning/10 text-warning' }}">
        <span class="w-1.5 h-1.5 rounded-full {{ $state === 'past_due' ? 'bg-error' : 'bg-warning' }}"></span>
        {{ $message }}
    </a>
@endif

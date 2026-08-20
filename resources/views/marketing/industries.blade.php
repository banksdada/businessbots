<x-layouts.app title="Industries — BusinessBots">

    <section class="mx-auto max-w-[900px] px-6 pt-20 pb-16">
        <span class="inline-block px-3 py-1 bg-accent-muted text-accent-light rounded-full text-xs font-semibold tracking-wide uppercase">
            INDUSTRIES
        </span>
        <h1 class="mt-5 text-3xl md:text-4xl font-bold leading-tight">
            Works for any business, <span class="text-gradient-accent">out of the box</span>
        </h1>
        <p class="mt-4 text-text-secondary text-base leading-relaxed max-w-[600px]">
            Pick your vertical and BusinessBots trains itself on how your industry talks, sells and follows up.
        </p>

        <div class="mt-10 grid grid-cols-2 md:grid-cols-3 gap-3">
            @foreach ($verticals as $vertical)
                <div class="bg-surface border rounded-lg p-4
                    {{ $selected === $vertical->slug ? 'border-border-accent bg-surface-elevated' : 'border-border' }}">
                    <div class="text-sm font-semibold text-text-primary">{{ $vertical->label }}</div>
                    <div class="mt-1 text-xs text-text-muted">{{ ucwords(str_replace('_', ' ', $vertical->slug)) }}</div>
                </div>
            @endforeach
        </div>
    </section>

</x-layouts.app>
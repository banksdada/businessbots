<x-layouts.app :title="'Insights'">

    <section class="mx-auto max-w-[720px] px-6 py-20">
        <div class="text-center mb-10">
            <span class="text-label inline-block px-2.5 py-0.5 bg-accent-muted text-accent rounded-full">INSIGHTS</span>
            <h1 class="mt-3 text-h2 text-text-primary">Problem → fix plan → savings</h1>
            <p class="mt-2 text-sm text-text-secondary">Articles on how to find, fix, and automate the bottlenecks in your business.</p>
        </div>

        <div class="space-y-4">
            @foreach ($posts as $post)
                <a href="{{ route('insights.show', $post['slug']) }}"
                   class="group block bg-surface border border-border rounded-xl p-6 hover:border-border-accent transition-colors">
                    <div class="flex items-center justify-between">
                        <span class="text-label">{{ $post['category'] }}</span>
                        <span class="text-xs text-text-muted">{{ $post['date'] }}</span>
                    </div>
                    <h2 class="mt-2 text-lg font-semibold text-text-primary group-hover:text-accent-light transition-colors">{{ $post['title'] }}</h2>
                    <p class="mt-1 text-sm text-text-secondary leading-relaxed">{{ $post['excerpt'] }}</p>
                    <span class="mt-3 inline-block text-xs text-accent-light group-hover:text-accent">Read more →</span>
                </a>
            @endforeach
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('marketing.demo') }}"
               class="inline-block px-5 py-2.5 bg-gradient-accent text-white rounded-md text-sm font-semibold hover:opacity-90 transition-opacity">
                Book a demo
            </a>
        </div>
    </section>

</x-layouts.app>
<x-layouts.app :title="{{$post['title']}}">

    <section class="mx-auto max-w-[720px] px-6 py-20">
        <div class="flex items-center gap-2 mb-5">
            <span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
            <h2 class="text-h2 text-text-primary">{{ $post['title'] }}</h2>
            <span class="text-label">updated as the system runs</span>
        </div>

        <div class="bg-surface border border-border rounded-xl p-8 md:p-10">
            <p class="mt-2 text-sm text-text-secondary leading-relaxed">{{ $post['excerpt'] }}</p>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('marketing.demo') }}"
               class="inline-block px-5 py-2.5 bg-gradient-accent text-white rounded-md text-sm font-semibold hover:opacity-90 transition-opacity">
                Describe your problem → get a fix plan
            </a>
        </div>
    </section>

</x-layouts.app>
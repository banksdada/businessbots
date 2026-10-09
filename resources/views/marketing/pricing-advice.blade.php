<x-layouts.app :title="'Pricing — ' . config('app.name')">
    <section class="mx-auto max-w-[720px] px-4 sm:px-6 py-16 sm:py-24 text-center">
        <span class="inline-block px-3 py-1 bg-accent-muted text-accent-dark rounded-full text-sm font-semibold">Early access</span>
        <h1 class="mt-5 text-3xl sm:text-4xl font-bold">A fair price, agreed before we start</h1>
        <p class="mt-4 text-lg text-text-secondary leading-relaxed">
            Every problem is different, so we don't use a one-size price list. First we look at what the problem costs you today. Then we agree a price that makes sense next to what fixing it saves. You'll see exactly what's included, and the price, before anything starts.
        </p>
        <p class="mt-4 text-text-secondary leading-relaxed">
            We're working with our first clients now. Sign up to tell us your first problem, or send us a message to talk it through.
        </p>
        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('register') }}" class="btn-primary text-base px-6 py-3">Get started</a>
            <a href="{{ route('contact') }}" class="btn-secondary text-base px-6 py-3">Email us</a>
        </div>
    </section>
</x-layouts.app>

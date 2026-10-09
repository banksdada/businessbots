<x-layouts.app :title="'Pricing — ' . config('app.name')">
    <section class="mx-auto max-w-[720px] px-4 sm:px-6 py-16 sm:py-24 text-center">
        <span class="inline-block px-3 py-1 bg-accent-muted text-accent-dark rounded-full text-sm font-semibold">Early access</span>
        <h1 class="mt-5 text-3xl sm:text-4xl font-bold">Pricing that fits your organisation</h1>
        <p class="mt-4 text-lg text-text-secondary leading-relaxed">
            We're working with our first clients now and agree pricing with each one. We look at what the problem costs you today, so the price makes sense next to what fixing it saves. You'll always see what's included and the price before anything starts. Sign up to send your first request, or get in touch to talk it through.
        </p>
        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('register') }}" class="btn-primary text-base px-6 py-3">Get started</a>
            <a href="mailto:{{ config('legal.support_email') }}" class="btn-secondary text-base px-6 py-3">Email us</a>
        </div>
    </section>
</x-layouts.app>

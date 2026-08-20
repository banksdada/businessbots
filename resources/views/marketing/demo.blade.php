<x-layouts.app title="Book a demo — BusinessBots">

    <section class="mx-auto max-w-[720px] px-6 pt-20 pb-16 text-center">
        <span class="inline-block px-3 py-1 bg-accent-muted text-accent-light rounded-full text-xs font-semibold tracking-wide">
            BOOK A DEMO
        </span>
        <h1 class="mt-5 text-3xl md:text-4xl font-bold leading-tight">
            See BusinessBots <span class="text-gradient-accent">run your business</span>
        </h1>
        <p class="mt-4 text-text-secondary text-base leading-relaxed">
            Watch how WhatsApp replies, content and lead follow-up run on autopilot — in your own industry.
        </p>

        <div class="mt-10 mx-auto max-w-md text-left bg-surface border border-border rounded-xl p-6">
            <form action="{{ route('marketing.demo') }}" method="GET" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Your business name</label>
                    <input type="text" name="business" required
                        class="w-full px-3 py-2 bg-surface-secondary border border-border-strong rounded-md text-sm text-text-primary focus:border-border-accent outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-text-secondary mb-1">Email</label>
                    <input type="email" name="email" required
                        class="w-full px-3 py-2 bg-surface-secondary border border-border-strong rounded-md text-sm text-text-primary focus:border-border-accent outline-none">
                </div>
                <button type="submit"
                    class="w-full py-2.5 bg-gradient-accent text-white rounded-md text-sm font-semibold">
                    Request my demo
                </button>
            </form>
        </div>

        <p class="mt-8 text-xs text-text-muted">
            We'll reach out within one business day. No sales pressure.
        </p>
    </section>

</x-layouts.app>
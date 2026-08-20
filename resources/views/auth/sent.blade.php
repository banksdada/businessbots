<x-layouts.app title="Check your email — BusinessBots">

    <section class="mx-auto max-w-[400px] px-6 py-20 text-center">
        <div class="mb-8">
            <h1 class="text-2xl font-bold">Check your email</h1>
            <p class="mt-2 text-text-secondary text-sm">
                We've sent a one-time login link to
                <span class="text-text-primary font-medium">{{ $email }}</span>.
            </p>
        </div>

        <div class="bg-surface border border-border rounded-xl p-6 text-left space-y-3">
            <p class="text-sm text-text-secondary">
                Open the email and click <span class="text-text-primary font-medium">Sign in to BusinessBots</span>.
            </p>
            <p class="text-xs text-text-muted">
                The link expires in 15 minutes and works once. No password, ever.
            </p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="mt-6">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" class="w-full py-2.5 border border-border-strong text-text-primary rounded-md text-sm font-semibold hover:border-border-accent transition-colors">
                Resend login link
            </button>
        </form>
    </section>

</x-layouts.app>
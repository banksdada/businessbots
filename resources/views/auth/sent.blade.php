<x-layouts.app :title="'Check your email — ' . config('app.name')">

    <section class="mx-auto max-w-[440px] px-4 sm:px-6 py-12 sm:py-20 text-center">
        <div class="mb-8">
            <x-person name="elder-shirt" bg="bg-sage" class="w-24 h-24 mx-auto mb-5" />
            <h1 class="text-3xl font-bold">Check your email</h1>
            <p class="mt-2 text-text-secondary">
                We've sent a one-time login link to
                <span class="text-text-primary font-medium">{{ $email }}</span>.
            </p>
        </div>

        <div class="card p-6 text-left space-y-3">
            <p class="text-sm text-text-secondary">
                Open the email and click <span class="text-text-primary font-medium">Sign in to {{ config('app.name') }}</span>.
            </p>
            <p class="text-sm text-text-muted">
                The link expires in 15 minutes and works once. No password, ever.
            </p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="mt-6">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" class="btn-secondary w-full">
                Resend login link
            </button>
        </form>
    </section>

</x-layouts.app>
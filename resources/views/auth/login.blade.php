<x-layouts.app :title="'Sign in — ' . config('app.name')">

    <section class="mx-auto max-w-[440px] px-4 sm:px-6 py-12 sm:py-20">
        <div class="text-center mb-8">
            <x-person name="owner-explain" bg="bg-peach" class="w-24 h-24 mx-auto mb-5" />
            <h1 class="text-3xl font-bold">Welcome back</h1>
            <p class="mt-2 text-text-secondary">We'll email you a one-time sign-in link. No password needed.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="card p-6 sm:p-8 space-y-5">
            @csrf

            <div>
                <label for="email" class="field-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="field-input"
                    placeholder="you@example.com"
                >
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-primary w-full py-3">
                Email me a login link
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-text-secondary">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-semibold text-accent hover:text-accent-dark transition-colors">Create one</a>
        </p>
    </section>

</x-layouts.app>
<x-layouts.app title="Create account — BusinessBots">

    <section class="mx-auto max-w-[400px] px-6 py-20">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold">Create your account</h1>
            <p class="mt-2 text-text-secondary text-sm">Start your 14-day free trial — no password, no card required</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-text-secondary mb-1">Full name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    class="w-full px-3 py-2.5 bg-surface border border-border rounded-md text-text-primary text-sm placeholder-text-muted focus:outline-none focus:border-border-accent transition-colors"
                    placeholder="Your name"
                >
                @error('name')
                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-text-secondary mb-1">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full px-3 py-2.5 bg-surface border border-border rounded-md text-text-primary text-sm placeholder-text-muted focus:outline-none focus:border-border-accent transition-colors"
                    placeholder="you@example.com"
                >
                @error('email')
                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full py-2.5 bg-gradient-accent text-white rounded-md text-sm font-semibold hover:opacity-90 transition-opacity">
                Create account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-text-secondary">
            Already have an account?
            <a href="{{ route('login') }}" class="text-accent-light hover:text-accent transition-colors">Sign in</a>
        </p>
    </section>

</x-layouts.app>
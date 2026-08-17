<x-layouts.app title="Sign in — BusinessBots">

    <section class="mx-auto max-w-[400px] px-6 py-20">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold">Welcome back</h1>
            <p class="mt-2 text-text-secondary text-sm">Sign in to your BusinessBots account</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-text-secondary mb-1">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="w-full px-3 py-2.5 bg-surface border border-border rounded-md text-text-primary text-sm placeholder-text-muted focus:outline-none focus:border-border-accent transition-colors"
                    placeholder="you@example.com"
                >
                @error('email')
                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-text-secondary mb-1">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full px-3 py-2.5 bg-surface border border-border rounded-md text-text-primary text-sm placeholder-text-muted focus:outline-none focus:border-border-accent transition-colors"
                    placeholder="Your password"
                >
                @error('password')
                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-border bg-surface text-accent focus:ring-accent">
                    <span class="text-sm text-text-secondary">Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full py-2.5 bg-gradient-accent text-white rounded-md text-sm font-semibold hover:opacity-90 transition-opacity">
                Sign in
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-text-secondary">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-accent-light hover:text-accent transition-colors">Create one</a>
        </p>
    </section>

</x-layouts.app>

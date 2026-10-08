<x-layouts.app :title="'Create account — ' . config('app.name')">

    <section class="mx-auto max-w-[440px] px-4 sm:px-6 py-12 sm:py-20">
        <div class="text-center mb-8">
            <div class="flex justify-center -space-x-4 mb-5">
                <x-person name="pastor-coffee" bg="bg-sage" class="w-20 h-20 ring-4 ring-background" />
                <x-person name="manager-hijab" bg="bg-peach" class="w-20 h-20 ring-4 ring-background" />
                <x-person name="volunteer-hoodie" bg="bg-sun" class="w-20 h-20 ring-4 ring-background" />
            </div>
            <h1 class="text-3xl font-bold">Create your account</h1>
            <p class="mt-2 text-text-secondary">We'll email you a sign-in link. No password needed.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="card p-6 sm:p-8 space-y-5">
            @csrf

            <div>
                <label for="name" class="field-label">Full name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    class="field-input"
                    placeholder="Your name"
                >
                @error('name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="field-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="field-input"
                    placeholder="you@example.com"
                >
                @error('email')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-primary w-full py-3">
                Create account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-text-secondary">
            Already have an account?
            <a href="{{ route('login') }}" class="font-semibold text-accent hover:text-accent-dark transition-colors">Sign in</a>
        </p>
    </section>

</x-layouts.app>
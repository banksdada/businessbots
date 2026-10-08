@php
    $social = config('features.social');
    // Mid-onboarding users have no active business yet; app links would only bounce them back.
    $inApp = auth()->check() && auth()->user()->activeBusiness();
    $navLink = fn (bool $active) => $active
        ? 'text-text-primary font-semibold'
        : 'text-text-secondary hover:text-text-primary';
@endphp

<nav class="bg-surface border-b border-border" x-data="{ mobileOpen: false }">
    <div class="mx-auto max-w-[1100px] px-4 sm:px-6 h-16 flex items-center justify-between">
        <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-md bg-gradient-accent flex items-center justify-center text-white font-bold text-sm">B</div>
            <span class="font-semibold text-base text-text-primary">{{ config('app.name') }}</span>
        </a>

        <div class="hidden md:flex items-center gap-6 text-sm">
            @if ($inApp)
                <a href="{{ route('dashboard') }}" class="{{ $navLink(request()->routeIs('dashboard', 'problems.show')) }}">My requests</a>
                <a href="{{ route('problems.create') }}" class="{{ $navLink(request()->routeIs('problems.create')) }}">Ask for advice</a>
                @if ($social)
                    <a href="{{ route('leads.index') }}" class="{{ $navLink(request()->routeIs('leads.index')) }}">Leads</a>
                @endif
                <a href="{{ route('settings') }}" class="{{ $navLink(request()->routeIs('settings')) }}">Settings</a>
                @if (auth()->user()->is_admin)
                    <a href="{{ url('/ops') }}" class="text-accent font-semibold hover:text-accent-dark">Review drafts</a>
                @endif
            @elseif (! auth()->check())
                <a href="{{ route('home') }}#how-it-works" class="{{ $navLink(false) }}">How it works</a>
                <a href="{{ route('marketing.pricing') }}" class="{{ $navLink(request()->routeIs('marketing.pricing')) }}">Pricing</a>
                @if ($social)
                    <a href="{{ route('live-proof') }}" class="{{ $navLink(request()->routeIs('live-proof')) }}">Live proof</a>
                @endif
            @endif
        </div>

        <div class="hidden md:flex items-center gap-3">
            @auth
                @if (config('billing.required'))
                    @livewire('settings.billing-status-badge')
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-text-secondary hover:text-text-primary">Sign out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-sm font-medium text-text-secondary hover:text-text-primary">Sign in</a>
                <a href="{{ route('register') }}" class="btn-primary">Get started</a>
            @endauth
        </div>

        <button class="md:hidden p-2 -mr-2 text-text-secondary" @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen" aria-label="Open menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-border px-4 py-4 space-y-1 text-base">
        @auth
            @if ($inApp)
            <a href="{{ route('dashboard') }}" class="block py-2 text-text-primary">My requests</a>
            <a href="{{ route('problems.create') }}" class="block py-2 text-text-primary">Ask for advice</a>
            @if ($social)
                <a href="{{ route('leads.index') }}" class="block py-2 text-text-primary">Leads</a>
            @endif
            <a href="{{ route('settings') }}" class="block py-2 text-text-primary">Settings</a>
            @if (auth()->user()->is_admin)
                <a href="{{ url('/ops') }}" class="block py-2 text-accent font-semibold">Review drafts</a>
            @endif
            @endif
            <form method="POST" action="{{ route('logout') }}" class="pt-2">
                @csrf
                <button type="submit" class="py-2 text-text-secondary">Sign out</button>
            </form>
        @else
            <a href="{{ route('home') }}#how-it-works" class="block py-2 text-text-primary">How it works</a>
            <a href="{{ route('marketing.pricing') }}" class="block py-2 text-text-primary">Pricing</a>
            <a href="{{ route('login') }}" class="block py-2 text-text-primary">Sign in</a>
            <a href="{{ route('register') }}" class="block py-2 text-accent font-semibold">Get started</a>
        @endauth
    </div>
</nav>

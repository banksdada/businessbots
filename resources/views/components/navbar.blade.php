<nav class="border-b border-border" x-data="{ mobileOpen: false }">
    <div class="mx-auto max-w-[1200px] px-6 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-md bg-gradient-accent flex items-center justify-center text-white font-semibold text-sm">B</div>
            <span class="font-semibold text-sm text-text-primary">BusinessBots</span>
        </a>

        <div class="hidden md:flex items-center gap-6 text-sm text-text-secondary">
            <a href="{{ route('marketing.industries') }}" class="hover:text-text-primary transition-colors">Industries</a>
            <a href="{{ route('marketing.pricing') }}" class="hover:text-text-primary transition-colors">Pricing</a>
            <a href="{{ route('live-proof') }}" class="text-accent-light hover:text-accent transition-colors">Live proof</a>
        </div>

        <div class="hidden md:flex items-center gap-3">
            @auth
                @livewire('settings.billing-status-badge')
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gradient-accent text-white rounded-md text-sm font-medium">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm text-text-secondary hover:text-text-primary">Sign in</a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-gradient-accent text-white rounded-md text-sm font-medium">
                    Start free trial →
                </a>
            @endauth
        </div>

        <button class="md:hidden text-text-secondary" @click="mobileOpen = !mobileOpen" aria-label="Toggle menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-border px-6 py-4 space-y-3 text-sm">
        <a href="{{ route('marketing.industries') }}" class="block text-text-secondary">Industries</a>
        <a href="{{ route('marketing.pricing') }}" class="block text-text-secondary">Pricing</a>
        <a href="{{ route('live-proof') }}" class="block text-accent-light">Live proof</a>
        @auth
            @livewire('settings.billing-status-badge')
            <a href="{{ route('dashboard') }}" class="block text-accent font-medium">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="block text-text-secondary">Sign in</a>
            <a href="{{ route('register') }}" class="block text-accent font-medium">Start free trial →</a>
        @endauth
    </div>
</nav>

<!DOCTYPE html>
<html lang="en" class="bg-background">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen flex flex-col bg-background text-text-primary font-sans antialiased">

    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:px-4 focus:py-2 focus:bg-surface focus:rounded-md">Skip to content</a>

    <x-navbar />

    @if (session('status') || session('notice'))
        <div class="mx-auto w-full max-w-[1100px] px-4 sm:px-6 pt-6">
            <div role="status" class="flex gap-3 items-start rounded-lg border border-success/30 bg-success-muted px-4 py-3 text-sm text-text-primary">
                <svg class="w-5 h-5 text-success shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('status') ?? session('notice') }}</span>
            </div>
        </div>
    @endif

    <main id="main" class="flex-1">
        {{ $slot }}
    </main>

    <x-footer />

    @livewireScripts
    @stack('scripts')
</body>
</html>

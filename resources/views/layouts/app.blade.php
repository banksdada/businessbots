<!DOCTYPE html>
<html lang="en" class="bg-background">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'BusinessBots — AI business OS for any industry' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-background text-text-primary font-sans antialiased">

    <x-navbar />

    <main>
        {{ $slot }}
    </main>

    <x-footer />

    @livewireScripts
    @stack('scripts')
</body>
</html>

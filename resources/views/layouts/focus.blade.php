<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f766e">
    <title>@yield('title', 'Exam') · SkillCheck</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo_skillcheck_square.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas text-ink antialiased">
    {{-- Minimal focus topbar: logo + optional right slot, no sidebar (distraction-free exam mode) --}}
    <header class="sticky top-0 z-20 border-b border-line bg-white/85 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-5xl items-center gap-3 px-4 sm:px-6">
            <x-brand-logo variant="wide" :height="24" />
            <div class="ml-auto">
                @yield('topbar')
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-6 sm:px-6">
        <x-ui.flash />
        @yield('content')
    </main>
</body>
</html>

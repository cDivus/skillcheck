<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f766e">
    <title>@yield('title', 'Authentication') · SkillCheck</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo_skillcheck_square.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen bg-gradient-to-br from-brand-900 via-brand-800 to-brand-950 text-white antialiased overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute -top-40 -right-40 h-96 w-96 rounded-full bg-brand-400/20 blur-3xl mix-blend-screen"></div>
    <div class="absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-brand-600/30 blur-3xl mix-blend-screen"></div>
    
    <div class="relative z-10 flex min-h-screen flex-col items-center justify-center p-4">
        <x-ui.flash />
        @yield('content')
    </div>
</body>
</html>

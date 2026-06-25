<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f766e">
    <title>@yield('title', 'Welcome') · SkillCheck</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo_skillcheck_square.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas text-ink antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <a href="{{ url('/') }}" class="mb-8 inline-flex">
            <x-brand-logo variant="wide" :height="34" />
        </a>

        <div class="w-full max-w-md">
            <div class="sc-card p-7 sm:p-8 shadow-sm">
                @yield('content')
            </div>
            @hasSection('below')
                <div class="mt-5 text-center text-sm text-muted">@yield('below')</div>
            @endif
        </div>

        <p class="mt-8 text-xs text-faint">&copy; {{ date('Y') }} SkillCheck · Online Examination System</p>
    </div>
</body>
</html>

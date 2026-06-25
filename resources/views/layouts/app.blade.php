<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f766e">
    <title>@yield('title', 'SkillCheck') · SkillCheck</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo_skillcheck_square.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_skillcheck_square.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-canvas text-ink antialiased">
    <x-app.shell :title="trim($__env->yieldContent('title', 'SkillCheck'))">
        @yield('content')
    </x-app.shell>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f766e">
    <title>SkillCheck · Online Examination System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_skillcheck_square.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-linear-to-br from-brand-50 via-canvas to-canvas text-ink antialiased relative">
    {{-- Decorative background glows --}}
    <div class="absolute inset-0 z-[-1] overflow-hidden pointer-events-none">
        <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-brand-200/40 blur-[120px]"></div>
        <div class="absolute top-[10%] -right-[10%] w-[40%] h-[40%] rounded-full bg-brand-300/20 blur-[100px]"></div>
    </div>

    {{-- Nav --}}
    <header class="sticky top-0 z-30 border-b border-line bg-canvas/80 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
            <a href="{{ url('/') }}" class="flex items-center"><x-brand-logo variant="wide" :height="28" /></a>
            <nav class="flex items-center gap-2">
                @if (Route::has('login'))
                    <x-ui.button href="{{ route('login') }}" variant="ghost" size="sm">Log in</x-ui.button>
                @endif
                @if (Route::has('register'))
                    <x-ui.button href="{{ route('register') }}" variant="primary" size="sm">Get started</x-ui.button>
                @endif
            </nav>
        </div>
    </header>

    {{-- Hero --}}
    <section class="mx-auto max-w-4xl px-4 pt-20 pb-16 text-center sm:px-6 sm:pt-28">
        <span class="inline-flex items-center gap-1.5 rounded-full border border-line bg-white px-3 py-1 text-xs font-medium text-muted shadow-xs">
            <x-icon name="sparkles" class="h-3.5 w-3.5 text-brand-500" /> Modern online assessment
        </span>
        <h1 class="mt-6 text-4xl font-semibold tracking-tight text-ink sm:text-6xl">
            Run exams that<br class="hidden sm:block"> just <span class="text-transparent bg-clip-text bg-linear-to-r from-brand-700 to-brand-400 drop-shadow-sm">work</span>.
        </h1>
        <p class="mx-auto mt-5 max-w-xl text-base leading-relaxed text-muted sm:text-lg">
            SkillCheck is a calm, focused platform for building question banks, running timed exams,
            auto-grading results, and reviewing submissions — all in one place.
        </p>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            @if (Route::has('register'))
                <x-ui.button href="{{ route('register') }}" variant="primary" size="lg">Get started free <x-icon name="arrow-right" /></x-ui.button>
            @endif
            @if (Route::has('login'))
                <x-ui.button href="{{ route('login') }}" variant="secondary" size="lg">Sign in</x-ui.button>
            @endif
        </div>

        {{-- Product peek --}}
        <div class="mx-auto mt-16 max-w-3xl">
            <div class="rounded-2xl border border-white/40 bg-white/30 backdrop-blur-md p-2 shadow-xl hover-lift">
                <div class="rounded-xl border border-white/50 bg-white/80 p-6 text-left sm:p-8 shadow-sm">
                    <div class="flex items-center gap-2 text-xs text-faint">
                        <span class="h-2.5 w-2.5 rounded-full bg-red-300"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-amber-300"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-green-300"></span>
                    </div>
                    <div class="mt-5 grid grid-cols-3 gap-4">
                        <div class="rounded-xl border border-line bg-white p-4">
                            <p class="text-xs text-muted">Active Exams</p>
                            <p class="mt-1 text-2xl font-semibold text-ink">12</p>
                        </div>
                        <div class="rounded-xl border border-line bg-white p-4">
                            <p class="text-xs text-muted">Submissions</p>
                            <p class="mt-1 text-2xl font-semibold text-ink">348</p>
                        </div>
                        <div class="rounded-xl border border-line bg-white p-4">
                            <p class="text-xs text-muted">Avg. Score</p>
                            <p class="mt-1 text-2xl font-semibold text-brand-600">86%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-2xl font-semibold tracking-tight text-ink sm:text-3xl">Built for every role</h2>
            <p class="mt-3 text-muted">One clean platform for students, instructors, and administrators.</p>
        </div>
        <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
            @php
                $features = [
                    ['icon' => 'graduation-cap', 'title' => 'Students', 'text' => 'Take timed exams in a distraction-free interface and review graded attempts with detailed feedback.'],
                    ['icon' => 'file-text', 'title' => 'Instructors', 'text' => 'Create exams, manage question banks, import & export, and grade submissions with ease.'],
                    ['icon' => 'shield-check', 'title' => 'Administrators', 'text' => 'Monitor system metrics, moderate exams, and manage user accounts across the platform.'],
                ];
            @endphp
            @foreach($features as $f)
                <div class="sc-card p-6">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <x-icon :name="$f['icon']" class="h-5 w-5" />
                    </span>
                    <h3 class="mt-4 text-base font-semibold text-ink">{{ $f['title'] }}</h3>
                    <p class="mt-1.5 text-sm leading-relaxed text-muted">{{ $f['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="mx-auto max-w-6xl px-4 pb-20 sm:px-6">
        <div class="overflow-hidden rounded-2xl border border-brand-300 bg-linear-to-br from-brand-700 via-brand-800 to-ink px-6 py-12 text-center sm:px-12 shadow-pop">
            <h2 class="text-2xl font-semibold tracking-tight text-white sm:text-3xl">Ready to run your first exam?</h2>
            <p class="mx-auto mt-3 max-w-md text-sm text-brand-50">Set up your question bank and invite students in minutes.</p>
            <div class="mt-7 flex justify-center">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-brand-700 shadow-xs transition hover:bg-brand-50">
                        Get started <x-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-line">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-4 py-8 sm:flex-row sm:px-6">
            <x-brand-logo variant="wide" :height="22" />
            <p class="text-xs text-faint">&copy; {{ date('Y') }} SkillCheck · Online Examination System</p>
        </div>
    </footer>
</body>
</html>

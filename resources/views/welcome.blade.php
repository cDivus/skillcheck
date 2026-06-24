<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'SkillCheck') }}</title>

        @fonts

        <script>
            if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.add('light');
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            }
        </script>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen bg-brand-light dark:bg-brand-dark text-brand-dark dark:text-brand-light antialiased">
        <header class="bg-brand-light dark:bg-brand-dark border-b border-brand-light/10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="md:flex md:items-center md:gap-12">
                        <a class="block text-brand-primary dark:text-brand-accent" href="{{ url('/') }}">
                            <span class="sr-only">Home</span>
                            <img class="h-8 w-auto" src="{{ asset('images/Icon.svg') }}" alt="SkillCheck Logo" />
                        </a>
                    </div>

                    <div class="hidden md:block">
                        <nav aria-label="Global">
                            <ul class="flex items-center gap-6 text-sm">
                                <li>
                                    <a class="text-gray-500 transition hover:text-brand-primary dark:text-brand-light/75 dark:hover:text-brand-accent" href="#">
                                        About
                                    </a>
                                </li>

                                <li>
                                    <a class="text-gray-500 transition hover:text-brand-primary dark:text-brand-light/75 dark:hover:text-brand-accent" href="#services">
                                        Services
                                    </a>
                                </li>

                                <li>
                                    <a class="text-gray-500 transition hover:text-brand-primary dark:text-brand-light/75 dark:hover:text-brand-accent" href="#contact">
                                        Contact Us
                                    </a>
                                </li>

                            </ul>
                        </nav>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- Theme Toggle Button -->
                        <button id="theme-toggle" class="rounded-md bg-brand-light p-2.5 text-brand-primary transition hover:bg-brand-primary/10 dark:bg-brand-secondary dark:text-brand-light dark:hover:bg-brand-primary/20 shadow-sm" aria-label="Toggle Theme">
                            <!-- Moon Icon (shows when theme is light, to switch to dark) -->
                            <svg id="theme-toggle-moon" class="size-5 block dark:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 6V3M18.5 12V7M14.5 4.5H11.5M21 9.5H16M15.5548 16.8151C16.7829 16.8151 17.9493 16.5506 19 16.0754C17.6867 18.9794 14.7642 21 11.3698 21C6.74731 21 3 17.2527 3 12.6302C3 9.23576 5.02061 6.31331 7.92462 5C7.44944 6.05072 7.18492 7.21708 7.18492 8.44523C7.18492 13.0678 10.9322 16.8151 15.5548 16.8151Z"></path>
                            </svg>
                            <!-- Sun/Dark Icon (shows when theme is dark, to switch to light) -->
                            <svg id="theme-toggle-sun" class="size-5 hidden dark:block" fill="currentColor" viewBox="0 5 18 21" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.75 8.25v0.219c0 0.844-0.375 1.25-1.156 1.25s-1.125-0.406-1.125-1.25v-0.219c0-0.813 0.344-1.219 1.125-1.219s1.156 0.406 1.156 1.219zM12.063 9.25l0.156-0.188c0.469-0.688 1.031-0.781 1.625-0.344 0.625 0.438 0.719 1.031 0.25 1.719l-0.188 0.156c-0.469 0.688-1.031 0.781-1.625 0.313-0.625-0.438-0.688-0.969-0.219-1.656zM5 9.063l0.125 0.188c0.469 0.688 0.406 1.219-0.188 1.656-0.625 0.469-1.219 0.375-1.688-0.313l-0.125-0.156c-0.469-0.688-0.406-1.281 0.188-1.719 0.625-0.438 1.219-0.281 1.688 0.344zM8.594 11.125c2.656 0 4.844 2.188 4.844 4.875 0 2.656-2.188 4.813-4.844 4.813-2.688 0-4.844-2.156-4.844-4.813 0-2.688 2.156-4.875 4.844-4.875zM1.594 12.5l0.219 0.063c0.813 0.25 1.063 0.719 0.844 1.469-0.25 0.75-0.75 0.969-1.531 0.719l-0.219-0.063c-0.781-0.25-1.063-0.719-0.844-1.469 0.25-0.75 0.75-0.969 1.531-0.719zM15.375 12.563l0.219-0.063c0.813-0.25 1.313-0.031 1.531 0.719s-0.031 1.219-0.844 1.469l-0.188 0.063c-0.813 0.25-1.313 0.031-1.531-0.719-0.25-0.75 0.031-1.219 0.813-1.469zM8.594 18.688c1.469 0 2.688-1.219 2.688-2.688 0-1.5-1.219-2.719-2.688-2.719-1.5 0-2.719 1.219-2.719 2.719 0 1.469 1.219 2.688 2.719 2.688zM0.906 17.281l0.219-0.063c0.781-0.25 1.281-0.063 1.531 0.688 0.219 0.75-0.031 1.219-0.844 1.469l-0.219 0.063c-0.781 0.25-1.281 0.063-1.531-0.688-0.219-0.75 0.063-1.219 0.844-1.469zM16.094 17.219l0.188 0.063c0.813 0.25 1.063 0.719 0.844 1.469s-0.719 0.938-1.531 0.688l-0.219-0.063c-0.781-0.25-1.063-0.719-0.813-1.469 0.219-0.75 0.719-0.938 1.531-0.688zM3.125 21.563l0.125-0.188c0.469-0.688 1.063-0.75 1.688-0.313 0.594 0.438 0.656 0.969 0.188 1.656l-0.125 0.188c-0.469 0.688-1.063 0.75-1.688 0.313-0.594-0.438-0.656-0.969-0.188-1.656zM13.906 21.375l0.188 0.188c0.469 0.688 0.375 1.219-0.25 1.656-0.594 0.438-1.156 0.375-1.625-0.313l-0.156-0.188c-0.469-0.688-0.406-1.219 0.219-1.656 0.594-0.438 1.156-0.375 1.625 0.313zM9.75 23.469v0.25c0 0.844-0.375 1.25-1.156 1.25s-1.125-0.406-1.125-1.25v-0.25c0-0.844 0.344-1.25 1.125-1.25s1.156 0.406 1.156 1.25z"></path>
                            </svg>
                        </button>

                        <div class="sm:flex sm:gap-4">
                            <a class="rounded-md bg-brand-primary px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-brand-secondary transition dark:bg-brand-accent dark:text-brand-secondary dark:hover:bg-brand-light" href="{{ route('login') }}">
                                Login
                            </a>

                            @if (Route::has('register'))
                                <div class="hidden sm:flex">
                                    <a class="rounded-md bg-brand-light px-5 py-2.5 text-sm font-medium text-brand-primary transition hover:bg-brand-accent/20 dark:bg-brand-secondary dark:text-brand-light dark:hover:bg-brand-primary" href="{{ route('register') }}">
                                        Register
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="block md:hidden">
                            <button id="menu-btn" class="rounded-sm bg-brand-light p-2 text-brand-primary transition hover:text-brand-secondary dark:bg-brand-secondary dark:text-brand-light dark:hover:text-brand-accent">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden border-t border-brand-light/10 bg-brand-light dark:bg-brand-dark px-4 py-4 space-y-3">
                <nav class="flex flex-col space-y-2">
                    <a class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-brand-light hover:text-brand-primary dark:text-brand-light/75 dark:hover:bg-brand-secondary dark:hover:text-brand-accent transition" href="#">
                        About
                    </a>
                    <a class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-brand-light hover:text-brand-primary dark:text-brand-light/75 dark:hover:bg-brand-secondary dark:hover:text-brand-accent transition" href="#services">
                        Services
                    </a>
                    <a class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-brand-light hover:text-brand-primary dark:text-brand-light/75 dark:hover:bg-brand-secondary dark:hover:text-brand-accent transition" href="#contact">
                        Contact Us
                    </a>
                </nav>
                <div class="pt-4 border-t border-brand-light/10 flex flex-col gap-2">
                    <a class="w-full text-center rounded-md bg-brand-primary px-5 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-brand-secondary transition dark:bg-brand-accent dark:text-brand-secondary dark:hover:bg-brand-light" href="{{ route('login') }}">
                        Login
                    </a>
                    @if (Route::has('register'))
                        <a class="w-full text-center rounded-md bg-brand-light px-5 py-2.5 text-sm font-medium text-brand-primary transition hover:bg-brand-accent/20 dark:bg-brand-secondary dark:text-brand-light dark:hover:bg-brand-primary" href="{{ route('register') }}">
                            Register
                        </a>
                    @endif
                </div>
            </div>
        </header>

        <section class="py-12">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-4 md:items-center">
                    <div class="md:col-span-1">
                        <div class="max-w-prose md:max-w-none">
                            <h2 class="text-4xl font-bold tracking-tight text-brand-dark dark:text-brand-light sm:text-5xl">
                                Your One Place for All Exams
                            </h2>

                            <p class="mt-4 text-pretty text-gray-600 dark:text-brand-light/80">
                                SkillCheck is a modern online examination platform for instructors, students, and administrators. 
                                Create exams, manage questions, track attempts, and review results in one secure workflow.
                            </p>
                        </div>
                    </div>

                    <div class="md:col-span-3">
                        <img src="{{ asset('images/Logo.svg') }}" class="rounded w-full h-auto" alt="Logo" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="py-16 bg-brand-light/30 dark:bg-brand-dark/50 border-t border-brand-light/10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-3xl font-bold tracking-tight text-brand-dark dark:text-brand-light sm:text-4xl">
                        Designed for Seamless Learning & Assessment
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-brand-light/80">
                        SkillCheck connects instructors and students with a unified suite of modern examination utilities that simplify test management and boost engagement.
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <!-- Instructor Services Card -->
                    <div class="p-8 rounded-2xl border border-brand-primary/10 bg-white dark:bg-brand-dark shadow-sm transition hover:shadow-md hover:border-brand-primary/30 flex flex-col justify-between">
                        <div>
                            <div class="inline-flex p-3 rounded-xl bg-brand-primary/10 text-brand-primary dark:bg-brand-accent/10 dark:text-brand-accent mb-6">
                                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-semibold text-brand-dark dark:text-brand-light mb-4">For Instructors</h3>
                            <ul class="space-y-6">
                                <li class="flex items-start gap-4">
                                    <span class="inline-flex size-6 shrink-0 items-center justify-center rounded-full bg-brand-primary/10 text-brand-primary dark:bg-brand-accent/20 dark:text-brand-accent text-sm font-semibold">1</span>
                                    <div>
                                        <h4 class="font-medium text-brand-dark dark:text-brand-light">Convenient Online Examination System</h4>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-brand-light/70 leading-relaxed">
                                            Create, schedule, and customize online assessments with ease. Build diverse question banks, incorporate time limits, and configure flexible grading metrics in a few simple clicks.
                                        </p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-4">
                                    <span class="inline-flex size-6 shrink-0 items-center justify-center rounded-full bg-brand-primary/10 text-brand-primary dark:bg-brand-accent/20 dark:text-brand-accent text-sm font-semibold">2</span>
                                    <div>
                                        <h4 class="font-medium text-brand-dark dark:text-brand-light">All-in-One Exam Management</h4>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-brand-light/70 leading-relaxed">
                                            Maintain total control of your curriculum. Import questions dynamically, manage multiple class schedules, moderate active test sessions, and grade essay submissions from a unified console.
                                        </p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Student Services Card -->
                    <div class="p-8 rounded-2xl border border-brand-primary/10 bg-white dark:bg-brand-dark shadow-sm transition hover:shadow-md hover:border-brand-primary/30 flex flex-col justify-between">
                        <div>
                            <div class="inline-flex p-3 rounded-xl bg-brand-accent/10 text-brand-primary dark:bg-brand-accent/10 dark:text-brand-accent mb-6">
                                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-semibold text-brand-dark dark:text-brand-light mb-4">For Students</h3>
                            <ul class="space-y-6">
                                <li class="flex items-start gap-4">
                                    <span class="inline-flex size-6 shrink-0 items-center justify-center rounded-full bg-brand-accent/20 text-brand-primary dark:bg-brand-accent/20 dark:text-brand-accent text-sm font-semibold">1</span>
                                    <div>
                                        <h4 class="font-medium text-brand-dark dark:text-brand-light">Exam History & Skill Assessment</h4>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-brand-light/70 leading-relaxed">
                                            Track your academic journey. Revisit completed exams, analyze scores, and review detailed breakdown feedback on graded questions to refine your study patterns and measure your skill growth.
                                        </p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-4">
                                    <span class="inline-flex size-6 shrink-0 items-center justify-center rounded-full bg-brand-accent/20 text-brand-primary dark:bg-brand-accent/20 dark:text-brand-accent text-sm font-semibold">2</span>
                                    <div>
                                        <h4 class="font-medium text-brand-dark dark:text-brand-light">Distraction-Free Test Interface</h4>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-brand-light/70 leading-relaxed">
                                            Experience a modern, highly responsive examination layout. Seamlessly view, save, and navigate answers with a clean user interface optimized for clear readability and simple access.
                                        </p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Us Section -->
        <section id="contact" class="py-16 bg-brand-light dark:bg-brand-dark border-t border-brand-light/10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                    <div>
                        <h2 class="text-center text-3xl font-bold tracking-tight text-brand-dark dark:text-brand-light sm:text-4xl">
                            Get in Touch
                        </h2>
                        <p class="text-center mt-4 text-lg text-gray-600 dark:text-brand-light/80 leading-relaxed">
                            Have questions about our examination platform or need assistance setting up your portal? <br> Our support team is here to help you get started with SkillCheck.
                        </p>
                        
                        <div class="mt-8 flex flex-col sm:flex-row justify-center items-center gap-8 sm:gap-16">
                            <div class="flex items-center gap-4">
                                <div class="inline-flex p-3 rounded-xl bg-brand-primary/10 text-brand-primary dark:bg-brand-accent/10 dark:text-brand-accent">
                                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 7.00005L10.2 11.65C11.2667 12.45 12.7333 12.45 13.8 11.65L20 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-brand-light/60">Email Address</p>
                                    <a href="mailto:skillcheck@email.com" class="text-brand-primary hover:text-brand-secondary dark:text-brand-accent dark:hover:text-brand-light font-medium transition">
                                        skillcheck@email.com
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="inline-flex p-3 rounded-xl bg-brand-primary/10 text-brand-primary dark:bg-brand-accent/10 dark:text-brand-accent">
                                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.05 6C15.0268 6.19057 15.9244 6.66826 16.6281 7.37194C17.3318 8.07561 17.8095 8.97326 18 9.95M14.05 2C16.0793 2.22544 17.9716 3.13417 19.4163 4.57701C20.8609 6.01984 21.7721 7.91101 22 9.94M18.5 21C9.93959 21 3 14.0604 3 5.5C3 5.11378 3.01413 4.73086 3.04189 4.35173C3.07375 3.91662 3.08968 3.69907 3.2037 3.50103C3.29814 3.33701 3.4655 3.18146 3.63598 3.09925C3.84181 3 4.08188 3 4.56201 3H7.37932C7.78308 3 7.98496 3 8.15802 3.06645C8.31089 3.12515 8.44701 3.22049 8.55442 3.3441C8.67601 3.48403 8.745 3.67376 8.88299 4.05321L10.0491 7.26005C10.2096 7.70153 10.2899 7.92227 10.2763 8.1317C10.2643 8.31637 10.2012 8.49408 10.0942 8.64506C9.97286 8.81628 9.77145 8.93713 9.36863 9.17882L8 10C9.2019 12.6489 11.3501 14.7999 14 16L14.8212 14.6314C15.0629 14.2285 15.1837 14.0271 15.3549 13.9058C15.5059 13.7988 15.6836 13.7357 15.8683 13.7237C16.0777 13.7237 16.2985 13.7904 16.74 13.9509L19.9468 15.117C20.3262 15.255 20.516 15.324 20.6559 15.4456C20.7795 15.553 20.8749 15.6891 20.9335 15.842C21 16.015 21 16.2169 21 16.6207V19.438C21 19.9181 21 20.1582 20.9007 20.364C20.8185 20.5345 20.663 20.7019 20.499 20.7963C20.3009 20.9103 20.0834 20.9262 19.6483 20.9581C19.2691 20.9859 18.8862 21 18.5 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-brand-light/60">Phone Number</p>
                                    <a href="tel:09098198988" class="text-brand-primary hover:text-brand-secondary dark:text-brand-accent dark:hover:text-brand-light font-medium transition">
                                        09098198988
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

            </div>
        </section>

        <!-- Theme and Mobile Menu Toggle Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Mobile Menu Toggle
                const menuBtn = document.getElementById('menu-btn');
                const mobileMenu = document.getElementById('mobile-menu');

                if (menuBtn && mobileMenu) {
                    menuBtn.addEventListener('click', function() {
                        mobileMenu.classList.toggle('hidden');
                    });

                    // Close menu when clicking a link
                    const menuLinks = mobileMenu.querySelectorAll('a');
                    menuLinks.forEach(link => {
                        link.addEventListener('click', function() {
                            mobileMenu.classList.add('hidden');
                        });
                    });
                }

                // Theme Toggle
                const themeToggle = document.getElementById('theme-toggle');
                if (themeToggle) {
                    themeToggle.addEventListener('click', function() {
                        if (document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark');
                            document.documentElement.classList.add('light');
                            localStorage.setItem('theme', 'light');
                        } else {
                            document.documentElement.classList.remove('light');
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('theme', 'dark');
                        }
                    });
                }
            });
        </script>
    </body>
</html>

@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md animate-fade-in-up">
  <div class="text-center mb-8">
    <a href="{{ url('/') }}" class="inline-block hover-lift">
      <img src="{{ asset('images/Logo.svg') }}" class="mx-auto h-16 w-auto" style="filter: brightness(0) invert(1);" alt="SkillCheck Logo" />
    </a>
    <h1 class="mt-6 text-3xl font-bold tracking-tight text-white">
      Welcome Back
    </h1>
    <p class="mt-2 text-sm text-brand-100/80">
      Sign in to continue your journey
    </p>
  </div>

  <div class="sc-card glass-panel p-8 shadow-2xl">
    @if (session('status'))
        <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 p-4 text-sm text-emerald-700" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-600">
            <ul class="mb-0 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="space-y-5">
      @csrf

      <div>
        <label for="login" class="block text-sm font-medium text-black mb-1.5">Email or Username</label>
        <div class="relative">
          <input
            type="text"
            name="login"
            id="login"
            class="w-full rounded-xl border border-black/10 bg-transparent p-3.5 text-sm text-black shadow-inner placeholder-black/50 focus:border-brand-500 focus:bg-black/5 focus:ring-1 focus:ring-brand-500 transition-all duration-200"
            placeholder="Enter your email or username"
            value="{{ old('login') }}"
            required
            autofocus
          />
        </div>
      </div>

      <div>
        <div class="flex justify-between items-center mb-1.5">
          <label for="password" class="block text-sm font-medium text-black">Password</label>
          <a href="{{ route('password.request') }}" class="text-xs font-medium text-brand-600 hover:text-brand-900 transition-colors">
            Forgot Password?
          </a>
        </div>
        <div class="relative">
          <input
            type="password"
            name="password"
            id="password"
            class="w-full rounded-xl border border-black/10 bg-transparent p-3.5 text-sm text-black shadow-inner placeholder-black/50 focus:border-brand-500 focus:bg-black/5 focus:ring-1 focus:ring-brand-500 transition-all duration-200"
            placeholder="••••••••"
            required
          />
        </div>
      </div>

      <div class="pt-2">
        <x-ui.button type="submit" variant="primary" class="w-full py-3.5 text-base shadow-pop">
          Log In
        </x-ui.button>
      </div>

      <p class="text-center text-sm text-black/70 mt-8">
        Don't have an account?
        <a class="font-medium text-brand-600 hover:text-brand-900 transition-colors" href="{{ route('register') }}">
          Create one now
        </a>
      </p>
    </form>
  </div>
</div>
@endsection

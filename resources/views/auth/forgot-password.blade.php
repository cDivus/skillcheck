@extends('layouts.auth')

@section('content')
<div class="w-full max-w-md animate-fade-in-up">
  <div class="text-center mb-8">
    <a href="{{ url('/') }}" class="inline-block hover-lift">
      <img src="{{ asset('images/Logo.svg') }}" class="mx-auto h-16 w-auto" style="filter: brightness(0) invert(1);" alt="SkillCheck Logo" />
    </a>
    <h1 class="mt-6 text-3xl font-bold tracking-tight text-white">
      Reset Password
    </h1>
    <p class="mt-2 text-sm text-brand-100/80">
      Enter your email address and we'll send you a recovery link.
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

    <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
      @csrf

      <div>
        <label for="email" class="block text-sm font-medium text-black mb-1.5">Email Address</label>
        <div class="relative">
          <input
            type="email"
            name="email"
            id="email"
            class="w-full rounded-xl border border-black/10 bg-transparent p-3.5 text-sm text-black shadow-inner placeholder-black/50 focus:border-brand-500 focus:bg-black/5 focus:ring-1 focus:ring-brand-500 transition-all duration-200"
            placeholder="you@example.com"
            value="{{ old('email') }}"
            required
            autofocus
          />
        </div>
      </div>

      <div class="pt-2">
        <x-ui.button type="submit" variant="primary" class="w-full py-3.5 text-base shadow-pop">
          Send Recovery Link
        </x-ui.button>
      </div>

      <p class="text-center text-sm text-black/70 mt-8">
        <a class="font-medium text-brand-600 hover:text-brand-900 transition-colors flex items-center justify-center gap-2" href="{{ route('login') }}">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
          Back to Login
        </a>
      </p>
    </form>
  </div>
</div>
@endsection

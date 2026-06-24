@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-lg px-4 py-8 sm:px-6 lg:px-8">
  <div class="text-center">
    <a href="{{ url('/') }}" class="inline-block">
      <img src="{{ asset('images/Icon.svg') }}" class="mx-auto h-12 w-auto" alt="Logo" />
    </a>
    <h1 class="mt-6 text-2xl font-bold tracking-tight text-brand-dark dark:text-brand-light sm:text-3xl">
      Welcome Back!
    </h1>
  </div>

  <div class="mt-8 rounded-2xl border border-brand-primary/10 bg-white p-8 shadow-lg dark:bg-brand-dark dark:border-brand-light/10">
    @if (session('status'))
        <div class="mb-4 rounded-lg bg-emerald-50 p-4 text-sm text-emerald-700 dark:bg-emerald-950/20 dark:text-emerald-400" role="alert">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-950/20 dark:text-red-400">
            <ul class="mb-0 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="space-y-4">
      @csrf

      <div>
        <label for="login" class="block text-sm font-medium text-gray-700 dark:text-brand-light/95">Email or Username</label>
        <div class="relative mt-1">
          <input
            type="text"
            name="login"
            id="login"
            class="w-full rounded-lg border-gray-200 p-4 pe-12 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:bg-brand-dark dark:border-brand-light/10 dark:text-brand-light"
            placeholder="Enter email or username"
            value="{{ old('login') }}"
            required
            autofocus
          />
        </div>
      </div>

      <div>
        <div class="flex justify-between items-center">
          <label for="password" class="block text-sm font-medium text-gray-700 dark:text-brand-light/95">Password</label>
          <a href="{{ route('password.request') }}" class="text-xs text-brand-primary hover:text-brand-secondary dark:text-brand-accent dark:hover:text-brand-light">
            Forgot Password?
          </a>
        </div>
        <div class="relative mt-1">
          <input
            type="password"
            name="password"
            id="password"
            class="w-full rounded-lg border-gray-200 p-4 pe-12 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:bg-brand-dark dark:border-brand-light/10 dark:text-brand-light"
            placeholder="Enter password"
            required
          />
        </div>
      </div>

      <button
        type="submit"
        class="block w-full rounded-lg bg-brand-primary px-5 py-3 text-sm font-medium text-white transition hover:bg-brand-secondary dark:bg-brand-accent dark:text-brand-secondary dark:hover:bg-brand-light"
      >
        Log-In
      </button>

      <p class="text-center text-sm text-gray-500 dark:text-brand-light/70 mt-6">
        Don't have an account?
        <a class="underline text-brand-primary hover:text-brand-secondary dark:text-brand-accent dark:hover:text-brand-light" href="{{ route('register') }}">
          Register here
        </a>
      </p>
    </form>
  </div>
</div>
@endsection

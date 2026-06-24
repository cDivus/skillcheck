@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-lg px-4 py-8 sm:px-6 lg:px-8">
  <div class="text-center">
    <a href="{{ url('/') }}" class="inline-block">
      <img src="{{ asset('images/Logo.svg') }}" class="mx-auto h-16 w-auto" alt="SkillCheck Logo" />
    </a>
    <h1 class="mt-6 text-2xl font-bold tracking-tight text-brand-dark dark:text-brand-light sm:text-3xl">
      Reset Your Password
    </h1>
    <p class="mt-2 text-sm text-gray-500 dark:text-brand-light/75">
      Enter your email address, and we'll send you a recovery link.
    </p>
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

    <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
      @csrf

      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-brand-light/95">Email Address</label>
        <div class="relative mt-1">
          <input
            type="email"
            name="email"
            id="email"
            class="w-full rounded-lg border-gray-200 p-4 pe-12 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:bg-brand-dark dark:border-brand-light/10 dark:text-brand-light"
            placeholder="Enter your email address"
            value="{{ old('email') }}"
            required
            autofocus
          />
        </div>
      </div>

      <button
        type="submit"
        class="block w-full rounded-lg bg-brand-primary px-5 py-3 text-sm font-medium text-white transition hover:bg-brand-secondary dark:bg-brand-accent dark:text-brand-secondary dark:hover:bg-brand-light"
      >
        Send Password Reset Link
      </button>

      <p class="text-center text-sm text-gray-500 dark:text-brand-light/70 mt-6">
        <a class="underline text-brand-primary hover:text-brand-secondary dark:text-brand-accent dark:hover:text-brand-light font-medium" href="{{ route('login') }}">
          Back to Login
        </a>
      </p>
    </form>
  </div>
</div>
@endsection

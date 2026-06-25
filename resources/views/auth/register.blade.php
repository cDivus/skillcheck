@extends('layouts.auth')

@section('content')
<div class="w-full max-w-2xl animate-fade-in-up">
  <div class="text-center mb-8">
    <a href="{{ url('/') }}" class="inline-block hover-lift">
      <img src="{{ asset('images/Logo.svg') }}" class="mx-auto h-16 w-auto" style="filter: brightness(0) invert(1);" alt="SkillCheck Logo" />
    </a>
    <h1 class="mt-6 text-3xl font-bold tracking-tight text-white">
      Create an Account
    </h1>
    <p class="mt-2 text-sm text-brand-100/80">
      Join SkillCheck to start your journey
    </p>
  </div>

  <div class="sc-card glass-panel p-8 shadow-2xl">
    @if ($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4 text-sm text-red-600">
            <ul class="mb-0 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
      @csrf

      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
          <label for="username" class="block text-sm font-medium text-black mb-1.5">Username</label>
          <input
            type="text"
            name="username"
            id="username"
            class="w-full rounded-xl border border-black/10 bg-transparent p-3.5 text-sm text-black shadow-inner placeholder-black/50 focus:border-brand-500 focus:bg-black/5 focus:ring-1 focus:ring-brand-500 transition-all duration-200"
            placeholder="Choose a username"
            value="{{ old('username') }}"
            required
          />
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-black mb-1.5">Email Address</label>
          <input
            type="email"
            name="email"
            id="email"
            class="w-full rounded-xl border border-black/10 bg-transparent p-3.5 text-sm text-black shadow-inner placeholder-black/50 focus:border-brand-500 focus:bg-black/5 focus:ring-1 focus:ring-brand-500 transition-all duration-200"
            placeholder="you@example.com"
            value="{{ old('email') }}"
            required
          />
        </div>
      </div>

      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
          <label for="password" class="block text-sm font-medium text-black mb-1.5">Password</label>
          <input
            type="password"
            name="password"
            id="password"
            class="w-full rounded-xl border border-black/10 bg-transparent p-3.5 text-sm text-black shadow-inner placeholder-black/50 focus:border-brand-500 focus:bg-black/5 focus:ring-1 focus:ring-brand-500 transition-all duration-200"
            placeholder="Create a password"
            required
          />
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-black mb-1.5">Confirm Password</label>
          <input
            type="password"
            name="password_confirmation"
            id="password_confirmation"
            class="w-full rounded-xl border border-black/10 bg-transparent p-3.5 text-sm text-black shadow-inner placeholder-black/50 focus:border-brand-500 focus:bg-black/5 focus:ring-1 focus:ring-brand-500 transition-all duration-200"
            placeholder="Re-enter password"
            required
          />
        </div>
      </div>

      <div>
        <label for="role" class="block text-sm font-medium text-black mb-1.5">Account Role</label>
        <select
          name="role"
          id="role"
          class="w-full rounded-xl border border-black/10 bg-transparent p-3.5 text-sm text-black shadow-inner focus:border-brand-500 focus:bg-black/5 focus:ring-1 focus:ring-brand-500 transition-all duration-200 [&>option]:text-ink"
          required
        >
          <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student (Take Exams)</option>
          <option value="instructor" {{ old('role') === 'instructor' ? 'selected' : '' }}>Instructor (Manage Exams)</option>
        </select>
      </div>

      <div>
        <label for="profile_picture" class="block text-sm font-medium text-black mb-1.5">Profile Picture</label>
        <div class="relative w-full rounded-xl border border-black/10 bg-transparent p-2 shadow-inner focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all duration-200">
          <input
            type="file"
            name="profile_picture"
            id="profile_picture"
            class="w-full text-sm text-black/70 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-500 file:text-white hover:file:bg-brand-400 file:transition-colors file:cursor-pointer cursor-pointer"
            accept="image/*"
          />
        </div>
        <p class="text-xs text-black/60 mt-2">Optional. Recommended format: JPEG/PNG/WebP, max 2MB.</p>
      </div>

      <div class="pt-2">
        <x-ui.button type="submit" variant="primary" class="w-full py-3.5 text-base shadow-pop">
          Create Account
        </x-ui.button>
      </div>

      <p class="text-center text-sm text-black/70 mt-8">
        Already have an account?
        <a class="font-medium text-brand-600 hover:text-brand-900 transition-colors" href="{{ route('login') }}">
          Login here
        </a>
      </p>
    </form>
  </div>
</div>
@endsection

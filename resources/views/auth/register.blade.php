@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
  <div class="text-center">
    <a href="{{ url('/') }}" class="inline-block">
      <img src="{{ asset('images/Logo.svg') }}" class="mx-auto h-16 w-auto" alt="SkillCheck Logo" />
    </a>
    <h1 class="mt-6 text-2xl font-bold tracking-tight text-brand-dark dark:text-brand-light sm:text-3xl">
      Welcome to SkillCheck!
    </h1>
    <p class="mt-2 text-sm text-gray-500 dark:text-brand-light/75">
      Let's Get You Started
    </p>
  </div>

  <div class="mt-8 rounded-2xl border border-brand-primary/10 bg-white p-8 shadow-lg dark:bg-brand-dark dark:border-brand-light/10">
    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-700 dark:bg-red-950/20 dark:text-red-400">
            <ul class="mb-0 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 dark:text-brand-light/95">Username</label>
          <input
            type="text"
            name="username"
            id="username"
            class="w-full rounded-lg border-gray-200 p-4 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:bg-brand-dark dark:border-brand-light/10 dark:text-brand-light mt-1"
            placeholder="Enter username"
            value="{{ old('username') }}"
            required
          />
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 dark:text-brand-light/95">Email Address</label>
          <input
            type="email"
            name="email"
            id="email"
            class="w-full rounded-lg border-gray-200 p-4 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:bg-brand-dark dark:border-brand-light/10 dark:text-brand-light mt-1"
            placeholder="Enter email address"
            value="{{ old('email') }}"
            required
          />
        </div>
      </div>

      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 dark:text-brand-light/95">Password</label>
          <input
            type="password"
            name="password"
            id="password"
            class="w-full rounded-lg border-gray-200 p-4 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:bg-brand-dark dark:border-brand-light/10 dark:text-brand-light mt-1"
            placeholder="Create password"
            required
          />
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-brand-light/95">Confirm Password</label>
          <input
            type="password"
            name="password_confirmation"
            id="password_confirmation"
            class="w-full rounded-lg border-gray-200 p-4 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:bg-brand-dark dark:border-brand-light/10 dark:text-brand-light mt-1"
            placeholder="Re-enter password"
            required
          />
        </div>
      </div>

      <div>
        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-brand-light/95">Account Role</label>
        <select
          name="role"
          id="role"
          class="w-full rounded-lg border-gray-200 p-4 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:bg-brand-dark dark:border-brand-light/10 dark:text-brand-light mt-1"
          required
        >
          <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student (Take Exams)</option>
          <option value="instructor" {{ old('role') === 'instructor' ? 'selected' : '' }}>Instructor (Manage Exams)</option>
        </select>
      </div>

      <div>
        <label for="profile_picture" class="block text-sm font-medium text-gray-700 dark:text-brand-light/95">Profile Picture</label>
        <input
          type="file"
          name="profile_picture"
          id="profile_picture"
          class="w-full rounded-lg border-gray-200 p-4 text-sm shadow-sm focus:border-brand-primary focus:ring-brand-primary dark:bg-brand-dark dark:border-brand-light/10 dark:text-brand-light mt-1 bg-white"
          accept="image/*"
        />
        <p class="text-xs text-gray-400 mt-1">Optional. Recommended format: JPEG/PNG/WebP, max 2MB.</p>
      </div>

      <button
        type="submit"
        class="block w-full rounded-lg bg-brand-primary px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-secondary transition dark:bg-brand-accent dark:text-brand-secondary dark:hover:bg-brand-light"
      >
        Create Account
      </button>

      <p class="text-center text-sm text-gray-500 dark:text-brand-light/70 mt-6">
        Already have an account?
        <a class="underline text-brand-primary hover:text-brand-secondary dark:text-brand-accent dark:hover:text-brand-light" href="{{ route('login') }}">
          Login here
        </a>
      </p>
    </form>
  </div>
</div>
@endsection

@extends('layouts.guest')

@section('title', 'Log in')

@section('content')
    <div class="mb-6 text-center">
        <h1 class="text-xl font-semibold text-ink">Welcome back</h1>
        <p class="mt-1 text-sm text-muted">Sign in to your SkillCheck account</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc space-y-0.5 pl-4">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="space-y-4">
        @csrf
        <x-ui.input label="Username or Email" name="login" value="{{ old('login') }}" required autofocus />

        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label for="password" class="text-sm font-medium text-ink">Password</label>
                <a href="{{ route('password.request') }}" class="text-xs font-medium text-brand-700 hover:text-brand-800">Forgot password?</a>
            </div>
            <input type="password" name="password" id="password" required
                class="w-full rounded-lg border border-line-strong bg-white px-3 py-2 text-sm text-ink shadow-xs outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25">
        </div>

        <x-ui.button type="submit" variant="primary" class="w-full">Log in</x-ui.button>
    </form>
@endsection

@section('below')
    Don't have an account? <a href="{{ route('register') }}" class="font-medium text-brand-700 hover:text-brand-800">Create one</a>
@endsection

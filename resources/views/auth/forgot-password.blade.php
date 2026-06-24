@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
    <div class="mb-6 text-center">
        <h1 class="text-xl font-semibold text-ink">Forgot password?</h1>
        <p class="mt-1 text-sm text-muted">Enter your email and we'll send you a reset link.</p>
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

    <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
        @csrf
        <x-ui.input label="Email Address" name="email" type="email" value="{{ old('email') }}" required autofocus />
        <x-ui.button type="submit" variant="primary" class="w-full">Send Password Reset Link</x-ui.button>
    </form>
@endsection

@section('below')
    <a href="{{ route('login') }}" class="font-medium text-brand-700 hover:text-brand-800">Back to login</a>
@endsection

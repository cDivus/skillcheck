@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
    <div class="mb-6 text-center">
        <h1 class="text-xl font-semibold text-ink">Reset password</h1>
        <p class="mt-1 text-sm text-muted">Choose a new password for your account.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc space-y-0.5 pl-4">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-ink">Email Address</label>
            <input type="email" name="email" id="email" value="{{ request()->query('email', old('email')) }}" required readonly
                class="w-full cursor-not-allowed rounded-lg border border-line bg-subtle px-3 py-2 text-sm text-muted">
        </div>

        <x-ui.input label="New Password" name="password" type="password" required autofocus />
        <x-ui.input label="Confirm Password" name="password_confirmation" type="password" required />

        <x-ui.button type="submit" variant="primary" class="w-full">Reset Password</x-ui.button>
    </form>
@endsection

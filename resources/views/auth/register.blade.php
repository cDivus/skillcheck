@extends('layouts.guest')

@section('title', 'Register')

@section('content')
    <div class="mb-6 text-center">
        <h1 class="text-xl font-semibold text-ink">Create your account</h1>
        <p class="mt-1 text-sm text-muted">Get started with SkillCheck</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc space-y-0.5 pl-4">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <x-ui.input label="Username" name="username" value="{{ old('username') }}" required />
        <x-ui.input label="Email Address" name="email" type="email" value="{{ old('email') }}" required />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-ui.input label="Password" name="password" type="password" required />
            <x-ui.input label="Confirm Password" name="password_confirmation" type="password" required />
        </div>

        <x-ui.select label="Account Role" name="role" required>
            <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student (Take Exams)</option>
            <option value="instructor" {{ old('role') === 'instructor' ? 'selected' : '' }}>Instructor (Manage Exams)</option>
        </x-ui.select>

        <div>
            <label for="profile_picture" class="mb-1.5 block text-sm font-medium text-ink">Profile Picture</label>
            <input type="file" name="profile_picture" id="profile_picture" accept="image/*"
                class="block w-full text-sm text-muted file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100">
            <p class="mt-1.5 text-xs text-muted">Optional. JPEG/PNG/WebP, max 2MB.</p>
        </div>

        <x-ui.button type="submit" variant="primary" class="w-full">Create Account</x-ui.button>
    </form>
@endsection

@section('below')
    Already have an account? <a href="{{ route('login') }}" class="font-medium text-brand-700 hover:text-brand-800">Log in</a>
@endsection

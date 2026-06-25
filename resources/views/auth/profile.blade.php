@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <x-ui.page-header title="My Profile" subtitle="Manage your account details and security." />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Identity card --}}
        <div class="lg:col-span-1">
            <x-ui.card class="text-center">
                <div class="flex flex-col items-center">
                    <x-ui.avatar :user="$user" size="xl" />
                    <h2 class="mt-4 text-lg font-semibold text-ink">{{ $user->username }}</h2>
                    <p class="mt-0.5 text-sm text-muted">{{ $user->email }}</p>
                    <div class="mt-3">
                        @php $roleColor = ['admin' => 'red', 'instructor' => 'brand', 'student' => 'blue'][$user->role] ?? 'gray'; @endphp
                        <x-ui.badge :color="$roleColor" class="uppercase tracking-wide">{{ $user->role }}</x-ui.badge>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Edit form --}}
        <div class="lg:col-span-2">
            <x-ui.card padding="p-0" class="overflow-hidden">
                <div class="border-b border-line px-5 py-4 sm:px-6">
                    <h2 class="flex items-center gap-2 text-base font-semibold text-ink">
                        <x-icon name="settings" class="w-4 h-4 text-muted" /> Edit Profile Details
                    </h2>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="px-5 py-5 sm:px-6 sm:py-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">
                        <x-ui.input label="Username" name="username" value="{{ old('username', $user->username) }}" required />

                        <div>
                            <label class="block text-sm font-medium text-ink mb-1.5">Email Address</label>
                            <input type="text" value="{{ $user->email }}" readonly disabled
                                class="w-full cursor-not-allowed rounded-lg border border-line bg-subtle px-3 py-2 text-sm text-muted">
                            <p class="mt-1.5 text-xs text-muted">Email address cannot be changed.</p>
                        </div>

                        <div>
                            <label for="profile_picture" class="block text-sm font-medium text-ink mb-1.5">Profile Picture</label>
                            <input type="file" name="profile_picture" id="profile_picture" accept="image/*"
                                class="block w-full text-sm text-muted file:mr-4 file:rounded-lg file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100 @error('profile_picture') text-red-600 @enderror">
                            <p class="mt-1.5 text-xs text-muted">Recommended: JPEG/PNG/WebP, max 2MB.</p>
                            @error('profile_picture')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="my-6 border-t border-line"></div>

                    <h3 class="flex items-center gap-2 text-sm font-semibold text-ink">
                        <x-icon name="lock" class="w-4 h-4 text-muted" /> Change Password
                    </h3>
                    <p class="mt-1 text-xs text-muted">Leave blank if you do not want to change your password.</p>

                    <div class="mt-4 grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <x-ui.input label="New Password" name="password" type="password" autocomplete="new-password" />
                        <x-ui.input label="Confirm New Password" name="password_confirmation" type="password" autocomplete="new-password" />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-ui.button type="submit" variant="primary"><x-icon name="check" /> Save Changes</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
@endsection

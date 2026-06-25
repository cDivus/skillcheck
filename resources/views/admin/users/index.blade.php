@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
    <x-ui.page-header
        title="Manage Users"
        subtitle="Browse accounts, filter by role, and suspend/unsuspend access." />

    {{-- Filter Form Card --}}
    <x-ui.card class="mb-4">
        <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-12 sm:items-end">
            <div class="sm:col-span-5">
                <x-ui.input
                    label="Search Username or Email"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Search username or email..." />
            </div>
            <div class="sm:col-span-3">
                <x-ui.select label="Filter by Role" name="role" id="role">
                    <option value="">All Roles</option>
                    <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </x-ui.select>
            </div>
            <div class="flex gap-2 sm:col-span-4">
                <x-ui.button type="submit" variant="primary" class="flex-1"><x-icon name="filter" /> Apply Filters</x-ui.button>
                @if(request()->anyFilled(['search', 'role']))
                    <x-ui.button href="{{ route('admin.users.index') }}" variant="secondary">Reset</x-ui.button>
                @endif
            </div>
        </form>
    </x-ui.card>

    {{-- Users Table Card --}}
    <x-ui.card padding="p-0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-line bg-subtle/60 text-left text-xs font-medium uppercase tracking-wide text-faint">
                        <th class="px-4 py-3 font-medium">User</th>
                        <th class="px-4 py-3 font-medium">Email</th>
                        <th class="px-4 py-3 font-medium">Role</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Registered</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($users as $user)
                        <tr class="hover:bg-subtle/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <x-ui.avatar :user="$user" size="md" />
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-semibold text-ink">{{ $user->username }}</span>
                                        @if($user->user_id === auth()->id())
                                            <x-ui.badge color="gray">You</x-ui.badge>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-muted">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                @if($user->role === 'admin')
                                    <x-ui.badge color="red">Admin</x-ui.badge>
                                @elseif($user->role === 'instructor')
                                    <x-ui.badge color="brand">Instructor</x-ui.badge>
                                @else
                                    <x-ui.badge color="blue">Student</x-ui.badge>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($user->is_suspended)
                                    <x-ui.badge color="red">Suspended</x-ui.badge>
                                @else
                                    <x-ui.badge color="green">Active</x-ui.badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-muted">
                                {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($user->user_id !== auth()->id())
                                    <form action="{{ route('admin.users.toggle-status', $user->user_id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        @if($user->is_suspended)
                                            <x-ui.button type="submit" variant="secondary" size="sm"><x-icon name="check-circle" /> Reactivate</x-ui.button>
                                        @else
                                            <x-ui.button type="submit" variant="danger-soft" size="sm" onclick="return confirm('Are you sure you want to suspend {{ $user->username }}? They will be blocked from logging in immediately.')"><x-icon name="lock" /> Suspend</x-ui.button>
                                        @endif
                                    </form>
                                @else
                                    <span class="text-xs italic text-faint">Cannot edit self</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-ui.empty-state icon="users" title="No users found" message="No users found matching your search." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    @if($users->hasPages())
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif
@endsection

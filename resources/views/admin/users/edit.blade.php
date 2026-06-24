@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <x-ui.page-header title="Edit User">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.users.index') }}" variant="secondary"><x-icon name="arrow-left" /> Back to Users</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.card>
        <p class="text-sm text-muted">
            User editing is managed from the
            <a href="{{ route('admin.users.index') }}" class="font-semibold text-brand-700 hover:text-brand-800">Manage Users</a>
            list.
        </p>
    </x-ui.card>
@endsection

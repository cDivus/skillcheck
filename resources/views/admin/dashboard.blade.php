@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <x-ui.page-header
        title="Overview Statistics"
        subtitle="High-level summary of the SkillCheck system's database entities." />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <x-ui.stat-card label="Total Accounts" :value="$stats['users']" icon="users" tone="brand" />
        <x-ui.stat-card label="Active Exams" :value="$stats['exams']" icon="clipboard-check" tone="green" />
        <x-ui.stat-card label="Student Submissions" :value="$stats['attempts']" icon="check-square" tone="amber" />
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-ui.card class="flex h-full flex-col justify-between">
            <div>
                <h2 class="flex items-center gap-2 text-lg font-semibold text-ink">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <x-icon name="users" class="h-5 w-5" />
                    </span>
                    User Management
                </h2>
                <p class="mt-3 text-sm text-muted">Inspect the full catalog of registered accounts. Suspend access for users who breach honor codes, or reactivate previously suspended accounts.</p>
            </div>
            <div class="mt-5">
                <x-ui.button href="{{ route('admin.users.index') }}" variant="primary">Go to Users List <x-icon name="arrow-right" /></x-ui.button>
            </div>
        </x-ui.card>

        <x-ui.card class="flex h-full flex-col justify-between">
            <div>
                <h2 class="flex items-center gap-2 text-lg font-semibold text-ink">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-green-50 text-green-600">
                        <x-icon name="clipboard-check" class="h-5 w-5" />
                    </span>
                    Exam Moderation
                </h2>
                <p class="mt-3 text-sm text-muted">Browse all exams generated across the system by various instructors. Audit titles, creators, and delete orphaned or violating exams to keep the index clean.</p>
            </div>
            <div class="mt-5">
                <x-ui.button href="{{ route('admin.exams.index') }}" variant="secondary">Go to Exams Moderation <x-icon name="arrow-right" /></x-ui.button>
            </div>
        </x-ui.card>
    </div>
@endsection

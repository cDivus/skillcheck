@extends('layouts.app')

@section('title', 'Moderate Exams')

@section('content')
    <x-ui.page-header
        title="Moderate Exams"
        subtitle="Browse and manage exams created by instructors on the platform." />

    {{-- Search Form Card --}}
    <x-ui.card class="mb-4">
        <form action="{{ route('admin.exams.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-12 sm:items-end">
            <div class="sm:col-span-8">
                <x-ui.input
                    label="Search Exam Title or Description"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Search exam title or keywords..." />
            </div>
            <div class="flex gap-2 sm:col-span-4">
                <x-ui.button type="submit" variant="primary" class="flex-1"><x-icon name="search" /> Search Exams</x-ui.button>
                @if(request()->filled('search'))
                    <x-ui.button href="{{ route('admin.exams.index') }}" variant="secondary">Reset</x-ui.button>
                @endif
            </div>
        </form>
    </x-ui.card>

    {{-- Exams List/Table Card --}}
    <x-ui.card padding="p-0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-line bg-subtle/60 text-left text-xs font-medium uppercase tracking-wide text-faint">
                        <th class="px-4 py-3 font-medium" style="width: 35%;">Exam Details</th>
                        <th class="px-4 py-3 font-medium">Created By</th>
                        <th class="px-4 py-3 font-medium">Duration</th>
                        <th class="px-4 py-3 font-medium">Schedule</th>
                        <th class="px-4 py-3 font-medium">Created Date</th>
                        <th class="px-4 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse($exams as $exam)
                        <tr class="hover:bg-subtle/50">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-ink">{{ $exam->title }}</div>
                                <div class="mt-0.5 max-w-xs truncate text-xs text-muted">
                                    {{ $exam->description ?? 'No description provided.' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($exam->instructor)
                                    <div>
                                        <span class="font-medium text-ink">{{ $exam->instructor->username }}</span>
                                        <span class="block text-xs text-muted">{{ $exam->instructor->email }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-muted">Unknown Instructor</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-ink">{{ round($exam->duration_s / 60, 1) }} mins</div>
                                <span class="text-xs text-muted">{{ $exam->duration_s }} seconds</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($exam->start_time && $exam->end_time)
                                    <div class="text-xs text-ink">
                                        <strong class="font-semibold">Start:</strong> {{ \Carbon\Carbon::parse($exam->start_time)->format('M d, g:i A') }}
                                    </div>
                                    <div class="text-xs text-muted">
                                        <strong class="font-semibold">End:</strong> {{ \Carbon\Carbon::parse($exam->end_time)->format('M d, g:i A') }}
                                    </div>
                                @else
                                    <x-ui.badge color="gray">Always Open</x-ui.badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-muted">
                                {{ $exam->created_at ? \Carbon\Carbon::parse($exam->created_at)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form action="{{ route('admin.exams.destroy', $exam->exam_id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="danger" size="sm" onclick="return confirm('Are you sure you want to delete this exam? This action will also delete all questions, options, student answers, and attempts for this exam and CANNOT be undone.')">
                                        <x-icon name="trash" /> Delete
                                    </x-ui.button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-ui.empty-state icon="clipboard-check" title="No exams found" message="No exams found matching your search." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    @if($exams->hasPages())
        <div class="mt-4">
            {{ $exams->links() }}
        </div>
    @endif
@endsection

@extends('layouts.app')

@section('title', 'Submissions')

@section('content')
    <x-ui.page-header title="Submissions" subtitle="Exam: {{ $exam->title }}">
        <x-slot:actions>
            <x-ui.button href="{{ route('instructor.exams.index') }}" variant="secondary">
                <x-icon name="arrow-left" /> Back to Exams Dashboard
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-3 flex items-center gap-2">
        <x-icon name="clipboard-check" class="w-5 h-5 text-faint" />
        <h2 class="text-lg font-semibold text-ink">Student Attempts</h2>
    </div>

    @if ($attempts->isEmpty())
        <x-ui.card padding="p-0" class="overflow-hidden">
            <x-ui.empty-state icon="clipboard-check" title="No submissions yet" message="No submissions recorded for this exam yet." />
        </x-ui.card>
    @else
        <x-ui.card padding="p-0" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-line bg-subtle/60 text-left text-xs font-medium uppercase tracking-wide text-faint">
                            <th class="px-4 py-3 font-medium">Student</th>
                            <th class="px-4 py-3 font-medium">Started At</th>
                            <th class="px-4 py-3 font-medium">Submitted At</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Score</th>
                            <th class="px-4 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($attempts as $attempt)
                            <tr class="hover:bg-subtle/50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-ink">{{ $attempt->student->username }}</div>
                                    <div class="text-xs text-muted">{{ $attempt->student->email }}</div>
                                </td>
                                <td class="px-4 py-3 text-muted">{{ $attempt->started_at ? $attempt->started_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                <td class="px-4 py-3 text-muted">{{ $attempt->submitted_at ? $attempt->submitted_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    @if ($attempt->status === 'graded')
                                        <x-ui.badge color="green">Graded</x-ui.badge>
                                    @else
                                        <x-ui.badge color="amber">Pending Grading</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($attempt->status === 'graded')
                                        <strong class="text-ink">{{ number_format($attempt->total_score, 1) }}</strong> <span class="text-muted">/ {{ number_format($attempt->max_score, 1) }}</span>
                                    @else
                                        <span class="text-muted">Partially graded</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <x-ui.button href="{{ route('instructor.submissions.grade', $attempt->attempt_id) }}" variant="primary" size="sm">
                                        {{ $attempt->status === 'graded' ? 'View Details' : 'Grade Submission' }}
                                    </x-ui.button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    @endif
@endsection

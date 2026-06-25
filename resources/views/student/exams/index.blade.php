@extends('layouts.app')

@section('title', 'My Exams')

@section('content')
<x-ui.page-header
    title="My Exam Dashboard"
    subtitle="Access new exams and track your attempts">
    <x-slot:actions>
        <x-ui.button variant="secondary" :href="route('profile.edit')">
            <x-icon name="user-cog" /> Edit Profile
        </x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

{{-- Access a New Exam --}}
<x-ui.card class="mb-6">
    <div class="flex items-center gap-2 text-brand-700">
        <x-icon name="arrow-right" class="h-5 w-5" />
        <h2 class="text-sm font-semibold text-ink">Access a New Exam</h2>
    </div>
    <p class="mt-1 text-sm text-muted">Enter the Exam ID (UUID) provided by your instructor to view details and start the exam.</p>
    <form id="access-exam-form" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
        <div class="flex-1">
            <x-ui.input id="exam-id-input" placeholder="Enter Exam UUID (e.g. 123e4567-e89b-12d3-a456-426614174000)" required />
        </div>
        <x-ui.button type="submit" variant="primary" class="sm:w-auto">Access Exam</x-ui.button>
    </form>
</x-ui.card>

{{-- My Exam Attempts --}}
<div class="mb-4 flex items-center gap-2">
    <x-icon name="list-ordered" class="h-5 w-5 text-brand-700" />
    <h2 class="text-base font-semibold text-ink">My Exam Attempts</h2>
</div>

@if($attempts->isEmpty())
    <x-ui.card padding="p-0" class="overflow-hidden">
        <x-ui.empty-state
            icon="clipboard-check"
            title="You have not attempted any exams yet"
            message="Enter an Exam ID above to get started." />
    </x-ui.card>
@else
    <x-ui.card padding="p-0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-line bg-subtle/60 text-left text-xs font-medium uppercase tracking-wide text-faint">
                        <th class="px-4 py-3 font-medium">Exam Title</th>
                        <th class="px-4 py-3 font-medium">Started At</th>
                        <th class="px-4 py-3 font-medium">Submitted At</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Score</th>
                        <th class="px-4 py-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @foreach($attempts as $attempt)
                        <tr class="hover:bg-subtle/50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-ink">{{ $attempt->exam->title ?? 'Unknown Exam' }}</div>
                                @if($attempt->exam && $attempt->exam->description)
                                    <div class="mt-0.5 text-xs text-muted">{{ Str::limit($attempt->exam->description, 70) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-muted">{{ $attempt->start_time ? $attempt->start_time->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-muted">{{ $attempt->end_time ? $attempt->end_time->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            <td class="px-4 py-3">
                                @if($attempt->status === 'in_progress')
                                    <x-ui.badge color="amber">In Progress</x-ui.badge>
                                @elseif($attempt->status === 'submitted')
                                    <x-ui.badge color="blue">Submitted</x-ui.badge>
                                @elseif($attempt->status === 'graded')
                                    <x-ui.badge color="green">Graded</x-ui.badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($attempt->status === 'graded')
                                    <span class="font-medium text-ink">{{ number_format($attempt->total_score, 2) }} / {{ number_format($attempt->max_score, 2) }}</span>
                                @elseif($attempt->status === 'submitted')
                                    <span class="text-xs text-muted">Pending Grading</span>
                                @else
                                    <span class="text-xs text-faint">&mdash;</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($attempt->status === 'in_progress')
                                    <x-ui.button size="sm" variant="primary" :href="route('student.exams.attempt.take', ['exam' => $attempt->exam_id, 'attempt' => $attempt->attempt_id])">
                                        <x-icon name="arrow-right" /> Resume
                                    </x-ui.button>
                                @else
                                    @if($attempt->exam && $attempt->exam->viewable_responses)
                                        <x-ui.button size="sm" variant="secondary" :href="route('student.exams.attempt.review', ['exam' => $attempt->exam_id, 'attempt' => $attempt->attempt_id])">
                                            <x-icon name="eye" /> View Responses
                                        </x-ui.button>
                                    @else
                                        <x-ui.button size="sm" variant="subtle" disabled title="Responses are locked by the instructor">
                                            <x-icon name="lock" /> Completed
                                        </x-ui.button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-ui.card>
@endif

<script>
    document.getElementById('access-exam-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const examId = document.getElementById('exam-id-input').value.trim();
        if (examId) {
            window.location.href = `{{ url('/student/exams') }}/${examId}`;
        }
    });
</script>
@endsection

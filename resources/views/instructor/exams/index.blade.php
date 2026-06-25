@extends('layouts.app')

@section('title', 'My Exams')

@section('content')
    <x-ui.page-header title="My Exams" subtitle="Manage your exams, questions, and submissions">
        <x-slot:actions>
            <x-ui.button href="{{ route('instructor.exams.create') }}" variant="primary">
                <x-icon name="plus" /> Create New Exam
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if ($exams->isEmpty())
        <x-ui.card padding="p-0" class="overflow-hidden">
            <x-ui.empty-state icon="file-text" title="No exams yet" message="You haven't created any exams. Get started by creating your first exam.">
                <x-slot:action>
                    <x-ui.button href="{{ route('instructor.exams.create') }}" variant="primary">
                        <x-icon name="plus" /> Create New Exam
                    </x-ui.button>
                </x-slot:action>
            </x-ui.empty-state>
        </x-ui.card>
    @else
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($exams as $exam)
                <x-ui.card class="flex flex-col">
                    <h2 class="text-lg font-semibold text-ink">{{ $exam->title }}</h2>
                    <p class="mt-1 text-sm text-muted">{{ $exam->description ?? 'No description' }}</p>

                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between gap-3">
                            <dt class="text-muted">Duration</dt>
                            <dd class="text-right text-ink">{{ $exam->duration_s }} seconds ({{ round($exam->duration_s / 60, 2) }} minutes)</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-muted">Start Time</dt>
                            <dd class="text-right text-ink">{{ $exam->start_time ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="text-muted">End Time</dt>
                            <dd class="text-right text-ink">{{ $exam->end_time ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-muted">Question Ordering</dt>
                            <dd>
                                <x-ui.badge :color="$exam->randomize_questions ? 'blue' : 'gray'">
                                    {{ $exam->randomize_questions ? 'Randomized' : 'Sequential' }}
                                </x-ui.badge>
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-muted">Viewable Responses</dt>
                            <dd>
                                <x-ui.badge :color="$exam->viewable_responses ? 'green' : 'red'">
                                    {{ $exam->viewable_responses ? 'Enabled' : 'Disabled' }}
                                </x-ui.badge>
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-ink mb-1.5">Student Link</label>
                        <input type="text" readonly value="{{ route('student.exams.show', ['exam' => $exam->exam_id]) }}"
                            class="w-full rounded-lg border border-line-strong bg-subtle px-3 py-2 text-sm text-muted shadow-xs outline-none focus:ring-2 focus:ring-brand-500/25">
                    </div>

                    <div class="mt-auto flex flex-wrap gap-2 pt-5">
                        <x-ui.button href="{{ route('instructor.exams.show', ['exam' => $exam->exam_id]) }}" variant="secondary" size="sm">
                            <x-icon name="list-ordered" /> Edit Questions
                        </x-ui.button>
                        <x-ui.button href="{{ route('instructor.exams.edit', ['exam' => $exam->exam_id]) }}" variant="secondary" size="sm">
                            <x-icon name="pencil" /> Edit Exam
                        </x-ui.button>
                        <x-ui.button href="{{ route('instructor.submissions.index', ['exam' => $exam->exam_id]) }}" variant="secondary" size="sm">
                            <x-icon name="users" /> View Submissions
                        </x-ui.button>
                    </div>
                </x-ui.card>
            @endforeach
        </div>
    @endif
@endsection

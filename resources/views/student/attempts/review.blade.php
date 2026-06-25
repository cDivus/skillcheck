@extends('layouts.app')

@section('title', 'Review Attempt')

@section('content')
<x-ui.page-header
    :title="'Exam Review: ' . $exam->title"
    subtitle="Review your responses and results">
    <x-slot:actions>
        <x-ui.badge :color="$attempt->status === 'graded' ? 'green' : 'blue'">
            Status: {{ ucfirst($attempt->status) }}
        </x-ui.badge>
        <x-ui.button variant="secondary" :href="route('student.exams.index')">
            <x-icon name="arrow-left" /> Back to Dashboard
        </x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

<div class="mx-auto max-w-4xl">
    {{-- Attempt Details Card --}}
    <x-ui.card class="mb-6">
        <div class="grid items-center gap-6 sm:grid-cols-12">
            <div class="space-y-2 sm:col-span-7">
                <p class="flex items-center gap-2 text-sm text-ink">
                    <strong class="font-medium">Instructor:</strong>
                    <x-ui.avatar :user="$exam->instructor" size="xs" />
                    <span>{{ $exam->instructor->username ?? 'N/A' }}</span>
                </p>
                <p class="text-sm text-ink"><strong class="font-medium">Started At:</strong> {{ $attempt->start_time ? $attempt->start_time->format('Y-m-d H:i:s') : 'N/A' }}</p>
                <p class="text-sm text-ink"><strong class="font-medium">Submitted At:</strong> {{ $attempt->end_time ? $attempt->end_time->format('Y-m-d H:i:s') : 'N/A' }}</p>
            </div>
            <div class="sm:col-span-5 sm:text-right">
                <div class="inline-block min-w-37.5 rounded-xl border border-brand-200 bg-brand-50 p-4 text-center">
                    <span class="block text-xs font-bold uppercase tracking-wide text-muted">Your Score</span>
                    @if ($attempt->status === 'graded')
                        <span class="text-3xl font-bold text-green-600">{{ number_format($attempt->total_score, 1) }}</span>
                        <span class="text-muted">/ {{ number_format($attempt->max_score, 1) }}</span>
                    @else
                        <span class="text-xl font-bold text-blue-600">Pending Grading</span>
                        <span class="mt-1 block text-xs text-muted">Some questions need manual grading</span>
                    @endif
                </div>
            </div>
        </div>
    </x-ui.card>

    <div class="mb-4 flex items-center gap-2">
        <x-icon name="clipboard-check" class="h-5 w-5 text-brand-700" />
        <h2 class="text-base font-semibold text-ink">Questions &amp; Your Responses</h2>
    </div>

    @foreach ($questions as $index => $question)
        @php
            $answer = $answers->get($question->question_id);
            $cardBorder = !$answer
                ? 'border-red-200'
                : ($question->type === 'essay' && is_null($answer->marks_awarded) ? 'border-amber-200' : 'border-line');
        @endphp
        <x-ui.card padding="p-0" class="mb-6 overflow-hidden border {{ $cardBorder }}">
            <div class="flex items-center justify-between gap-2 border-b border-line bg-subtle/60 px-5 py-3">
                <h3 class="text-sm font-semibold text-ink">
                    Question {{ $index + 1 }}
                    <span class="font-normal text-muted">({{ str_replace('_', ' ', $question->type) }})</span>
                </h3>
                <div>
                    @if ($answer && !is_null($answer->marks_awarded))
                        <x-ui.badge :color="$answer->marks_awarded > 0 ? 'green' : 'red'">
                            Marks: {{ number_format($answer->marks_awarded, 1) }} / {{ number_format($question->marks, 1) }}
                        </x-ui.badge>
                    @else
                        <x-ui.badge color="amber">
                            Marks: &mdash; / {{ number_format($question->marks, 1) }}
                        </x-ui.badge>
                    @endif
                </div>
            </div>

            <div class="p-5 sm:p-6">
                {{-- Question Text --}}
                <p class="text-base text-ink">{!! nl2br(e($question->question_text)) !!}</p>

                @if ($question->image_url)
                    <div class="mt-4">
                        <img src="{{ asset('storage/' . $question->image_url) }}" alt="Question Diagram" class="max-h-50 rounded-lg border border-line">
                    </div>
                @endif

                <hr class="my-5 border-line">

                {{-- Student response display --}}
                <div>
                    <strong class="mb-3 block text-sm font-medium text-ink">Options / Responses:</strong>

                    @if ($question->type === 'multiple_choice' || $question->type === 'true_false')
                        <div class="space-y-2">
                            @foreach ($question->options as $option)
                                @php
                                    $isSelected = $answer && $answer->selected_option === $option->option_id;
                                    $isCorrect = $option->is_correct;

                                    $bgClass = 'border-line';
                                    $badge = '';

                                    if ($isSelected) {
                                        if ($isCorrect) {
                                            $bgClass = 'border-green-200 bg-green-50';
                                            $badge = '<span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-200">Your Answer &amp; Correct</span>';
                                        } else {
                                            $bgClass = 'border-red-200 bg-red-50';
                                            $badge = '<span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-200">Your Answer</span>';
                                        }
                                    } elseif ($isCorrect) {
                                        $bgClass = 'border-green-200 bg-green-50/50';
                                        $badge = '<span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-200">Correct Answer</span>';
                                    }
                                @endphp
                                <div class="flex items-center justify-between gap-3 rounded-lg border px-4 py-3 {{ $bgClass }}">
                                    <span class="text-sm text-ink">{{ $option->option_text }}</span>
                                    <div>{!! $badge !!}</div>
                                </div>
                            @endforeach
                        </div>

                    @elseif ($question->type === 'question_answer')
                        <div class="mb-2 rounded-xl border border-brand-200 bg-brand-50 p-4">
                            <strong class="text-sm font-medium text-ink">Your Answer:</strong>
                            @if ($answer && $answer->text_answer)
                                <span class="ml-1 font-mono text-sm text-brand-700">{{ $answer->text_answer }}</span>
                            @else
                                <span class="ml-1 text-sm italic text-red-600">No answer provided.</span>
                            @endif
                        </div>
                        <div class="mt-2 text-sm text-muted">
                            <strong class="font-medium">Acceptable Correct Answers:</strong>
                            <ul class="mt-1 list-disc space-y-0.5 pl-5">
                                @foreach ($question->options->where('is_correct', true) as $opt)
                                    <li><span class="font-mono">{{ $opt->option_text }}</span></li>
                                @endforeach
                            </ul>
                        </div>

                    @elseif ($question->type === 'essay')
                        <div class="rounded-xl border border-brand-200 bg-brand-50 p-4">
                            <strong class="text-sm font-medium text-ink">Your Answer:</strong>
                            <p class="mt-2 text-sm text-ink">{!! nl2br(e($answer->text_answer ?? 'No answer provided.')) !!}</p>
                        </div>
                        @if ($answer && is_null($answer->marks_awarded))
                            <div class="mt-3 flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800">
                                <x-icon name="clock" class="h-4 w-4" /> This essay response is currently awaiting grading by your instructor.
                            </div>
                        @endif
                    @endif

                    @if (!$answer)
                        <div class="mt-2 flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                            <x-icon name="alert-triangle" class="h-4 w-4" /> No answer was submitted for this question.
                        </div>
                    @endif
                </div>
            </div>
        </x-ui.card>
    @endforeach

    <div class="mb-8 mt-6 text-center">
        <x-ui.button variant="primary" :href="route('student.exams.index')">
            <x-icon name="arrow-left" /> Back to Dashboard
        </x-ui.button>
    </div>
</div>
@endsection

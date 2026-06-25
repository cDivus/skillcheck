@extends('layouts.app')

@section('title', 'Grade Submission')

@section('content')
    <x-ui.page-header title="Grading Attempt: {{ $attempt->student->username }}" subtitle="Exam: {{ $attempt->exam->title }}">
        <x-slot:actions>
            <x-ui.button href="{{ route('instructor.submissions.index', $attempt->exam_id) }}" variant="secondary">
                <x-icon name="arrow-left" /> Back to Submissions
            </x-ui.button>
            <form action="{{ route('instructor.attempts.destroy', $attempt->attempt_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this student attempt? This will permanently delete their answers and cannot be undone.');" class="inline">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="danger"><x-icon name="trash" /> Delete Attempt</x-ui.button>
            </form>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="space-y-6">
        <div class="flex justify-end">
            <x-ui.badge color="gray">Status: <strong>{{ ucfirst($attempt->status) }}</strong></x-ui.badge>
        </div>

        {{-- Attempt summary --}}
        <x-ui.card padding="p-0" class="overflow-hidden">
            <div class="flex items-center gap-2 border-b border-line bg-brand-700 px-5 py-4 text-white">
                <x-icon name="user" class="w-5 h-5" />
                <h4 class="text-base font-semibold">Grading Attempt: {{ $attempt->student->username }}</h4>
            </div>
            <div class="grid gap-4 p-5 text-sm sm:grid-cols-2">
                <div>
                    <p><span class="text-muted">Exam:</span> <span class="text-ink">{{ $attempt->exam->title }}</span></p>
                    <p>
                        <span class="text-muted">Duration:</span> 
                        <span class="text-ink">
                            {{ $attempt->exam->timer_type === 'per_question' ? 'Per Question' : $attempt->exam->duration_m . ' minutes' }}
                        </span>
                    </p>
                </div>
                <div class="sm:text-right">
                    <p><span class="text-muted">Started At:</span> <span class="text-ink">{{ $attempt->started_at ? $attempt->started_at->format('Y-m-d H:i:s') : 'N/A' }}</span></p>
                    <p><span class="text-muted">Submitted At:</span> <span class="text-ink">{{ $attempt->submitted_at ? $attempt->submitted_at->format('Y-m-d H:i:s') : 'N/A' }}</span></p>
                </div>
            </div>
        </x-ui.card>

        <h3 class="text-lg font-semibold text-ink">Questions &amp; Answers</h3>

        @foreach ($attempt->exam->questions as $index => $question)
            @php
                $answer = $answers->get($question->question_id);
            @endphp
            <x-ui.card padding="p-0" class="overflow-hidden {{ $question->type === 'essay' ? 'border-amber-200' : '' }}">
                <div class="flex items-center justify-between gap-3 border-b border-line bg-subtle/60 px-5 py-3">
                    <h5 class="text-sm font-semibold text-ink">Question {{ $index + 1 }} <span class="text-muted font-normal">({{ str_replace('_', ' ', $question->type) }})</span></h5>
                    <x-ui.badge color="blue">Max Marks: {{ number_format($question->marks, 1) }}</x-ui.badge>
                </div>
                <div class="p-5">
                    <p class="text-base text-ink">{{ $question->question_text }}</p>

                    @if ($question->image_url)
                        <div class="mt-3">
                            <img src="{{ filter_var($question->image_url, FILTER_VALIDATE_URL) ? $question->image_url : asset('storage/' . $question->image_url) }}" alt="Question Diagram" class="max-h-[200px] rounded-lg border border-line">
                        </div>
                    @endif

                    <hr class="my-4 border-line">

                    <!-- Student response display -->
                    <div class="mb-4">
                        <strong class="text-sm text-ink">Student Response:</strong>
                        <div class="mt-2 rounded-lg border border-line bg-subtle/60 p-3 text-sm text-ink">
                            @if ($answer)
                                @if ($question->type === 'multiple_choice' || $question->type === 'true_false')
                                    @php
                                        $selectedOpt = $question->options->where('option_id', $answer->selected_option)->first();
                                    @endphp
                                    {{ $selectedOpt ? $selectedOpt->option_text : 'No option chosen' }}
                                @else
                                    {!! nl2br(e($answer->text_answer ?? 'No answer provided')) !!}
                                @endif
                            @else
                                <span class="italic text-red-600">No answer submitted for this question.</span>
                            @endif
                        </div>
                    </div>

                    <!-- Grading controls / feedback -->
                    @if ($question->type === 'essay' || $question->type === 'question_answer')
                        @if ($question->type === 'question_answer')
                            <div class="mb-2 flex items-center gap-2">
                                <span class="text-sm font-bold text-ink">Auto-Graded Marks:</span>
                                @if ($answer)
                                    <x-ui.badge :color="$answer->marks_awarded > 0 ? 'green' : 'red'">
                                        {{ number_format($answer->marks_awarded, 1) }} / {{ number_format($question->marks, 1) }}
                                    </x-ui.badge>
                                @else
                                    <x-ui.badge color="red">0.0 / {{ number_format($question->marks, 1) }}</x-ui.badge>
                                @endif
                            </div>
                            <div class="mb-3 text-sm">
                                <strong class="text-ink">Correct Setup:</strong>
                                <ul class="mt-1 list-disc pl-5 text-muted">
                                    @foreach($question->options as $opt)
                                        @if($opt->is_correct)
                                            <li>{{ $opt->option_text }} <x-ui.badge color="green">Correct</x-ui.badge></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('instructor.answers.grade.update', $answer ? $answer->answer_id : 0) }}" method="POST" class="flex flex-wrap items-center gap-3">
                            @csrf
                            @method('PUT')
                            <label for="marks-{{ $question->question_id }}" class="text-sm font-bold {{ $question->type === 'essay' ? 'text-amber-700' : 'text-brand-700' }}">
                                {{ $question->type === 'essay' ? 'Award Marks:' : 'Override Marks:' }}
                            </label>
                            <input type="number" step="0.01" min="0" max="{{ $question->marks }}"
                                name="marks_awarded" id="marks-{{ $question->question_id }}"
                                class="w-24 rounded-lg border border-line-strong bg-white px-3 py-1.5 text-sm text-ink shadow-xs outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25 disabled:opacity-50"
                                value="{{ $answer && $answer->marks_awarded !== null ? (float)$answer->marks_awarded : '' }}" required
                                {{ !$answer ? 'disabled' : '' }}>
                            <x-ui.button type="submit" size="sm" :variant="$question->type === 'essay' ? 'secondary' : 'primary'" :disabled="!$answer">
                                {{ $question->type === 'essay' ? 'Save Marks' : 'Override Marks' }}
                            </x-ui.button>
                            @if (!$answer)
                                <span class="text-xs text-muted">Cannot grade a non-existent answer.</span>
                            @endif
                        </form>
                    @else
                        <!-- Auto-graded display -->
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-ink">Auto-Graded Marks:</span>
                            @if ($answer)
                                <x-ui.badge :color="$answer->marks_awarded > 0 ? 'green' : 'red'">
                                    {{ number_format($answer->marks_awarded, 1) }} / {{ number_format($question->marks, 1) }}
                                </x-ui.badge>
                            @else
                                <x-ui.badge color="red">0.0 / {{ number_format($question->marks, 1) }}</x-ui.badge>
                            @endif
                        </div>
                        <div class="mt-2 text-sm">
                            <strong class="text-ink">Options Setup:</strong>
                            <ul class="mt-1 list-disc space-y-1 pl-5 text-muted">
                                @foreach($question->options as $opt)
                                    @php
                                        $isStudentSelected = $answer && $answer->selected_option === $opt->option_id;
                                    @endphp
                                    <li class="{{ $isStudentSelected ? 'font-bold text-ink' : '' }}">
                                        {{ $opt->option_text }}
                                        @if($opt->is_correct)
                                            <x-ui.badge color="green">Correct</x-ui.badge>
                                        @endif
                                        @if($isStudentSelected)
                                            <x-ui.badge color="brand">Student Choice</x-ui.badge>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </x-ui.card>
        @endforeach

        <!-- Finalize Grading Button -->
        <x-ui.card class="bg-brand-50">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h5 class="text-base font-semibold text-ink">Submit Grade Evaluation</h5>
                    <p class="mt-0.5 text-sm text-muted">This will lock the attempt score and publish the results to the student dashboard.</p>
                </div>
                <div>
                    <form action="{{ route('instructor.attempts.finalize', $attempt->attempt_id) }}" method="POST">
                        @csrf
                        <x-ui.button type="submit" variant="primary" size="lg"
                            onclick="return confirm('Confirm finalizing the grade sheet? Make sure all essay questions have been saved first.');">
                            <x-icon name="check-circle" /> Finalize and Grade
                        </x-ui.button>
                    </form>
                </div>
            </div>
        </x-ui.card>
    </div>
@endsection

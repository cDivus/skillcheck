@extends('layouts.app')

@section('title', 'Exam Details')

@section('content')
    <x-ui.page-header title="Exam: {{ $exam->title }}" subtitle="Manage the questions for this exam">
        <x-slot:actions>
            <x-ui.button href="{{ route('instructor.exams.index') }}" variant="secondary">
                <x-icon name="arrow-left" /> Back to Exams List
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Exam Details Card --}}
    <x-ui.card class="mb-6">
        <p class="text-base text-ink">{{ $exam->description ?? 'No description' }}</p>

        <div class="mt-4 grid gap-4 sm:grid-cols-3 text-sm">
            <div>
                <span class="text-muted">Duration</span>
                <p class="text-ink">{{ $exam->duration_s }} seconds ({{ round($exam->duration_s / 60, 2) }} minutes)</p>
            </div>
            <div>
                <span class="text-muted">Start Time</span>
                <p class="text-ink">{{ $exam->start_time ?? 'N/A' }}</p>
            </div>
            <div>
                <span class="text-muted">End Time</span>
                <p class="text-ink">{{ $exam->end_time ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
            <span class="flex items-center gap-2">
                <span class="text-muted">Question Ordering:</span>
                <x-ui.badge :color="$exam->randomize_questions ? 'blue' : 'gray'">
                    {{ $exam->randomize_questions ? 'Randomized' : 'Sequential (Order Index)' }}
                </x-ui.badge>
            </span>
            <span class="flex items-center gap-2">
                <span class="text-muted">Viewable Responses:</span>
                <x-ui.badge :color="$exam->viewable_responses ? 'green' : 'red'">
                    {{ $exam->viewable_responses ? 'Enabled' : 'Disabled' }}
                </x-ui.badge>
            </span>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-medium text-ink mb-1.5">Student Link</label>
            <input type="text" readonly value="{{ route('student.exams.show', ['exam' => $exam->exam_id]) }}"
                class="w-full rounded-lg border border-line-strong bg-subtle px-3 py-2 text-sm text-muted shadow-xs outline-none focus:ring-2 focus:ring-brand-500/25">
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            <x-ui.button href="{{ route('instructor.questions.create', ['exam' => $exam->exam_id]) }}" variant="primary">
                <x-icon name="plus" /> Add Question
            </x-ui.button>

            <x-ui.modal title="Import Questions from JSON" size="max-w-2xl">
                <x-slot:trigger>
                    <x-ui.button variant="secondary">
                        <x-icon name="upload" /> Import Questions (JSON)
                    </x-ui.button>
                </x-slot:trigger>

                <form id="import-questions-form" action="{{ route('instructor.questions.import', ['exam' => $exam->exam_id]) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label for="json_file" class="block text-sm font-medium text-ink mb-1.5">Choose JSON File</label>
                        <input class="w-full rounded-lg border border-line-strong bg-white px-3 py-2 text-sm text-ink shadow-xs outline-none file:mr-3 file:rounded-md file:border-0 file:bg-subtle file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-ink focus:ring-2 focus:ring-brand-500/25" type="file" id="json_file" name="json_file" accept=".json" required>
                        <p class="mt-1.5 text-xs text-muted">Upload a valid JSON file containing an array of questions. Max size 2MB.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink mb-2">Import Mode</label>
                        <div class="flex flex-col gap-2 sm:flex-row sm:gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input class="h-4 w-4 border-line-strong text-brand-700 focus:ring-brand-500/25" type="radio" name="import_mode" id="mode_append" value="append" checked>
                                <span class="text-sm text-ink"><strong>Append</strong> (Add to existing questions)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input class="h-4 w-4 border-line-strong text-red-600 focus:ring-red-500/25" type="radio" name="import_mode" id="mode_overwrite" value="overwrite">
                                <span class="text-sm text-red-600"><strong>Overwrite</strong> (Delete all current questions in this exam first)</span>
                            </label>
                        </div>
                    </div>

                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                        <h6 class="mb-2 text-sm font-semibold text-blue-700">Supported JSON Schema Example:</h6>
                        <pre class="mb-0 max-h-[250px] overflow-y-auto rounded-lg bg-gray-900 p-3 text-xs text-gray-100"><code>[
  {
    "question_text": "What is the capital of France?",
    "type": "multiple_choice",
    "marks": 5,
    "time_limit_s": 60,
    "is_locked": false,
    "image_url": "https://upload.wikimedia.org/wikipedia/commons/e/e6/Paris_Night.jpg",
    "options": [
      { "option_text": "Paris", "is_correct": true },
      { "option_text": "London", "is_correct": false }
    ]
  },
  {
    "question_text": "Laravel is built with PHP.",
    "type": "true_false",
    "correct_answer": true,
    "marks": 2
  },
  {
    "question_text": "What is the name of Laravel's ORM?",
    "type": "question_answer",
    "correct_answers": ["Eloquent", "eloquent"],
    "marks": 3
  },
  {
    "question_text": "Explain the Laravel request lifecycle.",
    "type": "essay",
    "marks": 10
  }
]</code></pre>
                    </div>

                    <div class="rounded-xl bg-brand-50 p-4">
                        <h6 class="flex items-center gap-1.5 text-sm font-semibold text-brand-800">
                            <x-icon name="info" class="w-4 h-4" /> Tip for Local Images:
                        </h6>
                        <p class="mt-1 text-xs text-muted">
                            You can include web image URLs in your JSON (using <code class="rounded bg-white px-1 py-0.5">"image_url": "https://..."</code>). For local files, import the questions first, and then click <strong>Edit Question</strong> to upload local images manually.
                        </p>
                    </div>
                </form>

                <x-slot:footer>
                    <x-ui.button type="button" variant="secondary" x-on:click="open = false">Cancel</x-ui.button>
                    <x-ui.button type="submit" variant="primary" x-on:click="document.getElementById('import-questions-form').submit()">
                        <x-icon name="upload" /> Upload &amp; Import
                    </x-ui.button>
                </x-slot:footer>
            </x-ui.modal>

            @if ($exam->questions->count() > 0)
                <x-ui.button href="{{ route('instructor.questions.export', ['exam' => $exam->exam_id]) }}" variant="secondary">
                    <x-icon name="download" /> Export Questions (JSON)
                </x-ui.button>
            @endif
            @if ($exam->questions->count() > 1)
                <x-ui.button href="{{ route('instructor.questions.reorder', ['exam' => $exam->exam_id]) }}" variant="secondary">
                    <x-icon name="list-ordered" /> Reorder Questions
                </x-ui.button>
            @endif
        </div>
    </x-ui.card>

    {{-- Questions Section --}}
    <div class="mb-3 flex items-center gap-2">
        <x-icon name="file-text" class="w-5 h-5 text-faint" />
        <h2 class="text-lg font-semibold text-ink">Questions ({{ $exam->questions->count() }})</h2>
    </div>

    @if ($exam->questions->isEmpty())
        <x-ui.card padding="p-0" class="overflow-hidden">
            <x-ui.empty-state icon="file-text" title="No questions yet" message="No questions added to this exam yet." />
        </x-ui.card>
    @else
        <div class="space-y-4">
            @foreach ($exam->questions as $question)
                <x-ui.card padding="p-0" class="overflow-hidden">
                    <div class="flex flex-col gap-3 border-b border-line bg-subtle/60 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-ui.badge color="brand">Index: {{ $question->order_index }}</x-ui.badge>
                            <x-ui.badge color="gray">Type: {{ str_replace('_', ' ', $question->type) }}</x-ui.badge>
                            <x-ui.badge color="blue">Marks: {{ $question->marks }}</x-ui.badge>
                            @if ($question->time_limit_s)
                                <x-ui.badge color="amber">Time Limit: {{ $question->time_limit_s }}s</x-ui.badge>
                            @endif
                            @if ($question->is_locked)
                                <x-ui.badge color="gray"><x-icon name="lock" /> Locked Position</x-ui.badge>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <x-ui.button href="{{ route('instructor.questions.edit', ['exam' => $exam->exam_id, 'question' => $question->question_id]) }}" variant="secondary" size="sm">
                                <x-icon name="pencil" /> Edit Question
                            </x-ui.button>
                            <form action="{{ route('instructor.questions.destroy', ['exam' => $exam->exam_id, 'question' => $question->question_id]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this question? This will reorder the remaining questions.');">
                                @csrf
                                @method('DELETE')
                                <x-ui.button type="submit" variant="danger-soft" size="sm">
                                    <x-icon name="trash" /> Delete
                                </x-ui.button>
                            </form>
                        </div>
                    </div>
                    <div class="p-4 sm:p-5">
                        @if ($question->image_url)
                            <div class="mb-3">
                                <img src="{{ filter_var($question->image_url, FILTER_VALIDATE_URL) ? $question->image_url : asset('storage/' . $question->image_url) }}" alt="Question Image" class="max-h-[250px] max-w-[250px] rounded-lg border border-line">
                            </div>
                        @endif

                        <p class="text-base font-semibold text-ink">{{ $question->question_text }}</p>

                        @if (in_array($question->type, ['multiple_choice', 'true_false', 'question_answer']))
                            <div class="mt-4 rounded-xl bg-subtle/60 p-4">
                                <h5 class="mb-3 text-sm font-semibold text-ink">Options / Acceptable Answers:</h5>
                                <ul class="space-y-2">
                                    @foreach ($question->options as $option)
                                        <li class="flex items-center justify-between gap-3 rounded-lg border border-line bg-white px-3 py-2 text-sm">
                                            <div class="flex items-center gap-2">
                                                <x-ui.badge color="gray">Index: {{ $option->order_index }}</x-ui.badge>
                                                <span class="text-ink">{{ $option->option_text }}</span>
                                            </div>
                                            @if ($option->is_correct)
                                                <x-ui.badge color="green"><x-icon name="check" /> Correct Answer</x-ui.badge>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </x-ui.card>
            @endforeach
        </div>
    @endif
@endsection

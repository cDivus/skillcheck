@extends('layouts.app')

@section('title', 'Edit Exam')

@section('content')
    <x-ui.page-header title="Edit Exam: {{ $exam->title }}" subtitle="Update the configuration for this exam">
        <x-slot:actions>
            <x-ui.button href="{{ route('instructor.exams.index') }}" variant="secondary">
                <x-icon name="arrow-left" /> Back to Exams List
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mx-auto max-w-3xl space-y-6">
        <x-ui.card>
            <form action="{{ route('instructor.exams.update', $exam) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <x-ui.input label="Exam Title" name="title" :value="old('title', $exam->title)" required placeholder="e.g. Midterm Exam" />

                <x-ui.textarea label="Description (Optional)" name="description" rows="4" placeholder="Enter instructions or description...">{{ old('description', $exam->description) }}</x-ui.textarea>

                <x-ui.input type="number" label="Duration (seconds)" name="duration_s" :value="old('duration_s', $exam->duration_s)" required min="1"
                    hint="e.g., 3600 for 1 hour, 1800 for 30 minutes." />

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-ui.input type="datetime-local" label="Start Time (Optional)" name="start_time" :value="old('start_time', $exam->start_time ? date('Y-m-d\TH:i', strtotime($exam->start_time)) : '')" />
                    <x-ui.input type="datetime-local" label="End Time (Optional)" name="end_time" :value="old('end_time', $exam->end_time ? date('Y-m-d\TH:i', strtotime($exam->end_time)) : '')" />
                </div>

                <label class="flex items-start gap-3 rounded-lg border border-line bg-subtle/50 p-3 cursor-pointer">
                    <input class="mt-0.5 h-4 w-4 rounded border-line-strong text-brand-700 focus:ring-brand-500/25" type="checkbox" id="randomize_questions" name="randomize_questions" value="1" {{ old('randomize_questions', $exam->randomize_questions) ? 'checked' : '' }}>
                    <span>
                        <span class="block text-sm font-medium text-ink">Randomize Questions</span>
                        <span class="block text-xs text-muted">If checked, the system will shuffle the order of questions for each student attempt.</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 rounded-lg border border-line bg-subtle/50 p-3 cursor-pointer">
                    <input class="mt-0.5 h-4 w-4 rounded border-line-strong text-brand-700 focus:ring-brand-500/25" type="checkbox" id="viewable_responses" name="viewable_responses" value="1" {{ old('viewable_responses', $exam->viewable_responses) ? 'checked' : '' }}>
                    <span>
                        <span class="block text-sm font-medium text-ink">Allow Students to View Responses</span>
                        <span class="block text-xs text-muted">If checked, students will be able to review their answers and scores after completing the exam.</span>
                    </span>
                </label>

                <div class="flex justify-end pt-2">
                    <x-ui.button type="submit" variant="primary" size="lg">
                        <x-icon name="check" /> Update Exam
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        {{-- Danger Zone --}}
        <x-ui.card class="border-red-200">
            <div class="flex items-center gap-2 text-red-600">
                <x-icon name="alert-triangle" class="w-5 h-5" />
                <h2 class="text-base font-semibold">Danger Zone</h2>
            </div>
            <p class="mt-2 text-sm text-muted">Deleting this exam will permanently remove all associated questions, answers, and student attempts. This action cannot be undone.</p>
            <form action="{{ route('instructor.exams.destroy', ['exam' => $exam->exam_id]) }}" method="POST" class="mt-4" onsubmit="return confirm('Are you sure you want to delete this exam? This will permanently remove all associated questions, answers, and student attempts.');">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="danger">
                    <x-icon name="trash" /> Delete Exam
                </x-ui.button>
            </form>
        </x-ui.card>
    </div>
@endsection

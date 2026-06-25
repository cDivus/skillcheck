@extends('layouts.app')

@section('title', 'Create Exam')

@section('content')
    <x-ui.page-header title="Create New Exam" subtitle="Set up a new exam for your students">
        <x-slot:actions>
            <x-ui.button href="{{ route('instructor.exams.index') }}" variant="secondary">
                <x-icon name="arrow-left" /> Back to Exams List
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mx-auto max-w-3xl">
        <x-ui.card>
            <form action="{{ route('instructor.exams.store') }}" method="POST" class="space-y-5">
                @csrf

                <x-ui.input label="Exam Title" name="title" :value="old('title')" required placeholder="e.g. Midterm Exam" />

                <x-ui.textarea label="Description (Optional)" name="description" rows="4" placeholder="Enter instructions or description...">{{ old('description') }}</x-ui.textarea>

                <div class="space-y-2">
                    <span class="block text-sm font-medium text-ink">Timer Type</span>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="timer_type" value="whole_exam" {{ old('timer_type', 'whole_exam') === 'whole_exam' ? 'checked' : '' }} class="accent-brand-700" onclick="toggleTimerFields()">
                            <span class="text-sm text-ink">Whole Exam</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="timer_type" value="per_question" {{ old('timer_type') === 'per_question' ? 'checked' : '' }} class="accent-brand-700" onclick="toggleTimerFields()">
                            <span class="text-sm text-ink">Per Question</span>
                        </label>
                    </div>
                </div>

                <div id="duration-container">
                    <x-ui.input type="number" label="Duration (minutes)" name="duration_m" id="duration_m" :value="old('duration_m', 60)" required min="1"
                        hint="e.g., 60 for 1 hour, 30 for 30 minutes." />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-ui.input type="datetime-local" label="Start Time (Optional)" name="start_time" :value="old('start_time')" />
                    <x-ui.input type="datetime-local" label="End Time (Optional)" name="end_time" :value="old('end_time')" />
                </div>

                <label class="flex items-start gap-3 rounded-lg border border-line bg-subtle/50 p-3 cursor-pointer">
                    <input class="mt-0.5 h-4 w-4 rounded border-line-strong text-brand-700 focus:ring-brand-500/25" type="checkbox" id="randomize_questions" name="randomize_questions" value="1" {{ old('randomize_questions') ? 'checked' : '' }}>
                    <span>
                        <span class="block text-sm font-medium text-ink">Randomize Questions</span>
                        <span class="block text-xs text-muted">If checked, the system will shuffle the order of questions for each student attempt.</span>
                    </span>
                </label>

                <label class="flex items-start gap-3 rounded-lg border border-line bg-subtle/50 p-3 cursor-pointer">
                    <input class="mt-0.5 h-4 w-4 rounded border-line-strong text-brand-700 focus:ring-brand-500/25" type="checkbox" id="viewable_responses" name="viewable_responses" value="1" {{ old('viewable_responses') ? 'checked' : '' }}>
                    <span>
                        <span class="block text-sm font-medium text-ink">Allow Students to View Responses</span>
                        <span class="block text-xs text-muted">If checked, students will be able to review their answers and scores after completing the exam.</span>
                    </span>
                </label>

                <div class="flex justify-end pt-2">
                    <x-ui.button type="submit" variant="primary" size="lg">
                        <x-icon name="check" /> Create Exam
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
    <script>
        function toggleTimerFields() {
            const timerType = document.querySelector('input[name="timer_type"]:checked').value;
            const durationContainer = document.getElementById('duration-container');
            const durationInput = document.getElementById('duration_m');

            if (timerType === 'per_question') {
                durationContainer.style.display = 'none';
                durationInput.removeAttribute('required');
            } else {
                durationContainer.style.display = 'block';
                durationInput.setAttribute('required', 'required');
            }
        }
        
        // Run on initial page load
        document.addEventListener('DOMContentLoaded', toggleTimerFields);
    </script>
@endsection

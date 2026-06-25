@extends('layouts.app')

@section('title', 'Edit Question')

@section('content')
    <x-ui.page-header title="Edit Question" subtitle="Exam: {{ $exam->title }}">
        <x-slot:actions>
            <x-ui.button href="{{ route('instructor.exams.show', ['exam' => $exam->exam_id]) }}" variant="secondary">
                <x-icon name="arrow-left" /> Back to Exam Details
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mx-auto max-w-4xl">
        <x-ui.card>
            <form action="{{ route('instructor.questions.update', ['exam' => $exam->exam_id, 'question' => $question->question_id]) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <x-ui.textarea label="Question Text" name="question_text" rows="4" required placeholder="Type the question content here...">{{ old('question_text', $question->question_text) }}</x-ui.textarea>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="sm:col-span-2">
                        <x-ui.select label="Question Type" name="type" id="type" required>
                            <option value="multiple_choice" {{ old('type', $question->type) == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="true_false" {{ old('type', $question->type) == 'true_false' ? 'selected' : '' }}>True / False</option>
                            <option value="question_answer" {{ old('type', $question->type) == 'question_answer' ? 'selected' : '' }}>Short Answer (QA)</option>
                            <option value="essay" {{ old('type', $question->type) == 'essay' ? 'selected' : '' }}>Essay (Manual Grade)</option>
                        </x-ui.select>
                    </div>

                    <x-ui.input type="number" label="Marks" name="marks" id="marks" step="0.01" :value="old('marks', $question->marks)" required min="0" />

                    <x-ui.input type="number" label="Time Limit (Sec)" name="time_limit_s" id="time_limit_s" :value="old('time_limit_s', $question->time_limit_s)" min="1"
                        placeholder="{{ $exam->timer_type === 'per_question' ? 'Required' : 'Optional' }}"
                        :required="$exam->timer_type === 'per_question'" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink mb-1.5">Question Image (Optional)</label>
                    @if ($question->image_url)
                        <div class="mb-3 rounded-xl bg-subtle/60 p-4">
                            <div class="flex items-center gap-4">
                                <img src="{{ filter_var($question->image_url, FILTER_VALIDATE_URL) ? $question->image_url : asset('storage/' . $question->image_url) }}" alt="Current Question Image" class="max-h-[150px] max-w-[150px] rounded-lg border border-line">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input class="h-4 w-4 rounded border-line-strong text-red-600 focus:ring-red-500/25" type="checkbox" name="remove_image" id="remove_image" value="1">
                                    <span class="text-sm font-bold text-red-600">Remove current image</span>
                                </label>
                            </div>
                        </div>
                    @endif
                    <div class="rounded-xl bg-subtle/60 p-4">
                        <div class="mb-3">
                            <label for="image" class="block text-xs font-semibold text-muted mb-1.5">Upload Local Image</label>
                            <input type="file" id="image" name="image" accept="image/*"
                                class="w-full rounded-lg border border-line-strong bg-white px-3 py-2 text-sm text-ink shadow-xs outline-none file:mr-3 file:rounded-md file:border-0 file:bg-subtle file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-ink focus:ring-2 focus:ring-brand-500/25">
                        </div>
                        <div class="my-2 text-center text-xs font-bold text-muted">— OR —</div>
                        <div>
                            <label for="image_url" class="block text-xs font-semibold text-muted mb-1.5">Image URL (Web Link)</label>
                            <input type="url" id="image_url" name="image_url" placeholder="https://example.com/image.jpg" value="{{ old('image_url', filter_var($question->image_url, FILTER_VALIDATE_URL) ? $question->image_url : '') }}"
                                class="w-full rounded-lg border border-line-strong bg-white px-3 py-2 text-sm text-ink placeholder:text-faint shadow-xs outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/25">
                        </div>
                    </div>
                </div>

                <label class="flex items-start gap-3 rounded-lg border border-line bg-subtle/50 p-3 cursor-pointer">
                    <input class="mt-0.5 h-4 w-4 rounded border-line-strong text-brand-700 focus:ring-brand-500/25" type="checkbox" id="is_locked" name="is_locked" value="1" {{ old('is_locked', $question->is_locked) ? 'checked' : '' }}>
                    <span>
                        <span class="block text-sm font-medium text-ink">Lock Question Position</span>
                        <span class="block text-xs text-muted">If checked, this question will always remain at its current index position, even when question randomization is enabled.</span>
                    </span>
                </label>

                <!-- Dynamic Options Section -->
                <div id="options_section" class="rounded-xl border border-dashed border-line-strong bg-subtle/60 p-4" style="display: none;">
                    <h5 class="text-sm font-semibold text-ink" id="options_title">Options</h5>
                    <p class="mt-1 mb-3 text-xs text-muted" id="options_help">Edit option texts. Clear an option text or click Remove to delete it.</p>

                    <div id="options_container" class="mb-3 space-y-2">
                        @foreach ($question->options as $index => $option)
                            <div class="option-row flex flex-col gap-2 sm:flex-row sm:items-center">
                                <div class="flex-1">
                                    <div class="flex items-stretch overflow-hidden rounded-lg border border-line-strong bg-white shadow-xs focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/25">
                                        <span class="flex items-center bg-subtle px-3 text-xs font-medium text-muted">Option</span>
                                        <input type="hidden" name="options[{{ $index }}][option_id]" value="{{ $option->option_id }}">
                                        <input type="text" name="options[{{ $index }}][option_text]" class="flex-1 border-0 bg-white px-3 py-2 text-sm text-ink placeholder:text-faint outline-none focus:ring-0" value="{{ old('options.'.$index.'.option_text', $option->option_text) }}" required placeholder="Type option text...">
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 sm:shrink-0">
                                    <label class="correct-checkbox-wrapper flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox" name="correct_options[]" class="h-4 w-4 rounded border-line-strong text-brand-700 focus:ring-brand-500/25" value="{{ $index }}" id="chk-{{ $index }}" {{ (is_array(old('correct_options')) && in_array($index, old('correct_options'))) || (!is_array(old('correct_options')) && $option->is_correct) ? 'checked' : '' }}>
                                        <span class="text-xs font-bold text-green-700">Correct</span>
                                    </label>
                                    <button type="button" class="remove-option-btn rounded-md border border-red-200 px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50">Remove</button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <x-ui.button type="button" id="add_option_btn" variant="secondary" size="sm">+ Add Option</x-ui.button>
                </div>

                <!-- True/False Configuration Section -->
                @php
                    $trueIsCorrect = $question->options->where('option_text', 'True')->first()?->is_correct ?? false;
                    $falseIsCorrect = $question->options->where('option_text', 'False')->first()?->is_correct ?? false;
                    $tfValue = $falseIsCorrect ? 'False' : 'True';
                @endphp
                <div id="true_false_section" class="rounded-xl bg-subtle/60 p-4" style="display: none;">
                    <h5 class="text-sm font-semibold text-ink">True / False Configuration</h5>
                    <label class="mt-2 block text-sm font-medium text-ink">Correct Answer:</label>
                    <div class="mt-2 flex items-center gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input class="h-4 w-4 border-line-strong text-brand-700 focus:ring-brand-500/25" type="radio" name="tf_correct" id="tf_true" value="True" {{ old('tf_correct', $tfValue) === 'True' ? 'checked' : '' }}>
                            <span class="text-sm font-bold text-green-700">True</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input class="h-4 w-4 border-line-strong text-brand-700 focus:ring-brand-500/25" type="radio" name="tf_correct" id="tf_false" value="False" {{ old('tf_correct', $tfValue) === 'False' ? 'checked' : '' }}>
                            <span class="text-sm font-bold text-red-600">False</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <x-ui.button type="submit" variant="primary" size="lg">
                        <x-icon name="check" /> Update Question
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const optionsSection = document.getElementById('options_section');
        const optionsTitle = document.getElementById('options_title');
        const optionsHelp = document.getElementById('options_help');
        const optionsContainer = document.getElementById('options_container');
        const tfSection = document.getElementById('true_false_section');
        const addOptionBtn = document.getElementById('add_option_btn');

        let optionIndex = {{ $question->options->count() }};
        let currentType = '{{ $question->type }}';

        function addOptionRow() {
            const row = document.createElement('div');
            row.className = 'option-row flex flex-col gap-2 sm:flex-row sm:items-center';
            row.innerHTML = `
                <div class="flex-1">
                    <div class="flex items-stretch overflow-hidden rounded-lg border border-line-strong bg-white shadow-xs focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/25">
                        <span class="flex items-center bg-subtle px-3 text-xs font-medium text-muted">Option</span>
                        <input type="text" name="options[${optionIndex}][option_text]" class="flex-1 border-0 bg-white px-3 py-2 text-sm text-ink placeholder:text-faint outline-none focus:ring-0" required placeholder="Type option text...">
                    </div>
                </div>
                <div class="flex items-center gap-3 sm:shrink-0">
                    <label class="correct-checkbox-wrapper flex items-center gap-1.5 cursor-pointer">
                        <input type="checkbox" name="correct_options[]" class="h-4 w-4 rounded border-line-strong text-brand-700 focus:ring-brand-500/25" value="${optionIndex}" id="chk-${optionIndex}">
                        <span class="text-xs font-bold text-green-700">Correct</span>
                    </label>
                    <button type="button" class="remove-option-btn rounded-md border border-red-200 px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50">Remove</button>
                </div>
            `;
            optionsContainer.appendChild(row);

            row.querySelector('.remove-option-btn').addEventListener('click', function() {
                row.remove();
            });

            const wrapper = row.querySelector('.correct-checkbox-wrapper');
            if (typeSelect.value === 'question_answer') {
                wrapper.style.display = 'none';
            } else {
                wrapper.style.display = 'block';
            }

            optionIndex++;
        }

        // Bind click event to existing remove buttons
        document.querySelectorAll('.remove-option-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                btn.closest('.option-row').remove();
            });
        });

        function toggleOptionsSection() {
            const selectedType = typeSelect.value;

            if (selectedType === 'multiple_choice' || selectedType === 'question_answer') {
                optionsSection.style.display = 'block';
                tfSection.style.display = 'none';

                if (selectedType === 'multiple_choice') {
                    optionsTitle.textContent = 'Multiple Choice Options';
                    optionsHelp.textContent = 'Edit option texts. Clear an option text or click Remove to delete it. Check the box to mark correctness.';
                    document.querySelectorAll('.correct-checkbox-wrapper').forEach(w => w.style.display = 'block');
                } else {
                    optionsTitle.textContent = 'Correct Answers (Acceptable synonyms/values)';
                    optionsHelp.textContent = 'Edit acceptable correct answer texts. Clear an option text or click Remove to delete it. All listed here are correct.';
                    document.querySelectorAll('.correct-checkbox-wrapper').forEach(w => w.style.display = 'none');
                }

                const currentRows = optionsContainer.querySelectorAll('.option-row');
                if (currentRows.length === 0) {
                    const initCount = (selectedType === 'multiple_choice') ? 4 : 1;
                    for (let i = 0; i < initCount; i++) {
                        addOptionRow();
                    }
                }
                currentType = selectedType;
            } else {
                optionsSection.style.display = 'none';
                if (selectedType === 'true_false') {
                    tfSection.style.display = 'block';
                } else {
                    tfSection.style.display = 'none';
                }
                currentType = selectedType;
            }
        }

        addOptionBtn.addEventListener('click', function() {
            addOptionRow();
        });

        typeSelect.addEventListener('change', toggleOptionsSection);
        toggleOptionsSection(); // Run on page load
    });
</script>
@endsection

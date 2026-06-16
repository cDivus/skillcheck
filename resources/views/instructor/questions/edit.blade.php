@extends('layouts.app')

@section('content')
    <h1>Edit Question in Exam: {{ $exam->title }}</h1>
    
    <div>
        <a href="{{ route('instructor.exams.index') }}">[ Back to Exams List ]</a>
    </div>

    <hr>

    @if ($errors->any())
        <div style="color: red; margin-bottom: 15px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('instructor.questions.update', ['exam' => $exam->exam_id, 'question' => $question->question_id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 10px;">
            <label for="question_text">Question Text (Required):</label><br>
            <textarea id="question_text" name="question_text" rows="5" cols="60" required>{{ old('question_text', $question->question_text) }}</textarea>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="type">Question Type (Required):</label><br>
            <select id="type" name="type" required>
                <option value="multiple_choice" {{ old('type', $question->type) == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                <option value="true_false" {{ old('type', $question->type) == 'true_false' ? 'selected' : '' }}>True / False</option>
                <option value="question_answer" {{ old('type', $question->type) == 'question_answer' ? 'selected' : '' }}>Question & Answer</option>
                <option value="essay" {{ old('type', $question->type) == 'essay' ? 'selected' : '' }}>Essay</option>
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="marks">Marks (Required):</label><br>
            <input type="number" id="marks" name="marks" step="0.01" value="{{ old('marks', $question->marks) }}" required min="0">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="time_limit_s">Time Limit in Seconds (Optional):</label><br>
            <input type="number" id="time_limit_s" name="time_limit_s" value="{{ old('time_limit_s', $question->time_limit_s) }}" min="1">
            <span style="font-size: 0.9em; color: gray;">(Question specific timeout)</span>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="image_url">Image URL (Optional):</label><br>
            <input type="text" id="image_url" name="image_url" value="{{ old('image_url', $question->image_url) }}">
        </div>

        <!-- 1. Options Section -->
        <div id="options_section" style="margin-bottom: 15px; border: 1px dashed gray; padding: 10px; display: none;">
            <h3 id="options_title">Options</h3>
            <p id="options_help" style="font-size: 0.9em; color: gray;">Edit option texts. Clear an option text or click Remove to delete it.</p>
            
            <div id="options_container" style="margin-bottom: 10px;">
                @foreach ($question->options as $index => $option)
                    <div class="option-row" style="margin-bottom: 8px;">
                        <label>Option:</label><br>
                        <input type="hidden" name="options[{{ $index }}][option_id]" value="{{ $option->option_id }}">
                        <input type="text" name="options[{{ $index }}][option_text]" style="width: 300px;" value="{{ old('options.'.$index.'.option_text', $option->option_text) }}">
                        <span class="correct-checkbox-wrapper">
                            <label>
                                <input type="checkbox" name="correct_options[]" value="{{ $index }}" {{ (is_array(old('correct_options')) && in_array($index, old('correct_options'))) || (!is_array(old('correct_options')) && $option->is_correct) ? 'checked' : '' }}>
                                Correct
                            </label>
                        </span>
                        <button type="button" class="remove-option-btn" style="margin-left: 10px;">Remove</button>
                    </div>
                @endforeach
            </div>

            <div>
                <button type="button" id="add_option_btn">[ + Add Option ]</button>
            </div>
        </div>

        <!-- 2. True / False Section -->
        @php
            $trueIsCorrect = $question->options->where('option_text', 'True')->first()?->is_correct ?? false;
            $falseIsCorrect = $question->options->where('option_text', 'False')->first()?->is_correct ?? false;
            $tfValue = $falseIsCorrect ? 'False' : 'True';
        @endphp
        <div id="true_false_section" style="margin-bottom: 15px; border: 1px dashed gray; padding: 10px; display: none;">
            <h3>True / False Configuration</h3>
            <label>Correct Answer:</label><br>
            <label>
                <input type="radio" name="tf_correct" value="True" {{ old('tf_correct', $tfValue) === 'True' ? 'checked' : '' }}>
                True
            </label>
            <label style="margin-left: 15px;">
                <input type="radio" name="tf_correct" value="False" {{ old('tf_correct', $tfValue) === 'False' ? 'checked' : '' }}>
                False
            </label>
        </div>

        <div style="margin-top: 15px;">
            <button type="submit">Update Question</button>
        </div>
    </form>

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
                row.className = 'option-row';
                row.style.marginBottom = '8px';
                row.innerHTML = `
                    <label>Option:</label><br>
                    <input type="text" name="options[${optionIndex}][option_text]" style="width: 300px;">
                    <span class="correct-checkbox-wrapper">
                        <label>
                            <input type="checkbox" name="correct_options[]" value="${optionIndex}">
                            Correct
                        </label>
                    </span>
                    <button type="button" class="remove-option-btn" style="margin-left: 10px;">Remove</button>
                `;
                optionsContainer.appendChild(row);

                row.querySelector('.remove-option-btn').addEventListener('click', function() {
                    row.remove();
                });

                const wrapper = row.querySelector('.correct-checkbox-wrapper');
                if (typeSelect.value === 'question_answer') {
                    wrapper.style.display = 'none';
                } else {
                    wrapper.style.display = 'inline';
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
                        document.querySelectorAll('.correct-checkbox-wrapper').forEach(w => w.style.display = 'inline');
                    } else {
                        optionsTitle.textContent = 'Correct Answers (Acceptable synonyms/values)';
                        optionsHelp.textContent = 'Edit acceptable correct answer texts. Clear an option text or click Remove to delete it. All listed here are correct.';
                        document.querySelectorAll('.correct-checkbox-wrapper').forEach(w => w.style.display = 'none');
                    }

                    // If type changed from non-option to option, or if the options list is empty, initialize it
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

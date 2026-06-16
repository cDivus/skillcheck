@extends('layouts.app')

@section('content')
    <h1>Add Question to Exam: {{ $exam->title }}</h1>
    
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

    <form action="{{ route('instructor.questions.store', ['exam' => $exam->exam_id]) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div style="margin-bottom: 10px;">
            <label for="question_text">Question Text (Required):</label><br>
            <textarea id="question_text" name="question_text" rows="5" cols="60" required>{{ old('question_text') }}</textarea>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="type">Question Type (Required):</label><br>
            <select id="type" name="type" required>
                <option value="">-- Select Type --</option>
                <option value="multiple_choice" {{ old('type') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                <option value="true_false" {{ old('type') == 'true_false' ? 'selected' : '' }}>True / False</option>
                <option value="question_answer" {{ old('type') == 'question_answer' ? 'selected' : '' }}>Question & Answer</option>
                <option value="essay" {{ old('type') == 'essay' ? 'selected' : '' }}>Essay</option>
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="marks">Marks (Required):</label><br>
            <input type="number" id="marks" name="marks" step="0.01" value="{{ old('marks', 1.00) }}" required min="0">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="time_limit_s">Time Limit in Seconds (Optional):</label><br>
            <input type="number" id="time_limit_s" name="time_limit_s" value="{{ old('time_limit_s') }}" min="1">
            <span style="font-size: 0.9em; color: gray;">(Question specific timeout)</span>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="image">Question Image (Optional):</label><br>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <div id="options_section" style="margin-bottom: 15px; border: 1px dashed gray; padding: 10px; display: none;">
            <h3 id="options_title">Options</h3>
            <p id="options_help" style="font-size: 0.9em; color: gray;">Fill in the option text. For multiple choice, check the correct option(s).</p>
            
            <div id="options_container" style="margin-bottom: 10px;">
                <!-- Dynamically populated via JS -->
            </div>

            <div>
                <button type="button" id="add_option_btn">[ + Add Option ]</button>
            </div>
        </div>

        <div id="true_false_section" style="margin-bottom: 15px; border: 1px dashed gray; padding: 10px; display: none;">
            <h3>True / False Configuration</h3>
            <label>Correct Answer:</label><br>
            <label>
                <input type="radio" name="tf_correct" value="True" {{ old('tf_correct', 'True') === 'True' ? 'checked' : '' }}>
                True
            </label>
            <label style="margin-left: 15px;">
                <input type="radio" name="tf_correct" value="False" {{ old('tf_correct') === 'False' ? 'checked' : '' }}>
                False
            </label>
        </div>

        <div style="margin-top: 15px;">
            <button type="submit">Save Question</button>
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

            let optionIndex = 0;
            let currentType = '';

            function addOptionRow(val = '') {
                const row = document.createElement('div');
                row.className = 'option-row';
                row.style.marginBottom = '8px';
                row.innerHTML = `
                    <label>Option:</label><br>
                    <input type="text" name="options[${optionIndex}]" style="width: 300px;" value="${val}">
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

            function toggleOptionsSection() {
                const selectedType = typeSelect.value;
                
                // If the general structure doesn't change, we just toggle checkbox visibility
                if (selectedType === 'multiple_choice' || selectedType === 'question_answer') {
                    optionsSection.style.display = 'block';
                    tfSection.style.display = 'none';

                    if (selectedType === 'multiple_choice') {
                        optionsTitle.textContent = 'Multiple Choice Options';
                        optionsHelp.textContent = 'Fill in the option text and check the box if it is a correct answer.';
                        document.querySelectorAll('.correct-checkbox-wrapper').forEach(w => w.style.display = 'inline');
                    } else {
                        optionsTitle.textContent = 'Correct Answers (Acceptable synonyms/values)';
                        optionsHelp.textContent = 'Fill in the correct textual answer(s) that will be accepted (all listed here are correct).';
                        document.querySelectorAll('.correct-checkbox-wrapper').forEach(w => w.style.display = 'none');
                    }

                    // If type changed or it's first load, initialize options
                    if (currentType !== 'multiple_choice' && currentType !== 'question_answer') {
                        optionsContainer.innerHTML = '';
                        optionIndex = 0;
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

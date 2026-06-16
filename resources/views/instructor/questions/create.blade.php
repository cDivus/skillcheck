@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Add Question</h1>
                <p class="text-muted mb-0">Exam: <strong>{{ $exam->title }}</strong></p>
            </div>
            <a href="{{ route('instructor.exams.show', ['exam' => $exam->exam_id]) }}" class="btn btn-outline-secondary">&larr; Back to Exam Details</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('instructor.questions.store', ['exam' => $exam->exam_id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="question_text" class="form-label fw-bold">Question Text</label>
                        <textarea id="question_text" name="question_text" class="form-control" rows="4" required placeholder="Type the question content here...">{{ old('question_text') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label fw-bold">Question Type</label>
                            <select id="type" name="type" class="form-select" required>
                                <option value="">-- Select Type --</option>
                                <option value="multiple_choice" {{ old('type') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                <option value="true_false" {{ old('type') == 'true_false' ? 'selected' : '' }}>True / False</option>
                                <option value="question_answer" {{ old('type') == 'question_answer' ? 'selected' : '' }}>Short Answer (QA)</option>
                                <option value="essay" {{ old('type') == 'essay' ? 'selected' : '' }}>Essay (Manual Grade)</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="marks" class="form-label fw-bold">Marks</label>
                            <input type="number" id="marks" name="marks" class="form-control" step="0.01" value="{{ old('marks', 1.00) }}" required min="0">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="time_limit_s" class="form-label fw-bold">Time Limit (Sec)</label>
                            <input type="number" id="time_limit_s" name="time_limit_s" class="form-control" value="{{ old('time_limit_s') }}" min="1" placeholder="Optional">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label fw-bold">Question Image (Optional)</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    </div>

                    <!-- Dynamic Options Section -->
                    <div id="options_section" class="card bg-light mb-4 border-dashed" style="display: none;">
                        <div class="card-body">
                            <h5 class="card-title text-secondary" id="options_title">Options</h5>
                            <p class="card-text text-muted small mb-3" id="options_help">Fill in the option text.</p>
                            
                            <div id="options_container" class="mb-3">
                                <!-- Dynamically populated via JS -->
                            </div>

                            <button type="button" id="add_option_btn" class="btn btn-outline-primary btn-sm">+ Add Option</button>
                        </div>
                    </div>

                    <!-- True/False Configuration Section -->
                    <div id="true_false_section" class="card bg-light mb-4" style="display: none;">
                        <div class="card-body">
                            <h5 class="card-title text-secondary">True / False Configuration</h5>
                            <label class="form-label fw-bold d-block">Correct Answer:</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tf_correct" id="tf_true" value="True" {{ old('tf_correct', 'True') === 'True' ? 'checked' : '' }}>
                                <label class="form-check-label text-success fw-bold" for="tf_true">True</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tf_correct" id="tf_false" value="False" {{ old('tf_correct') === 'False' ? 'checked' : '' }}>
                                <label class="form-check-label text-danger fw-bold" for="tf_false">False</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Save Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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

        let optionIndex = 0;
        let currentType = '';

        function addOptionRow(val = '') {
            const row = document.createElement('div');
            row.className = 'option-row row g-3 mb-2 align-items-center';
            row.innerHTML = `
                <div class="col-sm-8 col-md-9">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-secondary">Option</span>
                        <input type="text" name="options[${optionIndex}]" class="form-control" value="${val}" required placeholder="Type option text...">
                    </div>
                </div>
                <div class="col-sm-4 col-md-3 d-flex align-items-center gap-2">
                    <div class="correct-checkbox-wrapper form-check mb-0">
                        <input type="checkbox" name="correct_options[]" class="form-check-input" value="${optionIndex}" id="chk-${optionIndex}">
                        <label class="form-check-label fw-bold text-success small" for="chk-${optionIndex}">Correct</label>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-option-btn py-0 px-2" style="font-size: 0.8rem;">Remove</button>
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

        function toggleOptionsSection() {
            const selectedType = typeSelect.value;
            
            if (selectedType === 'multiple_choice' || selectedType === 'question_answer') {
                optionsSection.style.display = 'block';
                tfSection.style.display = 'none';

                if (selectedType === 'multiple_choice') {
                    optionsTitle.textContent = 'Multiple Choice Options';
                    optionsHelp.textContent = 'Fill in the option text and check the box if it is a correct answer.';
                    document.querySelectorAll('.correct-checkbox-wrapper').forEach(w => w.style.display = 'block');
                } else {
                    optionsTitle.textContent = 'Correct Answers (Acceptable synonyms/values)';
                    optionsHelp.textContent = 'Fill in the correct textual answer(s) that will be accepted (all listed here are correct).';
                    document.querySelectorAll('.correct-checkbox-wrapper').forEach(w => w.style.display = 'none');
                }

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

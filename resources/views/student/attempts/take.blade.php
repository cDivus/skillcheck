@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <!-- Main Question Area -->
        <div class="col-lg-8 col-md-10 px-4">
            
            @if(session('error'))
                <div class="alert alert-danger mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h1 class="h2 mb-1">{{ $exam->title }}</h1>
                    @if($exam->description)
                        <p class="text-muted mb-0">{{ $exam->description }}</p>
                    @endif
                </div>
                <div class="text-end">
                    <span class="text-secondary fw-semibold">Time Remaining:</span>
                    <span id="timer-display" class="fs-4 font-monospace text-danger fw-bold ms-1">--:--:--</span>
                </div>
            </div>

            <!-- Single Question Card -->
            <div class="card mb-4 shadow-sm question-card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-secondary">Question {{ $page }}</h5>
                    <div class="d-flex align-items-center gap-2">
                        @if($questionTimeLeft !== null)
                            <span class="badge bg-warning text-dark">
                                Question Timer: <span id="question-timer-display" class="font-monospace fw-bold">--:--</span>
                            </span>
                        @endif
                        <span class="badge bg-info text-white">{{ number_format($question->marks, 1) }} Marks</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Question Text -->
                    <p class="fs-5 mb-3">{!! nl2br(e($question->question_text)) !!}</p>

                    <!-- Optional Question Image -->
                    @if($question->image_url)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $question->image_url) }}" alt="Question diagram" class="img-fluid border rounded" style="max-height: 300px;">
                        </div>
                    @endif

                    <hr class="my-3">

                    <!-- Question Form -->
                    <form action="{{ route('student.exams.attempt.answers.store', [$exam->exam_id, $attempt->attempt_id]) }}" method="POST" id="question-form">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $question->question_id }}">
                        <input type="hidden" name="next_page" value="{{ $page + 1 }}">

                        @if($question->type === 'multiple_choice')
                            @foreach($question->options as $option)
                                <div class="form-check my-2">
                                    <input class="form-check-input" type="radio" name="selected_option" 
                                        id="opt-{{ $option->option_id }}" value="{{ $option->option_id }}"
                                        {{ (isset($answers[$question->question_id]) && $answers[$question->question_id]->selected_option === $option->option_id) ? 'checked' : '' }}>
                                    <label class="form-check-label py-1 w-100" for="opt-{{ $option->option_id }}">
                                        {{ $option->option_text }}
                                    </label>
                                </div>
                            @endforeach

                        @elseif($question->type === 'true_false')
                            @foreach($question->options as $option)
                                <div class="form-check my-2">
                                    <input class="form-check-input" type="radio" name="selected_option" 
                                        id="opt-{{ $option->option_id }}" value="{{ $option->option_id }}"
                                        {{ (isset($answers[$question->question_id]) && $answers[$question->question_id]->selected_option === $option->option_id) ? 'checked' : '' }}>
                                    <label class="form-check-label py-1 w-100" for="opt-{{ $option->option_id }}">
                                        {{ $option->option_text }}
                                    </label>
                                </div>
                            @endforeach

                        @elseif($question->type === 'question_answer')
                            <div class="mb-3">
                                <label class="form-label text-muted small">Short Answer Text:</label>
                                <input type="text" name="text_answer" class="form-control" placeholder="Type your answer here..."
                                    value="{{ isset($answers[$question->question_id]) ? $answers[$question->question_id]->text_answer : '' }}">
                            </div>

                        @elseif($question->type === 'essay')
                            <div class="mb-3">
                                <label class="form-label text-muted small">Your Essay Answer:</label>
                                <textarea name="text_answer" class="form-control" rows="6" placeholder="Write your full response here...">{{ isset($answers[$question->question_id]) ? $answers[$question->question_id]->text_answer : '' }}</textarea>
                            </div>
                        @endif

                        <!-- Navigation Controls for Single Question View -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <span class="text-muted small fw-semibold">Question {{ $page }} of {{ $questionsCount }}</span>
                            @if($page === $questionsCount)
                                <button type="submit" name="action" value="submit" class="btn btn-danger next-question-btn">
                                    Submit Exam
                                </button>
                            @else
                                <button type="submit" name="action" value="next" class="btn btn-primary next-question-btn">
                                    Next Question &rarr;
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Live countdown timer logic
    let secondsLeft = parseInt("{{ $timeLeft }}");
    const timerDisplay = document.getElementById('timer-display');
    const form = document.getElementById('question-form');

    function updateCountdown() {
        if (secondsLeft <= 0) {
            timerDisplay.textContent = "00:00:00";
            alert('Time has expired! Your attempt is being submitted automatically.');
            
            // Append action=submit and submit the form
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'submit';
            form.appendChild(actionInput);
            
            form.submit();
            return;
        }

        const hrs = Math.floor(secondsLeft / 3600);
        const mins = Math.floor((secondsLeft % 3600) / 60);
        const secs = secondsLeft % 60;

        timerDisplay.textContent = 
            String(hrs).padStart(2, '0') + ':' + 
            String(mins).padStart(2, '0') + ':' + 
            String(secs).padStart(2, '0');

        secondsLeft--;
    }

    setInterval(updateCountdown, 1000);
    updateCountdown();

    @if($questionTimeLeft !== null)
    // Live countdown timer for the specific question
    let questionSecondsLeft = parseInt("{{ $questionTimeLeft }}");
    const questionTimerDisplay = document.getElementById('question-timer-display');

    function updateQuestionCountdown() {
        if (questionSecondsLeft <= 0) {
            questionTimerDisplay.textContent = "00:00";
            
            // Append action input and submit
            const nextAction = "{{ $page === $questionsCount ? 'submit' : 'next' }}";
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = nextAction;
            form.appendChild(actionInput);
            
            form.submit();
            return;
        }

        const mins = Math.floor(questionSecondsLeft / 60);
        const secs = questionSecondsLeft % 60;

        questionTimerDisplay.textContent = 
            String(mins).padStart(2, '0') + ':' + 
            String(secs).padStart(2, '0');

        questionSecondsLeft--;
    }

    setInterval(updateQuestionCountdown, 1000);
    updateQuestionCountdown();
    @endif

    // Client-side validation: lock next/submit button if unanswered
    const submitBtn = form.querySelector('.next-question-btn');

    function isQuestionAnswered() {
        // Check radio buttons
        const radios = form.querySelectorAll('input[type="radio"]');
        if (radios.length > 0) {
            return form.querySelector('input[type="radio"]:checked') !== null;
        }
        
        // Check text input or textarea
        const textInput = form.querySelector('input[type="text"], textarea');
        if (textInput) {
            return textInput.value.trim() !== '';
        }
        
        return false;
    }

    function updateSubmitButtonState() {
        if (submitBtn) {
            submitBtn.disabled = !isQuestionAnswered();
        }
    }

    // Listen to changes on inputs
    form.querySelectorAll('input, textarea').forEach(input => {
        input.addEventListener('input', updateSubmitButtonState);
        input.addEventListener('change', updateSubmitButtonState);
    });

    // Initialize state
    updateSubmitButtonState();
</script>
@endsection

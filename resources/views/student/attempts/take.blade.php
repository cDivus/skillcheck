@extends('layouts.focus')

@section('title', 'Taking Exam')

@section('topbar')
    <div class="flex items-center gap-2">
        <span class="flex items-center gap-1.5 text-sm font-medium text-brand-700">
            <x-icon name="clock" class="h-4 w-4" /> Time Remaining:
        </span>
        <span id="timer-display" class="font-mono text-lg font-bold text-red-600">--:--:--</span>
    </div>
@endsection

@section('content')
<div class="mx-auto max-w-3xl">

    {{-- Exam heading --}}
    <div class="mb-6 border-b border-line pb-4">
        <h1 class="text-xl font-semibold tracking-tight text-ink sm:text-2xl">{{ $exam->title }}</h1>
        @if($exam->description)
            <p class="mt-1 text-sm text-muted">{{ $exam->description }}</p>
        @endif
    </div>

    {{-- Single Question Card --}}
    <x-ui.card padding="p-0" class="question-card overflow-hidden">
        <div class="flex items-center justify-between gap-2 border-b border-line bg-subtle/60 px-5 py-3">
            <h2 class="text-sm font-semibold text-ink">Question {{ $page }}</h2>
            <div class="flex items-center gap-2">
                @if($questionTimeLeft !== null)
                    <x-ui.badge color="amber">
                        Question Timer: <span id="question-timer-display" class="font-mono font-bold">--:--</span>
                    </x-ui.badge>
                @endif
                <x-ui.badge color="blue">{{ number_format($question->marks, 1) }} Marks</x-ui.badge>
            </div>
        </div>

        <div class="p-5 sm:p-6">
            {{-- Question Text --}}
            <p class="text-base text-ink">{!! nl2br(e($question->question_text)) !!}</p>

            {{-- Optional Question Image --}}
            @if($question->image_url)
                <div class="mt-4">
                    <img src="{{ asset('storage/' . $question->image_url) }}" alt="Question diagram" class="max-h-75 rounded-lg border border-line">
                </div>
            @endif

            <hr class="my-5 border-line">

            {{-- Question Form --}}
            <form action="{{ route('student.exams.attempt.answers.store', [$exam->exam_id, $attempt->attempt_id]) }}" method="POST" id="question-form">
                @csrf
                <input type="hidden" name="question_id" value="{{ $question->question_id }}">
                <input type="hidden" name="next_page" value="{{ $page + 1 }}">

                @if($question->type === 'multiple_choice')
                    <div class="space-y-2">
                        @foreach($question->options as $option)
                            @php $checked = isset($answers[$question->question_id]) && $answers[$question->question_id]->selected_option === $option->option_id; @endphp
                            <label for="opt-{{ $option->option_id }}"
                                class="flex cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 transition-colors {{ $checked ? 'border-brand-500 bg-brand-50' : 'border-line hover:bg-subtle/60' }}">
                                <input class="h-4 w-4 accent-brand-700" type="radio" name="selected_option"
                                    id="opt-{{ $option->option_id }}" value="{{ $option->option_id }}"
                                    {{ $checked ? 'checked' : '' }}>
                                <span class="text-sm text-ink">{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>

                @elseif($question->type === 'true_false')
                    <div class="space-y-2">
                        @foreach($question->options as $option)
                            @php $checked = isset($answers[$question->question_id]) && $answers[$question->question_id]->selected_option === $option->option_id; @endphp
                            <label for="opt-{{ $option->option_id }}"
                                class="flex cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 transition-colors {{ $checked ? 'border-brand-500 bg-brand-50' : 'border-line hover:bg-subtle/60' }}">
                                <input class="h-4 w-4 accent-brand-700" type="radio" name="selected_option"
                                    id="opt-{{ $option->option_id }}" value="{{ $option->option_id }}"
                                    {{ $checked ? 'checked' : '' }}>
                                <span class="text-sm text-ink">{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>

                @elseif($question->type === 'question_answer')
                    <x-ui.input name="text_answer" label="Short Answer Text" placeholder="Type your answer here..."
                        value="{{ isset($answers[$question->question_id]) ? $answers[$question->question_id]->text_answer : '' }}" />

                @elseif($question->type === 'essay')
                    <x-ui.textarea name="text_answer" label="Your Essay Answer" :rows="6" placeholder="Write your full response here...">{{ isset($answers[$question->question_id]) ? $answers[$question->question_id]->text_answer : '' }}</x-ui.textarea>
                @endif

                {{-- Navigation Controls for Single Question View --}}
                <div class="mt-6 flex items-center justify-between border-t border-line pt-4">
                    <span class="text-sm font-medium text-muted">Question {{ $page }} of {{ $questionsCount }}</span>
                    @if($page === $questionsCount)
                        <x-ui.button type="submit" name="action" value="submit" variant="danger" class="next-question-btn">
                            Submit Exam
                        </x-ui.button>
                    @else
                        <x-ui.button type="submit" name="action" value="next" variant="primary" class="next-question-btn">
                            Next Question <x-icon name="arrow-right" />
                        </x-ui.button>
                    @endif
                </div>
            </form>
        </div>
    </x-ui.card>
</div>

<script>
    // Live countdown timer logic
    let secondsLeft = parseInt("{{ $timeLeft }}");
    const timerDisplay = document.getElementById('timer-display');
    const form = document.getElementById('question-form');
    let isSubmittingNext = false;

    // Set submit flag on regular form submission
    form.addEventListener('submit', function() {
        isSubmittingNext = true;
    });

    // Auto-submit via beacon when student navigates away or closes tab/window
    window.addEventListener('pagehide', function (event) {
        if (!isSubmittingNext) {
            const url = "{{ route('student.exams.attempt.submit', [$exam->exam_id, $attempt->attempt_id]) }}";
            const data = new FormData();
            data.append('_token', "{{ csrf_token() }}");
            navigator.sendBeacon(url, data);
        }
    });

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

            isSubmittingNext = true;
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

            isSubmittingNext = true;
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

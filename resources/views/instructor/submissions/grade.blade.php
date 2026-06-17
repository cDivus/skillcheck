@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex gap-2">
                <a href="{{ route('instructor.submissions.index', $attempt->exam_id) }}" class="btn btn-outline-secondary btn-sm">&larr; Back to Submissions</a>
                <form action="{{ route('instructor.attempts.destroy', $attempt->attempt_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this student attempt? This will permanently delete their answers and cannot be undone.');" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete Attempt</button>
                </form>
            </div>
            <div>
                <span class="badge bg-secondary fs-6">Status: <strong>{{ ucfirst($attempt->status) }}</strong></span>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="mb-0">Grading Attempt: {{ $attempt->student->username }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <strong>Exam:</strong> {{ $attempt->exam->title }}<br>
                        <strong>Duration:</strong> {{ round($attempt->exam->duration_s / 60) }} minutes
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <strong>Started At:</strong> {{ $attempt->started_at ? $attempt->started_at->format('Y-m-d H:i:s') : 'N/A' }}<br>
                        <strong>Submitted At:</strong> {{ $attempt->submitted_at ? $attempt->submitted_at->format('Y-m-d H:i:s') : 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <h3 class="h4 mb-3">Questions & Answers</h3>

        @foreach ($attempt->exam->questions as $index => $question)
            @php
                $answer = $answers->get($question->question_id);
            @endphp
            <div class="card mb-4 shadow-sm border-{{ $question->type === 'essay' ? 'warning' : 'light' }}">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Question {{ $index + 1 }} <small class="text-muted">({{ str_replace('_', ' ', $question->type) }})</small></h5>
                    <span class="badge bg-info text-white">Max Marks: {{ number_format($question->marks, 1) }}</span>
                </div>
                <div class="card-body">
                    <p class="fs-5">{{ $question->question_text }}</p>
                    
                    @if ($question->image_url)
                        <div class="mb-3">
                            <img src="{{ filter_var($question->image_url, FILTER_VALIDATE_URL) ? $question->image_url : asset('storage/' . $question->image_url) }}" alt="Question Diagram" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    @endif

                    <hr>

                    <!-- Student response display -->
                    <div class="mb-3">
                        <strong>Student Response:</strong>
                        <div class="p-3 bg-light rounded mt-2 border">
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
                                <span class="text-danger italic">No answer submitted for this question.</span>
                            @endif
                        </div>
                    </div>

                    <!-- Grading controls / feedback -->
                    @if ($question->type === 'essay' || $question->type === 'question_answer')
                        @if ($question->type === 'question_answer')
                            <div class="d-flex align-items-center mb-2">
                                <span class="fw-bold me-2">Auto-Graded Marks:</span>
                                @if ($answer)
                                    <span class="badge bg-{{ $answer->marks_awarded > 0 ? 'success' : 'danger' }} fs-6">
                                        {{ number_format($answer->marks_awarded, 1) }} / {{ number_format($question->marks, 1) }}
                                    </span>
                                @else
                                    <span class="badge bg-danger fs-6">0.0 / {{ number_format($question->marks, 1) }}</span>
                                @endif
                            </div>
                            <div class="mb-3 small">
                                <strong>Correct Setup:</strong>
                                <ul class="mb-0 ps-3">
                                    @foreach($question->options as $opt)
                                        @if($opt->is_correct)
                                            <li>{{ $opt->option_text }} <span class="badge bg-success bg-opacity-75 ms-1">Correct</span></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('instructor.answers.grade.update', $answer ? $answer->answer_id : 0) }}" method="POST" class="row g-3 align-items-center">
                            @csrf
                            @method('PUT')
                            <div class="col-auto">
                                <label for="marks-{{ $question->question_id }}" class="col-form-label fw-bold text-{{ $question->type === 'essay' ? 'warning' : 'primary' }}">
                                    {{ $question->type === 'essay' ? 'Award Marks:' : 'Override Marks:' }}
                                </label>
                            </div>
                            <div class="col-auto">
                                <input type="number" step="0.01" min="0" max="{{ $question->marks }}" 
                                    name="marks_awarded" id="marks-{{ $question->question_id }}" 
                                    class="form-control form-control-sm" style="width: 100px;" 
                                    value="{{ $answer && $answer->marks_awarded !== null ? (float)$answer->marks_awarded : '' }}" required
                                    {{ !$answer ? 'disabled' : '' }}>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-{{ $question->type === 'essay' ? 'warning' : 'primary' }} btn-sm" {{ !$answer ? 'disabled' : '' }}>
                                    {{ $question->type === 'essay' ? 'Save Marks' : 'Override Marks' }}
                                </button>
                            </div>
                            @if (!$answer)
                                <div class="col-auto text-muted small">Cannot grade a non-existent answer.</div>
                            @endif
                        </form>
                    @else
                        <!-- Auto-graded display -->
                        <div class="d-flex align-items-center">
                            <span class="fw-bold me-2">Auto-Graded Marks:</span>
                            @if ($answer)
                                <span class="badge bg-{{ $answer->marks_awarded > 0 ? 'success' : 'danger' }} fs-6">
                                    {{ number_format($answer->marks_awarded, 1) }} / {{ number_format($question->marks, 1) }}
                                </span>
                            @else
                                <span class="badge bg-danger fs-6">0.0 / {{ number_format($question->marks, 1) }}</span>
                            @endif
                        </div>
                        <div class="mt-2 small">
                            <strong>Correct Setup:</strong>
                            <ul class="mb-0 ps-3">
                                @foreach($question->options as $opt)
                                    @if($opt->is_correct)
                                        <li>{{ $opt->option_text }} <span class="badge bg-success bg-opacity-75 ms-1">Correct</span></li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Finalize Grading Button -->
        <div class="card shadow-sm mb-5">
            <div class="card-body d-flex justify-content-between align-items-center bg-light">
                <div>
                    <h5 class="mb-1">Submit Grade Evaluation</h5>
                    <p class="text-muted mb-0 small">This will lock the attempt score and publish the results to the student dashboard.</p>
                </div>
                <div>
                    <form action="{{ route('instructor.attempts.finalize', $attempt->attempt_id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg px-4" 
                            onclick="return confirm('Confirm finalizing the grade sheet? Make sure all essay questions have been saved first.');">
                            Finalize and Grade
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

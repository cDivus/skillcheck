@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('instructor.exams.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Back to Exams List</a>
    </div>

    <!-- Exam Details Card -->
    <div class="card mb-4 shadow-sm border-primary">
        <div class="card-header bg-primary text-white">
            <h1 class="h3 mb-0">Exam: {{ $exam->title }}</h1>
        </div>
        <div class="card-body">
            <p class="lead">{{ $exam->description ?? 'No description' }}</p>
            <div class="row">
                <div class="col-md-4">
                    <strong>Duration:</strong> {{ $exam->duration_s }} seconds ({{ round($exam->duration_s / 60, 2) }} minutes)
                </div>
                <div class="col-md-4">
                    <strong>Start Time:</strong> {{ $exam->start_time ?? 'N/A' }}
                </div>
                <div class="col-md-4">
                    <strong>End Time:</strong> {{ $exam->end_time ?? 'N/A' }}
                </div>
            </div>
            
            <div class="row mt-2">
                <div class="col-md-12">
                    <span class="me-3">
                        <strong>Question Ordering:</strong> 
                        <span class="badge bg-{{ $exam->randomize_questions ? 'info text-white' : 'secondary text-white' }}">
                            {{ $exam->randomize_questions ? 'Randomized' : 'Sequential (Order Index)' }}
                        </span>
                    </span>
                    <span>
                        <strong>Viewable Responses:</strong> 
                        <span class="badge bg-{{ $exam->viewable_responses ? 'success text-white' : 'danger text-white' }}">
                            {{ $exam->viewable_responses ? 'Enabled' : 'Disabled' }}
                        </span>
                    </span>
                </div>
            </div>
            
            <div class="mt-3">
                <strong>Student Link:</strong>
                <div class="input-group">
                    <input type="text" readonly value="{{ route('student.exams.show', ['exam' => $exam->exam_id]) }}" class="form-control bg-light">
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('instructor.questions.create', ['exam' => $exam->exam_id]) }}" class="btn btn-success">+ Add Question</a>
            </div>
        </div>
    </div>

    <!-- Questions Section -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Questions ({{ $exam->questions->count() }})</h2>
    </div>

    @if ($exam->questions->isEmpty())
        <div class="alert alert-info">
            No questions added to this exam yet.
        </div>
    @else
        <div class="row">
            @foreach ($exam->questions as $question)
                <div class="col-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <div>
                                <span class="badge bg-primary text-white">Index: {{ $question->order_index }}</span>
                                <span class="badge bg-secondary text-white">Type: {{ str_replace('_', ' ', $question->type) }}</span>
                                <span class="badge bg-info text-dark">Marks: {{ $question->marks }}</span>
                                @if ($question->time_limit_s)
                                    <span class="badge bg-warning text-dark">Time Limit: {{ $question->time_limit_s }}s</span>
                                @endif
                                @if ($question->is_locked)
                                    <span class="badge bg-dark text-white">🔒 Locked Position</span>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('instructor.questions.edit', ['exam' => $exam->exam_id, 'question' => $question->question_id]) }}" class="btn btn-outline-primary btn-sm">Edit Question</a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($question->image_url)
                                <div class="mb-3">
                                    <img src="{{ filter_var($question->image_url, FILTER_VALIDATE_URL) ? $question->image_url : asset('storage/' . $question->image_url) }}" alt="Question Image" class="img-thumbnail" style="max-width: 250px; max-height: 250px;">
                                </div>
                            @endif
                            
                            <p class="fs-5 fw-semibold mb-3">{{ $question->question_text }}</p>

                            @if (in_array($question->type, ['multiple_choice', 'true_false', 'question_answer']))
                                <div class="card p-3 bg-light">
                                    <h5 class="card-title h6 mb-3 text-muted">Options / Acceptable Answers:</h5>
                                    <ul class="list-group">
                                        @foreach ($question->options as $option)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="badge bg-secondary text-white me-2">Index: {{ $option->order_index }}</span>
                                                    {{ $option->option_text }}
                                                </div>
                                                @if ($option->is_correct)
                                                    <span class="badge bg-success text-white">Correct Answer</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 mb-0">Create New Exam</h1>
            <a href="{{ route('instructor.exams.index') }}" class="btn btn-outline-secondary">&larr; Back to Exams List</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('instructor.exams.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">Exam Title</label>
                        <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required placeholder="e.g. Midterm Exam">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description (Optional)</label>
                        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Enter instructions or description...">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="duration_s" class="form-label fw-bold">Duration</label>
                        <div class="input-group">
                            <input type="number" id="duration_s" name="duration_s" class="form-control" value="{{ old('duration_s', 3600) }}" required min="1">
                            <span class="input-group-text">seconds</span>
                        </div>
                        <span class="form-text text-muted">e.g., 3600 for 1 hour, 1800 for 30 minutes.</span>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_time" class="form-label fw-bold">Start Time (Optional)</label>
                            <input type="datetime-local" id="start_time" name="start_time" class="form-control" value="{{ old('start_time') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_time" class="form-label fw-bold">End Time (Optional)</label>
                            <input type="datetime-local" id="end_time" name="end_time" class="form-control" value="{{ old('end_time') }}">
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="randomize_questions" name="randomize_questions" value="1" {{ old('randomize_questions') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="randomize_questions">
                            Randomize Questions
                        </label>
                        <div class="form-text text-muted">If checked, the system will shuffle the order of questions for each student attempt.</div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">Create Exam</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

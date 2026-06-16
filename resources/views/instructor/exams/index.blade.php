@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-0">Instructor Dashboard - Exams List</h1>
            <p class="text-muted mb-0">Logged in as: {{ auth()->user()->username ?? 'Guest' }} ({{ auth()->user()->email ?? '' }})</p>
        </div>
        <div>
            <!-- Logout -->
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Logout</button>
            </form>
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

    <div class="mb-4">
        <a href="{{ route('instructor.exams.create') }}" class="btn btn-primary">+ Create New Exam</a>
    </div>

    @if ($exams->isEmpty())
        <div class="card p-4 text-center text-muted">
            No exams created yet.
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 g-4">
            @foreach ($exams as $exam)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title h4 text-primary mb-3">{{ $exam->title }}</h2>
                            <p class="card-text text-muted mb-4">{{ $exam->description ?? 'No description' }}</p>
                            
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><strong>Duration:</strong> {{ $exam->duration_s }} seconds ({{ round($exam->duration_s / 60, 2) }} minutes)</li>
                                <li class="mb-2"><strong>Start Time:</strong> {{ $exam->start_time ?? 'N/A' }}</li>
                                <li class="mb-2"><strong>End Time:</strong> {{ $exam->end_time ?? 'N/A' }}</li>
                                <li class="mb-2">
                                    <strong>Question Ordering:</strong> 
                                    <span class="badge bg-{{ $exam->randomize_questions ? 'info text-white' : 'secondary text-white' }}">
                                        {{ $exam->randomize_questions ? 'Randomized' : 'Sequential' }}
                                    </span>
                                </li>
                            </ul>

                            <div class="mb-3">
                                <label class="form-label mb-1"><strong>Student Link:</strong></label>
                                <input type="text" readonly value="{{ route('student.exams.show', ['exam' => $exam->exam_id]) }}" class="form-control form-control-sm bg-light">
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-4">
                                <a href="{{ route('instructor.exams.show', ['exam' => $exam->exam_id]) }}" class="btn btn-outline-primary btn-sm">Manage Exam</a>
                                <a href="{{ route('instructor.submissions.index', ['exam' => $exam->exam_id]) }}" class="btn btn-outline-secondary btn-sm">View Submissions</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

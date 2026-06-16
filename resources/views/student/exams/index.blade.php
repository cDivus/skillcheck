@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>My Exam Dashboard</h1>
        <div>
            <span class="me-3 text-muted">Logged in as: <strong>{{ auth()->user()->username }}</strong></span>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Start Exam via ID -->
    <div class="card mb-4 border-primary">
        <div class="card-body">
            <h5 class="card-title text-primary">Access a New Exam</h5>
            <p class="card-text text-muted small">Enter the Exam ID (UUID) provided by your instructor to view details and start the exam.</p>
            <form id="access-exam-form" class="row g-3 align-items-center">
                <div class="col-sm-8 col-md-9">
                    <input type="text" id="exam-id-input" class="form-control" placeholder="Enter Exam UUID (e.g. 123e4567-e89b-12d3-a456-426614174000)" required>
                </div>
                <div class="col-sm-4 col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Access Exam</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attempted Exams -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-secondary">My Exam Attempts</h5>
        </div>
        <div class="card-body">
            @if($attempts->isEmpty())
                <div class="text-center py-4 text-muted">
                    <p class="mb-0">You have not attempted any exams yet.</p>
                    <small>Enter an Exam ID above to get started.</small>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Exam Title</th>
                                <th>Started At</th>
                                <th>Submitted At</th>
                                <th>Status</th>
                                <th>Score</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attempts as $attempt)
                                <tr>
                                    <td>
                                        <strong>{{ $attempt->exam->title ?? 'Unknown Exam' }}</strong>
                                        @if($attempt->exam && $attempt->exam->description)
                                            <br><small class="text-muted">{{ Str::limit($attempt->exam->description, 70) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $attempt->start_time ? $attempt->start_time->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                    <td>{{ $attempt->end_time ? $attempt->end_time->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                    <td>
                                        @if($attempt->status === 'in_progress')
                                            <span class="badge bg-warning text-dark">In Progress</span>
                                        @elseif($attempt->status === 'submitted')
                                            <span class="badge bg-info text-white">Submitted</span>
                                        @elseif($attempt->status === 'graded')
                                            <span class="badge bg-success">Graded</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attempt->status === 'graded')
                                            <strong>{{ number_format($attempt->total_score, 2) }} / {{ number_format($attempt->max_score, 2) }}</strong>
                                        @elseif($attempt->status === 'submitted')
                                            <span class="text-muted small">Pending Grading</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attempt->status === 'in_progress')
                                            <a href="{{ route('student.exams.attempt.take', ['exam' => $attempt->exam_id, 'attempt' => $attempt->attempt_id]) }}" class="btn btn-primary btn-sm">Resume</a>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>Completed</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.getElementById('access-exam-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const examId = document.getElementById('exam-id-input').value.trim();
        if (examId) {
            window.location.href = `{{ url('/student/exams') }}/${examId}`;
        }
    });
</script>
@endsection

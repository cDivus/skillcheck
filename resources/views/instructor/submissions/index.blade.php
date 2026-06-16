@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Submissions</h1>
                <p class="text-muted mb-0">Exam: <strong>{{ $exam->title }}</strong></p>
            </div>
            <a href="{{ route('instructor.exams.index') }}" class="btn btn-outline-secondary">&larr; Back to Exams Dashboard</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-secondary">Student Attempts</h5>
            </div>
            <div class="card-body p-0">
                @if ($attempts->isEmpty())
                    <div class="text-center py-5 text-muted">
                        No submissions recorded for this exam yet.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Student</th>
                                    <th>Started At</th>
                                    <th>Submitted At</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attempts as $attempt)
                                    <tr>
                                        <td class="ps-4 fw-bold">{{ $attempt->student->username }}</td>
                                        <td>{{ $attempt->started_at ? $attempt->started_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                        <td>{{ $attempt->submitted_at ? $attempt->submitted_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                        <td>
                                            @if ($attempt->status === 'graded')
                                                <span class="badge bg-success">Graded</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending Grading</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($attempt->status === 'graded')
                                                <strong>{{ number_format($attempt->total_score, 1) }}</strong> <span class="text-muted">/ {{ number_format($attempt->max_score, 1) }}</span>
                                            @else
                                                <span class="text-muted">Partially graded</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('instructor.submissions.grade', $attempt->attempt_id) }}" class="btn btn-primary btn-sm">
                                                {{ $attempt->status === 'graded' ? 'View Details' : 'Grade Submission' }}
                                            </a>
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
</div>
@endsection

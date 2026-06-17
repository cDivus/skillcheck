@extends('layouts.app')

@section('content')
    <!-- Admin Navigation Bar -->
    <div class="card mb-4 bg-light shadow-sm">
        <div class="card-body py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <span class="fs-4 fw-bold text-dark me-2">👑 Admin Portal</span>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm {{ Route::is('admin.dashboard') ? 'btn-primary' : 'btn-outline-primary' }}">Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm {{ Route::is('admin.users.index') ? 'btn-primary' : 'btn-outline-primary' }}">Manage Users</a>
                <a href="{{ route('admin.exams.index') }}" class="btn btn-sm {{ Route::is('admin.exams.index') ? 'btn-primary' : 'btn-outline-primary' }}">Moderate Exams</a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small">Logged in as: <strong>{{ auth()->user()->username }}</strong></span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Title and Search Section -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Moderate Exams</h1>
            <p class="text-muted mb-0">Browse and manage exams created by instructors on the platform.</p>
        </div>
    </div>

    <!-- Status Alerts -->
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

    <!-- Search Form Card -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body">
            <form action="{{ route('admin.exams.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="search" class="form-label small fw-bold text-muted">Search Exam Title or Description</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search exam title or keywords..." class="form-control">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success flex-grow-1">Search Exams</button>
                    @if(request()->filled('search'))
                        <a href="{{ route('admin.exams.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Exams List/Table Card -->
    <div class="card shadow-sm border-0 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 35%;">Exam Details</th>
                        <th>Created By</th>
                        <th>Duration</th>
                        <th>Schedule</th>
                        <th>Created Date</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold text-primary mb-1">{{ $exam->title }}</div>
                                <div class="text-muted small text-truncate" style="max-width: 300px;">
                                    {{ $exam->description ?? 'No description provided.' }}
                                </div>
                            </td>
                            <td>
                                @if($exam->instructor)
                                    <div>
                                        <span class="fw-medium text-dark">{{ $exam->instructor->username }}</span>
                                        <span class="text-muted small d-block" style="font-size: 0.8rem;">{{ $exam->instructor->email }}</span>
                                    </div>
                                @else
                                    <span class="text-muted small">Unknown Instructor</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ round($exam->duration_s / 60, 1) }} mins</div>
                                <span class="text-muted small" style="font-size: 0.8rem;">{{ $exam->duration_s }} seconds</span>
                            </td>
                            <td>
                                @if($exam->start_time && $exam->end_time)
                                    <div class="small text-dark">
                                        <strong>Start:</strong> {{ \Carbon\Carbon::parse($exam->start_time)->format('M d, g:i A') }}
                                    </div>
                                    <div class="small text-muted">
                                        <strong>End:</strong> {{ \Carbon\Carbon::parse($exam->end_time)->format('M d, g:i A') }}
                                    </div>
                                @else
                                    <span class="badge bg-secondary text-white">Always Open</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $exam->created_at ? \Carbon\Carbon::parse($exam->created_at)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="pe-4 text-end">
                                <form action="{{ route('admin.exams.destroy', $exam->exam_id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete the exam \'{{ $exam->title }}\'? This action will also delete all questions, options, student answers, and attempts for this exam and CANNOT be undone.')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No exams found matching your search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if($exams->hasPages())
            <div class="card-footer bg-white border-0 py-3 ps-4 pe-4">
                {{ $exams->links() }}
            </div>
        @endif
    </div>
@endsection

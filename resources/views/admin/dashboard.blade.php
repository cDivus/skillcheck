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

    <!-- Page Title -->
    <div class="mb-4">
        <h1 class="h2">Overview Statistics</h1>
        <p class="text-muted">High-level summary of the SkillCheck system's database entities.</p>
    </div>

    <!-- Alert Messages -->
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

    <!-- Stat Cards -->
    <div class="row g-4">
        <!-- Users Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-left: 5px solid #0d6efd !important;">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-2 fw-bold">Total Accounts</h6>
                            <span class="h1 fw-bold text-dark">{{ $stats['users'] }}</span>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.047 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724C2.3 10.634 3.268 10 5 10a5.5 5.5 0 0 0 .92.083Zm-1.6-4.13a3 3 0 1 1 0-6 3 3 0 0 1 0 6Zm0-5a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exams Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-left: 5px solid #198754 !important;">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-2 fw-bold">Active Exams</h6>
                            <span class="h1 fw-bold text-dark">{{ $stats['exams'] }}</span>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
                                <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                                <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attempts Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 bg-white" style="border-left: 5px solid #fd7e14 !important;">
                <div class="card-body py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-2 fw-bold">Student Submissions</h6>
                            <span class="h1 fw-bold text-dark">{{ $stats['attempts'] }}</span>
                        </div>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-journal-check" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M10.854 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 8.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                                <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2zm0 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3z"/>
                                <path d="M1 5h14v1H1V5zm0 4h14v1H1V9z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Prompts -->
    <div class="row mt-5">
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h4 class="fw-bold mb-3">👥 User Management</h4>
                        <p class="text-muted">Inspect the full catalog of registered accounts. Suspend access for users who breach honor codes, or reactivate previously suspended accounts.</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary mt-3">Go to Users List &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <h4 class="fw-bold mb-3">📝 Exam Moderation</h4>
                        <p class="text-muted">Browse all exams generated across the system by various instructors. Audit titles, creators, and delete orphaned or violating exams to keep the index clean.</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.exams.index') }}" class="btn btn-success mt-3">Go to Exams Moderation &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

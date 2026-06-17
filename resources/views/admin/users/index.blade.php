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
            <h1 class="h2 mb-1">Manage Users</h1>
            <p class="text-muted mb-0">Browse accounts, filter by role, and suspend/unsuspend access.</p>
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

    <!-- Filter Form Card -->
    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="search" class="form-label small fw-bold text-muted">Search Username or Email</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Search username or email..." class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="role" class="form-label small fw-bold text-muted">Filter by Role</label>
                    <select name="role" id="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Apply Filters</button>
                    @if(request()->anyFilled(['search', 'role']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="card shadow-sm border-0 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    @if($user->profile_picture)
                                        <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile Picture" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <span class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center me-3 fw-bold" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($user->username, 0, 1)) }}
                                        </span>
                                    @endif
                                    <div>
                                        <span class="fw-semibold text-dark">{{ $user->username }}</span>
                                        @if($user->user_id === auth()->id())
                                            <span class="badge bg-dark-subtle text-dark-emphasis ms-1" style="font-size: 0.75rem;">You</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger text-white">Admin</span>
                                @elseif($user->role === 'instructor')
                                    <span class="badge bg-success text-white">Instructor</span>
                                @else
                                    <span class="badge bg-info text-white">Student</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_suspended)
                                    <span class="badge bg-danger text-white">Suspended</span>
                                @else
                                    <span class="badge bg-success text-white">Active</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="pe-4 text-end">
                                @if($user->user_id !== auth()->id())
                                    <form action="{{ route('admin.users.toggle-status', $user->user_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        @if($user->is_suspended)
                                            <button type="submit" class="btn btn-sm btn-outline-success">Reactivate</button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to suspend {{ $user->username }}? They will be blocked from logging in immediately.')">Suspend</button>
                                        @endif
                                    </form>
                                @else
                                    <span class="text-muted small italic">Cannot edit self</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No users found matching your search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        @if($users->hasPages())
            <div class="card-footer bg-white border-0 py-3 ps-4 pe-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection

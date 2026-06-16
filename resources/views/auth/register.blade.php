@extends('layouts.app')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="col-md-6 col-sm-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4 text-primary">Register Account</h3>

                @if ($errors->any())
                    <div class="alert alert-danger mb-4 py-2">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label text-secondary small fw-bold">Username</label>
                            <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label text-secondary small fw-bold">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label text-secondary small fw-bold">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label text-secondary small fw-bold">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label text-secondary small fw-bold">Account Role</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student (Take Exams)</option>
                            <option value="instructor" {{ old('role') === 'instructor' ? 'selected' : '' }}>Instructor (Manage Exams)</option>
                        </select>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary py-2 fw-semibold">Create Account</button>
                    </div>
                </form>

                <div class="text-center mt-4 pt-2 border-top">
                    <p class="mb-0 text-muted small">Already have an account? <a href="{{ route('login') }}" class="text-decoration-none">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

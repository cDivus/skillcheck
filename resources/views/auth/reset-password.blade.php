@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="mb-0 text-center">Reset Password</h4>
            </div>
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

                <form action="{{ route('password.update') }}" method="POST">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address:</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ request()->query('email', old('email')) }}" required readonly>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password:</label>
                        <input type="password" name="password" id="password" class="form-control" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password:</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

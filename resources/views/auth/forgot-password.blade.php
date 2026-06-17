@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="mb-0 text-center">Forgot Password</h4>
            </div>
            <div class="card-body p-4">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <p class="text-muted mb-4">
                    Enter your email address, and we will send you a link to reset your password.
                </p>

                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address:</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
                    </div>
                </form>

                <hr class="my-4">

                <p class="text-center mb-0"><a href="{{ route('login') }}" class="text-decoration-none">Back to Login</a></p>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
    <h1>Create New Exam</h1>
    
    <div>
        <a href="{{ route('instructor.exams.index') }}">[ Back to Exams List ]</a>
    </div>

    <hr>

    @if ($errors->any())
        <div style="color: red; margin-bottom: 15px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('instructor.exams.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 10px;">
            <label for="title">Exam Title (Required):</label><br>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="description">Description (Optional):</label><br>
            <textarea id="description" name="description" rows="4" cols="50">{{ old('description') }}</textarea>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="duration_s">Duration in Seconds (Required):</label><br>
            <input type="number" id="duration_s" name="duration_s" value="{{ old('duration_s', 3600) }}" required min="1">
            <span style="font-size: 0.9em; color: gray;">(e.g., 3600 for 1 hour)</span>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="start_time">Start Time (Optional):</label><br>
            <input type="datetime-local" id="start_time" name="start_time" value="{{ old('start_time') }}">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="end_time">End Time (Optional):</label><br>
            <input type="datetime-local" id="end_time" name="end_time" value="{{ old('end_time') }}">
        </div>

        <div style="margin-top: 15px;">
            <button type="submit">Create Exam</button>
        </div>
    </form>
@endsection

@extends('layouts.app')

@section('content')
    <h1>Instructor Dashboard - Exams List</h1>
    <p>Logged in as: {{ auth()->user()->username ?? 'Guest' }} ({{ auth()->user()->email ?? '' }})</p>

    <!-- Logout -->
    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit">Logout</button>
    </form>

    <hr>

    @if (session('success'))
        <div style="color: green; font-weight: bold; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="color: red; font-weight: bold; margin-bottom: 15px;">
            {{ session('error') }}
        </div>
    @endif

    <div>
        <a href="{{ route('instructor.exams.create') }}"><strong>[ + Create New Exam ]</strong></a>
    </div>

    <hr>

    @if ($exams->isEmpty())
        <p>No exams created yet.</p>
    @else
        @foreach ($exams as $exam)
            <div style="border: 1px solid black; padding: 15px; margin-bottom: 20px;">
                <h2>Exam: {{ $exam->title }}</h2>
                <p><strong>Description:</strong> {{ $exam->description ?? 'No description' }}</p>
                <p><strong>Duration:</strong> {{ $exam->duration_s }} seconds ({{ round($exam->duration_s / 60, 2) }} minutes)</p>
                <p><strong>Start Time:</strong> {{ $exam->start_time ?? 'N/A' }}</p>
                <p><strong>End Time:</strong> {{ $exam->end_time ?? 'N/A' }}</p>
                
                <p>
                    <strong>Student Link:</strong> 
                    <input type="text" readonly value="{{ route('student.exams.show', ['exam' => $exam->exam_id]) }}" style="width: 400px;">
                </p>

                <p>
                    <a href="{{ route('instructor.questions.create', ['exam' => $exam->exam_id]) }}"><strong>[ + Add Question ]</strong></a>
                    | 
                    <a href="{{ route('instructor.submissions.index', ['exam' => $exam->exam_id]) }}"><strong>[ View Submissions ]</strong></a>
                </p>

                <h3>Questions ({{ $exam->questions->count() }})</h3>
                @if ($exam->questions->isEmpty())
                    <p>No questions added to this exam yet.</p>
                @else
                    <ol>
                        @foreach ($exam->questions as $question)
                            <li style="margin-bottom: 15px;">
                                <strong>Question Index:</strong> {{ $question->order_index }} | 
                                <strong>Type:</strong> {{ $question->type }} | 
                                <strong>Marks:</strong> {{ $question->marks }}
                                @if ($question->time_limit_s)
                                    | <strong>Time Limit:</strong> {{ $question->time_limit_s }} seconds
                                @endif
                                @if ($question->image_url)
                                    <br><strong>Image:</strong><br>
                                    <img src="{{ filter_var($question->image_url, FILTER_VALIDATE_URL) ? $question->image_url : asset('storage/' . $question->image_url) }}" alt="Question Image" style="max-width: 150px; max-height: 150px; display: block; margin-top: 5px; margin-bottom: 5px;">
                                @endif
                                <p><em>{{ $question->question_text }}</em></p>

                                <p>
                                    <a href="{{ route('instructor.questions.edit', ['exam' => $exam->exam_id, 'question' => $question->question_id]) }}"><strong>[ Edit Question ]</strong></a>
                                </p>

                                @if (in_array($question->type, ['multiple_choice', 'true_false', 'question_answer']))
                                    <ul>
                                        @foreach ($question->options as $option)
                                            <li>
                                                Index: {{ $option->order_index }} | 
                                                {{ $option->option_text }}
                                                @if ($option->is_correct)
                                                    <strong style="color: green;">[ CORRECT ANSWER ]</strong>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        @endforeach
    @endif
@endsection

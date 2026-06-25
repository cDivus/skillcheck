@extends('layouts.app')

@section('title', 'Exam Details')

@section('content')
<x-ui.page-header
    :title="$exam->title"
    subtitle="Exam details & instructions">
    <x-slot:actions>
        <x-ui.button variant="secondary" :href="route('student.exams.index')">
            <x-icon name="arrow-left" /> Back to Dashboard
        </x-ui.button>
    </x-slot:actions>
</x-ui.page-header>

<div class="mx-auto max-w-3xl">
    <x-ui.card>
        <div class="flex items-center gap-2 text-ink">
            <x-icon name="info" class="h-5 w-5 text-brand-700" />
            <h2 class="text-sm font-semibold">Exam Details &amp; Instructions</h2>
        </div>

        <div class="mt-3 flex items-center gap-2">
            <x-ui.avatar :user="$exam->instructor" size="sm" />
            <span class="text-sm text-muted">Instructor: <span class="font-medium text-ink">{{ $exam->instructor->username ?? 'Unknown' }}</span></span>
        </div>

        <hr class="my-5 border-line">

        @if($exam->description)
            <div class="mb-6">
                <h3 class="text-sm font-medium text-ink">Description</h3>
                <p class="mt-1 text-sm text-muted">{{ $exam->description }}</p>
            </div>
        @endif

        <div class="grid gap-x-8 gap-y-3 sm:grid-cols-2">
            <div class="flex items-center justify-between border-b border-line py-2">
                <span class="flex items-center gap-2 text-sm text-muted"><x-icon name="clock" class="h-4 w-4 text-brand-700" /> Duration</span>
                <x-ui.badge color="gray">{{ round($exam->duration_s / 60) }} minutes</x-ui.badge>
            </div>
            <div class="flex items-center justify-between border-b border-line py-2">
                <span class="flex items-center gap-2 text-sm text-muted"><x-icon name="circle-dot" class="h-4 w-4 text-brand-700" /> Total Questions</span>
                <span class="text-sm font-medium text-ink">{{ $exam->questions()->count() }}</span>
            </div>
            <div class="flex items-center justify-between border-b border-line py-2">
                <span class="flex items-center gap-2 text-sm text-muted"><x-icon name="layers" class="h-4 w-4 text-brand-700" /> Question Ordering</span>
                <x-ui.badge :color="$exam->randomize_questions ? 'blue' : 'gray'">
                    {{ $exam->randomize_questions ? 'Randomized' : 'Sequential' }}
                </x-ui.badge>
            </div>
            @if($exam->start_time)
                <div class="flex items-center justify-between border-b border-line py-2">
                    <span class="flex items-center gap-2 text-sm text-muted"><x-icon name="check-circle" class="h-4 w-4 text-brand-700" /> Available From</span>
                    <span class="text-sm text-ink">{{ \Carbon\Carbon::parse($exam->start_time)->format('Y-m-d H:i') }}</span>
                </div>
            @endif
            @if($exam->end_time)
                <div class="flex items-center justify-between border-b border-line py-2">
                    <span class="flex items-center gap-2 text-sm text-muted"><x-icon name="alert-triangle" class="h-4 w-4 text-brand-700" /> Available Until</span>
                    <span class="text-sm font-medium text-red-600">{{ \Carbon\Carbon::parse($exam->end_time)->format('Y-m-d H:i') }}</span>
                </div>
            @endif
        </div>

        <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
            <div class="flex items-center gap-2 text-amber-700">
                <x-icon name="alert-triangle" class="h-5 w-5" />
                <h3 class="text-sm font-semibold">Important Rules</h3>
            </div>
            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-amber-800">
                <li>Once you start, the timer cannot be paused.</li>
                <li>Make sure you have a stable internet connection.</li>
                <li>If the time expires, your answers will be automatically submitted.</li>
                <li>Do not refresh or close the browser window unless necessary.</li>
            </ul>
        </div>

        {{-- Verify availability constraints --}}
        @php
            $now = now();
            $isAvailable = true;
            $errorMsg = '';

            if ($exam->start_time && $now->lessThan(\Carbon\Carbon::parse($exam->start_time))) {
                $isAvailable = false;
                $errorMsg = 'This exam has not started yet.';
            } elseif ($exam->end_time && $now->greaterThan(\Carbon\Carbon::parse($exam->end_time))) {
                $isAvailable = false;
                $errorMsg = 'This exam is no longer available (deadline passed).';
            }
        @endphp

        @if($isAvailable)
            <form action="{{ route('student.exams.attempt.store', ['exam' => $exam->exam_id]) }}" method="POST" class="mt-6">
                @csrf
                <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
                    Start Attempt <x-icon name="arrow-right" />
                </x-ui.button>
            </form>
        @else
            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4 text-center text-sm text-red-700">
                <strong>Not Available:</strong> {{ $errorMsg }}
            </div>
        @endif
    </x-ui.card>
</div>
@endsection

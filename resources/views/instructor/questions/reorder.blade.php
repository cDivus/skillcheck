@extends('layouts.app')

@section('title', 'Reorder Questions')

@section('content')
    <x-ui.page-header title="Reorder Questions" subtitle="Drag and drop to rearrange questions in &quot;{{ $exam->title }}&quot;">
        <x-slot:actions>
            <x-ui.button href="{{ route('instructor.exams.show', $exam->exam_id) }}" variant="secondary">
                <x-icon name="arrow-left" /> Back to Exam
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if($exam->questions->isEmpty())
        <x-ui.card padding="p-0" class="overflow-hidden">
            <x-ui.empty-state icon="list-ordered" title="Nothing to reorder" message="This exam has no questions to reorder." />
        </x-ui.card>
    @else
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <form action="{{ route('instructor.questions.save-order', $exam->exam_id) }}" method="POST">
                    @csrf

                    <div id="sortable-list" class="mb-6 space-y-2">
                        @foreach($exam->questions as $index => $question)
                            <div class="sortable-item flex items-center rounded-xl border border-line bg-white p-3 shadow-xs"
                                 draggable="true"
                                 data-id="{{ $question->question_id }}">

                                <input type="hidden" name="question_ids[]" value="{{ $question->question_id }}">

                                <!-- Drag Handle -->
                                <div class="drag-handle mr-3 text-faint" style="cursor: grab;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M7 2a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                    </svg>
                                </div>

                                <!-- Visual Index Badge -->
                                <div class="mr-3">
                                    <span class="visual-index inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand-700 text-sm font-medium text-white">
                                        {{ $index + 1 }}
                                    </span>
                                </div>

                                <!-- Question Summary -->
                                <div class="min-w-0 flex-1">
                                    <div class="mb-1 flex flex-wrap items-center gap-2">
                                        <x-ui.badge color="gray">{{ str_replace('_', ' ', strtoupper($question->type)) }}</x-ui.badge>
                                        <x-ui.badge color="blue">Marks: {{ $question->marks }}</x-ui.badge>
                                        @if($question->is_locked)
                                            <x-ui.badge color="gray"><x-icon name="lock" /> Locked Position</x-ui.badge>
                                        @endif
                                    </div>
                                    <div class="truncate text-sm font-semibold text-ink">
                                        {{ Str::limit($question->question_text, 120) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex gap-2">
                        <x-ui.button type="submit" variant="primary"><x-icon name="check" /> Save New Order</x-ui.button>
                        <x-ui.button href="{{ route('instructor.exams.show', $exam->exam_id) }}" variant="secondary">Cancel</x-ui.button>
                    </div>
                </form>
            </div>

            <div>
                <x-ui.card>
                    <h5 class="mb-3 flex items-center gap-2 text-sm font-semibold text-ink"><x-icon name="info" class="w-4 h-4" /> Tips &amp; Guidelines</h5>
                    <ul class="list-disc space-y-2 pl-5 text-sm text-muted">
                        <li>Click and drag from the handle <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="inline-block align-middle" viewBox="0 0 16 16"><path d="M7 2a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg> or anywhere on the question card to move it up or down.</li>
                        <li>The numbers on the left will automatically update to preview the new sequence.</li>
                        <li>Make sure to click <strong>"Save New Order"</strong> to apply the changes.</li>
                        <li>Questions marked with 🔒 Locked Position are pinned during exam-taking when randomization is enabled, but can still be reordered here.</li>
                    </ul>
                </x-ui.card>
            </div>
        </div>
    @endif

<style>
    .sortable-item {
        transition: transform 0.2s, box-shadow 0.2s, background-color 0.2s;
        user-select: none;
    }

    .sortable-item:hover {
        background-color: #f8f9fa !important;
        transform: translateY(-1px);
        box-shadow: 0 .25rem .5rem rgba(0,0,0,.08)!important;
    }

    .sortable-item.dragging {
        opacity: 0.5;
        background-color: #e9ecef !important;
        border-style: dashed !important;
        border-color: #0f766e !important;
        transform: scale(0.98);
        box-shadow: none !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const list = document.getElementById('sortable-list');
        if (!list) return;

        let dragEl = null;

        list.addEventListener('dragstart', function(e) {
            // Find the parent draggable container
            const item = e.target.closest('.sortable-item');
            if (item) {
                dragEl = item;
                // Add class for styling asynchronously to keep the drag image intact
                setTimeout(() => {
                    dragEl.classList.add('dragging');
                }, 0);
            }
        });

        list.addEventListener('dragover', function(e) {
            e.preventDefault();
            const dragging = document.querySelector('.dragging');
            if (!dragging) return;

            const afterElement = getDragAfterElement(list, e.clientY);
            if (afterElement == null) {
                list.appendChild(dragging);
            } else {
                list.insertBefore(dragging, afterElement);
            }
        });

        list.addEventListener('dragend', function(e) {
            const dragging = document.querySelector('.dragging');
            if (dragging) {
                dragging.classList.remove('dragging');
            }
            updateVisualIndices();
            dragEl = null;
        });

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.sortable-item:not(.dragging)')];

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        function updateVisualIndices() {
            const badges = document.querySelectorAll('.visual-index');
            badges.forEach((badge, index) => {
                badge.textContent = index + 1;
            });
        }
    });
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('instructor.exams.show', $exam->exam_id) }}" class="btn btn-outline-secondary btn-sm mb-2">&larr; Back to Exam</a>
            <h1 class="h2 text-dark font-weight-bold">Reorder Questions</h1>
            <p class="text-muted mb-0">Drag and drop the questions below to rearrange their order in the exam <strong>"{{ $exam->title }}"</strong>.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($exam->questions->isEmpty())
        <div class="alert alert-info">
            This exam has no questions to reorder.
        </div>
    @else
        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('instructor.questions.save-order', $exam->exam_id) }}" method="POST">
                    @csrf
                    
                    <div id="sortable-list" class="list-group mb-4">
                        @foreach($exam->questions as $index => $question)
                            <div class="sortable-item list-group-item list-group-item-action border rounded mb-2 p-3 shadow-sm d-flex align-items-center bg-white" 
                                 draggable="true" 
                                 data-id="{{ $question->question_id }}">
                                
                                <input type="hidden" name="question_ids[]" value="{{ $question->question_id }}">
                                
                                <!-- Drag Handle -->
                                <div class="drag-handle text-muted me-3" style="cursor: grab;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-grip-vertical" viewBox="0 0 16 16">
                                        <path d="M7 2a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                    </svg>
                                </div>

                                <!-- Visual Index Badge -->
                                <div class="me-3">
                                    <span class="badge bg-primary rounded-circle visual-index d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.95rem;">
                                        {{ $index + 1 }}
                                    </span>
                                </div>

                                <!-- Question Summary -->
                                <div class="flex-grow-1 min-width-0">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge bg-secondary text-white me-2" style="font-size: 0.75rem;">
                                            {{ str_replace('_', ' ', strtoupper($question->type)) }}
                                        </span>
                                        <span class="badge bg-info text-dark" style="font-size: 0.75rem;">
                                            Marks: {{ $question->marks }}
                                        </span>
                                        @if($question->is_locked)
                                            <span class="badge bg-dark text-white ms-2" style="font-size: 0.75rem;">🔒 Locked Position</span>
                                        @endif
                                    </div>
                                    <div class="text-truncate fw-semibold text-dark" style="max-width: 90%;">
                                        {{ Str::limit($question->question_text, 120) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary shadow-sm px-4">Save New Order</button>
                        <a href="{{ route('instructor.exams.show', $exam->exam_id) }}" class="btn btn-outline-secondary px-4">Cancel</a>
                    </div>
                </form>
            </div>
            
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card bg-light border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold text-dark mb-3">Tips & Guidelines</h5>
                        <ul class="text-muted mb-0 ps-3">
                            <li class="mb-2">Click and drag from the handle <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-grip-vertical d-inline-block align-middle" viewBox="0 0 16 16"><path d="M7 2a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 5a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zM7 8a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm-3 3a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm3 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg> or anywhere on the question card to move it up or down.</li>
                            <li class="mb-2">The numbers on the left will automatically update to preview the new sequence.</li>
                            <li class="mb-2">Make sure to click <strong>"Save New Order"</strong> to apply the changes.</li>
                            <li>Questions marked with 🔒 Locked Position are pinned during exam-taking when randomization is enabled, but can still be reordered here.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

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
        border-color: #0d6efd !important;
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

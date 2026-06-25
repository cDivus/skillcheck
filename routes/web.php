<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Instructor;
use App\Http\Controllers\Student;
use App\Http\Controllers\Admin;

// Root / Welcome route
Route::get('/', function () {
    return view('welcome');
})->middleware('guest');

// Module 1: Authentication & Identity
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Forgot / Reset Password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile/edit', [AuthController::class, 'editProfile'])->name('profile.edit');
    Route::get('/profile', fn () => redirect()->route('profile.edit'));
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Module 2 & 4: Exam Management & Grading (Instructor Portal - Requires Instructor Middleware)
    Route::middleware('role:instructor')->prefix('instructor')->name('instructor.')->group(function () {
        // Exam Management
        Route::get('/exams', [Instructor\ExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/create', [Instructor\ExamController::class, 'create'])->name('exams.create');
        Route::post('/exams', [Instructor\ExamController::class, 'store'])->name('exams.store');
        Route::get('/exams/{exam}', [Instructor\ExamController::class, 'show'])->name('exams.show');
        Route::get('/exams/{exam}/edit', [Instructor\ExamController::class, 'edit'])->name('exams.edit');
        Route::put('/exams/{exam}', [Instructor\ExamController::class, 'update'])->name('exams.update');
        Route::delete('/exams/{exam}', [Instructor\ExamController::class, 'destroy'])->name('exams.destroy');

        Route::get('/exams/{exam}/questions/create', [Instructor\QuestionController::class, 'create'])->name('questions.create');
        Route::post('/exams/{exam}/questions', [Instructor\QuestionController::class, 'store'])->name('questions.store');
        Route::post('/exams/{exam}/questions/import', [Instructor\QuestionController::class, 'import'])->name('questions.import');
        Route::get('/exams/{exam}/questions/export', [Instructor\QuestionController::class, 'export'])->name('questions.export');

        Route::get('/exams/{exam}/questions/reorder', [Instructor\QuestionController::class, 'reorder'])->name('questions.reorder');
        Route::post('/exams/{exam}/questions/reorder', [Instructor\QuestionController::class, 'saveOrder'])->name('questions.save-order');

        Route::get('/exams/{exam}/questions/{question}/edit', [Instructor\QuestionController::class, 'edit'])->name('questions.edit');
        Route::put('/exams/{exam}/questions/{question}', [Instructor\QuestionController::class, 'update'])->name('questions.update');
        Route::delete('/exams/{exam}/questions/{question}', [Instructor\QuestionController::class, 'destroy'])->name('questions.destroy');

        // Grading & Evaluation
        Route::get('/exams/{exam}/submissions', [Instructor\SubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/{attempt}/grade', [Instructor\SubmissionController::class, 'show'])->name('submissions.grade');
        Route::put('/answers/{answer}/grade', [Instructor\AnswerController::class, 'update'])->name('answers.grade.update');
        Route::post('/attempts/{attempt}/finalize', [Instructor\SubmissionController::class, 'finalize'])->name('attempts.finalize');
        Route::delete('/attempts/{attempt}', [Instructor\SubmissionController::class, 'destroy'])->name('attempts.destroy');
    });

    // Module 3 & 4: Test-Taking Environment & Student Submission (Student Portal - Requires Student Middleware)
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/exams', [Student\ExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/{exam}', [Student\ExamController::class, 'show'])->name('exams.show');
        Route::post('/exams/{exam}/attempt', [Student\AttemptController::class, 'store'])->name('exams.attempt.store');
        Route::get('/exams/{exam}/attempt/{attempt}/take', [Student\AttemptController::class, 'show'])->name('exams.attempt.take');
        Route::get('/exams/{exam}/attempt/{attempt}/review', [Student\AttemptController::class, 'review'])->name('exams.attempt.review');
        Route::post('/exams/{exam}/attempt/{attempt}/answers', [Student\AnswerController::class, 'store'])->name('exams.attempt.answers.store');
        Route::post('/exams/{exam}/attempt/{attempt}/submit', [Student\AttemptController::class, 'submit'])->name('exams.attempt.submit');
    });

    // Module 5: Admin Operations (Admin Portal - Requires Admin Middleware)
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // User management
        Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}/toggle-status', [Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Exam moderation
        Route::get('/exams', [Admin\ExamController::class, 'index'])->name('exams.index');
        Route::delete('/exams/{exam}', [Admin\ExamController::class, 'destroy'])->name('exams.destroy');
    });
});

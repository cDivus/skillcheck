<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'exams' => Exam::count(),
            'attempts' => ExamAttempt::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}

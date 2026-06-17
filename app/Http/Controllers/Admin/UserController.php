<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Toggle the suspension status of a user.
     */
    public function toggleStatus($userId)
    {
        $user = User::findOrFail($userId);

        // Prevent self-suspension
        if ($user->user_id === auth()->id()) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        $user->is_suspended = !$user->is_suspended;
        $user->save();

        $statusMessage = $user->is_suspended ? 'suspended' : 'activated';

        return back()->with('success', "User '{$user->username}' has been {$statusMessage} successfully.");
    }
}

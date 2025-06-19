<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- MODIFIED: Import the Auth facade

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        // Fetch all users except the currently logged-in admin
        // MODIFIED: Changed auth()->id() to Auth::id() to correctly get the user's ID
        $users = User::where('id', '!=', Auth::id())->paginate(10);
        return view('admin.users.index', compact('users'));
    }
}

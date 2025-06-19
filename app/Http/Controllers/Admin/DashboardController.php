<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $bookCount = Book::count();
        $userCount = User::count();
        $latestUsers = User::latest()->take(5)->get();
        $latestBooks = Book::latest()->take(5)->get();

        return view('admin.dashboard', compact('bookCount', 'userCount', 'latestUsers', 'latestBooks'));
    }
}

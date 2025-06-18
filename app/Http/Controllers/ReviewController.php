<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:10',
        ]);

        // Use updateOrCreate to prevent a user from reviewing the same book twice.
        $book->reviews()->updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'rating' => $request->rating,
                'content' => $request->content,
            ]
        );

        return back()->with('success', 'Thank you for your review!');
    }
}

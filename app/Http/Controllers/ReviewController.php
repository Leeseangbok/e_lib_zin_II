<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store or update a review for a book.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:10',
        ]);

        $book->reviews()->updateOrCreate(
            [
                'user_id' => Auth::id(),
            ],
            [
                'content' => $request->content,
                'rating' => $request->rating,
            ]
        );

        return back()->with('success', 'Your review has been successfully submitted/updated!');
    }
}

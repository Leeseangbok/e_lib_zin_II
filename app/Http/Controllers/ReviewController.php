<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class ReviewController extends Controller
{
    /**
     * Store or update a review for a book.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\RedirectResponse
     */
    // app/Http/Controllers/ReviewController.php
    public function store(Request $request)
    {
        // 1. Change validation key from 'comment' to 'review_text'
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string|max:1000', // Changed
            'gutenberg_book_id' => 'required|integer',
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'gutenberg_book_id' => $validated['gutenberg_book_id'],
            'rating' => $validated['rating'],
            // 2. Change key in create() from 'comment' to 'review_text'
            'review_text' => $validated['review_text'], // Changed
        ]);

        return back()->with('success', 'Your review has been submitted!');
    }

    /**
     * Remove the specified review from storage.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Review $review)
    {
        // Authorize that the user can delete the review
        if (Auth::id() !== $review->user_id && !Auth::user()->isAdmin()) {
            throw new AuthorizationException('You are not authorized to delete this review.');
        }

        $review->delete();

        return back()->with('success', 'Your review has been successfully deleted.');
    }
}

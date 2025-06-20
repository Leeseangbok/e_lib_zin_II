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
     * Store or update a review for a book.!
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validation is correct
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string|min:10|max:1000',
            'gutenberg_book_id' => 'required|integer',
        ]);

        // CORRECTED: Use the validated data to create the review
        Review::create([
            'user_id' => Auth::id(),
            'gutenberg_book_id' => $validated['gutenberg_book_id'], // This was missing
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'],
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

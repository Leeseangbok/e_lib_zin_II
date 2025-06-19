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

<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;

class ReviewController extends Controller
{
    /**
     * Store a new review for a book using AJAX.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string|min:10|max:1000',
            'gutenberg_book_id' => 'required|integer',
        ]);

        try {
            $review = Review::create([
                'user_id' => Auth::id(),
                'gutenberg_book_id' => $validated['gutenberg_book_id'],
                'rating' => $validated['rating'],
                'review_text' => $validated['review_text'],
            ]);

            // Eager load the user information for the response
            $review->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Your review has been submitted!',
                'review' => [
                    'id' => $review->id,
                    'user_name' => $review->user->name,
                    'rating' => (int) $review->rating,
                    'review_text' => $review->review_text,
                    'created_at_diff' => $review->created_at->diffForHumans(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Review submission failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred on the server.'
            ], 500);
        }
    }

    /**
     * Remove the specified review from storage.
     *
     * @param  \App\Models\Review  $review
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy(Review $review)
    {
        if (Auth::id() !== $review->user_id && !Auth::user()->isAdmin()) {
            throw new AuthorizationException('You are not authorized to delete this review.');
        }

        $review->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Review deleted successfully.']);
        }

        return back()->with('success', 'Your review has been successfully deleted.');
    }
}

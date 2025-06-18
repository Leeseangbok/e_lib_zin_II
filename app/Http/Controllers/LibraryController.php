<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    /**
     * Display the user's library of favorite books.
     */
    public function index()
    {
        /** @var \App\Models\User $user */ // Good practice to add it here too
        $user = Auth::user();
        // Use the 'favoriteBooks' relationship we defined in the User model
        $favoriteBooks = $user->favoriteBooks()->orderBy('title')->paginate(12);

        return view('library.index', compact('favoriteBooks'));
    }

    /**
     * Add a book to the user's library.
     */
    public function add(Book $book)
    {
        /** @var \App\Models\User $user */ // <-- ADD THIS LINE
        $user = Auth::user();
        // The warning on the next line will now disappear
        $user->favoriteBooks()->syncWithoutDetaching([$book->id]);

        return back()->with('success', '"' . $book->title . '" was added to your library!');
    }

    /**
     * Remove a book from the user's library.
     */
    public function remove(Book $book)
    {
        /** @var \App\Models\User $user */ // <-- AND ADD THIS LINE
        $user = Auth::user();
        // The warning on the next line will now disappear
        $user->favoriteBooks()->detach($book->id);

        return back()->with('success', '"' . $book->title . '" was removed from your library.');
    }
}

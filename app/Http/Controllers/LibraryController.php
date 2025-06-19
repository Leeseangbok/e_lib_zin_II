<?php

namespace App\Http\Controllers;

use App\Services\GutendexService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LibraryController extends Controller
{
    protected $gutendexService;

    // Inject the GutendexService
    public function __construct(GutendexService $gutendexService)
    {
        $this->gutendexService = $gutendexService;
    }

    /**
     * Display the user's library of favorite books.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Get the IDs of the user's favorite books from the database.
        // We sort by 'created_at' to show the most recently added books first by default.
        $favoriteBookIds = $user->favoriteBooks()
                                ->latest('favorite_books.created_at')
                                ->pluck('gutenberg_book_id');

        // 2. Fetch the full book details for each ID from the Gutendex API.
        $books = [];
        foreach ($favoriteBookIds as $id) {
            $bookData = $this->gutendexService->getBookById($id);
            if ($bookData) {
                $books[] = $bookData;
            }
        }

        // 3. Convert the array of books into a Laravel Collection to easily sort.
        $bookCollection = new Collection($books);

        // 4. Sort the collection of books by title.
        $sortedBooks = $bookCollection->sortBy('title')->values();

        // 5. Manually paginate the sorted collection.
        $perPage = 12;
        $currentPage = $request->input('page', 1);
        $currentPageItems = $sortedBooks->slice(($currentPage - 1) * $perPage, $perPage);
        $paginatedBooks = new LengthAwarePaginator(
            $currentPageItems,
            $sortedBooks->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('library.index', ['favoriteBooks' => $paginatedBooks]);
    }

    /**
     * Add a book to the user's library.
     * Note: We use the 'gutenberg_book_id' now.
     */
    public function add(Request $request)
    {
        $request->validate(['gutenberg_book_id' => 'required|integer']);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->favoriteBooks()->syncWithoutDetaching([$request->gutenberg_book_id]);

        return back()->with('success', 'The book was added to your library!');
    }

    /**
     * Remove a book from the user's library.
     * Note: We use the 'gutenberg_book_id' now.
     */
    public function remove(Request $request)
    {
        $request->validate(['gutenberg_book_id' => 'required|integer']);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->favoriteBooks()->detach($request->gutenberg_book_id);

        return back()->with('success', 'The book was removed from your library.');
    }
}
